<?php

namespace App\Controller\Principal\SistemaAvaliacao;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\SistemaAvaliacaoFacade;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class SistemaAvaliacaoController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\SistemaAvaliacaoFacade
     */
    private $sistemaAvaliacaoFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->sistemaAvaliacaoFacade = new SistemaAvaliacaoFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sistema_avaliacao/listar",
     *     summary="Listar sistema_avaliacao",
     *     description="Lista as sistema_avaliacao do banco",
     *     tags={"Sistema Avaliacao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os sistema_avaliacao"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/sistema_avaliacao/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->sistemaAvaliacaoFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/sistema_avaliacao/{id}",
     *     summary="Buscar a sistema_avaliacao",
     *     description="Busca a sistema_avaliacao através da ID",
     *     tags={"Sistema Avaliacao"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a sistema_avaliacao"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/sistema_avaliacao/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->sistemaAvaliacaoFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/sistema_avaliacao/criar",
     *     summary="Cria uma sistema_avaliacao",
     *     description="Cria uma sistema_avaliacao no banco",
     *     tags={"Sistema Avaliacao"},
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
     * @FOSRest\RequestParam(name="conceito_aprovacao",                     strict=true, nullable=false, allowBlank=false, description="ID da tabela conceito", requirements="\d+")
     * @FOSRest\RequestParam(name="conceito_corte_compromisso_qualidade",   strict=true, nullable=false, allowBlank=false, description="ID da tabela conceito", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",                              strict=true, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="frequencia_minima",                      strict=false, nullable=false, description="Frequencia Minima", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="frequencia_corte_compromisso_qualidade", strict=true, nullable=false, description="Frequencia Corte Compromisso Qualidade", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_aprovacao",                         strict=false, nullable=false, description="Nota Aprovacao", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_corte_compromisso_qualidade",       strict=false, nullable=false, description="Nota Corte Compromisso Qualidade", requirements="^\d{0,10}+\.?\d{0,2}?$")
     *
     * @FOSRest\Post("/sistema_avaliacao/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->sistemaAvaliacaoFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/sistema_avaliacao/atualizar/{id}",
     *     summary="Atualiza um sistema_avaliacao",
     *     description="Atualiza um sistema_avaliacao no banco",
     *     tags={"Sistema Avaliacao"},
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
     * @FOSRest\RequestParam(name="descricao",                              strict=false, nullable=false, allowBlank=false, description="Descricao")
     * @FOSRest\RequestParam(name="frequencia_minima",                      strict=false, nullable=false, description="Frequencia Minima", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="frequencia_corte_compromisso_qualidade", strict=false, nullable=false, description="Frequencia Corte Compromisso Qualidade", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_aprovacao",                         strict=false, nullable=false, description="Nota Aprovacao", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="nota_corte_compromisso_qualidade",       strict=false, nullable=false, description="Nota corte compromisso qualidade", requirements="^\d{0,10}+\.?\d{0,2}?$")
     * @FOSRest\RequestParam(name="exclusao",                               strict=false, nullable=false, description="Exclusao", requirements="[1|0]")
     *
     * @FOSRest\Patch("/sistema_avaliacao/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->sistemaAvaliacaoFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/sistema_avaliacao/remover/{id}",
     *     summary="Remove uma sistema_avaliacao",
     *     description="Remove uma sistema_avaliacao no banco",
     *     tags={"Sistema Avaliacao"},
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
     * @FOSRest\Delete("/sistema_avaliacao/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->sistemaAvaliacaoFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], $mensagem);
    }


}
