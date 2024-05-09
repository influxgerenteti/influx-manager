<?php

namespace App\Controller\Principal\RelacionamentoAluno;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\RelacionamentoAlunoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class RelacionamentoAlunoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\RelacionamentoAlunoFacade
     */
    private $relacionamentoAlunoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->relacionamentoAlunoFacade = new RelacionamentoAlunoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relacionamento_aluno/listar",
     *     summary="Listar relacionamento_aluno",
     *     description="Lista as relacionamento_aluno do banco",
     *     tags={"Relacionamento Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os relacionamento_aluno"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/relacionamento_aluno/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->relacionamentoAlunoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relacionamento_aluno/{id}",
     *     summary="Buscar a relacionamento_aluno",
     *     description="Busca a relacionamento_aluno através da ID",
     *     tags={"Relacionamento Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a relacionamento_aluno"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/relacionamento_aluno/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->relacionamentoAlunoFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }


}
