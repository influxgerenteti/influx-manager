<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class InteressadoAtividadeExtraBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "interessadoRepository" => $entityManager->getRepository(\App\Entity\Principal\Interessado::class),
                "livroRepository"       => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
            ]
        );
    }

    /**
     * Verifica se os relacionamentos obrigatorios existem
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaInteressadoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica os parametros não obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoLivro = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $bRetornoLivro = self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO], ConstanteParametros::CHAVE_LIVRO);
        }

        return $bRetornoLivro;
    }

    /**
     * Verifica as regras de relacionamentos obrigatórios para poder criar o registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se existe o InteressadoAtividadeExtra no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\InteressadoAtividadeExtraRepository $interessadoAtividadeExtraRepository Repositorio da InteressadoAtividadeExtra
     * @param integer $id Chave primaria da InteressadoAtividadeExtra
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\InteressadoAtividadeExtra|null $interessadoAtividadeExtraORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaInteressadoAtividadeExtraExiste(\App\Repository\Principal\InteressadoAtividadeExtraRepository $interessadoAtividadeExtraRepository, $id, &$mensagemErro, &$interessadoAtividadeExtraORM)
    {
        $interessadoAtividadeExtraORM = $interessadoAtividadeExtraRepository->find($id);
        if (is_null($interessadoAtividadeExtraORM) === true) {
            $mensagemErro = "InteressadoAtividadeExtra não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
