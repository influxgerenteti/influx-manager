<?php

namespace App\Controller\Principal\AgendaComercial;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AgendaComercialFacade;
use App\Facade\Principal\FollowupComercialFacade;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Augusto Lucas de Souza (GATI labs)
 * @Route("/api")
 */
class AgendaComercialController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\AgendaComercialFacade
     */
    private $agendaComercialFacade;

    /**
     * @var \App\Facade\Principal\FollowupComercialFacade
     */
    private $followupComercialFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->agendaComercialFacade = new AgendaComercialFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agenda_comercial/listar",
     *     summary="Listar agenda_comercial",
     *     description="Lista as agenda_comercial do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os agenda_comercial"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data",          strict=false, allowBlank=true, nullable=true, description="Data")
     * @FOSRest\QueryParam(name="nome",          strict=false, allowBlank=true, nullable=true, description="Nome")
     * @FOSRest\QueryParam(name="telefone",      strict=false, allowBlank=true, nullable=true, description="Telefone")
     * @FOSRest\QueryParam(name="tipo_workflow", strict=false, allowBlank=true, nullable=true, description="Filtrar por workflows", map=true)
     *
     * @FOSRest\Get("/agenda_comercial/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->agendaComercialFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/agenda_comercial/{id}",
     *     summary="Buscar a agenda_comercial",
     *     description="Busca a agenda_comercial através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a agenda_comercial"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/agenda_comercial/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->agendaComercialFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok(['item' => $objetoORM]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/agenda_comercial/criar",
     *     summary="Cria uma agenda_comercial",
     *     description="Cria uma agenda_comercial no banco",
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
     * @FOSRest\RequestParam(name="tipo_agendamento", strict=true, nullable=false, allowBlank=false, description="ID do tipo de agendamento", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, allowBlank=false, description="ID do funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",          strict=true, nullable=false, allowBlank=false, description="ID do usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="interessado",      strict=false, nullable=true, allowBlank=false, description="ID do cadastro da tabela Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="data_agendamento", strict=true, nullable=false, allowBlank=false, description="Data do agendamento")
     * @FOSRest\RequestParam(name="situacao_visita",  strict=true, nullable=true, allowBlank=false, description="Situação da visita", requirements="(NC|VE|VE2|VD)")
     * @FOSRest\RequestParam(name="follow_ups",       strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Post("/agenda_comercial/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        $objetoORM  = $this->agendaComercialFacade->criar($mensagem, $parametros);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === false)&&(count($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) > 0)) {
            $interessadoId     = $parametros[ConstanteParametros::CHAVE_INTERESSADO];
            $agendaComercialId = $objetoORM->getId();
            $followUpInicial   = [
                ConstanteParametros::CHAVE_USUARIO          => $usuarioId,
                ConstanteParametros::CHAVE_INTERESSADO      => $interessadoId,
                ConstanteParametros::CHAVE_AGENDA_COMERCIAL => $agendaComercialId,
            ];

            $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpInicial, true);
            if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }

            foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $followUpData) {
                $followUpData[ConstanteParametros::CHAVE_INTERESSADO]      = $interessadoId;
                $followUpData[ConstanteParametros::CHAVE_USUARIO]          = $usuarioId;
                $followUpData[ConstanteParametros::CHAVE_AGENDA_COMERCIAL] = $agendaComercialId;

                $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpData);
                if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }
        }//end if

        return ResponseFactory::created(["aluno" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/agenda_comercial/atualizar/{id}",
     *     summary="Atualiza um agenda_comercial",
     *     description="Atualiza um agenda_comercial no banco",
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
     * @FOSRest\RequestParam(name="tipo_agendamento", strict=true, nullable=false, allowBlank=false, description="ID do tipo de agendamento", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, allowBlank=false, description="ID do funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",          strict=true, nullable=false, allowBlank=false, description="ID do usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="interessado",      strict=false, nullable=true, allowBlank=false, description="ID do cadastro da tabela Interessado", requirements="\d+")
     * @FOSRest\RequestParam(name="data_agendamento", strict=true, nullable=false, allowBlank=false, description="Data do agendamento")
     * @FOSRest\RequestParam(name="situacao_visita",  strict=true, nullable=true, allowBlank=false, description="Situação da visita", requirements="(NC|VE|VE2|VD)")
     *
     * @FOSRest\RequestParam(name="follow_ups", strict=true, nullable=true, allowBlank=true, description="Lista de followups a serem atrelados", map=true)
     *
     * @FOSRest\Patch("/agenda_comercial/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $usuarioId  = $request->headers->get('Authorization-User-ID');
        $retorno    = $this->agendaComercialFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) === false)&&(count($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS]) > 0)) {
            $dadosAgendaComercial = $this->agendaComercialFacade->buscarPorId($mensagem, $id);

            $interessadoId = $parametros[ConstanteParametros::CHAVE_INTERESSADO];

            $bCriaPrimeiroFollowUp = false === ((isset($dadosAgendaComercial[ConstanteParametros::CHAVE_FOLLOWUPCOMERCIALS_PROPRIEDADE]) === true) && (count($dadosAgendaComercial[ConstanteParametros::CHAVE_FOLLOWUPCOMERCIALS_PROPRIEDADE]) > 0));
            if ($bCriaPrimeiroFollowUp === true) {
                $followUpInicial = [
                    ConstanteParametros::CHAVE_USUARIO          => $usuarioId,
                    ConstanteParametros::CHAVE_INTERESSADO      => $interessadoId,
                    ConstanteParametros::CHAVE_AGENDA_COMERCIAL => $id,
                ];

                $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpInicial, true);
                if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }

            foreach ($parametros[ConstanteParametros::CHAVE_FOLLOW_UPS] as $followUpData) {
                $followUpData[ConstanteParametros::CHAVE_INTERESSADO]      = $interessadoId;
                $followUpData[ConstanteParametros::CHAVE_USUARIO]          = $usuarioId;
                $followUpData[ConstanteParametros::CHAVE_AGENDA_COMERCIAL] = $id;

                $objetoFollowUp = $this->followupComercialFacade->criar($mensagem, $followUpData);
                if ((is_null($objetoFollowUp) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }
        }//end if

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/agenda_comercial/remover/{id}",
     *     summary="Remove uma agenda_comercial",
     *     description="Remove uma agenda_comercial no banco",
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
     * @FOSRest\Delete("/agenda_comercial/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->agendaComercialFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
