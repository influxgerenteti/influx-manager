<?php

namespace App\Controller\Principal\ClassificacaoAluno;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ClassificacaoAlunoFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class ClassificacaoAlunoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ClassificacaoAlunoFacade
     */
    private $classificacaoAlunoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->classificacaoAlunoFacade = new ClassificacaoAlunoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/classificacao_aluno/listar",
     *     summary="Listar classificacao_aluno",
     *     description="Lista as classificacao_aluno do banco",
     *     tags={"Classificacao Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os classificacao_aluno"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",     strict=true, allowBlank=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",         strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="itensPorPagina", strict=false,allowBlank=false, default="10", description="Quantidade de itens a ser exibido", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="order",          strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",        strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/classificacao_aluno/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->classificacaoAlunoFacade->listar($mensagem, $parametros);
        if ($resultados === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/classificacao_aluno/{id}",
     *     summary="Buscar a classificação de aluno",
     *     description="Busca a classificação de aluno através da ID",
     *     tags={"Classificacao Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a classificação de aluno"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/classificacao_aluno/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->classificacaoAlunoFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true)
            return ResponseFactory::notFound([], "Classificação de Aluno não encontrada.");
        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/classificacao_aluno/criar",
     *     summary="Cria uma classificacao_aluno",
     *     description="Cria uma classificacao_aluno no banco",
     *     tags={"Classificacao Aluno"},
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
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",       strict=true, nullable=false, allowBlank=true, description="Nome da Classificação de Aluno", requirements="[\w\s-]+")
     * @FOSRest\RequestParam(name="icone",      strict=true, nullable=false, allowBlank=true, description="Ícone da Classificação do Aluno", requirements="[\w\s-]+")
     *
     * @FOSRest\Post("/classificacao_aluno/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros    = $request->all();
        $mensagem      = "";
        $franqueada_id = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $objetoORM     = $this->classificacaoAlunoFacade->criar($mensagem, $franqueada_id, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["classificacao_aluno" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/classificacao_aluno/atualizar/{id}",
     *     summary="Atualiza um classificacao_aluno",
     *     description="Atualiza um classificacao_aluno no banco",
     *     tags={"Classificacao Aluno"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="nome",       strict=true, nullable=false, allowBlank=true, description="Nome da Classificação de Aluno", requirements="[\w\s-]+")
     * @FOSRest\RequestParam(name="icone",      strict=true, nullable=false, allowBlank=true, description="Ícone da Classificação do Aluno", requirements="[\w\s-]+")
     *
     * @FOSRest\Patch("/classificacao_aluno/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->classificacaoAlunoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false)
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/classificacao_aluno/remover/{id}",
     *     summary="Remove uma classificacao_aluno",
     *     description="Remove uma classificacao_aluno no banco",
     *     tags={"Classificacao Aluno"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/classificacao_aluno/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->classificacaoAlunoFacade->remover($mensagem, $id);
        if ($retorno === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
