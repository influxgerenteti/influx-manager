<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Franqueada;
use App\Entity\Principal\Pessoa;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use DateInterval;
use DateTime;
use Exception;

/**
 *
 * @method Pessoa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pessoa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pessoa[]    findAll()
 * @method Pessoa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PessoaRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pessoa::class);
    }

    /**
     * Filtra a pessoa por pagina e numero de itens por pagina
     *
     * @param array $parametros Parametros enviados pela requisicao
     *
     * @return Pessoa[] Resultados em formato de array
     */
    public function filtrarPessoasPorPagina($parametros)
    {
        $opcoes = [];

        $queryBuilder = $this->createQueryBuilder("p");
        $queryBuilder->innerJoin('p.franqueadas', 'fra');
        $queryBuilder->addSelect("plc");
        $queryBuilder->addSelect("alu");
        $queryBuilder->addSelect("func");
        $queryBuilder->leftJoin('p.plano_conta', 'plc');
        $queryBuilder->leftJoin('p.alunos', 'alu');
        $queryBuilder->leftJoin('p.funcionarios', 'func');
        $queryBuilder->where('fra.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_EMPRESA]) === false) {
            $queryBuilder->andWhere('p.tipo_pessoa = :empresa');
            $queryBuilder->setParameter('empresa', 'J');
        }

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_CPF]) === false) {
            $cpfFormatado = str_replace(['.', '-'], '', $parametros[ConstanteParametros::CHAVE_FILTRO_CPF]);
            $queryBuilder->andWhere('p.cnpj_cpf = :cpf');
            $queryBuilder->setParameter('cpf', $cpfFormatado);
        }

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_CNPJ]) === false) {
            $cnpjFormatado = str_replace(['.', '/', '-'], '', $parametros[ConstanteParametros::CHAVE_FILTRO_CNPJ]);
            $queryBuilder->andWhere('p.cnpj_cpf = :cnpj');
            $queryBuilder->setParameter('cnpj', $cnpjFormatado);
        }

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_TELEFONE]) === false) {
            $telefoneFormatado = "%" . $parametros[ConstanteParametros::CHAVE_FILTRO_TELEFONE] . "%";

            /*
             * TODO: Implementar função REPLACE ou REGEXP_REPLACE no doctrine conforme link e fazer funcionar da forma abaixo ()
             * https://stackoverflow.com/questions/23825237/doctrine-how-to-use-replace-function
             * Doctrine tem acesso às funções do DQL, o resto tem que ser criado customizado:
             * https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/dql-doctrine-query-language.html#dql-functions
             *
             * *Com Replace, seria:
                $telefoneFormatado = str_replace(['(', ')', ' ', '-'], '', $parametros[ConstanteParametros::CHAVE_FILTRO_TELEFONE]);
                $telefoneFormatado = "%$telefoneFormatado%";
                $valorBuscar = "REPLACE(p.telefone_preferencial,'(','')";
                $valorBuscar = "REPLACE($valorBuscar,')','')";
                $valorBuscar = "REPLACE($valorBuscar,'-','')";
                $valorBuscar = "REPLACE($valorBuscar,' ','')";
                $queryBuilder->andWhere("$valorBuscar like :telefone_celular'");
             ** Com REGEXP_REPLACE seria:
                $telefoneFormatado = str_replace(['(', ')', ' ', '-'], '', $parametros[ConstanteParametros::CHAVE_FILTRO_TELEFONE]);
                $telefoneFormatado = "%$telefoneFormatado%";
                // $queryBuilder->andWhere("REGEXP_REPLACE(p.telefone_preferencial, '[^0-9]+', '') like :telefone_celular'");
             */

            $queryBuilder->andWhere("p.telefone_preferencial like :telefone_celular");
            $queryBuilder->setParameter('telefone_celular', $telefoneFormatado);
        } //end if

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_NOME]) === false) {
            $nomeFormatado = strtolower($parametros[ConstanteParametros::CHAVE_FILTRO_NOME]);
            $nomeFormatado = "%$nomeFormatado%";
            $queryBuilder->andWhere('LOWER(p.nome_contato) like :nome');
            $queryBuilder->setParameter('nome', $nomeFormatado);
        }

        if (empty($parametros[ConstanteParametros::CHAVE_TIPO_PESSOA]) === false) {
            // $queryBuilder->andWhere('p.tipo_pessoa = :tipo_pessoa');
            // $queryBuilder->setParameter('tipo_pessoa', $parametros[ConstanteParametros::CHAVE_TIPO_PESSOA]);
        }

        if ((bool) $parametros[ConstanteParametros::CHAVE_ALUNOS] === false) {
            $queryBuilder->andWhere('alu.id IS NULL');
            $queryBuilder->andWhere('func IS NULL');
        }

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_ALUNO]) === false) {
            $queryBuilder->addSelect('alu')->leftJoin('p.alunos', 'alu');
            $queryBuilder->andWhere('alu.id IS NOT NULL');
        }

        if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_FUNCIONARIO]) === false) {
            $queryBuilder->addSelect('func')->leftJoin('p.funcionarios', 'func');
            if (empty($parametros[ConstanteParametros::CHAVE_FILTRO_ALUNO]) === false) {
                $queryBuilder->orWhere('func.id IS NOT NULL');
            } else {
                $queryBuilder->andWhere('func.id IS NOT NULL');
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $arrayColunas = explode(";", $parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]);
            for ($i = 0; $i < count($arrayColunas); $i++) {
                $queryBuilder->addOrderBy(trim($arrayColunas[$i]), $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            }

            $opcoes[Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        /*
         * Estava ocorrendo um bug com a lista filtrada onde não retornava a lista, apenas a quantidade
         * Para manter o padrão de retorno, está sendo retornado sempre o array de pessoas resultante
         */

        if (isset($parametros["eh_lista_filtrada"]) === true && empty($parametros["eh_lista_filtrada"]) === false) {
            return FunctionHelper::retornaArrayNull($queryBuilder);
        }

        $retorno = FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $parametros[ConstanteParametros::CHAVE_ITENS_POR_PAGINA], $opcoes);
        return $retorno->getItems();
    }

    /**
     * Busca todos os registros da tabela pessoa atraves do CPF/CNPJ informado
     *
     * @param string $cpfCnpj CPF/CNPJ
     * @param integer $ignorarID ID da pessoa a ignorar
     * @param boolean $isCpf Flag para identificar se eh para buscar por CPF ou CNPJ
     *
     * @return Pessoa[]|NULL
     */
    public function buscarCpfCnpj($cpfCnpj, $ignorarID = null, $isCpf = true)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p');
        $queryBuilder->join('p.franqueadas', 'pessoaFranqueadas');
        $queryBuilder->where('pessoaFranqueadas = :franqueada');
        $queryBuilder->andWhere('p.cnpj_cpf LIKE :cpfcnpj');
        $queryBuilder->andWhere('p.tipo_pessoa = :iscpf');

        if (is_null($ignorarID) === false) {
            $queryBuilder->andWhere('p.id <> :id');
            $queryBuilder->setParameter('id', $ignorarID);
        }

        $queryBuilder->setParameter('cpfcnpj', '%' . $cpfCnpj . '%');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if ($isCpf === true) {
            $queryBuilder->setParameter('iscpf', 'F');
        } else {
            $queryBuilder->setParameter('iscpf', 'J');
        }

        return FunctionHelper::retornaArrayNull($queryBuilder);
    }

    public function buscarPessoaSimples($cpfCnpj)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p');
        $queryBuilder->andWhere('p.cnpj_cpf = :cpfcnpj');
        $queryBuilder->setParameter('cpfcnpj', $cpfCnpj);

        $hydrate = Query::HYDRATE_OBJECT;

        return $queryBuilder->getQuery()->getOneOrNullResult($hydrate);
    }

    /**
     * Busca pessoa por ID
     *
     * @param integer $id
     * @param boolean $retornarObjeto Se deve retornar como objeto
     *
     * @return Pessoa|null
     */
    public function buscarPorId($id, $retornarObjeto = false)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->addSelect("plc")
            ->addSelect("est")
            ->addSelect("cid")
            ->addSelect("bc")
            ->join('p.franqueadas', 'franqueadas')
            ->leftJoin('p.plano_conta', 'plc')
            ->leftJoin('p.estado', 'est')
            ->leftJoin('p.cidade', 'cid')
            ->leftJoin('p.banco', 'bc')
            ->where('franqueadas = :franqueada')
            ->andWhere('p.id = :id')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->setParameter('id', $id);

        if ($retornarObjeto === false) {
            $hydrate = Query::HYDRATE_ARRAY;
        } else {
            $hydrate = Query::HYDRATE_OBJECT;
        }

        return $queryBuilder->getQuery()->getOneOrNullResult($hydrate);
    }

    /**
     * Busca pessoas por nome relacionado com contrato
     *
     * @param string $nome
     *
     * @return Pessoa[]
     */
    public function buscarPorNomeComContrato($nome)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p.nome_contato');
        $queryBuilder->addSelect('rf.id');
        $queryBuilder->join('p.alunos', 'aluno');
        $queryBuilder->join('aluno.contratos', 'contrato');
        $queryBuilder->join('aluno.responsavel_financeiro_pessoa', 'rf');
        $queryBuilder->where('p.nome_contato LIKE :nome');
        $queryBuilder->andWhere('contrato.franqueada = :franqueada');
        $queryBuilder->setParameter('nome', "%$nome%");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->distinct();

        return FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca todas as pessoas da franqueada informada
     *
     * @param String $nome
     *
     * @return array
     */
    public function buscarEmpresaPorNome($nome)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p');
        $queryBuilder->join('p.franqueadas', 'franqueada');
        $queryBuilder->where('p.nome_contato LIKE :nome');
        $queryBuilder->andWhere('franqueada = :franqueada');
        // Removido o filtro de PJ para no Relatório Saídas de Estoque aparecer os responsáveis financeiros.
        // $queryBuilder->andWhere("p.tipo_pessoa = 'J'");
        $queryBuilder->setParameter('nome', "%$nome%");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->distinct();

        return FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Busca todas pessoas que tem o telefone passado cadastrado
     *
     * @param array $parametros
     *
     * @return array|null
     */
    public function buscarPorTelefone($parametros)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p');
        $queryBuilder->join('p.franqueadas', 'franqueada');
        $queryBuilder->where('franqueada = :franqueada');
        $queryBuilder->andWhere("p.tipo_pessoa = 'F'");
        $queryBuilder->setParameter('franqueada', $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq("p.telefone_preferencial", ":telefone"),
                $queryBuilder->expr()->eq("p.telefone_contato", ":telefone"),
                $queryBuilder->expr()->eq("p.telefone_profissional", ":telefone")
            )
        );

        $queryBuilder->setParameter('telefone', $parametros[ConstanteParametros::CHAVE_TELEFONE]);
        $queryBuilder->distinct();

        return FunctionHelper::retornaArrayNull($queryBuilder);
    }


    /**
     * Busca pessoas por nome
     *
     * @param string $nome
     * @param array $tipoFornecedor
     *
     * @return Pessoa[]
     */
    public function buscarPorNome($nome, $tipoFornecedor = [])
    {
        $queryBuilder = $this->createQueryBuilder('aPessoa');
        $queryBuilder->leftJoin("aPessoa.franqueadas", "franqueada");
        $orWheres = [];

        if (is_null($tipoFornecedor) === false && $tipoFornecedor !== []) {
            if (in_array(ConstanteParametros::CHAVE_ALUNO, $tipoFornecedor) === true) {
                $queryBuilder->addSelect('aluno');
                $queryBuilder->leftJoin('aPessoa.alunos', 'aluno');
                $orWheres[] = $queryBuilder->expr()->isNotNull('aluno.pessoa');
            }

            if (in_array(ConstanteParametros::CHAVE_FUNCIONARIO, $tipoFornecedor) === true) {
                $queryBuilder->addSelect('funcionario');
                $queryBuilder->leftJoin('aPessoa.funcionarios', 'funcionario');
                $orWheres[] = $queryBuilder->expr()->isNotNull('funcionario.pessoa');
            }

           if (in_array(ConstanteParametros::CHAVE_FORNECEDOR, $tipoFornecedor) === true) {
            $tipoF = "'J', 'F'";
               $orWheres[] = $queryBuilder->expr()->in('aPessoa.tipo_pessoa', $tipoF);
              // $queryBuilder->setParameter('tipoPessoa',$tipoF);
           }
           

            if (in_array(ConstanteParametros::CHAVE_INSTRUTOR_FLAG, $tipoFornecedor) === true) {
                $queryBuilder->leftJoin('aPessoa.funcionarios', 'instrutor');
                $orWheres[] = $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->isNotNull('instrutor.pessoa'),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('instrutor.instrutor', 1),
                        $queryBuilder->expr()->eq('instrutor.instrutor_personal', 1)
                    )
                );
            }
        } //end if

        $queryBuilder->andWhere('aPessoa.nome_contato LIKE :nome');
        $queryBuilder->andWhere('franqueada.id = :franqueada');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(...$orWheres)
            // Aplicado spread operator
        );

        $queryBuilder->setParameter('nome', "%$nome%");
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->distinct();

        return FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorio($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("pessoa");
        $queryBuilder->select('pessoa.id');
        $queryBuilder->leftJoin('pessoa.alunos', 'aluno');
        $queryBuilder->leftJoin('pessoa.funcionarios', 'funcionario');
        $queryBuilder->leftJoin('aluno.contratos', 'contrato');
        $queryBuilder->leftJoin('pessoa.franqueadas', 'f');

        $queryBuilder->andWhere("pessoa.excluido = 0");
        $queryBuilder->andWhere("pessoa.tipo_pessoa = 'F'");

        $queryBuilder->andWhere('f.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if (is_null($parametros["mes"]) === false) {
            if ($parametros["mes"] === '0') {
                $queryBuilder->andWhere('month(pessoa.data_nascimento) = month(CURRENT_DATE())');
            } else if ($parametros["mes"] === '1') {
                $queryBuilder->andWhere('month(pessoa.data_nascimento) = month(CURRENT_DATE())+1');
            }
        }

        if (is_null($parametros["turma"]) === false) {
            $queryBuilder->andWhere('contrato.turma = :turma');
            $queryBuilder->setParameter('turma', $parametros["turma"]);
        }

        if (is_null($parametros["situacao"]) === false) {
            if ($parametros["situacao"] === '0') {
                // ativos
                $queryBuilder->andWhere("aluno.situacao <> 'INA' or funcionario.situacao <> 'I' ");
            } else {
                // inativos
                $queryBuilder->andWhere("aluno.situacao = 'INA' or funcionario.situacao = 'I' ");
            }
        }

        // Filtra e substitui a query para passar ao Jasper
        $sql = $queryBuilder->getQuery()->getSQL();

        $sql = preg_replace('/.*WHERE\s(.*)$/', '$1', $sql);

        // Seleciona somente os wheres
        $sql = preg_replace('/p0_/', 'pessoa', $sql);
        $sql = preg_replace('/a1_/', 'aluno', $sql);
        $sql = preg_replace('/f2_/', 'funcionario', $sql);
        $sql = preg_replace('/c3_/', 'contrato', $sql);
        $sql = preg_replace('/f4_/', 'franqueada', $sql);
        $sql = preg_replace('/p5_/', 'pessoa_franqueada', $sql);

        // Substituição de parâmetros
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $parameter) {
            $param = $parameter->getValue();
            $sql   = preg_replace('/\?/', "'$param'", $sql, 1);
        }

        return $sql;
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param array $parametros
     *
     * @return array
     */
    public function prepararDadosRelatorioAniversariantes($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("pessoa");
        $queryBuilder->select('pessoa.id', 'pessoa.nome_contato', 'pessoa.data_nascimento');
        $queryBuilder->leftJoin('pessoa.alunos', 'aluno');
        $queryBuilder->leftJoin('pessoa.funcionarios', 'funcionario');
        $queryBuilder->leftJoin('aluno.contratos', 'contrato');
        $queryBuilder->leftJoin('pessoa.franqueadas', 'f');

        $queryBuilder->andWhere("pessoa.excluido = 0");
        $queryBuilder->andWhere("pessoa.tipo_pessoa = 'F'");

        $queryBuilder->andWhere('f.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
        if (is_null($parametros["mes"]) === false) {
            if ($parametros["mes"] === '0') {
                $queryBuilder->andWhere('month(pessoa.data_nascimento) = month(CURRENT_DATE())');
            } else if ($parametros["mes"] === '1') {
                $queryBuilder->andWhere('month(pessoa.data_nascimento) = month(CURRENT_DATE())+1');
            }
        }

        if (is_null($parametros["turma"]) === false) {
            $queryBuilder->andWhere('contrato.turma = :turma');
            $queryBuilder->setParameter('turma', $parametros["turma"]);
        }

        if (is_null($parametros["situacao"]) === false) {
            if ($parametros["situacao"] === '0') {
                // ativos
                $queryBuilder->andWhere("aluno.situacao <> 'INA' or funcionario.situacao <> 'I' ");
            } else {
                // inativos
                $queryBuilder->andWhere("aluno.situacao = 'INA' or funcionario.situacao = 'I' ");
            }
        }

        $queryBuilder->orderBy('month(pessoa.data_nascimento)', 'ASC');
        $queryBuilder->addOrderBy('day(pessoa.data_nascimento)', 'ASC');
        $queryBuilder->distinct();

        return  FunctionHelper::retornaArrayNull($queryBuilder);
    }

    public function buscarDadosRelatorioDadosCadastro($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("pessoa")
            ->select(
                [
                    'pessoa',
                    'aluno',
                    'classificacao',
                    'interessados',
                    'consultor',
                    'consultor_pessoa',
                    'responsavel_financeiro_pessoa',
                    'responsavel_financeiro_relacionamento_aluno',
                    'responsavel_didatico_pessoa',
                    'responsavel_didatico_relacionamento_aluno',
                    'contrato',
                    'turma',
                    'curso',
                    'cidade',
                    'estado',
                    'escolaridade',
                ]
            )
            ->join('pessoa.alunos', 'aluno')
            ->leftJoin('pessoa.franqueadas', 'f')
            ->leftJoin('pessoa.cidade', 'cidade')
            ->leftJoin('pessoa.estado', 'estado')
            ->leftJoin('aluno.escolaridade', 'escolaridade')
            ->leftJoin('aluno.classificacao_aluno', 'classificacao')
            ->leftJoin('aluno.interessados', 'interessados')
            ->leftJoin('interessados.consultor_funcionario', 'consultor')
            ->leftJoin('consultor.pessoa', 'consultor_pessoa')
            ->leftJoin('aluno.responsavel_financeiro_pessoa', 'responsavel_financeiro_pessoa')
            ->leftJoin('aluno.responsavel_financeiro_relacionamento_aluno', 'responsavel_financeiro_relacionamento_aluno')
            ->leftJoin('aluno.responsavel_didatico_pessoa', 'responsavel_didatico_pessoa')
            ->leftJoin('aluno.responsavel_didatico_relacionamento_aluno', 'responsavel_didatico_relacionamento_aluno')
            ->leftJoin('aluno.contratos', 'contrato', 'WITH', "contrato.situacao LIKE 'V'")
            ->leftJoin('contrato.turma', 'turma')
            ->leftJoin('turma.curso', 'curso')
            ->andWhere("pessoa.excluido = 0")
            ->andWhere("pessoa.tipo_pessoa = 'F'")
            ->andWhere('f.id = :franqueada')
            ->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID)
            ->orderBy('aluno.id', 'DESC');

        if (isset($parametros['pessoa_id'])) {
            $queryBuilder->andWhere('pessoa = :filtro_pessoa')
                ->setParameter('filtro_pessoa', $parametros['pessoa_id']);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SITUACAO])) {
            $queryBuilder->andWhere("aluno.situacao IN(:situacao)")
                ->setParameter('situacao', $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_INICIAL])) {
            $dataInicial = strtotime(str_replace("/", "-", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_INICIAL] . " 00:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("pessoa.data_cadastramento >= :data_cadastro_inicial");
            $queryBuilder->setParameter('data_cadastro_inicial', $dataInicial);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_FINAL])) {
            $dataFinal = strtotime(str_replace("/", "-", $parametros[ConstanteParametros::CHAVE_DATA_CADASTRO_FINAL] . " 23:59:59"));
            $dataFinal = date('Y-m-d H:i:s', $dataFinal);
            $queryBuilder->andWhere("pessoa.data_cadastramento <= :data_cadastro_final");
            $queryBuilder->setParameter('data_cadastro_final', $dataFinal);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO])) {
            $queryBuilder->andWhere("aluno.classificacao_aluno = :classificacao")
                ->setParameter('classificacao', $parametros[ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_BAIRRO])) {
            $queryBuilder->andWhere("pessoa.bairro_endereco LIKE :bairro")
                ->setParameter('bairro', '%' . $parametros[ConstanteParametros::CHAVE_BAIRRO] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CONSULTOR])) {
            $queryBuilder->andWhere("consultor IN(:consultor)")
                ->setParameter('consultor', $parametros[ConstanteParametros::CHAVE_CONSULTOR]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG])) {
            $queryBuilder->andWhere("consultor IN(:atendente)")
                ->setParameter('atendente', $parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CURSO])) {
            $queryBuilder->andWhere("curso IN(:curso)")
                ->setParameter('curso', $parametros[ConstanteParametros::CHAVE_CURSO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TELEFONE_PREFERENCIAL])) {
            $queryBuilder->andWhere("pessoa.telefone_preferencial LIKE :telefone")
                ->setParameter('telefone', '%' . $parametros[ConstanteParametros::CHAVE_TELEFONE_PREFERENCIAL] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ENDERECO])) {
            $queryBuilder->andWhere("pessoa.endereco LIKE :endereco")
                ->setParameter('endereco', '%' . $parametros[ConstanteParametros::CHAVE_ENDERECO] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CEP])) {
            $queryBuilder->andWhere("pessoa.cep_endereco LIKE :cep")
                ->setParameter('cep', '%' . $parametros[ConstanteParametros::CHAVE_CEP] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CIDADE])) {
            $queryBuilder->andWhere("cidade.nome LIKE :cidade")
                ->setParameter('cidade', '%' . $parametros[ConstanteParametros::CHAVE_CIDADE] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ESTADO_UF])) {
            $queryBuilder->andwhere("estado = :estado")
                ->setParameter('estado', $parametros[ConstanteParametros::CHAVE_ESTADO_UF]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_DE])) {
            $dataInicial = strtotime(str_replace("/", "-", $parametros[ConstanteParametros::CHAVE_DATA_ANIVERSARIO_DE] . " 12:00:00"));
            $dataInicial = date('Y-m-d H:i:s', $dataInicial);
            $queryBuilder->andWhere("pessoa.data_nascimento = :data_aniversario")
                ->setParameter('data_aniversario', $dataInicial);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_ESCOLARIDADE])) {
            $queryBuilder->andWhere("aluno.escolaridade = :escolaridade")
                ->setParameter('escolaridade', $parametros[ConstanteParametros::CHAVE_ESCOLARIDADE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO])) {
            $queryBuilder->andWhere("pessoa.telefone_contato LIKE :telefone_contato")
                ->setParameter('telefone_contato', '%' . $parametros[ConstanteParametros::CHAVE_TELEFONE_CONTATO] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TELEFONE_PROFISSIONAL])) {
            $queryBuilder->andWhere("pessoa.telefone_profissional LIKE :telefone_profissional")
                ->setParameter('telefone_profissional', '%' . $parametros[ConstanteParametros::CHAVE_TELEFONE_PROFISSIONAL] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CODIGO_MATRICULA])) {
            $queryBuilder->andWhere("aluno = :codigo_matricula")
                ->setParameter('codigo_matricula', $parametros[ConstanteParametros::CHAVE_CODIGO_MATRICULA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO])) {
            $queryBuilder->andWhere("pessoa.observacao LIKE :observacao")
                ->setParameter('observacao', '%' . $parametros[ConstanteParametros::CHAVE_OBSERVACAO] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_PESSOA_SEXO])) {
            $queryBuilder->andWhere("pessoa.sexo = :pessoa_sexo")
                ->setParameter('pessoa_sexo', $parametros[ConstanteParametros::CHAVE_PESSOA_SEXO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA])) {
            $queryBuilder->andWhere("aluno.responsavel_didatico_pessoa = :responsavel_didatico")
                ->setParameter('responsavel_didatico', $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_DIDATICO_PESSOA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA])) {
            $queryBuilder->andWhere("aluno.responsavel_financeiro_pessoa = :responsavel_financeiro")
                ->setParameter("responsavel_financeiro", $parametros[ConstanteParametros::CHAVE_RESPONSAVEL_FINANCEIRO_PESSOA]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_CPF])) {
            $queryBuilder->andWhere("pessoa.cnpj_cpf LIKE :cpf")
                ->setParameter('cpf', '%' . $parametros[ConstanteParametros::CHAVE_CPF] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_NUMERO_IDENTIDADE])) {
            $queryBuilder->andWhere("pessoa.numero_identidade LIKE :numero_identidade")
                ->setParameter('numero_identidade', '%' . $parametros[ConstanteParametros::CHAVE_NUMERO_IDENTIDADE] . '%');
        }

        if (isset($parametros[ConstanteParametros::CHAVE_IDADE_MINIMA])) {
            $intervalo  = new DateInterval('P' . $parametros[ConstanteParametros::CHAVE_IDADE_MINIMA] . 'Y');
            $dataMinima = new DateTime();
            $dataMinima->sub($intervalo);

            $queryBuilder->andWhere("pessoa.data_nascimento <= :idade_minima")
                ->setParameter('idade_minima', $dataMinima->format('Y-m-d'));
        }

        if (isset($parametros[ConstanteParametros::CHAVE_IDADE_MAXIMA])) {
            $intervalo  = new DateInterval('P' . $parametros[ConstanteParametros::CHAVE_IDADE_MAXIMA] . 'Y');
            $dataMinima = new DateTime();
            $dataMinima->sub($intervalo);

            $queryBuilder->andWhere("pessoa.data_nascimento >= :idade_maxima")
                ->setParameter('idade_maxima', $dataMinima->format('Y-m-d'));
        }

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    public function buscarPessoaFranqueadaSponteId($sponteId)
    {
        try {
            $queryBuilder = $this->createQueryBuilder("pessoa");
            $queryBuilder
                ->andWhere('pessoa.sponte_id = :sponteId');

            $queryBuilder->setParameter('sponteId', $sponteId);
            $result = $queryBuilder->getQuery()->getOneOrNullResult();
            // if(count($result) > 1){
            //     die;
            // }
        } catch (Exception $e) {
            echo $queryBuilder->getQuery()->getSQL();
            die;
            //throw $th;
        }

        // try {
        //     $queryBuilder = $this->createQueryBuilder("pessoa");
        // $queryBuilder
        //     ->join('pessoa.franqueadas', 'f')
        //     ->andWhere('pessoa.sponte_id = :sponte')
        //     ->andWhere('f.sponte_id = :franqueada_sponte');

        // $queryBuilder->setParameter('franqueada_sponte', $escola->getSponteId());
        // $queryBuilder->setParameter('sponte', $responsavelID);
        // $result = $queryBuilder->getQuery()->getResult();
        // if(count($result) > 1){
        //     die;
        // }
        // } catch (Exception $e) {
        //     echo $queryBuilder->getQuery()->getSQL();            
        //     die;
        //     //throw $th;
        // }


        // if(count($result) > 0){
        //     return $result[0];
        // }
        return $result;
    }
}
