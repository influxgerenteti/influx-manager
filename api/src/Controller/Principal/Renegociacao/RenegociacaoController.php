<?php

namespace App\Controller\Principal\Renegociacao;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Request\ParamFetcher;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Helper\ConstanteParametros;
use Exception;

/**
 *
 * @author        Marcelo A Naegeler
 * @Route("/api")
 */
class RenegociacaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\RenegociacaoFacade
     */
    private $renegociacaoFacade;

    /**
     *
     * @var \App\Facade\Principal\ContaReceberFacade
     */
    private $contaReceberFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloReceberFacade
     */
    private $tituloReceberFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->renegociacaoFacade  = new \App\Facade\Principal\RenegociacaoFacade(self::getManagerRegistry());
        $this->contaReceberFacade  = new \App\Facade\Principal\ContaReceberFacade(self::getManagerRegistry());
        $this->tituloReceberFacade = new \App\Facade\Principal\TituloReceberFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/renegociacao/listar",
     *     summary="Listar renegociacao",
     *     description="Lista as renegociacao do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os renegociacao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Paginação", requirements="\d+")
     *
     * @FOSRest\Get("/renegociacao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $resultados = $this->renegociacaoFacade->listar($parametros);

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/renegociacao/{id}",
     *     summary="Buscar a renegociacao",
     *     description="Busca a renegociacao através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a renegociacao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/renegociacao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        // TODO: seu objeto ORM
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/renegociacao/criar",
     *     summary="Cria uma renegociacao",
     *     description="Cria uma renegociacao no banco",
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
     * @FOSRest\RequestParam(name="responsavel_financeiro_pessoa", strict=true, nullable=false, description="Responsavel Financeiro Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="aluno",                         strict=true, nullable=true, description="Responsavel Financeiro Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="vendedor_funcionario",          strict=true, nullable=false, description="Responsavel Financeiro Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="titulos_receber",               strict=true, nullable=false, allowBlank=false, description="Array de títulos a receber", map=true)
     * @FOSRest\RequestParam(name="titulos_receber_renegociados",  strict=true, nullable=false, allowBlank=false, description="Array de títulos a receber", map=true)
     *
     * @FOSRest\Post("/renegociacao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request, Request $requestHeader)
    {
        $parametros = $request->all();
        $mensagem   = "";

        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = \App\Helper\VariaveisCompartilhadas::$franqueadaID;

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)||(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)) {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $requestHeader->headers->get('Authorization-User-ID');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER_RENEGOCIADOS]) === false) {
            return ResponseFactory::conflict([], 'Títulos a renegociar devem ser informados.');
        }

        $marcarRenegociados = $this->tituloReceberFacade->marcarRenegociados($parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER_RENEGOCIADOS], $parametros[ConstanteParametros::CHAVE_USUARIO],$mensagem);
        if ($marcarRenegociados === false && empty($mensagem) === false) {
            return ResponseFactory::conflict([], $mensagem);
        }
        self::getEntityManager()->getConnection()->beginTransaction(); // suspend auto-commit
        try {
    


            $contaReceberData = [];
            $contaReceberData[ConstanteParametros::CHAVE_USUARIO]         = $parametros[ConstanteParametros::CHAVE_USUARIO];
            $contaReceberData[ConstanteParametros::CHAVE_FRANQUEADA]      = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
            $contaReceberData[ConstanteParametros::CHAVE_TITULOS_RECEBER] = $parametros[ConstanteParametros::CHAVE_TITULOS_RECEBER];
            $contaReceberData[ConstanteParametros::CHAVE_ALUNO]           = $parametros['aluno'];
            $contaReceberData[ConstanteParametros::CHAVE_SACADO_PESSOA]   = $parametros['responsavel_financeiro_pessoa'];
            $contaReceberData[ConstanteParametros::CHAVE_VENDEDOR_FUNCIONARIO] = $parametros[ConstanteParametros::CHAVE_VENDEDOR_FUNCIONARIO];

        
            $contaReceberORM = $this->contaReceberFacade->criar($mensagem, $contaReceberData, $boletos);
            $renegociacaoORM = $this->renegociacaoFacade->criar($mensagem, $parametros, $contaReceberORM, false);

            

            if ((is_null($contaReceberORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict([], $mensagem);
            }

            self::getEntityManager()->flush();

            if ((is_null($renegociacaoORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }
        
            self::getEntityManager()->getConnection()->commit();
            return ResponseFactory::created(["objetoORM" => $renegociacaoORM->getId()], "Renegociação concluída");

            
        } catch (Exception $e) {
            self::getEntityManager()->getConnection()->rollBack();
            return ResponseFactory::badRequest(
                [],
                "Falha ao gerar renegociação :".$e->getMessage()
            );            
        }
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/renegociacao/alterar/{id}",
     *     summary="Atualiza um renegociacao",
     *     description="Atualiza um renegociacao no banco",
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
     * @FOSRest\RequestParam(name="exemplo_string",  strict=true, nullable=false, allowBlank=false, description="exemplo_string")
     * @FOSRest\RequestParam(name="exemplo_integer", strict=true, nullable=false, allowBlank=false, description="exemplo_integer", default="0")
     *
     * @FOSRest\Patch("/renegociacao/alterar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = false;
        // TODO: True ou False
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/renegociacao/remover/{id}",
     *     summary="Remove uma renegociacao",
     *     description="Remove uma renegociacao no banco",
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
     * @FOSRest\Delete("/renegociacao/remover/{id}")
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
