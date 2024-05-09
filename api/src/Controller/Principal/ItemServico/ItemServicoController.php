<?php

namespace App\Controller\Principal\ItemServico;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ItemServicoFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\SituacoesSistema;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ItemServicoController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\ItemServicoFacade
     */
    private $itemServicoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->itemServicoFacade = new ItemServicoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/item_servico/listar",
     *     summary="Listar item",
     *     description="Lista as item do banco",
     *     tags={"Item"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os item"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",              strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",          strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_venda_inicial", strict=false, nullable=true, description="Valor Venda Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_venda_final",   strict=false, nullable=true, description="Valor Venda Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="situacao",            strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="descricao",           strict=true, nullable=true, allowBlank=true, description="Descricao do Item", requirements=".{0,80}")
     * @FOSRest\QueryParam(name="order",               strict=false, nullable=true,  description="Coluna de ordenação", default="it.descricao")
     * @FOSRest\QueryParam(name="direcao",             strict=false, nullable=true,  description="ASC|DESC", default="ASC")
     * @FOSRest\QueryParam(name="tipo_item",           strict=false, nullable=true,  allowBlank=true, description="Chave Primaria da tabela TipoItem")
     * @FOSRest\QueryParam(name="filtro_franqueada",   strict=true, nullable=false,  allowBlank=false, description="Chave Primaria da tabela Franqueada", map=true)
     *
     * @FOSRest\Get("/item_servico/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        if ($parametros[ConstanteParametros::CHAVE_TIPO_ITEM] === null) {
            $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = SituacoesSistema::TIPOS_SERVICO;
        } else {
            $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = [$parametros[ConstanteParametros::CHAVE_TIPO_ITEM]];
        }

        $mensagem   = "";
        $resultados = $this->itemServicoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/item_servico/{id}",
     *     summary="Buscar a item",
     *     description="Busca a item através da ID",
     *     tags={"Item"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a item"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/item_servico/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->itemServicoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/item_servico/buscar_descricao/{query}",
     *     summary="Buscar item por nome",
     *     description="Busca item por nome",
     *     tags={"Item"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a item"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, nullable=true, allowBlank=false, description="ID da Franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/item_servico/buscar_descricao/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorDescricao ($query, ParamFetcher $paramFetcher)
    {
        $franqueada = $paramFetcher->get('franqueada');
        $itens      = $this->itemServicoFacade->buscarPorDescricaoServico($query, $franqueada);

        return ResponseFactory::ok($itens);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/item_servico/criar",
     *     summary="Cria uma item",
     *     description="Cria uma item no banco",
     *     tags={"Item"},
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
     * @FOSRest\RequestParam(name="franqueada",  strict=true, nullable=false, allowBlank=false, description="Chave Primaria da tabela Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_item",   strict=true, nullable=false, allowBlank=false, description="Chave Primaria da tabela TipoItem", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",   strict=true, nullable=false, allowBlank=true, description="Descricao do Item", requirements=".{0,80}")
     * @FOSRest\RequestParam(name="narrativa",   strict=false, nullable=true, allowBlank=true, description="Descricao detalhada do item")
     * @FOSRest\RequestParam(name="valor_venda", strict=false, nullable=true, description="Valor de venda 1", requirements="^\d{0,9}+\.?\d{0,6}?$")
     * @FOSRest\RequestParam(name="situacao",    strict=true, nullable=false, allowBlank=false, description="Situacao do Registro", requirements="[A|I]", default="A")
     * @FOSRest\RequestParam(name="plano_conta", strict=true, nullable=false, description="ID da tabela Plano Conta", requirements="\d+")
     *
     * @FOSRest\Post("/item_servico/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->itemServicoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/item_servico/atualizar_valor_venda/{id}",
     *     summary="Atualiza um item",
     *     description="Atualiza um item no banco",
     *     tags={"Item"},
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
     * @FOSRest\RequestParam(name="filtro_franqueada", strict=false, nullable=false, allowBlank=false, description="\d+")
     * @FOSRest\RequestParam(name="valor_venda",       strict=false, nullable=false, allowBlank=false, description="Valor de venda")
     *
     * @FOSRest\Patch("/item_servico/atualizar_valor_venda/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarValorVenda($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->itemServicoFacade->preencheItemFranqueada($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/item_servico/atualizar/{id}",
     *     summary="Atualiza um item",
     *     description="Atualiza um item no banco",
     *     tags={"Item"},
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
     * @FOSRest\RequestParam(name="descricao",   strict=true, nullable=false, allowBlank=true, description="Descricao do Item", requirements=".{0,80}")
     * @FOSRest\RequestParam(name="tipo_item",   strict=true, nullable=false, allowBlank=false, description="Chave Primaria da tabela TipoItem", requirements="\d+")
     * @FOSRest\RequestParam(name="narrativa",   strict=false, nullable=true, allowBlank=true, description="Descricao detalhada do item")
     * @FOSRest\RequestParam(name="situacao",    strict=true, nullable=false, allowBlank=false, description="Situacao do Registro", requirements="[A|I]", default="A")
     * @FOSRest\RequestParam(name="plano_conta", strict=true, nullable=false, description="ID da tabela Plano Conta", requirements="\d+")
     *
     * @FOSRest\Patch("/item_servico/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->itemServicoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/item_servico/remover/{id}",
     *     summary="Remove uma item",
     *     description="Remove uma item no banco",
     *     tags={"Item"},
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
     * @FOSRest\Delete("/item_servico/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->itemServicoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
