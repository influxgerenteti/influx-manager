<?php

namespace App\Controller\Principal\AtividadeExtra;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Entity\Principal\ConvidadoAtividadeExtra;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AtividadeExtraFacade;
use App\Facade\Principal\ConvidadoAtividadeExtraFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\AlunoAtividadeExtraFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Facade\Principal\ContaReceberFacade;
use App\Facade\Principal\FollowupComercialFacade;
use App\Facade\Principal\InteressadoFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\InteressadoAtividadeExtraFacade;
use App\Facade\Principal\PagamentoFuncionarioFacade;
use App\Facade\Principal\FuncionarioFacade;

use DateTime;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class AtividadeExtraController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\InteressadoFacade
     */
    private $interessadoFacade;

    /**
     *
     * @var \App\Facade\Principal\AtividadeExtraFacade
     */
    private $atividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAtividadeExtraFacade
     */
    private $alunoAtividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\ConvidadoAtividadeExtraFacade
     */
    private $convidadoAtividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\FollowupComercialFacade
     */
    private $followupComercialFacade;

    /**
     *
     * @var \App\Facade\Principal\InteressadoAtividadeExtraFacade
     */
    private $interessadoAtividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\PagamentoFuncionarioFacade
     */
    private $pagamentoFuncionarioFacade;

    /**
     *
     * @var \App\Facade\Principal\FuncionarioFacade
     */
    private $funcionarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->atividadeExtraFacade          = new AtividadeExtraFacade(self::getManagerRegistry());
        $this->alunoAtividadeExtraFacade     = new AlunoAtividadeExtraFacade(self::getManagerRegistry());
        $this->convidadoAtividadeExtraFacade = new ConvidadoAtividadeExtraFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade     = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->contaReceberFacade      = new ContaReceberFacade(self::getManagerRegistry());
        $this->followupComercialFacade = new FollowupComercialFacade(self::getManagerRegistry());
        $this->interessadoFacade       = new InteressadoFacade(self::getManagerRegistry());
        $this->interessadoAtividadeExtraFacade = new InteressadoAtividadeExtraFacade(self::getManagerRegistry());
        $this->pagamentoFuncionarioFacade      = new PagamentoFuncionarioFacade(self::getManagerRegistry());
        $this->funcionarioFacade = new FuncionarioFacade(self::getManagerRegistry());
    }

    /**
     * Gera um novo aluno que tem a atividade extra vinculada
     *
     * @param string $mensagemErro
     * @param array $parametros
     * @param \App\Entity\Principal\AtividadeExtra|NULL $objetoORM
     * @param int|NULL $idAtividadeExtra
     *
     * @return boolean
     */
    private function gerarAlunoAtividadeExtra(&$mensagemErro, $parametros, $objetoORM=null, $idAtividadeExtra=null)
    {
        if (is_null($objetoORM) === false) {
            $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA] = $objetoORM;
        } else {
            $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA] = $idAtividadeExtra;
        }

        $atividadeAlunoORM = $this->alunoAtividadeExtraFacade->criarAtualizarAlunoAtividadeExtra($mensagemErro, $parametros);
        return (is_null($atividadeAlunoORM) === false) && (empty($mensagemErro) === true);
    }

    /**
     * Gera uma ocorrencia academica(monta parametros e cria objeto) com a situacao encerrada
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $itemId
     * @param string $obsevacaoOcorrencia
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademicaEncerrada(&$mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia)
    {
        if (is_null($obsevacaoOcorrencia) === true) {
            $obsevacaoOcorrencia = "";
        }

        $parametrosOcorrenciaAcademica = $this->ocorrenciaAcademicaFacade->gerarParametrosOcorrenciaAcademica($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia);
        $bSuccesso = empty($mensagemErro) === true;
        if ($bSuccesso === true) {
            $ocorrenciaAcademicaORM = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                $bSuccesso = false;
            } else {
                $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
                if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                    $bSuccesso = false;
                }
            }
        }

        return $bSuccesso;
    }

    /**
     * Gera conta a receber
     *
     * @param string $mensagemErro
     * @param double $valor
     * @param int $formaCobrancaId
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $itemId
     * @param \App\Entity\Principal\AtividadeExtra $atividadeExtraORM
     *
     * @return boolean
     */
    private function gerarContaReceber(&$mensagemErro, $valor, $formaCobrancaId, $franqueadaId, $alunoId, $usuarioId, $itemId, &$atividadeExtraORM)
    {
        $parametrosContaReceber = $this->contaReceberFacade->gerarParametrosContaReceberTituloReceber($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $valor, $formaCobrancaId, $itemId);
        $objetoORM = $this->contaReceberFacade->criar($mensagemErro, $parametrosContaReceber);
        $bRetorno  = (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
        if ($bRetorno === true) {
            $atividadeExtraORM->addContaReceber($objetoORM);
            $objetoORM->addAtividadeExtra($atividadeExtraORM);
        }

        return $bRetorno;
    }

    /**
     * Gera os dados conforme passagem de parametros
     *
     * @param array $mensagemErro Ponteiro para retorno de msg
     * @param array $parametros Array de parametros(root)
     * @param array $metaDataAlunoAtividadeExtra Array de parametros com as informações de alunos(dados_alunos)
     * @param int|NULL $idAtividadeExtra Id da atividade Extra para caso estejamos editando
     * @param \App\Entity\Principal\AtividadeExtra|NULL $objetoORM Objeto da atividade Extra caso estejamos criando
     * @param boolean $bGerarAlunoAtividadeExtra Parametro para informar se devemos criar um AlunoAtividadeExtra que tem relacionamento com AtividadeExtra(passar objeto ou id da atividade extra na chamada da função)
     * @param boolean $bGerarContaReceber Parametro para informar se deve ou não gerar conta a receber
     * @param boolean $bGerarOcorrenciaAcademica Parametro para informar se deve criar uma ocorrencia academia já fechada
     *
     * @return boolean
     */
    private function gerarAlunoAtividadeExtraContaReceberOcorrenciaAcademica(&$mensagemErro, $parametros, $metaDataAlunoAtividadeExtra, $idAtividadeExtra=null, $objetoORM=null, $bGerarAlunoAtividadeExtra=true, $bGerarContaReceber=false, $bGerarOcorrenciaAcademica=false)
    {
        $bSuccesso = true;
        foreach ($metaDataAlunoAtividadeExtra as $parametrosAlunoAtividadeExtra) {
            if ($bGerarAlunoAtividadeExtra === true) {
                if (($bSuccesso = $this->gerarAlunoAtividadeExtra($mensagemErro, $parametrosAlunoAtividadeExtra, $objetoORM, $idAtividadeExtra)) === false) {
                    break;
                }
            }

            if ($bGerarContaReceber === true) {
                if (($bSuccesso = $this->gerarContaReceber($mensagemErro, $parametros[ConstanteParametros::CHAVE_VALOR], $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametrosAlunoAtividadeExtra[ConstanteParametros::CHAVE_ALUNO], $parametros[ConstanteParametros::CHAVE_USUARIO], $parametros[ConstanteParametros::CHAVE_ITEM], $objetoORM)) === false) {
                    break;
                }
            }

            if ($bGerarOcorrenciaAcademica === true) {
                if (($bSuccesso = $this->gerarOcorrenciaAcademicaEncerrada($mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametrosAlunoAtividadeExtra[ConstanteParametros::CHAVE_ALUNO], $parametros[ConstanteParametros::CHAVE_USUARIO], $parametros[ConstanteParametros::CHAVE_ITEM], $parametros[ConstanteParametros::CHAVE_DESCRICAO_ATIVIDADE])) === false) {
                    break;
                }
            }
        }

        return $bSuccesso;
    }

    /**
     * Salvar os convidados da atividade extra
     *
     * @param string $mensagemErro Ponteiro para retorno de msg
     * @param int $franqueadaId id da franqueada
     * @param array $metaDataConvidadosAtividadeExtra Array de parametros com as informações dos convidados(dados_convidados)
     * @param \App\Entity\Principal\AtividadeExtra|NULL $objetoORM Objeto da atividade Extra caso estejamos criando
     */
    private function gerarConvidadoAtividadeExtra (&$mensagemErro, $franqueadaId, $metaDataConvidadosAtividadeExtra, $objetoORM=null)
    {

        foreach ($metaDataConvidadosAtividadeExtra as $parametrosConvidadoAtividadeExtra) {
            $parametrosConvidadoAtividadeExtra[ConstanteParametros::CHAVE_FRANQUEADA]      = $franqueadaId;
            $parametrosConvidadoAtividadeExtra[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA] = $objetoORM;

            $objetoConvidadoAtividadeORM = $this->convidadoAtividadeExtraFacade->criar($mensagemErro, $parametrosConvidadoAtividadeExtra);
        }

        return (is_null($objetoConvidadoAtividadeORM) === false) && (empty($mensagemErro) === true);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/atividade_extra/listar",
     *     summary="Listar atividade_extra",
     *     description="Lista as atividade_extra do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os atividade_extra"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",               strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",           strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="data_agendamento_de",  strict=false, nullable=true, description="Data de agendamento inicio")
     * @FOSRest\QueryParam(name="data_agendamento_ate", strict=false, nullable=true, description="Data de agendamento fim")
     * @FOSRest\QueryParam(name="item",                 strict=false, nullable=true, description="ID do item", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo",                 strict=false, nullable=true, description="Tipo do item")
     * @FOSRest\QueryParam(name="responsavel_execucao", strict=false, nullable=true, description="ID do item", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",              strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/atividade_extra/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros       = $request->all();
        $mensagem         = "";
        $atividadesExtras = $this->atividadeExtraFacade->listar($parametros);
        if ($atividadesExtras === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($atividadesExtras);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/atividade_extra/fetch",
     *     summary="Listar atividade_extra",
     *     description="Lista as atividade_extra do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os atividade_extra"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/atividade_extra/fetch")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fetch(ParamFetcher $request)
    {
        $parametros       = $request->all();
        $atividadesExtras = $this->atividadeExtraFacade->fetch($parametros);
        return ResponseFactory::ok($atividadesExtras);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/atividade_extra/{id}",
     *     summary="Buscar a atividade_extra",
     *     description="Busca a atividade_extra através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a atividade_extra"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/atividade_extra/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->atividadeExtraFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/atividade_extra/criar",
     *     summary="Cria uma atividade_extra",
     *     description="Cria uma atividade_extra no banco",
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
     * @FOSRest\RequestParam(name="item",                     strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",               strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",                  strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",          strict=true, nullable=false, allowBlank=false, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade",      strict=true, nullable=true,  allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="data",                     strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",              strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_final",               strict=true, nullable=false, allowBlank=false, description="Horario de término da atividade")
     * @FOSRest\RequestParam(name="concluido",                strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="isenta",                   strict=true, nullable=false, description="Verifica se atividade gera contas receber", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="quantidade_maxima_alunos", strict=false, nullable=true, allowBlank=true, description="Quantidade Maxima de alunos", requirements="\d+")
     * @FOSRest\RequestParam(name="forma_cobranca",           strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",                    strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\RequestParam(name="responsaveis_execucao", strict=true, nullable=false, allowBlank=false, description="Lista de Funcionarios ID", map=true, requirements="\d+")
     * @FOSRest\RequestParam(name="dados_alunos",          strict=false, nullable=true, allowBlank=true, description="Lista de Alunos para atividade extras", map=true)
     * @FOSRest\RequestParam(name="dados_convidados",      strict=false, nullable=true, allowBlank=true, description="Lista de convidados para atividade extras", map=true)
     *
     * @FOSRest\Post("/atividade_extra/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $metaDataAlunoAtividadeExtra      = $parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS];
        $metaDataConvidadosAtividadeExtra = $parametros[ConstanteParametros::CHAVE_DADOS_CONVIDADOS];

        if (is_null($metaDataAlunoAtividadeExtra) === true) {
            $metaDataAlunoAtividadeExtra = [];
        }

        if (is_null($metaDataConvidadosAtividadeExtra) === true) {
            $metaDataConvidadosAtividadeExtra = [];
        }

        $parametros[ConstanteParametros::CHAVE_USUARIO] = $request->headers->get('Authorization-User-ID');
        $mensagem = "";
        unset($parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS]);
        $objetoORM = $this->atividadeExtraFacade->criar($mensagem, $parametros);

        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((is_array($metaDataAlunoAtividadeExtra) === true)) {
            if (count($metaDataAlunoAtividadeExtra) > 0) {
                if ($this->gerarAlunoAtividadeExtraContaReceberOcorrenciaAcademica($mensagem, $parametros, $metaDataAlunoAtividadeExtra, null, $objetoORM) === false) {
                    return ResponseFactory::conflict(["parametros_dados_alunos" => $metaDataAlunoAtividadeExtra], $mensagem);
                }
            }
        } else {
            return ResponseFactory::conflict(["parametros_dados_alunos" => $metaDataAlunoAtividadeExtra], "Não foi passado uma lista de alunos valida para a requisição. Favor verificar novamente os parametros.");
        }

        if ((is_array($metaDataConvidadosAtividadeExtra) === true)) {
            if (count($metaDataConvidadosAtividadeExtra) > 0) {
                if ($this->gerarConvidadoAtividadeExtra($mensagem, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $metaDataConvidadosAtividadeExtra, $objetoORM) === false) {
                    return ResponseFactory::conflict(["parametros_dados_convidados" => $metaDataConvidadosAtividadeExtra], $mensagem);
                }
            }
        } else {
            return ResponseFactory::conflict(["parametros_dados_convidados" => $metaDataConvidadosAtividadeExtra], "Não foi passado uma lista de alunos valida para a requisição. Favor verificar novamente os parametros.");
        }

        if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
            if ((bool) $parametros[ConstanteParametros::CHAVE_ISENTA] === false) {
                if ($this->gerarAlunoAtividadeExtraContaReceberOcorrenciaAcademica($mensagem, $parametros, $metaDataAlunoAtividadeExtra, null, $objetoORM, false, true, true) === false) {
                    return ResponseFactory::conflict(["atividade-extra-controllerconta_receber_ocorrencia_academica"], $mensagem);
                }
            }
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/atividade_extra/alterar/{id}",
     *     summary="Atualiza um atividade_extra",
     *     description="Atualiza um atividade_extra no banco",
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
     * @FOSRest\RequestParam(name="item",                     strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",               strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",                  strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",          strict=true, nullable=false, allowBlank=false, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade",      strict=true, nullable=true,  allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="data",                     strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",              strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_final",               strict=true, nullable=false, allowBlank=false, description="Horario de término da atividade")
     * @FOSRest\RequestParam(name="concluido",                strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="cancelamento",             strict=true, nullable=false, description="Marca como cancelado", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="quantidade_maxima_alunos", strict=false, nullable=true, allowBlank=true, description="Quantidade Maxima de alunos", requirements="\d+")
     * @FOSRest\RequestParam(name="isenta",                   strict=true, nullable=false, description="Verifica se atividade gera contas receber", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="forma_cobranca",           strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",                    strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\RequestParam(name="responsaveis_execucao", strict=true, nullable=false, allowBlank=false, description="Lista de Funcionarios ID", map=true, requirements="\d+")
     * @FOSRest\RequestParam(name="dados_alunos",          strict=false, nullable=true, allowBlank=true, description="Lista de Alunos para atividade extras", map=true)
     * @FOSRest\RequestParam(name="dados_convidados",      strict=false, nullable=true, allowBlank=true, description="Lista de convidados para atividade extras", map=true)
     *
     * @FOSRest\Patch("/atividade_extra/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();


        //alteração de data
        // como a tabela não tem campo data ele ajusta os campos de inicio e fim
        if(isset($parametros['data'])){                        
            $newDate     = (new DateTime($parametros[ConstanteParametros::CHAVE_DATA]));
            $startDate     = (new DateTime($parametros[ConstanteParametros::CHAVE_HORA_INICIO]));           
            $startDate->setDate( $newDate->format('Y'), $newDate->format('m'), $newDate->format('d'));           
            $parametros[ConstanteParametros::CHAVE_HORA_INICIO] = $startDate->format("Y-m-d\TH:i:s.uP");
            $endDate       = (new DateTime($parametros[ConstanteParametros::CHAVE_HORA_FINAL]));
            $endDate->setDate( $newDate->format('Y'), $newDate->format('m'), $newDate->format('d'));           
            $parametros[ConstanteParametros::CHAVE_HORA_FINAL] = $endDate->format("Y-m-d\TH:i:s.uP");
        
        }
        


        $metaDataAlunoAtividadeExtra      = $parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS];
        $metaDataConvidadosAtividadeExtra = $parametros[ConstanteParametros::CHAVE_DADOS_CONVIDADOS];

        if (is_null($metaDataAlunoAtividadeExtra) === true) {
            $metaDataAlunoAtividadeExtra = [];
        }

        if (is_null($metaDataConvidadosAtividadeExtra) === true) {
            $metaDataConvidadosAtividadeExtra = [];
        }

        $mensagem          = "";
        $atividadeExtraORM = null;
        unset($parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS]);
        $retorno = $this->atividadeExtraFacade->atualizar($mensagem, $id, $parametros, $atividadeExtraORM);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((is_array($metaDataAlunoAtividadeExtra) === true)) {
            if (count($metaDataAlunoAtividadeExtra) > 0) {
                if ($this->gerarAlunoAtividadeExtraContaReceberOcorrenciaAcademica($mensagem, $parametros, $metaDataAlunoAtividadeExtra, null, $atividadeExtraORM) === false) {
                    return ResponseFactory::conflict(["parametros_dados_alunos_1" => $metaDataAlunoAtividadeExtra], $mensagem);
                }
            }
        } else {
            return ResponseFactory::conflict(["parametros_dados_alunos" => $metaDataAlunoAtividadeExtra], "Não foi passado uma lista de alunos valida para a requisição. Favor verificar novamente os parametros.");
        }

        if ((is_array($metaDataConvidadosAtividadeExtra) === true)) {
            if (count($metaDataConvidadosAtividadeExtra) > 0) {
                if ($this->gerarConvidadoAtividadeExtra($mensagem, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $metaDataConvidadosAtividadeExtra, $atividadeExtraORM) === false) {
                    return ResponseFactory::conflict(["parametros_dados_convidados" => $metaDataConvidadosAtividadeExtra], $mensagem);
                }
            }
        } else {
            return ResponseFactory::conflict(["parametros_dados_convidados" => $metaDataConvidadosAtividadeExtra], "Não foi passado uma lista de alunos valida para a requisição. Favor verificar novamente os parametros.");
        }

        if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
            if ((bool) $parametros[ConstanteParametros::CHAVE_ISENTA] === false) {
                if ($this->gerarAlunoAtividadeExtraContaReceberOcorrenciaAcademica($mensagem, $parametros, $metaDataAlunoAtividadeExtra, null, $atividadeExtraORM, false, true, true) === false) {
                    return ResponseFactory::conflict(["atividade-extra-controllerconta_receber_ocorrencia_academica"], $mensagem);
                }
            }
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            
        }
        self::getEntityManager()->flush();            
        return ResponseFactory::created(["objetoORM" =>  $atividadeExtraORM->getId()], "Registro atualizado com sucesso!");
        // return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/atividade_extra/concluir/{id}",
     *     summary="Seta uma atividade_extra como concluída",
     *     description="Seta uma atividade_extra como concluída no banco",
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
     * @FOSRest\Patch("/atividade_extra/concluir/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function concluir($id)
    {
        $mensagem          = "";
        $atividadeExtraORM = null;
        $retorno           = $this->atividadeExtraFacade->concluir($mensagem, $id, $atividadeExtraORM);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => []], $mensagem);
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/atividade_extra/novo_nivelamento",
     *     summary="Cria uma atividade_extra",
     *     description="Cria uma atividade_extra no banco",
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
     * @FOSRest\RequestParam(name="item",                strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",          strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",             strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",     strict=true, nullable=false, allowBlank=false, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade", strict=true, nullable=false, allowBlank=false, description="Observacao")
     * @FOSRest\RequestParam(name="data",                strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",         strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_final",          strict=true, nullable=false, allowBlank=false, description="Horario de término da atividade")
     * @FOSRest\RequestParam(name="concluido",           strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="interessado",         strict=true, nullable=false, allowBlank=false, description="Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",               strict=false, nullable=true, allowBlank=true, description="Livro")
     *
     * @FOSRest\RequestParam(name="responsaveis_execucao", strict=true, nullable=false, allowBlank=false, description="Lista de Funcionarios ID", map=true, requirements="\d+")
     * @FOSRest\RequestParam(name="follow_ups",            strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Post("/atividade_extra/novo_nivelamento")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function novoNivelamento(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA]) === true) &&
                (isset($parametros[ConstanteParametros::CHAVE_HORA_INICIO]) === true) && 
                    (isset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === true)) {


                        $data = new Datetime($parametros[ConstanteParametros::CHAVE_DATA]);
                        $data = $data->format('Y-m-d');
                        $horaIni = new Datetime($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
                        $horaIni = $horaIni->format('H:i:s');
                        $horaFim = new Datetime($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                        $horaFim = $horaFim->format('H:i:s');
                        $parametros[ConstanteParametros::CHAVE_HORA_INICIO] = $data. 'T'. $horaIni.'.000Z';
                        $parametros[ConstanteParametros::CHAVE_HORA_FINAL] = $data. 'T'. $horaFim.'.000Z';        
        }

        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        $objetoORM  = $this->atividadeExtraFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $interessadoAtividadeExtraORM = $this->interessadoAtividadeExtraFacade->criar($mensagem, $objetoORM, $parametros);
        if ((is_null($interessadoAtividadeExtraORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros_interessadoAtividadeExtra" => $parametros], $mensagem);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === false) {
            $parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] = [];
        }

        $consultorFuncionario = $objetoORM->getResponsaveisExecucacao()->get(0);
        foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $followUpData) {
            $originalParams = $followUpData;
            $followUpData[ConstanteParametros::CHAVE_INTERESSADO]      = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM];
            $followUpData[ConstanteParametros::CHAVE_USUARIO]          = $usuarioId;
            $followUpData[ConstanteParametros::CHAVE_CURSO_PRETENDIDO] = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getCurso();
            $followUpData[ConstanteParametros::CHAVE_GRAU_INTERESSE]   = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getGrauInteresse();
            $followUpData[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO] = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getDataValidadePromocao();
            $followUpData[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]  = $consultorFuncionario;

            $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpData);
            if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                unset($followUpData[ConstanteParametros::CHAVE_INTERESSADO]);
                return ResponseFactory::conflict(["parametros_followup" => $originalParams], $mensagem);
            }
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/atividade_extra/alterar_nivelamento/{id}",
     *     summary="Atualiza um atividade_extra",
     *     description="Atualiza um atividade_extra no banco",
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
     * @FOSRest\RequestParam(name="item",                strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",          strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",             strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",     strict=true, nullable=false, allowBlank=false, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade", strict=true, nullable=false, allowBlank=false, description="Observacao")
     * @FOSRest\RequestParam(name="data",                strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",         strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_final",          strict=true, nullable=false, allowBlank=false, description="Horario de término da atividade")
     * @FOSRest\RequestParam(name="concluido",           strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="cancelamento",        strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="interessado",         strict=true, nullable=false, allowBlank=false, description="Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",               strict=false, nullable=true, allowBlank=true, description="Livro")
     *
     * @FOSRest\RequestParam(name="responsaveis_execucao", strict=true, nullable=false, allowBlank=false, description="Lista de Funcionarios ID", map=true, requirements="\d+")
     * @FOSRest\RequestParam(name="follow_ups",            strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Patch("/atividade_extra/alterar_nivelamento/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alterarNivelamento($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros          = $paramFetcher->all();
        $parametrosOriginais = $parametros;
        $mensagem            = "";
        $usuarioId           = $request->headers->get('Authorization-User-ID');
        
        
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA]) === true) &&
                (isset($parametros[ConstanteParametros::CHAVE_HORA_INICIO]) === true) && 
                    (isset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]) === true)) {
                        $data = new Datetime($parametros[ConstanteParametros::CHAVE_DATA]);
                        $data = $data->format('Y-m-d');
                        $horaIni = new Datetime($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
                        $horaIni = $horaIni->format('H:i:s');
                        $horaFim = new Datetime($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                        $horaFim = $horaFim->format('H:i:s');
                        $parametros[ConstanteParametros::CHAVE_HORA_INICIO] = $data. 'T'. $horaIni.'.000Z';
                        $parametros[ConstanteParametros::CHAVE_HORA_FINAL] = $data. 'T'. $horaFim.'.000Z';        
        }
        $retorno = $this->atividadeExtraFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        $retornoInteressadoAtividadeExtra = $this->interessadoAtividadeExtraFacade->atualizar($mensagem, $id, $parametros);
        if ($retornoInteressadoAtividadeExtra === false) {
            return ResponseFactory::badRequest(["parametros_interessadoAtividadeExtra" => $parametros], $mensagem);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === false) {
            $parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] = [];
        }

        $consultorFuncionario = $this->funcionarioFacade->buscarId($mensagem, (int) $parametrosOriginais[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO][0]);
        foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $followUpData) {
            $followUpData[ConstanteParametros::CHAVE_INTERESSADO]      = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM];
            $followUpData[ConstanteParametros::CHAVE_USUARIO]          = $usuarioId;
            $followUpData[ConstanteParametros::CHAVE_CURSO_PRETENDIDO] = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getCurso();
            $followUpData[ConstanteParametros::CHAVE_GRAU_INTERESSE]   = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getGrauInteresse();
            $followUpData[ConstanteParametros::CHAVE_DATA_VALIDADE_PROMOCAO] = $parametros[ConstanteParametros::CHAVE_INTERESSADO_ORM]->getDataValidadePromocao();
            $followUpData[ConstanteParametros::CHAVE_CONSULTOR_FUNCIONARIO]  = $consultorFuncionario;

            $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpData);
            if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                unset($followUpData[ConstanteParametros::CHAVE_INTERESSADO]);
                return ResponseFactory::conflict(["parametros_followup" => $followUpData], $mensagem);
            }
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/atividade_extra/remover/{id}",
     *     summary="Remove uma atividade_extra",
     *     description="Remove uma atividade_extra no banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna removido com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/atividade_extra/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
