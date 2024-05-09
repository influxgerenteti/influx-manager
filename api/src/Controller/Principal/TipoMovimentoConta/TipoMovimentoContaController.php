<?php

namespace App\Controller\Principal\TipoMovimentoConta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TipoMovimentoContaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class TipoMovimentoContaController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\TipoMovimentoContaFacade
     */
    private $tipoMovimentoContaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->tipoMovimentoContaFacade = new TipoMovimentoContaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_movimento_conta/listar",
     *     summary="Listar tipo_movimento_conta",
     *     description="Lista as tipo_movimento_conta do banco",
     *     tags={"Tipo Movimento Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os tipo_movimento_conta"
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
     * @FOSRest\Get("/tipo_movimento_conta/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->tipoMovimentoContaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/tipo_movimento_conta/{id}",
     *     summary="Buscar a tipo_movimento_conta",
     *     description="Busca a tipo_movimento_conta através da ID",
     *     tags={"Tipo Movimento Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a tipo_movimento_conta"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/tipo_movimento_conta/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->tipoMovimentoContaFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/tipo_movimento_conta/criar",
     *     summary="Cria uma tipo_movimento_conta",
     *     description="Cria uma tipo_movimento_conta no banco",
     *     tags={"Tipo Movimento Conta"},
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
     * @FOSRest\RequestParam(name="descricao",     strict=true, allowBlank=true, description="Descricao do tipo Movimento conta")
     * @FOSRest\RequestParam(name="tipo_operacao", strict=true, allowBlank=true, description="Tipo de operacao do registro", requirements="[A-Z]{1,2}")
     * @FOSRest\RequestParam(name="reservado",     strict=false, nullable=true, description="Reservado", default="0", requirements="[0|1]")
     *
     * @FOSRest\Post("/tipo_movimento_conta/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->tipoMovimentoContaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/tipo_movimento_conta/atualizar/{id}",
     *     summary="Atualiza um tipo_movimento_conta",
     *     description="Atualiza um tipo_movimento_conta no banco",
     *     tags={"Tipo Movimento Conta"},
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
     * @FOSRest\RequestParam(name="descricao",     strict=true, allowBlank=true, description="Descricao do tipo Movimento conta")
     * @FOSRest\RequestParam(name="tipo_operacao", strict=true, allowBlank=true, description="Tipo de operacao do registro", requirements="[A-Z]{1,2}")
     * @FOSRest\RequestParam(name="reservado",     strict=false, nullable=true, description="Reservado", requirements="[0|1]")
     * @FOSRest\RequestParam(name="situacao",      strict=false, nullable=false, allowBlank=false, description="Situacao da conta", requirements="[A|I]")
     *
     * @FOSRest\Patch("/tipo_movimento_conta/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->tipoMovimentoContaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/tipo_movimento_conta/remover/{id}",
     *     summary="Remove uma tipo_movimento_conta",
     *     description="Remove uma tipo_movimento_conta no banco",
     *     tags={"Tipo Movimento Conta"},
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
     * @FOSRest\Delete("/tipo_movimento_conta/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->tipoMovimentoContaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
