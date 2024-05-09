<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class AtividadeExtraBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "usuarioRepository"        => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "itemRepository"           => $entityManager->getRepository(\App\Entity\Principal\Item::class),
                "salaFranqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
                "formaPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
    }

    /**
     * Verifica os relacionamentos obrigatorios para criação/atualização do registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaRelacionamentosObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaItemExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ITEM]) === true) {
                if (self::verificaSalaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) {
                    if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                        $listaFuncionarios = $parametros[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO];
                        if ((is_array($listaFuncionarios) === true)&&(count($listaFuncionarios) > 0)) {
                            $listaFuncionariosORM = [];
                            for ($i = 0; $i < count($listaFuncionarios); $i++) {
                                $funcionarioORM = null;
                                if (self::verificaFuncionarioExisteBO([ConstanteParametros::CHAVE_FUNCIONARIO => $listaFuncionarios[$i]], $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $funcionarioORM) === false) {
                                    $mensagemErro .= "O funcionario com a id:" . $listaFuncionarios[$i] . " não foi encontrado na base.";
                                    break;
                                }

                                $listaFuncionariosORM[] = $funcionarioORM;
                            }

                            if (empty($mensagemErro) === true) {
                                $parametros[ConstanteParametros::CHAVE_USUARIO_SOLICITANTE] = $parametros[ConstanteParametros::CHAVE_USUARIO];
                                unset($parametros[ConstanteParametros::CHAVE_USUARIO]);
                                $parametros[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO] = $listaFuncionariosORM;
                                return true;
                            }
                        } else {
                            $mensagemErro .= "Não foi enviado a lista de responsaveis pela execução da atividade extra.";
                        }//end if
                    }//end if
                }//end if
            }//end if
        }//end if

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
     * Verifica se as data e os horarios de inicio e fim estão corretos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaDataInicioTermino(&$parametros, &$mensagemErro)
    {
        $dataSelecionada = $parametros[ConstanteParametros::CHAVE_DATA];
        $horarioInicio   = $parametros[ConstanteParametros::CHAVE_HORA_INICIO];
        $horarioFim      = $parametros[ConstanteParametros::CHAVE_HORA_FINAL];
        if (self::converteData($dataSelecionada, $mensagemErro) === true) {
            if (self::converteData($horarioInicio, $mensagemErro) === true) {
                if (self::converteData($horarioFim, $mensagemErro) === true) {
                    if ($horarioInicio < $horarioFim) {
 
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO] = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $horarioInicio);
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM] = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $horarioFim);
                        //\DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $dataString);
                        unset($parametros[ConstanteParametros::CHAVE_DATA]);
                        unset($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
                        unset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                        return true;
                    } else {                        
                        $mensagemErro .= "O horario de término, precisa ser maior que o horario de inicio.";
                    }   
                } else {
                        if (self::verificaDataFinalMaiorDataInicio($horarioFim, $horarioInicio) === false) {
                        unset($parametros[ConstanteParametros::CHAVE_DATA]);
                        unset($parametros[ConstanteParametros::CHAVE_HORA_INICIO]);
                        unset($parametros[ConstanteParametros::CHAVE_HORA_FINAL]);
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO] = $horarioInicio;
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]    = $horarioFim;
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica as regras de relacionamentos obrigatórios para poder criar o registro
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
                if ($this->verificaDataInicioTermino($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica se existe a AtividadeExtra no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AtividadeExtraRepository $atividadeExtraRepository Repositorio da AtividadeExtra
     * @param integer $id Chave primaria da AtividadeExtra
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AtividadeExtra|null $atividadeExtraORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAtividadeExtraExiste(\App\Repository\Principal\AtividadeExtraRepository $atividadeExtraRepository, $id, &$mensagemErro, &$atividadeExtraORM)
    {
        $atividadeExtraORM = $atividadeExtraRepository->find($id);
        if (is_null($atividadeExtraORM) === true) {
            $mensagemErro = "AtividadeExtra não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
