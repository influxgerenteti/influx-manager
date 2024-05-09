<?php

namespace App\Facade\Principal;


use App\BO\Principal\ContaBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

/**
 *
 * @author Luiz A Costa
 */
class ContaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRepository;

    /**
     *
     * @var \App\BO\Principal\ContaBO
     */
    private $contaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->contaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Conta::class);
        $this->contaBO         = new ContaBO(self::getEntityManager());
    }

    /**
     * Executa o flush de operações
     *
     * @param string $mensagemErro
     */
    public function flush (&$mensagemErro)
    {
        self::flushSeguro($mensagemErro);
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
        $retornoRepositorio = $this->contaRepository->filtrarContaPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\Conta
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $this->contaBO->verificaContaIdExiste($this->contaRepository, $id, $mensagemErro, $objetoORM, true);

        return $objetoORM;
    }

    /**
     * Busca conta(s) pelos parametros passados
     *
     * @param array $parametros
     *
     * @return array|null retorna a conta com a id passada ou null
     */
    public function buscarPorParametros($parametros)
    {
        return $this->contaRepository->buscarPorParametros($parametros);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param bool $criarRegistro Se deve gravar o registro no banco dentro desta função
     *
     * @return null|\App\Entity\Principal\Conta
     */
    public function criar(&$mensagemErro, $parametros=[], $criarRegistro=true)
    {
        $objetoORM = null;
        if ($this->contaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Conta::class, true, $parametros);
            if ($criarRegistro === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            }

            if (empty($mensagemErro) === true) {
                $objetoORM->getFranqueada()->getUsuarios()->clear();
            }
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
        $objetoORM = null;
        if ($this->contaBO->verificaContaIdExiste($this->contaRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($this->contaBO->podeSalvar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
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
        $objetoORM = null;
        if ($this->contaBO->verificaContaIdExiste($this->contaRepository, $id, $mensagemErro, $objetoORM) === true) {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

     /**
     * Atualiza os saldos 
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function atualiza_saldos(&$mensagemErro, $id)
    {
        $objetoORM = null;
        if ($this->contaBO->verificaContaIdExiste($this->contaRepository, $id, $mensagemErro, $objetoORM) === true) {
            $this->contaBO->atualizaSaldos($objetoORM);
            // self::persistSeguro($objetoORM, $mensagemErro);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

}
