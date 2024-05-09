<?php

namespace App\Controller\Principal\Recibo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Entity\Principal\Recibo;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\ReciboFacade;
use Jurosh\PDFMerge\PDFMerger;
use Dompdf\Exception;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ReciboController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ReciboFacade
     */
    private $reciboFacade;

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

     /**
     *
     * @var \App\Repository\Principal\TransacaoCartaoRepository
     */
    private $transacaoCartaoRepository;


    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->reciboFacade = new ReciboFacade(self::getManagerRegistry());
        $this->movimentoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MovimentoConta::class);
        $this->transacaoCartaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TransacaoCartao::class);
    }

    /**
     * Funcao para criacao de novos pdf
     *
     * @param string $diretorioArquivo
     * @param int $franqueada
     * @param int $usuarioId
     * @param array $parametros
     * @param array $listaPdfs
     * @param array $dadosParaHtml
     * @param string $tipo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function geraPdfsNovos($diretorioArquivo, $franqueada, $usuarioId, $parametros, &$listaPdfs, &$dadosParaHtml, $tipo)
    {
        $mensagemErro = "";
        if ($this->reciboFacade->gerarPdfs($parametros, $listaPdfs, $dadosParaHtml, $mensagemErro, $tipo) === false) {
            return ResponseFactory::internalServerError([], $mensagemErro);
        }

        $pdfGenerator = $this->get('knp_snappy.pdf');
        foreach ($dadosParaHtml as $pessoa) {
            $hash      = uniqid();
            $reciboORM = new \App\Entity\Principal\Recibo();
            $reciboORM->setDataGeracao(new \DateTime());
            $reciboORM->setHashNomeArquivo($hash . ".pdf");
            if ($this->reciboFacade->criaReciboORM($franqueada, $usuarioId, $mensagemErro, $reciboORM) === true) {
                if (empty($mensagemErro) === false) {
                    return ResponseFactory::internalServerError([], $mensagemErro);
                }
            }

            foreach ($pessoa[ConstanteParametros::CHAVE_ITENS] as $itemMovimento) {
                $itemMovimento[ConstanteParametros::CHAVE_MOVIMENTO_CONTA]->setRecibo($reciboORM);
                self::getEntityManager()->flush();
            }

            $pessoa["numeroRecibo"] = $reciboORM->getNumeroRecibo();
            $html = $this->renderView('recibo.html.twig', ["dados" => $pessoa]);
            $pdfGenerator->generateFromHtml($html, $diretorioArquivo . $hash . ".pdf");
            $listaPdfs[] = $diretorioArquivo . $hash . ".pdf";
        }//end foreach

        self::getEntityManager()->flush();
    }

    /**
     * Verifica as movimentacoes de contas existentes(PDF)
     *
     * @param array $listaPdfs
     * @param array $parametros
     * @param string $diretorioArquivo
     */
    protected function verificaMovimentacaoConta(&$listaPdfs, &$parametros, $diretorioArquivo)
    {
        $retorno = [];
        foreach ($parametros as $movimentoContaId) {
            $movimentoContaORM = $this->movimentoContaRepository->find($movimentoContaId);
            if (is_null($movimentoContaORM) === false) {
                if (is_null($movimentoContaORM->getRecibo()) === false) {
                    $listaPdfs[] = $diretorioArquivo . $movimentoContaORM->getRecibo()->getHashNomeArquivo();
                    continue;
                }
            }

            $retorno[] = $movimentoContaId;
        }

        $parametros = $retorno;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/recibo/gerar_recibo",
     *     summary="Gera o Recibo",
     *     description="Gera o Recibo",
     *     tags={"Usuario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o recibo"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario",          strict=false, nullable=true, description="Usuario Logadi", requirements="\d+")
     * @FOSRest\QueryParam(name="movimentos_conta", strict=true, nullable=false, description="MovimentoConta", map=true)
     * @FOSRest\QueryParam(name="tipo",             strict=true, nullable=false, allowBlank=false, description="Tipo de recibo", default="CR", requirements="(CR|CP)")
     *
     * @FOSRest\Get("/recibo/gerar_recibo")
     *
     * @return \Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse
     */
    public function gerarRecibo(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros    = $paramFetcher->all();
        $listaPdfs     = [];
        $dadosParaHtml = [];
        $franqueada    = $parametros["franqueada"];
        $usuarioId     = $request->headers->get('Authorization-User-ID');
        if (empty($usuarioId) === true) {
            $usuarioId = $parametros[ConstanteParametros::CHAVE_USUARIO];
        }

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/uploads/" . $franqueada . "/";
        $this->verificaMovimentacaoConta($listaPdfs, $parametros[ConstanteParametros::CHAVE_MOVIMENTOS_CONTA], $diretorioArquivo);
        $this->geraPdfsNovos($diretorioArquivo, $franqueada, $usuarioId, $parametros, $listaPdfs, $dadosParaHtml, $paramFetcher->get('tipo', true));
        $pdf = new PDFMerger;
        foreach ($listaPdfs as $nomePdf) {
            $pdf->addPDF($nomePdf);
        }

        $hoje = new \DateTime();

        $pdf->merge("download", "Recibos_" . $hoje->format("d/m/Y") . ".pdf");

    }


   


    /**
     *
     * @SWG\Get(
     *     path="/api/recibo/imprimir",
     *     summary="Imprime um recibo",
     *     description="Imprime um recibo",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna recibos em PDF"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="usuario",                strict=true, nullable=false, description="Usuario", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",             strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="movimentos_conta",       strict=true, nullable=true, description="Franqueada", map=true)
     * @FOSRest\QueryParam(name="titulos",                strict=true, nullable=true, description="Franqueada", map=true)
     *
     * @FOSRest\Get("/recibo/imprimir")  
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imprimir(ParamFetcher $request)
    {
     
            date_default_timezone_set('America/Sao_Paulo');

            $parametros = $request->all();   
            
            $html   = "";

            $franqueada = $parametros["franqueada"];
            
            $movimentos = $parametros[ConstanteParametros::CHAVE_MOVIMENTOS_CONTA];
            
            $titulos = $parametros[ConstanteParametros::CHAVE_TITULOS];
            // $transacaoCheques  = $parametros[ConstanteParametros::CHAVE_CHEQUES];
            

            $dataHoje   = new \DateTime();
            $extensoObj = new \phputil\extenso\Extenso();

            $recibosTemp =[];

 
            if ($titulos != null) {
                //  buscar os contas a receber e agrupar os titulos
            
                foreach ($titulos as $titulo) {


                    $reciboTitulo =[];
//todo verificar se a data de pagamento esta correta no recibo
                    $sql = "SELECT  tr.id as id, 
                                    cr.id as contas_receber,
                                    p.nome_contato as PAGADOR,
                                    tr.valor_original as VALORLIQUIDO,
                                    tr.data_prorrogacao as DATAPAGAMENTO,
                                    tr.data_prorrogacao as DATAVENC,
                                    tr.valor_saldo_devedor ,
                                    tr.valor_parcela_sem_desconto as VALORTOTAL,
                                    tr.desconto_antecipacao as Desconto,
                                    t.descricao as Descricao_Turma,
	                                c2.id as Numero_contrato,
                                    f.nome as nome_franqueada,
                                    f.endereco as end_franqueada,
                                    f.numero_endereco as numero_franqueada, 
                                    f.bairro_endereco as Bairro_franqueada,
                                    c.nome  as cidade_Franqueada,
                                    e.nome  as estado_Franqueada,
                                    f.cnpj as Cnpj_Franqueada,
                                    f.cep_endereco as CEP_Franqueada,
                                    tr.observacao as Detalhes,
                                    fp.descricao  as Forma_Pag
                        from titulo_receber tr 
                        INNER JOIN conta_receber cr on tr.conta_receber_id = cr.id
                        INNER JOIN pessoa p on tr.sacado_pessoa_id = p.id 
                        LEFT JOIN contrato c2 on cr.contrato_id  = c2.id
                        LEFT JOIN turma t on c2.turma_id = t.id
                        INNER JOIN franqueada f on tr.franqueada_id = f.id 
                        INNER join forma_pagamento fp on tr.forma_recebimento_id = fp.id 
                        INNER join cidade c on f.cidade_id = c.id 
                        INNER JOIN estado e on f.estado_id = e.id
                        where tr.id = {$titulo} and tr.franqueada_id = {$franqueada} ";
                           
                            $connection = self::getManagerRegistry()->getConnection();
                            
                            $dadosTitulo = $connection->fetchAll($sql); 

                                                        
                        foreach ($dadosTitulo as $dc) {
 
                        $dataVenc = $dc["DATAVENC"];

                        $reciboTitulo["categoria"] = 'Outras'; 
                        $reciboTitulo["vencimento"] = $dataVenc;
                        $reciboTitulo["valorTitulo"] = $dc["VALORLIQUIDO"];
                        $reciboTitulo["detalhamentoMovimento"] = $dc["Detalhes"] ;
                        $reciboTitulo["detalhamentoTitulo"] = '' ;
                        $reciboTitulo["desconto"] = $dc["Desconto"] ;
                        $reciboTitulo["juros"] = '0.00';
                        $reciboTitulo["pagamento"] =  $dc["Detalhes"] ;
                        $reciboTitulo["itens"] = $dc["Forma_Pag"];
                        $reciboTitulo["formapagamento"] = $dc["Forma_Pag"] ;
                        $reciboTitulo["nomeEmpresa"] = $dc["nome_franqueada"];
                        $reciboTitulo["enderecoCompletoEmpresa"] = $dc["Bairro_franqueada"].', '.
                                                                    $dc["end_franqueada"].', '.
                                                                    $dc["numero_franqueada"].', '.
                                                                    $dc["cidade_Franqueada"].', '.
                                                                    $dc["estado_Franqueada"].', '.
                                                                    $dc["CEP_Franqueada"];

                        $reciboTitulo["dataImpressao"] = $dataHoje->format("d/m/Y");
                        $reciboTitulo["dataImpressaoCompleto"] = $dataHoje->format("d/m/Y H:m");
                        $reciboTitulo["valorExtenso"] = $extensoObj->extenso($dc["VALORLIQUIDO"], \phputil\extenso\Extenso::MOEDA);
                        $reciboTitulo["dataContabil"] = $dc["DATAPAGAMENTO"];
                        $reciboTitulo["recebimentoFormapagamentoEmitente"] = $dc["Forma_Pag"]. ' / '. $dc["PAGADOR"];
                        $reciboTitulo["Emitente"] = $dc["PAGADOR"];
                        $reciboTitulo["recebidoPor"] = $dc["PAGADOR"];
                        $reciboTitulo["referente"] ='';
                        if($dc["Numero_contrato"]) {
                            $reciboTitulo["referente"] .='Contrato Num.: '. $dc["Numero_contrato"];
                        }
                       
                        if($dc["Descricao_Turma"]) {
                            $reciboTitulo["referente"] .= ' - da Turma : '. $dc["Descricao_Turma"];
                        }
                       

                        $usuarioId =  $parametros["usuario"];    
                        $reciboTituloORM = $this->reciboFacade->criaReciboORM($franqueada, $usuarioId);
                    
                        if(!$reciboTituloORM){
                            Throw new Exception("não foi possivel criar o recibo para o usuário:".$usuarioId." fraqueada:".$franqueada);
                        }
                        $reciboTitulo["numeroRecibo"] = $reciboTituloORM->getNumeroRecibo();

                        
                        if ( !isset($recibosTemp[$dc['contas_receber'] ])) {
                            $recibosTemp[$dc['contas_receber']] = [];
                        }

                        $recibosTemp[$dc['contas_receber']][] = $reciboTitulo;
                    }
                }
                $recibos = [];
                
                foreach ($recibosTemp as $registros) {
                    $valorTotal = 0;
                    $recibo = [];
                    $recibo = $registros[0];
                    $recibo['itens'] = [];
                    foreach ($registros as $registro) {
                        $recibo['itens'][] = $registro;
                        $valorTotal = $valorTotal + $registro["valorTitulo"];
                    }
                    $recibo['valorTotal'] = $valorTotal;
                    $recibos[] = $recibo;
                }
            }


            $dados =[];
        // GERANDO DADOS
            if ($movimentos != null) {
                foreach ($movimentos as $movimento) {
                    $movimentoContaORM = $this->movimentoContaRepository->find($movimento);
                    
                    if (is_null($movimentoContaORM) === false) {
                            $this->reciboFacade->montaReciboImpressao($dados, $movimentoContaORM, $parametros);
                    }
                }
                // $recibos =[];
                    foreach ($dados as $d) {
                        $recibo = [];
                         $itens = $d["itens"];
                        $recibo["itens"] = $itens;
                        $recibo["valorTotal"] = $d["VALORTOTAL"];
                        $recibo["nomeEmpresa"] = $d["FRANQUEADA"];
                        $recibo["enderecoCompletoEmpresa"] = $d["FRANQUEADAENDERECO"];
                        $recibo["dataImpressao"] = $d["DATAIMPRESSAO"];
                        $recibo["dataImpressaoCompleto"] = $d["HORAIMPRESSAO"];
                        $recibo["valorExtenso"] = $d["VALOREXTENSO"];
                        $recibo["dataContabil"] = $d["DATAPAGAMENTO"];
                        $recibo["recebimentoFormapagamentoEmitente"] = $d["PAGADOR"];
                        $recibo["Emitente"] = $d["PAGADOR"];
                        $recibo["recebidoPor"] = $d["RECEBEDOR"];
                        $recibo["referente"] = $d["referente"];
                        

                        $usuarioId =  $parametros["usuario"];
                        $reciboORM = $this->reciboFacade->criaReciboORM($franqueada, $usuarioId);
                        if(!$reciboORM){
                            Throw new Exception("não foi possivel criar o recibo para o usuário:".$usuarioId." fraqueada:".$franqueada);
                        }
                        $recibo["numeroRecibo"] = $reciboORM->getNumeroRecibo();

                        $recibos[] = $recibo;            
                    }
                }

            $html = $this->renderView('relatorios/recibo/report.html', ["recibos" => $recibos]);
            echo $html;

            die;
        }

        /**
         *
         * @SWG\Get(
         *     path="/api/recibo_entrega_item/imprimir",
         *     summary="Imprime um recibo de entrega de item",
         *     description="Imprime um recibo de entrega de item",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         * @SWG\Response(
         *         response="204",
         *         description="Retorna recibos em PDF"
         *     ),
         * @SWG\Response(
         *         response="400",
         *         description="Ocorreu algum erro no servidor",
         *     )
         * )
         *
         * @FOSRest\QueryParam(name="usuario",                strict=true, nullable=false, description="Usuario", requirements="\d+")
         * @FOSRest\QueryParam(name="franqueada",             strict=true, nullable=false, description="Franqueada", requirements="\d+")
         * @FOSRest\QueryParam(name="lista_id",               strict=true, nullable=true, allowBlank=false, description="Lista de ID", requirements="\d+", map=true)
         *
         * @FOSRest\Get("/recibo_entrega_item/imprimir")  
         *
         * @return \Symfony\Component\HttpFoundation\Response
         */
        public function imprimirEntregaItem(ParamFetcher $request)
        {
        
                date_default_timezone_set('America/Sao_Paulo');

                $parametros = $request->all(); 
                $listaIds       = $parametros[ConstanteParametros::CHAVE_LISTA_ID];
                
                $html   = "";
                $franqueada = $parametros["franqueada"];
                
                foreach ($listaIds as $idItemContaReceber) {
                
                }


                $dataHoje   = new \DateTime();
                $extensoObj = new \phputil\extenso\Extenso();

                $recibosTemp =[];

    
                if ($listaIds != null) {
                    foreach ($listaIds as $idItemContaReceber) {
                        $reciboItens =[];

                        $sql = "SELECT  icr.id as iten_conta_receber_id,
                                    p.id as nome_id,
                                    p.nome_contato as nome_Aluno,
                                    p.cnpj_cpf as CPF_CNPJ_Aluno,
                                    p2.nome_contato as nome_responsavel,
                                    p2.cnpj_cpf as CPF_CNPJ_responsavel,
                                    i.descricao as Descricao_item,
                                    icr.quantidade as qtd_entrega,
                                    icr.valor as Valor_total_item,
                                    icr.valor_item as Valor_item,
                                    icr.valor_desconto as Valor_desconto,
                                    icr.data_entrega as data_entrega,
                                    cr.data_emissao as data_emissao,
                                    f.nome as nome_franqueada,
                                    f.endereco as end_franqueada,
                                    f.numero_endereco as numero_franqueada, 
                                    f.bairro_endereco as Bairro_franqueada,
                                    c.nome  as cidade_Franqueada,
                                    e.nome  as estado_Franqueada,
                                    f.cnpj as Cnpj_Franqueada,
                                    f.cep_endereco as CEP_Franqueada
                            from item_conta_receber icr 
                            INNER JOIN conta_receber cr on icr.conta_receber_id = cr.id
                            INNER JOIN aluno a on cr.aluno_id = a.id 
                            INNER JOIN pessoa p on a.pessoa_id = p.id 
                            INNER JOIN pessoa p2 on a.responsavel_financeiro_pessoa_id = p2.id
                            INNER JOIN item i on icr.item_id = i.id
                            INNER JOIN franqueada f on cr.franqueada_id = f.id 
                            INNER join cidade c on f.cidade_id = c.id 
                            INNER JOIN estado e on f.estado_id = e.id
                            where icr.id = {$idItemContaReceber} and cr.franqueada_id = {$franqueada} ";
                            
                    $connection = self::getManagerRegistry()->getConnection();
                                
                    $dadosItem = $connection->fetchAll($sql); 
                                
                            foreach ($dadosItem as $dc) {
    
                            $reciboItens["itenContaReceberId"] = $dc["iten_conta_receber_id"]; 
                            $reciboItens["Aluno"] = $dc["nome_Aluno"];
                            $reciboItens["cpfAluno"] = $dc["CPF_CNPJ_Aluno"];
                            $reciboItens["Responsavel"] = $dc["nome_responsavel"];
                            $reciboItens["cpfResponsavel"] = $dc["CPF_CNPJ_responsavel"];
                            $reciboItens["descricaoItem"] = $dc["Descricao_item"];
                            $reciboItens["ValorTotalItem"] = $dc["Valor_total_item"];
                            $reciboItens["ValorItem"] = $dc["Valor_item"];
                            $reciboItens["ValorDesconto"] = $dc["Valor_desconto"];
                            $reciboItens["Qtidade"] = $dc["qtd_entrega"];
                            $reciboItens["nomeEmpresa"] = $dc["nome_franqueada"];
                            $reciboItens["enderecoCompletoEmpresa"] = $dc["Bairro_franqueada"].', '.
                                                                        $dc["end_franqueada"].', '.
                                                                        $dc["numero_franqueada"].', '.
                                                                        $dc["cidade_Franqueada"].', '.
                                                                        $dc["estado_Franqueada"].', '.
                                                                        $dc["CEP_Franqueada"];

                            $reciboItens["dataImpressao"] = $dataHoje->format("d/m/Y");
                            $reciboItens["dataEntrega"] = $dc["data_entrega"];
                            $reciboItens["dataEmissao"] = $dc["data_emissao"];
                            $reciboItens["dataImpressaoCompleto"] = $dataHoje->format("d/m/Y H:m");
                            $reciboItens["valorExtenso"] = $extensoObj->extenso($dc["Valor_total_item"], \phputil\extenso\Extenso::MOEDA);
                            
                            $usuarioId =  $parametros["usuario"];    
                            $reciboItemORM = $this->reciboFacade->criaReciboORM($franqueada, $usuarioId);
                        
                            if(!$reciboItemORM){
                                Throw new Exception("não foi possivel criar o recibo para o usuário:".$usuarioId." fraqueada:".$franqueada);
                            }
                        
                            $reciboItens["numeroRecibo"] = $reciboItemORM->getNumeroRecibo();

                            $recibosTemp[] = $reciboItens;
                        }
                    }
                }

                
                    $html = $this->renderView('relatorios/recibo_entrega_itens/report.html', ["recibos" => $recibosTemp]);
                echo $html;

                die;
            }
    
}
