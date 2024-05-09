<?php

namespace App\Facade\Principal;

use App\BO\Principal\PlanoContaBO;
use App\BO\Principal\ContaPagarBO;
use App\BO\Principal\PlanoContasContaPagarBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Marcelo André Naegeler
 */
class PlanoContasContaPagarFacade extends GenericFacade
{


    /**
     *
     * @var \App\BO\Principal\PlanoContaBO
     */
    private $planoContaBO;

    /**
     *
     * @var \App\BO\Principal\ContaPagarBO
     */
    private $contaPagarBO;

    /**
     *
     * @var \App\Repository\Principal\PlanoContasContaPagarRepository
     */
    private $planoContasContaPagarRepository;

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
        $this->planoContaBO            = new PlanoContaBO(self::getEntityManager());
        $this->contaPagarBO            = new ContaPagarBO(self::getEntityManager());
        $this->planoContasContaPagarBO = new PlanoContasContaPagarBO(self::getEntityManager());
        $this->planoContasContaPagarRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoContasContaPagar::class);
        $this->planoContaRepository            = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoConta::class);
    }

    /**
     * Cria uma coleção de objetos
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ContaPagar $contaPagarORM
     * @param array $parametros
     *
     * @return null|\App\Entity\Principal\PlanoContasContaPagar[]
     */
    public function criarMultiplos(&$mensagemErro, $contaPagarORM, $parametros=[])
    {
        $planosConta   = $parametros[ConstanteParametros::CHAVE_PLANO_CONTA];
        $planosCriados = [];
        foreach ($planosConta as $plano) {
            if ($this->planoContaBO->verificaPlanoContaExiste($this->planoContaRepository, $plano[ConstanteParametros::CHAVE_PLANO_CONTA], $mensagemErro, $plano[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
                $plano[ConstanteParametros::CHAVE_CONTA_PAGAR] = $contaPagarORM;

                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PlanoContasContaPagar::class, true, $plano);

                self::persistSeguro($objetoORM, $mensagemErro);
                $planosCriados[] = $objetoORM;
            } else {
                return null;
            }
        }

        return $planosCriados;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     * @param \App\Entity\Principal\ContaPagar $contaPagarORM
     * @param array $plano
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $contaPagarORM, $plano=[])
    {
        $objetoORM = null;
        if ($this->planoContasContaPagarBO->verificaPlanoContasContaPagarExiste($id, $mensagemErro, $objetoORM) === true) {
            if ($this->planoContaBO->verificaPlanoContaExiste($this->planoContaRepository, $plano[ConstanteParametros::CHAVE_PLANO_CONTA], $mensagemErro, $plano[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) {
                $plano[ConstanteParametros::CHAVE_CONTA_PAGAR] = $contaPagarORM;

                self::getFctHelper()->setParams($plano, $objetoORM);
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
    public function remover (&$mensagemErro, $id)
    {
        $objetoORM = null;
        if ($this->planoContasContaPagarBO->verificaPlanoContasContaPagarExiste($id, $mensagemErro, $objetoORM) === true) {
            self::removerSeguro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
