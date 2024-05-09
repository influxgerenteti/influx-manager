<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\MovimentoConta;
use Dompdf\Exception;

/**
 *
 * @author Luiz A Costa
 */
class ReciboFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->movimentoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MovimentoConta::class);
        $this->franqueadaRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->usuarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
    }

    /**
     * Gera o numero de recibo por franqueada e atualiza
     *
     * @param int $franqueadaId
     *
     * @return number
     */
    protected function geraNumeroReciboPorFranqueada($franqueadaId)
    {
        $franqueadaORM      = $this->franqueadaRepository->find($franqueadaId);
        $ultimoReciboGerado = ($franqueadaORM->getNumeroRecibo() + 1);
        $franqueadaORM->setNumeroRecibo($ultimoReciboGerado);
        self::flushSeguro();
        return $ultimoReciboGerado;
    }

    /**
     * Monta um array vazio com indices relacionado a pessoa ID
     *
     * @param \App\Entity\Principal\MovimentoConta[] $movimentoContaArray
     * @param string $tipoRecibo Flag para recibo Conta Receber(CR) ou Conta Pagar(CP)
     *
     * @return array
     */
    protected function retornaArrayPorPessoaId($movimentoContaArray, $tipoRecibo)
    {
        $retorno = [];
        foreach ($movimentoContaArray as $movimentoContaORM) {
            if ($tipoRecibo === "CP") {
                $pessoaId = $movimentoContaORM->getTituloPagar()->getFavorecidoPessoa()->getId();
            } else {
                $pessoaId = $movimentoContaORM->getTituloReceber()->getSacadoPessoa()->getId();
            }

            $retorno[$pessoaId][ConstanteParametros::CHAVE_ITENS] = [];
        }

        return $retorno;
    }

    /**
     * Verifica o tipo de recibo e atribui as informações para imprimir o Recibo
     *
     * @param string $tipoRecibo
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param string $recebidoPor
     * @param string $emitente
     * @param string $vencimentoString
     * @param string $tituloObservacao
     * @param string $pessoaId
     */
    protected function alimentaReciboPorTipo($tipoRecibo, $movimentoContaORM, $franqueadaORM, &$recebidoPor, &$emitente, &$vencimentoString, &$tituloObservacao, &$pessoaId)
    {
        if ($tipoRecibo === "CP" || $tipoRecibo === "D") {
            if ($movimentoContaORM->getTituloPagar()->getFavorecidoPessoa()->getTipoPessoa() === "F") {
                $recebidoPor = $movimentoContaORM->getTituloPagar()->getFavorecidoPessoa()->getNomeContato();
            } else {
                $recebidoPor = $movimentoContaORM->getTituloPagar()->getFavorecidoPessoa()->getNomeFantasia();
            }

            $emitente         = $franqueadaORM->getNome();
            $tituloObservacao = $movimentoContaORM->getTituloPagar()->getObservacao();
            $vencimentoString = $movimentoContaORM->getTituloPagar()->getDataProrrogacao()->format("d/m/Y");
            $pessoaId         = $movimentoContaORM->getTituloPagar()->getFavorecidoPessoa()->getId();
        } else {
            if ($movimentoContaORM->getTituloReceber()->getSacadoPessoa()->getTipoPessoa() === "F") {
                $emitente = $movimentoContaORM->getTituloReceber()->getSacadoPessoa()->getNomeContato();
            } else {
                $emitente = $movimentoContaORM->getTituloReceber()->getSacadoPessoa()->getNomeFantasia();
            }

            $recebidoPor      = $franqueadaORM->getNome();
            $tituloObservacao = $movimentoContaORM->getTituloReceber()->getObservacao();
            $vencimentoString = $movimentoContaORM->getTituloReceber()->getDataProrrogacao()->format("d/m/Y");
            $pessoaId         = $movimentoContaORM->getTituloReceber()->getSacadoPessoa()->getId();
        }//end if
    }

    /**
     * Monta o array de item por $pessoaId
     *
     * @param array $arrayRetorno
     * @param \DateTime $dataHoje
     * @param \phputil\extenso\Extenso $extensoObj
     * @param int $pessoaId
     * @param string $emitente
     * @param string $recebidoPor
     * @param string $vencimentoString
     * @param string $tituloObservacao
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     */
    protected function montaArrayItens(&$arrayRetorno, $dataHoje, $extensoObj, $pessoaId, $emitente, $recebidoPor, $vencimentoString, $tituloObservacao, $movimentoContaORM, $franqueadaORM)
    {
        
        $dataContabil       = $movimentoContaORM->getDataContabil()->format("d/m/Y");
        $descricaoDocumento = $movimentoContaORM->getFormaPagamento()->getDescricao();
        
        $arrayRetorno[$pessoaId][ConstanteParametros::CHAVE_ITENS][] = [
            "categoria"                                => "Outras",
            "vencimento"                               => $vencimentoString,
            "valorTitulo"                              => $movimentoContaORM->getValorLancamento(),
            "detalhamentoMovimento"                    => $movimentoContaORM->getObservacao(),
            "detalhamentoTitulo"                       => $tituloObservacao,
            "desconto"                                 => number_format($movimentoContaORM->getValorDesconto(), 2),
            "juros"                                    => number_format($movimentoContaORM->getValorJuros(), 2),            
            "pagamento"                                 =>  $dataContabil,
            "formapagamento"                             => $descricaoDocumento,
            "recebimentoFormapagamentoEmitente"                                  => $descricaoDocumento . ' / '   .$emitente,
            ConstanteParametros::CHAVE_MOVIMENTO_CONTA => $movimentoContaORM,
        ];
        if (isset($arrayRetorno[$pessoaId]["VALORTOTAL"]) === false) {
            $arrayRetorno[$pessoaId]["VALORTOTAL"] = 0;
        }
  
        $arrayRetorno[$pessoaId]["FRANQUEADA"] = $franqueadaORM->getNome();
        $endereco = '';
        $endereco = $endereco.", ". $franqueadaORM->getBairroEndereco();
        $endereco = $endereco.", ". $franqueadaORM->getEndereco() ;
        $endereco = $endereco.", ". $franqueadaORM->getNumeroEndereco() ;
        if($franqueadaORM->getCidade() != null){
            $endereco = $endereco.", ".  $franqueadaORM->getCidade()->getNome() ;
        }
        if($franqueadaORM->getEstado() != null){
            $endereco = $endereco.", ".  $franqueadaORM->getEstado()->getNome() ;
        }
        
        $endereco = $endereco.", ". $franqueadaORM->getCepEndereco() ;
        
        $arrayRetorno[$pessoaId]["FRANQUEADAENDERECO"] = $endereco;
        $arrayRetorno[$pessoaId]["DATAIMPRESSAO"]           = $dataHoje->format("d/m/Y");
        $arrayRetorno[$pessoaId]["HORAIMPRESSAO"]   = $dataHoje->format("d/m/Y H:m");
        $arrayRetorno[$pessoaId]["VALORTOTAL"]  += $movimentoContaORM->getValorLancamento();
        $arrayRetorno[$pessoaId]["VALOREXTENSO"] = $extensoObj->extenso($arrayRetorno[$pessoaId]["VALORTOTAL"], \phputil\extenso\Extenso::MOEDA);
        $arrayRetorno[$pessoaId]["DATAPAGAMENTO"] = $dataContabil;
        $arrayRetorno[$pessoaId]["PAGADOR"]     = $emitente;
        $arrayRetorno[$pessoaId]["RECEBEDOR"]  = $recebidoPor;
       
    }

    /**
     * Gera array de dados de recibo
     *
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param \App\Entity\Principal\MovimentoConta[] $movimentoContaArray
     * @param string $tipoRecibo Flag para recibo Conta Receber(CR) ou Conta Pagar(CP)
     *
     * @return array
     */
    protected function geraArrayDadosRecibo($franqueadaORM, $movimentoContaArray, $tipoRecibo="CR")
    {
        $retorno    = $this->retornaArrayPorPessoaId($movimentoContaArray, $tipoRecibo);
        $dataHoje   = new \DateTime();
        $extensoObj = new \phputil\extenso\Extenso();
        foreach ($movimentoContaArray as $movimentoContaORM) {
            $recebidoPor      = "";
            $emitente         = "";
            $vencimentoString = "";
            $tituloObservacao = "";
            $pessoaId         = "";
            $this->alimentaReciboPorTipo($tipoRecibo, $movimentoContaORM, $franqueadaORM, $recebidoPor, $emitente, $vencimentoString, $tituloObservacao, $pessoaId);
            $this->montaArrayItens($retorno, $dataHoje, $extensoObj, $pessoaId, $emitente, $recebidoPor, $vencimentoString, $tituloObservacao, $movimentoContaORM, $franqueadaORM);
        }

        return $retorno;
    }

    /**
     * Realiza a geracao de html do pdf
     *
     * @param array $parametros
     * @param array $listaNomesPdf
     * @param array $dadosParaHtml
     * @param string $mensagemErro
     * @param string $tipo
     *
     * @return boolean
     */
    public function gerarPdfs($parametros, &$listaNomesPdf, &$dadosParaHtml, &$mensagemErro, $tipo='CR')
    {
        $dadosParaGerarRecebido = [];
        $arrayEntidades         = [];
        $franqueadaORM          = $this->franqueadaRepository->find($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        $tamanhoMovimentoConta  = count($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_CONTA]);
        if ($tamanhoMovimentoConta > 0) {
            if (is_null($franqueadaORM) === false) {
                $bErro        = false;
                $mensagemErro = "";
                $arrayEntidades[ConstanteParametros::CHAVE_FRANQUEADA] = $franqueadaORM;
                foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_CONTA] as $movimentoContaId) {
                    $movimentoContaORM = $this->movimentoContaRepository->findOneBy(["franqueada" => $franqueadaORM->getId(), "id" => $movimentoContaId]);
                    if (is_null($movimentoContaORM) === true) {
                        $mensagemErro = "MovimentoConta(" . $movimentoContaId . ") não encontrado para a franqueada não informada.";
                        $bErro        = true;
                        break;
                    }

                    $arrayEntidades[ConstanteParametros::CHAVE_MOVIMENTO_CONTA][] = $movimentoContaORM;
                }

                if ($bErro === true) {
                    return false;
                }

                $dadosParaGerarRecebido = $this->geraArrayDadosRecibo($franqueadaORM, $arrayEntidades[ConstanteParametros::CHAVE_MOVIMENTO_CONTA], $tipo);

                $dadosParaHtml = $dadosParaGerarRecebido;
                return true;
            } else {
                $mensagemErro = "Franqueada inexistente.";
                return false;
            }//end if
        }//end if

        return true;
    }

     /**
     * Realiza a geracao de html do pdf
     *
     * @param int $franqueada
     * @param MovimentoConta $movimentoORM
     * @param array $parametros
     * @param string $tipo
     *
     * @return bool
     */
    public function montaReciboImpressao(&$dados, $movimentoORM,$parametros)
    {
        // $dadosParaGerarRecebido = [];
        $franqueada = $parametros["franqueada"];
        $franqueadaORM          = $this->franqueadaRepository->find($franqueada);

        // $numeroRecibo = $movimentoORM->getRecibo()->getNumeroRecibo();
        $tipo = $movimentoORM->getTipoMovimentoConta()->getTipoOperacao();
        
        $dataHoje   = new \DateTime();
        $extensoObj = new \phputil\extenso\Extenso();
        
        
        $recebidoPor      = "";
        $emitente         = "";
        $vencimentoString = "";
        $tituloObservacao = "";
        $pessoaId         = "";
        $this->alimentaReciboPorTipo($tipo, $movimentoORM, $franqueadaORM, $recebidoPor, $emitente, $vencimentoString, $tituloObservacao, $pessoaId);
        $this->montaArrayItens($dados, $dataHoje, $extensoObj, $pessoaId, $emitente, $recebidoPor, $vencimentoString, $tituloObservacao, $movimentoORM, $franqueadaORM);
        
        // $dadosParaGerarRecebido["FRANQUEADA"]= $franqueadaORM->getNome();
        // $dadosParaGerarRecebido["FRANQUEADAENDERECO"]= $franqueadaORM->getBairroEndereco().", ".$franqueadaORM->getEndereco().", ".$franqueadaORM->getNumeroEndereco().", ".$franqueadaORM->getCidade()->getNome().", ".$franqueadaORM->getEstado()->getNome().", ".$franqueadaORM->getCepEndereco();

        // $dadosParaGerarRecebido["PAGADOR"]=  $emitente;
        // $dadosParaGerarRecebido["VALOREXTENSO"]=  $extensoObj->extenso( $movimentoORM->getValorTitulo(), \phputil\extenso\Extenso::MOEDA);
       
        // $dadosParaHtml = $dadosParaGerarRecebido;
        // return $dadosParaGerarRecebido;
        return true;
    }

    /**
     * Cria um registro na tabela de recibo
     *
     * @param int $franqueadaId
     * @param int $usuarioLogadoId
     *
     * @return \App\Entity\Principal\Recibo 
     */
    public function criaReciboORM($franqueadaId, $usuarioLogadoId)
    {
        $reciboORM = new \App\Entity\Principal\Recibo();
        $reciboORM->setDataGeracao(new \DateTime());
    
        $usuarioORM = $this->usuarioRepository->find($usuarioLogadoId);
        if (is_null($usuarioORM) === false) {
            $reciboORM->setHashNomeArquivo("file.pdf");
            $reciboORM->setUsuario($usuarioORM);
            $reciboORM->setFranqueada($this->franqueadaRepository->find($franqueadaId));
            $reciboORM->setNumeroRecibo($this->geraNumeroReciboPorFranqueada($franqueadaId));
            self::persistSeguro($reciboORM, $mensagemErro);
            return  $reciboORM;
        } else {
            // $mensagemErro = "Usuario não encontrado.";
            throw new Exception;
        }
    }


}
