<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class TipoContatoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TipoContatoRepository
     */
    private $tipoContatoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoContatoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoContato::class);
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
        $retornoRepositorio = $this->tipoContatoRepository->filtraTipoContatoPorPagina($parametros);
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
     * @return array|\App\Entity\Principal\TipoContato
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        return $this->tipoContatoRepository->buscarPorId($id);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\TipoContato
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoContato::class, true, $parametros);
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
        $objetoORM = $this->tipoContatoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Tipo Contato não encontrado.";
        } else {
            self::getFctHelper()->setParams($parametros, $objetoORM);
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
        $objetoORM = $this->tipoContatoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Tipo Contato não encontrado.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
