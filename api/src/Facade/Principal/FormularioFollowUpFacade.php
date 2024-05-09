<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\FormularioFollowUpBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class FormularioFollowUpFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FormularioFollowUpRepository
     */
    private $formularioFollowUpRepository;

    /**
     *
     * @var \App\BO\Principal\FormularioFollowUpBO
     */
    private $formularioFollowUpBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->formularioFollowUpRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormularioFollowUp::class);
        $this->formularioFollowUpBO         = new FormularioFollowUpBO(self::getEntityManager());
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
        $retornoRepositorio = $this->formularioFollowUpRepository->filtrarFormularioFollowUpPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\FormularioFollowUp
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->formularioFollowUpRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "FormularioFollowUp não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\FormularioFollowUp
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->formularioFollowUpBO->podeCriar($parametros, $mensagemErro) === true) {
            $parametros[ConstanteParametros::CHAVE_USUARIO_ALTERACAO] = $parametros[ConstanteParametros::CHAVE_USUARIO];
            unset($parametros[ConstanteParametros::CHAVE_USUARIO]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FormularioFollowUp::class, true, $parametros);
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
        if ($this->formularioFollowUpBO->verificaFormularioFollowUpExisteBO([ConstanteParametros::CHAVE_FORMULARIO_FOLLOW_UP => $id], $mensagemErro, $objetoORM) === true) {
            if ($this->formularioFollowUpBO->podeAtualizar($parametros, $mensagemErro) === true) {
                $parametros[ConstanteParametros::CHAVE_USUARIO_ALTERACAO] = $parametros[ConstanteParametros::CHAVE_USUARIO];
                unset($parametros[ConstanteParametros::CHAVE_USUARIO]);
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
        return empty($mensagemErro);
    }


}
