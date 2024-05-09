<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoAvaliacaoBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"        => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"             => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "turmaRepository"             => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "livroRepository"             => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "conceitoAvaliacaoRepository" => $entityManager->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class),
                "contratoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Contrato::class),
            ]
        );
    }

    /**
     * Verifica se os parametros de relacionamentos obrigatorios foram passados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        $bReturn = false;
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO], true) === true) {
                if (self::verificaLivroExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_LIVRO]) === true) {
                    if ((bool) $parametros[ConstanteParametros::CHAVE_PERSONAL] === false) {
                        $bReturn = self::verificaTurmaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TURMA]);
                    } else {
                        $bReturn = self::verificaContratoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTRATO]);
                    }
                }
            }
        }

        return $bReturn;
    }

    /**
     * Verifica se as regras para criação dos registros foram aplicadas
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificarRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se os parametros relacionais existem
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function verificarRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoNotas = $this->verificaNotasOralExiste($parametros, $mensagemErro);

        return $bRetornoNotas;
    }


    /**
     * Verifica se notas orais existem
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function verificaNotasOralExiste(&$parametros, &$mensagemErro)
    {
        $bRetornoNotas = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === false)) {
            $bRetornoNotas = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]);
        } else {
            $parametros[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]) === false)) {
            $bRetornoNotas = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL]);
        } else {
            $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ORAL] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === false)) {
            $bRetornoNotas = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_FINAL_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]);
        } else {
            $parametros[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]) === false)) {
            $bRetornoNotas = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL, $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL]);
        } else {
            $parametros[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ORAL] = null;
        }

        return $bRetornoNotas;
    }

    /**
     * Verifica se existe o alunoAvaliacao no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoAvaliacaoRepository $alunoAvaliacaoRepository Repositorio da AlunoAvaliacao
     * @param integer $id Chave primaria do AlunoAvaliacaoConceitual
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AlunoAvaliacao|null $alunoAvaliacaoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAlunoAvaliacaoExiste(\App\Repository\Principal\AlunoAvaliacaoRepository $alunoAvaliacaoRepository, $id, &$mensagemErro, &$alunoAvaliacaoORM)
    {
        $alunoAvaliacaoORM = $alunoAvaliacaoRepository->find($id);
        if (is_null($alunoAvaliacaoORM) === true) {
            $mensagemErro = "AlunoAvaliacao não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
