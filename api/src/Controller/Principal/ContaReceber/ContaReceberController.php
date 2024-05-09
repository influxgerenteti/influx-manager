<?php

namespace App\Controller\Principal\ContaReceber;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\MovimentoEstoqueFacade;
use App\Facade\Principal\ContaReceberFacade;
use App\Facade\Principal\TituloReceberFacade;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\BoletoFacade;
use App\Facade\Principal\TransacaoCartaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ContaReceberController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoEstoqueFacade
     */
    private $movimentoEstoqueFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     *
     * @var \App\Facade\Principal\BoletoFacade
     */
    private $boletoFacade;

    /**
     *
     * @var \App\Facade\Principal\TransacaoCartaoFacade
     */
    private $transacaoCartaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->contaReceberFacade     = new ContaReceberFacade(self::getManagerRegistry());
        $this->movimentoEstoqueFacade = new MovimentoEstoqueFacade(self::getManagerRegistry());
        $this->tituloReceberFacade    = new TituloReceberFacade(self::getManagerRegistry());
        $this->chequeFacade           = new ChequeFacade(self::getManagerRegistry());
        $this->boletoFacade           = new BoletoFacade(self::getManagerRegistry());
        $this->transacaoCartaoFacade  = new TransacaoCartaoFacade(self::getManagerRegistry());
    }

   /**
     *
     * @FOSRest\QueryParam(name="franqueada",              strict=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",                  strict=false, nullable=true, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="mes",                     strict=false, nullable=true, description="Mes", requirements=".+")
     * @FOSRest\QueryParam(name="ano",                     strict=false, nullable=true, description="Ano", requirements=".+")
     * @FOSRest\QueryParam(name="busca",                   strict=false, nullable=true, description="Busca", requirements=".+")
     * @FOSRest\QueryParam(name="situacao",                strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="formato_impressao",       strict=false, nullable=true, description="Formato Impressao")
     * @FOSRest\QueryParam(name="forma_cobranca",          strict=false, nullable=true, description="Forma de cobrança", requirements="\d+")   
     * @FOSRest\QueryParam(name="order",                   strict=false, nullable=true, description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                 strict=false, nullable=true, description="ASC|DESC")
     * @FOSRest\QueryParam(name="tipo_data",               strict=false, nullable=true, description="Tipo data VENCIMENTO MOVIMENTO", requirements=".+")
     * @FOSRest\QueryParam(name="data_inicio",             strict=false, allowBlank=true, description="Data Inicial", requirements=".+")
     * @FOSRest\QueryParam(name="data_fim",                strict=false, allowBlank=true, description="Data Final", requirements=".+")
     *
     * @FOSRest\Get("/conta_receber/consulta")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function consulta(ParamFetcher $request)
    {
        $parametros = $request->all();

        $mensagem   = "";
        $parametros['situacao_aluno'] = ['ATI','INA','TRA'];

        if (isset($parametros['tipo_data'])  === false) {
            $parametros['tipo_data'] = 'VENCIMENTO';
        }

        
            

        $resultados = $this->contaReceberFacade->consulta($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        if($parametros['formato_impressao'] === 'PDF') {
                     
            $html = $this->renderView('relatorios/contas_receber/report.html', ["data" => $resultados]);
            echo $html;
            // /home/gilberto/FONTES/influx-manager/api/templates/relatorios/contas_receber/report.html
            
        } else {
            return ResponseFactory::ok($resultados);
        }
        die;
    }

     /**
     *
     * @FOSRest\QueryParam(name="franqueada",              strict=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",                  strict=false, nullable=true, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="titulo_id",               strict=false, nullable=true, description="titulo id", requirements="\d+")
     *
     * @FOSRest\Get("/conta_receber/detalhes")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detalhes(ParamFetcher $request)
    {
        $parametros = $request->all();

        $mensagem   = "";
       
        $titulo_id = $parametros["titulo_id"];

        $resultados = $this->contaReceberFacade->detalhes($titulo_id);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        
        return ResponseFactory::ok($resultados);
       
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta_receber/listar",
     *     summary="Listar conta_receber",
     *     description="Lista as conta_receber do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os conta_receber"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",              strict=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",                  strict=false, nullable=true, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="sacado_pessoa",           strict=false, nullable=true, description="Sacado", requirements="\d+")
     * @FOSRest\QueryParam(name="contrato",                strict=false, nullable=true, description="Contrato", requirements=".*")
     * @FOSRest\QueryParam(name="nosso_numero",            strict=false, nullable=true, description="Nosso número", requirements="\d+")
     * @FOSRest\QueryParam(name="conta",                   strict=false, nullable=true, description="Conta", requirements=".*")
     * @FOSRest\QueryParam(name="agencia",                 strict=false, nullable=true, description="Agência", requirements=".*")
     * @FOSRest\QueryParam(name="turma",                   strict=false, nullable=true, description="Turma", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_cobranca",          strict=false, nullable=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\QueryParam(name="item",                    strict=false, nullable=true, description="Item", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_inicial",           strict=false, nullable=true, allowBlank=true, description="Valor mínimo do saldo devedor", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_final",             strict=false, nullable=true, allowBlank=true, description="Valor máximo do saldo devedor", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="mes",                     strict=false, nullable=true, description="Mes", requirements=".+")
     * @FOSRest\QueryParam(name="ano",                     strict=false, nullable=true, description="Ano", requirements="\d{0,4}")
     * @FOSRest\QueryParam(name="data_inicial_vencimento", strict=false, nullable=true, description="Data vencimento", requirements=".+")
     * @FOSRest\QueryParam(name="data_final_vencimento",   strict=false, nullable=true, description="Data vencimento", requirements=".+")
     * @FOSRest\QueryParam(name="situacao",                strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                 strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/conta_receber/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->contaReceberFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta_receber/{id}",
     *     summary="Buscar a conta_receber",
     *     description="Busca a conta_receber através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a conta_receber"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/conta_receber/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->contaReceberFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/conta_receber/criar",
     *     summary="Cria uma conta_receber",
     *     description="Cria uma conta_receber no banco",
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
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=true, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",                strict=false, nullable=true, allowBlank=false, description="Aluno", requirements="\d*")
     * @FOSRest\RequestParam(name="sacado_pessoa",        strict=false, nullable=true, allowBlank=false, description="Sacado Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",              strict=false, nullable=true, allowBlank=false, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="vendedor_funcionario", strict=true, nullable=false, allowBlank=false, description="Vendedor Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="contrato",             strict=false, nullable=true, allowBlank=false, description="Contrato", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_total",          strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="observacao",           strict=true, nullable=true, allowBlank=true, description="Observacao")
     *
     * @FOSRest\RequestParam(name="itens",           strict=true, nullable=false, allowBlank=false, description="Items do ContaReceber", map=true)
     * @FOSRest\RequestParam(name="titulos_receber", strict=true, nullable=false, allowBlank=false, description="Titulos Receber", map=true)
     *
     * @FOSRest\Post("/conta_receber/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $boletos    = [];

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === false) || (empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) === false)&&(count($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER]) < 1)) {
            return ResponseFactory::conflict(["parametros" => $parametros], "Para prosseguir com a criação da conta receber, será necessario ao menos 1 titulo informado.");
        }

        $objetoORM = $this->contaReceberFacade->criar($mensagem, $parametros, $boletos);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        self::getEntityManager()->flush();
        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/conta_receber/atualizar_status/{id}",
     *     summary="Atualiza um conta_receber",
     *     description="Atualiza um conta_receber no banco",
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
     * @FOSRest\RequestParam(name="franqueada", strict=true, nullable=false, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",   strict=true, nullable=false, allowBlank=false, description="Situacao", requirements="(PEN|VEN|NEG|QUI)")
     *
     * @FOSRest\Patch("/conta_receber/atualizar_status/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizarStatus($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->contaReceberFacade->atualizarStatus($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
