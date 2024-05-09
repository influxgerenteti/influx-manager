<?php

namespace App\Facade\Principal;


use App\BO\Principal\TipoMovimentoContaBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class TipoMovimentoContaFacade extends GenericFacade
{
    /**
     *
     * @var \App\BO\Principal\TipoMovimentoContaBO
     */
    private $tipoMovimentoContaBO;

    /**
     *
     * @var \App\Repository\Principal\TipoMovimentoContaRepository
     */
    private $tipoMovimentoContaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoMovimentoContaBO         = new TipoMovimentoContaBO(self::getEntityManager());
        $this->tipoMovimentoContaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoMovimentoConta::class);
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
        $retornoRepositorio = $this->tipoMovimentoContaRepository->filtrarTipoMovimentoContaPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\TipoMovimentoConta
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $this->tipoMovimentoContaBO->verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, $id, $mensagemErro, $objetoORM);
        return $objetoORM;
    }

    /**
     * Busca um tipo de movimento de conta que conforme propriedades
     *
     * @param array $propriedades
     *
     * @return \App\Entity\Principal\TipoMovimentoConta
     */
    public function buscarPorPropriedades ($propriedades)
    {
        return $this->tipoMovimentoContaRepository->findOneBy($propriedades);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\TipoMovimentoConta
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoMovimentoConta::class, true, $parametros);
        self::criarRegistro($objetoORM, $mensagemErro);
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
        $objetoORM = null;
        if ($this->tipoMovimentoContaBO->verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($objetoORM->getReservado() === false) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            } else {
                $mensagemErro = "O Registro se encontra reservado pelo sistema, então, no entanto, não será possível realizar quaisquer alterações.";
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
        $objetoORM = null;
        if ($this->tipoMovimentoContaBO->verificaTipoMovimentoContaExisteId($this->tipoMovimentoContaRepository, $id, $mensagemErro, $objetoORM) === true) {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
