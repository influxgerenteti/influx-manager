<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\PagamentoFuncionarioBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class PagamentoFuncionarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PagamentoFuncionarioRepository
     */
    private $pagamentoFuncionarioRepository;

    /**
     *
     * @var \App\BO\Principal\PagamentoFuncionarioBO
     */
    private $pagamentoFuncionarioBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->pagamentoFuncionarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PagamentoFuncionario::class);
        $this->pagamentoFuncionarioBO         = new PagamentoFuncionarioBO(self::getEntityManager());
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
        $retornoRepositorio = $this->pagamentoFuncionarioRepository->filtrarPagamentoFuncionarioPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return mixed|null|\App\Entity\Principal\PagamentoFuncionario
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->pagamentoFuncionarioBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoFuncionario::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Adiciona ContaPagar ao pagamentoFuncionario
     *
     * @param int $pagamentoFuncionarioId
     * @param \App\Entity\Principal\ContaPagar $contaPagarORM
     *
     * @return boolean
     */
    public function adicionarContaPagar($pagamentoFuncionarioId, $contaPagarORM)
    {
        $objetoORM = $this->pagamentoFuncionarioRepository->find($pagamentoFuncionarioId);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "PagamentoFuncionario não encontrado na base de dados.";
        } else {
            $objetoORM->setContaPagar($contaPagarORM);
        }

        return empty($mensagemErro);
    }


}
