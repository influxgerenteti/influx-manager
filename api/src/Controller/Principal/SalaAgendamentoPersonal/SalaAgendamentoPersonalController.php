<?php

namespace App\Controller\Principal\SalaAgendamentoPersonal;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\SalaAgendamentoPersonalFacade;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Gilberto M Martins
 * @Route("/api")
 */
class SalaAgendamentoPersonalController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\SalaAgendamentoPersonalFacade
     */
    private $salaAgendamentoPersonalFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->salaAgendamentoPersonalFacade = new SalaAgendamentoPersonalFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sala_agendamento_personal/listar",
     *     summary="Listar sala",
     *     description="Lista as sala do banco",
     *     tags={"Sala"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os sala"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",            strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="sala_franqueada",   strict=false, allowBlank=true, default="0", description="Se deve ser listados dados para sala da franqueada")
     * @FOSRest\QueryParam(name="franqueada",        strict=true, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="data",              strict=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\QueryParam(name="order",             strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="situacao",          strict=false, nullable=true,  description="Aberta/ Finalizada/ Cancelada / ")
     * @FOSRest\QueryParam(name="direcao",           strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/sala_agendamento_personal/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->salaAgendamentoPersonalFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sala_agendamento_personal/{id}",
     *     summary="Buscar a sala",
     *     description="Busca a sala através da ID",
     *     tags={"Sala"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a sala"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial",             strict=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",               strict=false, allowBlank=false, description="Data Final")
     * 
     * @FOSRest\Get("/sala_agendamento_personal/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem  = "";
        $objetoORM = $this->salaAgendamentoPersonalFacade->buscarPorId($mensagem, $id, $parametros);

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/sala_agendamento_personal/criar",
     *     summary="Cria uma sala",
     *     description="Cria uma sala no banco",
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
     * @FOSRest\RequestParam(name="sala_franqueada",    strict=true, nullable=false, allowBlank=false, description="Sala_franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",         strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="data_inicio",        strict=true, nullable=false, allowBlank=false, description="Data de Inicio" )
     * @FOSRest\RequestParam(name="data_fim",           strict=true, nullable=false, allowBlank=false, description="Data de Inicio")
     * @FOSRest\RequestParam(name="situacao",       strict=true, nullable=false, allowBlank=false, description="Situação", requirements="[A|I]", default="A")
    *
     * @FOSRest\Post("/sala_agendamento_personal/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->salaAgendamentoPersonalFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/sala_agendamento_personal/atualizar/{id}",
     *     summary="Atualiza um sala",
     *     description="Atualiza um sala no banco",
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
     * @FOSRest\RequestParam(name="franqueada",         strict=false, nullable=true, allowBlank=false, description="Franqueada", requirements=".*")
     * @FOSRest\RequestParam(name="dados",              strict=false, nullable=true, allowBlank=false)
     *
     * @FOSRest\Patch("/sala_agendamento_personal/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->get('dados');
        try{
            $disponibilidadesApagar = $this->salaAgendamentoPersonalFacade->obterDisponibilidades($id);

            foreach($disponibilidadesApagar as $registro) {
                $this->salaAgendamentoPersonalFacade->deletar($registro);
            }

            if($parametros && count($parametros) > 0) {
                foreach ($parametros as $key => $disponibilidade) {
                    $disponibilidade['sala_franqueada'] = $id;
                    $this->salaAgendamentoPersonalFacade->criar($disponibilidade);
                }
            }
        } catch(Exception $e) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $e->getMessage());
        }
        return ResponseFactory::created([]);
    }


}
