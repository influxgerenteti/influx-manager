<?php

namespace App\Controller\Principal\Servico;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
;
use App\Facade\Principal\ServicoFacade;
use App\Facade\Principal\ServicoHistoricoFacade;
use App\Helper\ConstanteParametros;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\ContaReceberFacade;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class ServicoController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\ServicoFacade
     */
    private $servicoFacade;

    /**
     *
     * @var \App\Facade\Principal\ServicoHistoricoFacade
     */
    private $servicoHistoricoFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        parent::constroiFacades();
        $this->servicoFacade          = new ServicoFacade(self::getManagerRegistry());
        $this->servicoHistoricoFacade = new ServicoHistoricoFacade(self::getManagerRegistry());
        $this->contaReceberFacade     = new ContaReceberFacade(self::getManagerRegistry());
    }

    /**
     * Gera conta a receber
     *
     * @param string $mensagemErro
     * @param double $valor
     * @param int $formaCobrancaId
     * @param int $franqueadaId
     * @param int $alunoId
     * @param int $usuarioId
     * @param int $itemId
     * @param \App\Entity\Principal\Servico $servicoORM
     *
     * @return boolean
     */
    private function gerarContaReceber(&$mensagemErro, $valor, $formaCobrancaId, $franqueadaId, $alunoId, $usuarioId, $itemId, &$servicoORM)
    {
        $parametrosContaReceber = $this->contaReceberFacade->gerarParametrosContaReceberTituloReceber($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $valor, $formaCobrancaId, $itemId);
        $objetoORM = $this->contaReceberFacade->criar($mensagemErro, $parametrosContaReceber);
        $bRetorno  = (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
        if ($bRetorno === true) {
            $servicoORM->setContaReceber($objetoORM);
        }

        return $bRetorno;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/servico/listar",
     *     summary="Listar servico",
     *     description="Lista as servico do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os servico"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",               strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",           strict=true, nullable=false,  allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="aluno",                strict=false, nullable=true,  allowBlank=true, description="Id do aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="protocolo",            strict=false, nullable=true,  allowBlank=true, description="Numero de protocolo")
     * @FOSRest\QueryParam(name="situacao",             strict=false, nullable=true,  allowBlank=true, description="Situacao do servico", map=true)
     * @FOSRest\QueryParam(name="data_solicitacao_de",  strict=false, nullable=true,  allowBlank=true, description="Data de solicitacao inicio")
     * @FOSRest\QueryParam(name="data_solicitacao_ate", strict=false, nullable=true,  allowBlank=true, description="Data de solicitacao fim")
     * @FOSRest\QueryParam(name="order",                strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",              strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/servico/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->servicoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/servico/{id}",
     *     summary="Buscar a servico",
     *     description="Busca a servico através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a servico"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/servico/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {

        $mensagemErro = "";
        $objetoORM    = $this->servicoFacade->buscarPorId($mensagemErro, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemErro);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/servico/buscar-numero-protocolo/{query}",
     *      summary="Busca servico por numero de portocolos",
     *      description="Buscar servico por numero de protocolo",
     *      tags={"Servico"},
     *      produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os servico por numero de protocolo"
     *     ),
     * )
     *
     * @FOSRest\Get("/servico/buscar-numero-protocolo/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarServicosPorNumeroDeProtocolo ($query)
    {
        $servico = $this->servicoFacade->buscarServicosPorProtocolo($query);

        return ResponseFactory::ok($servico);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/servico/criar",
     *     summary="Cria uma servico",
     *     description="Cria uma servico no banco",
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
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=false,  allowBlank=false,  description="Id da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",            strict=true, nullable=false,  allowBlank=false,  description="Id do aluno", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false,  allowBlank=false,  description="Id do funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="item",             strict=true, nullable=false,  allowBlank=false,  description="Id do item", requirements="\d+")
     * @FOSRest\RequestParam(name="quantidade",       strict=true, nullable=false,  allowBlank=false,  description="Quantidade")
     * @FOSRest\RequestParam(name="data_solicitacao", strict=true, nullable=false,  allowBlank=false,   description="Data prevista de solicitacao")
     * @FOSRest\RequestParam(name="data_conclusao",   strict=false, nullable=true,  allowBlank=true,   description="Data prevista de conclusão")
     * @FOSRest\RequestParam(name="descricao",        strict=true, nullable=false,  allowBlank=false,   description="Descricao do servico")
     * @FOSRest\RequestParam(name="concluido",        strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="forma_cobranca",   strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",            strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/servico/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $usuarioID  = $request->headers->get('Authorization-User-ID');
        $mensagem   = "";
        $objetoORM  = $this->servicoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        $servicoHistoricoORM = $this->servicoHistoricoFacade->criar($mensagem, $objetoORM, $parametros);
        if ((is_null($servicoHistoricoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros_historico" => $parametros], $mensagem);
        }

        if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
            if ($this->gerarContaReceber($mensagem, $parametros[ConstanteParametros::CHAVE_VALOR], $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_ALUNO], $usuarioID, $parametros[ConstanteParametros::CHAVE_ITEM], $objetoORM) === false) {
                return ResponseFactory::conflict(["servico-controllerconta_receber_ocorrencia_academica"], $mensagem);
            }
        }

        self::getEntityManager()->flush();

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/servico/atualizar/{id}",
     *     summary="Atualiza um servico",
     *     description="Atualiza um servico no banco",
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
     * @FOSRest\RequestParam(name="franqueada",       strict=true, nullable=false,  allowBlank=false,  description="Id da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",      strict=true, nullable=false,  allowBlank=false,  description="Id do funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="item",             strict=true, nullable=false,  allowBlank=false,  description="Id do item", requirements="\d+")
     * @FOSRest\RequestParam(name="quantidade",       strict=true, nullable=false,  allowBlank=false,  description="Quantidade")
     * @FOSRest\RequestParam(name="data_solicitacao", strict=true, nullable=false,  allowBlank=false,   description="Data prevista de solicitacao")
     * @FOSRest\RequestParam(name="data_conclusao",   strict=false, nullable=true,  allowBlank=true,   description="Data prevista de conclusão")
     * @FOSRest\RequestParam(name="descricao",        strict=true, nullable=false,  allowBlank=false,   description="Descricao do servico")
     * @FOSRest\RequestParam(name="concluido",        strict=true, nullable=false, description="Marca como concluido", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="cancelamento",     strict=true, nullable=false, description="Marca como cancelamento", default="0", requirements="(0|1)")
     * @FOSRest\RequestParam(name="forma_cobranca",   strict=true, nullable=true, allowBlank=true, description="Forma de cobrança", requirements="\d+")
     * @FOSRest\RequestParam(name="valor",            strict=true, nullable=true, allowBlank=true, description="Valor Total", requirements="^\d{0,7}+\.?\d{0,2}?$")
     *
     * @FOSRest\Patch("/servico/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $servicoORM = null;
        $usuarioID  = $request->headers->get('Authorization-User-ID');
        $retorno    = $this->servicoFacade->atualizar($mensagem, $id, $parametros, $servicoORM);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false) {
            $parametros[ConstanteParametros::CHAVE_SERVICO] = $id;
            $servicoHistoricoORM = $this->servicoHistoricoFacade->criar($mensagem, null, $parametros);
            if ((is_null($servicoHistoricoORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::badRequest(["parametros_servico_historico" => $parametros], $mensagem);
            }
        }

        if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
            if ($this->gerarContaReceber($mensagem, $parametros[ConstanteParametros::CHAVE_VALOR], $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $servicoORM->getAluno()->getId(), $usuarioID, $parametros[ConstanteParametros::CHAVE_ITEM], $servicoORM) === false) {
                return ResponseFactory::conflict(["servico-controllerconta_receber_ocorrencia_academica"], $mensagem);
            }
        }

        self::getEntityManager()->flush();
        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/servico/remover/{id}",
     *     summary="Remove uma servico",
     *     description="Remove uma servico no banco",
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
     * @FOSRest\Delete("/servico/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
