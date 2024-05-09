<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class NotificacoesFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\NotificacoesRepository
     */
    private $notificacoesRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->notificacoesRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Notificacoes::class);
    }

    /**
     * Lista todas as notificacoes
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listarTodasNotificacoes($parametros)
    {
        return $this->notificacoesRepository->filtraNotificacoes($parametros);
    }

    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->notificacoesRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Notificação não encontrado na base de dados.";
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === false)) {
                \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO], $parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]);
                if ($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO] === false) {
                    $mensagemErro = "Ocorreu um erro na formatação da data de prorrogacao.\nPossivelmente formato invalido de data.";
                } else {
                    $objetoORM->setDataProrrogacao($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]);
                }
            }

            $objetoORM->setIsLida((bool) ($parametros[ConstanteParametros::CHAVE_IS_LIDA]));
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
