<?php

namespace App\Controller\Principal\OcorrenciaAcademica;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\FuncionarioFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Dayan Freitas
 * @Route("/api")
 */
class OcorrenciaAcademicaController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var FuncionarioFacade
     */
    private $funcionarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->funcionarioFacade = new FuncionarioFacade(self::getManagerRegistry());

    }

    /**
     *
     * @SWG\Get(
     *     path="/api/ocorrencia_academica/listar",
     *     summary="Listar ocorrencia_academica",
     *     description="Lista as ocorrencia_academica do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os ocorrencia_academica"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",                 strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",               strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="aluno",                 strict=false, nullable=true,  description="id do aluno para ser filtrado")
     * @FOSRest\QueryParam(name="tipo_ocorrencia",       strict=false, nullable=true,  description="Tipo da ocorrencia")
     * @FOSRest\QueryParam(name="funcionario",           strict=false, nullable=true,  description="id do funcionario para ser filtrado")
     * @FOSRest\QueryParam(name="situacao",              strict=false, nullable=true,  description="Situacao para filtro")
     * @FOSRest\QueryParam(name="data_abertura",         strict=false, nullable=true,  description="Data abertura")
     * @FOSRest\QueryParam(name="data_fechamento",       strict=false, nullable=true,  description="Data fechamento")
     * @FOSRest\QueryParam(name="data_movimentacao_de",  strict=false, nullable=true,  description="Data movimento de")
     * @FOSRest\QueryParam(name="data_movimentacao_ate", strict=false, nullable=true,  description="Data movimento ate")
     *
     * @FOSRest\Get("/ocorrencia_academica/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {

        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->ocorrenciaAcademicaFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/ocorrencia_academica/{id}",
     *     summary="Buscar a ocorrencia_academica",
     *     description="Busca a ocorrencia_academica através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a ocorrencia_academica"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/ocorrencia_academica/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagemError = "";
        $objetoORM     = $this->ocorrenciaAcademicaFacade->buscarPorId($mensagemError, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagemError);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/ocorrencia_academica/criar",
     *     summary="Cria uma uma ocorrencia academica",
     *     description="Cria uma ocorrencia academica no banco",
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
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=false, allowBlank=false, description="Aluno id", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_ocorrencia",      strict=true, nullable=false, allowBlank=false, description="Tipo da ocorrencia", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=false, allowBlank=false, description="Id da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",              strict=true, nullable=false, allowBlank=false, description="Id do usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",          strict=true, nullable=false, allowBlank=false, description="Id do funcionario responsavel", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",             strict=true, nullable=false, allowBlank=false, description="situacao da ocorrencia", default="A", requirements="(A|F)")
     * @FOSRest\RequestParam(name="data_proximo_contato", strict=false, nullable=true, allowBlank=true, description="Data do proximo contato da ocorrencia")
     * @FOSRest\RequestParam(name="horario",              strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato da ocorrencia")
     * @FOSRest\RequestParam(name="texto",                strict=true, nullable=true, allowBlank=true, description="Texto de detalhe da ocorrencia")
     *
     * @FOSRest\Post("/ocorrencia_academica/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();

        $mensagem   = "";
        $objetoORM  = $this->ocorrenciaAcademicaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {

            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TEXTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TEXTO]) === false)) {
            $funcionarioORM = $this->funcionarioFacade->buscarPorUsuarioFranqueadaLogado($mensagem, $parametros);
            if ((is_null($funcionarioORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_academica_detalhes" => $parametros], $mensagem);
            }

            $parametros[ConstanteParametros::CHAVE_FUNCIONARIO] = $funcionarioORM->getId();
            $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagem, $objetoORM, $parametros, false);
            if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_academica_detalhes" => $parametros], $mensagem);
            }
        }

        if (empty($mensagem) === true) {
            self::getEntityManager()->flush();
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/ocorrencia_academica/atualizar/{id}",
     *     summary="Atualiza uma ocorrencia academica",
     *     description="Atualiza uma ocorrencia academica no banco",
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
     * @FOSRest\RequestParam(name="aluno",                strict=true, nullable=false, allowBlank=false, description="Aluno id", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_ocorrencia",      strict=true, nullable=false, allowBlank=false, description="Tipo da ocorrencia", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",           strict=true, nullable=false, allowBlank=false, description="Id da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",              strict=true, nullable=false, allowBlank=false, description="Id do usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",          strict=true, nullable=false, allowBlank=false, description="Id do funcionario responsavel", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",             strict=true, nullable=false, allowBlank=false, description="situacao da ocorrencia", default="A", requirements="(A|F)")
     * @FOSRest\RequestParam(name="data_proximo_contato", strict=false, nullable=true, allowBlank=true, description="Data do proximo contato da ocorrencia")
     * @FOSRest\RequestParam(name="horario",              strict=false, nullable=true, allowBlank=true, description="Horario do proximo contato da ocorrencia")
     * @FOSRest\RequestParam(name="texto",                strict=true, nullable=true,  description="Texto de detalhe da ocorrencia")
     *
     * @FOSRest\Patch("/ocorrencia_academica/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->ocorrenciaAcademicaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        if (empty($parametros[ConstanteParametros::CHAVE_TEXTO]) === false) {
            $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA] = $id;
            $funcionarioORM = $this->funcionarioFacade->buscarPorUsuarioFranqueadaLogado($mensagem, $parametros);
            if ((is_null($funcionarioORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_academica_detalhes" => $parametros], $mensagem);
            }

           // $parametros[ConstanteParametros::CHAVE_FUNCIONARIO] = $funcionarioORM->getId();
            $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagem, null, $parametros);
            if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagem) === false)) {
                return ResponseFactory::conflict(["parametros_academica_detalhes" => $parametros], $mensagem);
            }
        }

        self::getEntityManager()->flush();

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/ocorrencia_academica/remover/{id}",
     *     summary="Remove uma ocorrencia_academica",
     *     description="Remove uma ocorrencia_academica no banco",
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
     * @FOSRest\Delete("/ocorrencia_academica/remover/{id}")
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
