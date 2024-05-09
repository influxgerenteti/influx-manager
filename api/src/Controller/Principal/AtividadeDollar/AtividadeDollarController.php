<?php

namespace App\Controller\Principal\AtividadeDollar;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AtividadeDollarFacade;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class AtividadeDollarController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\AtividadeDollarFacade
     */
    private $atividadeDollarFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->atividadeDollarFacade = new AtividadeDollarFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/atividade_dollar/listar",
     *     summary="Listar Atividade Dollar",
     *     description="Lista as Atividades Dollar da base de dados",
     *     tags={"AtividadeDollar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as Atividades Dollar"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/atividade_dollar/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->atividadeDollarFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/atividade_dollar/{id}",
     *     summary="Buscar a Atividade Dollar",
     *     description="Busca a Atividade Dollar através da ID",
     *     tags={"AtividadeDollar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a Atividade Dollar"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/atividade_dollar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = $this->atividadeDollarFacade->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Atividade Dollar não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/atividade_dollar/criar",
     *     summary="Cria uma Atividade Dollar",
     *     description="Cria uma Atividade Dollar na base de dados",
     *     tags={"AtividadeDollar"},
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=true, description="Descricao da Atividade Dollar")
     * @FOSRest\RequestParam(name="situacao",  strict=false, nullable=false, allowBlank=false, description="Situação da Atividade Dollar", requirements="[A|I|R]", default="A")
     *
     * @FOSRest\Post("/atividade_dollar/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->atividadeDollarFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/atividade_dollar/atualizar/{id}",
     *     summary="Atualiza uma Atividade Dollar",
     *     description="Atualiza uma Atividade Dollar na base de dados",
     *     tags={"AtividadeDollar"},
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=true, description="Descricao da Atividade Dollar")
     * @FOSRest\RequestParam(name="situacao",  strict=false, nullable=false, allowBlank=false, description="Situação da Atividade Dollar", requirements="[A|I|R]", default="A")
     *
     * @FOSRest\Patch("/atividade_dollar/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->atividadeDollarFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/atividade_dollar/remover/{id}",
     *     summary="Remove uma Atividade Dollar",
     *     description="Remove uma Atividade Dollar na base de dados",
     *     tags={"AtividadeDollar"},
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
     * @FOSRest\Delete("/atividade_dollar/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->atividadeDollarFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
