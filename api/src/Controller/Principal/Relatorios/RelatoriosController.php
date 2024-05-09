<?php

namespace App\Controller\Principal\Relatorios;

use App\Reports\Repository\ChequesAnaliticoPorBancoLinhasRepository;
use App\Reports\Repository\ChequesAnaliticoPorBancoRepository;
use App\Reports\Repository\ChequesAnaliticoRepository;
use App\Reports\Repository\ChequesSinteticoPorBancoLinhasRepository;
use App\Reports\Repository\ChequesSinteticoPorBancoRepository;
use App\Reports\Repository\ChequesSinteticoRepository;
use App\Reports\Repository\ContasPagarAgrupadoCategoriaLinhasRepository;
use App\Reports\Repository\ContasPagarAgrupadoCategoriaRepository;
use App\Reports\Repository\ContasPagarAgrupadoDataPagamentoLinhasRepository;
use App\Reports\Repository\ContasPagarAgrupadoDataPagamentoRepository;
use App\Reports\Repository\ContasPagarAgrupadoDataVencimentoLinhasRepository;
use App\Reports\Repository\ContasPagarAgrupadoDataVencimentoRepository;
use App\Reports\Repository\ContasPagarAgrupadoDestinoLinhasRepository;
use App\Reports\Repository\ContasPagarAgrupadoDestinoRepository;
use App\Reports\Repository\ContasPagarAgrupadoSituacaoLinhasRepository;
use App\Reports\Repository\ContasPagarAgrupadoSituacaoRepository;
use App\Reports\Repository\ContasPagarRepository;
use App\Reports\Repository\InadimplentesAlunosRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\AlunoFacade;
use App\Facade\Principal\AlunoDiarioFacade;
use App\Facade\Principal\GenericItemFacade;
use App\Facade\Principal\TurmaAulaFacade;
use App\Facade\Principal\AtividadeExtraFacade;
use App\Facade\Principal\AlunoAvaliacaoFacade;
use App\Facade\Principal\PessoaFacade;
use App\Facade\Principal\TituloPagarFacade;
use App\Facade\Principal\TurmaFacade;
use App\Facade\Principal\TituloReceberFacade;
use App\Facade\Principal\InteressadoFacade;
use App\Facade\Principal\FuncionarioFacade;
use App\Facade\Principal\HistoricoSituacaoAlunoFacade;
use App\Facade\Principal\AgendamentoPersonalFacade;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\ContratoFacade;
use App\Facade\Principal\MovimentoContaFacade;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Facade\Principal\ChecklistAtividadeFacade;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use App\Facade\Principal\ItemContaReceberFacade;
use App\Helper\FunctionHelper;
use App\Helper\SituacoesSistema;
use App\Repository\Principal\InteressadoRepository;
use Shuchkin\SimpleXLSXGen;
use Jurosh\PDFMerge\PDFMerger;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\ContaReceberFacade;
use App\Facade\Principal\ItemServicoFacade;
use App\Facade\Principal\ConvenioFacade;
use App\Facade\Principal\FollowupComercialFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class RelatoriosController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoFacade
     */
    private $alunoFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoDiarioFacade
     */
    private $alunoDiarioFacade;

    /**
     *
     * @var \App\Facade\Principal\GenericItemFacade
     */
    private $itemFacade;

    /**
     *
     * @var \App\Facade\Principal\AtividadeExtraFacade
     */
    private $atividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAtividadeExtraFacade
     */
    private $alunoAtividadeExtraFacade;

    /**
     *
     * @var \App\Facade\Principal\TurmaAulaFacade
     */
    private $turmaAulaFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoFacade
     */
    private $alunoAvaliacaoFacade;

    /**
     *
     * @var \App\Facade\Principal\PessoaFacade
     */
    private $pessoaFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\TurmaFacade
     */
    private $turmaFacade;

    /**
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\InteressadoFacade
     */
    private $interessadoFacade;

    /**
     *
     * @var \App\Facade\Principal\HistoricoSituacaoAlunoFacade
     */
    private $historicoSituacaoAlunoFacade;

    /**
     *
     * @var \App\Facade\Principal\ContratoFacade
     */
    private $contratoFacade;

    /**
     *
     * @var \App\Facade\Principal\FuncionarioFacade
     */
    private $funcionarioFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Facade\Principal\ChecklistAtividadeFacade
     */
    private $checklistAtividadeFacade;

    /**
     *
     * @var \App\Facade\Principal\ItemContaReceberFacade
     */
    private $itemContaReceberFacade;

    /**
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     * @var \App\Facade\Principal\ConvenioFacade
     */
    private $convenioFacade;

    /**
     * 
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     * 
     * @var \App\Facade\Principal\ItemServicoFacade
     */
    private $itemServicoFacade;

    /**
     * 
     * @var \App\Facade\Principal\AgendamentoPersonalFacade
     */
    private $agendamentoPersonalFacade;

    /**
     * 
     * @var \App\Facade\Principal\FollowupComercialFacade
     */
    private $followupComercialFacade;

       /**
     *
     * @var \App\Repository\Principal\ContaReceberRepository
     */
    private $contaReceberRepository;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->turmaFacade          = new TurmaFacade(self::getManagerRegistry());
        $this->contaReceberRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ContaReceber::class);
        $this->chequeFacade         = new ChequeFacade(self::getManagerRegistry());
        $this->alunoFacade          = new AlunoFacade(self::getManagerRegistry());
        $this->alunoDiarioFacade    = new AlunoDiarioFacade(self::getManagerRegistry());
        $this->itemFacade           = new GenericItemFacade(self::getManagerRegistry());
        $this->atividadeExtraFacade = new AtividadeExtraFacade(self::getManagerRegistry());
        $this->turmaAulaFacade      = new TurmaAulaFacade(self::getManagerRegistry());
        $this->alunoAvaliacaoFacade = new AlunoAvaliacaoFacade(self::getManagerRegistry());
        $this->pessoaFacade         = new PessoaFacade(self::getManagerRegistry());
        $this->tituloPagarFacade    = new TituloPagarFacade(self::getManagerRegistry());
        $this->tituloReceberFacade  = new TituloReceberFacade(self::getManagerRegistry());
        $this->interessadoFacade    = new InteressadoFacade(self::getManagerRegistry());
        $this->historicoSituacaoAlunoFacade = new HistoricoSituacaoAlunoFacade(self::getManagerRegistry());
        $this->contratoFacade           = new ContratoFacade(self::getManagerRegistry());
        $this->funcionarioFacade        = new FuncionarioFacade(self::getManagerRegistry());
        $this->movimentoContaFacade     = new MovimentoContaFacade(self::getManagerRegistry());
        $this->checklistAtividadeFacade = new ChecklistAtividadeFacade(self::getManagerRegistry());
        $this->itemContaReceberFacade   = new ItemContaReceberFacade(self::getManagerRegistry());
        $this->franqueadaRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->ocorrenciaAcademicaFacade = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->contaReceberFacade       = new ContaReceberFacade(self::getManagerRegistry());
        $this->itemServicoFacade       = new ItemServicoFacade(self::getManagerRegistry());
        $this->convenioFacade = new ConvenioFacade(self::getManagerRegistry());
        $this->agendamentoPersonalFacade       = new AgendamentoPersonalFacade(self::getManagerRegistry());
        $this->followupComercialFacade = new FollowupComercialFacade(self::getManagerRegistry());
    }

    private function getInfluxPath()
    {
        return $this->get('kernel')->getProjectDir() . "/public/img/logo/logo_INFLUX-1.png";
    }


    /**
     * Returns a rendered view.
     *
     * @final
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        if ($this->container->has('templating') && $this->container->get('templating')->supports($view)) {
            @trigger_error('Using the "templating" service is deprecated since version 4.3 and will be removed in 5.0; use Twig instead.', \E_USER_DEPRECATED);

            return $this->container->get('templating')->render($view, $parameters);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/listar",
     *     summary="Listar relatorios",
     *     description="Lista as relatorios do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os relatorios"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/relatorios/teste")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teste(ParamFetcher $request)
    {
        $mensagemErro = "Gerado com sucesso";
        $parametros   = [
            'nomeFranqueada' => 'inFlux Champagnat',
            'logoInflux'     => $this->getInfluxPath(),
        ];
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida(['pdf']);
        // self::getJasperHelper()->compilaTodosRelatoriosJRXML();
        self::getJasperHelper()->processaRelatorio("Exemplo.jasper", $mensagemErro);
        var_dump($mensagemErro);
        die;
    }

    /**
     * Verifica se existe valor para o parâmetro
     * se tiver, retorna, senão retorna a string vazia
     */
    private function setaParametro(string $var)
    {
        if ($var === null) {
            $parametro = "";
        } else {
            $parametro = $var;
        }

        return $parametro;
    }

    private function getJasperFilesBaseDir()
    {
        return $this->get('kernel')->getProjectDir() . '/src/Reports/jasper/';
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/cheques",
     *     summary="Imprimir relatórios de cheques",
     *     description="Imprimir o relatório de cheque passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_entrada_inicial",    strict=true, allowBlank=true, nullable=true, description="Data de Entrada")
     * @FOSRest\QueryParam(name="data_entrada_final",      strict=true, allowBlank=true, nullable=true, description="Data de Entrada")
     * @FOSRest\QueryParam(name="data_bom_para_inicial",   strict=true, allowBlank=true, nullable=true, description="Data de Bom Para")
     * @FOSRest\QueryParam(name="data_bom_para_final",     strict=true, allowBlank=true, nullable=true, description="Data de Bom Para")
     * @FOSRest\QueryParam(name="data_baixa_inicial",      strict=true, allowBlank=true, nullable=true, description="Data de baixa")
     * @FOSRest\QueryParam(name="data_baixa_final",        strict=true, allowBlank=true, nullable=true, description="Data de baixa")
     * @FOSRest\QueryParam(name="data_devolucao_inicial",  strict=true, allowBlank=true, nullable=true, description="Data de devolução")
     * @FOSRest\QueryParam(name="data_devolucao_final",    strict=true, allowBlank=true, nullable=true, description="Data de devolução")
     * @FOSRest\QueryParam(name="situacao",                strict=true, allowBlank=true, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="tipo",                    strict=true, allowBlank=true, nullable=true, description="Tipo")
     * @FOSRest\QueryParam(name="conta",                   strict=true, allowBlank=true, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="motivo_devolucao_cheque", strict=true, allowBlank=true, nullable=true, description="Motivo de devolução", requirements="\d+")
     * @FOSRest\QueryParam(name="detalhado",               strict=true, allowBlank=true, nullable=false, default=0, description="Detalhado", requirements="[1|0]")
     * @FOSRest\QueryParam(name="agrupado_banco",          strict=true, allowBlank=true, nullable=false, default=0, description="Agrupado por banco", requirements="[1|0]")
     * @FOSRest\QueryParam(name="excel",                   strict=true, allowBlank=true, nullable=false, default=0, description="Exportar para Excel", requirements="[1|0]")
     *
     * @FOSRest\Get("/relatorios/cheques")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioCheques(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->chequeFacade->gerarDadosRelatorio($filtros);
        return new Response(Json_encode($data));
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/cheques/imprimir",
     *     summary="Imprimir relatórios de cheques",
     *     description="Imprimir o relatório de cheque passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_entrada_inicial",    strict=true, allowBlank=true, nullable=true, description="Data de Entrada")
     * @FOSRest\QueryParam(name="data_entrada_final",      strict=true, allowBlank=true, nullable=true, description="Data de Entrada")
     * @FOSRest\QueryParam(name="data_bom_para_inicial",   strict=true, allowBlank=true, nullable=true, description="Data de Bom Para")
     * @FOSRest\QueryParam(name="data_bom_para_final",     strict=true, allowBlank=true, nullable=true, description="Data de Bom Para")
     * @FOSRest\QueryParam(name="data_baixa_inicial",      strict=true, allowBlank=true, nullable=true, description="Data de baixa")
     * @FOSRest\QueryParam(name="data_baixa_final",        strict=true, allowBlank=true, nullable=true, description="Data de baixa")
     * @FOSRest\QueryParam(name="data_devolucao_inicial",  strict=true, allowBlank=true, nullable=true, description="Data de devolução")
     * @FOSRest\QueryParam(name="data_devolucao_final",    strict=true, allowBlank=true, nullable=true, description="Data de devolução")
     * @FOSRest\QueryParam(name="situacao",                strict=true, allowBlank=true, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="tipo",                    strict=true, allowBlank=true, nullable=true, description="Tipo")
     * @FOSRest\QueryParam(name="conta",                   strict=true, allowBlank=true, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="motivo_devolucao_cheque", strict=true, allowBlank=true, nullable=true, description="Motivo de devolução", requirements="\d+")
     * @FOSRest\QueryParam(name="detalhado",               strict=true, allowBlank=true, nullable=false, default=0, description="Detalhado", requirements="[1|0]")
     * @FOSRest\QueryParam(name="agrupado_banco",          strict=true, allowBlank=true, nullable=false, default=0, description="Agrupado por banco", requirements="[1|0]")
     * @FOSRest\QueryParam(name="excel",                   strict=true, allowBlank=true, nullable=false, default=0, description="Exportar para Excel", requirements="[1|0]")
     *
     * @FOSRest\Get("/relatorios/cheques/imprimir")
     */
    public function relatorioChequesImprimir(
        ParamFetcher $request,
        ChequesAnaliticoPorBancoRepository $chequesAnaliticoPorBancoRepository,
        ChequesAnaliticoPorBancoLinhasRepository $chequesAnaliticoPorBancoLinhasRepository,
        ChequesAnaliticoRepository $chequesAnaliticoRepository,
        ChequesSinteticoPorBancoRepository $chequesSinteticoPorBancoRepository,
        ChequesSinteticoPorBancoLinhasRepository $chequesSinteticoPorBancoLinhasRepository,
        ChequesSinteticoRepository $chequesSinteticoRepository
    ) {
        $filtros     = $request->all();
        $whereClause = $this->chequeFacade->gerarDadosRelatorio($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            return $this->json(['messagem' => 'Franqueada não encontrada'], 422);
        }

        $date    = (new \DateTime())->format('Ymd-Hi');
        $formato = 'pdf';
        if ($filtros[ConstanteParametros::CHAVE_EXCEL] !== '0') {
            $formato = 'xlsx';
        }

        if ($filtros[ConstanteParametros::CHAVE_DETALHADO] !== '0') {
            if ($filtros[ConstanteParametros::CHAVE_AGRUPADO_BANCO] !== '0') {
                $template = 'relatorios/cheques/cheques-analitico-por-banco.html.twig';
                $fileName = 'cheques-analitico-por-banco' . $date . '.xlsx';
                $bancos   = $chequesAnaliticoPorBancoRepository->get($whereClause);
                $chequesAnaliticoPorBancoLinhasResult = $chequesAnaliticoPorBancoLinhasRepository->get($whereClause, $bancos);
                $data = $this->chequeFacade->agrupaDadosPorBanco($chequesAnaliticoPorBancoLinhasResult);
            } else {
                $template = 'relatorios/cheques/cheques-analitico.html.twig';
                $fileName = 'cheques-analitico' . $date . '.xlsx';
                $data     = $chequesAnaliticoRepository->get($whereClause);
            }
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPADO_BANCO] !== '0') {
            $template = 'relatorios/cheques/cheques-sintetico-por-banco.html.twig';
            $fileName = 'cheques-sintetico-por-banco' . $date . '.xlsx';
            $bancos   = $chequesSinteticoPorBancoRepository->get($whereClause);
            $chequesSinteticoPorBancoLinhasResult = $chequesSinteticoPorBancoLinhasRepository->get($whereClause, $bancos);
            $data = $this->chequeFacade->agrupaDadosPorBanco($chequesSinteticoPorBancoLinhasResult);
        } else {
            $template = 'relatorios/cheques/cheques-sintetico.html.twig';
            $fileName = 'cheques-sintetico' . $date . '.xlsx';
            $data     = $chequesSinteticoRepository->get($whereClause);
        } //end if

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'data'           => $data,
        ];

        if ($formato === 'xlsx') {
            return SimpleXLSXGen::fromArray($data)->downloadAs($fileName);
        }

        return $this->render($template, $parametros);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/contas_pagar",
     *     summary="Imprimir relatórios de contas_pagar",
     *     description="Imprimir o relatório de contas_pagar passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_vencimento_inicial",  strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_vencimento_final",    strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_pagamento_inicial",   strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="data_pagamento_final",     strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="valor_inicial",            strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="valor_final",              strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="situacao",                 strict=true, allowBlank=true, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="numero_parcela_documento", strict=true, allowBlank=true, nullable=true, description="Número da parcela")
     * @FOSRest\QueryParam(name="conta",                    strict=true, allowBlank=true, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="plano_conta",              strict=true, allowBlank=true, nullable=true, description="Plano de Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="favorecido_pessoa",        strict=true, allowBlank=true, nullable=true, description="Favorecido", requirements="\d+")
     * @FOSRest\QueryParam(name="campanha",                 strict=true, allowBlank=true, nullable=true, description="Campanha", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_cobranca",           strict=true, allowBlank=true, nullable=true, description="Forma de Cobrança", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_pagamento",          strict=true, allowBlank=true, nullable=true, description="Forma de Pagamento", requirements="\d+")
     * @FOSRest\QueryParam(name="agrupamento",              strict=true, allowBlank=true, nullable=true, description="Agrupamento")
     * @FOSRest\QueryParam(name="excel",                    strict=true, allowBlank=true, nullable=false, default=0, description="Exportar para Excel", requirements="[1|0]")
     * @FOSRest\QueryParam(name="apenas_folhas_pagamento",  strict=true, allowBlank=true, nullable=false, default=0, description="Apenas despesas de folhas de pagamento", requirements="[1|0]")
     *
     * @FOSRest\Get("/relatorios/contas_pagar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioContaPagar(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/contas-pagar";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->tituloPagarFacade->gerarDadosRelatorio($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato = 'pdf';
        if ((int) $filtros[ConstanteParametros::CHAVE_EXCEL] !== 0) {
            $formato = 'xlsx';
        }

        $relatorio = "";
        if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'destino') {
            $relatorio = 'ContasPagarAgrupadoDestino.jasper';
            $parametros["subreport"] = $this->getJasperFilesBaseDir() . 'ContasPagarAgrupadoDestinoLinhas.jasper';
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'data_vencimento') {
            $relatorio = 'ContasPagarAgrupadoDataVencimento.jasper';
            $parametros["subreport"] = $this->getJasperFilesBaseDir() . 'ContasPagarAgrupadoDataVencimentoLinhas.jasper';
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'data_pagamento') {
            $relatorio = 'ContasPagarAgrupadoDataPagamento.jasper';
            $parametros["subreport"] = $this->getJasperFilesBaseDir() . 'ContasPagarAgrupadoDataPagamentoLinhas.jasper';
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'situacao') {
            $relatorio = 'ContasPagarAgrupadoSituacao.jasper';
            $parametros["subreport"] = $this->getJasperFilesBaseDir() . 'ContasPagarAgrupadoSituacaoLinhas.jasper';
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'categoria') {
            $relatorio = 'ContasPagarAgrupadoCategoria.jasper';
            $parametros["subreport"] = $this->getJasperFilesBaseDir() . 'ContasPagarAgrupadoCategoriaLinhas.jasper';
        } else {
            $relatorio = 'ContasPagar.jasper';
        }

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);

        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/contas_pagar/imprimir",
     *     summary="Imprimir relatórios de contas_pagar HTML",
     *     description="Imprimir o relatório de contas_pagar passado por parametro",
     *     produces={"text/html"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_vencimento_inicial",  strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_vencimento_final",    strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_pagamento_inicial",   strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="data_pagamento_final",     strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="valor_inicial",            strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="valor_final",              strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="situacao",                 strict=true, allowBlank=true, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="numero_parcela_documento", strict=true, allowBlank=true, nullable=true, description="Número da parcela")
     * @FOSRest\QueryParam(name="conta",                    strict=true, allowBlank=true, nullable=true, description="Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="plano_conta",              strict=true, allowBlank=true, nullable=true, description="Plano de Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="favorecido_pessoa",        strict=true, allowBlank=true, nullable=true, description="Favorecido", requirements="\d+")
     * @FOSRest\QueryParam(name="campanha",                 strict=true, allowBlank=true, nullable=true, description="Campanha", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_cobranca",           strict=true, allowBlank=true, nullable=true, description="Forma de Cobrança", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_pagamento",          strict=true, allowBlank=true, nullable=true, description="Forma de Pagamento", requirements="\d+")
     * @FOSRest\QueryParam(name="agrupamento",              strict=true, allowBlank=true, nullable=true, description="Agrupamento")
     * @FOSRest\QueryParam(name="excel",                    strict=true, allowBlank=true, nullable=false, default=0, description="Exportar para Excel", requirements="[1|0]")
     * @FOSRest\QueryParam(name="apenas_folhas_pagamento",  strict=true, allowBlank=true, nullable=false, default=0, description="Apenas despesas de folhas de pagamento", requirements="[1|0]")
     *
     * @FOSRest\View
     * @FOSRest\Get("/relatorios/contas_pagar/imprimir")
     */
    public function relatorioContaPagarHtml(
        ParamFetcher $request,
        ContasPagarAgrupadoDestinoRepository $contasPagarAgrupadoDestinoRepository,
        ContasPagarAgrupadoDestinoLinhasRepository $contasPagarAgrupadoDestinoLinhasRepository,
        ContasPagarAgrupadoDataVencimentoRepository $contasPagarAgrupadoDataVencimentoRepository,
        ContasPagarAgrupadoDataVencimentoLinhasRepository $contasPagarAgrupadoDataVencimentoLinhasRepository,
        ContasPagarAgrupadoDataPagamentoRepository $contasPagarAgrupadoDataPagamentoRepository,
        ContasPagarAgrupadoDataPagamentoLinhasRepository $contasPagarAgrupadoDataPagamentoLinhasRepository,
        ContasPagarAgrupadoSituacaoRepository $contasPagarAgrupadoSituacaoRepository,
        ContasPagarAgrupadoSituacaoLinhasRepository $contasPagarAgrupadoSituacaoLinhasRepository,
        ContasPagarAgrupadoCategoriaRepository $contasPagarAgrupadoCategoriaRepository,
        ContasPagarAgrupadoCategoriaLinhasRepository $contasPagarAgrupadoCategoriaLinhasRepository,
        ContasPagarRepository $contasPagarRepository
    ) {
        $filtros     = $request->all();
        $whereClause = $this->tituloPagarFacade->gerarDadosRelatorio($filtros);
        $franqueada  = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);

        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $date    = (new \DateTime())->format('Ymd-Hi');
        $formato = 'pdf';
        if ((int) $filtros[ConstanteParametros::CHAVE_EXCEL] !== 0) {
            $formato = 'xlsx';
        }

        // $pessoaId       = 1;
        // $categoria      = 1;
        // $situacao       = 1;
        // $dataVencimento = 0;
        if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'destino') {
            $template      = 'relatorios/contas_paga/contas-paga-agrupado-destino.html';
            $fileName      = 'contas-paga-agrupado-destino' . $date . '.xlsx';
            $reportData    = $contasPagarAgrupadoDestinoRepository->get($whereClause);
            // $reportDataSub = $contasPagarAgrupadoDestinoLinhasRepository->get($whereClause, $pessoaId);
            // $reportDataSub = $contasPagarAgrupadoDataVencimentoLinhasRepository->get($whereClause, $dataVencimento);
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'data_vencimento') {
            $template      = 'relatorios/contas_paga/contas-pagar-agrupado-data-vencimento.html';
            $fileName      = 'contas-pagar-agrupado-data-vencimento' . $date . '.xlsx';
            $reportData    = $contasPagarAgrupadoDataVencimentoRepository->get($whereClause);
            // $reportDataSub = $contasPagarAgrupadoDataVencimentoLinhasRepository->get($whereClause, $dataVencimento);
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'data_pagamento') {
            $template      = 'relatorios/contas_paga/contas-pagar-agrupado-data-pagamento.html';
            $fileName      = 'contas-pagar-agrupado-data-pagamento' . $date . '.xlsx';
            $reportData    = $contasPagarAgrupadoDataPagamentoRepository->get($whereClause);
            // $reportDataSub = $contasPagarAgrupadoDataPagamentoLinhasRepository->get($whereClause, $dataVencimento);
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'situacao') {
            $template      = 'relatorios/contas_paga/contas-pagar-agrupado-situacao.html';
            $fileName      = 'contas-pagar-agrupado-situacao' . $date . '.xlsx';
            $reportData    = $contasPagarAgrupadoSituacaoRepository->get($whereClause);
            // $reportDataSub = $contasPagarAgrupadoSituacaoLinhasRepository->get($whereClause, $situacao);
        } else if ($filtros[ConstanteParametros::CHAVE_AGRUPAMENTO] === 'categoria') {
            $template      = 'relatorios/contas_paga/contas-pagar-agrupado-categoria.html';
            $fileName      = 'contas-pagar-agrupado-categoria' . $date . '.xlsx';
            $reportData    = $contasPagarAgrupadoCategoriaRepository->get($whereClause);
            // $reportDataSub = $contasPagarAgrupadoCategoriaLinhasRepository->get($whereClause, $categoria);
        } else {
            $template   = 'relatorios/contas_paga/contas-pagar.html';
            $fileName   = 'contas-pagar' . $date . '.xlsx';
            $reportData = $contasPagarRepository->get($whereClause);
        }//end if

        if ($formato === 'xlsx') {
            $data = array_merge([str_replace('_', ' ', array_keys($reportData[0])) ], $reportData);
            return SimpleXLSXGen::fromArray($data, str_replace('.xlsx', '', $fileName))->downloadAs($fileName);
        }

        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace('excel=0', 'excel=1', $url);

        $template = $this->renderView($template, ['data' => $reportData, 'url' => $url]);
        echo $template;
        die;

        // para mais informações de porque sair com die e não com o  return response.
        // aparentemente não fomos capases de configurar corretamento o FOSRest para aceitar o retorno no formato correto.
        // $response = new Response($template);
        // return $response;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/contas_receber",
     *     summary="Imprimir relatórios de contas_receber",
     *     description="Imprimir o relatório de contas_receber passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_vencimento_inicial",  strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_vencimento_final",    strict=true, allowBlank=true, nullable=true, description="Data de Vencimento")
     * @FOSRest\QueryParam(name="data_pagamento_inicial",   strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="data_pagamento_final",     strict=true, allowBlank=true, nullable=true, description="Data de Pagamento")
     * @FOSRest\QueryParam(name="valor_inicial",            strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="valor_final",              strict=true, allowBlank=true, nullable=true, description="Valor")
     * @FOSRest\QueryParam(name="situacao",                 strict=true, allowBlank=true, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="conta",                    strict=true, allowBlank=true, nullable=true, description="Conta de Crédito")
     * @FOSRest\QueryParam(name="plano_conta",              strict=true, allowBlank=true, nullable=true, description="Categoria")
     * @FOSRest\QueryParam(name="forma_cobranca",           strict=true, allowBlank=true, nullable=true, description="Forma de Cobrança")
     * @FOSRest\QueryParam(name="forma_recebimento",        strict=true, allowBlank=true, nullable=true, description="Forma de Recebimento")
     * @FOSRest\QueryParam(name="somente_recebidas_atraso", strict=true, allowBlank=true, nullable=true, default=0, description="Apenas as recebidas em atraso")
     * @FOSRest\QueryParam(name="somente_vencidas",         strict=true, allowBlank=true, nullable=true, default=0, description="Apenas as vencidas")
     *
     * @FOSRest\Get("/relatorios/contas_receber")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioContaReceber(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/contas-receber";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->tituloReceberFacade->gerarDadosRelatorioContaReceber($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato = 'pdf';

        $relatorio = 'ContasReceber.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);

        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/balancete_financeiro",
     *     summary="Imprimir relatório de balancete financeiro",
     *     description="Imprimir o relatório de balancete financeiro passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data Final")
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="conta",        strict=true, allowBlank=true, nullable=true, description="Conta")
     *
     * @FOSRest\Get("/relatorios/balancete_financeiro")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioBalanceteFinanceiro(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->movimentoContaFacade->gerarDadosRelatoriosDeBalanceteFinanceiro($filtros);
        return new Response(Json_encode($data));

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/alunos_inadimplentes/html",
     *     summary="Imprimir relatório de alunos inadimplentes",
     *     description="Imprimir o relatório de alunos inadimplentes",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",     strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="tipo_aluno",     strict=true, allowBlank=false, nullable=false, description="Tipo de aluno ira definir o tipo do relatorio", default="0", requirements="(0|1|2)")
     * @FOSRest\QueryParam(name="tipo_relatorio", strict=true, allowBlank=false, nullable=false, description="Tipo do relatório", default="0", requirements="(0|1)")
     *
     * @FOSRest\QueryParam(name="situacao",            strict=false, allowBlank=true, nullable=true, description="Situação do aluno")
     * @FOSRest\QueryParam(name="classificacao_aluno", strict=false, allowBlank=true, nullable=true, description="Classificação")
     * @FOSRest\QueryParam(name="tipo_ocorrencia",     strict=false, allowBlank=true, nullable=true, description="Tipo da ocorrência")
     *
     * @FOSRest\Get("/relatorios/alunos_inadimplentes/html")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAlunosInadimplentes(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/alunos-inadimplentes";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->tituloReceberFacade->prepararDadosRelatorioAlunosInadimplentes($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $formato = 'pdf';

        if ((int) $request->get('tipo_relatorio') === 0) {
            $parametros = [
                'nomeFranqueada'  => $franqueada->getNome(),
                'logoInflux'      => $this->getInfluxPath(),
                'clausulaWhere'   => $whereClause,
                'subreportsPath'  => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
                'tipo_ocorrencia' => $request->get('tipo_ocorrencia'),
            ];
            $relatorio  = 'InadimplentesAlunos.jasper';
        } else {
            $parametros = [
                'nomeFranqueada' => $franqueada->getNome(),
                'logoInflux'     => $this->getInfluxPath(),
                'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
                'franqueada_id'  => $request->get('franqueada'),
            ];
            $relatorio  = 'InadimplentesValores.jasper';
        }

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/alunos_inadimplentes_buscar",
     *     summary="Retorna um JSON com os dados para gerar o relatório de alunos inadimplentes",
     *     description="Dados do relatório de alunos inadimplentes",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retornou o JSON com o dados para gerar o relatório de alunos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",     strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="tipo_aluno",     strict=true, allowBlank=false, nullable=false, description="Tipo de aluno ira definir o tipo do relatorio", default="0", requirements="(0|1|2)")
     * @FOSRest\QueryParam(name="tipo_relatorio", strict=true, allowBlank=false, nullable=false, description="Tipo do relatório", default="0", requirements="(0|1)")
     *
     * @FOSRest\QueryParam(name="situacao",            strict=false, allowBlank=true, nullable=true, description="Situação do aluno")
     * @FOSRest\QueryParam(name="classificacao_aluno", strict=false, allowBlank=true, nullable=true, description="Classificação")
     * @FOSRest\QueryParam(name="forma_cobranca",      strict=true, allowBlank=true, nullable=true, description="Forma de Cobrança")
     * @FOSRest\QueryParam(name="data_inicio",    strict=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_fim",      strict=false, allowBlank=false, description="Data Final")
     *
     * @FOSRest\Get("/relatorios/alunos_inadimplentes_buscar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAlunosInadimplentesImprimir(ParamFetcher $request, InadimplentesAlunosRepository $inadimplentesAlunosRepository) {
        try{
            $filtros = $request->all();

            $result = $inadimplentesAlunosRepository->getInadimplentes($filtros);
            
            return new Response(json_encode($result), 200);
        }catch(\Exception $e){  
            return new Response(json_encode($e->getMessage(), 500));
        }
    }

       /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/alunos_inadimplentes",
     *     summary="Retorna um JSON com os dados para gerar o relatório de alunos inadimplentes",
     *     description="Dados do relatório de alunos inadimplentes",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retornou o JSON com o dados para gerar o relatório de alunos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",     strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="tipo_aluno",     strict=true, allowBlank=false, nullable=false, description="Tipo de aluno ira definir o tipo do relatorio", default="0", requirements="(0|1|2)")
     * @FOSRest\QueryParam(name="tipo_relatorio", strict=true, allowBlank=false, nullable=false, description="Tipo do relatório", default="0", requirements="(0|1)")
     *
     * @FOSRest\QueryParam(name="situacao",            strict=false, allowBlank=true, nullable=true, description="Situação do aluno")
     * @FOSRest\QueryParam(name="classificacao_aluno", strict=false, allowBlank=true, nullable=true, description="Classificação")
     * @FOSRest\QueryParam(name="forma_cobranca",      strict=true, allowBlank=true, nullable=true, description="Forma de Cobrança")
     * @FOSRest\QueryParam(name="data_inicio",    strict=false, allowBlank=false, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_fim",      strict=false, allowBlank=false, description="Data Final")
     *
     * @FOSRest\Get("/relatorios/alunos_inadimplentes")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAlunosInadimplentesBuscar(ParamFetcher $request, InadimplentesAlunosRepository $inadimplentesAlunosRepository) {
        try{
            $filtros = $request->all();
            $params = [
                'franqueada' =>  $filtros['franqueada'],
                'situacao_aluno' => $filtros['situacao'],
                'situacao' => ['VEN'],
                'data_inicio' => $filtros['data_inicio'],
                'data_fim' => $filtros['data_fim']
            ];  
          
            $resultadosConsulta = $this->contaReceberFacade->consulta($params);

            $dados = $resultadosConsulta['itens'];
         
            $agrupadoPorCliente = [];
            $valorSaldoDevedor = 0;
 

            foreach ($dados as $item) {
                $alunoId        = $item['aluno_id'];
                $clienteNome = isset($item['cliente_nome']) ? $item['cliente_nome'] : '';
                $clienteCNPJ_CPF = isset($item['cliente_cpf']) ? $item['cliente_cpf'] : '';

                $idade = isset($item['cliente_idade']) ? $item['cliente_idade'] : '';
                $situacao = isset($item['situacao_aluno']) ? $item['situacao_aluno'] : '';
                $endereco = isset($item['cliente_cpf']) ? $item['cliente_cpf'] : '';
                $bairro = isset($item['cliente_bairro']) ? $item['cliente_bairro'] : '';
                $cidade = isset($item['cliente_cidade']) ? $item['cliente_cidade'] : '';
                $cep = isset($item['cliente_cep']) ? $item['cliente_cep'] : '';
                $fone = isset($item['cliente_telefone']) ? $item['cliente_telefone'] : '';
                $rg = isset($item['cliente_identidade']) ? $item['cliente_identidade'] : '';
               
               if($item['cliente_nome'] === $item['responsavel_financeiro_nome']) {
                   $nome_responsavel = "O próprio";
                   $parentesco_responsavel =  "";
                   $cpf_responsavel =  "";
                   $fone_responsavel =  "";                   
               } else {
                   $nome_responsavel = $item['responsavel_financeiro_nome'];
                   $parentesco_responsavel =  isset($item['parentesco_aluno']) ? $item['parentesco_aluno'] : '';
                   $cpf_responsavel =  isset($item['responsavel_financeiro_cpf']) ? $item['responsavel_financeiro_cpf'] : '';
                   $fone_responsavel =  isset($item['responsavel_financeiro_telefone_preferencial']) ? $item['responsavel_financeiro_telefone_preferencial'] : '';
               }
                   $celular_responsavel =  "";
                   $fone_comercial_responsavel =  "";
              
                $valorSaldoDevedor = floatval($item['valor_saldo_devedor']); // Converte para float
                 $dataVencimento = isset($item['data_vencimento']) ? $item['data_vencimento'] : null;
        
            // Se o cliente já existir no array, adiciona o valor_saldo_devedor, verifica e atualiza a menor data de vencimento
            if (isset($agrupadoPorCliente[$clienteNome])) {
                $agrupadoPorCliente[$clienteNome]['total_vencido'] += $valorSaldoDevedor;
        
                // Verifica e atualiza a menor data de vencimento
                if ($dataVencimento !== null && ($agrupadoPorCliente[$clienteNome]['inadimplente_desde'] === null || $dataVencimento < $agrupadoPorCliente[$clienteNome]['inadimplente_desde'])) {
                    $agrupadoPorCliente[$clienteNome]['inadimplente_desde'] = $dataVencimento;
                }
            } else {
                $agrupadoPorCliente[$clienteNome] = [
                    'alunoId' => $alunoId,
                    'idade' => $idade,
                    'situacao' => $situacao,
                    "endereco"=> $endereco,
                    "bairro" =>$bairro,
                    "cidade" => $cidade,
                    "cep" => $cep,
                    "fone"=> $fone,
                    "rg"=> $rg,
                    "cnpj_cpf"=> $clienteCNPJ_CPF,
                    "nome_responsavel"=> $nome_responsavel,
                    "parentesco_responsavel"=> $parentesco_responsavel,
                    "cpf_responsavel"=> $cpf_responsavel,
                    "fone_responsavel"=> $fone_responsavel,
                    "celular_responsavel"=> $celular_responsavel,
                    "fone_comercial_responsavel"=>  $fone_comercial_responsavel,
                    'total_vencido' => $valorSaldoDevedor,
                    'inadimplente_desde' => $dataVencimento,
                ];
            }
        }
        
            // Transforma o resultado em um array com o formato desejado
            $resultados = [];
            foreach ($agrupadoPorCliente as $nome => $dadosCliente) {
                $resultado = [
                    'aluno_id'  => $dadosCliente['alunoId'],
                    'nome_contato' => $nome,
                    'idade' => $dadosCliente['idade'],
                    'situacao' => $dadosCliente['situacao'],
                    "endereco"=> $dadosCliente['endereco'],
                    "bairro" => $dadosCliente['bairro'],
                    "cidade" => $dadosCliente['cidade'],
                    "cep" => $dadosCliente['cep'],
                    "fone"=> $dadosCliente['fone'],
                    "rg"=> $dadosCliente['rg'],
                    "cnpj_cpf"=> $dadosCliente['cnpj_cpf'],
                    "nome_responsavel"=> $dadosCliente['nome_responsavel'],
                    "parentesco_responsavel"=> $dadosCliente['parentesco_responsavel'],
                    "cpf_responsavel"=> $dadosCliente['cpf_responsavel'],
                    "fone_responsavel"=> $dadosCliente['fone_responsavel'],
                    "celular_responsavel"=> $dadosCliente['celular_responsavel'],
                    "fone_comercial_responsavel"=> $dadosCliente['fone_comercial_responsavel'],
                    'total_vencido' => number_format($dadosCliente['total_vencido'], 2, '.', ''), // Formata como número com duas casas decimais
                    'inadimplente_desde' => $dadosCliente['inadimplente_desde'],
                ];
                $resultados[] = $resultado;
            }

            $numItens = count($resultados);
            $resultFinal = [
                "total" => [
                    [
                        "total_devedor" => $resultadosConsulta['total_vencido'],
                        "total_inadimplentes" => $numItens
                    ]
                ],
                "results" => $resultados
            ];

            

           // $titulos = $this->contaReceberRepository->buscarTitulos($params);

            return new Response(json_encode($resultFinal), 200);
        }catch(\Exception $e){  
            return new Response(json_encode($e->getMessage(), 500));
        }
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/informacoes-funcionarios",
     *     summary="Imprimir relatório de informações de funcionários",
     *     description="Imprimir o relatório de informações de funcionários passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="data_aniversario_de",  strict=true, allowBlank=true, nullable=true, description="Data de aniversario do funcionario inicial")
     * @FOSRest\QueryParam(name="data_aniversario_ate", strict=true, allowBlank=true, nullable=true, description="Data de aniversario do funcionario final")
     * @FOSRest\QueryParam(name="data_cadastro_de",     strict=true, allowBlank=true, nullable=true, description="Data de cadastro do funcionario inicial")
     * @FOSRest\QueryParam(name="data_cadastro_ate",    strict=true, allowBlank=true, nullable=true, description="Data de cadastro do funcionario final")
     * @FOSRest\QueryParam(name="funcionario",          strict=true, allowBlank=true, nullable=true, description="Id Funcionário", requirements="\d+")
     * @FOSRest\QueryParam(name="cargo",                strict=true, allowBlank=true, nullable=true, description="Id Cargo", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",             strict=true, allowBlank=true, nullable=true, description="Situação")
     *
     * @FOSRest\Get("/relatorios/informacoes-funcionarios")
     *
     * @return \Symfony\Component\HttpFoundation\Response
    */
    
    public function relatorioInformacoesFuncionarios(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->funcionarioFacade->gerarDadosRelatorio($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *return new Response(json_encode($e->getMessage(), 500));
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",              strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",                strict=true, allowBlank=true, nullable=true, description="Situações dos titulos", map=true)
     * @FOSRest\QueryParam(name="formas_cobranca",         strict=true, allowBlank=true, nullable=true, description="Formas de cobrança dos titulos", map=true)
     * @FOSRest\QueryParam(name="formas_pagamento",        strict=true, allowBlank=true, nullable=true, description="Formas de pagamento dos titulos", map=true)
     * @FOSRest\QueryParam(name="forma_cartao",            strict=true, nullable=true, description="Ignora Titulos Cartao", requirements="(0|1)", default="0")
     * @FOSRest\QueryParam(name="forma_cheque",            strict=true, nullable=true, description="Ignora Titulos Cheque", requirements="(0|1)", default="0")
     * @FOSRest\QueryParam(name="data_inicial_vencimento", strict=true, allowBlank=true, nullable=true, description="Data inicial de vencimento do titulo")
     * @FOSRest\QueryParam(name="data_final_vencimento",   strict=true, allowBlank=true, nullable=true, description="Data final de vencimento do titulo")
     * @FOSRest\QueryParam(name="plano_conta",             strict=true, allowBlank=true, nullable=true, description="Id de um plano de conta que ao menos um item da conta a receber do titulo deve ter")
     * @FOSRest\QueryParam(name="agrupar_por_parcelas",    strict=true, allowBlank=true, nullable=true, description="Como deve agrupar - altera a forma do relatório")
     * @FOSRest\QueryParam(name="order",                   strict=true, allowBlank=true, nullable=true, description="Ordenamento dos dados")
     *
     * @FOSRest\Get("/relatorios/titulos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioTitulos(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        if (isset($filtros["agrupar_por_parcelas"]) === true && $filtros["agrupar_por_parcelas"] === 'S') {
            $response['agrupado'] = true;
            $retorno = $this->tituloReceberFacade->gerarDadosRelatorioTitulosAnalitico($mensagemErro, $filtros);
           // $response['data'] = $this->tituloReceberFacade->gerarDadosRelatorioTitulosAnalitico($mensagemErro, $filtros);
        } else {
            $response['agrupado'] = false;
            $retorno = $this->tituloReceberFacade->gerarDadosRelatorioTitulosSintetico($mensagemErro, $filtros);
        }

        $response['LIQUIDADOS'] = $retorno['LIQ'];
        $response['PENDENTES'] = $retorno['PEN'];
        $response['VENCIDOS'] = $retorno['VEN'];
        $response['RENEGOCIADOS'] = $retorno['SUB'];
        $response['CANCELADOS'] = $retorno['CAN'];
        $response['data'] = $retorno['data'];

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        return new Response(json_encode($response));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/situacao_aluno",
     *     summary="Gerar relatório de situação alunos",
     *     description="Gerar relatório de situação alunos de acordo com os parametros",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",            strict=true, allowBlank=true, nullable=true, description="Id do aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="instrutor",        strict=true, allowBlank=true, nullable=true, description="Id do instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="curso",            strict=true, allowBlank=true, nullable=true, description="Id do curso", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade_turma", strict=true, allowBlank=true, nullable=true, description="Id da modalidade_turma", requirements="\d+")
     * @FOSRest\QueryParam(name="turma",            strict=true, allowBlank=true, nullable=true, description="Id do turma", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",         strict=true, allowBlank=true, nullable=true, description="Situações dos alunos", map=true)
     * @FOSRest\QueryParam(name="frequencia",       strict=false, nullable=true,  description="Aluno frequencia")
     * @FOSRest\QueryParam(name="notas",            strict=false, nullable=true,  description="Aluno Notas")
     *
     * @FOSRest\Get("/relatorios/situacao_aluno")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioSituacaoAlunos(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();
        
        
        $linhas_excel = [
            [
                "Nome",
                "Email",
                "Fone",
                "",
                
            ],
        ];
        
        if ($filtros['notas'] == 'true') {
            $linhas_excel[0][] = "Notas";           
        }
        
        if ($filtros['frequencia'] == 'true') {
            $linhas_excel[0][] = "Frequência";           
        }


        $linhas_excel[0][] = "Situação";
 

        $mensagemErro = "";
        $filtros      = $request->all();

        $dadosRelatorio = $this->alunoFacade->gerarDadosRelatorioSituacaoAlunos($mensagemErro, $filtros);

    
        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $i = 0;

        foreach ($dadosRelatorio["alunos"] as $aluno) {
            $i++;
            $linhas_excel[] = [
                $aluno["nome"],
                $aluno["email"],
                $aluno["fone"],
                "",
            ];
            if ($filtros['notas'] == 'true') {
                $linhas_excel[$i][] = "9";           
            }
            
            if ($filtros['frequencia'] == 'true') {
                $linhas_excel[$i][] = "80%";           
            }
            
            $linhas_excel[$i][] = $aluno["situacao"]; 
        }
    
        foreach ($dadosRelatorio["totais"] as $k => $v) {
            $linhas_excel[] = [
                "",
                "$k:",
                $v,
            ];
        }

        $xlsx = SimpleXLSXGen::fromArray($linhas_excel);
        return $xlsx->downloadAs('situacao_alunos.xlsx');
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/situacao_contrato",
     *     summary="Gerar relatório de situação contratos",
     *     description="Gerar relatório de situação contratos de acordo com os parametros",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="tipo_movimentacao",                strict=true, allowBlank=true, nullable=true, description="Tipo de movimentação")
     * @FOSRest\QueryParam(name="data_criacao_de",                  strict=true, allowBlank=true, nullable=true, description="Data criação DE")
     * @FOSRest\QueryParam(name="data_criacao_ate",                 strict=true, allowBlank=true, nullable=true, description="Data criação ATE")
     * @FOSRest\QueryParam(name="data_contrato_cancelamento_de",    strict=true, allowBlank=true, nullable=true, description="Data cancelamento DE")
     * @FOSRest\QueryParam(name="data_contrato_cancelamento_ate",   strict=true, allowBlank=true, nullable=true, description="Data cancelamento ATE")
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="curso",                            strict=true, allowBlank=true, nullable=true, description="Id do curso dos contratos", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao_contrato",                strict=true, allowBlank=true, nullable=true, description="Situações dos contratos")
     * @FOSRest\QueryParam(name="situacao_aluno",                   strict=true, allowBlank=true, nullable=true, description="Situações dos alunos")
     * @FOSRest\QueryParam(name="data_inicio_contrato_de",          strict=true, allowBlank=true, nullable=true, description="Data Início contrato DE")
     * @FOSRest\QueryParam(name="data_termino_contrato_ate",        strict=true, allowBlank=true, nullable=true, description="Data Termino contrato ATE")
     * @FOSRest\QueryParam(name="idioma",                           strict=true, allowBlank=true, nullable=true, description="Idioma dos cursos do contrato")
     * @FOSRest\QueryParam(name="livro",                            strict=true, allowBlank=true, nullable=true, description="Id do livro", requirements="\d+")
     * @FOSRest\QueryParam(name="instrutor",                        strict=true, allowBlank=true, nullable=true, description="Id do instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="semestre",                         strict=true, allowBlank=true, nullable=true, description="Id do semestre", requirements="\d+")
     * @FOSRest\QueryParam(name="responsavel_carteira",             strict=true, allowBlank=true, nullable=true, description="Id do responsável carteira", requirements="\d+")
     * @FOSRest\QueryParam(name="mostrar_motivo_cancelamento",      strict=true, allowBlank=true, nullable=true, description="Se deve ou não trazer o motivo do cancelamento no relatório")
     *
     * @FOSRest\Get("/relatorios/situacao_contrato")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioSituacaoContrato(ParamFetcher $request)
    {
        $filtros      = $request->all();
        $filtros[ConstanteParametros::CHAVE_MOSTRAR_MOTIVO_CANCELAMENTO] = $filtros[ConstanteParametros::CHAVE_MOSTRAR_MOTIVO_CANCELAMENTO] === 'true';

        $resposta = $this->contratoFacade->gerarDadosRelatorioSituacaoContrato($filtros);

        return new Response(json_encode($resposta));
        /*
            $possuiCampoMovimentacao = (isset($filtros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE]) === true && empty($filtros[ConstanteParametros::CHAVE_DATA_INICIO_CONTRATO_DE]) === false) ||
                (isset($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === true && empty($filtros[ConstanteParametros::CHAVE_DATA_TERMINO_CONTRATO_ATE]) === false) ||
                (isset($filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) === true && count($filtros[ConstanteParametros::CHAVE_SITUACAO_CONTRATO]) > 0);

            if ($possuiCampoMovimentacao === true) {
                // Movimentação: Se data_inicio_contrato_de, data_termino_contrato_ate, situacao_contrato
                $linhas_excel  = $this->contratoFacade->gerarDadosRelatorioMovimentacaoContratos($mensagemErro, $filtros);
                $nomeRelatorio = 'contratos_movimentacao';
            } else {
                // Senão, Matriculas e rematriculas
                $linhas_excel  = $this->contratoFacade->gerarDadosRelatorioMatriculaRematriculaContratos($mensagemErro, $filtros);
                $nomeRelatorio = 'contratos_matricula_rematricula';
            }

            if (empty($mensagemErro) === false) {
                $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
                $response->headers->set('Content-Type', 'text/html');
                return $response;
            }

            $xlsx = SimpleXLSXGen::fromArray($linhas_excel);
            return $xlsx->downloadAs("$nomeRelatorio.xlsx");
        */
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/frequencia",
     *     summary="Gerar relatório das frequencias",
     *     description="Gerar JSON do relatório das frequencias de acordo com os parametros",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o JSON para gerar o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=false, nullable=false, description="Data período DE")
     * @FOSRest\QueryParam(name="data_final", strict=true, allowBlank=false, nullable=false, description="Data período ATE")
     * @FOSRest\QueryParam(name="turma", strict=true, allowBlank=true, nullable=true, description="Id da turma", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno", strict=true, allowBlank=true, nullable=true, description="Id do aluno", requirements="\d+")
     *
     * @FOSRest\Get("/relatorios/frequencia")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioFrequencia(ParamFetcher $parametros)
    {
        try{
            $filtros = $parametros->all();
        
            $response = $this->alunoDiarioFacade->gerarDadosRelatorioFrequencia($filtros);
    
        }catch(\Exception $e) {
            return new Response(json_encode($e->getMessage()), 422);
        }
        return new Response(json_encode($response));
        
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/nota",
     *     summary="Gerar relatório das notas",
     *     description="Gerar relatório das notas de acordo com os parametros",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="data_periodo_de",      strict=true, allowBlank=true, nullable=true, description="Data período DE")
     * @FOSRest\QueryParam(name="data_periodo_ate",     strict=true, allowBlank=true, nullable=true, description="Data período ATE")
     * @FOSRest\QueryParam(name="curso",                strict=true, allowBlank=true, nullable=true, description="Id do curso dos contratos", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade_turma",     strict=true, allowBlank=true, nullable=true, description="Modalidade da turma", map=true)
     * @FOSRest\QueryParam(name="situacao_turma",       strict=true, allowBlank=true, nullable=true, description="Situações das turmas", map=true)
     * @FOSRest\QueryParam(name="turma",                strict=true, allowBlank=true, nullable=true, description="Id da turma", requirements="\d+")
     * @FOSRest\QueryParam(name="agrupar_turma",        strict=true, allowBlank=true, nullable=true, description="Se deve ou não agrupar o relatório por turma")
     * @FOSRest\QueryParam(name="livro",                strict=true, allowBlank=true, nullable=true, description="Id do livro", requirements="\d+")
     * @FOSRest\QueryParam(name="instrutor",            strict=true, allowBlank=true, nullable=true, description="Id do instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",                strict=true, allowBlank=true, nullable=true, description="Id do aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_mid_term_min",   strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_mid_term_max",   strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     * @FOSRest\QueryParam(name="valor_final_test_min", strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_final_test_max", strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     * @FOSRest\QueryParam(name="valor_wg_max",         strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_wg_min",         strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     *
     * @FOSRest\Get("/relatorios/nota")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioNota(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $filtros[ConstanteParametros::CHAVE_AGRUPAR_TURMA] = $filtros[ConstanteParametros::CHAVE_AGRUPAR_TURMA] === 'true';
        $agruparTurma = $filtros[ConstanteParametros::CHAVE_AGRUPAR_TURMA] === true;

        $modalidadePersonalSelecionada = isset($filtros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true && count($filtros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) > 0
            && in_array(SituacoesSistema::MODALIDADE_PERSONAL, $filtros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);

        if ($modalidadePersonalSelecionada === true) {
            $linhas_excel  = $this->alunoAvaliacaoFacade->gerarDadosRelatorioNotasPersonal($mensagemErro, $filtros);
            $nomeRelatorio = 'notas_personal';
        } else if ($agruparTurma === true) {
            $linhas_excel  = $this->alunoAvaliacaoFacade->gerarDadosRelatorioNotasAgrupadoTurma($mensagemErro, $filtros);
            $nomeRelatorio = 'notas_agrupado_turma';
        } else {
            $linhas_excel  = $this->alunoAvaliacaoFacade->gerarDadosRelatorioNotasAlunos($mensagemErro, $filtros);
            $nomeRelatorio = 'notas_alunos';
        }

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $xlsx = SimpleXLSXGen::fromArray($linhas_excel);
        return $xlsx->downloadAs("$nomeRelatorio.xlsx");

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/nota-turma",
     *     summary="Gerar relatório das notas",
     *     description="Gerar relatório das notas de acordo com os parametros",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="semestre",             strict=true, allowBlank=true, nullable=true, description="Semestre")
     * @FOSRest\QueryParam(name="curso",                strict=true, allowBlank=true, nullable=true, description="Id do curso dos contratos", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade_turma",     strict=true, allowBlank=true, nullable=true, description="Modalidade da turma", map=true)
     * @FOSRest\QueryParam(name="situacao_turma",       strict=true, allowBlank=true, nullable=true, description="Situações das turmas", map=true)
     * @FOSRest\QueryParam(name="turma",                strict=true, allowBlank=true, nullable=true, description="Id da turma", requirements="\d+")
     * @FOSRest\QueryParam(name="agrupar_turma",        strict=true, allowBlank=true, nullable=true, description="Se deve ou não agrupar o relatório por turma")
     * @FOSRest\QueryParam(name="livro",                strict=true, allowBlank=true, nullable=true, description="Id do livro", requirements="\d+")
     * @FOSRest\QueryParam(name="instrutor",            strict=true, allowBlank=true, nullable=true, description="Id do instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",                strict=true, allowBlank=true, nullable=true, description="Id do aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="valor_mid_term_min",   strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_mid_term_max",   strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     * @FOSRest\QueryParam(name="valor_final_test_min", strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_final_test_max", strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     * @FOSRest\QueryParam(name="valor_wg_max",         strict=true, allowBlank=true, nullable=true, description="Valor mínimo da nota")
     * @FOSRest\QueryParam(name="valor_wg_min",         strict=true, allowBlank=true, nullable=true, description="Valor máximo da nota")
     *
     * @FOSRest\Get("/relatorios/nota-turma")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioNotaTurma(ParamFetcher $request)
    {
        $filtros      = $request->all();

        $agruparTurma = isset($filtros[ConstanteParametros::CHAVE_AGRUPAR_TURMA]) && $filtros[ConstanteParametros::CHAVE_AGRUPAR_TURMA] == "true";
         
        if ($agruparTurma && !isset($filtros[ConstanteParametros::CHAVE_ALUNO])) {

            $data  = $this->alunoAvaliacaoFacade->gerarDadosRelatorioNotasTurmasAgrupadoTurma($filtros);
            $nomeRelatorio = 'notas-agrupado-turma';
        } else {
            $data  = $this->alunoAvaliacaoFacade->gerarDadosRelatorioNotasTurmasAlunos($filtros);
            $nomeRelatorio = 'notas-alunos';
        }

        $response = ['nomeRelatorio' => $nomeRelatorio, 'data' => $data ];

        return new Response(json_encode($response));
    }

 /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/gerar_mala_direta",
     *     summary="Realiza o processo de geração do arquivo para mala direta.",
     *     description="Realiza o processo de geração do arquivo para mala direta.",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a mala direta"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_termino_contrato_inicio", strict=true, allowBlank=true, nullable=true, description="Data Termino contrato DE")
     * @FOSRest\QueryParam(name="data_termino_contrato_fim",    strict=true, allowBlank=true, nullable=true, description="Data Termino contrato ATE")
     * @FOSRest\QueryParam(name="franqueada",                   strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_responsavel",             strict=true, allowBlank=false, nullable=false, description="Tipo de responsavel", default="0", requirements="(0|1|2)")
     * @FOSRest\QueryParam(name="turma",                        strict=true, allowBlank=true, nullable=true, description="Id Turma", requirements="\d+")
     * @FOSRest\QueryParam(name="livro",                        strict=true, allowBlank=true, nullable=true, description="Id Livro", requirements="\d+")
     *
     * @FOSRest\Get("/relatorios/gerar_mala_direta")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gerarDadosParaMalaDireta(ParamFetcher $request)
    {
        $parametros = $request->all();

        $dados = $this->contratoFacade->buscaDadosMalaDireta($parametros);

        return new Response(json_encode($dados));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/interessados_periodo",
     *     summary="Imprimir relatórios de interessados_periodo",
     *     description="Imprimir o relatório de interessados por periodo passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="periodo_pretendido", strict=true, allowBlank=true, nullable=true, description="Período Pretendido")
     * @FOSRest\QueryParam(name="livro",              strict=true, allowBlank=true, nullable=true, description="Livro")
     * @FOSRest\QueryParam(name="idioma",             strict=true, allowBlank=true, nullable=true, description="Idioma")
     * @FOSRest\QueryParam(name="data_inicial",             strict=true, allowBlank=true, nullable=true, description="Idioma")
     * @FOSRest\QueryParam(name="data_final",             strict=true, allowBlank=true, nullable=true, description="Idioma")
     *
     * @FOSRest\Get("/relatorios/interessados_periodo")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioInteressadosPeriodo(ParamFetcher $request)
    {
        $filtros = $request->all();
        $dados = $this->interessadoFacade->gerarDadosRelatorio($filtros, '1');

        return new Response(json_encode($dados));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/alunos_situacao",
     *     summary="Imprimir relatórios de alunos (quantidade) por situaçao (por mês)",
     *     description="Imprimir o relatório de alunos (quantidade) por situaçao, por mês (do ano passado por parametro)",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="ano",      strict=true, allowBlank=true, nullable=false, description="Ano para gerar o relatório")
     * @FOSRest\QueryParam(name="situacao", strict=true, allowBlank=true, nullable=true, description="Situação")
     *
     * @FOSRest\Get("/relatorios/alunos_situacao")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAlunosSituacao(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/alunos-situacao";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->historicoSituacaoAlunoFacade->gerarDadosRelatorio($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato = 'pdf';

        $relatorio = 'AlunosSituacao.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/checklist_atividade",
     *     summary="Imprimir relatórios de atividades do checklist por consultor",
     *     description="Imprimir o relatório de checklist do consultor passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pendentes",    strict=true, allowBlank=true, nullable=true, description="Pendentes")
     * @FOSRest\QueryParam(name="realizadas",   strict=true, allowBlank=true, nullable=true, description="Realizadas")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data de Conclusão")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data de Conclusão")
     * @FOSRest\QueryParam(name="consultor",    strict=true, allowBlank=true, nullable=false, description="Consultor")
     *
     * @FOSRest\Get("/relatorios/checklist_atividade")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioChecklistAtividade(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/checklist_atividade";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->checklistAtividadeFacade->gerarDadosRelatorio($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato = 'pdf';

        $relatorio = 'AtividadesChecklist.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);

        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/class_record_diario",
     *     summary="Imprimir relatório class record diario",
     *     description="Imprimir o relatório class record diario da turma passada por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=true, nullable=true, description="Ids de turma", map=true)
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     *
     * @FOSRest\Get("/relatorios/class_record_diario")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioClassRecordDiario(ParamFetcher $request)
    {
        $mensagemErro = "";
        $ids          = $request->get('turma');
        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/class-record-diario";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $pdf      = new PDFMerger;
        $arquivos = [];

        foreach ($ids as $id) {
            $parametros = [
                'nomeFranqueada' => $franqueada->getNome(),
                'logoInflux'     => $this->getInfluxPath(),
                'franqueada_id'  => $request->get('franqueada'),
                'turma_id'       => $id,
                'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
            ];

            $formato   = 'pdf';
            $relatorio = 'ClassRecordDiario.jasper';

            $nomeRelatorio = str_replace('.jasper', '', $relatorio);
            $date          = (new \DateTime())->format('Ymd-Hi');
            $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$id-$date";
            self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
            self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
            self::getJasperHelper()->setParametrosRelatorios($parametros);
            self::getJasperHelper()->setFormatosDeSaida([$formato]);
            self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

            if (empty($mensagemErro) === false) {
                $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
                $response->headers->set('Content-Type', 'text/html');
                return $response;
            }

            $pdf->addPDF("$arquivoRelatorio.$formato");
            $arquivos[] = "$arquivoRelatorio.$formato";
        } //end foreach

        $file = $pdf->merge("string");

        foreach ($arquivos as $arquivo) {
            unlink($arquivo);
        }

        return new PdfResponse($file, "classRecord.pdf");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/class_record_diario/imprimir_reduzido",
     *     summary="Imprimir relatório class record diario",
     *     description="Imprimir o relatório class record diario da turma passada por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=true, nullable=true, description="Ids de turma", map=true)
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     *
     * @FOSRest\Get("/relatorios/class_record_diario/imprimir_reduzido")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioClassRecordDiarioImprimir(ParamFetcher $request)
    {
        $mensagemErro = "";
        $ids          = $request->get('turma');
        $franqueada   = $request->get('franqueada');

        $turmas = implode(",", $ids);

        $sql = "
        select  turma.id, turma.descricao as class,
                semestre.descricao as semestre,
                turma_aula.data_aula as date_lesson, 
                licao.numero as number_lesson, 
                licao.descricao as lesson, 
                sala.descricao as classroom,
                pessoa.nome_contato as teacher,
                horario.descricao as days_time,
                livro.descricao as book                
        
        from turma  
        
        inner join turma_aula
            on turma.id = turma_aula.turma_id
        inner join licao
            on turma_aula.licao_id = licao.id
        
        inner join horario
            on turma.horario_id = horario.id
        
        left join sala_franqueada
            on turma.sala_franqueada_id = sala_franqueada.id
        inner join sala
            on sala_franqueada.sala_id = sala.id
        
        inner join semestre
            on turma.semestre_id = semestre.id
        
        inner join livro
            on turma.livro_id = livro.id    
        
        left join funcionario
            on turma.funcionario_id = funcionario.id
        inner join pessoa
            on funcionario.pessoa_id = pessoa.id

        where turma.id in ({$turmas})
        and turma.franqueada_id = {$franqueada} ";

        $connection = self::getManagerRegistry()->getConnection();

        $data = $connection->fetchAll($sql);

        $turmas = [];
        foreach ($ids as $id) {
            $itens = array_filter(
                $data,
                function ($item) use ($id) {
                    return $item['id'] == $id;
                }
            );

            if (count($itens) > 0) {
                $first = array_values($itens)[0];
                
                            
                $turma_id =   $id;
  
                $turmas[$id]['name']      = $first['class'];
                $turmas[$id]['daystime']  = $first['days_time'];
                $turmas[$id]['teacher']   = $first['teacher'];
                $turmas[$id]['book']      = $first['book'];
                $turmas[$id]['classroom'] = $first['classroom'];
                $turmas[$id]['itens']     = $itens;
                
                $sqlAlunos = "select turma.id as turma_id, pa.nome_contato as nome_aluno 
                                        
                                        from turma
                                        
                                        inner join contrato 
                                        on turma.id = contrato.turma_id 
                                        
                                        inner JOIN aluno 
                                        on contrato.aluno_id = aluno.id 
                                        
                                        inner join pessoa as pa 
                                        on aluno.pessoa_id = pa.id 
                                        
                                        where turma.id in ({$turma_id})
                                         and aluno.situacao = 'ATI'
                                         and contrato.situacao <> 'C'";
                                        
                                        $alunos = $connection->fetchAll($sqlAlunos);
                                        
                                        $turmas[$id]['alunos']    = $alunos;                      
            }


        }
 
        $html = $this->renderView('relatorios/class_record/report.html', ["turmas" => $turmas]);

        echo $html;

        die;
    }

     /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/class_record_diario/imprimir",
     *     summary="Imprimir relatório class record diario",
     *     description="Imprimir o relatório class record diario da turma passada por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=true, nullable=true, description="Ids de turma", map=true)
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     *
     * @FOSRest\Get("/relatorios/class_record_diario/imprimir")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioClassRecordDiarioImprimirCompleto(ParamFetcher $request)
    {
        $mensagemErro = "";

        $ids          = $request->get('turma');
        $franqueada   = $request->get('franqueada');
        
        $turmas = implode(",", $ids);
        
        $parametros = [
            'turma'          => $turmas,
            'franqueada'  => $franqueada
        ];
        
        $data = $this->turmaAulaFacade->gerarDadosRelatorioDiarioClasseCompleto($parametros);
        
        $turmas = [];
        
        foreach ($data as $turma) {
            $turmas[$turma['id']]['name']      = $turma['descricao'];
            $turmas[$turma['id']]['daystime']  = $turma['horario']['descricao'];
            $turmas[$turma['id']]['teacher']   = isset($turma['funcionario']['pessoa']['nome_contato']) ? $turma['funcionario']['pessoa']['nome_contato'] : 'Indefinido';
            $turmas[$turma['id']]['book']      = $turma['livro']['descricao'];
            $turmas[$turma['id']]['classroom'] = isset($turma['sala_franqueada']['sala']['descricao']) ? $turma['sala_franqueada']['sala']['descricao'] : 'Sem Sala';
            $contratos = $turma['contratos'];
            $itens = [];
            
            $alunos = [];
            foreach ($contratos as $contrato) {
                if ($contrato['situacao'] !== 'C') {
                    $itens[$contrato['aluno']['id']]['nome'] = $contrato['aluno']['pessoa']['nome_contato'];
                    
                    $itensLicoes = [];

                    $alunoDiario = $contrato['aluno']['alunoDiarios'];
                   
                    foreach ($alunoDiario as $licoes) {
                        $itensLicoes[$licoes['licao'][0]['descricao']]['lesson'] = $licoes['licao'][0]['descricao'];
                        $itensLicoes[$licoes['licao'][0]['descricao']]['data_licao'] = $licoes['turma_aula']['data_aula']->format('d/m');
                        $itensLicoes[$licoes['licao'][0]['descricao']]['instrutor'] = $licoes['funcionario']['apelido'];
                        $itensLicoes[$licoes['licao'][0]['descricao']]['presenca'] = $licoes['presenca'];
                        $itensLicoes[$licoes['licao'][0]['descricao']]['atividade_ca'] = $licoes['atividade_ca'];
                        $itensLicoes[$licoes['licao'][0]['descricao']]['atividade_ce'] = $licoes['atividade_ce'];             
                    }
                    $itens[$contrato['aluno']['id']]['licoes'] = $itensLicoes;

                    
                    $alunoAvaliacaos = $contrato['aluno']['alunoAvaliacaos'];
                    $alunoAvaliacaoConceituals = $contrato['aluno']['alunoAvaliacaoConceituals'];
                    
                   // $alunos = $contrato['aluno']['pessoa']['nome_contato'];
                    $alunos[$contrato['aluno']['id']] = [
                        'nome_aluno' => $contrato['aluno']['pessoa']['nome_contato'],
                        'alunoAvaliacaos' => $alunoAvaliacaos,
                        'alunoAvaliacaoConceituals' => $alunoAvaliacaoConceituals
                    ];
                    
                }
                
            }

            $chavesAlunosNumericos = array_keys($alunos);
            $valores = array_values($alunos);            
            $alunosSubstituidos = [];            
            for ($i = 0; $i < count($alunos); $i++) {
                $alunosSubstituidos[$i] = $valores[$i]; // Usa o valor original
            }
           $turmas[$turma['id']]['alunos'] = $alunosSubstituidos;

            $aulas = []; // Array para armazenar as aulas como chaves
    
            // Iterar sobre os alunos para organizar os dados por lição
            foreach ($itens as $aluno) {
                foreach ($aluno['licoes'] as $licao => $dadosLicao) {
                    if (!isset($aulas[$licao])) {
                        $aulas[$licao] = [];
                    }
                    
                    $dadosAluno = [
                        'nome' => $aluno['nome'],
                        'presenca' => $dadosLicao['presenca'],
                        'atividade_ca' => $dadosLicao['atividade_ca'],
                        'atividade_ce' => $dadosLicao['atividade_ce'],
                    ];
                    
                    $aulas[$licao]['lesson'] = $dadosLicao['lesson'];
                    $aulas[$licao]['date_lesson'] = $dadosLicao['data_licao'];
                    $aulas[$licao]['instrutor'] = $dadosLicao['instrutor'];
                    $aulas[$licao]['detalhes'][] = $dadosAluno;
                }
               
            }

            $chavesNumericas = array_keys($aulas);
            $valores = array_values($aulas);            
            $nomesSubstituidos = [];            
            for ($i = 0; $i < count($aulas); $i++) {
                $nomesSubstituidos[$i] = $valores[$i]; // Usa o valor original
            }
            
            $turmas[$turma['id']]['itens']     = $nomesSubstituidos;
          
        }
        
        $html = $this->renderView('relatorios/class_record/report.html', ["turmas" => $turmas]);

        echo $html;

        die;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/class_record_cronograma/imprimir",
     *     summary="Imprimir relatório class record cronograma",
     *     description="Imprimir o relatório class record cronograma da turma passada por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=true, nullable=true, description="Ids de turma", map=true)
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     *
     * @FOSRest\Get("/relatorios/class_record_cronograma/imprimir")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioClassRecordCronogramaImprimir(ParamFetcher $request)
    {
        $mensagemErro = "";
        $ids          = $request->get('turma');
        $franqueada   = $request->get('franqueada');

        $turmas = implode(",", $ids);

        $sql = "
                select	turma.id, turma.descricao as turma,
                semestre.descricao as semestre,
                turma_aula.data_aula, 
                licao.numero as numero_licao, 
                licao.descricao as descricao_licao, 
                sala.descricao as sala,
                pessoa.nome_contato as professor,
                horario.descricao as horario,
                livro.descricao as estagio,
                curso.descricao as curso,
                turma.data_inicio,
                turma.data_fim
                
        from turma 

        inner join turma_aula
            on turma.id = turma_aula.turma_id
        inner join licao
            on turma_aula.licao_id = licao.id

        inner join horario
            on turma.horario_id = horario.id

        left join sala_franqueada
            on turma.sala_franqueada_id = sala_franqueada.id
        inner join sala
            on sala_franqueada.sala_id = sala.id

        inner join semestre
            on turma.semestre_id = semestre.id

        inner join livro
            on turma.livro_id = livro.id	

        left join funcionario
            on turma.funcionario_id = funcionario.id
        inner join pessoa
            on funcionario.pessoa_id = pessoa.id
            
        inner join curso
            on turma.curso_id = curso.id
            
            where turma.id in ({$turmas})
            and turma.franqueada_id = {$franqueada} ";

        $connection = self::getManagerRegistry()->getConnection();

        $data = $connection->fetchAll($sql);



        $turmas = [];
        foreach ($ids as $id) {
            $itens = array_filter(
                $data,
                function ($item) use ($id) {
                    return $item['id'] == $id;
                }
            );

            if (count($itens) > 0) {
                // var_dump( $itens[0]);
                $first = array_values($itens)[0];

                $turmas[$id]['turma']   = $first['turma'];
                $turmas[$id]['estagio'] = $first['estagio'];

                $turmas[$id]['curso']       = $first['curso'];
                $turmas[$id]['horario']     = $first['horario'];
                $turmas[$id]['professor']   = $first['professor'];
                $turmas[$id]['data_inicio'] = $first['data_inicio'];
                $turmas[$id]['data_fim']    = $first['data_fim'];
                $turmas[$id]['sala']        = $first['sala'];

                // nos itens
                // $turmas[$id]['data_aula'] = $first['data_aula'];
                // $turmas[$id]['numero_licao'] = $first['numero_licao'];
                // $turmas[$id]['descricao_licao'] = $first['descricao_licao'];
                $turmas[$id]['itens'] = $itens;
            } //end if
        } //end foreach

        $html = $this->renderView('relatorios/class_record_cronograma/report.html', ["turmas" => $turmas]);

        echo $html;

        die;
    }

    // /**
        // *
        // * @SWG\Get(
        // *     path="/api/relatorios/class_record_cronograma",
        // *     summary="Imprimir relatório class record cronograma",
        // *     description="Imprimir o relatório class record cronograma da turma passada por parametro",
        // *     produces={"application/json"},
        // * @SWG\Response(
        // *         response="200",
        // *         description="Retorna o relatorio"
        // *     ),
        // * @SWG\Response(
        // *         response="400",
        // *         description="Ocorreu algum erro no servidor",
        // *     )
        // * )
        // *
        // * @FOSRest\QueryParam(name="turma",      strict=true, allowBlank=true, nullable=true, description="Ids de turma", map=true)
        // * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
        // *
        // * @FOSRest\Get("/relatorios/class_record_cronograma")
        // *
        // * @return \Symfony\Component\HttpFoundation\Response
        // */
        // public function relatorioClassRecordCronograma(ParamFetcher $request)
        // {
        // $mensagemErro = "";
        // $ids          = $request->get('turma');
        // $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/class-record-cronograma";
        // if (file_exists($diretorioArquivo) === false) {
        // mkdir($diretorioArquivo, 0777, true);
        // }
        // $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        // if (is_null($franqueada) === true) {
        // $mensagemErro = 'Franqueada não encontrada.';
        // }
        // $pdf      = new PDFMerger;
        // $arquivos = [];
        // foreach ($ids as $id) {
        // $parametros = [
        // 'nomeFranqueada' => $franqueada->getNome(),
        // 'logoInflux'     => $this->getInfluxPath(),
        // 'franqueada_id'  => $request->get('franqueada'),
        // 'turma_id'       => $id,
        // ];
        // $formato   = 'pdf';
        // $relatorio = 'ClassRecordCronograma.jasper';
        // $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        // $date          = (new \DateTime())->format('Ymd-Hi');
        // $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio=-$id-$date";
        // self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        // self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        // self::getJasperHelper()->setParametrosRelatorios($parametros);
        // self::getJasperHelper()->setFormatosDeSaida([$formato]);
        // self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);
        // if (empty($mensagemErro) === false) {
        // $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
        // $response->headers->set('Content-Type', 'text/html');
        // return $response;
        // }
        // $pdf->addPDF("$arquivoRelatorio.$formato");
        // $arquivos[] = "$arquivoRelatorio.$formato";
        // } //end foreach
        // $file = $pdf->merge("string");
        // foreach ($arquivos as $arquivo) {
        // unlink($arquivo);
        // }
        // return new PdfResponse($file, "classRecordCronograma.pdf");
    // }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/aniversariantes",
     *     summary="Imprimir relatórios de aniversariantes",
     *     description="Imprimir o relatório de aniversariantes dos parametros passados",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="mes",             strict=true, allowBlank=true, nullable=false, description="Mês atual, próximo mês, ou, os 12 meses")
     * @FOSRest\QueryParam(name="turma",           strict=true, allowBlank=true, nullable=true,  description="Id da Turma")
     * @FOSRest\QueryParam(name="situacao",        strict=true, allowBlank=true, nullable=true,  description="Situação da Pessoa")
     * @FOSRest\QueryParam(name="franqueada",      strict=true, allowBlank=true, nullable=true,  description="Id Franqueada")
     * @FOSRest\Get("/relatorios/aniversariantes")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAniversariantes(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        if (is_null($filtros["franqueada"]) === false) {
            $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
            if (is_null($franqueada) === true) {
                return new Response(json_encode('Franqueada não encontrada.'));
            }
        } else {
            return new Response(json_encode('Franqueada não encontrada.'));
        }        
        
        $response = $this->pessoaFacade->gerarDadosRelatorioAniversariantes($filtros);
        return new Response(json_encode($response));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/dados_aluno",
     *     summary="Imprimir relatório de informações de um aluno",
     *     description="Imprimir o relatório de informações do aluno passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="aluno", strict=true, allowBlank=true, nullable=false, description="Id aluno", requirements="\d+")
     *
     * @FOSRest\Get("/relatorios/dados_aluno")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioDadosAluno(ParamFetcher $request)
    {
        $mensagemErro     = "";
        $filtros          = $request->all();
        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/dados-aluno";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $whereClause = $this->alunoFacade->gerarDadosRelatorio($filtros);

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'root'           => $this->get('kernel')->getProjectDir(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato   = 'pdf';
        $relatorio = 'DadosAlunos.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/quantidade_alunos_turma",
     *     summary="Imprimir relatórios de quantidade de alunos por turma",
     *     description="Imprimir o relatório de quantidade de alunos por turma",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="detalhado", strict=true, allowBlank=true, nullable=false, default=0, description="Detalhado", requirements="[1|0]")
     *
     * @FOSRest\Get("/relatorios/quantidade_alunos_turma")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioQuantidadeAlunosTurma(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/quantidade-alunos-turma";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->contratoFacade->gerarDadosRelatorio($filtros, 1);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
            'detalhado'      => $request->get('detalhado'),
            'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
        ];

        $formato = 'pdf';

        $relatorio = 'QuantidadeAlunosTurma.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/atividade_extra_participantes",
     *     summary="Imprimir relatórios de participantes de uma atividade extra",
     *     description="Imprimir o relatório de participantes de uma atividade extra passada por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",      strict=true, allowBlank=true, nullable=true,  description="Id Franqueada")
     * @FOSRest\QueryParam(name="atividade_extra", strict=true, allowBlank=true, nullable=true,  description="Id da Atividade Extra")
     *
     * @FOSRest\Get("/relatorios/atividade_extra_participantes")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAtividadeExtraParticipantes(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/atividade-extra-participantes";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada'     => $franqueada->getNome(),
            'logoInflux'         => $this->getInfluxPath(),
            'franqueada_id'      => $request->get('franqueada'),
            'atividade_extra_id' => $request->get('atividade_extra'),
        ];

        $formato = 'pdf';

        $relatorio     = 'AtividadeExtraParticipantes.jasper';
        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/atividade_extra",
     *     summary="Imprimir relatório de informações de atividade extra",
     *     description="Imprimir o relatório de informações de atividade extra passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="tipo",         strict=true, allowBlank=true, nullable=true, description="Tipo de atividade extra")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data final")
     * @FOSRest\QueryParam(name="situacao",     strict=true, allowBlank=true, nullable=true, description="Situação atividade")
     * @FOSRest\QueryParam(name="aluno",        strict=true, allowBlank=true, nullable=true, description="Id Aluno")
     *
     * @FOSRest\Get("/relatorios/atividade_extra")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAtividadeExtra(ParamFetcher $request)
    {
        $mensagemErro     = "";
        $filtros          = $request->all();
        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/atividade-extra";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $whereClause = $this->atividadeExtraFacade->gerarDadosRelatorio($filtros);

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato   = 'pdf';
        $relatorio = 'AtividadesExtras.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/medias_turmas",
     *     summary="Imprimir relatório de medias por turmas",
     *     description="Imprimir o relatório de medias por turmas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="detalhado",  strict=true, allowBlank=true, nullable=false, default=0, description="Detalhado", requirements="[1|0]")
     *
     * @FOSRest\Get("/relatorios/medias_turmas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMediasTurmas(ParamFetcher $request)
    {
        $mensagemErro     = "";
        $filtros          = $request->all();
        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/medias-turmas";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'franqueada_id'  => $request->get('franqueada'),
        ];

        $formato = 'pdf';
        if ($filtros[ConstanteParametros::CHAVE_DETALHADO] === "0") {
            $relatorio = 'MediasTurmas_resumido.jasper';
        } else {
            $relatorio = 'MediasTurmas_detalhado.jasper';
        }

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/matriculas_vendas",
     *     summary="Imprimir relatório de de matriculas(vendas) realizadas",
     *     description="Imprimir o relatório de matriculas(vendas) realizadas entre periodos",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data Final")
     * @FOSRest\QueryParam(name="tipo_lead",    strict=true, allowBlank=true, nullable=true, description="Tipo Matrícula")
     * @FOSRest\QueryParam(name="funcionario",  strict=true, allowBlank=true, nullable=true, description="Id Funcionario")
     *
     * @FOSRest\Get("/relatorios/matriculas_vendas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMatriculasVendas(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/matriculas-vendas";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->contratoFacade->gerarDadosRelatorio($filtros, 2);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato   = 'pdf';
        $relatorio = 'Matriculas_vendas.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/turmas_existentes",
     *     summary="Imprimir relatórios de turmas existentes",
     *     description="Imprimir o relatório de turmas existentes dos parametros passados",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio de turmas existentes"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",      strict=true, allowBlank=true, nullable=false,  description="Id da Franqueada")
     * @FOSRest\QueryParam(name="curso",      strict=true, allowBlank=true, nullable=true,  description="Id do Curso")
     * @FOSRest\QueryParam(name="livro",      strict=true, allowBlank=true, nullable=true,  description="Id do Livro")
     * @FOSRest\QueryParam(name="situacao_turma",   strict=true, allowBlank=true, nullable=true,  description="Situação da Turma")
     * @FOSRest\QueryParam(name="modalidade_turma", strict=true, allowBlank=true, nullable=true,  description="Id da Modalidade")
     * @FOSRest\QueryParam(name="turma", strict=true, allowBlank=true, nullable=true,  description="Id da Turma")
     * @FOSRest\QueryParam(name="turnos", strict=true, allowBlank=true, nullable=true,  description="Turno em que ocorre o curso")
     * @FOSRest\QueryParam(name="dia_semana", strict=true, allowBlank=true, nullable=true,  description="Dia da semana em que ocorre o curso")
     * @FOSRest\QueryParam(name="instrutor", strict=true, allowBlank=true, nullable=true,  description="Instrutor/Professor da Turma")
     *
     * @FOSRest\Get("/relatorios/turmas_existentes")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioTurmasExistentes(ParamFetcher $request)
    {
        try{
            $filtros      = $request->all();  
            $result = $this->turmaFacade->gerarDadosRelatorioTurmasExistente($filtros);
            return new Response(json_encode($result));

        }catch(\Exception $e){
            return new Response($e->getMessage(), 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/matriculas_perdidas",
     *     summary="Imprimir relatórios de matriculas perdidas",
     *     description="Imprimir o relatório de matriculas perdidas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data Final")
     * 
     * @FOSRest\Get("/relatorios/matriculas_perdidas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMatriculasPerdidas(ParamFetcher $request)
    {
        $filtros      = $request->all();
        $data = $this->interessadoFacade->gerarDadosRelatorio($filtros, '2');
        return new Response(json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/aulas_ocorridas",
     *     summary="Imprimir relatório de aulas ocorridas",
     *     description="Imprimir o relatório de aulas ocorridas dentro de um periodo",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data Final")
     * @FOSRest\QueryParam(name="curso",        strict=true, allowBlank=true, nullable=true, description="Id do curso")
     * @FOSRest\QueryParam(name="livro",        strict=true, allowBlank=true, nullable=true, description="Id do Livro")
     * @FOSRest\QueryParam(name="idioma",       strict=true, allowBlank=true, nullable=true, description="Id do Idioma")
     * @FOSRest\QueryParam(name="turma",        strict=true, allowBlank=true, nullable=true, description="Id da Turma")
     * @FOSRest\QueryParam(name="instrutor",  strict=true, allowBlank=true, nullable=true, description="Id do Funcionario")
     * @FOSRest\QueryParam(name="finalizada",     strict=true, allowBlank=true, nullable=true, description="turma_aula finalizada(1) ou não(0)", default="1", requirements="(0|1)")
     * @FOSRest\QueryParam(name="situacao",     strict=true, allowBlank=true, nullable=true, description="Situacao da Aula ENCERRADA|ABERTA|FORMAÇÃO", default="ABE", requirements="(ENC|ABE|FOR)")
     * @FOSRest\QueryParam(name="modalidade_turma",     strict=true, allowBlank=true, nullable=true, description="Id da modalidade turma")
     *
     * @FOSRest\Get("/relatorios/aulas_ocorridas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAulasOcorridas(ParamFetcher $request)
    {
        $mensagemErro     = "";
        $filtros          = $request->all();

        $response = $this->turmaAulaFacade->gerarDadosRelatorio($filtros);
        return new Response(json_encode($response));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/notas_frequencia",
     *     summary="Imprimir relatórios de notas e frequencia de alunos por turma",
     *     description="Imprimir o relatório de notas e frequencia de alunos por turma",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma",                 strict=true, allowBlank=true, nullable=true, description="Id Turma")
     * @FOSRest\QueryParam(name="funcionario",           strict=true, allowBlank=true, nullable=true, description="Id Funcionário")
     * @FOSRest\QueryParam(name="aluno",                 strict=true, allowBlank=true, nullable=true, description="Id Aluno")
     * @FOSRest\QueryParam(name="livro",                 strict=true, allowBlank=true, nullable=true, description="Id Livro")
     * @FOSRest\QueryParam(name="classificacao_aluno",   strict=true, allowBlank=true, nullable=true, description="Classificação do Aluno")
     * @FOSRest\QueryParam(name="quantidade_faltas_de",  strict=true, allowBlank=true, nullable=true, description="Número de faltas De")
     * @FOSRest\QueryParam(name="quantidade_faltas_ate", strict=true, allowBlank=true, nullable=true, description="Número de faltas Até")
     *
     * @FOSRest\Get("/relatorios/notas_frequencia")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioNotasFrequencias(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/notas-frequencia";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $whereClause = $this->alunoAvaliacaoFacade->gerarDadosRelatorio($filtros);

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
            'clausulaWhere'  => $whereClause,
        ];

        $formato = 'pdf';

        $relatorio = 'NotasFrequencias.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/bolsas_percentuais_desconto",
     *     summary="Imprimir relatórios de quantidades de alunos por percentuais",
     *     description="Imprimir o relatório de quantidades de alunos por percentuais",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",      strict=true, allowBlank=false, nullable=false, description="Id Franqueada")
     * @FOSRest\QueryParam(name="semestre",        strict=true, allowBlank=false, nullable=false, description="Id Semestre")
     * @FOSRest\QueryParam(name="situacao",        strict=true, allowBlank=false, nullable=false, description="Situação do Aluno")
     * @FOSRest\QueryParam(name="forma_pagamento", strict=true, allowBlank=true,  nullable=true,  description="Forma de Pagamento")
     * @FOSRest\QueryParam(name="tipo_relatorio",  strict=true, allowBlank=false, nullable=false, description="Comparar Semestres Anteriores", default="0", requirements="(0|1)")
     *
     * @FOSRest\Get("/relatorios/bolsas_percentuais_desconto")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioBolsasPercentuaisDesconto(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/bolsas-percentuais-desconto";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $formato = 'pdf';

        if ($filtros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO] === null) {
            $forma_pagamento = "";
        } else {
            $forma_pagamento = $filtros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO];
        }

        if ((int) $request->get('tipo_relatorio') === 0) {
            $parametros = [
                'nomeFranqueada'     => $franqueada->getNome(),
                'logoInflux'         => $this->getInfluxPath(),
                'franqueada_id'      => $request->get('franqueada'),
                'subreportsPath'     => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
                'semestre_id'        => $request->get('semestre'),
                'situacao_aluno'     => $request->get('situacao'),
                'forma_pagamento_id' => $forma_pagamento,
            ];
            $relatorio  = 'BolsasPercentuaisDesconto.jasper';
        } else {
            $parametros = [
                'nomeFranqueada' => $franqueada->getNome(),
                'logoInflux'     => $this->getInfluxPath(),
                'franqueada_id'  => $request->get('franqueada'),
                'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
                'semestre_id'    => $request->get('semestre'),
            ];
            $relatorio  = 'BolsasPercentuaisDescontoComparativo.jasper';
        } //end if

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);

        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/matriculas_renovar",
     *     summary="Relatório de matriculas a serem renovadas",
     *     description="Relatório de matriculas a serem renovadas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="tipo_turma",       strict=true, allowBlank=true,  nullable=true)
     * @FOSRest\QueryParam(name="orderBy",          strict=true, allowBlank=true,  nullable=true)
     * @FOSRest\QueryParam(name="orderDesc",        strict=true, allowBlank=true,  nullable=true)
     *
     * @FOSRest\Get("/relatorios/matriculas_renovar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMatriculasRenovar(ParamFetcher $request)
    {
        $filtros = $request->all();

        if (is_null($filtros['franqueada'])) {
            return new Response('Franqueada não encontrada', 500);
        }

        $data = $this->alunoFacade->relatorioMatriculasRenovar($filtros);
        return new Response(json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/historico_aluno",
     *     summary="Imprimir relatório de histórico do aluno",
     *     description="Imprimir o relatório de histórico do aluno passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="aluno",      strict=true, allowBlank=true, nullable=true, description="Id Aluno")
     *
     * @FOSRest\Get("/relatorios/historico_aluno")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioHistoricoAluno(ParamFetcher $request)
    {
        $mensagemErro = "";
        $filtros      = $request->all();

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/historico-aluno";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'franqueada_id'  => $request->get('franqueada'),
            'aluno_id'       => $request->get('aluno'),
            'subreportsPath' => $this->get('kernel')->getProjectDir() . '/src/Reports/jasper',
        ];

        $formato = 'pdf';

        $relatorio     = 'HistoricoAluno.jasper';
        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/itens_estoque",
     *     summary="Imprimir relatório de itens de estoque",
     *     description="Imprimir o relatório de itens de estoque passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=true, nullable=true, description="Id Franqueada")
     * @FOSRest\QueryParam(name="descricao",            strict=true, allowBlank=true, nullable=true, description="Descrição do Item")
     * @FOSRest\QueryParam(name="valor_compra_inicial", strict=true, allowBlank=true, nullable=true, description="Valor de Compra De")
     * @FOSRest\QueryParam(name="valor_compra_final",   strict=true, allowBlank=true, nullable=true, description="Valor de Compra Até")
     * @FOSRest\QueryParam(name="valor_venda_inicial",  strict=true, allowBlank=true, nullable=true, description="Valor Venda De")
     * @FOSRest\QueryParam(name="valor_venda_final",    strict=true, allowBlank=true, nullable=true, description="Valor Venda até")
     * @FOSRest\QueryParam(name="situacao",             strict=true, allowBlank=true, nullable=false, description="Apenas abaixo do estoque mínimo", default="0", requirements="(0|1)")
     *
     * @FOSRest\Get("/relatorios/itens_estoque")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioItensEstoque(ParamFetcher $request)
    {
        $mensagemErro     = "";
        $filtros          = $request->all();
        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/itens-estoque";

        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $whereClause = $this->itemFacade->gerarDadosRelatorio($filtros);

        $parametros = [
            'nomeFranqueada' => $franqueada->getNome(),
            'logoInflux'     => $this->getInfluxPath(),
            'clausulaWhere'  => $whereClause,
        ];

        $formato   = 'pdf';
        $relatorio = 'ItensEstoque.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/saldo_horas_personal",
     *     summary="Imprimir relatório de balancete financeiro",
     *     description="Imprimir o relatório de balancete financeiro passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=false, nullable=false, description="Id Franqueada")
     * @FOSRest\QueryParam(name="aluno",        strict=true, allowBlank=true, nullable=true, description="Id do Aluno")
     * @FOSRest\QueryParam(name="livro",        strict=true, allowBlank=true, nullable=true, description="Id do Livro")
     * @FOSRest\QueryParam(name="data_inicial", strict=true, allowBlank=true, nullable=true, description="Data Inicial")
     * @FOSRest\QueryParam(name="data_final",   strict=true, allowBlank=true, nullable=true, description="Data Final")
     *
     * @FOSRest\Get("/relatorios/saldo_horas_personal")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioSaldoHorasPersonal(ParamFetcher $request)
    {
        $mensagemErro = "";
        $parametros   = $request->all();

        $aluno       = $this->setaParametro($parametros[ConstanteParametros::CHAVE_ALUNO]);
        $livro       = $this->setaParametro($parametros[ConstanteParametros::CHAVE_LIVRO]);
        $dataInicial = $this->setaParametro($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
        $dataFinal   = $this->setaParametro($parametros[ConstanteParametros::CHAVE_DATA_FINAL]);

        $diretorioArquivo = $this->container->getParameter("kernel.project_dir") . "/public/relatorios/saldo-horas-personal";
        if (file_exists($diretorioArquivo) === false) {
            mkdir($diretorioArquivo, 0777, true);
        }

        $franqueada = $this->franqueadaRepository->find(VariaveisCompartilhadas::$franqueadaID);
        if (is_null($franqueada) === true) {
            $mensagemErro = 'Franqueada não encontrada.';
        }

        $parametros = [
            'nomeFranqueada'    => $franqueada->getNome(),
            'logoInflux'        => $this->getInfluxPath(),
            'franqueada_id'     => $request->get('franqueada'),
            'aluno_id'          => $aluno,
            'livro_id'          => $livro,
            'data_contrato_de'  => $dataInicial,
            'data_contrato_ate' => $dataFinal,
        ];

        $formato   = 'pdf';
        $relatorio = 'SaldoHorasPersonal.jasper';

        $nomeRelatorio = str_replace('.jasper', '', $relatorio);
        $date          = (new \DateTime())->format('Ymd-Hi');
        $arquivoRelatorio = "$diretorioArquivo/$nomeRelatorio-$date";
        self::getJasperHelper()->setRelatoriosGerados($arquivoRelatorio);
        self::getJasperHelper()->setConexaoBanco(FunctionHelper::getConfigBanco());
        self::getJasperHelper()->setParametrosRelatorios($parametros);
        self::getJasperHelper()->setFormatosDeSaida([$formato]);
        self::getJasperHelper()->processaRelatorio($relatorio, $mensagemErro);

        if (empty($mensagemErro) === false) {
            $response = new Response("<h2>Houve um erro</h2><pre>$mensagemErro</pre>");
            $response->headers->set('Content-Type', 'text/html');
            return $response;
        }

        $file = $this->file("$arquivoRelatorio.$formato", "$nomeRelatorio-$date.$formato");
        $file->deleteFileAfterSend(true);

        return $file;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/descontos",
     *     summary="Imprimir relatórios de descontos HTML",
     *     description="Imprimir o relatório de descontos passado por parametro",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="compararSemestre",         strict=true, allowBlank=true, nullable=true, description="Comparar Semestre")
     * @FOSRest\QueryParam(name="detalhesAluno",            strict=true, allowBlank=true, nullable=true, description="Detalhes dos Alunos")
     * @FOSRest\QueryParam(name="excel",                    strict=true, allowBlank=true, nullable=false, default=false, description="Exportar para Excel")
     * @FOSRest\QueryParam(name="formaPagamento",           strict=true, allowBlank=true, nullable=true, description="Formas de Pagamento")
     * @FOSRest\QueryParam(name="modalidade",               strict=true, allowBlank=true, nullable=true, description="Modalidade")
     * @FOSRest\QueryParam(name="semestre",                 strict=true, allowBlank=true, nullable=true, description="Semestre de Busca")
     * @FOSRest\QueryParam(name="situacao",                 strict=true, allowBlank=true, nullable=true, description="Situação dos Alunos")
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=false, description="Franqueada")
     *
     * @FOSRest\View
     * @FOSRest\Get("/relatorios/descontos")
     */
    public function relatorioDescontosHtml(
        ParamFetcher $request,
        AlunoFacade $alunoFacade
        ) {
        $filtros = $request->all();
        $data = $alunoFacade->gerarDadosRelatorioDescontos($filtros);
        return new Response(json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/fluxocaixa",
     *     summary="Relatórios Fluxo de Caixa",
     *     description="Relatório Fluxo de Caixa",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                   strict=true, allowBlank=false, nullable=false, description="Franqueada")
     * @FOSRest\QueryParam(name="situacao",                     strict=true, allowBlank=true, nullable=true, description="Situacao da entrada/saída")
     * @FOSRest\QueryParam(name="forma_pagamento",              strict=true, allowBlank=true, nullable=true, description="Forma de Pagamento")
     * @FOSRest\QueryParam(name="contato",                      strict=true, allowBlank=true, nullable=true, description="Fornecedor/Cliente")
     * @FOSRest\QueryParam(name="conta",                        strict=true, allowBlank=true, nullable=true, description="Conta")
     * @FOSRest\QueryParam(name="data_inicial_vencimento",      strict=true, allowBlank=true, nullable=true, description="Vencimento a partir da data inicial")
     * @FOSRest\QueryParam(name="data_final_vencimento",        strict=true, allowBlank=true, nullable=true, description="Vencimento até a data final")
     * @FOSRest\QueryParam(name="data_inicial_pagamento",       strict=true, allowBlank=true, nullable=true, description="Pagamento a partir da data inicial")
     * @FOSRest\QueryParam(name="data_final_pagamento",         strict=true, allowBlank=true, nullable=true, description="Pagamento até a data final")
     *
     * @FOSRest\View
     * @FOSRest\Get("/relatorios/fluxocaixa")
     */
    public function relatorioFluxoCaixa(ParamFetcher $request, ContasPagarRepository $contasPagarRepository) {
        $filtros = $request->all();
        $data = $contasPagarRepository->gerarDadosRelatorioFluxoCaixa($filtros);
        return new Response(json_encode($data));
    }
    
    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/matriculas",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="idioma",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="curso",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="turma",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="situacaoMatricula",    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contrato",        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="semestre",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="aluno",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="matricula",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="rematricula",          strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="alteraNiveis",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="transferencias",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="recisoes",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="cancelamentos",        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="transfUnidadeEntrada", strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="transfUnidadeSaida",   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="trancamentos",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="destrancamentos",      strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="encerramentos",        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="orderBy",              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="orderDesc",            strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/matriculas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMatriculas(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->contratoFacade->gerarDadosRelatorioMatriculas($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/retencao-alunos",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",           strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="instrutor",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="turma",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="modalidade_turma",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",           strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/retencao-alunos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioRetencaoAlunos(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->contratoFacade->gerarDadosRelatorioRetencaoAlunos($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/visitas",
     *     summary="Relatórios Fluxo de Caixa",
     *     description="Relatório Fluxo de Caixa",
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="consultor",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",                   strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\View
     * @FOSRest\Get("/relatorios/visitas")
     */
    public function relatoriovisitas(ParamFetcher $request, InteressadoRepository $interessadoRepository) {
        $filtros = $request->all();
        $data = $interessadoRepository->buscarRelatorioVisitas($filtros);
        return new Response(json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/controle-material-didatico",
     *     summary="Relatórios Fluxo de Caixa",
     *     description="Relatório Fluxo de Caixa",
     *      *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",                   strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="semestre",                     strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="saldo_de",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="saldo_ate",                    strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\View
     * @FOSRest\Get("/relatorios/controle-material-didatico")
     */
    public function relatorioControleMaterialDidatico(ParamFetcher $request) {
        $filtros = $request->all();
        $data = $this->itemContaReceberFacade->montarRelatorioControleMaterialDidatico($filtros);
        return new Response(json_encode($data));
    }

    /**
     * 
     * @SWG\Get(
     *     path="/api/relatorios/pedido-material-didatico",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="turma",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="aluno",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="franqueada",                strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/pedido-material-didatico")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
     public function relatorioPedidoMaterialDidatico(ParamFetcher $request, ItemContaReceberFacade $itemContaReceberFacade)
     {
        $filtros = $request->all();
        $data = $itemContaReceberFacade->gerarDadosRelatorioPedidoMaterialDidatico($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/saldo-horas",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="modalidade_turma",         strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/saldo-horas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioSaldoHoras(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->contratoFacade->gerarDadosRelatorioSaldoHoras($filtros);
        return new Response(Json_encode($data));
    }

    

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/retorno-consultor",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="data_inicial",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="consultor",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=false)
     *
     * @FOSRest\Get("/relatorios/retorno-consultor")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioRetornoConsultor(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->interessadoFacade->gerarDadosRelatorioRetornoConsultor($filtros);
        return new Response(Json_encode($data));
    }
    
    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/alunos-por-turma",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="turma",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="horario",          strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="sala",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="instrutor",        strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/alunos-por-turma")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAlunosPorTurma(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->turmaFacade->gerarDadosRelatorioAlunosPorTurma($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/atividade-extra",
     *     summary="Imprimir relatório de atividades extras",
     *     description="Imprimir o relatório de atividades extras",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="atividade_extra",  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="situacao",         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="aluno",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",       strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/atividade-extra")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAtividadesExtras(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->atividadeExtraFacade->gerarDadosRelatorioAtividadesExtras($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/mapa-sala-turma",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="turma",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="sala",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",               strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/mapa-sala-turma")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMapaSalaTurma(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->turmaFacade->gerarDadosRelatorioMapaSalaTurma($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/itens-de-estoque",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="item",                             strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="estoque_negativo",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="situacao",                      strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="valor_venda_de",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="valor_venda_ate",                  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="valor_custo_de",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="valor_custo_ate",                  strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/itens-de-estoque")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioItensDeEstoque(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->itemFacade->buscarDadosRelatorioItensDeEstoque($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/contatos",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="data_inicial",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_prospeccao",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="consultor",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_lead",                        strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/contatos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioContato(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->interessadoFacade->buscarDadosRelatorioContato($filtros);
        return new Response(Json_encode($data));
    }
    
    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/estoque",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="item",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",            strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/estoque")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioEstoque(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->itemFacade->gerarDadosRelatorioEstoque($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/ocorrencias",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="situacao",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_contato_inicial",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_contato_final",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_ocorrencia",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="funcionario_responsavel",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="aluno",       strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/ocorrencias")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioOcorrencias(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->ocorrenciaAcademicaFacade->buscarDadosRelatorioOcorrencias($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/negociacao-convenio",
     *     summary="Imprimir relatório de negociação de convenio",
     *     description="Imprimir o relatório de negociação de convenio",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="situacao",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="segmento_empresa_convenio",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="abrangencia_nacional",       strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/negociacao-convenio")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioNegociacaoConvenio(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->convenioFacade->buscarDadosRelatorioNegociacaoConvenios($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/compromisso-aprendizado",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",          strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="aluno",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="curso",               strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="semestre",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="professor",           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",          strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/compromisso-aprendizado")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioCompromissoAprendizado(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->alunoFacade->buscarDadosRelatorioCompromissoAprendizado($filtros);
        return new Response(Json_encode($data));
    }

    /**
     * 
     * @SWG\Get(
     *     path="/api/relatorios/servicos-solicitados",
     *     summary="Imprimir relatórios de serviços solicitados",
     *     description="Imprimir o relatório de serviços solicitados",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="aluno",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_item",                       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                 strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/servicos-solicitados")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioServicosSolicitados(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->itemFacade->buscarDadosRelatorioServicosSolicitados($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/consulta-desistencias",
     *     summary="Imprimir relatórios de consulta de desistencias",
     *     description="Imprimir o relatório de consulta de desistencias",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="consultor_responsavel_funcionario",  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="semestre",  strict=true, allowBlank=true, nullable=true)
     * 
     * @FOSRest\Get("/relatorios/consulta-desistencias")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioConsultaDesistencias(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->interessadoFacade->buscarDadosRelatorioConsultaDesistencias($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/valores-turma",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=true)
     * @FOSRest\QueryParam(name="turma",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="curso",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="livro",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="idioma",                   strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="situacao_contrato",        strict=true, allowBlank=true, nullable=true,  description="Situações do Contrato")
     * 
     * 
     * @FOSRest\QueryParam(name="planoMensalidade",         strict=true, allowBlank=true, nullable=true, default="1")
     * @FOSRest\QueryParam(name="materialDidatico",         strict=true, allowBlank=true, nullable=true, default="1") 
     * @FOSRest\QueryParam(name="order",                    strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                  strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\Get("/relatorios/valores-turma")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioValoresTurma(ParamFetcher $request)
    {
        try{
             
            $filtros = $request->all();
             $data = $this->contratoFacade->gerarDadosRelatorioValoresTurma($filtros);
             
             return new Response(json_encode($data));

        }catch(\Exception $e){
            return new Response($e->getMessage(), 500);
        }
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/matriculas-venda",
     *     summary="Imprimir relatórios de matriculas-venda",
     *     description="Imprimir o relatório de matriculas-venda",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="responsavel_venda_funcionario",  strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",                    strict=true, allowBlank=true, nullable=true)
     * 
     * @FOSRest\Get("/relatorios/matriculas-venda")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioMatriculaVenda(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->contratoFacade->gerarDadosRelatorioMatriculasVendas($filtros);

        return new Response(Json_encode($data));
    }
   
   /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/diario-classe",
     *     summary="Imprimir relatórios de Diario de clase detalhado",
     *     description="Imprimir relatórios de Diario de clase detalhado",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=true, allowBlank=false, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="instrutor",        strict=true, allowBlank=true, nullable=true, description="Id do instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="turma",            strict=true, allowBlank=true, nullable=true, description="Id do turma", requirements="\d+")
     * 
     * @FOSRest\Get("/relatorios/diario-classe")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioDiarioClasse(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->turmaAulaFacade->gerarDadosRelatorioDiarioClasse($filtros);

        return new Response(Json_encode($data));
    }
   


       /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/instrutores_disponiveis",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     * @FOSRest\QueryParam(name="franqueada",          strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="instrutor",           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",          strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/instrutores_disponiveis")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioInstrutoresDisponiveis(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->funcionarioFacade->buscaDisponibilidadeInstrutores($filtros, $mensagemErro);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/aulas-desmarcadas",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",               strict=true, allowBlank=false, nullable=true)
     * @FOSRest\QueryParam(name="aluno",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_inicial",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\Get("/relatorios/aulas-desmarcadas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioAulasDesmarcadas(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->agendamentoPersonalFacade->gerarDadosRelatorioAulasDesmarcadas($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/consulta-conversao",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="data_inicial",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="consultor",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_prospeccao",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_lead",                        strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/consulta-conversao")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioConsultaConversao(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->interessadoFacade->buscarDadosRelatorioConsultaConversao($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/prospeccao",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="data_inicial",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_final",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="grau_interesse",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="workflow",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_contato",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="tipo_prospeccao",                     strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/prospeccao")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioProspeccao(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->interessadoFacade->buscarDadosRelatorioProspeccao($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/dados-cadastro",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="situacao",                         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_cadastro_inicial",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_cadastro_final",              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="classificacao_aluno",              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="consultor",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="atendente",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="bairro",                           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="curso",                            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="idade_minima",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="idade_maxima",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="telefone_preferencial",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="endereco",                         strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="cep",                              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="cidade",                           strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="estado_uf",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_aniversario_de",              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="escolaridade",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="telefone_contato",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="telefone_profissional",            strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="codigo_matricula",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="observacao",                       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="pessoa_sexo",                      strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="responsavel_didatico_pessoa",      strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="responsavel_financeiro_pessoa",    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="cpf",                              strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="numero_identidade",                strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="pessoa_id",                        strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/dados-cadastro")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioDadosCadastro(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->pessoaFacade->buscarDadosRelatorioDadosCadastro($filtros);
        return new Response(Json_encode($data));
    }


    
    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/saida-estoque",
     *     summary="Imprimir relatórios de matriculas",
     *     description="Imprimir o relatório de matriculas",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                       strict=true, allowBlank=false, nullable=false)
     * @FOSRest\QueryParam(name="data_saida_de",                     strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_saida_ate",                       strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_entrega_de",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="data_entrega_ate",                 strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="situacao_entrega",                    strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="aluno",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="empresa",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="item",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="usuario",                        strict=true, allowBlank=true, nullable=true)
     * @FOSRest\QueryParam(name="apelido",                        strict=true, allowBlank=true, nullable=true)
     *
     * @FOSRest\Get("/relatorios/saida-estoque")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioSaidaEstoque(ParamFetcher $request)
    {
        $filtros = $request->all();
        $data = $this->contaReceberFacade->buscarDadosRelatorioSaidaEstoque($filtros);
        return new Response(Json_encode($data));
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/relatorios/followup_comercial",
     *     summary="Relatorio followup_comercial",
     *     description="Retorna um JSON com o relatorio de followup_comercial, seguindo os filtros passados",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o relatorio dos followup_comercial"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",                        strict=false, allowBlank=false,  description="Id da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="data_cadastro_de",                  strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_cadastro_ate",                 strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="data_proximo_contato_de",           strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_proximo_contato_ate",          strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="data_termino_contrato_de",          strict=false, allowBlank=false,  description="Data de cadastro de")
     * @FOSRest\QueryParam(name="data_termino_contrato_ate",         strict=false, allowBlank=false,  description="Data de cadastro ate")
     * @FOSRest\QueryParam(name="situacao_interessado",              strict=false, allowBlank=false,  description="Situações do Interessado", map=true)
     * @FOSRest\QueryParam(name="grau_interesse",                    strict=false, allowBlank=false,  description="Graus de Interesse", map=true)
     * @FOSRest\QueryParam(name="situacao_contrato",                 strict=false, allowBlank=false,  description="Situações do Contrato", map=true)
     * @FOSRest\QueryParam(name="situacao_aluno",                    strict=false, allowBlank=false,  description="Situações do Aluno", map=true)
     * @FOSRest\QueryParam(name="interessado",                       strict=false, allowBlank=false,  description="Id do interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="conveniado",                        strict=false, allowBlank=false,  description="Id do interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_lead",                         strict=false, allowBlank=false,  description="Tipo Lead")
     * @FOSRest\QueryParam(name="consultor_responsavel_funcionario", strict=false, allowBlank=false, description="Id do funcionario do proximo atendimento Interessado", requirements="\d+")
     * @FOSRest\QueryParam(name="responsavel_venda_funcionario",     strict=false, allowBlank=false, description="Id do funcionario do responsavel pela venda", requirements="\d+")
     * @FOSRest\QueryParam(name="tipo_followup_selecionado",         strict=false, allowBlank=false, description="Id do funcionario", requirements="(0|1|2|3)")
     *
     * @FOSRest\Get("/relatorios/followup_comercial")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function relatorioFollowUp(ParamFetcher $request)
    {
        try{
            $filtros = $request->all();
            $data = $this->followupComercialFacade->buscarDadosRelatorioFollowupComercial($filtros);    
            return new Response(Json_encode($data));
        }catch(\Exception $e){
            return new Response(json_encode($e->getMessage(), $e->getCode()));
        }
    }
}