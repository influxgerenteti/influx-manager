<?php

namespace App\Controller\Principal\FormularioFollowUp;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FormularioFollowUpFacade;
use App\Facade\Principal\FormularioFollowUpCamposFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FormularioFollowUpController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FormularioFollowUpFacade
     */
    private $formularioFollowUpFacade;

    /**
     *
     * @var \App\Facade\Principal\FormularioFollowUpCamposFacade
     */
    private $formularioFollowUpCamposFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->formularioFollowUpFacade       = new FormularioFollowUpFacade(self::getManagerRegistry());
        $this->formularioFollowUpCamposFacade = new FormularioFollowUpCamposFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/formulario_follow_up/listar",
     *     summary="Listar formulario_follow_up",
     *     description="Lista as formulario_follow_up do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os formulario_follow_up"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",            strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="follow_up_inicial", strict=false, nullable=true, allowBlank=false, description="Busca só os formularios iniciais", requirements="(0|1)")
     * @FOSRest\QueryParam(name="apenas_primeiro",   strict=false, nullable=true, allowBlank=false, description="Traz apenas o primeiro", requirements="(0|1)")
     * @FOSRest\QueryParam(name="tipo_formulario",   strict=false, nullable=true, allowBlank=false, description="Buscar por tipo de formulário", map=true, requirements="(CA|CR|NP|NI)")
     * @FOSRest\QueryParam(name="situacao",          strict=false, nullable=true, description="Situação", map=true, requirements="(A|I)")
     *
     * @FOSRest\Get("/formulario_follow_up/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->formularioFollowUpFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/formulario_follow_up/{id}",
     *     summary="Buscar a formulario_follow_up",
     *     description="Busca a formulario_follow_up através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a formulario_follow_up"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/formulario_follow_up/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->formularioFollowUpFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/formulario_follow_up/criar",
     *     summary="Cria uma formulario_follow_up",
     *     description="Cria uma formulario_follow_up no banco",
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
     * @FOSRest\RequestParam(name="descricao_formulario", strict=true, nullable=false, allowBlank=false, description="Nome do formulario")
     * @FOSRest\RequestParam(name="tipo_formulario",      strict=true, nullable=false, description="Tipo Formulario")
     * @FOSRest\RequestParam(name="follow_up_inicial",    strict=false, nullable=true, description="Se formulario inicial")
     * @FOSRest\RequestParam(name="situacao",             strict=true, nullable=false, description="Se o registro esta ativo", default="A", requirements="(A|I)")
     *
     * @FOSRest\RequestParam(name="formulario_follow_up_campos", strict=true, nullable=true, allowBlank=true, description="Array de dados dos campos", map=true)
     *
     * @FOSRest\Post("/formulario_follow_up/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametrosFormularioCampo = [];
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        $objetoORM = $this->formularioFollowUpFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((is_array($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS]) === true) && (count($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS] as $registro) {
                $parametrosFormularioCampo[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP] = $objetoORM->getId();
                $parametrosFormularioCampo[ConstanteParametros::CHAVE_NOME_CAMPO]           = $registro[ConstanteParametros::CHAVE_NOME_CAMPO];
                $parametrosFormularioCampo[ConstanteParametros::CHAVE_TEXTO_LONGO]          = $registro[ConstanteParametros::CHAVE_TEXTO_LONGO];

                $formularioCampo = $this->formularioFollowUpCamposFacade->criar($mensagem, $parametrosFormularioCampo);
                if ((is_null($formularioCampo) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros_form_campo" => $parametrosFormularioCampo], $mensagem);
                }
            }
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/formulario_follow_up/atualizar/{id}",
     *     summary="Atualiza um formulario_follow_up",
     *     description="Atualiza um formulario_follow_up no banco",
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
     * @FOSRest\RequestParam(name="descricao_formulario", strict=false, nullable=true, allowBlank=true, description="Nome do formulario")
     * @FOSRest\RequestParam(name="tipo_formulario",      strict=true, nullable=false, description="Tipo Formulario")
     * @FOSRest\RequestParam(name="follow_up_inicial",    strict=false, nullable=true, description="Se formulario inicial")
     * @FOSRest\RequestParam(name="situacao",             strict=false, nullable=true, allowBlank=true, description="Se o registro esta ativo", default="A", requirements="(A|I)")
     *
     * @FOSRest\Patch("/formulario_follow_up/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function atualizar($id, ParamFetcher $request, Request $requestHeader)
     {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        $retorno = $this->formularioFollowUpFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
     }

    /**
     *
     * @SWG\Delete(
     *     path="/api/formulario_follow_up/remover/{id}",
     *     summary="Remove uma formulario_follow_up",
     *     description="Remove uma formulario_follow_up no banco",
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
     * @FOSRest\Delete("/formulario_follow_up/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->formularioFollowUpFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
