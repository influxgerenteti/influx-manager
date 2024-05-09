<?php

namespace App\Controller\Principal\PlanoConta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\PlanoContaFacade;

/**
 *
 * @author        Marcelo André Naegeler
 * @Route("/api")
 */
class PlanoContaController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\PlanoContaFacade
     */
    private $planoContaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->planoContaFacade = new PlanoContaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/plano_conta/listar",
     *     summary="Listar plano_conta",
     *     description="Lista as plano_conta do banco",
     *     tags={"Plano de Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os plano_conta"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/plano_conta/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $planosConta = $this->planoContaFacade->listar();
        return ResponseFactory::ok($planosConta);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/plano_conta/{id}",
     *     summary="Buscar a plano_conta",
     *     description="Busca a plano_conta através da ID",
     *     tags={"Plano de Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a plano_conta"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/plano_conta/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->planoContaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/plano_conta/criar",
     *     summary="Cria uma plano_conta",
     *     description="Cria uma plano_conta no banco",
     *     tags={"Plano de Conta"},
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
     * @FOSRest\RequestParam(name="franqueada",          strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="pai",                 strict=false, nullable=true, description="Plano de conta pai", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",           strict=true, nullable=true, description="Descricao do tipo da nota")
     * @FOSRest\RequestParam(name="tipo_movimento_nota", strict=true, nullable=false, allowBlank=false, description="Tipo movimento nota", requirements="\w{0,1}")
     * @FOSRest\RequestParam(name="situacao",            strict=true, nullable=false, allowBlank=false, description="Situacao", default="A", requirements="[A|I|R]")
     *
     * @FOSRest\Post("/plano_conta/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->planoContaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/plano_conta/atualizar/{id}",
     *     summary="Atualiza um plano_conta",
     *     description="Atualiza um plano_conta no banco",
     *     tags={"Plano de Conta"},
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
     * @FOSRest\RequestParam(name="pai",                 strict=false, nullable=true, description="Plano de conta pai", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",           strict=true, nullable=true, description="Descricao do tipo da nota")
     * @FOSRest\RequestParam(name="tipo_movimento_nota", strict=true, nullable=false, allowBlank=false, description="Tipo movimento nota", requirements="\w{0,1}")
     * @FOSRest\RequestParam(name="situacao",            strict=true, nullable=false, allowBlank=false, description="Situacao", default="A", requirements="[A|I|R]")
     *
     * @FOSRest\Patch("/plano_conta/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->planoContaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/plano_conta/remover/{id}",
     *     summary="Remove uma plano_conta",
     *     description="Remove uma plano_conta no banco",
     *     tags={"Plano de Conta"},
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
     * @FOSRest\Delete("/plano_conta/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->planoContaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
