<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz Antonio Costa
 */
class TituloReceberBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\TituloReceberRepository
     */
    private $tituloReceberRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->tituloReceberRepository = $entityManager->getRepository(\App\Entity\Principal\TituloReceber::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "contaReceberRepository"    => $entityManager->getRepository(\App\Entity\Principal\ContaReceber::class),
                "pessoaRepository"          => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "alunoRepository"           => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "contaRepository"           => $entityManager->getRepository(\App\Entity\Principal\Conta::class),
                "formaPagamentoRepository"  => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
                "transacaoCartaoRepository" => $entityManager->getRepository(\App\Entity\Principal\TransacaoCartao::class),
                "chequeRepository"          => $entityManager->getRepository(\App\Entity\Principal\Cheque::class),
                "boletoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Boleto::class),
            ]
        );
    }

    /**
     * Verifica os Parametros de relacionamentos obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionadosObrigatorios(&$parametros, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SACADO_PESSOA]) === true)) {
            $parametros[ConstanteParametros::CHAVE_SACADO_PESSOA] = $parametros[ConstanteParametros::CHAVE_CONTA_RECEBER]->getSacadoPessoa();
        }

        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SACADO_PESSOA], ConstanteParametros::CHAVE_SACADO_PESSOA, true) === true) {
                if (self::verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
                    if (self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_COBRANCA], ConstanteParametros::CHAVE_FORMA_COBRANCA) === true) {
                        if (self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_RECEBIMENTO], ConstanteParametros::CHAVE_FORMA_RECEBIMENTO) === true) {
                            return true;
                        }
                    } else {
                        $mensagemErro = "Titulo Receber:Forma de pagamento não encontrada";
                    }
                } else {
                    $mensagemErro = "Titulo Receber:Conta não encontrada";
                }
            } else {
                $mensagemErro = "Titulo Receber: Pessoa não encontrada";
            }
        } else {
            $mensagemErro = "Titulo Receber:Franqueada não encontrada";
        }//end if

        return false;
    }

    /**
     * Verifica os parametros de relacionamentos opcionais
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionadosOpcionais(&$parametros, &$mensagemErro)
    {
        $bChequeRetorno          = true;
        $bBoletoRetorno          = true;
        $bTransacaoCartaoRetorno = true;
        $bAlunoRetorno           = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CHEQUE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CHEQUE]) === false)) {
            $bChequeRetorno = self::verificaChequeExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CHEQUE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_BOLETO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_BOLETO]) === false)) {
            $bBoletoRetorno = self::verificaBoletoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BOLETO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === false)) {
            $bTransacaoCartaoRetorno = self::verificaTransacaoCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $bAlunoRetorno = self::verificaAlunoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ALUNO]);
        } else {
            $parametros[ConstanteParametros::CHAVE_ALUNO] = null;
        }

        $parametros[ConstanteParametros::CHAVE_DATA_EMISSAO] = null;

        return ($bChequeRetorno && $bBoletoRetorno && $bTransacaoCartaoRetorno && $bAlunoRetorno);
    }

    /**
     * Valida as datas e aplica a conversao em objetos para serem salvos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function validaDatasInformadas(&$parametros, &$mensagemErro)
    {
        $bDataVencimentoRetorno  = true;
        $bDataProrrogacaoRetorno = true;
        $bDataEmissaoRetorno     = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            if ($data === false) {
                $bDataVencimentoRetorno = false;
                $mensagemErro          .= "Data de vencimento informado, possui um formato invalido";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_VENCIMENTO]  = $data;
                $parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO] = $data;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === false)&&(is_object($parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            if ($data === false) {
                $bDataProrrogacaoRetorno = false;
                $mensagemErro           .= "Data de Prorrogação informado, possui um formato invalido";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_PRORROGACAO] = $data;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_EMISSAO]) === false)) {
            $data = $parametros[ConstanteParametros::CHAVE_DATA_EMISSAO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($data, $data);
            if ($data === false) {
                $bDataEmissaoRetorno = false;
                $mensagemErro       .= "Data de Emissão informado, possui um formato invalido";
            } else {
                $parametros[ConstanteParametros::CHAVE_DATA_EMISSAO] = $data;
            }
        }

        return ($bDataEmissaoRetorno && $bDataProrrogacaoRetorno && $bDataVencimentoRetorno);
    }

    /**
     * Verifica se foi enviado algum campo de relacionamento para ser feito a alteracao
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisAlteracao(&$parametros, &$mensagemErro)
    {
        $bContaRetorno            = true;
        $bFormaRecebimentoRetorno = true;
        $bChequeRetorno           = true;
        $bBoletoRetorno           = true;
        $bTransacaoCartaoRetorno  = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CONTA]) === false)) {
            $bContaRetorno = self::verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FORMA_RECEBIMENTO]) === true)&&(empty($parametros[CHAVE_FORMA_RECEBIMENTO]) === false)) {
            $bFormaRecebimentoRetorno = self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_RECEBIMENTO], ConstanteParametros::CHAVE_FORMA_RECEBIMENTO);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CHEQUE]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CHEQUE]) === false)) {
            $bChequeRetorno = self::verificaChequeExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CHEQUE]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_BOLETO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_BOLETO]) === false)) {
            $bBoletoRetorno = self::verificaBoletoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BOLETO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]) === false)) {
            $bTransacaoCartaoRetorno = self::verificaTransacaoCartaoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TRANSACAO_CARTAO]);
        }

        return ($bContaRetorno && $bFormaRecebimentoRetorno && $bChequeRetorno && $bBoletoRetorno && $bTransacaoCartaoRetorno);
    }

    /**
     * Realiza as regras aplicadas para criacao do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionadosObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosRelacionadosOpcionais($parametros, $mensagemErro) === true) {
                if ($this->validaDatasInformadas($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Executa as regras para realizar a alteração do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisAlteracao($parametros, $mensagemErro) === true) {
            if ($this->validaDatasInformadas($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se a Conta existe atraves do campo ID
     *
     * @param \App\Repository\Principal\TituloReceberRepository $tituloReceberRepository Repositorio da TituloReceberRepository
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\TituloReceber $resultadoORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaTituloReceberExisteId(\App\Repository\Principal\TituloReceberRepository $tituloReceberRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $tituloReceberRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "TituloReceber não foi encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se pode atualizar o saldo, apos o pagamento
     *
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     *
     * @return boolean
     */
    public function podeAtualizarSaldo(&$parametros, &$mensagemErro)
    {
        if (self::verificaValoresSaldo($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Cancela título a receber da conta a receber informada
     *
     * @param \App\Entity\Principal\ContaReceber $contaReceberORM
     */
    public static function cancelaTitulosReceber(\App\Entity\Principal\ContaReceber &$contaReceberORM)
    {
        /*
         * @var \App\Entity\Principal\TituloReceber $contasReceber
         */

        $titulosReceberORM = $contaReceberORM->getTituloRecebers();

        foreach ($titulosReceberORM as &$tituloReceberORM) {
            if ($tituloReceberORM->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
                if ($tituloReceberORM->getTransacoesCartao() == true) {
                $transacoesCartoes = $tituloReceberORM->getTransacoesCartao();
                    foreach ($transacoesCartoes as $transacaoCartao) {
                        if ($transacaoCartao->getSituacao() == 'PEN') {
                            $transacaoCartao->setSituacao(SituacoesSistema::SITUACAO_ESTORNADO);
                        }
                    }                
                }
                $tituloReceberORM->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);
            }
        }
    }


}
