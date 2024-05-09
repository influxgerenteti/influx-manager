<?php

namespace App\Controller\Principal\Pessoa;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\PessoaFacade;
use App\Helper\ConstanteParametros;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class PessoaController extends GenericController
{

    /**
     *
     * @var \App\Facade\Principal\PessoaFacade
     */
    private $pessoaFacade;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->pessoaFacade = new PessoaFacade(self::getManagerRegistry());
        $this->pessoaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Pessoa::class);
        
    }

    /**
     *
     * @var \App\Repository\Principal\PessoaRepository
     */
    private $pessoaRepository;

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/listar",
     *     summary="Listar pessoa",
     *     description="Lista as pessoa do banco",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os pessoa"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada",        strict=true,  nullable=false, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\QueryParam(name="pagina",            strict=false, nullable=true,  description="Pagina para realizar o scroll", requirements="\d{0,2}", default="1")
     * @FOSRest\QueryParam(name="itensPorPagina",    strict=false, nullable=true,  description="Quantidade de itens a ser exibido", requirements="\d{0,2}", default="50")
     * @FOSRest\QueryParam(name="aluno",             strict=false, nullable=true,  description="Checkbox de Aluno", default="0")
     * @FOSRest\QueryParam(name="alunos",            strict=false, nullable=true,  description="Filtra alunos Aluno", default="0")
     * @FOSRest\QueryParam(name="empresa",           strict=false, nullable=true,  description="Checkbox de Empresa", default="0")
     * @FOSRest\QueryParam(name="funcionario",       strict=false, nullable=true,  description="Checkbox de Funcionário", default="0")
     * @FOSRest\QueryParam(name="order",             strict=false, nullable=true,  description="Coluna de ordenação")
     * @FOSRest\QueryParam(name="direcao",           strict=false, nullable=true,  description="ASC|DESC")
     * @FOSRest\QueryParam(name="cpf",               strict=false, nullable=true,  allowBlank=true, description="filtro de CPF")
     * @FOSRest\QueryParam(name="cnpj",              strict=false, nullable=true,  allowBlank=true, description="filtro de CNPJ")
     * @FOSRest\QueryParam(name="telefone",          strict=false, nullable=true,  allowBlank=true, description="filtro de telefone")
     * @FOSRest\QueryParam(name="nome",              strict=false, nullable=true,  allowBlank=true, description="filtro de nome")
     * @FOSRest\QueryParam(name="tipo_pessoa",       strict=false, nullable=true,  allowBlank=true, description="filtro de tipo de pessoa")
     * @FOSRest\QueryParam(name="eh_lista_filtrada", strict=false, nullable=true,  allowBlank=true, description="se a lista é resultante de filtro")
     *
     * @FOSRest\Get("/pessoa/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";
        $resultados = $this->pessoaFacade->listar($mensagem, $parametros);
        if ($resultados === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/{id}",
     *     summary="Buscar as pessoa",
     *     description="Busca as pessoa através da ID",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a pessoa"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/pessoa/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $mensagem  = "";
        $objetoORM = $this->pessoaFacade->buscarPorId($mensagem, $id);
        if (is_null($objetoORM) === true)
            return ResponseFactory::notFound([], "Pessoa não encontrada.");
        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/buscar/{cpfCnpj}",
     *     summary="Buscar as pessoas",
     *     description="Busca as pessoas através do CPF/CNPJ",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna uma lista de pessoas"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/pessoa/buscar/{cpfCnpj}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarPessoas($cpfCnpj)
    {
        $numerosCpf       = preg_replace('/\D/', '', $cpfCnpj);
        $mensagem         = "";
        $pessoaCollection = $this->pessoaFacade->buscarPorCpfCnpj($mensagem, $cpfCnpj);
        return ResponseFactory::ok($pessoaCollection);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/buscar/empresa/{nomeEmpresa}",
     *     summary="Buscar as pessoas",
     *     description="Busca as pessoas através do nome da empresa",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna uma lista de pessoas"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/pessoa/buscar/empresa/{nomeEmpresa}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarEmpresasPorNome($nomeEmpresa, ParamFetcher $paramFetcher)
    {
        $pessoas = $this->pessoaFacade->buscarEmpresaPorNome($nomeEmpresa);

        return ResponseFactory::ok($pessoas);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/buscar_nome_contato/{query}",
     *     summary="Buscar pessoa por nome",
     *     description="Busca pessoas pelo nome",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as pessoas"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="tipo_fornecedor", strict=false, nullable=true, allowBlank=true, description="Tipos de fornecedores a serem filtrados", map=true)
     *
     * @FOSRest\Get("/pessoa/buscar_nome_contato/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeSemContrato ($query, ParamFetcher $paramFetcher)
    {
        $tipoFornecedor = $paramFetcher->get('tipo_fornecedor');
        $pessoas        = $this->pessoaFacade->buscarPorNome($query, $tipoFornecedor);

        return ResponseFactory::ok($pessoas);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/buscar_por_nome/{query}",
     *     summary="Buscar pessoa por nome",
     *     description="Busca pessoas pelo nome",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as pessoas"
     *     ),
     * )
     *
     * @FOSRest\QueryParam(name="tipo_fornecedor", strict=false, nullable=true, allowBlank=true, description="Tipos de fornecedores a serem filtrados", map=true)
     *
     * @FOSRest\Get("/pessoa/buscar_por_nome/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNome ($query, ParamFetcher $paramFetcher)
    {
        $tipoFornecedor = $paramFetcher->get('tipo_fornecedor');
        $pessoas        = $this->pessoaFacade->buscarPorNome($query, $tipoFornecedor);

        return ResponseFactory::ok($pessoas);
    }


    /**
     *
     * @SWG\Get(
     *     path="/api/pessoa/buscar_nome_contato_com_contrato/{query}",
     *     summary="Buscar pessoa por nome com contrato",
     *     description="Busca pessoas pelo nome",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna as pessoas"
     *     ),
     * )
     *
     * @FOSRest\Get("/pessoa/buscar_nome_contato_com_contrato/{query}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarNomeComContrato ($query)
    {
        $pessoas = $this->pessoaFacade->buscarPorNomeComContrato($query);

        return ResponseFactory::ok($pessoas);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/pessoa/criar",
     *     summary="Cria uma pessoa",
     *     description="Cria uma pessoa no banco",
     *     tags={"Pessoa"},
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
     * @FOSRest\RequestParam(name="franqueada",            strict=true, nullable=false, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                 strict=false, nullable=true, description="ID da tabela Banco", requirements="\d+")
     * @FOSRest\RequestParam(name="plano_conta",           strict=false, nullable=true, description="ID da tabela Plano Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="agencia",               strict=false, nullable=true, description="Número da Agência ", default="")
     * @FOSRest\RequestParam(name="conta",                 strict=false, nullable=true, description="Número da Conta ", default="")
     * @FOSRest\RequestParam(name="data_nascimento",       strict=false, nullable=true, description="Data Nascimento")
     * @FOSRest\RequestParam(name="numero_identidade",     strict=false, nullable=true, description="Numero da Identidade(RG) ", default="")
     * @FOSRest\RequestParam(name="orgao_emissor",         strict=false, nullable=true, description="Orgao Expeditor da Identidade", default="")
     * @FOSRest\RequestParam(name="sexo",                  strict=false, nullable=true, description="Genero se é Homem ou Mulher", default="N")
     * @FOSRest\RequestParam(name="estado_civil",          strict=false, nullable=true, description="Estado Civil", default="")
     * @FOSRest\RequestParam(name="tipo_pessoa",           strict=false, nullable=true, description="Tipo da Pessoa, se é fisica ou Juridica", default="F")
     * @FOSRest\RequestParam(name="observacao",            strict=false, nullable=true, description="Observações", default="")
     * @FOSRest\RequestParam(name="inscricao_estadual",    strict=false, nullable=true, description="Incrição Estadual", default="")
     * @FOSRest\RequestParam(name="inscricao_municipal",   strict=false, nullable=true, description="Inscrição Municipal", default="")
     * @FOSRest\RequestParam(name="nome_contato",          strict=false, nullable=true, description="Nome do Contato", default="")
     * @FOSRest\RequestParam(name="home_page",             strict=false, nullable=true, description="Home Page(url no caso)", default="")
     * @FOSRest\RequestParam(name="endereco",              strict=false, nullable=true, description="Endereco da Pessoa", default="")
     * @FOSRest\RequestParam(name="numero_endereco",       strict=false, nullable=true, description="Numero da Casa/Apto", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="bairro_endereco",       strict=false, nullable=true, description="Bairro da Pessoa", default="")
     * @FOSRest\RequestParam(name="complemento_endereco",  strict=false, nullable=true, description="Complemento", default="")
     * @FOSRest\RequestParam(name="cep_endereco",          strict=false, nullable=true, description="CEP da Pessoa", default="")
     * @FOSRest\RequestParam(name="email_preferencial",    strict=false, nullable=true, description="E-mail de preferencial", default="")
     * @FOSRest\RequestParam(name="email_contato",         strict=false, nullable=true, description="E-mail para contato", default="")
     * @FOSRest\RequestParam(name="email_profissional",    strict=false, nullable=true, description="E-mail do trabalho", default="")
     * @FOSRest\RequestParam(name="telefone_preferencial", strict=false, nullable=true, description="Telefone preferencial", default="")
     * @FOSRest\RequestParam(name="telefone_contato",      strict=false, nullable=true, description="Telefone para contato", default="")
     * @FOSRest\RequestParam(name="telefone_profissional", strict=false, nullable=true, description="Telefone do Trabalho", default="")
     * @FOSRest\RequestParam(name="negativado",            strict=true, nullable=false, description="Se a pessoa está negativado", default="0", requirements="\d")
     * @FOSRest\RequestParam(name="id_importado",          strict=false, nullable=true, description="ID que havia sido importada do sistema antigo", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="cnpj_cpf",              strict=true, nullable=true,  description="CPF/CNPJ")
     * @FOSRest\RequestParam(name="razao_social",          strict=false, nullable=true, description="Razao Social", default="")
     * @FOSRest\RequestParam(name="nome_fantasia",         strict=false, nullable=true, description="Nome Fantasia", default="")
     * @FOSRest\RequestParam(name="data_consulta_credito", strict=true, nullable=true,  description="Data Consulta Credito")
     * @FOSRest\RequestParam(name="estado",                strict=false, nullable=true, description="ID da tabela Estado", requirements="\d+")
     * @FOSRest\RequestParam(name="cidade",                strict=false, nullable=true, description="ID da tabela Cidade", requirements="\d+")
     *
     * @FOSRest\Post("/pessoa/criar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function criar(ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";

        $cpfCnpj =  preg_replace('/[^0-9]/', '', $parametros['cnpj_cpf'] ?? "999999999999");
         //verifica se a pessoa já existe no banco e se existir retorna este registro.
        $pessoaORM = $this->pessoaRepository->buscarPessoaSimples($cpfCnpj);

        if( $pessoaORM == null){
            $pessoaORM = $this->pessoaFacade->criar($mensagem, $parametros);
            if (empty($mensagem) === false) {
                return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
            }
        } else {
            $pessoaFranqueadaORM = $this->pessoaRepository->buscarCpfCnpj($cpfCnpj, null, true);
            if ($pessoaFranqueadaORM == null) {
                 // if ($parametros['franqueada'] == $pessoaORM)
                $mensagem = "Pessoa já cadastrada com o Nome de ".$pessoaORM->getNomeContato();
                return  ResponseFactory::badRequest('', $mensagem);
            }
        }

        $this->pessoaFacade->relacionaPessoaComFranqueadaAtual($pessoaORM);

        return ResponseFactory::ok(["pessoa" => $pessoaORM->getId()], "Registro criado com sucesso!");
    }

    /**
     *
     * @SWG\Patch(
     *     path="/api/pessoa/atualizar/{id}",
     *     summary="Atualiza um pessoa",
     *     description="Atualiza um pessoa no banco",
     *     tags={"Pessoa"},
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="204",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="franqueada",            strict=true, nullable=false, description="ID da tabela Franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="banco",                 strict=false, nullable=true, description="ID da tabela Banco", requirements="\d+")
     * @FOSRest\RequestParam(name="plano_conta",           strict=false, nullable=true, description="ID da tabela Plano Conta", requirements="\d+")
     * @FOSRest\RequestParam(name="agencia",               strict=false, nullable=true, description="Número da Agência ", default="")
     * @FOSRest\RequestParam(name="conta",                 strict=false, nullable=true, description="Número da Conta ", default="")
     * @FOSRest\RequestParam(name="data_nascimento",       strict=false, nullable=true, description="Data Nascimento")
     * @FOSRest\RequestParam(name="numero_identidade",     strict=false, nullable=true, description="Numero da identidade(RG) ", default="")
     * @FOSRest\RequestParam(name="orgao_emissor",         strict=false, nullable=true, description="Orgao Expeditor da Identidade", default="")
     * @FOSRest\RequestParam(name="sexo",                  strict=false, nullable=true, description="Genero se é Homem ou Mulher", default="N")
     * @FOSRest\RequestParam(name="estado_civil",          strict=false, nullable=true, description="Estado Civil", default="")
     * @FOSRest\RequestParam(name="tipo_pessoa",           strict=false, nullable=true, description="Tipo da Pessoa, se é fisica ou Juridica", default="F")
     * @FOSRest\RequestParam(name="observacao",            strict=false, nullable=true, description="Observações", default="")
     * @FOSRest\RequestParam(name="inscricao_estadual",    strict=false, nullable=true, description="Incrição Estadual", default="")
     * @FOSRest\RequestParam(name="inscricao_municipal",   strict=false, nullable=true, description="Inscrição Municipal", default="")
     * @FOSRest\RequestParam(name="nome_contato",          strict=false, nullable=true, description="Nome do Contato", default="")
     * @FOSRest\RequestParam(name="home_page",             strict=false, nullable=true, description="Home Page(url no caso)", default="")
     * @FOSRest\RequestParam(name="endereco",              strict=false, nullable=true, description="Endereco da Pessoa", default="")
     * @FOSRest\RequestParam(name="numero_endereco",       strict=false, nullable=true, description="Numero da Casa/Apto", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="bairro_endereco",       strict=false, nullable=true, description="Bairro da Pessoa", default="")
     * @FOSRest\RequestParam(name="complemento_endereco",  strict=false, nullable=true, description="Complemento", default="")
     * @FOSRest\RequestParam(name="cep_endereco",          strict=false, nullable=true, description="CEP da Pessoa", default="")
     * @FOSRest\RequestParam(name="email_preferencial",    strict=false, nullable=true, description="E-mail de preferencial", default="")
     * @FOSRest\RequestParam(name="email_contato",         strict=false, nullable=true, description="E-mail para contato", default="")
     * @FOSRest\RequestParam(name="email_profissional",    strict=false, nullable=true, description="E-mail do trabalho", default="")
     * @FOSRest\RequestParam(name="telefone_preferencial", strict=false, nullable=true, description="Telefone preferencial", default="")
     * @FOSRest\RequestParam(name="telefone_contato",      strict=false, nullable=true, description="Telefone para contato", default="")
     * @FOSRest\RequestParam(name="telefone_profissional", strict=false, nullable=true, description="Telefone do Trabalho", default="")
     * @FOSRest\RequestParam(name="negativado",            strict=true, nullable=false, description="Se a pessoa está negativado", default="0", requirements="\d")
     * @FOSRest\RequestParam(name="id_importado",          strict=false, nullable=true, description="ID que havia sido importada do sistema antigo", default="0", requirements="\d+")
     * @FOSRest\RequestParam(name="razao_social",          strict=false, nullable=true, description="Razao Social", default="")
     * @FOSRest\RequestParam(name="nome_fantasia",         strict=false, nullable=true, description="Nome Fantasia", default="")
     * @FOSRest\RequestParam(name="data_consulta_credito", strict=true, nullable=true,  description="Data Consulta Credito")
     * @FOSRest\RequestParam(name="estado",                strict=false, nullable=true, description="ID da tabela Estado", requirements="\d+")
     * @FOSRest\RequestParam(name="cidade",                strict=false, nullable=true, description="ID da tabela Cidade", requirements="\d+")
     * @FOSRest\RequestParam(name="cnpj_cpf",              strict=true, nullable=true,  description="CPF/CNPJ")
     *
     * @FOSRest\Patch("/pessoa/atualizar/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function atualizar($id, ParamFetcher $paramFetcher)
    {
        $parametros = $paramFetcher->all();
        $mensagem   = "";

        $parametros[ConstanteParametros::CHAVE_ID] = $id;
        $retorno = $this->pessoaFacade->atualizar($mensagem, $id, $parametros);
        if ($retorno === false) {
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagem);
        }

        $parametros['id'] = $id;
        return ResponseFactory::ok(['pessoa' => $parametros], 'Atualizado com sucesso');
    }

    /**
     *
     * @SWG\Delete(
     *     path="/api/pessoa/remover/{id}",
     *     summary="Remove uma pessoa",
     *     description="Remove uma pessoa no banco",
     *     tags={"Pessoa"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Delete("/pessoa/remover/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function excluir($id)
    {
        $mensagem = "";
        $retorno  = $this->pessoaFacade->remover($mensagem, $id);
        if ($retorno === false)
            return ResponseFactory::badRequest([], $mensagem);
        return ResponseFactory::ok([], "Excluido com sucesso");
    }


}
