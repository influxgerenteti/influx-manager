<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\HorarioAulaBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class HorarioAulaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\HorarioAulaRepository
     */
    private $horarioAulaRepository;

    /**
     *
     * @var \App\BO\Principal\HorarioAulaBO
     */
    private $horarioAulaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->horarioAulaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\HorarioAula::class);
        $this->horarioAulaBO         = new HorarioAulaBO(self::getEntityManager());
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param \App\Entity\Principal\Horario $horarioORM Horario para realizar o relacionamento
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\HorarioAula
     */
    public function criar(&$mensagemErro, $horarioORM, $parametros=[])
    {
        $objetoORM = null;
        $parametros[ConstanteParametros::CHAVE_HORARIO] = $horarioORM;
        if ($this->horarioAulaBO->parametrosValidosCriacao($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\HorarioAula::class, true, $parametros);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\HorarioAula $horarioAulaORM
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[], &$horarioAulaORM=null)
    {
        $objetoORM = $this->horarioAulaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Horario Aula não encontrado na base de dados.";
        } else {
            $this->horarioAulaBO->configuraParametrosAlteracao($parametros, $mensagemErro, $objetoORM);
            $horarioAulaORM = $objetoORM;
        }

        return empty($mensagemErro);
    }


}
