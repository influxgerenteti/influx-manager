<?php

namespace App\Controller\Principal\PagamentoFuncionario;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\PagamentoFuncionarioFacade;
use App\Helper\ConstanteParametros;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\ContaPagarFacade;
use App\Facade\Principal\TituloPagarFacade;
use App\Facade\Principal\PlanoContasContaPagarFacade;
use App\Facade\Principal\MovimentoContaFacade;
use App\Helper\LockHelper;
use App\Facade\Principal\ChequeFacade;
use App\Facade\Principal\FuncionarioFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class PagamentoFuncionarioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\PagamentoFuncionarioFacade
     */
    private $pagamentoFuncionarioFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaPagarFacade
     */
    private $contaPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\PlanoContasContaPagarFacade
     */
    private $planoContasContaPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\MovimentoContaFacade
     */
    private $movimentoContaFacade;

    /**
     *
     * @var \App\Facade\Principal\ChequeFacade
     */
    private $chequeFacade;

    /**
     *
     * @var \App\Facade\Principal\FuncionarioFacade
     */
    private $funcionarioFacade;

    /**
     *
     * @var \App\Helper\LockHelper;
     */
    private $lockHelper;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->pagamentoFuncionarioFacade  = new PagamentoFuncionarioFacade(self::getManagerRegistry());
        $this->contaPagarFacade            = new ContaPagarFacade(self::getManagerRegistry());
        $this->tituloPagarFacade           = new TituloPagarFacade(self::getManagerRegistry());
        $this->planoContasContaPagarFacade = new PlanoContasContaPagarFacade(self::getManagerRegistry());
        $this->movimentoContaFacade        = new MovimentoContaFacade(self::getManagerRegistry());
        $this->funcionarioFacade           = new FuncionarioFacade(self::getManagerRegistry());
        $this->chequeFacade = new ChequeFacade(self::getManagerRegistry());
        $this->lockHelper   = new LockHelper();
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pagamento_funcionario/listar",
     *     summary="Listar pagamento_funcionario",
     *     description="Lista as pagamento_funcionario do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os pagamento_funcionario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",           strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",      strict=true, nullable=false, description="ID do professor", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade_turma", strict=false, nullable=true, description="ID da modalidade turma")
     * @FOSRest\QueryParam(name="data_inicio",      strict=false, nullable=true, description="Data Inicio")
     * @FOSRest\QueryParam(name="data_fim",         strict=false, nullable=true, description="Data Fim")
     *
     * @FOSRest\Get("/pagamento_funcionario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->funcionarioFacade->consultaAulasParaPagamento($parametros);
        if (is_null($resultados['erro']) === false) {
            return ResponseFactory::badRequest([], $resultados['erro']);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/pagamento_funcionario/criar",
     *     summary="Listar pagamento_funcionario",
     *     description="Lista as pagamento_funcionario do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os pagamento_funcionario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false, description="ID do professor", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma", strict=false, nullable=true, description="ID da modalidade turma")
     * @FOSRest\RequestParam(name="data_inicio",      strict=false, nullable=true, description="Data Inicio")
     * @FOSRest\RequestParam(name="data_fim",         strict=false, nullable=true, description="Data Fim")
     *
     * @FOSRest\Post("/pagamento_funcionario/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $requestParam, Request $request)
    {
        $parametros = $requestParam->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $request->headers->get('Authorization-User-ID');
        $resultados = $this->funcionarioFacade->geraContaPagarFuncionario($parametros, $mensagem);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], 'Pagamento para instrutor efetuado');
    }


}
