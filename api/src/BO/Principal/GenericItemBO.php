<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class GenericItemBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "tipoItemRepository"   => $entityManager->getRepository(\App\Entity\Principal\TipoItem::class),
                "planoContaRepository" => $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class),
                "itemRepository"       => $entityManager->getRepository(\App\Entity\Principal\Item::class),
            ]
        );
    }

    /**
     * Verifica se os campos relacionais obrigatorios foram preenchidos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se os parametros de relacionamento não obrigatorios foram preenchidos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bTipoItemRetorno   = true;
        $bPlanoContaRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_ITEM]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO_ITEM]) === false)) {
            $bTipoItemRetorno = self::verificaTipoItemExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_TIPO_ITEM, $parametros[ConstanteParametros::CHAVE_TIPO_ITEM]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
            $bPlanoContaRetorno = self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]);
        }

        return $bTipoItemRetorno && $bPlanoContaRetorno;
    }

    /**
     * Verifica se podemos prosseguir com o processo de salvar
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se podemos prosseguir com o processo de alteração
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o item existe atraves da Chave Primaria informada
     *
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Item $itemORM Ponteiro para retornar o objeto
     * @param null|\App\Repository\Principal\ItemRepository $itemRepository Repositorio para ser acessado estaticamente
     * @param boolean $bApenasObjeto
     *
     * @return boolean
     */
    public static function verificaItemExistePorId($id, &$mensagemErro, &$itemORM=null, $itemRepository=null, $bApenasObjeto=false)
    {
        if ($bApenasObjeto === false) {
            $itemORM = $itemRepository->buscarPorId($id);
        } else {
            $itemORM = $itemRepository->find($id);
        }

        if (is_null($itemORM) === true) {
            $mensagemErro .= "Item não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
