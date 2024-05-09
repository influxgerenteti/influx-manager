<?php

namespace App\Controller\Principal\OperadoraCartao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\OperadoraCartaoFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\ParcelamentoOperadoraCartaoFacade;
use App\Facade\Principal\ParcelaParcelamentoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class OperadoraCartaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\OperadoraCartaoFacade
     */
    private $operadoraCartaoFacade;

    /**
     *
     * @var \App\Facade\Principal\ParcelamentoOperadoraCartaoFacade
     */
    private $parcelamentoOperadoraCartaoFacade;

    /**
     *
     * @var \App\Facade\Principal\ParcelaParcelamentoFacade
     */
    private $parcelaParcelamentoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->operadoraCartaoFacade = new OperadoraCartaoFacade(self::getManagerRegistry());
        $this->parcelamentoOperadoraCartaoFacade = new ParcelamentoOperadoraCartaoFacade(self::getManagerRegistry());
        $this->parcelaParcelamentoFacade         = new ParcelaParcelamentoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/operadora_cartao/listar",
     *     summary="Listar operadora_cartao",
     *     description="Lista as operadora_cartao do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os operadora_cartao"
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
     * @FOSRest\Get("/operadora_cartao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->operadoraCartaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/operadora_cartao/{id}",
     *     summary="Buscar a operadora_cartao",
     *     description="Busca a operadora_cartao através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a operadora_cartao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/operadora_cartao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemErro = "";
        $objetoORM    = $this->operadoraCartaoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/operadora_cartao/criar",
     *     summary="Cria uma operadora_cartao",
     *     description="Cria uma operadora_cartao no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_operacao", strict=true, nullable=false, description="Tipo de Operação", requirements="(C|D)")
     * @FOSRest\RequestParam(name="descricao",     strict=true, nullable=false, allowBlank=false, description="Descricao da Operação", requirements=".{0,255}")
     *
     * @FOSRest\RequestParam(name="parcelamento_operadora_cartaos", strict=true, nullable=true, allowBlank=true, description="Array de dados de cada linha de ParcelamentoOperadoraCartao", map=true)
     *
     * @FOSRest\Post("/operadora_cartao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $bRetorno   = true;
        $objetoORM  = $this->operadoraCartaoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS]) > 0)) {
            $arrayParametrosPocMetaData = $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS];
            foreach ($arrayParametrosPocMetaData as $parametrosPocMetaData) {
                $parametrosPocMetaData[ConstanteParametros::CHAVE_OPERADORA_CARTAO] = $objetoORM;
                $parcelaOperadoraCartaoORM = null;
                $bRetorno = $this->parcelamentoOperadoraCartaoFacade->criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosPocMetaData, $parcelaOperadoraCartaoORM, $mensagem);
                if ((isset($parametrosPocMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)&&(((bool) $parametrosPocMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)) {
                    unset($parametrosPocMetaData);
                } else if ($bRetorno === true) {
                    if ((isset($parametrosPocMetaData[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTOS]) === true)&&(count($parametrosPocMetaData[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTOS]) > 0)) {
                        $bRetorno = $this->parcelaParcelamentoFacade->criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosPocMetaData, $parcelaOperadoraCartaoORM, $mensagem);
                        if ($bRetorno === false) {
                            break;
                        }
                    }
                } else {
                    break;
                }
            }
        }

        if ($bRetorno === true) {
            self::getEntityManager()->flush();
            return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
        } else {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/operadora_cartao/atualizar/{id}",
     *     summary="Atualiza um operadora_cartao",
     *     description="Atualiza um operadora_cartao no banco",
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
     * @FOSRest\RequestParam(name="franqueada",    strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_operacao", strict=true, nullable=false, description="Tipo de Operação", requirements="(C|D)")
     * @FOSRest\RequestParam(name="descricao",     strict=true, nullable=false, allowBlank=false, description="Descricao da Operação", requirements=".{0,255}")
     * @FOSRest\RequestParam(name="situacao",      strict=true, nullable=false, allowBlank=false, description="Situacao", requirements="(A|I)")
     *
     * @FOSRest\RequestParam(name="parcelamento_operadora_cartaos", strict=true, nullable=true, allowBlank=true, description="Array de dados de cada linha de ParcelamentoOperadoraCartao", map=true)
     *
     * @FOSRest\Patch("/operadora_cartao/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $bRetorno   = $this->operadoraCartaoFacade->atualizar($mensagem, $id, $parametros);
        if ($bRetorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS]) > 0)) {
            $arrayParametrosPocMetaData = $parametros[ConstanteParametros::CHAVE_PARCELAMENTO_OPERADORA_CARTAOS];
            foreach ($arrayParametrosPocMetaData as $parametrosPocMetaData) {
                $parametrosPocMetaData[ConstanteParametros::CHAVE_OPERADORA_CARTAO] = $id;
                $parcelaOperadoraCartaoORM = null;
                $bRetorno = $this->parcelamentoOperadoraCartaoFacade->criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosPocMetaData, $parcelaOperadoraCartaoORM, $mensagem);
                if ((isset($parametrosPocMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)&&(((bool) $parametrosPocMetaData[ConstanteParametros::CHAVE_DELETADO]) === true)) {
                    unset($parametrosPocMetaData);
                } else if ($bRetorno === true) {
                    if ((isset($parametrosPocMetaData[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTOS]) === true)&&(count($parametrosPocMetaData[ConstanteParametros::CHAVE_PARCELA_PARCELAMENTOS]) > 0)) {
                        $bRetorno = $this->parcelaParcelamentoFacade->criarOuAtualizarOuRemoverViaOperadoraCartao($parametrosPocMetaData, $parcelaOperadoraCartaoORM, $mensagem);
                        if ($bRetorno === false) {
                            break;
                        }
                    }
                } else {
                    break;
                }
            }
        }

        if ($bRetorno === true) {
            self::getEntityManager()->flush();
            return ResponseFactory::noContent([]);
        } else {
            return ResponseFactory::badRequest([], $mensagem);
        }
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/operadora_cartao/remover/{id}",
     *     summary="Remove uma operadora_cartao",
     *     description="Remove uma operadora_cartao no banco",
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
     * @FOSRest\Delete("/operadora_cartao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->operadoraCartaoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
