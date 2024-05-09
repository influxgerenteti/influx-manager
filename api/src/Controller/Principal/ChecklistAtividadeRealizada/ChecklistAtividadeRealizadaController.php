<?php

namespace App\Controller\Principal\ChecklistAtividadeRealizada;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ChecklistAtividadeRealizadaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ChecklistAtividadeRealizadaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ChecklistAtividadeRealizadaFacade
     */
    private $checklistAtividadeRealizadaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->checklistAtividadeRealizadaFacade = new ChecklistAtividadeRealizadaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/checklist_atividade_realizada/listar",
     *     summary="Listar checklist_atividade_realizada",
     *     description="Lista as checklist_atividade_realizada do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os checklist_atividade_realizada"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/checklist_atividade_realizada/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = "";
        // TODO::seu face;
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/checklist_atividade_realizada/buscar_por_usuario",
     *     summary="Listar checklist_atividade_realizada",
     *     description="Lista as checklist_atividade_realizada do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os checklist_atividade_realizada"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="usuario",    strict=true, nullable=false, description="ID do usuario", requirements="\d+")
     *
     * @FOSRest\QueryParam(name="atividades_diarias",    strict=true, nullable=true, allowBlank=true, description="Checklists associados", map=true)
     * @FOSRest\QueryParam(name="atividades_semanais",   strict=true, nullable=true, allowBlank=true, description="Checklists associados", map=true)
     * @FOSRest\QueryParam(name="atividades_mensais",    strict=true, nullable=true, allowBlank=true, description="Checklists associados", map=true)
     * @FOSRest\QueryParam(name="atividades_atemporais", strict=true, nullable=true, allowBlank=true, description="Checklists associados", map=true)
     *
     * @FOSRest\Get("/checklist_atividade_realizada/buscar_por_usuario")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorUsuario(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->checklistAtividadeRealizadaFacade->buscaAtividadesRealizadasPorUsuario($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/checklist_atividade_realizada/{id}",
     *     summary="Buscar a checklist_atividade_realizada",
     *     description="Busca a checklist_atividade_realizada através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a checklist_atividade_realizada"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/checklist_atividade_realizada/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        // TODO: seu objeto ORM
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/checklist_atividade_realizada/criar",
     *     summary="Cria uma checklist_atividade_realizada",
     *     description="Cria uma checklist_atividade_realizada no banco",
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
     * @FOSRest\RequestParam(name="franqueada",          strict=true, nullable=false, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\RequestParam(name="checklist_atividade", strict=true, nullable=false, allowBlank=false, description="Checklist Atividade ID", requirements="\d+")
     * @FOSRest\RequestParam(name="checklist",           strict=true, nullable=false, allowBlank=false, description="Checklist ID", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",             strict=true, nullable=false, allowBlank=false, description="Usuario ID", requirements="\d+")
     *
     * @FOSRest\Post("/checklist_atividade_realizada/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->checklistAtividadeRealizadaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/checklist_atividade_realizada/alterar/{id}",
     *     summary="Atualiza um checklist_atividade_realizada",
     *     description="Atualiza um checklist_atividade_realizada no banco",
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
     * @FOSRest\Patch("/checklist_atividade_realizada/alterar/{id}")
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
     *     path="/api/checklist_atividade_realizada/remover/{id}",
     *     summary="Remove uma checklist_atividade_realizada",
     *     description="Remove uma checklist_atividade_realizada no banco",
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
     * @FOSRest\Delete("/checklist_atividade_realizada/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->checklistAtividadeRealizadaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
