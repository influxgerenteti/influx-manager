<?php

namespace App\Facade\Principal;


use App\Facade\Principal\GenericFacade;
use App\BO\Principal\MovimentoEstoqueBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class MovimentoEstoqueFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\MovimentoEstoqueBO
     */
    private $movimentoEstoqueBO;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Principal\MovimentoEstoqueRepository
     */
    private $movimentoEstoqueRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->itemRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Item::class);
        $this->movimentoEstoqueRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MovimentoEstoque::class);
        $this->movimentoEstoqueBO         = new MovimentoEstoqueBO(self::getEntityManager());
    }

    /**
     * Monta os parametros para movimento estoque
     *
     * @param \App\Entity\Principal\ItemContaReceber $objetoItemContaReceberORM
     * @param string $mensagemErro
     * @param boolean $subtraiEstoque
     *
     * @return array
     */
    protected function montaParametros(&$objetoItemContaReceberORM, $mensagemErro, $subtraiEstoque=true)
    {
        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA             => $objetoItemContaReceberORM->getContaReceber()->getFranqueada()->getId(),
            ConstanteParametros::CHAVE_ITEM                   => $objetoItemContaReceberORM->getItem()->getId(),
            ConstanteParametros::CHAVE_ITEM_CONTA_RECEBER     => $objetoItemContaReceberORM,
            ConstanteParametros::CHAVE_USUARIO                => $objetoItemContaReceberORM->getContaReceber()->getUsuario()->getId(),
            ConstanteParametros::CHAVE_DATA_MOVIMENTO         => new \DateTime(),
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE => 3,
            ConstanteParametros::CHAVE_QUANTIDADE_ITEM        => $objetoItemContaReceberORM->getQuantidade(),
            ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL => $this->movimentoEstoqueBO->subtraiEstoqueItem($objetoItemContaReceberORM->getItem(), $objetoItemContaReceberORM->getQuantidade()),
            ConstanteParametros::CHAVE_VALOR_MOVIMENTO        => $objetoItemContaReceberORM->getValor(),
        ];
        if ($subtraiEstoque === false) {
            $parametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL] = $this->movimentoEstoqueBO->adicionaEstoqueItem($objetoItemContaReceberORM->getItem(), $objetoItemContaReceberORM->getQuantidade());
        }

        return $parametros;
    }

    /**
     * Monta os parametros para movimento estoque
     *
     * @param \App\Entity\Principal\Item $itemORM
     * @param int $usuarioId
     * @param int $tipoMovimentoEstoqueId
     * @param string $quantidade
     * @param string $mensagemErro
     * @param string $justificativa
     * @param boolean $subtraiEstoque
     *
     * @return array
     */
    protected function montaParametrosComApenasItem(&$itemORM, $usuarioId, $tipoMovimentoEstoqueId, $quantidade, &$mensagemErro, $justificativa="", $subtraiEstoque=true)
    {
        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA             => VariaveisCompartilhadas::$franqueadaID,
            ConstanteParametros::CHAVE_ITEM                   => $itemORM->getId(),
            ConstanteParametros::CHAVE_USUARIO                => $usuarioId,
            ConstanteParametros::CHAVE_DATA_MOVIMENTO         => new \DateTime(),
            ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE => $tipoMovimentoEstoqueId,
            ConstanteParametros::CHAVE_QUANTIDADE_ITEM        => $quantidade,
            ConstanteParametros::CHAVE_OBSERVACAO             => $justificativa,
        ];
        if ($subtraiEstoque === false) {
            $parametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL] = $this->movimentoEstoqueBO->adicionaEstoqueItem($itemORM, $quantidade);
        } else {
            $parametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL] = $this->movimentoEstoqueBO->subtraiEstoqueItem($itemORM, $quantidade);
        }

        return $parametros;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param null|\App\Entity\Principal\ItemContaReceber $objetoItemContaReceberORM Array de itens de ItemContaReceber
     * @param boolean $subtraiEstoque Flag para subtrair o estoque, caso seja falso, sera adicionado
     *
     * @return boolean
     */
    public function criar(&$mensagemErro, $objetoItemContaReceberORM, $subtraiEstoque=true)
    {
        $objetoORM = null;

        $movimentoEstoqueParametros = $this->montaParametros($objetoItemContaReceberORM, $mensagemErro, $subtraiEstoque);
        $deveValidarEstoque         = $objetoItemContaReceberORM->getItem()->getTipoItem()->getTipo() === SituacoesSistema::TIPO_ITEM_PRODUTO;
        if ($this->movimentoEstoqueBO->podeCriar($movimentoEstoqueParametros, $mensagemErro, $deveValidarEstoque) === true) {
            $itemFranqueadas = $objetoItemContaReceberORM->getItem()->getItemFranqueadas();
            $itemMovimentar  = null;
            foreach ($itemFranqueadas as $itemFranquia) {
                if ($itemFranquia->getFranqueada()->getId() === (int) VariaveisCompartilhadas::$franqueadaID) {
                    $itemMovimentar = $itemFranquia;
                    break;
                }
            }

            if (is_null($itemMovimentar) === true) {
                foreach ($itemFranqueadas as $itemFranquia) {
                    if ($itemFranquia->getFranqueada()->getFranqueadora() === true) {
                        $itemMovimentar = $itemFranquia;
                        break;
                    }
                }
            }

            if (is_null($itemMovimentar) === false) {
                $itemMovimentar->setSaldoEstoque($movimentoEstoqueParametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL]);
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoEstoque::class, true, $movimentoEstoqueParametros);
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }//end if

        return empty($mensagemErro);
    }

    /**
     *
     * @param string $mensagemErro
     * @param int $itemId
     * @param int $usuarioId
     * @param int $tipoMovimentoEstoqueId
     * @param string $tipoMovimentoEstoqueTexto
     * @param double $quantidade
     * @param string $justificativa
     *
     * @return boolean
     */
    public function criarMovimentoPelaTelaItem(&$mensagemErro, $itemId, $usuarioId, $tipoMovimentoEstoqueId, $tipoMovimentoEstoqueTexto, $quantidade, $justificativa="")
    {
        $objetoORM = null;
        $itemORM   = $this->itemRepository->find($itemId);

        if (is_null($itemORM) === true) {
            $mensagemErro = "Item informado não encontrado.";
        } else {
            $itemFranqueadas = $itemORM->getItemFranqueadas();
            $itemMovimentar  = null;
            foreach ($itemFranqueadas as $itemFranquia) {
                if ($itemFranquia->getFranqueada()->getId() === (int) VariaveisCompartilhadas::$franqueadaID) {
                    $itemMovimentar = $itemFranquia;
                    break;
                }
            }

            if (is_null($itemMovimentar) === true) {
                foreach ($itemFranqueadas as $itemFranquia) {
                    if ($itemFranquia->getFranqueada()->getFranqueadora() === true) {
                        $itemMovimentar = $itemFranquia;
                        break;
                    }
                }
            }

            $bSubtraiEstoque = false;
            if ($tipoMovimentoEstoqueTexto === SituacoesSistema::TIPO_MOVIMENTO_ESTOQUE_AJUSTE_SAIDA) {
                if ($itemFranquia->getSaldoEstoque() > 0) {
                    $bSubtraiEstoque = true;
                }
            }

            $movimentoEstoqueParametros = $this->montaParametrosComApenasItem($itemORM, $usuarioId, $tipoMovimentoEstoqueId, $quantidade, $mensagemErro, $justificativa, $bSubtraiEstoque);
            if ($this->movimentoEstoqueBO->podeCriar($movimentoEstoqueParametros, $mensagemErro, $bSubtraiEstoque) === true) {
                $itemMovimentar->setSaldoEstoque($movimentoEstoqueParametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL]);
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\MovimentoEstoque::class, true, $movimentoEstoqueParametros);
                self::criarRegistro($objetoORM, $mensagemErro);
            }
        }//end if

        return $objetoORM;
    }


}
