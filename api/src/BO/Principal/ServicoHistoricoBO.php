<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Dayan Freitas
 */
class ServicoHistoricoBO extends GenericBO
{

    /**
     *
     * @var \App\Repository\Principal\ServicoHistoricoRepository
     */
    private static $servicoHistoricoRepository;

    function __construct(EntityManagerInterface $entityManager)
    {

        parent::configuraGenericBO(
            [
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "servicoRepository"        => $entityManager->getRepository(\App\Entity\Principal\Servico::class),
                "formaPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
    }

    /**
     * Configura os parametros obrigatorios para criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (is_object($parametros[ConstanteParametros::CHAVE_SERVICO]) === false) {
            if (self::verificaServicoExiteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SERVICO]) === true) {
                if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                    return true;
                }
            }
        } else {
            if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                return true;
            }
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
        $bRetornoFormaCobranca = true;
        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA]) === false)) {
            $bRetornoFormaCobranca = self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA);
        }

        return $bRetornoFormaCobranca;
    }

    /**
     * Realiza a verificacao das regras para permitir a criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaRelacionamentosOpcionais($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }


}
