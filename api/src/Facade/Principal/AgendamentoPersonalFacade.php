<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use Carbon\Carbon;
use DateTime;

/**
 *
 * @author Marcelo A Naegeler
 */
class AgendamentoPersonalFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AgendamentoPersonalRepository
     */
    private $agendamentoPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\SalaFranqueadaRepository
     */
    private $salaFranqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\CalendarioRepository
     */
    private $calendarioRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->agendamentoPersonalRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AgendamentoPersonal::class);
        $this->funcionarioRepository         = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->contratoRepository            = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->salaFranqueadaRepository      = self::getEntityManager()->getRepository(\App\Entity\Principal\SalaFranqueada::class);
        $this->calendarioRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Calendario::class);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $resultado = $this->agendamentoPersonalRepository->listar($parametros);
        return [
            ConstanteParametros::CHAVE_ITENS => $resultado,
            ConstanteParametros::CHAVE_TOTAL => count($resultado),
        ];
    }

    /**
     * Lista todos os registros do banco de dados sem filtros de semana
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listarTodosItens($parametros)
    {
        $resultado = $this->agendamentoPersonalRepository->filtrarAgendamentoPersonalPorPagina($parametros);
        return [
            ConstanteParametros::CHAVE_ITENS => $resultado->getItems(),
            ConstanteParametros::CHAVE_TOTAL => $resultado->getTotalItemCount(),
        ];
    }

        /**
     * Lista todos os registros do banco de dados sem filtros de semana
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function consultaDisponibilidadePersonal($parametros)
    {
        $resultado = $this->agendamentoPersonalRepository->consultaAgendamentoPersonalDisponibilidade($parametros);
        
        return [
            ConstanteParametros::CHAVE_ITENS => $resultado->getItems(),
            ConstanteParametros::CHAVE_TOTAL => $resultado->getTotalItemCount(),
        ];
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

    }

    /**
     * Busca os registros de agendamento pelo contrato
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $contrato_id id do contrato
     *
     * @return array|
     */
    public function buscarInfoPorContrato(&$mensagemErro, $contrato_id)
    {
        $contratoORM = $this->contratoRepository->find($contrato_id);
        if (is_null($contratoORM) === true) {
            $mensagemErro = 'Contrato não encontrado.';
            return [];
        }

        $agendamentos = $contratoORM->getAgendamentoPersonals();

        return $this->buscarInformacoesAgendamentos($mensagemErro, $agendamentos);
    }

    /**
     * Busca os registros de agendamento pelo contrato
     *
     * @param string $mensagemErro
     * @param array $agendamentos agendamentos de onde se quer pegar o horario e dias da semana
     *
     * @return array
     */
    public function buscarInformacoesAgendamentos(&$mensagemErro, $agendamentos)
    {
        $retorno = [
            "diasHoras"        => [],
            "dataInicio"       => '',
            "dataFim"          => '',
            "apelidoProfessor" => '',
            "descricaoSala"    => '',
        ];
        if (count($agendamentos) > 0) {
            $semana = [
                'Sun' => 'Domingo',
                'Mon' => 'Segunda-Feira',
                'Tue' => 'Terca-Feira',
                'Wed' => 'Quarta-Feira',
                'Thu' => 'Quinta-Feira',
                'Fri' => 'Sexta-Feira',
                'Sat' => 'Sábado',
            ];
            $primeiroAgendamento  = $agendamentos[0];
            $primeiroDateTime     = $primeiroAgendamento->getInicio();
            $primeiroDiaSemana    = $primeiroDateTime->format("D");
            $primeiroHorario      = $primeiroDateTime->format("H:i");
            $personalDataInicio   = $primeiroDateTime;
            $personalDataFim      = $primeiroDateTime;
            $concatenarDiaHorario = true;
            foreach ($agendamentos as $agendamento) {
                $dataAgendamento = $agendamento->getInicio();
                $agendamentoId   = $agendamento->getId();
                if ($dataAgendamento < $personalDataInicio) {
                    $personalDataInicio = $dataAgendamento;
                }

                if ($dataAgendamento > $personalDataFim) {
                    $personalDataFim = $dataAgendamento;
                }

                if ($primeiroDateTime !== $dataAgendamento && $primeiroDiaSemana === $dataAgendamento->format("D") && $primeiroHorario === $dataAgendamento->format("H:i")) {
                    $concatenarDiaHorario = false;
                }

                if ($concatenarDiaHorario === true) {
                    $retorno["diasHoras"][] = [
                        "dia"            => $semana[$dataAgendamento->format("D")],
                        "hora"           => $dataAgendamento->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format("H:i"),
                        "timestamp"      => $dataAgendamento,
                        "agendamento_id" => $agendamentoId,
                    ];
                }
            }//end foreach

            $retorno["dataInicio"]       = $personalDataInicio->format("d/m/Y");
            $retorno["dataFim"]          = $personalDataFim->format("d/m/Y");
            $retorno["apelidoProfessor"] = $primeiroAgendamento->getFuncionario()->getApelido();
            $retorno["descricaoSala"]    = $primeiroAgendamento->getSalaFranqueada()->getSala()->getDescricao();
        }//end if

        return $retorno;
    }

    /**
     * Compara as horas e minutos retornando se a primeira é maior que a segunda
     *
     * @param int $h1 hora 1
     * @param int $h2 hora 2
     * @param int $m1 minutos 1
     * @param int $m2 minutos 2
     *
     * @return bool
     */
    private function compararHoras($h1, $h2, $m1, $m2)
    {
        return ($h1 > $h2) || (($h1 === $h2) && ($m1 > $m2));
    }

    /**
     * Gera a nova index para loop
     *
     * @param int $indexCorrente
     * @param int $countAgendamentos
     *
     * @return int
     */
    private function novaIndexAgendamentos($indexCorrente, $countAgendamentos)
    {
        if ($indexCorrente === ($countAgendamentos - 1)) {
            return 0;
        } else {
            return $indexCorrente + 1;
        }
    }

    /**
     * Cria o planejamento das aulas personal
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return bool
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $quantidadeCreditos = $parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL];
        $creditosPersonal   = [
            ConstanteParametros::CHAVE_CONTRATO   => $parametros[ConstanteParametros::CHAVE_CONTRATO],
            ConstanteParametros::CHAVE_QUANTIDADE => $quantidadeCreditos,
            ConstanteParametros::CHAVE_SALDO      => $quantidadeCreditos,
        ];

        $creditosPersonalORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\CreditosPersonal::class, true, $creditosPersonal);
        self::persistSeguro($creditosPersonalORM, $mensagemErro);

        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = ($parametros[ConstanteParametros::CHAVE_CONTRATO])->getFranqueada();

        $funcionario = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $sala        = $this->salaFranqueadaRepository->find($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);

        $calendarioPadrao = $this->calendarioRepository->buscarCalendarioPadrao();

        if (is_null($calendarioPadrao) === true) {
            $mensagemErro = 'Calendário padrão não configurado';
            return false;
        }

        $diasSemana = [
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ];

        $agendamentos      = $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL];
        $countAgendamentos = count($agendamentos);
        $agendamentosORM   = [];
        $dias          = [];
        $dataCorrente  = null;
        $indexCorrente = 0;

        for ($i = 0; $i < $countAgendamentos; $i++) {
            $data   = Carbon::parse($agendamentos[$i]['inicio']);
            $dias[] = [
                'dia_semana' => $data->dayOfWeek,
                'data'       => $data,
                'horas'      => $data->hour,
                'minutos'    => $data->minute,
            ];
        }

        while ($quantidadeCreditos > 0) {
            $dia = $dias[$indexCorrente];

            if (count($agendamentosORM) >= count($dias)) {
                if (($dataCorrente->dayOfWeek === $dia['dia_semana']) && ($this->compararHoras($dia['horas'], $dataCorrente->hour, $dia['minutos'], $dataCorrente->minute) === true)) {
                    $dataCorrente = $dataCorrente->setTime($dia['horas'], $dia['minutos']);
                } else {
                    $dataCorrente = $dataCorrente->next($diasSemana[$dia['dia_semana']])->setTime($dia['horas'], $dia['minutos']);
                }
            } else {
                $dataCorrente = $dia['data'];
            }

            $possuiDiaNaoLetivo = $this->calendarioRepository->buscaDataNaoLetiva($parametros[ConstanteParametros::CHAVE_FRANQUEADA], $dataCorrente);
            if (is_null($possuiDiaNaoLetivo) === false) {
                $indexCorrente = $this->novaIndexAgendamentos($indexCorrente, $countAgendamentos);
                continue;
            }

            $dadosAgendamento = [
                ConstanteParametros::CHAVE_CONTRATO          => $parametros[ConstanteParametros::CHAVE_CONTRATO],
                ConstanteParametros::CHAVE_FRANQUEADA        => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_CREDITOS_PERSONAL => $creditosPersonalORM,
                ConstanteParametros::CHAVE_FUNCIONARIO       => $funcionario,
                ConstanteParametros::CHAVE_SALA_FRANQUEADA   => $sala,
                ConstanteParametros::CHAVE_INICIO            => $dataCorrente->toDate(),
                ConstanteParametros::CHAVE_DATA_AULA         => $dataCorrente->toDate(),
                ConstanteParametros::CHAVE_REAGENDADO        => false,
            ];

            $agendamentoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AgendamentoPersonal::class, true, $dadosAgendamento);
            self::persistSeguro($agendamentoORM, $mensagemErro);

            $agendamentosORM[] = $agendamentoORM;

            $indexCorrente = $this->novaIndexAgendamentos($indexCorrente, $countAgendamentos);
            $quantidadeCreditos--;
        }//end while

        return true;
    }

    /**
     * Atualiza o planejamento das aulas personal
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem alterados no objeto
     *
     * @return bool
     */
    public function atualizar(&$mensagemErro, $parametros=[])
    {
        $agendamentoORM = $this->agendamentoPersonalRepository->find($parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]);

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === true && is_null($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === false) {
            $agendamentoORM->setInicio($parametros[ConstanteParametros::CHAVE_DATA_AULA]);
            var_dump($agendamentoORM->getId());
        }

        self::flushSeguro($mensagemErro);

        return true;
    }


    /**
     * Atualiza o planejamento das aulas personal
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem alterados no objeto
     *
     * @return bool
     */
    public function reagendarPersonal(&$mensagemErro, &$parametros=[], $id)
    {
        $agendamentoORM = $this->agendamentoPersonalRepository->find($id);

        if (isset($agendamentoORM) === false && is_null($agendamentoORM) === true) {
            $mensagemErro = 'Agenda Personal não encontrada!';
            return false;
        }
        
        if (isset($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === true && is_null($parametros[ConstanteParametros::CHAVE_DATA_AULA]) === false) {
            if (isset($parametros['permanente']) === true && is_null($parametros['permanente']) === false) {
                //vai alterar todas as agendas da data fornecida pra frente sendo no mesmo dia da semana(da data anterior)
                $dataAgenda = $parametros[ConstanteParametros::CHAVE_DATA_AULA];
                $timezone = new \DateTimeZone('America/Sao_Paulo');
                $dataObj = new DateTime($dataAgenda, $timezone);

                $data_Anterior = $agendamentoORM->getInicio()->format("Y-m-d\TH:i:s.uP");

                if ($parametros['permanente'] == 1) {
                    $qtdadesAulasPAlterar = $this->agendamentoPersonalRepository->buscarQtdadeAgendasAbertasPeloDiaSemana($agendamentoORM->getContrato()->getId(), 
                                                                                                  $data_Anterior);
                   
                   $diasLivres =  $this->agendamentoPersonalRepository->buscarDisponibilidadeNasSalasNosHorariosPersonal(count($qtdadesAulasPAlterar), $parametros);

                    if ($diasLivres == 0) {
                        foreach ($qtdadesAulasPAlterar as $agendamento) {
                            $agendamentoSubsequenteORM = $this->agendamentoPersonalRepository->find($agendamento['id']);                       
                            $agendamentoSubsequenteORM->setInicio($dataObj);
                            $agendamentoSubsequenteORM->setDataAula($dataObj);
                            $agendamentoSubsequenteORM->setReagendado(1);
                            if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
                                $salaFranqueada = $this->salaFranqueadaRepository->findOneBy(["id" => $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]]);
                                $agendamentoSubsequenteORM->setSalaFranqueada($salaFranqueada);
                            }
                            if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                                $funcionario = $this->funcionarioRepository->findOneBy(["id" => $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]]);
                                $agendamentoSubsequenteORM->setFuncionario($funcionario);
                            }
                            self::flushSeguro($mensagemErro);
                            $dataObj->modify("+1 week");
                          //  self::flushSeguro($mensagemErro);
                        }                        
                    } else {
                        $mensagemErro = 'Esse horário está com agenda cheia em algum ou mais dias subsequentes!';
                        return false;
                    }
                } 
                else {
                    //alterar o apenas aquele horario
                    $agendamentoORM->setDataAula($dataObj);
                    $agendamentoORM->setReagendado(1);
                    if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
                        $salaFranqueada = $this->salaFranqueadaRepository->findOneBy(["id" => $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]]);
                        $agendamentoORM->setSalaFranqueada($salaFranqueada);
                    }
                    if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && is_null($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                        $funcionario = $this->funcionarioRepository->findOneBy(["id" => $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]]);
                        $agendamentoORM->setFuncionario($funcionario);
                    }
                }  
            } 
        }

        $parametros[ConstanteParametros::CHAVE_DADOS_ANTERIORES] = $agendamentoORM->getInicio()->format("d-m-Y H:i:s");
        $parametros[ConstanteParametros::CHAVE_DADOS_ATUAIS] = (new DateTime(substr($parametros[ConstanteParametros::CHAVE_DATA_AULA], 0, 19)))->format("d-m-Y H:i:s");
        $parametros[ConstanteParametros::CHAVE_ALUNO] = $agendamentoORM->getContrato()->getAluno()->getPessoa()->getNomeContato();
        $parametros[ConstanteParametros::CHAVE_ALUNO_ID] = $agendamentoORM->getContrato()->getAluno()->getId();
        $parametros[ConstanteParametros::CHAVE_INSTRUTOR_NOME] = $funcionario->getPessoa()->getNomeContato();

        self::flushSeguro($mensagemErro);
        return true;
    }

   /**
     * Calcula Ultima data Personal
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem alterados no objeto
     *
     * @return string
     */
    public function calculaUltimaDataPersonal(&$mensagemErro, $parametros=[], $franqueadaORM)
    {
        $quantidadeCreditos = $parametros[ConstanteParametros::CHAVE_CREDITOS_PERSONAL];
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = $franqueadaORM;
        
        $calendarioPadrao = $this->calendarioRepository->buscarCalendarioPadrao();
        if (is_null($calendarioPadrao) === true) {
            $mensagemErro = 'Calendário padrão não configurado';
            return false;
        }
        
        $diasSemana = [
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ];
        
        $agendamentos      = $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL];
        $countAgendamentos = count($agendamentos);
        $agendamentosORM   = [];
        $dias          = [];
        $dataCorrente  = null;
        $indexCorrente = 0;
        
        for ($i = 0; $i < $countAgendamentos; $i++) {
            $data   = Carbon::parse($agendamentos[$i]['inicio']);
            $dias[] = [
                'dia_semana' => $data->dayOfWeek,
                'data'       => $data,
                'horas'      => $data->hour,
                'minutos'    => $data->minute,
            ];
        }
        
        while ($quantidadeCreditos > 0) {
            $dia = $dias[$indexCorrente];
            
            if ($dataCorrente) {
                if (($dataCorrente->dayOfWeek === $dia['dia_semana']) && ($this->compararHoras($dia['horas'], $dataCorrente->hour, $dia['minutos'], $dataCorrente->minute) === true)) {
                    $dataCorrente = $dataCorrente->setTime($dia['horas'], $dia['minutos']);
                } else {
                    $dataCorrente = $dataCorrente->next($diasSemana[$dia['dia_semana']])->setTime($dia['horas'], $dia['minutos']);
                }
            } else {
                $dataCorrente = $dia['data'];
            }
            
            $possuiDiaNaoLetivo = $this->calendarioRepository->buscaDataNaoLetiva($parametros[ConstanteParametros::CHAVE_FRANQUEADA], $dataCorrente);
            if (is_null($possuiDiaNaoLetivo) === false) {
                $indexCorrente = $this->novaIndexAgendamentos($indexCorrente, $countAgendamentos);
                continue;
            }
                
                $indexCorrente = $this->novaIndexAgendamentos($indexCorrente, $countAgendamentos);
                $quantidadeCreditos--;
            }//end while
          
            $ultimaData = new dateTime($dataCorrente->day.'-'.$dataCorrente->month.'-'.$dataCorrente->year);
            return $ultimaData->format('Y-m-d').'T12:00:00.000Z';

    }



    public function gerarDadosRelatorioAulasDesmarcadas($filtros) {
        return $this->agendamentoPersonalRepository->buscarDadosRelatorioAulasDesmarcadas($filtros);
    }
}
