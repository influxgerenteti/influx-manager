<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContaReceberBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
            parent::configuraGenericBO(
                [
                    "franqueadaRepository"  => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                    "alunoRepository"       => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                    "pessoaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                    "usuarioRepository"     => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                    "funcionarioRepository" => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                    "contratoRepository"    => $entityManager->getRepository(\App\Entity\Principal\Contrato::class),
                ]
            );
    }

    /**
     * Verifica os parametros obrigatorios de relacionamentos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SACADO_PESSOA], ConstanteParametros::CHAVE_SACADO_PESSOA, true) === true) {
                if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                    if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_VENDEDOR_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_VENDEDOR_FUNCIONARIO]) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica os parametros nao obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosOpcionais(&$parametros, &$mensagemErro)
    {
        $bContratoRetorno = true;
        $bAlunoRetorno    = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTRATO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTRATO]) === false)) {
            $bContratoRetorno = self::verificaContratoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTRATO]);
        } else {
            $parametros[ConstanteParametros::CHAVE_CONTRATO] = null;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $bAlunoRetorno = self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]);
        } else {
            $parametros[ConstanteParametros::CHAVE_ALUNO] = null;
        }

        return $bContratoRetorno === true && $bAlunoRetorno === true;
    }

    /**
     * Verifica se a ContaReceber ja existe no banco
     *
     * @param \App\Repository\Principal\SemestreRepository $contaReceberRepository Repositorio do ContaReceber
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ContaReceber $contaReceberORM retorno objeto
     *
     * @return boolean
     */
    public static function verificaContaReceberExiste(\App\Repository\Principal\ContaReceberRepository $contaReceberRepository, $id, &$mensagemErro, &$contaReceberORM=null)
    {
        $contaReceberORM = $contaReceberRepository->find($id);
        if (is_null($contaReceberORM) === true) {
            $mensagemErro = "ContaReceber não encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se podera prosseguir com a criacao de registros
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaRelacionamentosObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cancela contas a receber de um contrato cancelado
     *
     * @param \App\Entity\Principal\Contrato $contratoORM
     */
    public static function cancelarContasReceber(\App\Entity\Principal\Contrato &$contratoORM)
    {
        /*
         * @var \App\Entity\Principal\ContaReceber $contasReceber
         */

        $contasReceber = $contratoORM->getContratoContaReceber();

        foreach ($contasReceber as &$contaReceberORM) {
            if ($contaReceberORM->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
                $contaReceberORM->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);
                \App\BO\Principal\TituloReceberBO::cancelaTitulosReceber($contaReceberORM);
            }
        }

    }


}
