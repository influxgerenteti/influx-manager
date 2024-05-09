<?php

namespace App\Facade\Principal;
use App\Helper\SituacoesSistema;
use App\BO\Principal\PlanoContaBO;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Marcelo André Naegeler
 */
class PlanoContaFacade extends GenericFacade
{
    /**
     *
     * @var \App\BO\Principal\PlanoContaBO
     */
    private $planoContaBO;

    /**
     *
     * @var \App\Repository\Principal\PlanoContaRepository
     */
    private $planoContaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->planoContaBO         = new PlanoContaBO(self::getEntityManager());
        $this->planoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoConta::class);
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @return array
     */
    public function listar()
    {
        return $this->planoContaRepository->buscarPlanosConta();
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro
     * @param int $id Chave primária do registro
     *
     * @return null|\App\Entity\Principal\PlanoConta
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $planoContaORM = $this->planoContaRepository->buscarRegistroPorId($id);
        if (empty($planoContaORM) === true) {
            $mensagemErro = "Plano de conta não encontrado na base de dados.";
        }

        return $planoContaORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro
     * @param array $parametros Parâmetros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\PlanoConta
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->planoContaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PlanoConta::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que irão ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {

        $objetoORM = null;
        if ($this->planoContaBO->verificaPlanoContaExiste($this->planoContaRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($this->planoContaBO->podeSalvar($parametros, $mensagemErro, true) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primária do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = null;
        if ($this->planoContaBO->verificaPlanoContaExiste($this->planoContaRepository, $id, $mensagemErro, $objetoORM) === true) {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_REMOVIDO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
