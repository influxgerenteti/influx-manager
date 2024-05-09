<?php

namespace App\Controller\Principal\Livro;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\LivroFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class LivroController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\LivroFacade
     */
    private $livroFacade;
    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->livroFacade = new LivroFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/livro/listar",
     *     summary="Listar livro",
     *     description="Lista as livro do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os livro"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",    strict=false, allowBlank=false, default="1", description="Página para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="descricao", strict=true, nullable=true, allowBlank=true, description="Descrição livro")
     * @FOSRest\QueryParam(name="idioma",    strict=true, nullable=true, allowBlank=true, description="Idioma", requirements="\d+")
     * @FOSRest\QueryParam(name="curso",     strict=true, nullable=true, allowBlank=true, description="Curso", requirements="\d+")
     * @FOSRest\QueryParam(name="order",     strict=false, nullable=true,  description="Coluna de ordenação", default="li.descricao")
     * @FOSRest\QueryParam(name="direcao",   strict=false, nullable=true,  description="ASC|DESC", default="ASC")
     *
     * @FOSRest\Get("/livro/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->livroFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/livro/{id}",
     *     summary="Buscar a livro",
     *     description="Busca a livro através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a livro"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/livro/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->livroFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/livro/criar",
     *     summary="Cria uma livro",
     *     description="Cria uma livro no banco",
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
     * @FOSRest\RequestParam(name="descricao",          strict=true, nullable=false, allowBlank=false, description="Descricao Livro")
     * @FOSRest\RequestParam(name="item",               strict=true, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="planejamento_licao", strict=true, nullable=false, allowBlank=false, description="Planejamento Licao", requirements="\d+")
     * @FOSRest\RequestParam(name="proximo_livro",      strict=false, nullable=false, allowBlank=false, description="Proximo Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",              strict=true, nullable=false, allowBlank=false, description="Array de Cursos", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/livro/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->livroFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/livro/atualizar/{id}",
     *     summary="Atualiza um livro",
     *     description="Atualiza um livro no banco",
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
     * @FOSRest\RequestParam(name="descricao",          strict=false, nullable=false, allowBlank=false, description="Descricao Livro")
     * @FOSRest\RequestParam(name="item",               strict=false, nullable=false, allowBlank=false, description="Item", requirements="\d+")
     * @FOSRest\RequestParam(name="planejamento_licao", strict=false, nullable=false, allowBlank=false, description="Planejamento Licao", requirements="\d+")
     * @FOSRest\RequestParam(name="proximo_livro",      strict=false, nullable=false, allowBlank=false, description="Proximo Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",           strict=false, nullable=false, allowBlank=false, description="Situação", requirements="A|I")
     * @FOSRest\RequestParam(name="curso",              strict=false, nullable=true, allowBlank=false, description="Array de Cursos", requirements="\d+", map=true)
     *
     * @FOSRest\Patch("/livro/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->livroFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/livro/remover/{id}",
     *     summary="Remove uma livro",
     *     description="Remove uma livro no banco",
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
     * @FOSRest\Delete("/livro/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->livroFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
