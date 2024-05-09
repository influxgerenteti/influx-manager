<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoDiarioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "licaoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Licao::class),
                "cursoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Curso::class),
                "turmaAulaRepository"      => $entityManager->getRepository(\App\Entity\Principal\TurmaAula::class),
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "salaFranqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
                "livroRepository"          => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
            ]
        );
    }

    /**
     * Verifica os relacionamentos que não são obrigatórios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoTurmaAula = true;
        $bRetornoLicao     = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === false)) {
            $bRetornoTurmaAula = self::verificaTurmaAulaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TURMA_AULA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_LICAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_LICAO]) === false)) {
            $bRetornoLicao = self::verificaLicaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LICAO]);
        }

        return $bRetornoTurmaAula && $bRetornoLicao;
    }

    /**
     * Verifica se é possivel prosseguir com a criacao do registro com os parametros informados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentoObrigatorio(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) !== true) {
            return false;
        }

        if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO], true) !== true) {
            return false;
        }

        if (self::verificaCursoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CURSO]) !== true) {
            return false;
        }

        if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
            if (self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) !== true) {
                return false;
            }
        }

        if (self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]) !== true) {
            return false;
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true && is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica as regras para criação de dados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentoObrigatorio($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentoOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se existe a AlunoDiario no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoDiarioRepository $alunoDiarioRepository Repositorio da AlunoDiario
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AlunoDiario|null $alunoDiarioORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAlunoDiarioExiste(\App\Repository\Principal\AlunoDiarioRepository $alunoDiarioRepository, $id, &$mensagemErro, &$alunoDiarioORM)
    {
        $alunoDiarioORM = $alunoDiarioRepository->find($id);
        if (is_null($alunoDiarioORM) === true) {
            $mensagemErro = "AlunoDiario não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
