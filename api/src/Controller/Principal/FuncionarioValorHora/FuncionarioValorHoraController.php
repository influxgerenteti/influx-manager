<?php

namespace App\Controller\Principal\FuncionarioValorHora;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FuncionarioValorHoraFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FuncionarioValorHoraController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FuncionarioValorHoraFacade
     */
    private $funcionarioValorHoraFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->funcionarioValorHoraFacade = new FuncionarioValorHoraFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario_valor_hora/listar",
     *     summary="Listar funcionario_valor_hora",
     *     description="Lista as funcionario_valor_hora do banco",
     *     tags={"Funcionario Valor Hora"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os funcionario_valor_hora"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/funcionario_valor_hora/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->funcionarioValorHoraFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario_valor_hora/{id}",
     *     summary="Buscar a funcionario_valor_hora",
     *     description="Busca a funcionario_valor_hora através da ID",
     *     tags={"Funcionario Valor Hora"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a funcionario_valor_hora"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario_valor_hora/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->funcionarioValorHoraFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/funcionario_valor_hora/criar",
     *     summary="Cria uma funcionario_valor_hora",
     *     description="Cria uma funcionario_valor_hora no banco",
     *     tags={"Funcionario Valor Hora"},
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
     * @FOSRest\RequestParam(name="funcionario", strict=true, nullable=false, allowBlank=false, description="Funcionario ID")
     * @FOSRest\RequestParam(name="valor_hora",  strict=true, nullable=false, allowBlank=false, description="Valor Hora ID")
     * @FOSRest\RequestParam(name="valor",       strict=true, nullable=false, allowBlank=false, description="valor", requirements="^\d{0,7}+\.?\d{0,2}?$"))
     *
     * @FOSRest\Post("/funcionario_valor_hora/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->funcionarioValorHoraFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/funcionario_valor_hora/atualizar/{id}",
     *     summary="Atualiza um funcionario_valor_hora",
     *     description="Atualiza um funcionario_valor_hora no banco",
     *     tags={"Funcionario Valor Hora"},
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
     * @FOSRest\RequestParam(name="valor", strict=true, nullable=false, allowBlank=false, description="valor", requirements="^\d{0,7}+\.?\d{0,2}?$"))
     *
     * @FOSRest\Patch("/funcionario_valor_hora/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->funcionarioValorHoraFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/funcionario_valor_hora/remover/{id}",
     *     summary="Remove uma funcionario_valor_hora",
     *     description="Remove uma funcionario_valor_hora no banco",
     *     tags={"Funcionario Valor Hora"},
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
     * @FOSRest\Delete("/funcionario_valor_hora/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->funcionarioValorHoraFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
