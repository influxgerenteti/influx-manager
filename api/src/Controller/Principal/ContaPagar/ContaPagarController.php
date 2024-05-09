<?php

namespace App\Controller\Principal\ContaPagar;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use App\Factory\ResponseFactory;
use App\Helper\ConstanteParametros;
use App\Helper\LockHelper;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\ContaPagarFacade;
use App\Facade\Principal\TituloPagarFacade;
use App\Facade\Principal\PlanoContasContaPagarFacade;
use App\Facade\Principal\MovimentoContaFacade;
use App\Facade\Principal\ChequeFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class ContaPagarController extends GenericController
{


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
        $this->contaPagarFacade            = new ContaPagarFacade(self::getManagerRegistry());
        $this->tituloPagarFacade           = new TituloPagarFacade(self::getManagerRegistry());
        $this->planoContasContaPagarFacade = new PlanoContasContaPagarFacade(self::getManagerRegistry());
        $this->movimentoContaFacade        = new MovimentoContaFacade(self::getManagerRegistry());
        $this->chequeFacade = new ChequeFacade(self::getManagerRegistry());
        $this->lockHelper   = new LockHelper();
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta_pagar/listar",
     *     summary="Listar conta_pagar",
     *     description="Lista as conta_pagar do banco",
     *     tags={"Conta a Pagar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os conta_pagar"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                  strict=false, nullable=true, default="1", description="Página para realizar o scroll", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="franqueada",              strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="favorecido_pessoa",       strict=false, nullable=true, description="Destino", requirements="\d+")
     * @FOSRest\QueryParam(name="plano_conta",             strict=false, nullable=true, description="Plano de Conta", requirements="\d+")
     * @FOSRest\QueryParam(name="forma_pagamento",         strict=false, nullable=true, description="Forma Pagamento", requirements="\d+")
     * @FOSRest\QueryParam(name="data_inicial_vencimento", strict=false, nullable=true, description="Data Inicial de vencimento")
     * @FOSRest\QueryParam(name="data_final_vencimento",   strict=false, nullable=true, description="Data Final de vencimento")
     * @FOSRest\QueryParam(name="data_inicial_pagamento",  strict=false, nullable=true, description="Data Inicial de pagamento")
     * @FOSRest\QueryParam(name="data_final_pagamento",    strict=false, nullable=true, description="Data Final de pagamento")
     * @FOSRest\QueryParam(name="valor_inicial",           strict=false, nullable=true, description="Valor Inicial", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="valor_final",             strict=false, nullable=true, description="Valor Final", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\QueryParam(name="mes",                     strict=false, nullable=true, description="Mês", requirements="\d{0,2}")
     * @FOSRest\QueryParam(name="ano",                     strict=false, nullable=true, description="Mês", requirements="\d{0,4}")
     * @FOSRest\QueryParam(name="situacao",                strict=false, nullable=true, description="Situação", map=true)
     * @FOSRest\QueryParam(name="order",                   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                 strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/conta_pagar/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros   = $request->all();
        $mensagemErro = "";
        $resultados   = $this->contaPagarFacade->listar($parametros, $mensagemErro);

        if (empty($mensagemErro) === false) {
            return ResponseFactory::badRequest(["parametrosEnviados" => $parametros], "Ocorreu algum erro inesperado.\n" . $mensagemErro);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/conta_pagar/{id}",
     *     summary="Buscar a conta_pagar",
     *     description="Busca a conta_pagar através da ID",
     *     tags={"Conta a Pagar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a conta_pagar"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/conta_pagar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->contaPagarFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/conta_pagar/criar",
     *     summary="Cria uma conta_pagar",
     *     description="Cria uma conta_pagar no banco",
     *     tags={"Conta a Pagar"},
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
     * @FOSRest\RequestParam(name="franqueada",              strict=true, nullable=false, description="Franqueada da NF", requirements="\d+")
     * @FOSRest\RequestParam(name="fornecedor_pessoa",       strict=true, nullable=false, description="Pessoa Fornecedora", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_total",             strict=false, nullable=true, description="Valor total da Nota", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="conta",                   strict=true, nullable=false, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="observacao",              strict=false, nullable=true, description="Observação dos Títulos", requirements=".*")
     * @FOSRest\RequestParam(name="plano_conta",             strict=true, nullable=false, description="Array de planos de conta", map=true)
     * @FOSRest\RequestParam(name="parcelas",                strict=true, nullable=false, description="Array de Parcelas", map=true)
     * @FOSRest\RequestParam(name="quitar_primeira_parcela", strict=true, nullable=true, description="Se a primeira parcela já deve ser quitada", requirements="\d")
     * @FOSRest\RequestParam(name="forma_cobranca",          strict=true, nullable=false, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_parcelas",         strict=true, nullable=true, description="Número de parcelas a serem geradas", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_parcela",           strict=true, nullable=true, description="Valor de cada parcela gerada", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/conta_pagar/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');

        if (isset($parametros[ConstanteParametros::CHAVE_PARCELA]) === false) {
            return ResponseFactory::internalServerError(["parametros" => $parametros], "Parcelas (títulos) não enviados.");
        }

        $contaPagarORM = $this->contaPagarFacade->criar($mensagem, $parametros);
        if ((is_null($contaPagarORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $planoContaORM = $this->planoContasContaPagarFacade->criarMultiplos($mensagem, $contaPagarORM, $parametros);
        if ((empty($planoContaORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $titulosPagarORM = $this->tituloPagarFacade->criar($mensagem, $contaPagarORM, $parametros, false);
        if ((empty($titulosPagarORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $this->chequeFacade->criarComTituloPagar($mensagem, $titulosPagarORM, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        // Flush das criações de títulos, caso tenha movimentação de conta, precisam existir.
        self::getEntityManager()->flush();

        if (empty($parametros[ConstanteParametros::CHAVE_QUITAR_PRIMEIRA_PARCELA]) === false) {
            $tituloQuitar = $titulosPagarORM[0];
            $movimento    = [
                ConstanteParametros::CHAVE_TITULO_PAGAR             => $tituloQuitar->getId(),
                ConstanteParametros::CHAVE_CONTA                    => $tituloQuitar->getConta()->getId(),
                ConstanteParametros::CHAVE_FRANQUEADA               => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_USUARIO                  => $parametros[ConstanteParametros::CHAVE_USUARIO],
                ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA     => 1,
                ConstanteParametros::CHAVE_FORMA_PAGAMENTO          => $tituloQuitar->getFormaCobranca()->getId(),
                ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO      => $tituloQuitar->getValorDocumento(),
                ConstanteParametros::CHAVE_MC_VALOR_MONTANTE        => $tituloQuitar->getValorDocumento(),
                ConstanteParametros::CHAVE_MC_VALOR_JUROS           => null,
                ConstanteParametros::CHAVE_MC_VALOR_DESCONTO        => null,
                ConstanteParametros::CHAVE_MC_VALOR_DIFERENCA_BAIXA => null,
                ConstanteParametros::CHAVE_MC_DATA_DEPOSITO         => new \DateTime(),
                ConstanteParametros::CHAVE_MC_DATA_CONTABIL         => new \DateTime(),
            ];

            $this->lockHelper->setLock('movimento_conta_conta_' . $movimento[ConstanteParametros::CHAVE_CONTA]);
            if ($this->lockHelper->getLock()->isAcquired() === false) {
                $this->lockHelper->getLock()->acquire(true);
                $movimentoContaORM = $this->movimentoContaFacade->criar($mensagem, $movimento);

                if ((is_null($movimentoContaORM) === true) || (empty($mensagem) === false)) {
                    $this->lockHelper->getLock()->release();
                    return ResponseFactory::conflict(["parametros" => $parametros, 'movimento_conta' => $movimento], $mensagem);
                }

                $this->lockHelper->getLock()->release();
            } else {
                $parametros = [
                    ConstanteParametros::CHAVE_TIPO_EVENTO => \App\Facade\Log\LogFacade::$LOG_CREATE,
                    ConstanteParametros::CHAVE_IP_ORIGEM   => $requestHeader->getClientIp(),
                    ConstanteParametros::CHAVE_FRANQUEADA  => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                    ConstanteParametros::CHAVE_USUARIO     => $parametros[ConstanteParametros::CHAVE_USUARIO],
                    ConstanteParametros::CHAVE_INFO_EVENTO => "Ocorreu um erro de Deadlock em:" . $requestHeader->getUri() . " \n Possivelmente 2 ou mais usuarios tentaram executar o update no mesmo registro, ao mesmo tempo.",
                ];
                $erroMsg    = "";
                self::getLogFacade()->criarLog($erroMsg, $parametros);
                return ResponseFactory::conflict(["parametros" => $parametros, 'movimento_conta' => $movimento], "Não foi possível prosseguir com o pagamento, possivelmente o pagamento já sendo executado por outra pessoa. Tente novamente.");
            }//end if
        }//end if

        return ResponseFactory::created(["contaPagarORM" => ["id" => $contaPagarORM->getId()]], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/conta_pagar/atualizar/{id}",
     *     summary="Atualiza um conta_pagar",
     *     description="Atualiza um conta_pagar no banco",
     *     tags={"Conta a Pagar"},
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
     * @FOSRest\RequestParam(name="franqueada",              strict=true, nullable=false, description="Franqueada da NF", requirements="\d+")
     * @FOSRest\RequestParam(name="fornecedor_pessoa",       strict=true, nullable=false, description="Pessoa Fornecedora", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_total",             strict=false, nullable=true, description="Valor total da Nota", requirements="^\d{0,7}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="conta",                   strict=true, nullable=false, description="Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="observacao",              strict=false, nullable=true, description="Observação dos Títulos", requirements=".*")
     * @FOSRest\RequestParam(name="plano_conta",             strict=true, nullable=false, description="Array de planos de conta", map=true)
     * @FOSRest\RequestParam(name="parcelas",                strict=true, nullable=false, description="Array de Parcelas", map=true)
     * @FOSRest\RequestParam(name="quitar_primeira_parcela", strict=true, nullable=true, description="Se a primeira parcela já deve ser quitada", requirements="\d")
     * @FOSRest\RequestParam(name="forma_cobranca",          strict=true, nullable=false, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="numero_parcelas",         strict=true, nullable=true, description="Número de parcelas a serem geradas", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_parcela",           strict=true, nullable=true, description="Valor de cada parcela gerada", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Patch("/conta_pagar/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, Request $request, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";

        $contaPagarORM = $this->contaPagarFacade->atualizar($mensagem, $id, $parametros);

        if (isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
            $planosContaACriar   = [];
            $planosContaARemover = $contaPagarORM->getPlanoContasContaPagar();

            foreach ($parametros[ConstanteParametros::CHAVE_PLANO_CONTA] as $planoConta) {
                if (isset($planoConta[ConstanteParametros::CHAVE_ID]) === true && empty($planoConta[ConstanteParametros::CHAVE_ID]) === false) {
                    if (is_null($this->planoContasContaPagarFacade->atualizar($mensagem, $planoConta[ConstanteParametros::CHAVE_ID], $contaPagarORM, $planoConta)) === true) {
                        return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                    }

                    $planosContaARemover = $planosContaARemover->filter(
                        function ($plano) use ($planoConta) {
                            return $plano->getId() !== intval($planoConta[ConstanteParametros::CHAVE_ID]);
                        }
                    );
                } else {
                    $planosContaACriar[] = $planoConta;
                }
            }

            if (empty($planosContaACriar) === false) {
                $planoContaORM = $this->planoContasContaPagarFacade->criarMultiplos($mensagem, $contaPagarORM, $parametros);
                if ((empty($planoContaORM) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }

            if (count($planosContaARemover) > 0) {
                $planosContaARemover->map(
                    function ($plano) use ($mensagem) {
                        $this->planoContasContaPagarFacade->remover($mensagem, $plano->getId());
                    }
                );
            }
        }//end if

        if (isset($parametros[ConstanteParametros::CHAVE_PARCELA]) === true) {
            $titulosACriar   = [];
            $titulosARemover = $contaPagarORM->getTituloPagar();

            $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $request->headers->get('Authorization-User-ID');
            foreach ($parametros[ConstanteParametros::CHAVE_PARCELA] as $chave => $parcela) {
                if (isset($parcela[ConstanteParametros::CHAVE_ID]) === true && empty($parcela[ConstanteParametros::CHAVE_ID]) === false) {
                    $parcela[ConstanteParametros::CHAVE_FRANQUEADA]        = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                    $parcela[ConstanteParametros::CHAVE_ATENDENTE_USUARIO] = $parametros[ConstanteParametros::CHAVE_ATENDENTE_USUARIO];

                    if (is_null($this->tituloPagarFacade->atualizar($mensagem, $parcela, false)) === true) {
                        return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                    }

                    $titulosARemover = $titulosARemover->filter(
                        function ($titulo) use ($parcela) {
                            return $titulo->getId() !== intval($parcela[ConstanteParametros::CHAVE_ID]);
                        }
                    );
                } else {
                    $titulosACriar[$chave] = $parcela;
                }
            }

            if (count($titulosARemover) > 0) {
                $titulosARemover->map(
                    function ($titulo) use ($mensagem) {
                        $this->tituloPagarFacade->remover($mensagem, $titulo);
                    }
                );
            }

            if (empty($titulosACriar) === false) {
                $titulosPagarORM = $this->tituloPagarFacade->criar($mensagem, $contaPagarORM, $parametros, false);
                if ((empty($titulosPagarORM) === true) || (empty($mensagem) === false)) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }

                $this->chequeFacade->criarComTituloPagar($mensagem, $titulosPagarORM, $parametros);
                if (empty($mensagem) === false) {
                    return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
                }
            }
        }//end if

        self::flushSeguro($mensagem);

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/conta_pagar/remover/{id}",
     *     summary="Remove uma conta_pagar",
     *     description="Remove uma conta_pagar no banco",
     *     tags={"Conta a Pagar"},
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
     * @FOSRest\Delete("/conta_pagar/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id, Request $requestHeader)
    {
        $mensagem = "";

        $tituloPagar = $this->tituloPagarFacade->excluir($mensagem, $id);
        if ($tituloPagar === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        $contaPagar = $this->contaPagarFacade->excluir($mensagem, $id);
        if ($contaPagar === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");

    }


}
