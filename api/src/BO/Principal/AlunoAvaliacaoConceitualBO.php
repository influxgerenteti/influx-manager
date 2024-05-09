<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoAvaliacaoConceitualBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"               => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"                    => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "turmaRepository"                    => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "livroRepository"                    => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "conceitoAvaliacaoRepository"        => $entityManager->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class),
                "contratoRepository"                 => $entityManager->getRepository(\App\Entity\Principal\Contrato::class),
                "alunoAvaliacaoConceitualRepository" => $entityManager->getRepository(\App\Entity\Principal\AlunoAvaliacaoConceitual::class),
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
                    if ($parametros[ConstanteParametros::CHAVE_PERSONAL] === false) {
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
     * Verifica se os parametros de relacionamentos opcionais foram passados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bNotaListenening1 = true;
        $bNotaListenening2 = true;
        $bNotaSpeaking1    = true;
        $bNotaSpeaking2    = true;
        $bNotaWriting1     = true;
        $bNotaWriting2     = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === false)) {
            $bNotaListenening1 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_LISTENING_1, $parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_1]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === false)) {
            $bNotaListenening2 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_LISTENING_2, $parametros[ConstanteParametros::CHAVE_NOTA_LISTENING_2]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]) === false)) {
            $bNotaSpeaking1 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_SPEAKING_1, $parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]) === false)) {
            $bNotaSpeaking2 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_SPEAKING_2, $parametros[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_WRITING_1]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_WRITING_1]) === false)) {
            $bNotaWriting1 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_WRITING_1, $parametros[ConstanteParametros::CHAVE_NOTA_WRITING_1]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NOTA_WRITING_2]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NOTA_WRITING_2]) === false)) {
            $bNotaWriting2 = self::verificaConceitoAvaliacao($parametros, $mensagemErro, ConstanteParametros::CHAVE_NOTA_WRITING_2, $parametros[ConstanteParametros::CHAVE_NOTA_WRITING_2]);
        }

        return $bNotaListenening1 && $bNotaListenening2 && $bNotaSpeaking1 && $bNotaSpeaking2 && $bNotaWriting1 && $bNotaWriting2;
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
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se os parametros opcionais podem ser setados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe o alunoAvaliacaoConceitual no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AlunoAvaliacaoConceitualRepository $alunoAvaliacaoConceitualRepository Repositorio da AlunoAvaliacaoConceitual
     * @param integer $id Chave primaria do AlunoAvaliacaoConceitual
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AlunoAvaliacaoConceitual|null $alunoAvaliacaoConceitualORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAlunoAvaliacaoConceitualExiste(\App\Repository\Principal\AlunoAvaliacaoConceitualRepository $alunoAvaliacaoConceitualRepository, $id, &$mensagemErro, &$alunoAvaliacaoConceitualORM)
    {
        $alunoAvaliacaoConceitualORM = $alunoAvaliacaoConceitualRepository->find($id);
        if (is_null($alunoAvaliacaoConceitualORM) === true) {
            $mensagemErro = "AlunoAvaliacaoConceitual não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
