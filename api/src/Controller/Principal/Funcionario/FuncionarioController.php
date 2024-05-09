<?php

namespace App\Controller\Principal\Funcionario;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\FuncionarioFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class FuncionarioController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\FuncionarioFacade
     */
    private $funcionarioFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->funcionarioFacade = new FuncionarioFacade(self::getManagerRegistry());
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/buscar_consultores_ativos",
     *     summary="Listar funcionario",
     *     description="Lista as buscar_consultores_ativos do banco",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os buscar_consultores_ativos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario/buscar_consultores_ativos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaConsultoresAtivos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_CONSULTOR_FLAG] = true;
        $resultados = $this->funcionarioFacade->buscaDeFuncionarioPorFlag($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/buscar_gestores_ativos",
     *     summary="Listar funcionario",
     *     description="Lista as buscar_gestores_ativos do banco",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os buscar_gestores_ativos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario/buscar_gestores_ativos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaGestoresAtivos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG] = true;
        $resultados = $this->funcionarioFacade->buscaDeFuncionarioPorFlag($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/buscar_instrutores_ativos",
     *     summary="Listar funcionario",
     *     description="Lista as buscar_instrutores_ativos do banco",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os buscar_instrutores_ativos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario/buscar_instrutores_ativos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaInstrutoresAtivos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG] = true;
        $resultados = $this->funcionarioFacade->buscaDeFuncionarioPorFlag($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/buscar_instrutores_personal_ativos",
     *     summary="Listar funcionario",
     *     description="Lista as buscar_instrutores_personal_ativos do banco",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os buscar_instrutores_personal_ativos"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario/buscar_instrutores_personal_ativos")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscaInstrutoresPersonalAtivosAtivos(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG] = true;
        $resultados = $this->funcionarioFacade->buscaDeFuncionarioPorFlag($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/listar",
     *     summary="Listar funcionario",
     *     description="Lista as funcionario do banco",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os funcionario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina",                              strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     * @FOSRest\QueryParam(name="order",                               strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",                             strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="franqueada",                          strict=false, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="franqueada_personalizada",            strict=false, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario",                         strict=false, nullable=true, description="Funcionário", requirements="\d+")
     * @FOSRest\QueryParam(name="apelido",                             strict=false, nullable=true, description="Apelido do funcionário")
     * @FOSRest\QueryParam(name="cnpj_cpf",                            strict=false, nullable=true, description="CPF da pessoa")
     * @FOSRest\QueryParam(name="data_admissao",                       strict=false, nullable=true, description="Data de admissão do funcionário")
     * @FOSRest\QueryParam(name="data_demissao",                       strict=false, nullable=true, description="Data de demissão do funcionário")
     * @FOSRest\QueryParam(name="tipo_pagamento",                      strict=false, nullable=true, description="Tipo de pagamento: Horista, Mensalista", requirements=".{0,3}")
     * @FOSRest\QueryParam(name="nivel_instrutor",                     strict=false, nullable=true, description="Nível de Instrutor", requirements="\d+")
     * @FOSRest\QueryParam(name="cargo",                               strict=false, nullable=true, description="Cargo do funcionário", requirements="\d+")
     * @FOSRest\QueryParam(name="consultor",                           strict=false, nullable=true, description="Consultor", requirements="0|1")
     * @FOSRest\QueryParam(name="coordenador_pedagogico",              strict=false, nullable=true, description="Coordenador pedagógico", requirements="0|1")
     * @FOSRest\QueryParam(name="gestor_comercial",                    strict=false, nullable=true, description="Gestor Comercial", requirements="0|1")
     * @FOSRest\QueryParam(name="atendente",                           strict=false, nullable=true, description="Atendente", requirements="0|1")
     * @FOSRest\QueryParam(name="instrutor",                           strict=false, nullable=true, description="Instrutor", requirements="0|1")
     * @FOSRest\QueryParam(name="instrutor_personal",                  strict=false, nullable=true, description="Instrutor Personal", requirements="0|1")
     * @FOSRest\QueryParam(name="email_usuario",                       strict=false, nullable=true, description="E-mail do usuário")
     * @FOSRest\QueryParam(name="apenas_funcionarios_ativos",          strict=true, nullable=false, description="Apenas ativos", default="1", requirements="(0|1)")
     * @FOSRest\QueryParam(name="consultor_ou_gestor_comercial",       strict=true, nullable=false, description="Gestor ou consultor", default="0", requirements="(0|1)")
     * @FOSRest\QueryParam(name="instrutor_ou_coordenador_pedagogico", strict=true, nullable=false, description="Gestor ou consultor", default="0", requirements="(0|1)")
     *
     * @FOSRest\Get("/funcionario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = $this->funcionarioFacade->listar($parametros);
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/verificar-disponibilidade",
     *     summary="Verifica a disponibilidade do funcionario",
     *     description="Verifica a disponibilidade do funcionario",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os funcionario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",  strict=false, nullable=false, description="Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="funcionario", strict=false, nullable=true, description="Funcionário", requirements="\d+")
     * @FOSRest\QueryParam(name="horario",     strict=true, allowBlank=false, description="Horário", requirements="\d+")
     *
     * @FOSRest\Get("/funcionario/verificar-disponibilidade")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function verificaDisponibilidadeFuncionario(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $resultados = $this->funcionarioFacade->verificarDisponibilidade($parametros, $mensagem);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/{id}",
     *     summary="Buscar a funcionario",
     *     description="Busca a funcionario através da ID",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a funcionario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/funcionario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->funcionarioFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], $mensagem);
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/funcionario/criar",
     *     summary="Cria uma funcionario",
     *     description="Cria uma funcionario no banco",
     *     tags={"Funcionario"},
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
     * @FOSRest\RequestParam(name="pessoa",                       strict=true, nullable=false, allowBlank=false, description="Pessoa", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",                   strict=true, nullable=false, allowBlank=false, description="Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="cargo",                        strict=true, nullable=false, allowBlank=false, description="Cargo", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",                      strict=true, nullable=true, allowBlank=true, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="nivel_instrutor",              strict=true, nullable=true, allowBlank=true, description="Nivel Instrutor", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                        strict=true, nullable=false, allowBlank=false, description="Banco", requirements="\d+")
     * @FOSRest\RequestParam(name="gestor_comercial_funcionario", strict=true, nullable=true, allowBlank=true, description="Gestor Comercial do Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_pagamento",               strict=true, nullable=false, allowBlank=false, description="Gestor Tipo de Pagamento", requirements="[H|M]")
     * @FOSRest\RequestParam(name="apelido",                      strict=true, nullable=false, allowBlank=false, description="Apelido do Funcionario")
     * @FOSRest\RequestParam(name="data_admissao",                strict=true, nullable=true, allowBlank=true, description="Data de Admissao")
     * @FOSRest\RequestParam(name="data_demissao",                strict=true, nullable=true, allowBlank=true, description="Data de Demissao")
     * @FOSRest\RequestParam(name="agencia",                      strict=false, nullable=true, allowBlank=true, description="Agencia")
     * @FOSRest\RequestParam(name="digito_agencia",               strict=false, nullable=true, allowBlank=true, description="Digito verificador da Agencia")
     * @FOSRest\RequestParam(name="conta_corrente",               strict=false, nullable=true, allowBlank=true, description="Conta Corrente")
     * @FOSRest\RequestParam(name="digito_conta_corrente",        strict=false, nullable=true, allowBlank=true, description="Digito da Conta Corrente")
     * @FOSRest\RequestParam(name="recebe_emails",                strict=false, nullable=false, allowBlank=false, description="Recebe Email", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="instrutor",                    strict=false, nullable=false, allowBlank=false, description="Instrutor", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="instrutor_personal",           strict=false, nullable=false, allowBlank=false, description="Instrutor Personal", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="gestor_comercial",             strict=false, nullable=false, allowBlank=false, description="Gestor Comercial", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="consultor",                    strict=false, nullable=false, allowBlank=false, description="Consultor", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="coordenador_pedagogico",       strict=false, nullable=true, allowBlank=true, description="É Coordenador Pedagógico", requirements="[0|1]")
     * @FOSRest\RequestParam(name="atendente",                    strict=false, nullable=false, allowBlank=false, description="Atendente", requirements="[0|1]", default=0)
     * @FOSRest\RequestParam(name="funcionario_valor_horas",      strict=false, nullable=true, allowBlank=true, description="Valor Hora", map=true)
     * @FOSRest\RequestParam(name="disponibilidades",             strict=false, nullable=true, allowBlank=true, description="Disponibilidade", map=true)
     *
     * @FOSRest\Post("/funcionario/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $objetoORM  = $this->funcionarioFacade->criar($mensagem, $parametros);
        if ((is_null($objetoORM) === true) || (empty($mensagem) === false)) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::created(["objetoORM" => $objetoORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/funcionario/atualizar/{id}",
     *     summary="Atualiza um funcionario",
     *     description="Atualiza um funcionario no banco",
     *     tags={"Funcionario"},
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
     * @FOSRest\RequestParam(name="cargo",                        strict=false, nullable=false, allowBlank=false, description="Cargo", requirements="\d+")
     * @FOSRest\RequestParam(name="usuario",                      strict=false, nullable=true, allowBlank=true, description="Usuario", requirements="\d+")
     * @FOSRest\RequestParam(name="nivel_instrutor",              strict=false, nullable=false, allowBlank=false, description="Nivel Instrutor", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                        strict=false, nullable=false, allowBlank=false, description="Banco", requirements="\d+")
     * @FOSRest\RequestParam(name="gestor_comercial_funcionario", strict=false, nullable=true, allowBlank=true, description="Gestor Comercial do Funcionario", requirements="\d+")
     * @FOSRest\RequestParam(name="tipo_pagamento",               strict=false, nullable=false, allowBlank=false, description="Gestor Tipo de Pagamento", requirements="[H|M]")
     * @FOSRest\RequestParam(name="apelido",                      strict=false, nullable=false, allowBlank=false, description="Apelido do Funcionario")
     * @FOSRest\RequestParam(name="data_admissao",                strict=false, nullable=true, allowBlank=true, description="Data de Admissao")
     * @FOSRest\RequestParam(name="data_demissao",                strict=false, nullable=true, allowBlank=true, description="Data de Demissao")
     * @FOSRest\RequestParam(name="agencia",                      strict=false, nullable=true, allowBlank=true, description="Agencia")
     * @FOSRest\RequestParam(name="digito_agencia",               strict=false, nullable=true, allowBlank=true, description="Digito verificador da Agencia")
     * @FOSRest\RequestParam(name="conta_corrente",               strict=false, nullable=true, allowBlank=true, description="Conta Corrente")
     * @FOSRest\RequestParam(name="digito_conta_corrente",        strict=false, nullable=true, allowBlank=true, description="Digito da Conta Corrente")
     * @FOSRest\RequestParam(name="recebe_emails",                strict=false, nullable=true, allowBlank=true, description="Recebe Email", requirements="[0|1]")
     * @FOSRest\RequestParam(name="instrutor",                    strict=false, nullable=true, allowBlank=true, description="Eh instrutor", requirements="[0|1]")
     * @FOSRest\RequestParam(name="instrutor_personal",           strict=false, nullable=true, allowBlank=true, description="Eh Instrutor Personal", requirements="[0|1]")
     * @FOSRest\RequestParam(name="gestor_comercial",             strict=false, nullable=true, allowBlank=true, description="Eh gestor comercial", requirements="[0|1]")
     * @FOSRest\RequestParam(name="consultor",                    strict=false, nullable=true, allowBlank=true, description="Eh consultor", requirements="[0|1]")
     * @FOSRest\RequestParam(name="atendente",                    strict=false, nullable=true, allowBlank=true, description="Eh Atendente", requirements="[0|1]")
     * @FOSRest\RequestParam(name="coordenador_pedagogico",       strict=false, nullable=true, allowBlank=true, description="É Coordenador Pedagógico", requirements="[0|1]")
     * @FOSRest\RequestParam(name="funcionario_valor_horas",      strict=false, nullable=true, allowBlank=true, description="Valor Hora", map=true)
     * @FOSRest\RequestParam(name="situacao",                     strict=false, nullable=true, allowBlank=true, description="Situacao", requirements="[A|I]")
     * @FOSRest\RequestParam(name="disponibilidades",             strict=false, nullable=true, allowBlank=true, description="Disponibilidade", map=true)
     *
     * @FOSRest\Patch("/funcionario/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $retorno    = $this->funcionarioFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        return ResponseFactory::ok(['funcionario' => $parametros], 'Atualizado com sucesso');
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/funcionario/remover/{id}",
     *     summary="Remove uma funcionario",
     *     description="Remove uma funcionario no banco",
     *     tags={"Funcionario"},
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
     * @FOSRest\Delete("/funcionario/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->funcionarioFacade->remover($mensagem, $id);
        if ($retorno === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok([], "Excluido com sucesso");
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/funcionario/buscar_nome_contato/{query}",
     *     summary="Buscar funcionário por nome",
     *     description="Busca funcionários pelo nome",
     *     tags={"Funcionario"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as funcionários"
     *     ),
     * )
     *
     * @FOSRest\Get("/funcionario/buscar_nome_contato/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeContato($query, ParamFetcher $paramFetcher)
    {
        $funcionarios = $this->funcionarioFacade->buscarPorNome($query);

        return ResponseFactory::ok($funcionarios);
    }


}
