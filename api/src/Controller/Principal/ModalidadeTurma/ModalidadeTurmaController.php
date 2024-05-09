<?php

namespace App\Controller\Principal\ModalidadeTurma;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ModalidadeTurmaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ModalidadeTurmaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ModalidadeTurmaFacade
     */
    private $modalidadeTurmaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->modalidadeTurmaFacade = new ModalidadeTurmaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/modalidade_turma/listar",
     *     summary="Listar modalidade_turma",
     *     description="Lista as modalidade_turma do banco",
     *     produces={"application/json"},
     *     tags={"Modalidade Turma"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os modalidade_turma"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/modalidade_turma/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->modalidadeTurmaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/modalidade_turma/{id}",
     *     summary="Buscar a modalidade_turma",
     *     description="Busca a modalidade_turma através da ID",
     *     tags={"Modalidade Turma"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a modalidade_turma"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/modalidade_turma/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->modalidadeTurmaFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "ModalidadeTurma não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }


}
