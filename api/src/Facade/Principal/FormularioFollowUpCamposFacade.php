<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\FormularioFollowUpCamposBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class FormularioFollowUpCamposFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FormularioFollowUpCamposRepository
     */
    private $formularioFollowUpCamposRepository;

    /**
     *
     * @var \App\BO\Principal\FormularioFollowUpCamposBO
     */
    private $formularioFollowUpCamposBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->formularioFollowUpCamposRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormularioFollowUpCampos::class);
        $this->formularioFollowUpCamposBO         = new FormularioFollowUpCamposBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados através do campo formulario
     *
     * @param int $formularioId FormularioId
     *
     * @return array
     */
    public function buscarPorFormularioId($formularioId)
    {
        return $this->formularioFollowUpCamposRepository->filtraPorFormulario($formularioId);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->formularioFollowUpCamposRepository->buscarPorId($id);
        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\FormularioFollowUpCampos
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->formularioFollowUpCamposBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FormularioFollowUpCampos::class, true, $parametros);
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
        $objetoORM = null;
        if ($this->formularioFollowUpCamposBO->verificaFormularioFollowUpCamposExisteBO([ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS => $id], $mensagemErro, $objetoORM) === true) {
            if ($this->formularioFollowUpCamposBO->podeAtualizar($parametros, $mensagemErro) === true) {
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
        if ($this->formularioFollowUpCamposBO->verificaFormularioFollowUpCamposExisteBO([ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP_CAMPOS => $id], $mensagemErro, $objetoORM) === true) {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
