<?php

namespace App\Controller\Principal\Conta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ContaFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ContaController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\ContaFacade
     */
    private $contaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->contaFacade = new ContaFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta/listar",
     *     summary="Listar conta",
     *     description="Lista as conta do banco",
     *     tags={"Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os conta"
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
     * @FOSRest\Get("/conta/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->contaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta/{id}",
     *     summary="Buscar a conta",
     *     description="Busca a conta através da ID",
     *     tags={"Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a conta"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/conta/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->contaFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/conta/criar",
     *     summary="Cria uma conta",
     *     description="Cria uma conta no banco",
     *     tags={"Conta"},
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
     * @FOSRest\RequestParam(name="franqueada",                                strict=true, nullable=true, description="Franqueada a qual a Conta pertence", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                                     strict=true, nullable=true, description="Banco a qual a conta pertence", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_agencia",                            strict=false, nullable=true, description="Numero da Agencia", requirements="[0-9]{0,10}")
     * @FOSRest\RequestParam(name="digito_agencia",                            strict=false, nullable=true, description="Digito da Agencia", requirements="[0-9]{0,3}")
     * @FOSRest\RequestParam(name="descricao",                                 strict=true, nullable=false, allowBlank=true, description="Descricao da conta corrente")
     * @FOSRest\RequestParam(name="conta_corrente",                            strict=false, nullable=true, description="Numero da Conta corrente", requirements="[0-9]{0,15}")
     * @FOSRest\RequestParam(name="digito_conta_corrente",                     strict=false, nullable=true, description="Digito da Conta corrente", requirements="[0-9]{0,2}")
     * @FOSRest\RequestParam(name="observacao",                                strict=false, nullable=true, description="Observacao")
     * @FOSRest\RequestParam(name="valor_saldo",                               strict=false, nullable=true, description="Valor/Saldo", requirements="^\d{0,13}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="considera_fluxo_caixa",                     strict=true, nullable=false, allowBlank=false, description="Considera Fluxo de Caixa", requirements="[0|1]", default="1")
     * @FOSRest\RequestParam(name="empresa_no_banco",                          strict=false, nullable=true, description="Empresa no banco")
     * @FOSRest\RequestParam(name="banco_emite_boleto",                        strict=true, nullable=false, allowBlank=false, description="Banco pode emitir boleto", requirements="[0|1]")
     * @FOSRest\RequestParam(name="primeira_instrucao",                        strict=false, nullable=true, description="Primeira Instrucao")
     * @FOSRest\RequestParam(name="segunda_instrucao",                         strict=false, nullable=true, description="Segunda Instrucao")
     * @FOSRest\RequestParam(name="numero_sequencia_arquivo_cobranca",         strict=false, nullable=true, description="Numero sequencial do arquivo de cobranca", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_dias_protesto",                      strict=false, nullable=true, description="Numeros de dia para protesto", requirements="\d+")
     * @FOSRest\RequestParam(name="taxa_juro_dia",                             strict=false, nullable=true, description="Percentual da Multa", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="taxa_multa",                                strict=false, nullable=true, description="Percentual da Multa", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="observacao_boleto",                         strict=false, nullable=true, description="Observacao do Boleto")
     * @FOSRest\RequestParam(name="variacao_carteira",                         strict=false, nullable=true, description="Variacao de carteira")
     * @FOSRest\RequestParam(name="numero_dias_devolucao",                     strict=false, nullable=true, description="Numero de dias devolucao", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade",                                strict=false, nullable=true, description="Modalidade")
     * @FOSRest\RequestParam(name="numero_dias_floating",                      strict=false, nullable=true, description="Quanto tempo varia para cair na conta(variacao)", requirements="\d+")
     * @FOSRest\RequestParam(name="carteira",                                  strict=false, nullable=true, description="Carteira", requirements="\d+")
     * @FOSRest\RequestParam(name="telefone_contato",                          strict=false, nullable=true, description="Telefone para contato")
     * @FOSRest\RequestParam(name="situacao",                                  strict=true, nullable=false, allowBlank=false, description="Situacao da conta", requirements="[A|I]", default="A")
     * @FOSRest\RequestParam(name="percentual_desconto_antecipado",            strict=false, nullable=true, description="Percentual de Desconto Antecipado", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="numero_dias_desconto_antecipado",           strict=false, nullable=true, description="Número de dias para desconto antecipado", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_dias_max_pagamento_apos_vencimento", strict=false, nullable=true, description="Número de dias máximo para pagamento após vencimento", requirements="\d+")
     * @FOSRest\RequestParam(name="texto_mora_diaria",                         strict=false, nullable=true, description="Texto que antecede a mora diária")
     * @FOSRest\RequestParam(name="texto_multa_atraso",                        strict=false, nullable=true, description="Texto que antecede a multa por atraso")
     *
     * @FOSRest\Post("/conta/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->contaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/conta/atualizar/{id}",
     *     summary="Atualiza um conta",
     *     description="Atualiza um conta no banco",
     *     tags={"Conta"},
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
     * @FOSRest\RequestParam(name="franqueada",                                strict=true, nullable=true, description="Franqueada a qual a Conta pertence", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                                     strict=true, nullable=true, description="Banco a qual a conta pertence", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_agencia",                            strict=false, nullable=true, description="Numero da Agencia", requirements="[0-9]{0,10}")
     * @FOSRest\RequestParam(name="digito_agencia",                            strict=false, nullable=true, description="Digito da Agencia", requirements="[0-9]{0,3}")
     * @FOSRest\RequestParam(name="descricao",                                 strict=true, nullable=false, allowBlank=true, description="Descricao da conta corrente")
     * @FOSRest\RequestParam(name="conta_corrente",                            strict=false, nullable=true, description="Numero da Conta corrente", requirements="[0-9]{0,15}")
     * @FOSRest\RequestParam(name="digito_conta_corrente",                     strict=false, nullable=true, description="Digito da Conta corrente", requirements="[0-9]{0,2}")
     * @FOSRest\RequestParam(name="observacao",                                strict=false, nullable=true, description="Observacao")
     * @FOSRest\RequestParam(name="considera_fluxo_caixa",                     strict=true, nullable=false, allowBlank=false, description="Considera Fluxo de Caixa", requirements="[0|1]", default="1")
     * @FOSRest\RequestParam(name="empresa_no_banco",                          strict=false, nullable=true, description="Empresa no banco")
     * @FOSRest\RequestParam(name="banco_emite_boleto",                        strict=true, nullable=false, allowBlank=false, description="Banco pode emitir boleto", requirements="[0|1]")
     * @FOSRest\RequestParam(name="primeira_instrucao",                        strict=false, nullable=true, description="Primeira Instrucao")
     * @FOSRest\RequestParam(name="segunda_instrucao",                         strict=false, nullable=true, description="Segunda Instrucao")
     * @FOSRest\RequestParam(name="numero_sequencia_arquivo_cobranca",         strict=false, nullable=true, description="Numero sequencial do arquivo de cobranca", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_dias_protesto",                      strict=false, nullable=true, description="Numeros de dia para protesto", requirements="\d+")
     * @FOSRest\RequestParam(name="taxa_juro_dia",                             strict=false, nullable=true, description="Percentual da Multa", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="taxa_multa",                                strict=false, nullable=true, description="Percentual da Multa", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="observacao_boleto",                         strict=false, nullable=true, description="Observacao do Boleto")
     * @FOSRest\RequestParam(name="variacao_carteira",                         strict=false, nullable=true, description="Variacao de carteira")
     * @FOSRest\RequestParam(name="numero_dias_devolucao",                     strict=false, nullable=true, description="Numero de dias devolucao", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade",                                strict=false, nullable=true, description="Modalidade")
     * @FOSRest\RequestParam(name="numero_dias_floating",                      strict=false, nullable=true, description="Quanto tempo varia para cair na conta(variacao)", requirements="\d+")
     * @FOSRest\RequestParam(name="carteira",                                  strict=false, nullable=true, description="Carteira", requirements="\d+")
     * @FOSRest\RequestParam(name="telefone_contato",                          strict=false, nullable=true, description="Telefone para contato")
     * @FOSRest\RequestParam(name="situacao",                                  strict=true, nullable=false, allowBlank=false, description="Situacao da conta", requirements="[A|I]")
     * @FOSRest\RequestParam(name="percentual_desconto_antecipado",            strict=false, nullable=true, description="Percentual de Desconto Antecipado", requirements="^\d{0,3}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="numero_dias_desconto_antecipado",           strict=false, nullable=true, description="Número de dias para desconto antecipado", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_dias_max_pagamento_apos_vencimento", strict=false, nullable=true, description="Número de dias máximo para pagamento após vencimento", requirements="\d+")
     * @FOSRest\RequestParam(name="texto_mora_diaria",                         strict=false, nullable=true, description="Texto que antecede a mora diária")
     * @FOSRest\RequestParam(name="texto_multa_atraso",                        strict=false, nullable=true, description="Texto que antecede a multa por atraso")
     *
     * @FOSRest\Patch("/conta/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->contaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/conta/remover/{id}",
     *     summary="Remove uma conta",
     *     description="Remove uma conta no banco",
     *     tags={"Conta"},
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
     * @FOSRest\Delete("/conta/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->contaFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

     /**
     *
     * @SWG\Post(
     *     path="/api/conta/atualiza_saldos/{id}",
     *     summary="Atualizar Saldos",
     *     description="Atualizar Saldos",
     *     tags={"Conta"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna atualizado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Post("/conta/atualiza_saldos/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualiza_saldos($id)
    {
        $mensagem = "";
        $retorno  = $this->contaFacade->atualiza_saldos($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Saldos atualizados com sucesso");
    }


}
