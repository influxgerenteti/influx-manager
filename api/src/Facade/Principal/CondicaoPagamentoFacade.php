<?php

namespace App\Facade\Principal;

use App\BO\Principal\CondicaoPagamentoBO;
use App\BO\Principal\CondicaoPagamentoParcelaBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class CondicaoPagamentoFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\CondicaoPagamentoRepository
     */
    private $condicaoPagamentoRepository;

    /**
     *
     * @var \App\BO\Principal\CondicaoPagamentoBO
     */
    private $condicaoPagamentoBO;

    /**
     *
     * @var \App\BO\Principal\CondicaoPagamentoParcelaBO
     */
    private $condicaoPagamentoParcelaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->condicaoPagamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\CondicaoPagamento::class);
        $this->condicaoPagamentoBO         = new CondicaoPagamentoBO();
        $this->condicaoPagamentoParcelaBO  = new CondicaoPagamentoParcelaBO();
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
        $retornoRepositorio = $this->condicaoPagamentoRepository->filtrarCondicaoPagamentoPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Repository\Principal\CondicaoPagamentoRepository
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        return $this->condicaoPagamentoRepository->find($id);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\CondicaoPagamento
     */
    public function criar(&$mensagemErro, &$parametros=[])
    {
        $objetoORM = null;
        if ($this->condicaoPagamentoParcelaBO->parcelaValida($parametros[ConstanteParametros::CHAVE_PARCELA], $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\CondicaoPagamento::class);
            $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
            $objetoORM->setQuantidadeParcelas($parametros[ConstanteParametros::CHAVE_QUANTIDADE_PARCELA]);
            unset($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
            unset($parametros[ConstanteParametros::CHAVE_QUANTIDADE_PARCELA]);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
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
        return empty($mensagemErro);
    }


}
