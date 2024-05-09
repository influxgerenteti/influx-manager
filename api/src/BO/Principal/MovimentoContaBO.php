<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use App\Helper\Logger;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;

use App\BO\Principal\ContaBO;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MovimentoContaBO extends GenericBO
{

    /** 
    * 
    * @var ContaBO $contaBO
    */

    private $contaBO;


    function __construct(EntityManagerInterface $entityManager,ContaBO $contaBO)
    {
        parent::configuraGenericBO(
            [
                "contaRepository"              => $entityManager->getRepository(\App\Entity\Principal\Conta::class),
                "franqueadaRepository"         => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "tipoMovimentoContaRepository" => $entityManager->getRepository(\App\Entity\Principal\TipoMovimentoConta::class),
                "tituloPagarRepository"        => $entityManager->getRepository(\App\Entity\Principal\TituloPagar::class),
                "tituloReceberRepository"      => $entityManager->getRepository(\App\Entity\Principal\TituloReceber::class),
                "usuarioRepository"            => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "movimentoContaRepository"     => $entityManager->getRepository(\App\Entity\Principal\MovimentoConta::class),
                "formaPagamentoRepository"     => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
        $this->contaBO = $contaBO;
    }

    /**
     * Valida se os parametros que sao de relacionamento, sao validos, se algum deles nao for valido, ele ira retornar falso
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaParametrosNaoObrigatoriosExiste(&$parametros, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false) && (self::verificaTituloPagarExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false)) {
            $mensagemErro = 'O TituloPagar que você informou não existe.';
            return false;
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === true) && (is_null($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === false) && (self::verificaTituloReceberExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === false)) {
            $mensagemErro = 'O TituloReceber que você informou não existe.';
            return false;
        }

        return true;
    }

    /**
     * Valida se os parametros que sao de relacionamento, sao validos, se algum deles nao for valido, ele ira retornar falso
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionadosValidos(&$parametros, &$mensagemErro)
    {
        if (is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === true) {
            $parametros[ConstanteParametros::CHAVE_CONTA] = $parametros[ConstanteParametros::CHAVE_FRANQUEADA]->getContaPadrao();
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === true) {
            if (self::verificaTituloPagarExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TITULO_PAGAR]) === false) {
                $mensagemErro = 'Titulo pagar não encontrado!';
                return false;
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === true) {
            if (self::verificaTituloReceberExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TITULO_RECEBER]) === false) {
                $mensagemErro = 'Titulo receber não encontrado!';
                return false;
            }
        }

        if (self::verificaTipoMovimentoContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA]) === false) {
            $mensagemErro = 'Tipo de movimento de conta não encontrado';
            return false;
        }

        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false) {
            $mensagemErro = 'Franqueada não encontrada';
            return false;
        }

        if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === false) {
            $mensagemErro = 'Usuário não encontrado';
            return false;
        }

        if (self::verificaContaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CONTA]) === false) {
            return false;
        }

        if (self::verificaFormaPagamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FORMA_PAGAMENTO]) === false) {
            $mensagemErro = 'Forma de pagamento não encontrada';
            return false;
        }

        if (self::verificaParametrosNaoObrigatoriosExiste($parametros, $mensagemErro) === false) {
            return false;
        }

        return true;
    }

    /**
     * Converte os parâmetros necessários para gravar o MovimentoConta
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function converteParametrosNecessarios(&$parametros, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_MC_OPERACAO]) === false) || (empty($parametros[ConstanteParametros::CHAVE_MC_OPERACAO]) === true)) {
            $parametros[ConstanteParametros::CHAVE_MC_OPERACAO] = $parametros[ConstanteParametros::CHAVE_TIPO_MOVIMENTO_CONTA]->getTipoOperacao();
        }
        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === false) {
            self::converteDataAndSave($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL], $mensagemErro, "Contabil");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO]) === false) {
             self::converteDataAndSave($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO], $mensagemErro, "Movimento");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]) === false) {
           self::converteDataAndSave($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO], $mensagemErro, "do Deposito");
        }

        return true;
    }

    /**
     * Converte os campos de data de String para DateTime
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function validaDatas(&$parametros, &$mensagemErro)
    {
        $bRetornoDataContabil  = true;
        $bRetornoDataDeposito  = true;
        $bRetornoDataMovimento = true;
        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === false) {
            $bRetornoDataContabil = self::converteData($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL], $mensagemErro, "Contabil");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO]) === false) {
            $bRetornoDataContabil = self::converteData($parametros[ConstanteParametros::CHAVE_MC_DATA_MOVIMENTO], $mensagemErro, "Movimento");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]) === true && is_object($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO]) === false) {
            $bRetornoDataDeposito = self::converteData($parametros[ConstanteParametros::CHAVE_MC_DATA_DEPOSITO], $mensagemErro, "do Deposito");
        }

    
        return $bRetornoDataContabil && $bRetornoDataDeposito && $bRetornoDataMovimento;
    }

    /**
     * Verifica se a data contabil e data do deposito são menores do que o Datetime de agora
     *
     * @param array $parametros Ponteiro de Array de parametros da requisicao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    protected function verificaDatetimeMenorQueDatetimeAgora(&$parametros, &$mensagemErro)
    {
        $bRetorno = true;
        if (isset($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === false) {
            return $bRetorno;
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]) === false) {
            $bRetorno = self::verificaDatetimeDataMenorQueDatetimeAgora($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL], $mensagemErro, "Contabil", true);
        } else {
            unset($parametros[ConstanteParametros::CHAVE_MC_DATA_CONTABIL]);
        }

        return $bRetorno;
    }

    /**
     * Calcula o valor do saldo final da conta
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param \App\Entity\Principal\MovimentoConta $movimentoContaORM Objeto de MovimentoConta
     *
     * @return boolean
     */
    public function calculaValorSaldoFinalConta(&$parametros, &$movimentoContaORM)
    {
        // $movimentoContaORM->setValorTitulo(self::calculaValorParaAbatimentoNoSaldo($parametros));
       
        $conta = $movimentoContaORM->getConta();
        $valor_saldo_conta = (float) $conta->getValorSaldo();
        Logger::log('calculaValorSaldoFinalConta na conta:'.$conta->getId(),'SALDOS');
      
        // Somente altera o saldo em conta se o movimento for conciliado
        if ($movimentoContaORM->getConciliado() === SituacoesSistema::CONCILIADO_SIM) {
            $valor_lancamento = (float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_LANCAMENTO];
            if ($movimentoContaORM->getOperacao() === SituacoesSistema::OPERACAO_DEBITO) {
                $valor_lancamento *= -1;
            }

            $valor_saldo_final_conta = $valor_saldo_conta + $valor_lancamento;

            $movimentoContaORM->setValorSaldoFinalConta($valor_saldo_final_conta);
            $conta->setValorSaldo($valor_saldo_final_conta);
        } else {
            $movimentoContaORM->setValorSaldoFinalConta($valor_saldo_conta);
        }
       // $this->contaBO->atualizaSaldos($conta);
          
        return true;
    }


   
    /**
     * Realiza a verificacao das regras para permitir ou nao a criacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     * @param boolean $precisaTestarDataMaiorQueHoje Se precisa testar a data maior que hoje
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro, $precisaTestarDataMaiorQueHoje=true)
    {
        if ($this->verificaParametrosRelacionadosValidos($parametros, $mensagemErro) === true) {
            if ($this->validaDatas($parametros, $mensagemErro) === true) {
                if (($precisaTestarDataMaiorQueHoje === true) && ($this->verificaDatetimeMenorQueDatetimeAgora($parametros, $mensagemErro) === false)) {
                    return false;
                }

                if ((float) $parametros[ConstanteParametros::CHAVE_MC_VALOR_MONTANTE] <= 0) {
                    $mensagemErro = "O valor do montante deve ser maior que zero.";
                    return false;
                }

                $this->converteParametrosNecessarios($parametros, $mensagemErro);
                return true;
            }
        }

        return false;
    }

    /**
     * Realiza a verificacao das regras para permitir ou nao a atualizacao do registro
     *
     * @param array $parametros Ponteiro do array para realizar a formatacao
     * @param string $mensagemErro Mensagem de erro a retornar pro front-end
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->validaDatas($parametros, $mensagemErro) === true) {
            if ($this->verificaDatetimeMenorQueDatetimeAgora($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica a existencia do MovimentoConta atraves da ID
     *
     * @param \App\Repository\Principal\MovimentoContaRepository $movimentoContaRepository MovimentoConta repositorio
     * @param int $id Chave Primaria
     * @param string $mensagemErro Mensagem de erro para retornar pro front-end
     * @param null|\App\Entity\Principal\MovimentoConta $resultadoORM Resultado da consulta
     *
     * @return boolean
     */
    public function verificaMovimentoContaExisteId(\App\Repository\Principal\MovimentoContaRepository $movimentoContaRepository, $id, &$mensagemErro, &$resultadoORM=null)
    {
        $resultadoORM = $movimentoContaRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "O movimento de conta informado não foi encontrado.";
            return false;
        }

        return true;
    }


}
