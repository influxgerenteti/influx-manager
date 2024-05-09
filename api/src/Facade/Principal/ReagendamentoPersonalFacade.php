<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ReagendamentoPersonalBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class ReagendamentoPersonalFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\ReagendamentoPersonalBO
     */
    private $reagendamentoPersonalBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->reagendamentoPersonalBO = new ReagendamentoPersonalBO(self::getEntityManager());
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\DatasReagendamentoPersonal
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->reagendamentoPersonalBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM           = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\DatasReagendamentoPersonal::class, true, $parametros);
            $agendamentoPersonal = $objetoORM->getAgendamentoPersonal();
            $agendamentoPersonal->setReagendado(true);
            $datasReagendamentoPersonalORMS = $agendamentoPersonal->getDatasReagendamentoPersonals();
            foreach ($datasReagendamentoPersonalORMS as $dataReagendamentoPersonal) {
                $dataReagendamentoPersonal->setUltimoReagendamento(false);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true && is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
                $agendamentoPersonal->setFuncionario($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
            }

            if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
                $agendamentoPersonal->setSalaFranqueada($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
            }

            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }


}
