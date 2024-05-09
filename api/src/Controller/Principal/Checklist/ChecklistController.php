<?php

namespace App\Controller\Principal\Checklist;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ChecklistFacade;
use App\Facade\Principal\ChecklistAtividadeFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ChecklistController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ChecklistFacade
     */
    private $checklistFacade;

    /**
     *
     * @var \App\Facade\Principal\ChecklistAtividadeFacade
     */
    private $checklistAtividadeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->checklistFacade          = new ChecklistFacade(self::getManagerRegistry());
        $this->checklistAtividadeFacade = new ChecklistAtividadeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/checklist/listar",
     *     summary="Listar checklist",
     *     description="Lista as checklist do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os checklist"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",     strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="order",      strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",    strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/checklist/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $resultados = $this->checklistFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/checklist/buscar_por_usuario",
     *     summary="Buscar a checklist",
     *     description="Busca a checklist através do usuario logado",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a checklist"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/checklist/buscar_por_usuario")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorUsuarioLogado(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $request->headers->get('Authorization-User-ID');
        $objetoORM = $this->checklistFacade->buscarPorUsuarioLogado($parametros);
        return ResponseFactory::ok($objetoORM);
    }



    /**
     *
     * @SWG\Get(
     *     path="/api/checklist/{id}",
     *     summary="Buscar a checklist",
     *     description="Busca a checklist através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a checklist"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/checklist/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->checklistFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/checklist/criar",
     *     summary="Cria uma checklist",
     *     description="Cria uma checklist no banco",
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
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",  strict=true, nullable=false, allowBlank=false, description="Descrição do Checklist")
     *
     * @FOSRest\RequestParam(name="checklist_atividades", strict=true, nullable=true, allowBlank=true, description="Lista de ChecklistAtividades", map=true, requirements="\d+")
     *
     * @FOSRest\Post("/checklist/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = null;
        // TODO: Não implementado
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/checklist/alterar/{id}",
     *     summary="Atualiza um checklist",
     *     description="Atualiza um checklist no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Patch("/checklist/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/checklist/remover/{id}",
     *     summary="Remove uma checklist",
     *     description="Remove uma checklist no banco",
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
     * @FOSRest\Delete("/checklist/remover/{id}")
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
