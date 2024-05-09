<?php

namespace App\Controller\Principal\FuncionarioDisponibilidade;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FuncionarioDisponibilidadeFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FuncionarioDisponibilidadeController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FuncionarioDisponibilidadeFacade
     */
    private $funcionarioDisponibilidadeFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->funcionarioDisponibilidadeFacade = new FuncionarioDisponibilidadeFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario_disponibilidade/listar",
     *     summary="Listar funcionario_disponibilidade",
     *     description="Lista as funcionario_disponibilidade do banco",
     *     tags={"Funcionario Disponibilidade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os funcionario_disponibilidade"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/funcionario_disponibilidade/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->funcionarioDisponibilidadeFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario_disponibilidade/{id}",
     *     summary="Buscar a funcionario_disponibilidade",
     *     description="Busca a funcionario_disponibilidade através da ID",
     *     tags={"Funcionario Disponibilidade"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a funcionario_disponibilidade"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario_disponibilidade/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar ($id)
    {
        $disponibilidades = $this->funcionarioDisponibilidadeFacade->buscarPorFuncionario($id);
        return ResponseFactory::ok($disponibilidades);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/funcionario_disponibilidade/criar",
     *     summary="Cria uma funcionario_disponibilidade",
     *     description="Cria uma funcionario_disponibilidade no banco",
     *     tags={"Funcionario Disponibilidade"},
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
     * @FOSRest\RequestParam(name="funcionario",  strict=true, nullable=false, allowBlank=false, description="Funcionario ID")
     * @FOSRest\RequestParam(name="dia_semana",   strict=true, nullable=false, allowBlank=false, description="Dia da Semana", requirements="(DOM|SEG|TER|QUA|QUI|SEX|SAB)")
     * @FOSRest\RequestParam(name="hora_inicial", strict=true, nullable=false, allowBlank=false, description="Hora Inicial")
     * @FOSRest\RequestParam(name="hora_final",   strict=true, nullable=false, allowBlank=false, description="Hora Final")
     *
     * @FOSRest\Post("/funcionario_disponibilidade/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->funcionarioDisponibilidadeFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/funcionario_disponibilidade/atualizar/{id}",
     *     summary="Atualiza um funcionario_disponibilidade",
     *     description="Atualiza um funcionario_disponibilidade no banco",
     *     tags={"Funcionario Disponibilidade"},
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
     * @FOSRest\RequestParam(name="dia_semana",   strict=true, nullable=true, allowBlank=true, description="Dia da Semana", requirements="(DOM|SEG|TER|QUA|QUI|SEX|SAB)")
     * @FOSRest\RequestParam(name="hora_inicial", strict=true, nullable=true, allowBlank=true, description="Hora Inicial")
     * @FOSRest\RequestParam(name="hora_final",   strict=true, nullable=true, allowBlank=true, description="Hora Final")
     *
     * @FOSRest\Patch("/funcionario_disponibilidade/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->funcionarioDisponibilidadeFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/funcionario_disponibilidade/atualizar_multiplos",
     *     summary="Atualiza ou cria vários itens de disponibilidade",
     *     description="Atualiza ou cria vários itens de disponibilidade",
     *     tags={"Funcionario Disponibilidade"},
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
     * @FOSRest\RequestParam(name="disponibilidades", strict=true, nullable=false, allowBlank=false, description="Array de disponibilidades", map=true)
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, allowBlank=false, description="Array de disponibilidades", requirements="\d+")
     *
     * @FOSRest\Patch("/funcionario_disponibilidade/atualizar_multiplos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarMultiplos (ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->funcionarioDisponibilidadeFacade->atualizarMultiplos($mensagem, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/funcionario_disponibilidade/remover/{id}",
     *     summary="Remove uma funcionario_disponibilidade",
     *     description="Remove uma funcionario_disponibilidade no banco",
     *     tags={"Funcionario Disponibilidade"},
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
     * @FOSRest\Delete("/funcionario_disponibilidade/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->funcionarioDisponibilidadeFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
