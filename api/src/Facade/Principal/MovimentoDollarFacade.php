<?php

namespace App\Facade\Principal;


use App\BO\Principal\MovimentoDollarBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MovimentoDollarFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\MovimentoDollarRepository
     */
    private $movimentoDollarRepository;

    /**
     *
     * @var \App\BO\Principal\MovimentoDollarBO
     */
    private $movimentoDollarBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
        $this->movimentoDollarRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MovimentoDollar::class);
        $this->movimentoDollarBO         = new MovimentoDollarBO(self::getEntityManager());
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
        $retornoRepositorio = $this->movimentoDollarRepository->filtrarMovimentoDollarPorPagina($parametros);
        $itens   = $retornoRepositorio[ConstanteParametros::CHAVE_ITENS];
        $retorno = [
            ConstanteParametros::CHAVE_TOTAL => $itens->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $itens->getItems(),
            ConstanteParametros::CHAVE_SALDO => $retornoRepositorio[ConstanteParametros::CHAVE_SALDO],
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id Chave primaria do registro
     *
     * @return null|\App\Entity\Principal\MovimentoDollar
     */
    public function buscarPorId($id)
    {
        $mensagem           = "";
        $movimentoDollarORM = null;
        $this->movimentoDollarBO->verificaMovimentoDollarExiste($id, $mensagem, $movimentoDollarORM);
        return $movimentoDollarORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return []|\App\Entity\Principal\MovimentoDollar[]
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $movimentosDollarORM = [];
        if ($this->movimentoDollarBO->podeCriar($parametros, $mensagemErro) === true) {
            $movimentosDollarORM = $this->criarMovimentoDollarCredito($mensagemErro, $parametros);
        }

        return $movimentosDollarORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param bool $bAplicarFlushFuncao Aplicar flush logo seguida
     *
     * @return []|\App\Entity\Principal\MovimentoDollar[]
     */
    protected function criarMovimentoDollarCredito(&$mensagemErro, $parametros, $bAplicarFlushFuncao=true)
    {
        $movimentosDollarORM = [];
        foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR] as $movimentoDollar) {
            if (isset($movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]) === true) {
                unset($movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]);
            }

            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoDollar::class, true, $movimentoDollar);
            self::persistSeguro($objetoORM, $mensagemErro);
            $movimentosDollarORM[] = $objetoORM;
        }

        if ((empty($mensagemErro) === true) && ($bAplicarFlushFuncao === true)) {
            self::flushSeguro($mensagemErro);
        }

        return $movimentosDollarORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return []|\App\Entity\Principal\MovimentoDollar[]
     */
    public function atualizar(&$mensagemErro, $parametros=[], $bAplicarFlushFuncao=true)
    {
        $movimentosDollarORM = [];
        if ($this->movimentoDollarBO->podeAtualizar($parametros, $mensagemErro) === true) {
            foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR] as $movimentoDollar) {
                // self::getEntityManager()->detach($movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]);
                // $movimentoDollarORM = clone $movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR];
                $movimentoDollarORM = $movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR];
                // $movimentoDollarORM->setTipoOperacao(SituacoesSistema::TIPO_OPERACAO_DEBITO);
                // $movimentoDollarORM->setValor($movimentoDollarORM->getValor() * -1);
                $movimentoDollarORM->setValor($movimentoDollar[ConstanteParametros::CHAVE_VALOR]);
                $movimentoDollarORM->setDataMovimento(new \DateTime());
                $movimentosDollarORM[] = $movimentoDollarORM;
            }

            if ($bAplicarFlushFuncao === true) {
                self::flushSeguro($mensagemErro);
            }

            // $movimentosDollarORM = array_merge($movimentosDollarORM, $this->criarMovimentoDollarCredito($mensagemErro, $parametros));
        }

        return $movimentosDollarORM;
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
