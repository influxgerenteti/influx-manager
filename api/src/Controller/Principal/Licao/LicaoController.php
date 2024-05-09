<?php

namespace App\Controller\Principal\Licao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\LicaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class LicaoController extends GenericController
{

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
        $this->licaoFacade = new LicaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/licao/listar",
     *     summary="Listar licao",
     *     description="Lista as licao do banco",
     *     tags={"Licao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os licao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/licao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->licaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/licao/{id}",
     *     summary="Buscar a licao",
     *     description="Busca a licao através da ID",
     *     tags={"Licao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a licao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/licao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->licaoFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Licao não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/licao/buscar_por_livro/{livroId}",
     *     summary="Buscar a licao",
     *     description="Busca a licao através da ID",
     *     tags={"Licao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a licao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/licao/buscar_por_livro/{livroId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarLicoesPorLivro($livroId)
    {
        $mensagem  = "";
        $objetoORM = $this->licaoFacade->buscarLicoesPorLivro($mensagem, $livroId);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Livro não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/licao/criar",
     *     summary="Cria uma licao",
     *     description="Cria uma licao no banco",
     *     tags={"Licao"},
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
     * @FOSRest\RequestParam(name="planejamento_licao", strict=true, nullable=false, allowBlank=false, description="Planejamento Licao ID", requirements="\d+")
     * @FOSRest\RequestParam(name="numero",             strict=true, nullable=false, allowBlank=false, description="Numero da licao", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",          strict=true, nullable=false, allowBlank=false, description="Descricao da licao")
     * @FOSRest\RequestParam(name="observacao",         strict=false, nullable=false, allowBlank=true, description="Observacao da licao")
     *
     * @FOSRest\Post("/licao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->licaoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/licao/remover/{id}",
     *     summary="Remove uma licao",
     *     description="Remove uma licao no banco",
     *     tags={"Licao"},
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
     * @FOSRest\Delete("/licao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->licaoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
