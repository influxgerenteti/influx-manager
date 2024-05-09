<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class PagamentoFuncionarioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"  => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "turmaRepository"       => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "livroRepository"       => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "licaoRepository"       => $entityManager->getRepository(\App\Entity\Principal\Licao::class),
                "funcionarioRepository" => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
            ]
        );
    }

    /**
     * Verifica parametros de relacionamento obrigatorio
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica parametros que não são obrigatórios na criação/alteração
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bTurmaRetorno = true;
        $bLivroRetorno = true;
        $bLicaoRetorno = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $bTurmaRetorno = self::verificaTurmaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TURMA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LIVRO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_LIVRO]) === false)) {
            $bLivroRetorno = self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_LICAO]) === false)) {
            $bLicaoRetorno = self::verificaLicaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LICAO]);
        }

        return $bTurmaRetorno && $bLivroRetorno && $bLicaoRetorno;
    }

    /**
     * Verifica as regras para criação de um registro no pagamentoFuncionario
     *
     * @param array $parametros
     * @param string $mensagemErro
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
     * Verifica se existe o PagamentoFuncionario no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\PagamentoFuncionarioRepository $pagamentoFuncionarioRepository Repositorio do PagamentoFuncionario
     * @param integer $id Chave primaria da PagamentoFuncionario
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\PagamentoFuncionario|null $pagamentoFuncionarioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaPagamentoFuncionarioExiste(\App\Repository\Principal\PagamentoFuncionarioRepository $pagamentoFuncionarioRepository, $id, &$mensagemErro, &$pagamentoFuncionarioORM)
    {
        $pagamentoFuncionarioORM = $pagamentoFuncionarioRepository->find($id);
        if (is_null($pagamentoFuncionarioORM) === true) {
            $mensagemErro = "PagamentoFuncionario não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
