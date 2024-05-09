<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AgendaCompromissoBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class AgendaCompromissoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AgendaCompromissoRepository
     */
    private $agendaCompromissoRepository;

    /**
     *
     * @var \App\BO\Principal\AgendaCompromissoBO
     */
    private $agendaCompromissoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->agendaCompromissoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AgendaCompromisso::class);
        $this->agendaCompromissoBO         = new AgendaCompromissoBO(self::getEntityManager());
    }

    /**
     * Altera os dados do pai ou do filho
     *
     * @param \App\Entity\Principal\AgendaCompromisso $paiFilhoORM
     * @param array $parametros
     * @param boolean $bRealizarExclusao
     */
    protected function alterarFilhos(&$paiFilhoORM, $parametros, $bRealizarExclusao=false, &$mensagemErro="")
    {
        $qtdFilhos            = $paiFilhoORM->getAgendaCompromissos()->count();
        $filhosAtreladosAoPai = $paiFilhoORM->getAgendaCompromissos();
        $horasInicio          = null;
        $horasFim = null;
        if (isset($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]) === true) {
            $horasInicio = explode(":", $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]->format("H:i"));
            unset($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]) === true) {
            $horasFim = explode(":", $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]->format("H:i"));
            unset($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]);
        }

        for ($i = 0;$i < $qtdFilhos;$i++) {
            $filhoORM = $filhosAtreladosAoPai->get($i);
            if ($bRealizarExclusao === true) {
                self::removerSeguro($filhoORM, $mensagemErro);
            } else {
                if (is_null($horasInicio) === false) {
                    $dataHoraInicio = clone $filhoORM->getDataHoraInicio();
                    $dataHoraInicio->setTime($horasInicio[0], $horasInicio[1]);
                    $filhoORM->setDataHoraInicio($dataHoraInicio);
                }

                if (is_null($horasFim) === false) {
                    $dataHoraFim = clone $filhoORM->getDataHoraFim();
                    $dataHoraFim->setTime($horasFim[0], $horasFim[1]);
                    $filhoORM->setDataHoraFim($dataHoraFim);
                }

                self::getFctHelper()->setParams($parametros, $filhoORM);
            }
        }

        if ($bRealizarExclusao === true) {
            self::removerSeguro($paiFilhoORM, $mensagemErro);
        } else {
            if (is_null($horasInicio) === false) {
                $dataHoraInicio = clone $paiFilhoORM->getDataHoraInicio();
                $dataHoraInicio->setTime($horasInicio[0], $horasInicio[1]);
                $paiFilhoORM->setDataHoraInicio($dataHoraInicio);
            }

            if (is_null($horasFim) === false) {
                $dataHoraFim = clone $paiFilhoORM->getDataHoraFim();
                $dataHoraFim->setTime($horasFim[0], $horasFim[1]);
                $paiFilhoORM->setDataHoraFim($dataHoraFim);
            }

            self::getFctHelper()->setParams($parametros, $paiFilhoORM);
        }
    }

    /**
     * Realiza as alterações nos objetos e em seus filhos, partindo desde um objeto unico, aos filhos atrelados a este objeto
     *
     * @param \App\Entity\Principal\AgendaCompromisso $paiFilhoORM
     * @param boolean $bAplicarNosFilhos
     * @param array $parametros
     * @param boolean $bRealizarExclusao
     * @param string $mensagemErro
     */
    protected function efetuarMudancasNosObjetos(&$paiFilhoORM, $bAplicarNosFilhos, $parametros, $bRealizarExclusao=false, &$mensagemErro="")
    {
        $qtdFilhos = $paiFilhoORM->getAgendaCompromissos()->count();
        $existeMenuPaiAtrelado = is_null($paiFilhoORM->getPeriodoPai()) === false;
        if ($bAplicarNosFilhos === true) {
            if (($qtdFilhos > 0) || ($existeMenuPaiAtrelado === true)) {
                if ($existeMenuPaiAtrelado === true) {
                    $idPai  = $paiFilhoORM->getPeriodoPai()->getId();
                    $paiORM = $this->agendaCompromissoRepository->find($idPai);
                    $this->alterarFilhos($paiORM, $parametros, $bRealizarExclusao, $mensagemErro);
                } else if (($qtdFilhos > 0) && ($existeMenuPaiAtrelado === false)) {
                    $this->alterarFilhos($paiFilhoORM, $parametros, $bRealizarExclusao, $mensagemErro);
                }
            } else {
                if ($bRealizarExclusao === true) {
                    self::removerSeguro($paiFilhoORM, $mensagemErro);
                } else {
                    self::getFctHelper()->setParams($parametros, $paiFilhoORM);
                }
            }
        } else {
            if (($qtdFilhos > 0) && ($existeMenuPaiAtrelado === false)) {
                $this->configurarProximoPai($paiFilhoORM);
            }

            $paiFilhoORM->setPeriodoPai(null);
            if ($bRealizarExclusao === true) {
                self::removerSeguro($paiFilhoORM, $mensagemErro);
            } else {
                self::getFctHelper()->setParams($parametros, $paiFilhoORM);
            }
        }//end if
    }

    /**
     * Configura o proximo pai
     *
     * @param \App\Entity\Principal\AgendaCompromisso $paiORM
     */
    protected function configurarProximoPai(&$paiORM)
    {
        $filhosAtrelados = $paiORM->getAgendaCompromissos();
        $qtdFilhos       = $filhosAtrelados->count();
        $proximosFilhos  = $this->agendaCompromissoRepository->findBy(["periodo_pai" => $paiORM->getId()], ["id" => "ASC"]);
        if (count($proximosFilhos) > 0) {
            $filhoDaVez = $proximosFilhos[0];
            for ($i = 0; $i < $qtdFilhos;$i++) {
                $agendaCompromissoORM = $filhosAtrelados->get($i);
                if ($filhoDaVez->getId() === $agendaCompromissoORM->getId()) {
                    $filhoDaVez->setPeriodoPai(null);
                } else {
                    $agendaCompromissoORM->setPeriodoPai($filhoDaVez);
                }
            }
        }
    }

    /**
     * Verifica se a data solicitada está disponivel para o funcionario
     *
     * @param array $parametros
     *
     * @return array
     */
    public function verificarDisponibilidadeFuncionario($parametros)
    {
        return [
            "disponivel" => $this->agendaCompromissoRepository->verificaDisponibilidadeAgendaFuncionario($parametros),
        ];
    }

    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros, $usuarioId)
    {
        return $this->agendaCompromissoBO->retornarAgendaCustomizada($parametros, $usuarioId);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $resultado = $this->agendaCompromissoRepository->buscarPorId($id);
        if (count($resultado) < 1) {
            $mensagemErro = "AgendaCompromisso não encontrada";
        }

        return $resultado;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\AgendaCompromisso
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        $agendaCompromissoOriginal = null;
        if ($this->agendaCompromissoBO->podeCriar($parametros, $mensagemErro) === true) {
            $dataInicio   = clone $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO];
            $dataFinal    = clone $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM];
            $horaFimArray = explode(":", $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]->format("H:i"));
            $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM] = (clone $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO])->setTime($horaFimArray[0], $horaFimArray[1]);
            $agendaCompromissoOriginal = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AgendaCompromisso::class, true, $parametros);
            self::persistSeguro($agendaCompromissoOriginal, $mensagemErro);
            $horaInicioArray = explode(":", $dataInicio->format("H:i"));
            $horaFimArray    = explode(":", $dataFinal->format("H:i"));
            $dataInicio->setTime(0, 0, 1);
            $dataFinal->setTime(0, 0, 1);
            $diasDiferenca = 0;
            if (self::getFctHelper()->comparaDataAMenorIgualDataB($dataInicio, $dataFinal, $diasDiferenca) === true) {
                if ($diasDiferenca > 0) {
                    $dataHoraInicio = clone $dataInicio;
                    $dataHoraFim    = clone $dataInicio;
                    for ($i = 0;$i < $diasDiferenca;$i++) {
                        $dataHoraInicio = clone $dataHoraInicio;
                        $dataHoraFim    = clone $dataHoraFim;
                        $dataHoraInicio->modify("+1 day");
                        $dataHoraInicio->setTime($horaInicioArray[0], $horaInicioArray[1]);
                        $dataHoraFim->modify("+1 day");
                        $dataHoraFim->setTime($horaFimArray[0], $horaFimArray[1]);
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO] = $dataHoraInicio;
                        $parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]    = $dataHoraFim;
                        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AgendaCompromisso::class, true, $parametros);
                        $objetoORM->setPeriodoPai($agendaCompromissoOriginal);
                        self::persistSeguro($objetoORM, $mensagemErro);
                    }
                }
            } else {
                $mensagemErro = "A data de inicio precisa ser maior que a data fim.";
            }//end if

            if (empty($mensagemErro) === true) {
                self::flushSeguro($mensagemErro);
            }
        }//end if

        return $agendaCompromissoOriginal;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->agendaCompromissoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Registro não encontrado na base de dados.";
        } else {
            if ($this->agendaCompromissoBO->podeAlterar($parametros, $mensagemErro) === true) {
                $this->efetuarMudancasNosObjetos($objetoORM, (bool) $parametros[ConstanteParametros::CHAVE_FLAG_ALTERAR_TODOS], $parametros);
                self::flushSeguro($mensagemErro);
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     * @param bool $bAlterarTodos Aplicar alterações para todos
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id, $bAlterarTodos)
    {
        $objetoORM = $this->agendaCompromissoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Registro não encontrado na base de dados.";
        } else {
            $this->efetuarMudancasNosObjetos($objetoORM, $bAlterarTodos, [], true, $mensagemErro);
            // $this->configurarProximoPai($objetoORM);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
