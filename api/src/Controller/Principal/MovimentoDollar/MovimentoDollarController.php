<?php

namespace App\Controller\Principal\MovimentoDollar;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\MovimentoDollarFacade;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author        Rodrigo de Souza Fernandes (GATI labs)
 * @Route("/api")
 */
class MovimentoDollarController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\MovimentoDollarFacade
     */
    private $movimentoDollarFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->movimentoDollarFacade = new MovimentoDollarFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/movimento_dollar/listar",
     *     summary="Listar Movimento Dollar",
     *     description="Lista os Movimentos Dollar da base de dados",
     *     tags={"MovimentoDollar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os Movimentos Dollar"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="aluno",   strict=false, allowBlank=false, description="ID do Aluno", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",  strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/movimento_dollar/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->movimentoDollarFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/movimento_dollar/{id}",
     *     summary="Buscar o Movimento Dollar",
     *     description="Busca o Movimento Dollar através da ID",
     *     tags={"MovimentoDollar"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna o Movimento Dollar"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/movimento_dollar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = $this->movimentoDollarFacade->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "Movimento Dollar não encontrado.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_dollar/lancar_atualizar",
     *     summary="Cria vários Movimento Dollar",
     *     description="Cria vários Movimento Dollar na base de dados",
     *     tags={"MovimentoDollar"},
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
     * @FOSRest\RequestParam(name="movimentos_dollar", strict=true, nullable=false, allowBlank=false, description="Lista de movimento dollar", map=true)
     *
     * @FOSRest\Post("/movimento_dollar/lancar_atualizar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lancarAtualizar(ParamFetcher $request)
    {
        $parametros     = $request->all();
        $mensagem       = "";
        $listaCriacao   = [ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR => []];
        $listaAlteracao = [ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR => []];
        foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR] as $movimentoDollarMetaData) {
            $movimentoDollarMetaData[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
            if ((isset($movimentoDollarMetaData[ConstanteParametros::CHAVE_ID]) === true) && (empty($movimentoDollarMetaData[ConstanteParametros::CHAVE_ID]) === false)) {
                $listaAlteracao[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR][] = $movimentoDollarMetaData;
            } else {
                $listaCriacao[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR][] = $movimentoDollarMetaData;
            }
        }

        if (count($listaAlteracao[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR]) > 0) {
            $objetosORM = $this->movimentoDollarFacade->atualizar($mensagem, $listaAlteracao, false);
            if ((empty($objetosORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::badRequest(["parametros_alteracao" => $listaAlteracao], $mensagem);
            }
        }

        if (count($listaCriacao[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR]) > 0) {
            $objetosORM = $this->movimentoDollarFacade->criar($mensagem, $listaCriacao, false);
            if ((empty($objetosORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_criacao" => $listaCriacao], $mensagem);
            }
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::created([], "Registros criado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_dollar/criar",
     *     summary="Cria vários Movimento Dollar",
     *     description="Cria vários Movimento Dollar na base de dados",
     *     tags={"MovimentoDollar"},
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
     * @FOSRest\RequestParam(name="movimentos_dollar", strict=true, nullable=false, allowBlank=false, description="Lista de movimento dollar", map=true)
     * @FOSRest\RequestParam(name="franqueada",        strict=true, allowBlank=false, nullable=false, description="Id Franqueada",requirements="\d+")
     *
     * @FOSRest\Post("/movimento_dollar/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetosORM = $this->movimentoDollarFacade->criar($mensagem, $parametros);
        if ((empty($objetosORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created([], "Registros criado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/movimento_dollar/atualizar",
     *     summary="Atualiza vários Movimento Dollar",
     *     description="Atualiza vários Movimento Dollar na base de dados",
     *     tags={"MovimentoDollar"},
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
     * @FOSRest\RequestParam(name="movimentos_dollar", strict=true, nullable=true, allowBlank=true, description="Lista de movimento dollar", map=true)
     * @FOSRest\RequestParam(name="franqueada",        strict=true, allowBlank=false, nullable=false, description="Id Franqueada", requirements="\d+")
     *
     * @FOSRest\Post("/movimento_dollar/atualizar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetosORM = $this->movimentoDollarFacade->atualizar($mensagem, $parametros);
        if ((empty($objetosORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }


}
