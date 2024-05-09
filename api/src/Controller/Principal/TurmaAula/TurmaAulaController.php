<?php

namespace App\Controller\Principal\TurmaAula;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TurmaAulaFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\LicaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TurmaAulaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\TurmaAulaFacade
     */
    private $turmaAulaFacade;

    /**
     *
     * @var \App\Facade\Principal\LicaoFacade
     */
    private $licaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->turmaAulaFacade = new TurmaAulaFacade(self::getManagerRegistry());
        $this->licaoFacade     = new LicaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/listar",
     *     summary="Listar turma_aula",
     *     description="Lista as turma_aula do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=false, description="Realiza a busca das aulas por turma", requirements="\d+")
     * @FOSRest\QueryParam(name="turma_aula", strict=false, allowBlank=false, description="Realiza a busca das aulas por turmaAula", requirements="\d+")
     *
     * @FOSRest\Get("/turma_aula/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->turmaAulaFacade->listar($parametros[ConstanteParametros::CHAVE_TURMA], $parametros, $mensagem);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/buscar_aulas_turma",
     *     summary="Busca as turma_aula da turma",
     *     description="Lista as turma_aula da turma",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade", strict=false, allowBlank=true, nullable=true, description="Modalidade a ser filtrada", requirements="V|P")
     *
     * @FOSRest\Get("/turma_aula/buscar_aulas_turma/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAulasTurma($turmaId, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->turmaAulaFacade->listarPorTurma($turmaId, $parametros, $mensagem);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/buscar_licoes/{turmaId}",
     *     summary="Listar turma_aula",
     *     description="Lista as licoes da Turma do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     *
     * @FOSRest\Get("/turma_aula/buscar_licoes/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarLicoes($turmaId, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_TURMA] = $turmaId;
        $resultados = $this->licaoFacade->listarLicoesPorTurma($parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/buscar_historico_aulas/{turmaId}",
     *     summary="Listar turma_aula",
     *     description="Lista as Historico de aulas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     *
     * @FOSRest\Get("/turma_aula/buscar_historico_aulas/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarHistoricoAulasPorTurma($turmaId)
    {
        $mensagem   = "";
        $resultados = $this->turmaAulaFacade->listarHistorico($turmaId);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/buscar_home_work/{turmaId}",
     *     summary="Listar turma_aula",
     *     description="Lista as HomeWorks",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     *
     * @FOSRest\Get("/turma_aula/buscar_home_work/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaHomeworkPorTurma($turmaId)
    {
        $mensagem   = "";
        $resultados = $this->turmaAulaFacade->buscarHomeworkPorTurma($turmaId);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma_aula/buscar_licoes_realizadas/{turmaId}",
     *     summary="Listar turma_aula",
     *     description="Lista as licoes da Turma do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\QueryParam(name="turma_aula", strict=true, allowBlank=false, description="Turma Aula ID", requirements="\d+")
     *
     * @FOSRest\Get("/turma_aula/buscar_licoes_realizadas/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarLicoesRealizadas($turmaId, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_TURMA] = $turmaId;
        $resultados = $this->licaoFacade->listarLicoesPorTurmaETurmaAula($parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }


}
