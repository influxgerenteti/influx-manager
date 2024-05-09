<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\CalendarioBO;

/**
 *
 * @author Luiz A Costa
 */
class CalendarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\CalendarioRepository
     */
    private $calendarioRepository;

    /**
     *
     * @var \App\BO\Principal\CalendarioBO
     */
    private $calendarioBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->calendarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Calendario::class);
        $this->calendarioBO         = new CalendarioBO(self::getEntityManager());
    }

    /**
     * Busca todos as datas do ano informado ou atual
     *
     * @param array $parametros
     *
     * @return array
     */
    public function listarTodos($parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_ANO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ANO]) === false)) {
            $anoAtual = $parametros[ConstanteParametros::CHAVE_ANO];
        } else {
            $anoAtual = intval((new \DateTime())->format('Y'));
        }

        $retornoRepositorio = $this->calendarioRepository->buscarTodos($parametros);
        $arrayItems         = $this->calendarioBO->retornaListaCalendarioCustomizado($anoAtual, $retornoRepositorio);
        return $arrayItems;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Calendario
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->calendarioRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Calendario não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Calendario
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->calendarioBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Calendario::class);
            $objetoORM->setFranqueada($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            $this->calendarioBO->configuraParametros($parametros, $objetoORM);
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
        $objetoORM = $this->calendarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Calendario não encontrado na base de dados.";
        } else {
            $this->calendarioBO->configuraParametros($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
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
        $objetoORM = $this->calendarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Calendario não encontrado na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Verifica se  a data passada é feriado bancário
     *
     * @param int $franqueadaId
     * @param string $data
     *
     * @return array|\App\Entity\Principal\Calendario
     */
    public function verificaFeriadoBancarioPorData ($franqueadaId, $data)
    {
        return empty(!$this->calendarioRepository->buscaFeriadoBancarioPorData($franqueadaId, $data));
    }


}
