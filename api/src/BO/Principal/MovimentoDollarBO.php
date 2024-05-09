<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MovimentoDollarBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\MovimentoDollarRepository
     */
    private $movimentoDollarRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->movimentoDollarRepository = $entityManager->getRepository(\App\Entity\Principal\MovimentoDollar::class);
        parent::configuraGenericBO(
            [
                "franqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "alunoRepository"           => $entityManager->getRepository(\App\Entity\Principal\Aluno::class),
                "contratoRepository"        => $entityManager->getRepository(\App\Entity\Principal\Contrato::class),
                "atividadeDollarRepository" => $entityManager->getRepository(\App\Entity\Principal\AtividadeDollar::class),
            ]
        );
    }

    /**
     * Verifica se existe o movimentoDollar no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\MovimentoDollarRepository $movimentoDollarRepos Repositorio do movimentoDollar
     * @param integer $id Chave primaria do movimentoDollar
     * @param string$mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\MovimentoDollar|null $movimentoDollarORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaMovimentoDollarExiste(\App\Repository\Principal\MovimentoDollarRepository $movimentoDollarRepos, $id, &$mensagemErro, &$movimentoDollarORM)
    {
        if (is_null($movimentoDollarRepos) === false) {
            $movimentoDollarORM = $movimentoDollarRepos->find($id);
        } else {
            $movimentoDollarORM = $this->movimentoDollarRepository->find($id);
        }

        if ($movimentoDollarORM === null) {
            $mensagemErro = "MovimentoDollar não encontrado na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se possui saldo suficiente para extornar os dados e criar novamente
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaSaldoSuficiente(&$parametros, &$mensagemErro)
    {
        $bRetorno = false;
        $arrayMovimentosDollar = [];
        foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR] as &$movimentoDollar) {
            if ($this->verificaMovimentoDollarExiste($this->movimentoDollarRepository, $movimentoDollar[ConstanteParametros::CHAVE_ID], $mensagemErro, $movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]) === false) {
                break;
            }

            if (isset($movimentoDollar[ConstanteParametros::CHAVE_ID]) === true) {
                unset($movimentoDollar[ConstanteParametros::CHAVE_ID]);
            }

            $alunoId = $movimentoDollar[ConstanteParametros::CHAVE_ALUNO]->getId();
            if (isset($arrayMovimentosDollar[$alunoId]) === false) {
                $arrayMovimentosDollar[$alunoId] = [];
            }

            $arrayMovimentosDollar[$alunoId][] = $movimentoDollar;
        }

        if (empty($mensagemErro) === true) {
            $movimento_banco = 0;
            $movimento_tela  = 0;
            foreach ($arrayMovimentosDollar as $alunosId => $alunos) {
                $bRetorno = true;
                foreach ($alunos as $movimentoDollar) {
                    $movimento_banco += $movimentoDollar[ConstanteParametros::CHAVE_MOVIMENTO_DOLLAR]->getValor();
                    $movimento_tela  += $movimentoDollar[ConstanteParametros::CHAVE_VALOR];
                }

                $param = [ConstanteParametros::CHAVE_ALUNO => $alunosId];
                self::verificaAlunoExisteBO($param, $mensagemErro, $param[ConstanteParametros::CHAVE_ALUNO], true);
                $saldo = $this->movimentoDollarRepository->filtrarMovimentoDollarPorAluno($param);
                if ($saldo - $movimento_banco + $movimento_tela < 0) {
                    $bRetorno     = false;
                    $mensagemErro = "Saldo Insuficiente para o Aluno " . $param[ConstanteParametros::CHAVE_ALUNO]->getPessoa()->getNomeContato() . ". Favor estornar a compra realizada pelo mesmo para realizar a atualização dos valores!";
                    break;
                }
            }
        }

        return $bRetorno;
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
        $bRetorno = false;

        if ((isset($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR]) === true) && (count($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_MOVIMENTOS_DOLLAR] as &$movimentoDollarMetaData) {
                $bRetorno = false;
                if (self::verificaFranqueadaExisteBO($movimentoDollarMetaData, $mensagemErro, $movimentoDollarMetaData[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
                    if (self::verificaAlunoExisteBO($movimentoDollarMetaData, $mensagemErro, $movimentoDollarMetaData[ConstanteParametros::CHAVE_ALUNO], true) === true) {
                        if (self::verificaContratoExisteBO($movimentoDollarMetaData, $mensagemErro, $movimentoDollarMetaData[ConstanteParametros::CHAVE_CONTRATO]) === true) {
                            if (self::verificaAtividadeDollarExisteBO($movimentoDollarMetaData, $mensagemErro, $movimentoDollarMetaData[ConstanteParametros::CHAVE_ATIVIDADE_DOLLAR]) === true) {
                                $bRetorno = true;
                            }
                        }
                    }
                }

                if ($bRetorno === false) {
                    break;
                }
            }
        } else {
            $mensagemErro .= "Não foi enviado os parametros de movimento dollar";
        }

        return $bRetorno;
    }

    /**
     * Verifica as regras para atualização de dados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAtualizar(&$parametros, &$mensagemErro)
    {
        if ($this->podeCriar($parametros, $mensagemErro) === true) {
            if ($this->verificaSaldoSuficiente($parametros, $mensagemErro) === true) {
                return true;
            }
        }

        return false;
    }


}
