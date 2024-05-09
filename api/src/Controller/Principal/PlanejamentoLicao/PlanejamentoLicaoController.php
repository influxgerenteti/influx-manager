<?php

namespace App\Controller\Principal\PlanejamentoLicao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\PlanejamentoLicaoFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\LicaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class PlanejamentoLicaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\PlanejamentoLicaoFacade
     */
    private $planejamentoLicoesFacade;

    /**
     *
     * @var \App\Facade\Principal\LicaoFacade
     */
    private $licaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->planejamentoLicoesFacade = new PlanejamentoLicaoFacade(self::getManagerRegistry());
        $this->licaoFacade = new LicaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/planejamento_licao/listar",
     *     summary="Listar planejamento_licao",
     *     description="Lista as planejamento_licao do banco",
     *     tags={"Planejamento Licao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os planejamento_licao"
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
     * @FOSRest\Get("/planejamento_licao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->planejamentoLicoesFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/planejamento_licao/{id}",
     *     summary="Buscar a planejamento_licao",
     *     description="Busca a planejamento_licao através da ID",
     *     tags={"Planejamento Licao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a planejamento_licao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/planejamento_licao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->planejamentoLicoesFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/planejamento_licao/criar",
     *     summary="Cria uma planejamento_licao",
     *     description="Cria uma planejamento_licao no banco",
     *     tags={"Planejamento Licao"},
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
     * @FOSRest\RequestParam(name="descricao", strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="licaos",    strict=true, nullable=true, allowBlank=true, description="Array de Licao", map=true)
     *
     * @FOSRest\Post("/planejamento_licao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        if ((isset($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_LICAOS]) < 1)) {
            return ResponseFactory::conflict(["parametros" => $parametros], "O array de lições não pode vir vazio!\n");
        }

        $objetoORM = $this->planejamentoLicoesFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_LICAOS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_LICAOS] as $arrayLicaoParams) {
                $licaoORM = $this->licaoFacade->criarComObjetoPlanejamento($mensagem, $objetoORM, $arrayLicaoParams);
                if ((is_null($licaoORM) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros_licao" => $arrayLicaoParams], $mensagem);
                }
            }
        }

        self::getEntityManager()->flush();

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/planejamento_licao/atualizar/{id}",
     *     summary="Atualiza um planejamento_licao",
     *     description="Atualiza um planejamento_licao no banco",
     *     tags={"Planejamento Licao"},
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
     * @FOSRest\RequestParam(name="descricao", strict=false, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="situacao",  strict=false, nullable=false, allowBlank=false, description="situacao", requirements="[A|I]")
     * @FOSRest\RequestParam(name="licaos",    strict=true, nullable=false, allowBlank=false, description="Array de Licao", map=true)
     *
     * @FOSRest\Patch("/planejamento_licao/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoPlanejamentoLicaoORM = null;
        if ((isset($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_LICAOS]) < 1)) {
            return ResponseFactory::conflict(["parametros" => $parametros], "O array de lições não pode vir vazio!\n");
        }

        $retorno = $this->planejamentoLicoesFacade->atualizar($mensagem, $id, $parametros, $objetoPlanejamentoLicaoORM);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LICAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_LICAOS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_LICAOS] as $arrayLicaoParams) {
                if ((isset($arrayLicaoParams[ConstanteParametros::CHAVE_ID]) === false)||((isset($arrayLicaoParams[ConstanteParametros::CHAVE_ID]) === true)&&(empty($arrayLicaoParams[ConstanteParametros::CHAVE_ID]) === true))) {
                    return ResponseFactory::badRequest(["parametros_licao" => $arrayLicaoParams], "Não foi encontrado a id da lição para ser alterado");
                }

                $retorno = $this->licaoFacade->atualizar($mensagem, $arrayLicaoParams[ConstanteParametros::CHAVE_ID], $objetoPlanejamentoLicaoORM, $arrayLicaoParams);
                if ($retorno === false) {
                    return ResponseFactory::badRequest(["parametros_licao" => $arrayLicaoParams], $mensagem);
                }
            }
        }

        self::getEntityManager()->flush();
        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/planejamento_licao/remover/{id}",
     *     summary="Remove uma planejamento_licao",
     *     description="Remove uma planejamento_licao no banco",
     *     tags={"Planejamento Licao"},
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
     * @FOSRest\Delete("/planejamento_licao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->planejamentoLicoesFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
