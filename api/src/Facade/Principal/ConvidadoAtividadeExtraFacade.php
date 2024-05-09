<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use App\BO\Principal\ConvidadoAtividadeExtraBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class ConvidadoAtividadeExtraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ConvidadoAtividadeExtraRepository
     */
    private $convidadoAtividadeExtraRepository;


    /**
     *
     * @var \App\BO\Principal\ConvidadoAtividadeExtraBO
     */
    private $convidadoAtividadeExtraBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->convidadoAtividadeExtraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ConvidadoAtividadeExtra::class);
        $this->convidadoAtividadeExtraBO         = new ConvidadoAtividadeExtraBO(self::getEntityManager());
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
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->convidadoAtividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ConvidadoAtividadeExtra::class, true, $parametros);
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

        $objetoORM = $this->convidadoAtividadeExtraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ConvidadoAtividadeExtra não encontrado na base de dados.";
        } else {
            if ($this->convidadoAtividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
                $objetoORM->setNome($parametros[ConstanteParametros::CHAVE_NOME]);
                $objetoORM->setTelefone($parametros[ConstanteParametros::CHAVE_TELEFONE]);
                $objetoORM->setPresenca($parametros[ConstanteParametros::CHAVE_PRESENCA]);
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
