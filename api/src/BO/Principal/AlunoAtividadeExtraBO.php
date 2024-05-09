<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoAtividadeExtraBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "atividadeExtraRepository" => $entityManager->getRepository(\App\Entity\Principal\AtividadeExtra::class),
                "alunoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "turmaRepository"          => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
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
        if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]) === true) {
            if (is_object($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === false) {
                if (self::verificaAtividadeExtraExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === true) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
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
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe o AlunoAtividadeExtra no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoAtividadeExtraRepository $alunoAtividadeExtraRepository Repositorio da AlunoAtividadeExtra
     * @param integer $id Chave primaria da AlunoAtividadeExtra
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AlunoAtividadeExtra|null $alunoAtividadeExtraORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAlunoAtividadeExtraExiste(\App\Repository\Principal\AlunoAtividadeExtraRepository $alunoAtividadeExtraRepository, $id, &$mensagemErro, &$alunoAtividadeExtraORM)
    {
        $alunoAtividadeExtraORM = $alunoAtividadeExtraRepository->find($id);
        if (is_null($alunoAtividadeExtraORM) === true) {
            $mensagemErro = "AlunoAtividadeExtra não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
