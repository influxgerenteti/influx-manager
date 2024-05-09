<?php

namespace App\Controller\Principal\Curso;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\CursoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class CursoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\CursoFacade
     */
    private $cursoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->cursoFacade = new CursoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/curso/listar",
     *     summary="Listar curso",
     *     description="Lista as curso do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os curso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",       strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",        strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",      strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="buscar_todas", strict=false, nullable=true,  description="Se busca todos cursos ou faz por paginação")
     * @FOSRest\QueryParam(name="situacao",     strict=false, nullable=true, default="T",description="Situação do registro", requirements="[A|I|T]")
     *
     * @FOSRest\Get("/curso/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->cursoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/curso/{id}",
     *     summary="Buscar a curso",
     *     description="Busca a curso através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a curso"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/curso/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->cursoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Curso não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/curso/criar",
     *     summary="Cria uma curso",
     *     description="Cria uma curso no banco",
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
     * @FOSRest\RequestParam(name="idioma",       strict=true, nullable=false, allowBlank=false, description="ID da tabela Idioma", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma",  strict=true, nullable=false, allowBlank=false, description="ID da tabela Modalidade turma", requirements="\d+")
     * @FOSRest\RequestParam(name="servico",      strict=true, nullable=false, allowBlank=false, description="item", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",    strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="sigla",        strict=true, nullable=false, allowBlank=false, description="sigla")
     * @FOSRest\RequestParam(name="situacao",     strict=true, nullable=false, allowBlank=false, description="situacao", requirements="[A|I]", default="A")
     * @FOSRest\RequestParam(name="idade_minima", strict=false, nullable=true, allowBlank=true, description="idade minima para o curso", requirements="\d+")
     * @FOSRest\RequestParam(name="idade_maxima", strict=false, nullable=true, allowBlank=true, description="idade maxima para o curso", requirements="\d+")
     * @FOSRest\RequestParam(name="intensidade",  strict=true, nullable=false, allowBlank=false, description="Intensidade do Curso", requirements="(R|I|S)")
     *
     * @FOSRest\Post("/curso/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->cursoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/curso/atualizar/{id}",
     *     summary="Atualiza um curso",
     *     description="Atualiza um curso no banco",
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
     * @FOSRest\RequestParam(name="idioma",       strict=false, nullable=true, allowBlank=false, description="ID da tabela Idioma", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma",   strict=false, nullable=true, allowBlank=false, description="ID da tabela Modalidade Turma", requirements="\d+")
     * @FOSRest\RequestParam(name="servico",      strict=true, nullable=false, allowBlank=false, description="item", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",    strict=false, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="sigla",        strict=false, nullable=false, allowBlank=false, description="sigla")
     * @FOSRest\RequestParam(name="situacao",     strict=false, nullable=false, allowBlank=false, description="situacao", requirements="[A|I]")
     * @FOSRest\RequestParam(name="idade_minima", strict=false, nullable=true, allowBlank=true, description="idade minima para o curso", requirements="\d+")
     * @FOSRest\RequestParam(name="idade_maxima", strict=false, nullable=true, allowBlank=true, description="idade maxima para o curso", requirements="\d+")
     * @FOSRest\RequestParam(name="intensidade",  strict=true, nullable=false, allowBlank=false, description="Intensidade do Curso", requirements="(R|I|S)")
     *
     * @FOSRest\Patch("/curso/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->cursoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/curso/remover/{id}",
     *     summary="Remove uma curso",
     *     description="Remove uma curso no banco",
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
     * @FOSRest\Delete("/curso/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->cursoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
