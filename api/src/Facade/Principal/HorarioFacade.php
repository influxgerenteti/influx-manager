<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\HorarioBO;

/**
 *
 * @author Luiz A Costa
 */
class HorarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\HorarioRepository
     */
    private $horarioRepository;

    /**
     *
     * @var \App\BO\Principal\HorarioBO
     */
    private $horarioBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->horarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Horario::class);
        $this->horarioBO         = new HorarioBO(self::getEntityManager());
    }

    /**
     * Lista os registros por página
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->horarioRepository->filtrarHorarioPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function buscarTodos($parametros)
    {
        $retornoRepositorio = [];
        $retornoRepositorio = $this->horarioRepository->buscarTodos($parametros);
        if ($retornoRepositorio === null) {
            $retornoRepositorio = [];
        }

        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => count($retornoRepositorio),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio,
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->horarioRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Horario não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Horario
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->horarioBO->parametrosValidosCriacao($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Horario::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param \App\Entity\Principal\Horario $horarioORM
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, &$horarioORM, $parametros=[])
    {
        $objetoORM = $this->horarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Horario não encontrado na base de dados.";
        } else {
            $this->horarioBO->configuraParametrosAlteracao($parametros, $mensagemErro, $objetoORM);
            $horarioORM = $objetoORM;
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
        $objetoORM = $this->horarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Horario não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
