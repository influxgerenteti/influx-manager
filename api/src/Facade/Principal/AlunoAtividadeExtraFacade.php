<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AlunoAtividadeExtraBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class AlunoAtividadeExtraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunoAtividadeExtraRepository
     */
    private $alunoAtividadeExtraRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoAtividadeExtraBO
     */
    private $alunoAtividadeExtraBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->alunoAtividadeExtraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoAtividadeExtra::class);
        $this->alunoAtividadeExtraBO         = new AlunoAtividadeExtraBO(self::getEntityManager());
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
     * @return mixed|null|\App\Entity\Principal\AlunoAtividadeExtra
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->alunoAtividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunoAtividadeExtra::class, true, $parametros);
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
        $objetoORM = $this->alunoAtividadeExtraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AlunoAtividadeExtra não encontrado na base de dados.";
        } else {
            if ($this->alunoAtividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
                $objetoORM->setRemovido($parametros[ConstanteParametros::CHAVE_REMOVIDO]);
                $objetoORM->setPresenca($parametros[ConstanteParametros::CHAVE_PRESENCA]);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Lancamento/Atualizacao de Notas para Aluno Avaliacao
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return bool
     */
    public function criarAtualizarAlunoAtividadeExtra(&$mensagemErro, $parametros)
    {
        $bPossuiAlunoAtividadeExtraId = isset($parametros[ConstanteParametros::CHAVE_ID]);
        if ($bPossuiAlunoAtividadeExtraId === true) {
            $alunoAtividadeExtraId = $parametros[ConstanteParametros::CHAVE_ID];
            $parametros[ConstanteParametros::CHAVE_ID];
            $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA];
            $bSuccesso = $this->atualizar($mensagemErro, $alunoAtividadeExtraId, $parametros);
        } else {
            $bSuccesso = $this->criar($mensagemErro, $parametros);
        }

        return $bSuccesso;
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
        $objetoORM = $this->alunoAtividadeExtraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AlunoAtividadeExtra não encontrado na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }

}
