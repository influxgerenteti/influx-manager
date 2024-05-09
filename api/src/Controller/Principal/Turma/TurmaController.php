<?php

namespace App\Controller\Principal\Turma;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\TurmaFacade;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Exception;

/**
 *
 * @author        Marcelo André Naegeler
 * @Route("/api")
 */
class TurmaController extends GenericController
{


    /**
     *
     * @var \App\Facade\Principal\TurmaFacade $turmaFacade
     */
    private $turmaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->turmaFacade = new TurmaFacade(parent::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/consultar_turmas",
     *     summary="Listar turma",
     *     description="Lista as turma do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",       strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",   strict=true, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="order",        strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",      strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="buscar_todas", strict=false, nullable=true,  description="Se busca todas turmas ou faz por paginação")
     * @FOSRest\QueryParam(name="livro",        strict=true, allowBlank=true, nullable=true, description="Filtro - livro selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="curso",        strict=true, allowBlank=true, nullable=true, description="Filtro - curso selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="descricao",    strict=true, allowBlank=true, nullable=true, description="Filtro - descrição")
     * @FOSRest\QueryParam(name="dia_semana",   strict=true, allowBlank=true, nullable=true, description="Filtro - dias da semana", map=true)
     * @FOSRest\QueryParam(name="situacao",     strict=true, allowBlank=true, nullable=true, description="Filtro - situação da turma", map=true)
     *
     * @FOSRest\Get("/turma/consultar_turmas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTurmas(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->turmaFacade->buscarTurmas($parametros);

        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/listarTodos",
     *     summary="Listar todas as turmas",
     *     description="Lista todas as turmas de acordo com os parametros enviados",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=false, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",   strict=false, allowBlank=true, nullable=true, description="ModalidadeTurma selecionada", map=true)
     *
     * @FOSRest\Get("/turma/listarTodos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarTodos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros['buscar_todas'] = '1';
        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false || count($parametros[ConstanteParametros::CHAVE_SITUACAO]) === 0) {
            $parametros[ConstanteParametros::CHAVE_SITUACAO] = [
                SituacoesSistema::SITUACAO_TURMA_ABERTA,
                SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO,
                SituacoesSistema::SITUACAO_TURMA_ENCERRADA,
            ];
        }

        $resultados = $this->turmaFacade->buscarTurmas($parametros);

        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turmas/list-select",
     *     summary="Listar turma",
     *     description="Lista as turma do banco que devem aparecer em um options nos relatorios",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as turma"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",       strict=false, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="situacao",         strict=false, nullable=true, description="Situação")
     * 
     * @FOSRest\Get("/turmas/list-select")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTurmasSelectOptions(ParamFetcher $request)
    {
        $parametros = $request->all();
        try{
            $turmas = $this->turmaFacade->buscarTurmasSelectOptions($parametros);
    
        }catch(\Exception $e){
            return ResponseFactory::badRequest([], $e->getMessage());
        }
        return ResponseFactory::ok($turmas);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/listar",
     *     summary="Listar turma",
     *     description="Lista as turma do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",           strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada",       strict=false, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="horario",          strict=false, allowBlank=false, description="Horario selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="modalidade_turma", strict=false, allowBlank=false, description="ModalidadeTurma selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="sala_franqueada",  strict=false, allowBlank=false, description="SalaFranqueada selecionada", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",      strict=false, allowBlank=false, description="Instrutor selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="curso",            strict=false, allowBlank=false, description="Curso selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="livro",            strict=false, allowBlank=false, description="Livro selecionado", requirements="\d+")
     * @FOSRest\QueryParam(name="semestre",         strict=false, nullable=true, description="Semestre")
     * @FOSRest\QueryParam(name="data_inicio",      strict=false, nullable=true, description="Data Inicial de vencimento")
     * @FOSRest\QueryParam(name="data_fim",         strict=false, nullable=true, description="Data Final de vencimento")
     * @FOSRest\QueryParam(name="situacao",         strict=false, nullable=true, description="Situação")
     * @FOSRest\QueryParam(name="dia_semana",       strict=false, nullable=true, description="Dia da Semana", map=true)
     * @FOSRest\QueryParam(name="descricao",        strict=false, nullable=true, description="Descricao")
     * @FOSRest\QueryParam(name="apenas_sala",      strict=false, nullable=true, description="Filtrar apenas sala não personal")
     * @FOSRest\QueryParam(name="order",            strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",          strict=false, nullable=true,  description="ASC|DESC")
     *
     * @FOSRest\Get("/turma/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();

        $mensagem   = "";
        $resultados = $this->turmaFacade->listar($parametros);

        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/calcular_data_termino",
     *     summary="Executa o cálculo da data de término da turma",
     *     description="Executa o cálculo da data de término da turma",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a data de término"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="horario",     strict=false, allowBlank=false, description="Horário", requirements="\d+")
     * @FOSRest\QueryParam(name="curso",       strict=false, allowBlank=false, description="Curso", requirements="\d+")
     * @FOSRest\QueryParam(name="livro",       strict=false, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\QueryParam(name="data_inicio", strict=false, allowBlank=false, description="Data de início da turma", requirements=".*")
     * @FOSRest\QueryParam(name="franqueada",  strict=false, allowBlank=false, description="Franqueada", requirements="\d+")
     *
     * @FOSRest\Get("/turma/calcular_data_termino")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function calcularDataTermino (ParamFetcher $request)
    {
        $parametros  = $request->all();
        $mensagem    = '';
        $dataTermino = $this->turmaFacade->calcularDataTermino($mensagem, $parametros);

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($dataTermino);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/diario_classe/{id}",
     *     summary="Buscar a turma",
     *     description="Busca a turma através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a turma"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="turma_aula", strict=false, allowBlank=false, description="Turma Aula ID", requirements="\d+")
     *
     * @FOSRest\Get("/turma/diario_classe/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarTurmaHistorico ($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = '';
        $objetoORM  = $this->turmaFacade->buscarDadosTurmaTurmaAulaPorId($mensagem, $id, $parametros);

        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound($objetoORM, $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/{id}",
     *     summary="Buscar a turma",
     *     description="Busca a turma através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a turma"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/turma/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar ($id)
    {
        $mensagem  = '';
        $objetoORM = $this->turmaFacade->buscarPorId($mensagem, $id);

        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound($objetoORM, $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/buscar_alunos_turma/{idTurma}",
     *     summary="Buscar alunos da turma",
     *     description="Busca alunos da turma através do ID da turma",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna alunos da turma"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="buscar_encerrados", allowBlank=true, nullable=true, description="se deve ou não trazer os alunos encerrados")
     *
     * @FOSRest\Get("/turma/buscar_alunos_turma/{idTurma}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar_alunos_turma ($idTurma, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = '';
        $objetoORM  = $this->turmaFacade->buscarAlunosTurmaPorId($mensagem, $idTurma, $parametros);

        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound($objetoORM, $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/buscar_descricao/{query}",
     *     summary="Buscar turmas pela descrição",
     *     description="Buscar turmas pela descrição",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as turmas"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/turma/buscar_descricao/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPorDescricao ($query, ParamFetcher $paramFetcher)
    {
        $turmas = $this->turmaFacade->buscarPorDescricao($query);

        return ResponseFactory::ok($turmas);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/turma/criar",
     *     summary="Cria uma turma",
     *     description="Cria uma turma no banco",
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
     * @FOSRest\RequestParam(name="franqueada",        strict=true, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma",  strict=true, allowBlank=false, description="Modalidade", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",             strict=true, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",             strict=true, allowBlank=false, description="Curso", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",       strict=true, allowBlank=true, nullable=true, description="Funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",   strict=true, allowBlank=true, nullable=true, description="Sala", requirements="\d+")
     * @FOSRest\RequestParam(name="horario",           strict=true, allowBlank=false, description="Horário", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_hora_linhas", strict=true, allowBlank=true, nullable=true, description="Valor Hora Linha", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",         strict=true, allowBlank=false, description="Descrição", requirements=".+")
     * @FOSRest\RequestParam(name="maximo_alunos",     strict=true, allowBlank=false, description="Máximo de alunos", requirements="\d+")
     * @FOSRest\RequestParam(name="intensidade",       strict=true, allowBlank=false, description="Intensidade", requirements="R|S|I")
     * @FOSRest\RequestParam(name="semestre",          strict=true, allowBlank=false, description="Semestre", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",          strict=false, allowBlank=true, description="Situação", default="FOR", requirements="ABE|ENC|FOR")
     * @FOSRest\RequestParam(name="data_inicio",       strict=true, allowBlank=false, description="Data de início")
     * @FOSRest\RequestParam(name="data_fim",          strict=false, allowBlank=true, description="Data de término")
     * @FOSRest\RequestParam(name="observacao",        strict=false, allowBlank=true, description="Observação")
     *
     * @FOSRest\RequestParam(name="lista_turma_aula", strict=true, nullable=true, allowBlank=false, description="Array de aulas", map=true)
     *
     * @FOSRest\Post("/turma/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->turmaFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["id" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/turma/atualizar/{id}",
     *     summary="Atualiza um turma",
     *     description="Atualiza um turma no banco",
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
     * @FOSRest\RequestParam(name="modalidade_turma",  strict=true, allowBlank=false, description="Modalidade", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",             strict=true, allowBlank=false, description="Curso", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",             strict=true, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",       strict=true, allowBlank=true, nullable=true, description="Funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",   strict=true, allowBlank=true, nullable=true, description="Sala", requirements="\d+")
     * @FOSRest\RequestParam(name="horario",           strict=true, allowBlank=false, description="Horário", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_hora_linhas", strict=true, allowBlank=true, nullable=true, description="Valor Hora Linha", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",         strict=true, allowBlank=false, description="Descrição", requirements=".+")
     * @FOSRest\RequestParam(name="maximo_alunos",     strict=true, allowBlank=false, description="Máximo de alunos", requirements="\d+")
     * @FOSRest\RequestParam(name="intensidade",       strict=true, allowBlank=false, description="Intensidade", requirements="R|S|I")
     * @FOSRest\RequestParam(name="semestre",          strict=true, allowBlank=false, description="Semestre", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",          strict=false, allowBlank=true, description="Situação", default="ABE", requirements="ABE|ENC|FOR")
     * @FOSRest\RequestParam(name="data_inicio",       strict=true, allowBlank=false, description="Data de início")
     * @FOSRest\RequestParam(name="data_fim",          strict=false, allowBlank=true, description="Data de término")
     * @FOSRest\RequestParam(name="observacao",        strict=false, allowBlank=true, description="Observação")
     *
     * @FOSRest\RequestParam(name="lista_turma_aula", strict=true, nullable=true, allowBlank=false, description="Array de aulas", map=true)
     *
     * @FOSRest\Patch("/turma/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->turmaFacade->atualizar($mensagem, $id, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["id" => $objetoORM->getId()], "Registro alterado com sucesso!");
    }


    /**
     *
     * @SWG\Patch(
     *     path="/api/turma/excluirTurma/{id}",
     *     summary="Excluir um turma apos checar se e possivel excluir",
     *     description="Valida se e possivel e exclui um turma no banco, caso seja",
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
     * @FOSRest\Patch("/turma/excluirTurma/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluirTurma($id, ParamFetcher $request)
    {

        $parametros = $request->all();
        $mensagem   = "";

        $objetoORM = $this->turmaFacade->excluir($mensagem, $id, false);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok([], "Turma excluída com sucesso!");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/turma/podeExcluir/{id}",
     *     summary="Valida se e possivel excluir uma turma",
     *     description="Valida se e possivel excluir uma turma",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna se e possivel excluir a turma"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/turma/podeExcluir/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function podeExcluir($id, ParamFetcher $request)
    {

        $parametros = $request->all();
        $mensagem   = "";

        $objetoORM = $this->turmaFacade->podeExcluir($mensagem, $id);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }


    /**
     *
     * @SWG\Patch(
     *     path="/api/turma/reativar/{id}",
     *     summary="Reativar um turma",
     *     description="Atualiza um turma no banco",
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
     * @FOSRest\Patch("/turma/reativar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reativar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->turmaFacade->remover($mensagem, $id, false);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok([], "Registro alterado com sucesso!");
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/turma/remover/{id}",
     *     summary="Remove uma turma",
     *     description="Remove uma turma no banco",
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
     * @FOSRest\Delete("/turma/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->turmaFacade->remover($mensagem, $id, true);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/turma/gerar_turma_aula",
     *     summary="Gerar turma_aula",
     *     description="Gerar as turma aulas da turma passada, sem salvar nenhum dos 2",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",        strict=true, allowBlank=false, description="Franqueadora selecionada", requirements="\d+")
     * @FOSRest\RequestParam(name="modalidade_turma",  strict=true, allowBlank=false, description="Modalidade", requirements="\d+")
     * @FOSRest\RequestParam(name="livro",             strict=true, allowBlank=false, description="Livro", requirements="\d+")
     * @FOSRest\RequestParam(name="curso",             strict=true, allowBlank=false, description="Curso", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",       strict=true, allowBlank=true, nullable=true, description="Funcionário", requirements="\d+")
     * @FOSRest\RequestParam(name="sala_franqueada",   strict=true, allowBlank=true, nullable=true, description="Sala", requirements="\d+")
     * @FOSRest\RequestParam(name="horario",           strict=true, allowBlank=false, description="Horário", requirements="\d+")
     * @FOSRest\RequestParam(name="valor_hora_linhas", strict=true, allowBlank=true, nullable=true, description="Valor Hora Linha", requirements="\d+")
     * @FOSRest\RequestParam(name="descricao",         strict=true, allowBlank=false, description="Descrição", requirements=".+")
     * @FOSRest\RequestParam(name="maximo_alunos",     strict=true, allowBlank=true, nullable=true, description="Máximo de alunos", requirements="\d+")
     * @FOSRest\RequestParam(name="semestre",          strict=true, allowBlank=false, description="Semestre", requirements="\d+")
     * @FOSRest\RequestParam(name="situacao",          strict=false, allowBlank=true, nullable=true, description="Situação", default="FOR", requirements="ABE|ENC|FOR")
     * @FOSRest\RequestParam(name="data_inicio",       strict=true, allowBlank=false, description="Data de início")
     * @FOSRest\RequestParam(name="data_fim",          strict=false, allowBlank=true, nullable=true, description="Data de término")
     * @FOSRest\RequestParam(name="observacao",        strict=false, allowBlank=true, description="Observação")
     * @FOSRest\RequestParam(name="id",                strict=false, allowBlank=true, nullable=true,  description="id", requirements="\d+")
     *
     * @FOSRest\Post("/turma/gerar_turma_aula")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gerarTurmaAula(ParamFetcher $request)
    {
        $mensagem = "";

        $parametros = $request->all();
        
        if($parametros['id']) {
            $resultados = $this->turmaFacade->atualizarTurma($mensagem, $parametros['id'], $parametros);
        } else {
            $resultados = $this->turmaFacade->gerarTurmaAula($mensagem, $parametros);
        }
        
        $retorno    = [];
        foreach ($resultados as $k => $turma_aula) {
            $retorno[] = [
                "licao"     => [
                    "id"        => $turma_aula->getLicao()->getId(),
                    "descricao" => $turma_aula->getLicao()->getDescricao(),
                    "numero"    => $turma_aula->getLicao()->getNumero(),
                ],
                "data_aula" => $turma_aula->getDataAula(),
                "turma_id" => $turma_aula->getTurma()->getId(),
            ];
        }

        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($retorno);
    }


}
