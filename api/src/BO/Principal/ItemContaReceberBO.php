<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz A. Costa
 */
class ItemContaReceberBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "itemRepository"       => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "planoContaRepository" => $entityManager->getRepository(\App\Entity\Principal\PlanoConta::class),
                "usuarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
            ]
        );
    }

    /**
     * Verifica os parametros de relacionamentos obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Configura os parametros de relacionamentos opcionais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoAluno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)) {
            $bRetornoAluno = self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_PLANO_CONTA]) === false)) {
            $bRetornoAluno = self::verificaPlanoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PLANO_CONTA]);
        } else {
            $parametros[ConstanteParametros::CHAVE_PLANO_CONTA] = null;
        }

        return $bRetornoAluno;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Configura a data de entrega
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraDataEntrega(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA], $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA] === false) {
                $bRetorno     = false;
                $mensagemErro = "Houve erro na formatação de data, possivel falha de reconhecimento no formato enviado.";
            }
        } else {
            $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA] = new \DateTime();
        }

        return $bRetorno;
    }

    /**
     * Configura a data de vencimento
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraDataVencimento(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === false)) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO], $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]);
            if ($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO] === false) {
                $bRetorno     = false;
                $mensagemErro = "Houve erro na formatação de data, possivel falha de reconhecimento no formato enviado.";
            }
        } else {
            $mensagemErro = "Data de vencimento do item não informado.";
            $bRetorno     = false;
        }

        return $bRetorno;
    }

    /**
     * Verifica se o ItemContaReceber existe na base de dados
     *
     * @param \App\Repository\Principal\ItemContaReceberRepository $itemContaReceberRepository
     * @param int $id
     * @param string $mensagemRetorno
     * @param \App\Entity\Principal\ItemContaReceber $retornoORM
     * @param boolean $bRetornaObjeto
     *
     * @return boolean
     */
    public static function verificaItemContaReceberExiste(\App\Repository\Principal\ItemContaReceberRepository $itemContaReceberRepository, $id, &$mensagemRetorno, &$retornoORM=null, $bRetornaObjeto=false)
    {
        if ($bRetornaObjeto === true) {
            $retornoORM = $itemContaReceberRepository->find($id);
        } else {
            $retornoORM = $itemContaReceberRepository->buscarPorId($id);
        }

        if (is_null($retornoORM) === true) {
            $mensagemRetorno = "ItemContaReceber não encontrado na base de dados";
            return false;
        }

        return true;
    }


}
