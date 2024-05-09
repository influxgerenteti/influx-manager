<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ModeloTemplateFranqueadaBO;

/**
 *
 * @author Luiz A Costa
 */
class ModeloTemplateFranqueadaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ModeloTemplateFranqueadaRepository
     */
    private $modeloTemplateFranqueadaRepository;

    /**
     *
     * @var \App\BO\Principal\ModeloTemplateFranqueadaBO
     */
    private $modeloTemplateFranqueadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->modeloTemplateFranqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ModeloTemplateFranqueada::class);
        $this->modeloTemplateFranqueadaBO         = new ModeloTemplateFranqueadaBO(self::getEntityManager());
    }


    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return boolean|int
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->modeloTemplateFranqueadaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ModeloTemplateFranqueada::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        if (empty($mensagemErro) === false) {
            return false;
        }

        return $objetoORM->getId();
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param int $id Chave primaria do registro
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     *
     * @return boolean
     */
    public function remover($id, &$mensagemErro)
    {
        $objetoORM = $this->modeloTemplateFranqueadaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ModeloTemplateFranqueada não encontrado na base de dados.";
        } else {
            self::removerSeguro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
