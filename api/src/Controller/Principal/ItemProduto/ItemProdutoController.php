<?php

namespace App\Controller\Principal\ItemProduto;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ItemProdutoFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\TipoItemFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ItemProdutoController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\TipoItemFacade
     */
    private $tipoItemFacade;

    /**
     *
     * @var \App\Facade\Principal\ItemProdutoFacade
     */
    private $itemProdutoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->itemProdutoFacade = new ItemProdutoFacade(self::getManagerRegistry());
        $this->tipoItemFacade    = new TipoItemFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/item_produto/listar",
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
     * @FOSRest\QueryParam(name="pagina",                 strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",             strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="filtro_franqueada",      strict=true, nullable=true, allowBlank=true, description="filtro Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="saldo_estoque_inicial",  strict=false, nullable=true, description="Saldo Estoque Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="saldo_estoque_final",    strict=false, nullable=true, description="Saldo Estoque Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_compra_inicial",   strict=false, nullable=true, description="Valor Compra Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_compra_final",     strict=false, nullable=true, description="Valor Compra Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="estoque_minimo_inicial", strict=false, nullable=true, description="Estoque Minimo Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="estoque_minimo_final",   strict=false, nullable=true, description="Estoque Minimo Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_venda_inicial",    strict=false, nullable=true, description="Valor Venda Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_venda_final",      strict=false, nullable=true, description="Valor Venda Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="situacao",               strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="descricao",              strict=true, nullable=true, allowBlank=true, description="Descricao do Item", requirements=".{0,80}")
     * @FOSRest\QueryParam(name="unidade_medida",         strict=true, nullable=true, allowBlank=true, description="Unidade de medida", requirements=".{0,3}")
     * @FOSRest\QueryParam(name="order",                  strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/item_produto/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = SituacoesSistema::TIPO_ITEM_PRODUTO;
        $mensagem   = "";
        $resultados = $this->itemProdutoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/item_produto/{id}",
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
     * @FOSRest\Get("/item_produto/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->itemProdutoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/item_produto/buscar_descricao/{query}",
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
     * @FOSRest\Get("/item_produto/buscar_descricao/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorDescricao ($query, ParamFetcher $paramFetcher)
    {
        $franqueada = $paramFetcher->get('franqueada');
        $itens      = $this->itemProdutoFacade->buscarPorDescricaoProduto($query, $franqueada);

        return ResponseFactory::ok($itens);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/item_produto/criar",
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
     * @FOSRest\RequestParam(name="descricao",   strict=true, nullable=false, allowBlank=true, description="Descricao do Item", requirements=".{0,80}")
     * @FOSRest\RequestParam(name="narrativa",   strict=false, nullable=true, allowBlank=true, description="Descricao detalhada do item")
     * @FOSRest\RequestParam(name="valor_venda", strict=false, nullable=true, description="Valor de venda 1", requirements="^\d{0,9}+\.?\d{0,6}?$")
     * @FOSRest\RequestParam(name="situacao",    strict=true, nullable=false, allowBlank=false, description="Situacao do Registro", requirements="[A|I]", default="A")
     *
     * @FOSRest\Post("/item_produto/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_TIPO_ITEM] = $this->tipoItemFacade->buscarPorTipo($mensagem, SituacoesSistema::TIPO_ITEM_PRODUTO);

        $objetoORM = $this->itemProdutoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/item_produto/atualizar/{id}",
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
     * @FOSRest\RequestParam(name="narrativa",   strict=false, nullable=true, allowBlank=true, description="Descricao detalhada do item")
     * @FOSRest\RequestParam(name="valor_venda", strict=false, nullable=true, description="Valor de venda 1", requirements="^\d{0,9}+\.?\d{0,6}?$")
     * @FOSRest\RequestParam(name="situacao",    strict=true, nullable=false, allowBlank=false, description="Situacao do Registro", requirements="[A|I]", default="A")
     *
     * @FOSRest\Patch("/item_produto/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->itemProdutoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/item_produto/remover/{id}",
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
     * @FOSRest\Delete("/item_produto/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->itemProdutoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
