<?php

namespace App\Facade\Principal;


use App\BO\Principal\PessoaBO;
use App\Entity\Principal\Pessoa;
use App\Entity\Principal\Franqueada;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz A Costa
 */
class PessoaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PessoaRepository
     */
    private $pessoaRepository;



    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\BO\Principal\PessoaBO
     */
    private $pessoaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
        $this->pessoaRepository = self::getEntityManager()->getRepository(Pessoa::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(Franqueada::class);
        $this->pessoaBO = new PessoaBO(self::getEntityManager());
    }

    /**
     * Atualiza o registro da tabela Pessoa com os parametros(campos) informados
     *
     * @param string $mensagemErro Mensagem de erro a ser retornada para o front-end quando ocorrer um erro
     * @param integer $id Chave primaria da tabela Pessoa para buscar o registro a ser removido
     * @param array $parametros Parametros enviados pela requisicao
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros)
    {
        $pessoaORM = null;
        if ($this->pessoaBO->verificaPessoaExiste($this->pessoaRepository, $id, $mensagemErro, $pessoaORM, true) === true) {
            if ($this->pessoaBO->podeSalvar($parametros, $mensagemErro) === true) {
                $this->pessoaBO->configuraParametros($parametros, $pessoaORM);
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Busca um registro da tabela Pessoa atraves da ID
     *
     * @param string $mensagemErro Mensagem que ira ser retornada para o front em caso de erro
     * @param integer $id Chave primaria da tabela Pessoa para buscar o registro
     *
     * @return NULL|\App\Entity\Principal\Pessoa
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $pessoaORM = null;
        $this->pessoaBO->verificaPessoaExiste($this->pessoaRepository, $id, $mensagemErro, $pessoaORM);
        return $pessoaORM;
    }

    /**
     * Busca um registro da tabela Pessoa atraves do CPF/CNPJ informado
     *
     * @param string $mensagemErro Mensagem que ira ser retornada para o front em caso de erro
     * @param string $cpfCnpj CPF/CNPJ a ser consultado no banco de dados
     * @param boolean $isCpf Flag para identificar se a pesquisa eh para pesquisar por CPF ou CNPJ
     *
     * @return NULL|\App\Entity\Principal\Pessoa[]
     */
    public function buscarPorCpfCnpj(&$mensagemErro, $cpfCnpj, $isCpf = true)
    {
        $pessoaCollection = null;
        $this->pessoaBO->buscaPessoasExistentes($this->pessoaRepository, $cpfCnpj, $mensagemErro, $pessoaCollection, $isCpf);
        return $pessoaCollection;
    }

    /**
     * Busca registros de pessoas com contrato com base no nome informado
     *
     * @param string $nome Nome da pessoa a ser buscado
     *
     * @return \App\Entity\Principal\Pessoa[]
     */
    public function buscarPorNomeComContrato($nome)
    {
        return $this->pessoaRepository->buscarPorNomeComContrato($nome);
    }

    /**
     * Busca registros de pessoas
     *
     * @param string $nome Nome da pessoa a ser buscado
     *
     * @return \App\Entity\Principal\Pessoa[]
     */
    public function buscarEmpresaPorNome($nome)
    {
        return $this->pessoaRepository->buscarEmpresaPorNome($nome);
    }

    public  function validaMaiorIdade($pessoa){
        if ((isset($pessoa['data_nascimento']) === true) && (empty($pessoa['data_nascimento']) === false)) {
            $diff = $pessoa['data_nascimento']->diff(new \DateTime());
            if ($diff->y >= 18) {
                return true;
            }
        }

        if ((isset($pessoa['alunos']) === true) && ($pessoa['alunos']['emancipado'] === true)) {
            return true;
        }

        return false;
    }

    /**
     * Busca registros de pessoas
     *
     * @param string $nome Nome da pessoa a ser buscado
     * @param array $tipoFornecedor Tipo de fornecedor a filtrar
     *
     * @return \App\Entity\Principal\Pessoa[]
     */
    public function buscarPorNome($nome, $tipoFornecedor = [])
    {
        $result = $this->pessoaRepository->buscarPorNome($nome, $tipoFornecedor);

        // TODO: Refatorar para Doctrine
        // Feio porém não foi possível fazer no Doctrine
        if (is_null($result) === false) {
            if ($tipoFornecedor === null) {
                $tipoFornecedor = [];
            }

            foreach ($result as &$res) {
                $res["tipo_fornecedor"] = $tipoFornecedor;
            }

            return array_values(
                array_filter(
                $result,
                function ($pessoa) {

                $juridicaOk = $pessoa['tipo_pessoa'] === 'J';    
                // if ($pessoa['tipo_pessoa'] === 'J') {
                //     return true;
                // }

                $adultoOk = $this->validaMaiorIdade($pessoa);
                

                $tipoFornecedorAluno = in_array('aluno', $pessoa["tipo_fornecedor"]) === true;

                if($juridicaOk || $adultoOk || $tipoFornecedorAluno){
                    return true;
                }

                return false;
            }
            )
            );
        }
        else {
            return [];
        } //end if

    }

    /**
     * Cria um registro na tabela Pessoa
     *
     * @param string $mensagemErro Mensagem que ira ser retornada para o front em caso de erro
     * @param array $parametros Parametros enviados pela requisicao
     *
     * @return \App\Entity\Principal\Pessoa|NULL $objetoORM
     */
    public function criar(&$mensagemErro, $parametros)
    {
        $objetoORM = null;
        if ($this->pessoaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Pessoa::class , true, $parametros);
            $this->pessoaBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    public function relacionaPessoaComFranqueadaAtual($pessoaORM)
    {
        $franqueadaId = VariaveisCompartilhadas::$franqueadaID;

        $franqueadaORM = $this->franqueadaRepository->find($franqueadaId);

        if (get_class($franqueadaORM) === \App\Entity\Principal\Franqueada::class) {
            $pessoaORM->addFranqueada($franqueadaORM);
            self::flushSeguro($mensagemErro);
        }
    }

    /**
     *
     * @param string $mensagemErro Mensagem que ira ser retornada para o front em caso de erro
     * @param array $parametros Parametros enviados pela requisicao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->pessoaRepository->filtrarPessoasPorPagina($parametros);
        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => count($retornoRepositorio),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio,
        ];
        return $retorno;
    }

    /**
     * Altera a Flag de "Excluido" para "True" indicando que o registro esta excluido
     *
     * @param string $mensagemErro Mensagem de erro a ser retornada para o front-end quando ocorrer um erro
     * @param integer $id Chave primaria da tabela Pessoa para buscar o registro a ser removido
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $pessoaORM = null;
        if ($this->pessoaBO->verificaPessoaExiste($this->pessoaRepository, $id, $mensagemErro, $pessoaORM) === true) {
            $pessoaORM->setExcluido(true);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Busca se um dado telefone está cadastrado como o de alguma pessoa
     *
     * @param string $mensagemErro Mensagem de erro a ser retornada para o front-end quando ocorrer um erro
     * @param array $parametros
     *
     * @return boolean
     */
    public function buscarTelefoneEstaCadastrado(&$mensagemErro, $parametros)
    {
        $pessoas = $this->pessoaRepository->buscarPorTelefone($parametros);

        return !is_null($pessoas);
    }



    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros   
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->pessoaRepository->prepararDadosRelatorio($parametros);
    }


        /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros   prepararDadosRelatorioAniversariantes
     *
     * @return string
     */
    public function gerarDadosRelatorioAniversariantes($parametros)
    {
        return $this->pessoaRepository->prepararDadosRelatorioAniversariantes($parametros);
    }

    public function buscarDadosRelatorioDadosCadastro($parametros){
        return $this->pessoaRepository->buscarDadosRelatorioDadosCadastro($parametros);
    }
}
