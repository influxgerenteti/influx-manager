<?php

namespace App\Controller\Principal\Modulo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Request\ParamFetcher;

use App\Controller\Principal\Base\GenericController;
use App\Facade\Principal\ModuloFacade;
use App\Factory\ResponseFactory;

/**
 *
 * @Route("/api")
 *
 * @author Marcelo Andre Naegeler
 */
class ModuloController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\ModuloFacade $moduloFacade
     */
    private $moduloFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Controller\Principal\Base\GenericController::constroiFacades()
     */
    protected function constroiFacades()
    {
        parent::constroiFacades();
        $this->moduloFacade = new ModuloFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/listar-menu",
     *      summary="Traz os dados do menu lateral, estruturado.",
     *      description="Traz os dados do menu lateral, estruturado.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Retorna os módulos cadastrados"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Houve algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\Get("/modulo/listar-menu")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarMenu (Request $request)
    {
        $usuarioID = $request->headers->get('Authorization-User-ID');
        $modulos   = $this->moduloFacade->listarMenu($usuarioID);

        return ResponseFactory::ok($modulos);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/listar-menu-relatorios",
     *      summary="Traz os dados do menu de relatórios, estruturado.",
     *      description="Traz os dados do menu de relatório, estruturado.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Retorna os módulos cadastrados"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Houve algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\Get("/modulo/listar-menu-relatorios")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarMenuRelatorios (Request $request)
    {
        $usuarioID = $request->headers->get('Authorization-User-ID');
        $modulos   = $this->moduloFacade->listarMenuRelatorios($usuarioID);

        return ResponseFactory::ok($modulos);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/listar",
     *      summary="Lista dos itens de módulo",
     *      description="Lista itens de módulo do sistema.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Retorna os módulos cadastrados"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Houve algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",  requirements="\d+", default="1", allowBlank=true, nullable=true, description="Contador da página a ser listada")
     * @FOSRest\QueryParam(name="order",   strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao", strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/modulo/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listar (ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();
        $resultados = $this->moduloFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([]);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Post(
     *      path="/api/modulo/criar",
     *      summary="Criar módulo",
     *      description="Cria um novo item de módulo.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="201",
     *          description="Retorna o módulo cadastrado"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Houve algum erro no cadastro",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="nome",       strict=true, allowBlank=true, description="Nome do Módulo", requirements=".{0,80}+")
     * @FOSRest\RequestParam(name="url",        strict=true, allowBlank=true, description="Url do Módulo", requirements="[a-zA-Z0-9\/-]+")
     * @FOSRest\RequestParam(name="situacao",   strict=true, allowBlank=false, description="Situação do Módulo", requirements="(A|I)", default="A")
     * @FOSRest\RequestParam(name="modulo_pai", strict=true, nullable=true, description="ID Módulo Pai", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="acao_sistemas", strict=true, nullable=true, allowBlank=false, description="Array de AcaoSistema", requirements="\d+", map=true)
     *
     * @FOSRest\Post("/modulo/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar (ParamFetcher $paramFetcher)
    {
        $mensagemErro = "";
        $parametros   = $paramFetcher->all();

        $itemSalvo = $this->moduloFacade->criar($mensagemErro, $parametros);
        if (is_null($itemSalvo) === true) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagemErro);
        }

        return ResponseFactory::created(["modulo" => $itemSalvo->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/buscar/{id}",
     *      summary="Busca um módulo",
     *      description="Busca um item de módulo pela ID fornecida.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="204",
     *          description="Atualizado com sucesso"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Houve um erro",
     *      )
     * )
     *
     * @FOSRest\Get("/modulo/buscar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar ($id, Request $request)
    {
        // TODO: Validar pelo usuário
        $mensagem = "";
        $modulo   = $this->moduloFacade->buscarModulo($mensagem, $id);

        if (empty($mensagem) === true) {
            return ResponseFactory::ok($modulo);
        }

        return ResponseFactory::badRequest([], $mensagem);
    }

    /**
     *
     * @SWG\Patch(
     *      path="/api/modulo/atualizar/{id}",
     *      summary="Atualiza um módulo",
     *      description="Atualiza um item de módulo pela ID fornecida.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="204",
     *          description="Atualizado com sucesso"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Houve um erro",
     *      )
     * )
     *
     * @FOSRest\RequestParam(name="nome",       strict=true, allowBlank=true, description="Nome do Módulo", requirements=".{0,80}+")
     * @FOSRest\RequestParam(name="url",        strict=true, allowBlank=true, description="Url do Módulo", requirements="[a-zA-Z0-9\/-]+")
     * @FOSRest\RequestParam(name="situacao",   strict=true, allowBlank=false, description="Situação do Módulo", requirements="(A|I)", default="A")
     * @FOSRest\RequestParam(name="modulo_pai", strict=true, nullable=true, description="ID Módulo Pai", requirements="\d+")
     *
     * @FOSRest\RequestParam(name="acao_sistemas", strict=true, nullable=true, allowBlank=false, description="Array de AcaoSistema", requirements="\d+", map=true)
     *
     * @FOSRest\Patch("/modulo/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar ($id, ParamFetcher $paramFetcher, Request $request)
    {
        $parametros = $paramFetcher->all();

        $mensagemErro = "";
        $itemSalvo    = $this->moduloFacade->editar($mensagemErro, $id, $parametros);

        if ($itemSalvo === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagemErro);
        }

        return ResponseFactory::noContent([]);
    }

    /**
     *
     * @SWG\Delete(
     *      path="/api/modulo/remover/{id}",
     *      summary="Remove um módulo",
     *      description="Remove um item de módulo pela ID.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="204",
     *          description="Módulo removido com sucesso"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Ocorreu algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\Delete("/modulo/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function remover ($id)
    {
        $mensagemErro = "";
        $itemRemovido = $this->moduloFacade->remover($mensagemErro, $id);

        if ($itemRemovido === false) {
            return ResponseFactory::badRequest([], $mensagemErro);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/buscar-modulos-pais",
     *      summary="Busca módulos que podem ser pais",
     *      description="Módulos que são filhos (modulo_pai_id !== null) não podem ser pais, para mantermos somente dois níveis de menu.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     * @SWG\Response(
     *          response="200",
     *          description="Lista com itens de módulo"
     *      ),
     * @SWG\Response(
     *          response="400",
     *          description="Houve um erro",
     *      )
     * )
     *
     * @FOSRest\Get("/modulo/buscar-modulos-pais")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarModulosPais (Request $request)
    {
        $mensagem = "";
        $modulos  = $this->moduloFacade->buscarModulosPais($mensagem);

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($modulos);
    }

    /**
     *
     * @SWG\Get(
     *      path="/api/modulo/buscarTodos",
     *      summary="buscarTodos módulo",
     *      description="buscarTodos módulo do sistema.",
     *      tags={"Modulo"},
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response="200",
     *          description="Retorna os módulos cadastrados"
     *      ),
     *      @SWG\Response(
     *          response="400",
     *          description="Houve algum erro no servidor",
     *      )
     * )
     *
     * @FOSRest\Get("/modulo/buscarTodos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTodosOsModulosSemPai()
    {
        $modulos = $this->moduloFacade->buscarTodosOsModulosSemPai();
        return ResponseFactory::ok($modulos);
    }


}
