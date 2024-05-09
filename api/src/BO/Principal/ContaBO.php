<?php
namespace App\BO\Principal;

use App\Helper\Logger;
use App\Helper\SituacoesSistema;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ContaBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\ContaRepository
     */
    private $contaRespository;

    /**
     *
     * @var \App\Repository\Principal\MovimentoContaRepository
     */
    private $movimentoRespository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->contaRespository = $entityManager->getRepository(\App\Entity\Principal\Conta::class);
        $this->movimentoRespository = $entityManager->getRepository(\App\Entity\Principal\MovimentoConta::class);
        parent::configuraGenericBO(
            [
                "bancoRepository"      => $entityManager->getRepository(\App\Entity\Principal\Banco::class),
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class)
                
                
            ]
        );
    }

    /**
     * Verifica se podemos prosseguir com o processo de salvar
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        $franqueadaORM = null;
        $bancoORM      = null;
        $bRetorno      = true;
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $franqueadaORM) === true) {
            if (self::verificaBancoExisteBO($parametros, $mensagemErro, $bancoORM) === true) {
                $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $franqueadaORM;
                $parametros[ConstanteParametros::CHAVE_BANCO]      = $bancoORM;
            }
        } else {
            $bRetorno = false;
        }

        return $bRetorno;
    }

    /**
     * Verifica se a Conta existe atraves do campo ID
     *
     * @param \App\Repository\Principal\ContaRepository $contaRepository Repositorio da Conta
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\Conta $resultadoORM Ponteiro para retornar o objeto
     * @param boolean $retornoComoArray Se deve retornar como array (false retorna objeto)
     *
     * @return boolean
     */
    public static function verificaContaIdExiste(\App\Repository\Principal\ContaRepository $contaRepository, $id, &$mensagemErro, &$resultadoORM=null, $retornoComoArray=false)
    {
        if ($retornoComoArray === true) {
            $resultadoORM = $contaRepository->buscarPorID($id);
        } else {
            $resultadoORM = $contaRepository->find($id);
        }

        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "Conta não foi encontrada na base de dados.";
            return false;
        }

        return true;
    }


 
 
     /**
     * Calcula o valor do saldo final da conta
     *
     * @param \App\Entity\Principal\Conta $contaORM 
     */
    public function atualizaSaldos(&$contaORM)
    {
        Logger::log('RECALCULANDO saldos da conta:'.$contaORM->getId(),'SALDOS');
            //log calculando saldos da conta
            $movimentos = $this->buscaMovimentos($contaORM);
            // $movimentoAnterior = null;
            $saldo = 0;
            $saldoPrevisto = 0;
            foreach ($movimentos as $movimento) {
                //log calculando movimento movimento
               
                // if($movimentoAnterior != null){
                //     $saldoPrevisto = $movimentoAnterior->getValorSaldoFinalConta();
                // }
                $valor_lancamento = (float) $movimento->getValorLancamento();
                if ($movimento->getOperacao() === SituacoesSistema::OPERACAO_DEBITO) {
                    $valor_lancamento *= -1;
                }
                
                $saldoPrevisto = $saldoPrevisto + $valor_lancamento;
                if ($movimento->getConciliado() === SituacoesSistema::CONCILIADO_SIM) {
                    //log movimento conciliado atualiza valor do saldo na conta                    
                    $saldo = $saldo + $valor_lancamento;
                    $contaORM->setValorSaldo($saldo);
                }               
                
                $movimento->setValorSaldoFinalConta($saldoPrevisto);
                
            }
    }

    /**
     * Consulta movimentos de conta
     *
     * @param \App\Entity\Principal\Conta *contaORM
     * @return array
     */
    public function buscaMovimentos(&$contaORM)
    {
        $conta = $contaORM->getId();
        
        $movimentosORM = $this->movimentoRespository->listaMovimentos($conta);
        return $movimentosORM;
    }

}
