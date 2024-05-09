<?php

namespace App\Controller\Principal\Contrato;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Entity\Principal\Franqueada;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\ContratoFacade;
use App\Facade\Principal\ContaReceberFacade;
use App\Facade\Principal\AgendamentoPersonalFacade;
use App\Facade\Principal\AlunoFacade;
use App\Helper\FunctionHelper;

use App\Helper\EmailAwsSender;
use Jurosh\PDFMerge\PDFMerger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ContratoController extends GenericController
{


     /**
     *
     * @var EmailAwsSender $emailAwsSender
     */
    private $emailAwsSender;

  /**
     *
     * @var Client
     */
    private $httpClient;
    /**
     *
     * @var \App\Facade\Principal\ContratoFacade
     */
    private $contratoFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\AgendamentoPersonalFacade
     */
    private $agendamentoPersonalFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoFacade
     */
    private $alunoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->contratoFacade            = new ContratoFacade(self::getManagerRegistry());
        $this->contaReceberFacade        = new ContaReceberFacade(self::getManagerRegistry());
        $this->agendamentoPersonalFacade = new AgendamentoPersonalFacade(self::getManagerRegistry());
        $this->alunoFacade = new AlunoFacade(self::getManagerRegistry());
        $this->httpClient          = new Client();
        $this->emailAwsSender       = new EmailAwsSender();
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/listar",
     *     summary="Listar contrato",
     *     description="Lista as contrato do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os contrato"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                           strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",                            strict=false, nullable=true, description="Aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="numero_contrato",                  strict=false, nullable=true, description="Número do contrato", requirements=".+")
     * @FOSRest\QueryParam(name="situacao",                         strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="data_inicio_contrato_inicio",      strict=false, nullable=true, description="Data Inicial de vencimento")
     * @FOSRest\QueryParam(name="data_inicio_contrato_fim",         strict=false, nullable=true, description="Data Final de vencimento")
     * @FOSRest\QueryParam(name="data_termino_contrato_inicio",     strict=false, nullable=true, description="Data Inicial de pagamento")
     * @FOSRest\QueryParam(name="data_termino_contrato_fim",        strict=false, nullable=true, description="Data Final de pagamento")
     * @FOSRest\QueryParam(name="responsavel_venda_funcionario",    strict=false, nullable=true, description="Responsavel de Venda Funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="responsavel_carteira_funcionario", strict=false, nullable=true, description="Responsavel de Carteira Funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="order",                            strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                          strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/contrato/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->contratoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }



   /**
     *
     * @SWG\Get(
     *     path="/api/contrato/listar_contratos",
     *     summary="Listar contrato",
     *     description="Lista as contrato do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os contrato"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                           strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",                            strict=false, nullable=true, description="Aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="numero_contrato",                  strict=false, nullable=true, description="Número do contrato", requirements=".+")
     * @FOSRest\QueryParam(name="situacao",                         strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="data_inicio_contrato_inicio",      strict=false, nullable=true, description="Data Inicial de vencimento")
     * @FOSRest\QueryParam(name="data_inicio_contrato_fim",         strict=false, nullable=true, description="Data Final de vencimento")
     * @FOSRest\QueryParam(name="data_termino_contrato_inicio",     strict=false, nullable=true, description="Data Inicial de pagamento")
     * @FOSRest\QueryParam(name="data_termino_contrato_fim",        strict=false, nullable=true, description="Data Final de pagamento")
     * @FOSRest\QueryParam(name="responsavel_venda_funcionario",    strict=false, nullable=true, description="Responsavel de Venda Funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="responsavel_carteira_funcionario", strict=false, nullable=true, description="Responsavel de Carteira Funcionario", requirements="\d+")
     * @FOSRest\QueryParam(name="order",                            strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                          strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/contrato/listar_contratos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listaContratos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->contratoFacade->listarContratos($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/buscar_contratos_ativos_com_dollar_por_turma/{turmaId}",
     *     summary="Buscar a contrato",
     *     description="Busca a contrato através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a contrato"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/contrato/buscar_contratos_ativos_com_dollar_por_turma/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarContratosAtivosComDollarPorTurma($turmaId)
    {
        $resultados = $this->contratoFacade->buscarContratosAtivosComDollarPorTurma($turmaId);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/buscar_texto_contrato/{id}",
     *     summary="Busca o texto do contrato",
     *     description="Busca o texto do contrato",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna o texto do contrato"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="modelo_contrato", strict=true, nullable=false, description="Modelo de contrato", requirements="\d+")
     *
     * @FOSRest\Get("/contrato/buscar_texto_contrato/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTextoContrato($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $contrato = $this->contratoFacade->gerarHtmlContrato($mensagem, $id, $parametros);
        if ($contrato === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($contrato);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/contrato/atualizar_texto/{id}",
     *     summary="Atualiza o texto do contrato",
     *     description="Atualiza o texto do contrato",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna ok"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="texto", strict=true, nullable=false, allowBlank=true, description="Texto do contrato")
     *
     * @FOSRest\Patch("/contrato/atualizar_texto/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarTexto($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $res        = $this->contratoFacade->atualizarTexto($mensagem, $id, $parametros);

        if ($res === false || empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/contrato/criar",
     *     summary="Cria uma contrato",
     *     description="Cria uma contrato no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",                             strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="semestre",                               strict=true, nullable=false, description="Semestres", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",                                  strict=true, nullable=false, description="Aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",                                  strict=true, nullable=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_venda_funcionario",          strict=true, nullable=false, description="Responsavel Venda Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_carteira_funcionario",       strict=true, nullable=false, description="Responsavel Carteira Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_pessoa",          strict=true, nullable=false, description="Responsavel Financeiro Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",                                  strict=true, nullable=true, description="Curso", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",                                  strict=true, nullable=true, description="Turma", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_contrato",                          strict=true, nullable=false, allowBlank=false, description="Tipo de Contrato", requirements="[M|R|T]")
     * @FOSRest\RequestParam(name="data_matricula",                         strict=true, nullable=true, allowBlank=true, description="Data de Matricula")
     * @FOSRest\RequestParam(name="data_inicio_contrato",                   strict=true, nullable=false, allowBlank=false, description="Data de Inicio do Contrato")
     * @FOSRest\RequestParam(name="data_termino_contrato",                  strict=true, nullable=false, allowBlank=false, description="Data de Termino do Contrato")
     * @FOSRest\RequestParam(name="observacao",                             strict=false, nullable=true, allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="bolsista",                               strict=true, nullable=false, allowBlank=false, description="Se o Contrato é bolsista", requirements="[1|0]", default="0")
     * @FOSRest\RequestParam(name="familiar_desconto",                      strict=true, nullable=true, allowBlank=true, description="Nome do familiar para desconto")
     * @FOSRest\RequestParam(name="convenio_desconto",                      strict=true, nullable=true, allowBlank=true, description="Convênio para desconto", requirements="\d+")
     * @FOSRest\RequestParam(name="aplica_desconto_super_amigos",           strict=true, nullable=false, allowBlank=false, description="Se aplica super amigos", requirements="(0|1)", default="0")
     * @FOSRest\RequestParam(name="aplica_desconto_super_amigos_turbinado", strict=true, nullable=false, allowBlank=false, description="Se aplica super amigos turbinado", requirements="(0|1)", default="0")
     * @FOSRest\RequestParam(name="aluno_indicador",                        strict=true, nullable=true, allowBlank=true, description="Aluno que indicou", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma",                       strict=true, nullable=false, allowBlank=false, description="Modalidade da turma", requirements="\w{3}")
     * @FOSRest\RequestParam(name="creditos_personal",                      strict=true, nullable=true, allowBlank=true, description="Créditos para personal", requirements="\d{1,3}")
     * @FOSRest\RequestParam(name="agendamento_personal",                   strict=true, nullable=true, allowBlank=true, description="Agendamentos para personal", map=true)
     * @FOSRest\RequestParam(name="funcionario",                            strict=true, nullable=true, allowBlank=true, description="Funcionário personal", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",                        strict=true, nullable=true, allowBlank=true, description="Sala personal", requirements="\d+")
     * @FOSRest\RequestParam(name="instrutor",                              strict=true, nullable=true, allowBlank=true, description="Instrutor personal", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="contas_receber",  strict=true, nullable=false, allowBlank=false, description="Array de contas a receber", map=true)
     * @FOSRest\RequestParam(name="titulos_receber", strict=true, nullable=false, allowBlank=false, description="Array de títulos a receber", map=true)
     *
     * @FOSRest\Post("/contrato/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();

        
        if ($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA] === SituacoesSistema::MODALIDADE_TURMA_PERSONAL) {
            // Rotina para calcular o ultimo dia do personal para colocar no Contrato
            $franqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);

            $franqueadaORM =$franqueadaRepository->find($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            
            $dataFinalContrato = $this->agendamentoPersonalFacade->calculaUltimaDataPersonal($mensagemErro, $parametros, $franqueadaORM);
            $parametros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO] = $dataFinalContrato;

        }

        $mensagem   = "";
        $boletosIDs = [];
        $boletos    = [];

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)||(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTAS_RECEBER]) === false)||(count($parametros[ConstanteParametros::CHAVE_CONTAS_RECEBER]) < 1)) {
            return ResponseFactory::conflict([], "Para poder prosseguir com a criação do contrato, será necessario no minimo 1 item a ser adicionado.");
        }

        if ($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA] === SituacoesSistema::MODALIDADE_TURMA_PERSONAL) {
            unset($parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        $alunoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $alunoORM =$alunoRepository->find($parametros[ConstanteParametros::CHAVE_ALUNO]);

       // $pessoaID = $alunoORM->getPessoa()->getID();
        $franqueadas = $alunoORM->getPessoa()->getFranqueadas();

        $pessoaRelacionada = false;
        foreach ($franqueadas as $franqueada) {
            if ($franqueada->getId() == $parametros[ConstanteParametros::CHAVE_FRANQUEADA]){
                $pessoaRelacionada = true;
            }
        }

        if ($pessoaRelacionada == false){
            $franqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
            $franqueadaORM =$franqueadaRepository->find($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            $alunoORM->getPessoa()->addFranqueada($franqueadaORM);
            self::getEntityManager()->flush();
        }

        $formaPagamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormaPagamento::class);
        foreach ($parametros[ConstanteParametros::CHAVE_CONTAS_RECEBER] as $keyData => $contaReceberData) {
            foreach ($contaReceberData[ConstanteParametros::CHAVE_ITENS] as $keyItem => $item) {
                $idFormaPagamento = $item[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]["id"];
                $parametros[ConstanteParametros::CHAVE_CONTAS_RECEBER][$keyData][ConstanteParametros::CHAVE_ITENS][$keyItem][ConstanteParametros::CHAVE_FORMA_PAGAMENTO] = $formaPagamentoRepository->find($idFormaPagamento);
            }
        }

        $boletosIDs = [];

        self::getEntityManager()->getConnection()->beginTransaction(); // suspend auto-commit
        try {
          
            $contratoORM = $this->contratoFacade->criar($mensagem, $parametros);
            if ((is_null($contratoORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict([], $mensagem);
            }
    
            if ($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA] === SituacoesSistema::MODALIDADE_TURMA_PERSONAL) {
                $personalData = [
                    ConstanteParametros::CHAVE_CONTRATO             => $contratoORM,
                    ConstanteParametros::CHAVE_FRANQUEADA           => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                    ConstanteParametros::CHAVE_CREDITOS_PERSONAL    => $parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL],
                    ConstanteParametros::CHAVE_FUNCIONARIO          => $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG],
                    ConstanteParametros::CHAVE_SALA_FRANQUEADA      => $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA],
                    ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL => $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL],
                ];
    
                $this->agendamentoPersonalFacade->criar($mensagem, $personalData);
            }
    
            foreach ($parametros[ConstanteParametros::CHAVE_CONTAS_RECEBER] as $contaReceberData) {
                $contaReceberData[ConstanteParametros::CHAVE_CONTRATO]        = $contratoORM;
                $contaReceberData[ConstanteParametros::CHAVE_USUARIO]         = $parametros[ConstanteParametros::CHAVE_USUARIO];
                $contaReceberData[ConstanteParametros::CHAVE_FRANQUEADA]      = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                $contaReceberData[ConstanteParametros::CHAVE_TITULOS_RECEBER] = $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER];
                $contaReceberData[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS]           = $parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS];
                $contaReceberData[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS_TURBINADO] = $parametros[ConstanteParametros::CHAVE_APLICAR_DESCONTO_SUPER_AMIGOS_TURBINADO];
                $contaReceberData[ConstanteParametros::CHAVE_ALUNO_INDICADOR] = $parametros[ConstanteParametros::CHAVE_ALUNO_INDICADOR];
                $contaReceberORM = $this->contaReceberFacade->criar($mensagem, $contaReceberData, $boletos);
                if ((is_null($contaReceberORM) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict([], $mensagem);
                }
            }
    
            
    
            $alunoORM = $contratoORM->getAluno();
            $this->alunoFacade->atualizarSituacao($mensagem, $alunoORM);
    
            if (empty($mensagem) === false) {
                return ResponseFactory::conflict([], $mensagem);
            }
    
            


            self::getEntityManager()->flush();
            self::getEntityManager()->getConnection()->commit();


            foreach ($boletos as $boletoORM) {
                $boletosIDs[] = $boletoORM->getId();
            }
    
            return ResponseFactory::created(
                [
                    "objetoORM" => $contratoORM->getId(),
                    'boletos'   => $boletosIDs,
                ],
                "Contrato criado!"
            );

        } catch (Exception $e) {
            self::getEntityManager()->getConnection()->rollBack();
            return ResponseFactory::badRequest(
                [],
                "Falha ao processar contrato:".$e->getMessage()
            );            
        }

       
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/contrato/atualizar/{id}",
     *     summary="Atualiza um contrato",
     *     description="Atualiza um contrato no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna atualizado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",                       strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",                            strict=true, nullable=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="semestre",                         strict=true, nullable=false, description="semestre", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_venda_funcionario",    strict=true, nullable=false, description="Responsavel Venda Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_carteira_funcionario", strict=true, nullable=false, description="Responsavel Carteira Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_financeiro_pessoa",    strict=true, nullable=false, description="Responsavel Financeiro Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",                            strict=true, nullable=true, description="Curso", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",                            strict=true, nullable=true, description="Turma", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_contrato",                    strict=true, nullable=false, allowBlank=false, description="Tipo de Contrato", requirements="[M|R|T]")
     * @FOSRest\RequestParam(name="data_matricula",                   strict=true, nullable=false, allowBlank=false, description="Data de Matricula")
     * @FOSRest\RequestParam(name="data_inicio_contrato",             strict=true, nullable=false, allowBlank=false, description="Data de Inicio do Contrato")
     * @FOSRest\RequestParam(name="data_termino_contrato",            strict=true, nullable=false, allowBlank=false, description="Data de Termino do Contrato")
     * @FOSRest\RequestParam(name="observacao",                       strict=false, nullable=true, allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="bolsista",                         strict=false, nullable=true, allowBlank=true, description="Se o Contrato é bolsista", requirements="[1|0]")
     * @FOSRest\RequestParam(name="situacao",                         strict=false, nullable=false, allowBlank=false, description="situacao", requirements="[V|E|R|C|T]")
     * @FOSRest\RequestParam(name="motivo_cancelamento",              strict=false, nullable=true, allowBlank=true, description="motivo de cancelamento")
     * @FOSRest\RequestParam(name="modalidade_turma",                 strict=true, nullable=false, allowBlank=false, description="Modalidade da turma", requirements="\w{3}")
     *
     * @FOSRest\Patch("/contrato/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->contratoFacade->atualizar($mensagem, $id, $parametros);

        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    //  /**
    //  *
    //  * @SWG\Get(
    //  *     path="/api/contrato/visualizar/{id}/{modelo}",
    //  *     summary="Visualizar para aceite",
    //  *     description="Visualizar para aceite",
    //  *     consumes={"application/x-www-form-urlencoded"},
    //  *     produces={"application/json"},
    //  * @SWG\Response(
    //  *         response="204",
    //  *         description="Retorna Contrato para aceite"
    //  *     ),
    //  * @SWG\Response(
    //  *         response="400",
    //  *         description="Ocorreu algum erro no servidor, contate o suporte",
    //  *     )
    //  * )
    //  *
    //  * @FOSRest\QueryParam(name="modelo", strict=true, nullable=true, description="Modelo de contrato a imprimir", requirements="\d+")
    //  *
    //  * @FOSRest\Get("/contrato/visualizar/{id}/{modelo}")
    //  *
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function visualizarContrato(id, modelo, ParamFetcher $request)
    // {
    //     $parametros = $request->all();
    //     $mensagem   = "";
       

        

    // }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/imprimir/{id}",
     *     summary="Imprime um contrato",
     *     description="Imprime um contrato",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna contrato em PDF"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",      strict=true, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="modelo_contrato", strict=true, nullable=true, description="Modelo de contrato a imprimir", requirements="\d+")
     *
     * @FOSRest\Get("/contrato/imprimir/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function imprimir($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        
        
        $htmlContrato = $this->contratoFacade->gerarHtmlContrato($mensagem, $id, $parametros);

        if ($htmlContrato === null || empty($mensagem) === false) {
            $response = new Response("<h2>Houve um erro</h2><p>$mensagem</p>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }
 
        $htmlContrato = $htmlContrato["texto"];

         $templateFile = $this->container->getParameter("kernel.project_dir") . "/public/templates/contrato.html";

      
       

        $nome        = str_replace('/', '-', $this->contratoFacade->getCodigoMatricula($id));
        $nomeArquivo = "contrato_$nome";

       

        if (file_exists($templateFile) === true) {
            $template = file_get_contents($templateFile);
            $html = str_replace("[CONTENT]", $htmlContrato, $template);
            // foi feito isto para geração do pdf mas deu problema para renderizar em tela a imagem do header.
            // $html = str_replace("\"", "\'", $html);
            echo $html;

        }
        else{
            echo "Modelo do contrato não encontrado contate o suporte";
        }
        $key = $id;
        $enviaEmail = true;
        $dados = $this->contratoFacade->getDadosSimplificado($id);
        if($dados != null){
            $chave  = $dados["chave_aceite"];
            if( !empty($chave ) ){
                $key = $chave ;
            }
    
            $dataAceite  = $dados["data_aceite"];            
            if( !empty($dataAceite ) ){
                $enviaEmail = false;
            }
            if(!isset($dados["nome_aluno"]) || !isset($dados["email_aluno"])){
                $enviaEmail = false;
            }
        }
        
        
        // converter para pdf e enviar para s3

        $url = "https://a6s6reqavotagjmrohec2zezga0exlrv.lambda-url.sa-east-1.on.aws";

        $data = [
            'htmldata' => $html,
            'bucket' => 'influx-manager-assets',
            'path' => 'contratos',
            'name' => $key
        ];

        $response = $this->httpClient->post( $url, [            
            'json' => $data
        ]);

        $body = $response->getBody();

        echo $body;


        if($enviaEmail){           
            
            // $email = "camargo@mobilesales.com.br";
            $email = $dados["email_aluno"];
            $this->enviaEmailAceite($email,$dados["nome_aluno"],$dados["chave_aceite"]);
            echo " Email:".$email;
        }

        die;

       // if (file_exists("$diretorioArquivo$nomeArquivo.pdf") === true) {
       //     unlink("$diretorioArquivo$nomeArquivo.pdf");
       // }

        
      //  $pdfGenerator = $this->get('knp_snappy.pdf');

      
       // $pdfGenerator->generateFromHtml($htmlContrato, "$diretorioArquivo$nomeArquivo.pdf", ['page-size' => 'A4']);

      //  echo $nomeArquivo ;
      //  die;


      //  $pdf = new PDFMerger;
      //  $pdf->addPDF("$diretorioArquivo$nomeArquivo.pdf");
      //  $pdf->merge("download", "$nomeArquivo.pdf");

       // unlink("$diretorioArquivo$nomeArquivo.pdf");

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/enviar/{id}",
     *     summary="Envia um contrato",
     *     description="Envia um contrato",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna contrato em PDF"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     *
     * @FOSRest\Get("/contrato/enviar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enviar($id)
    {
       
        

        try {
            $enviaEmail = true;
            $dados = $this->contratoFacade->getDadosSimplificado($id);
            if($dados == null || $dados == false){
                $enviaEmail = false;  
            }
            else
            {
                $chave  = $dados["chave_aceite"];
                if( !empty($chave ) ){
                    $key = $chave ;
                }
        
                $dataAceite  = $dados["data_aceite"];            
                if( !empty($dataAceite ) ){
                    $enviaEmail = false;
                }
                if(!isset($dados["nome_aluno"]) || !isset($dados["email_aluno"])){
                    $enviaEmail = false;
                }
            }
        

            if($enviaEmail){

                // $email = "camargo@mobilesales.com.br";
                $email = $dados["email_aluno"];
                $this->enviaEmailAceite($email,$dados["nome_aluno"],$dados["chave_aceite"]);
                echo " Email:".$email;
            }
            else{
                echo "dados inválidos, verifique se o contrato já foi impresso e se o aluno possui email cadastrado";
            }
        } catch (Exception $e) {
            echo "Erro ao processar solicitação.";
        }

        die;

      

    }

    public function enviaEmailAceite($emailTo, $nome, $chave)
    {
        $urlAceite = "https://api.manager.influx.com.br/api/contrato/aceitar/".$chave;
        $urlLink = "https://influx-manager-assets.s3.sa-east-1.amazonaws.com/contratos/".$chave.".pdf";
      
            $mensagem = "
                <p>Parabéns {$nome}!</p>

                <p>Você está um passo mais perto de alcançar seus sonhos com o inglês ou o espanhol!
                O seu contrato já está pronto e está disponível no link abaixo. Ao clicar no botão “Aceite” você nos confirma que leu o contrato e está de acordo com todos os seus termos.</p>

                <p> Nós agradacemos a sua confiança e desejamos um semestre cheio de experiências incríveis! Caso haja alguma dúvida, nos chame sem hesitar!</p>

                <p>link para o contrato:</p>
                <a href=$urlLink> Ver Contrato<a>
                

                <p>Clique no link abaixo para confirmar que leu e está de acordo com os termos do contrato.</p>
                <div style='text-align: center;'>
                <button ><a href='$urlAceite'> Aceitar<a> </button>
                </div>
                ";
            if( $emailTo != ""){
                $this->emailAwsSender->setEmails([ $emailTo]);
                $this->emailAwsSender->setCc(['camargo@mobilesales.com.br,gerenteti@influx.com.br']);
                $this->emailAwsSender->setSubject('Contrato Influx - Pendente de Aceite');
                $this->emailAwsSender->setMessage($mensagem);
                $retorno = $this->emailAwsSender->send();    
                echo "Email enviado para:".$emailTo;
            }
            else{
                echo "Aluno sem email cadastrado, não foi possivel enviar o contrato";
            }
            
    

        // return $retorno;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/contrato/{id}",
     *     summary="Buscar a contrato",
     *     description="Busca a contrato através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a contrato"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/contrato/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->contratoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }


     /**
     *
     * @SWG\Get(
     *     path="/api/contrato/aceitar/{chave}",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Contrato aceito"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Contrato não encontrado",
     *     )
     * )
     *
     * @FOSRest\Get("/contrato/aceitar/{chave}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aceitar($chave)
    {
        
        $sucesso = $this->contratoFacade->aceitarContrato( $chave);
        if ($sucesso) {
            echo " Contrato aceito! Obrigado";    
        }
        die;
        
    }


}
