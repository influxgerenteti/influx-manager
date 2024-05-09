<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AgendaComercialBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Factory\ResponseFactory;
use Composer\Downloader\VcsCapableDownloaderInterface;

/**
 *
 * @author Augusto Lucas de Souza (GATI labs)
 */
class AgendaComercialFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AgendaComercialRepository
     */
    private $agendaComercialRepository;

    /**
     *
     * @var \App\Repository\Principal\WorkflowAcaoRepository
     */
    private $workflowAcaoRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoAgendamentoRepository
     */
    private $tipoAgendamentoRepository;

    /**
     *
     * @var \App\BO\Principal\AgendaComercialBO
     */
    private $agendaComercialBO;

    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->agendaComercialRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AgendaComercial::class);
        $this->workflowAcaoRepository    = self::getEntityManager()->getRepository(\App\Entity\Principal\WorkflowAcao::class);
        $this->tipoAgendamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoAgendamento::class);
        $this->interessadoRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\Interessado::class);
        $this->agendaComercialBO         = new AgendaComercialBO(self::getEntityManager());
    }


    /**
     * Lista todas as agendas comerciais do banco de dados
     *
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->agendaComercialRepository->filtrarAgendaComercialPorPagina($parametros);
        return [
            ConstanteParametros::CHAVE_TOTAL => count($retornoRepositorio),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio,
        ];
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param int $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\AgendaComercial
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->agendaComercialRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Agenda comercial não encontrada na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um registro na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Parametros para realizar a criacao do registro
     *
     * @return boolean null|\App\Entity\Principal\AgendaComercial
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->agendaComercialBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AgendaComercial::class, true, $parametros);
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
        $objetoORM = $this->agendaComercialRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Agenda Comercial não encontrado na base de dados.";
        } else {
            $mensagemErro = "Metodo não implementado";
        }

        return empty($mensagemErro);
    }

    /**
     * Realiza criação de nova agenda
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametrosNovaAgenda
     *
     * @return null|\App\Entity\Principal\AgendaComercial
     */
    public function atualizarVindoInteressado(&$mensagemErro, $id, $parametrosNovaAgenda=[])
    {
        $interessadoORM = $this->interessadoRepository->find($id);
        $agendaComercialInteressado = $interessadoORM->getAgendaComerciais();
        foreach ($agendaComercialInteressado as &$agendaComercialORM) {
            $agendaComercialORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
        }

        $agendaComercialORM = $this->criar($mensagemErro, $parametrosNovaAgenda);
        return $agendaComercialORM;
    }

    /**
     * Realiza a atualizacao da agenda no na coluna de situacao quando matricula perdida ou efetuada
     *
     * @param string $mensagemErro
     * @param int $id
     *
     * @return boolean
     */
    public function atualizaSituacaoDoInteressado(&$mensagemErro, $id)
    {
        $interessadoORM = $this->interessadoRepository->find($id);
        $agendaComercialInteressado = $interessadoORM->getAgendaComerciais();

        foreach ($agendaComercialInteressado as &$agendaComercialORM) {
            $agendaComercialORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
        }

        self::flushSeguro($mensagemErro);
        return (empty($mensagemErro) === true);
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
        $objetoORM = $this->agendaComercialRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Agenda Comercial não encontrado na base de dados.";
        } else {
            $mensagemErro = "Metodo não implementado";
        }

        return empty($mensagemErro);
    }


}
