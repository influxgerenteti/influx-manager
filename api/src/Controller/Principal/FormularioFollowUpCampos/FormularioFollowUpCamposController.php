<?php

namespace App\Controller\Principal\FormularioFollowUpCampos;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FormularioFollowUpCamposFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FormularioFollowUpCamposController extends GenericController
{

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
        $this->formularioFollowUpCamposFacade = new FormularioFollowUpCamposFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/formulario_follow_up_campos/buscarPorFormulario/{formularioId}",
     *     summary="Listar formulario_follow_up_campos",
     *     description="Lista as formulario_follow_up_campos do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os formulario_follow_up_campos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/formulario_follow_up_campos/buscarPorFormulario/{formularioId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorFormularioId(ParamFetcher $request, $formularioId)
    {
        $mensagem   = "Formulario não encontrado.";
        $resultados = $this->formularioFollowUpCamposFacade->buscarPorFormularioId($formularioId);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/formulario_follow_up_campos/{id}",
     *     summary="Buscar a formulario_follow_up_campos",
     *     description="Busca a formulario_follow_up_campos através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a formulario_follow_up_campos"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/formulario_follow_up_campos/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->formularioFollowUpCamposFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/formulario_follow_up_campos/criar",
     *     summary="Cria uma formulario_follow_up_campos",
     *     description="Cria uma formulario_follow_up_campos no banco",
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
     * @FOSRest\RequestParam(name="formulario_follow_up",  strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     * @FOSRest\RequestParam(name="nome_campo",            strict=true, nullable=false, allowBlank=false, description="Nome do campo")
     * @FOSRest\RequestParam(name="texto_longo",           strict=true, nullable=false, description="Se resposta da coluna for longa", default="0", requirements="(0|1)")
     * @FOSRest\Post("/formulario_follow_up_campos/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $objetoORM = $this->formularioFollowUpCamposFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros_formulario_campos" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/formulario_follow_up_campos/atualizar/{id}",
     *     summary="Atualiza um formulario_follow_up_campos",
     *     description="Atualiza um formulario_follow_up_campos no banco",
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
     * @FOSRest\Patch("/formulario_follow_up_campos/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        if ((empty($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS]) === false)&&(count($parametros[ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS]) > 0)) {
            $retorno = $this->formularioFollowUpCamposFacade->atualizar($mensagem, $id, $parametros);
            if ($retorno === false) {
                return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
            }

            self::getEntityManager()->flush();

            return ResponseFactory::noContent([]);
        } else {
            return ResponseFactory::badRequest([], "");
        }
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/formulario_follow_up_campos/remover/{id}",
     *     summary="Remove uma formulario_follow_up_campos",
     *     description="Remove uma formulario_follow_up_campos no banco",
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
     * @FOSRest\Delete("/formulario_follow_up_campos/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->formularioFollowUpCamposFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
