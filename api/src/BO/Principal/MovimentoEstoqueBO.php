<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz Antonio Costa
 */
class MovimentoEstoqueBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\MovimentoEstoqueRepository
     */
    private $movimentoEstoqueRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->movimentoEstoqueRepository = $entityManager->getRepository(\App\Entity\Principal\MovimentoEstoque::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"           => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "itemRepository"                 => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "usuarioRepository"              => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "tipoMovimentoEstoqueRepository" => $entityManager->getRepository(\App\Entity\Principal\TipoMovimentoEstoque::class),
                "itemContaReceberRepository"     => $entityManager->getRepository(\App\Entity\Principal\ItemContaReceber::class),
            ]
        );
    }

    /**
     * Executa a validacao de parametros que necessitam de relacionamento
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisValido(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                    if (self::verificaTipoMovimentoEstoqueExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_ESTOQUE]) === true) {
                            return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Calcula estoque final subtraindo Item->SaldoEstoque-Quantidade
     *
     * @param \App\Entity\Principal\Item $itemORM
     * @param float $quantidadeItemRequisicao
     *
     * @return float
     */
    public static function subtraiEstoqueItem(\App\Entity\Principal\Item $itemORM, $quantidadeItemRequisicao)
    {
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

        if (is_null($itemMovimentar) === false) {
            $resultado = $itemMovimentar->getSaldoEstoque() - $quantidadeItemRequisicao;
            return round($resultado, 6);
        }

        return 0;
    }

    /**
     * Calcula estoque final somando Item->SaldoEstoque+Quantidade
     *
     * @param \App\Entity\Principal\Item $itemORM
     * @param float $quantidadeItemRequisicao
     *
     * @return number
     */
    public static function adicionaEstoqueItem(\App\Entity\Principal\Item $itemORM, $quantidadeItemRequisicao)
    {
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

        $resultado = $itemMovimentar->getSaldoEstoque() + $quantidadeItemRequisicao;
        return round($resultado, 6);
    }

    /**
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param boolean $validarEstoque
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro, $validarEstoque=true)
    {
        if ($this->verificaParametrosRelacionaisValido($parametros, $mensagemErro) === true) {
            if ($validarEstoque === true && $parametros[ConstanteParametros::CHAVE_QUANTIDADE_SALDO_FINAL] < 0) {
                $item         = $parametros[ConstanteParametros::CHAVE_ITEM];
                $mensagemErro = "Não há estoque suficiente para o item {$item->getDescricao()}";
                return false;
            }

            return true;
        }

        return false;
    }


}
