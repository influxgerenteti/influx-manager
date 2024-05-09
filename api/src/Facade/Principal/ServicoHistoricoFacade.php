<?php

namespace App\Facade\Principal;

use App\BO\Principal\ServicoHistoricoBO;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;


/**
 *
 * @author Dayan Freitas
 */
class ServicoHistoricoFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\ServicoHistoricoBO
     */
    private $servicoHistoricoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->servicoHistoricoBO = new ServicoHistoricoBO(self::getEntityManager());
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

    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param object $servicoORM objeto de relacionameto do servico
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $servicoORM=null, $parametros=[])
    {
        $objetoORM = null;
        if ($servicoORM !== null) {
            $parametros[ConstanteParametros::CHAVE_SERVICO] = $servicoORM;
        }

        if ($this->servicoHistoricoBO->podeSalvar($parametros, $objetoORM, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ServicoHistorico::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
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
