<?php

namespace App\Controller\Principal\ReposicaoAula;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ReposicaoAulaFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Facade\Principal\ContaReceberFacade;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\AtividadeExtraFacade;
use App\Facade\Principal\PagamentoFuncionarioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ReposicaoAulaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ReposicaoAulaFacade
     */
    private $reposicaoAulaFacade;

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
     * @var \App\Facade\Principal\AtividadeExtraFacade
     */
    private $atividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\PagamentoFuncionarioFacade
     */
    private $pagamentoFuncionarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->reposicaoAulaFacade       = new ReposicaoAulaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->contaReceberFacade         = new ContaReceberFacade(self::getManagerRegistry());
        $this->atividadeExtraFacade       = new AtividadeExtraFacade(self::getManagerRegistry());
        $this->pagamentoFuncionarioFacade = new PagamentoFuncionarioFacade(self::getManagerRegistry());
    }

    /**
     * Gera uma ocorrencia academica(monta parametros e cria objeto) com a situacao encerrada
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $itemId
     * @param array $obsevacaoOcorrencia
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     *
     * @todo Refatorar aqui e em AtividadeExtraController para mover para um lugar: facade, bo, generic controller, para que qualquer lugar possa ser chamado de forma generica
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademica(&$mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia, &$reposicaoAulaORM)
    {
        $observacaoVazia = "";
        $parametrosOcorrenciaAcademica = $this->ocorrenciaAcademicaFacade->gerarParametrosOcorrenciaAcademica($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $observacaoVazia, SituacoesSistema::SITUACAO_FECHADO);
        if (empty($mensagemErro) === false){
            return null;
        }
            $ocorrenciaAcademicaORM = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                return null;
            } else {
                foreach ($obsevacaoOcorrencia as $observacaoTexto) {
                    $parametrosOcorrenciaAcademica[ConstanteParametros::CHAVE_TEXTO] = $observacaoTexto;
                    $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
                    if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                        $bSuccesso = false;
                        break;
                    }
                }
                return $ocorrenciaAcademicaORM;
            }

        return null;
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
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     *
     * @return boolean
     */
    private function gerarContaReceber(&$mensagemErro, $valor, $formaCobrancaId, $franqueadaId, $alunoId, $usuarioId, $itemId, &$reposicaoAulaORM)
    {
        if (is_null($reposicaoAulaORM->getContaReceber()) === false) {
            return true;
        }

        $parametrosContaReceber = $this->contaReceberFacade->gerarParametrosContaReceberTituloReceber($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $valor, $formaCobrancaId, $itemId);
        $objetoORM = $this->contaReceberFacade->criar($mensagemErro, $parametrosContaReceber);
        $bRetorno  = (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
        if ($bRetorno === true) {
            $reposicaoAulaORM->setContaReceber($objetoORM);
        }

        return $bRetorno;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/reposicao_aula/listar",
     *     summary="Listar reposicao_aula",
     *     description="Lista as reposicao_aula do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os reposicao_aula"
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
     * @FOSRest\QueryParam(name="responsavel_execucao", strict=false, nullable=true, description="ID do item", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",              strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/reposicao_aula/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros       = $request->all();
        $reposicaoAulas   = $this->reposicaoAulaFacade->listar($parametros);
        $atividadesExtras = $this->atividadeExtraFacade->listar($parametros);
        $retorno          = [
            "atividades_extras" => $atividadesExtras,
            "reposicao_aulas"   => $reposicaoAulas,
        ];
        return ResponseFactory::ok($retorno);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/reposicao_aula/listar_simples",
     *     summary="Listar reposicao_aula",
     *     description="Lista as reposicao_aula do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os reposicao_aula"
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
     * @FOSRest\QueryParam(name="responsavel_execucao", strict=false, nullable=true, description="ID do item", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",              strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/reposicao_aula/listar_simples")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista_simples(ParamFetcher $request)
    {
        $parametros       = $request->all();

        $parametros['order'] = 'ra.data_hora_inicio';
        $parametros['direcao'] = 'DESC';
        
        $reposicaoAulas   = $this->reposicaoAulaFacade->listar($parametros);
     //   $parametros['order'] = null;
        //$atividadesExtras = $this->atividadeExtraFacade->listar($parametros);
        $retorno          = [
            //"atividades_extras" => $atividadesExtras,
            "reposicao_aulas"   => $reposicaoAulas,
        ];
        return ResponseFactory::ok($retorno);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/reposicao_aula/{id}",
     *     summary="Buscar a reposicao_aula",
     *     description="Busca a reposicao_aula através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a reposicao_aula"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/reposicao_aula/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        $mensagem  = " ";
        $objetoORM = $this->reposicaoAulaFacade->buscarPorId($mensagem, $id);

        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/reposicao_aula/criar",
     *     summary="Cria uma reposicao_aula",
     *     description="Cria uma reposicao_aula no banco",
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
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=false, allowBlank=false, description="Aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",                strict=true, nullable=false, allowBlank=false, description="Turma", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",                strict=true, nullable=false, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="licao",                strict=true, nullable=false, allowBlank=false, description="Licao", requirements="\d+")
     * @FOSRest\RequestParam(name="item",                 strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",      strict=true, nullable=true, allowBlank=true, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario_solicitante",  strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_execucao", strict=true, nullable=false, allowBlank=false, description="Responsavel Execucao", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade",  strict=true, nullable=true, allowBlank=true, default="", description="Observacao")
     * @FOSRest\RequestParam(name="isenta",               strict=true, nullable=false, description="Verifica se atividade gera contas receber", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="data",                 strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",          strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="concluido",            strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="forma_cobranca",       strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",                strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="presenca",             strict=true, nullable=true, allowBlank=true, description="Presenca", requirements="(P|F)", default="P")
     * @FOSRest\RequestParam(name="personal",             strict=true, nullable=false, description="Personal", default="0", requirements="(0|1)")
     *
     * @FOSRest\RequestParam(name="nota_mid_term_oral",               strict=true, nullable=true, allowBlank=true, description="Mid Term Oral")
     * @FOSRest\RequestParam(name="nota_mid_term_escrita",            strict=true, nullable=true, allowBlank=true, description="Mid Term Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_mid_term_test",               strict=true, nullable=true, allowBlank=true, description="Mid Term Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_mid_term_composition",        strict=true, nullable=true, allowBlank=true, description="Mid Term Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_oral",                  strict=true, nullable=true, allowBlank=true, description="Final oral")
     * @FOSRest\RequestParam(name="nota_final_escrita",               strict=true, nullable=true, allowBlank=true, description="Final Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_test",                  strict=true, nullable=true, allowBlank=true, description="Final Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_composition",           strict=true, nullable=true, allowBlank=true, description="Final Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_oral",        strict=true, nullable=true, allowBlank=true, description="Mid Term Oral")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_escrita",     strict=true, nullable=true, allowBlank=true, description="Mid Term Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_test",        strict=true, nullable=true, allowBlank=true, description="Mid Term Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_composition", strict=true, nullable=true, allowBlank=true, description="Mid Term Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_oral",           strict=true, nullable=true, allowBlank=true, description="Final oral")
     * @FOSRest\RequestParam(name="nota_retake_final_escrita",        strict=true, nullable=true, allowBlank=true, description="Final Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_test",           strict=true, nullable=true, allowBlank=true, description="Final Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_composition",    strict=true, nullable=true, allowBlank=true, description="Final Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     *
     * @FOSRest\RequestParam(name="observacao_ocorrencia_academicas", strict=true, nullable=true, allowBlank=true, description="Lista de Observacao de ocorrencia academica", map=true)
     *
     * @FOSRest\Post("/reposicao_aula/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        
        $parametros = $paramFetcher->all();
        $usuarioID  = $request->headers->get('Authorization-User-ID');
        $mensagem   = "";
        self::getEntityManager()->getConnection()->beginTransaction(); // suspend auto-commit
        try {
        
           

            if ((isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === false) || (isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === true) && (is_array($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === false)) {
                $parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS] = [];
            }

            $ocorrencia_academica = $this->gerarOcorrenciaAcademica($mensagem, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNO], $usuarioID, $parametros[ConstanteParametros::CHAVE_ITEM], $parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS], $objetoORM);
            if($ocorrencia_academica){
                $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA] = $ocorrencia_academica;
            
                $objetoORM  = $this->reposicaoAulaFacade->criar($mensagem, $parametros);
                if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }


                if ((bool) $parametros[ConstanteParametros::CHAVE_ISENTA] === false) {
                    if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                    }
                    if ($this->gerarContaReceber($mensagem, $parametros[ConstanteParametros::CHAVE_VALOR], $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNO], $usuarioID, $parametros[ConstanteParametros::CHAVE_ITEM], $objetoORM) === false) {
                        return ResponseFactory::conflict(["parametros_conta_receber" => $parametros], $mensagem);
                    }
                }

                self::getEntityManager()->flush();
                self::getEntityManager()->getConnection()->commit();

                return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
            }
            else{
                throw new Exception('não foi possível registrar ocorrencia academica');
            }    
                   
        
        } catch (Exception $e) {
            self::getEntityManager()->getConnection()->rollBack();
            return ResponseFactory::badRequest(
                [],
                "Falha ao processar Agendamento:".$e->getMessage()
            );            
        }
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/reposicao_aula/alterar/{id}",
     *     summary="Atualiza um reposicao_aula",
     *     description="Atualiza um reposicao_aula no banco",
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
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=false, allowBlank=false, description="Aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",                strict=true, nullable=false, allowBlank=false, description="Turma", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",                strict=true, nullable=false, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="licao",                strict=true, nullable=false, allowBlank=false, description="Licao", requirements="\d+")
     * @FOSRest\RequestParam(name="item",                 strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",      strict=true, nullable=true, allowBlank=true, description="Sala Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario_solicitante",  strict=true, nullable=false, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="responsavel_execucao", strict=true, nullable=false, allowBlank=false, description="Responsavel Execucao", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao_atividade",  strict=true, nullable=true, allowBlank=true, default="", description="Observacao")
     * @FOSRest\RequestParam(name="isenta",               strict=true, nullable=false, description="Verifica se atividade gera contas receber", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="data",                 strict=true, nullable=false, allowBlank=false, description="Data de inicio da atividade")
     * @FOSRest\RequestParam(name="hora_inicio",          strict=true, nullable=false, allowBlank=false, description="Horario de inicio da atividade")
     * @FOSRest\RequestParam(name="concluido",            strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="cancelamento",         strict=true, nullable=false, description="Marca como cancelado", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="forma_cobranca",       strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",                strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="presenca",             strict=true, nullable=true, allowBlank=true, description="Presenca", requirements="(P|F)", default="P")
     * @FOSRest\RequestParam(name="personal",             strict=true, nullable=false, description="Personal", default="0", requirements="(0|1)")
     *
     * @FOSRest\RequestParam(name="nota_mid_term_oral",               strict=true, nullable=true, allowBlank=true, description="Mid Term Oral")
     * @FOSRest\RequestParam(name="nota_mid_term_escrita",            strict=true, nullable=true, allowBlank=true, description="Mid Term Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_mid_term_test",               strict=true, nullable=true, allowBlank=true, description="Mid Term Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_mid_term_composition",        strict=true, nullable=true, allowBlank=true, description="Mid Term Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_oral",                  strict=true, nullable=true, allowBlank=true, description="Final oral")
     * @FOSRest\RequestParam(name="nota_final_escrita",               strict=true, nullable=true, allowBlank=true, description="Final Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_test",                  strict=true, nullable=true, allowBlank=true, description="Final Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_final_composition",           strict=true, nullable=true, allowBlank=true, description="Final Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_oral",        strict=true, nullable=true, allowBlank=true, description="Mid Term Oral")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_escrita",     strict=true, nullable=true, allowBlank=true, description="Mid Term Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_test",        strict=true, nullable=true, allowBlank=true, description="Mid Term Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_mid_term_composition", strict=true, nullable=true, allowBlank=true, description="Mid Term Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_oral",           strict=true, nullable=true, allowBlank=true, description="Final oral")
     * @FOSRest\RequestParam(name="nota_retake_final_escrita",        strict=true, nullable=true, allowBlank=true, description="Final Escrita", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_test",           strict=true, nullable=true, allowBlank=true, description="Final Test", requirements="^\d{0,5}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_retake_final_composition",    strict=true, nullable=true, allowBlank=true, description="Final Composition", requirements="^\d{0,5}+\.?\d{0,2}?$")
     *
     * @FOSRest\RequestParam(name="observacao_ocorrencia_academicas", strict=true, nullable=true, allowBlank=true, description="Lista de Observacao de ocorrencia academica", map=true)
     *
     * @FOSRest\Patch("/reposicao_aula/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros       = $paramFetcher->all();
        $mensagem         = "";
        $reposicaoAulaORM = null;
        $usuarioID        = $request->headers->get('Authorization-User-ID');
        if ((isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === false) || (isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === true) && (is_array($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS]) === false)) {
            $parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS] = [];
        }

        $funcionarioId = $this->ocorrenciaAcademicaFacade->retornaFuncionarioIdDoUsuario($usuarioID);
        $retorno       = $this->reposicaoAulaFacade->atualizar($mensagem, $id, $funcionarioId, $parametros, $reposicaoAulaORM);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        //if (((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) && ((bool) $parametros[ConstanteParametros::CHAVE_ISENTA] === false)) {
            if ((bool) $parametros[ConstanteParametros::CHAVE_ISENTA] === false) {
                if ($this->gerarContaReceber($mensagem, $parametros[ConstanteParametros::CHAVE_VALOR], $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNO], $usuarioID, $parametros[ConstanteParametros::CHAVE_ITEM], $reposicaoAulaORM) === false) {
                return ResponseFactory::conflict(["parametros_conta_receber" => $parametros], $mensagem);
            }
        }

        self::getEntityManager()->flush();

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/reposicao_aula/remover/{id}",
     *     summary="Remove uma reposicao_aula",
     *     description="Remove uma reposicao_aula no banco",
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
     * @FOSRest\Delete("/reposicao_aula/remover/{id}")
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
