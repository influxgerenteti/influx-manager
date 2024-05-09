<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\FuncionarioDisponibilidadeBO;

/**
 *
 * @author Luiz A Costa
 */
class FuncionarioDisponibilidadeFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FuncionarioDisponibilidadeRepository
     */
    private $funcionarioDisponibilidadeRepository;

    /**
     *
     * @var \App\BO\Principal\FuncionarioDisponibilidadeBO
     */
    private $funcionarioDisponibilidadeBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->funcionarioDisponibilidadeRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FuncionarioDisponibilidade::class);
        $this->funcionarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->funcionarioDisponibilidadeBO = new FuncionarioDisponibilidadeBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->funcionarioDisponibilidadeRepository->filtrarFuncionarioDisponibilidadePorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->funcionarioDisponibilidadeRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Funcionario Disponibilidade não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Busca registros pelo funcionário
     *
     * @param int $funcionario
     *
     * @return array
     */
    public function buscarPorFuncionario ($funcionario)
    {
        $disponibilidades = $this->funcionarioDisponibilidadeRepository->buscarPorFuncionario($funcionario);

        return [
            ConstanteParametros::CHAVE_TOTAL => count($disponibilidades),
            ConstanteParametros::CHAVE_ITENS => $disponibilidades,
        ];
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\FuncionarioDisponibilidade
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->funcionarioDisponibilidadeBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FuncionarioDisponibilidade::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->funcionarioDisponibilidadeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario Disponibilidade não encontrado na base de dados.";
        } else {
            if ($this->funcionarioDisponibilidadeBO->podeAlterar($parametros, $mensagemErro, $objetoORM) === true) {
                $this->funcionarioDisponibilidadeBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
                if (empty($mensagemErro) === true) {
                    self::flushSeguro($mensagemErro);
                }
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Atualiza múltiplos registros de disponibilidades
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizarMultiplos (&$mensagemErro, $parametros=[])
    {
        $disponibilidades = $parametros[ConstanteParametros::CHAVE_DISPONIBILIDADES];
        $funcionario      = $parametros[ConstanteParametros::CHAVE_FUNCIONARIO];

        $funcionarioORM = $this->funcionarioRepository->find($funcionario);
        if (is_null($funcionarioORM) === true) {
            $mensagemErro = 'Funcionário não encontrado.';
            return false;
        }

        $remover        = $funcionarioORM->getDisponibilidades();
        $itensPresentes = [];

        if (isset($disponibilidades) === true && is_array($disponibilidades) === true) {
            foreach ($disponibilidades as $disponibilidade) {
                $disponibilidade[ConstanteParametros::CHAVE_FUNCIONARIO] = $funcionario;
                if (empty($disponibilidade[ConstanteParametros::CHAVE_ID]) === true) {
                    $o = $this->criar($mensagemErro, $disponibilidade);
                    $itensPresentes[$o->getId()] = true;
                } else {
                    $itensPresentes[$disponibilidade[ConstanteParametros::CHAVE_ID]] = true;
                    $this->atualizar($mensagemErro, $disponibilidade[ConstanteParametros::CHAVE_ID], $disponibilidade);
                }
            }
        }

        foreach ($remover as $itemRemover) {
            if (isset($itensPresentes[$itemRemover->getId()]) === false) {
                $this->remover($mensagemErro, $itemRemover->getId());
            }
        }

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
            return true;
        }

        return false;
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = $this->funcionarioDisponibilidadeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario Disponibilidade não encontrado na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
