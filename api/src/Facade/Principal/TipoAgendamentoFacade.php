<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\TipoAgendamento;
use App\BO\Principal\TipoAgendamentoBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Augusto Lucas de Souza (GATI labs)
 */
class TipoAgendamentoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TipoAgendamentoRepository
     */
    private $tipoAgendamentoRepository;

    /**
     *
     * @var \App\BO\Principal\TipoAgendamentoBO
     */
    private $tipoAgendamentoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoAgendamentoRepository = self::getEntityManager()->getRepository(TipoAgendamento::class);
        $this->tipoAgendamentoBO         = new TipoAgendamentoBO(self::getEntityManager());
    }


    /**
     * Lista todos os tipos de agendamento do banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->tipoAgendamentoRepository->filtrarTipoAgendamentoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\TipoAgendamento
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->tipoAgendamentoRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "TipoAgendamento não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\TipoAgendamento
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        // TODO: Se vier a solicitarem a criacao de funcao de criacao registro, implementar aqui
        // if ($this->tipoAgendamentoBO->podeSalvar($parametros, $mensagemErro) === true) {
        // }
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
        $objetoORM = $this->tipoAgendamentoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TipoAgendamento não encontrado na base de dados.";
        } else {
            // TODO: Se vier a solicitarem a criacao de funcao de atualizar registro, implementar aqui
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
        $objetoORM = $this->tipoAgendamentoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TipoAgendamento não encontrado na base de dados.";
        } else {
            // TODO: Se vier a solicitarem a criacao de funcao de remover registro, implementar aqui
        }

        return empty($mensagemErro);
    }


}
