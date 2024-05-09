<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ChecklistAtividadeRealizadaBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class ChecklistAtividadeRealizadaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ChecklistAtividadeRealizadaRepository
     */
    private $checklistAtividadeRealizadaRepository;

    /***
     *
     * @var \App\BO\Principal\ChecklistAtividadeRealizadaBO
     */
    private $checklistAtividadeRealizadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->checklistAtividadeRealizadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ChecklistAtividadeRealizada::class);
        $this->checklistAtividadeRealizadaBO         = new ChecklistAtividadeRealizadaBO(self::getEntityManager());
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
     * Lista todos os registros do banco de dados por usuario
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function buscaAtividadesRealizadasPorUsuario($parametros)
    {
        $dataAtual          = new \DateTime();
        $diaAtual           = $dataAtual->format("Y-m-d");
        $semanaAtual        = $dataAtual->format("W");
        $mesAtual           = $dataAtual->format("m");
        $anoAtual           = $dataAtual->format("Y");
        $arrayAtividadesIds = [];
        $bFiltroDiario      = false;
        $bFiltroSemanal     = false;
        $bFiltroMensal      = false;
        $bFiltroAtemporal   = false;
        if ((isset($parametros[ConstanteParametros::CHAVE_ATIVIDADES_DIARIAS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ATIVIDADES_DIARIAS]) > 0)) {
            $bFiltroDiario      = true;
            $arrayAtividadesIds = array_merge($arrayAtividadesIds, $parametros[ConstanteParametros::CHAVE_ATIVIDADES_DIARIAS]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ATIVIDADES_SEMANAIS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ATIVIDADES_SEMANAIS]) > 0)) {
            $bFiltroSemanal     = true;
            $arrayAtividadesIds = array_merge($arrayAtividadesIds, $parametros[ConstanteParametros::CHAVE_ATIVIDADES_SEMANAIS]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ATIVIDADES_MENSAIS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ATIVIDADES_MENSAIS]) > 0)) {
            $bFiltroMensal      = true;
            $arrayAtividadesIds = array_merge($arrayAtividadesIds, $parametros[ConstanteParametros::CHAVE_ATIVIDADES_MENSAIS]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ATIVIDADES_ATEMPORAIS]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ATIVIDADES_ATEMPORAIS]) > 0)) {
            $bFiltroAtemporal   = true;
            $arrayAtividadesIds = array_merge($arrayAtividadesIds, $parametros[ConstanteParametros::CHAVE_ATIVIDADES_ATEMPORAIS]);
        }

        $parametrosBuscaAtividades = [
            ConstanteParametros::CHAVE_USUARIO          => $parametros[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_FRANQUEADA       => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
            ConstanteParametros::CHAVE_ANO              => $anoAtual,
            ConstanteParametros::CHAVE_NUMERO_MES       => $mesAtual,
            ConstanteParametros::CHAVE_NUMERO_SEMANA    => $semanaAtual,
            ConstanteParametros::CHAVE_DIA_ATUAL        => $diaAtual,
            ConstanteParametros::CHAVE_FILTRO_DIARIO    => $bFiltroDiario,
            ConstanteParametros::CHAVE_FILTRO_SEMANAL   => $bFiltroSemanal,
            ConstanteParametros::CHAVE_FILTRO_MENSAL    => $bFiltroMensal,
            ConstanteParametros::CHAVE_FILTRO_ATEMPORAL => $bFiltroAtemporal,
            ConstanteParametros::CHAVE_ATIVIDADES       => $arrayAtividadesIds,
        ];

        return $this->checklistAtividadeRealizadaRepository->buscarPorUsuario($parametrosBuscaAtividades);
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
     * @return mixed|null|\App\Entity\Principal\ChecklistAtividadeRealizada
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->checklistAtividadeRealizadaBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ChecklistAtividadeRealizada::class, true, $parametros);
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
        $objetoORM = $this->checklistAtividadeRealizadaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ChecklistAtividadeRealizada não encontrado na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }


}
