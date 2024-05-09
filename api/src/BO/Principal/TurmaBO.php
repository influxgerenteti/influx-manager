<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Marcelo André Naegeler
 */
class TurmaBO extends GenericBO
{

    /**
     *
     * @var \App\BO\Principal\CalendarioBO
     */
    private $calendarioBO;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var array
     */
    private static $diasDaSemana = [
        'DOM' => [
            'ordem' => 0,
            'dia'   => 'sunday',
        ],
        'SEG' => [
            'ordem' => 1,
            'dia'   => 'monday',
        ],
        'TER' => [
            'ordem' => 2,
            'dia'   => 'tuesday',
        ],
        'QUA' => [
            'ordem' => 3,
            'dia'   => 'wednesday',
        ],
        'QUI' => [
            'ordem' => 4,
            'dia'   => 'thursday',
        ],
        'SEX' => [
            'ordem' => 5,
            'dia'   => 'friday',
        ],
        'SAB' => [
            'ordem' => 6,
            'dia'   => 'saturday',
        ],
    ];

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->turmaRepository = $entityManager->getRepository(\App\Entity\Principal\Turma::class);
        $this->calendarioBO    = new CalendarioBO($entityManager);
        parent::configuraGenericBO(
            [
                "turmaRepository"           => $entityManager->getRepository(\App\Entity\Principal\Turma::class),
                "franqueadaRepository"      => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "modalidadeTurmaRepository" => $entityManager->getRepository(\App\Entity\Principal\ModalidadeTurma::class),
                "calendarioRepository"      => $entityManager->getRepository(\App\Entity\Principal\Calendario::class),
                "livroRepository"           => $entityManager->getRepository(\App\Entity\Principal\Livro::class),
                "funcionarioRepository"     => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "salaFranqueadaRepository"  => $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class),
                "horarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Horario::class),
                "valorHoraLinhasRepository" => $entityManager->getRepository(\App\Entity\Principal\ValorHoraLinhas::class),
                "semestreRepository"        => $entityManager->getRepository(\App\Entity\Principal\Semestre::class),
                "cursoRepository"           => $entityManager->getRepository(\App\Entity\Principal\Curso::class),
            ]
        );
    }

    /**
     * Validação dos dados para criação de registro
     *
     * @param array $params Parâmetros que possuam os campos das entidades e valores
     * @param string $mensagemErro
     * @param int $id ID da turma (caso houver)
     *
     * @return boolean
     */
    public function podeCriar (&$params, &$mensagemErro, $id=null)
    {
        // caso o valor da Modalidade seja 4(conforme no Banco) significa que é Hybrid
        $modalidade_turma_id = $params['modalidade_turma'];

        if (self::verificaModalidadeTurmaExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false) {
            return false;
        }
        
        if (self::verificaLivroExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_LIVRO]) === false) {
            return false;
        }

        if (self::verificaCursoExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_CURSO]) === false) {
            return false;
        }

        if ((empty($params[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) && (self::verificaFuncionarioExisteBO($params, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $params[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            return false;
        }
        
        if ((empty($params[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) && (self::verificaSalaFranqueadaExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false)) {
            return false;
        }
        
        if (self::verificaHorarioExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_HORARIO]) === false) {
            return false;
        }
        
        if ((empty($params[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === false) && (self::verificaValorHoraLinhasExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_VALOR_HORA_LINHAS]) === false)) {
            return false;
        }
        
        if (self::verificaSemestreExisteBO($params, $mensagemErro, $params[ConstanteParametros::CHAVE_SEMESTRE]) === false) {
            return false;
        }
      
       
        if ($this->validarDatasInicioFim($params, $mensagemErro, $modalidade_turma_id) === false) {
            return false;
        }
        
        if ($params[ConstanteParametros::CHAVE_SITUACAO] !== SituacoesSistema::SITUACAO_TURMA_ENCERRADA) {
            if ($this->salaEstaOcupadaNoHorario($params, $id, $mensagemErro) === true) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validação dos dados para excluir turma
     *
     * @param string $mensagemErro
     * @param int $id ID da turma
     * @param \App\Entity\Principal\Turma $turmaORM
     *
     * @return boolean
     */
    public function podeExcluir (&$mensagemErro, $id, \App\Entity\Principal\Turma $turmaORM)
    {

        if ($this->verificaSeExisteDiariosCadastradosParaTurma($id) === true) {
            $mensagemErro = "Não é possivel excluir a turma, pois já existe diarios cadastrados.";
            return false;
        }

        if ($turmaORM->getExcluido() === '1') {
            $mensagemErro = "Não é possivel excluir a turma, pois ela já estava excluída.";
            return false;
        }

        return true;
    }

    /**
     * Validação das datas de início e fim
     *
     * @param array $params Parâmetros que possuam os campos das entidades e valores
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function validarDatasInicioFim (&$params, &$mensagemErro, $modalidade_turma_id)
    {
         
        if (isset($params[ConstanteParametros::CHAVE_DATA_INICIO]) === true) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($params[ConstanteParametros::CHAVE_DATA_INICIO], $params[ConstanteParametros::CHAVE_DATA_INICIO]);
        }

        if (isset($params[ConstanteParametros::CHAVE_DATA_INICIO]) === false || $params[ConstanteParametros::CHAVE_DATA_INICIO] === false) {
            $mensagemErro = 'Data de início é inválida.';
            return false;
        }

        if (isset($params[ConstanteParametros::CHAVE_DATA_FIM]) === true) {
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($params[ConstanteParametros::CHAVE_DATA_FIM], $params[ConstanteParametros::CHAVE_DATA_FIM]);
        }

        if ($modalidade_turma_id != 4) {
            if (isset($params[ConstanteParametros::CHAVE_DATA_FIM]) === false || $params[ConstanteParametros::CHAVE_DATA_FIM] === false) {
            //  $mensagemErro = 'Data de término é inválida.';
            //  return false;
            }
            
            if ($params[ConstanteParametros::CHAVE_DATA_FIM] < $params[ConstanteParametros::CHAVE_DATA_INICIO]) {
                $mensagemErro = 'Data de término deve ser superior a data de início.';
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica se a sala está ocupada em determinado horário
     *
     * @param \App\Entity\Principal\SalaFranqueada $salaFranqueada
     * @param \App\Entity\Principal\Horario $horario
     * @param int $id Turma
     * @param string $mensagemErro
     *
     * @return boolean
     */
    private function salaEstaOcupadaNoHorario ($params, $id=null, &$mensagemErro='')
    {
        $salaFranqueada = $params[ConstanteParametros::CHAVE_SALA_FRANQUEADA];
        $horario = $params[ConstanteParametros::CHAVE_HORARIO];
        $dataInicio = $params[ConstanteParametros::CHAVE_DATA_INICIO];
        $dataFim = $params[ConstanteParametros::CHAVE_DATA_FIM];
        
        $salaEstaOcupada = $this->turmaRepository->salaEstaOcupadaNoHorario($salaFranqueada, $horario,$dataInicio,$dataFim, $id);

        if ($salaEstaOcupada === true) {
            $mensagemErro = 'Esta sala está ocupada no horário selecionado.';
            return true;
        }

        return false;
    }

    /**
     * Verifica se o funcionário está ocupado em determinado horário
     *
     * @param \App\Entity\Principal\Funcionario $funcionario
     * @param \App\Entity\Principal\Horario $horario
     * @param int $id Turma
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function funcionarioEstaOcupadoNoHorario ($funcionario, $horario, $id=null, &$mensagemErro="")
    {
        $funcionarioEstaOcupado = $this->turmaRepository->funcionarioEstaOcupadoNoHorario($funcionario, $horario, $id);

        if ($funcionarioEstaOcupado === true) {
            $mensagemErro = 'Este professor está ocupado no horário selecionado.';
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe diarios cadastrados para a turma
     *
     * @param int $id
     *
     * @return boolean
     */
    private function verificaSeExisteDiariosCadastradosParaTurma($id)
    {
        if ($id !== null) {
            $turma      = $this->turmaRepository->find($id);
            $turmaAulas = $turma->getTurmaAulas();
            $bExisteDiarioParaTurma = false;
            foreach ($turmaAulas as $turmaAulaORM) {
                $bExisteDiarioParaTurma = $turmaAulaORM->getAlunoDiarios()->count() > 0;
                if ($bExisteDiarioParaTurma === true) {
                    break;
                }
            }

            if ($bExisteDiarioParaTurma === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Converte os parametros para realização do calculo
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param NULL|\App\Entity\Principal\Franqueada $franqueadaORM
     * @param NULL|\App\Entity\Principal\Livro $livroORM
     * @param NULL|\App\Entity\Principal\Horario $horarioORM
     * @param NULL|\DateTime $dataObjeto
     *
     * @return boolean
     */
    private function converteParametrosParaCalculo($parametros, &$mensagemErro, &$franqueadaORM, &$livroORM, &$horarioORM, &$dataObjeto)
    {
        if (self::verificaFranqueadaExisteBO([ConstanteParametros::CHAVE_FRANQUEADA => VariaveisCompartilhadas::$franqueadaID], $mensagemErro, $franqueadaORM) === true) {
            if (self::verificaLivroExisteBO($parametros, $mensagemErro, $livroORM) === true) {
                if (self::verificaHorarioExisteBO($parametros, $mensagemErro, $horarioORM, true) === true) {
                    if (\App\Helper\FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_INICIO], $dataObjeto) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Realiza o calculo do ultimo dia de aula, baseado nos parametros fornecidos conforme função descrita no 'see'
     *
     * @param int $numeroAulas
     * @param \App\Entity\Principal\HorarioAula[] $horariosArray
     * @param array $diasDaSemanaDosHorarios
     * @param \DateTime $dataInicio
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param \DateTime[] $retornoDatasAulas
     *
     * @see TurmaBO::calcularDataTermino()
     *
     * @return string
     */
    private function calculaUltimoDiaAula($numeroAulas, $horariosArray, $diasDaSemanaDosHorarios, $dataInicio, $franqueadaORM, &$retornoDatasAulas=[])
    {
        $aulaCorrente = 0;
        $quantidadeHorariosPercorridos = 0;
        $dataInicio->setTime(0, 0, 1);
        $dataFinal          = $dataInicio;
        $proximoHorarioAula = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
        while ($aulaCorrente < $numeroAulas) {
            if ($this->calendarioBO->existeDiaNaoLetivoNoCalendario($franqueadaORM, $dataFinal) === true) {
                $dataFinal->modify("+1 day");
                $dataFinal->setTime(0, 0, 1);
                $proximoHorarioAula = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
                continue;
            }

            $dia = (int) $dataFinal->format("w");
            if (self::$diasDaSemana[$proximoHorarioAula->getDiaSemana()]['ordem'] === $dia) {
                $quantidadesHorariosCadastradosParaODia = count(array_keys($diasDaSemanaDosHorarios, $dia));
                if (($quantidadesHorariosCadastradosParaODia > 1) && ($quantidadeHorariosPercorridos !== $quantidadesHorariosCadastradosParaODia)) {
                    $horariosString      = $proximoHorarioAula->getHorarioInicio()->format("H:i");
                    $horariosStringArray = explode(":", $horariosString);
                    $dataFinal->setTime($horariosStringArray[0], $horariosStringArray[1]);
                    $retornoDatasAulas[] = clone $dataFinal;
                    $aulaCorrente++;
                    $quantidadesHorariosCadastradosParaODia--;
                    for ($i = 0;$i < $quantidadesHorariosCadastradosParaODia;$i++) {
                        $proximoHorarioAula  = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
                        $horariosString      = $proximoHorarioAula->getHorarioInicio()->format("H:i");
                        $horariosStringArray = explode(":", $horariosString);
                        $dataFinal->setTime($horariosStringArray[0], $horariosStringArray[1]);
                        $retornoDatasAulas[] = clone $dataFinal;
                        if ($aulaCorrente === $numeroAulas) {
                            break;
                        }

                        $aulaCorrente++;
                    }

                    if ($aulaCorrente === $numeroAulas) {
                        break;
                    }

                    $quantidadeHorariosPercorridos = 0;
                    $dataFinal->modify("+1 day");
                    $dataFinal->setTime(0, 0, 1);
                    $proximoHorarioAula = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
                } else {
                    $horariosString      = $proximoHorarioAula->getHorarioInicio()->format("H:i");
                    $horariosStringArray = explode(":", $horariosString);
                    $dataFinal->setTime($horariosStringArray[0], $horariosStringArray[1]);
                    $retornoDatasAulas[] = clone $dataFinal;
                    $aulaCorrente++;
                    $dataFinal->modify("+1 day");
                    $dataFinal->setTime(0, 0, 1);
                    $proximoHorarioAula = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
                }//end if
            } else {
                $quantidadeHorariosPercorridos = 0;
                $dataFinal->modify("+1 day");
                $dataFinal->setTime(0, 0, 1);
                $proximoHorarioAula = self::buscarProximoHorarioAula($horariosArray, $diasDaSemanaDosHorarios, $dataFinal);
            }//end if
        }//end while

        return $dataFinal->format('c');
    }

    /**
     * Realiza ordenacao das datas
     *
     * @param \App\Entity\Principal\HorarioAula[] $horariosArray
     * @param array $diasDaSemanaDosHorarios
     */
    private function realizaOrdenacaoDatas(&$horariosArray, &$diasDaSemanaDosHorarios)
    {
        $diasDaSemanaDosHorarios = array_map(
            function ($horario) {
                return self::$diasDaSemana[$horario->getDiaSemana()]['ordem'];
            },
            $horariosArray
        );
    }

    /**
     * Calcula a data de término da turma conforme o livro, horário, calendário e data de início da turma
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return string|null
     */
    public function calcularDataTermino (&$mensagemErro, $parametros, &$retornaDiasAulaArrayObjeto=[])
    {
        $franqueadaORM = null;
        $livroORM      = null;
        $horarioORM    = null;
        $dataObjeto    = null;
        $diasDaSemanaDosHorarios = null;
        if ($this->converteParametrosParaCalculo($parametros, $mensagemErro, $franqueadaORM, $livroORM, $horarioORM, $dataObjeto) === true) {
            $numeroAulas   = $livroORM->getPlanejamentoLicao()->getLicaos()->count();
            $horariosArray = $horarioORM->getHorarioAulas()->toArray();
            if (empty($horariosArray) === true) {
                $mensagemErro = 'O horário está incompleto';
                return null;
            }

            $this->realizaOrdenacaoDatas($horariosArray, $diasDaSemanaDosHorarios);
            return $this->calculaUltimoDiaAula($numeroAulas, $horariosArray, $diasDaSemanaDosHorarios, $dataObjeto, $franqueadaORM, $retornaDiasAulaArrayObjeto);
        }

        return null;
    }

    /**
     * Busca o Proximo horario de aula baseado nos dias da semana do HorarioAula
     *
     * @param \App\Entity\Principal\HorarioAula[] $horarios
     * @param array $diasDaSemanaDosHorarios
     * @param \DateTime $data
     *
     * @return \App\Entity\Principal\HorarioAula
     */
    private static function buscarProximoHorarioAula ($horarios, $diasDaSemanaDosHorarios, $data)
    {
        $dia = (int) $data->format('w');
        $totalHorariosCadastrados = count($horarios);
        $horarioRetorno           = $horarios[0];
        for ($i = 0;$i < $totalHorariosCadastrados; $i++) {
            $horarioORM         = $horarios[$i];
            $horarioString      = $horarioORM->getHorarioInicio()->format("H:i");
            $horarioStringArray = explode(":", $horarioString);
            $dataComHorario     = clone $data;
            $dataComHorario->setTime($horarioStringArray[0], $horarioStringArray[1]);
            $intervalo      = $data->diff($dataComHorario);
            $diaHorarioAula = self::$diasDaSemana[$horarioORM->getDiaSemana()]['ordem'];
            if ($diaHorarioAula === $dia) {
                // Quando o horario é no mesmo dia com horas diferentes
                if (((int) $intervalo->format("%r%h")) > 0) {
                    $horarioRetorno = $horarioORM;
                    break;
                }
            } else if ($diaHorarioAula > $dia) {
                // Verifica se o proximo horarioAula possui o dia que estamos procurando
                if (($i + 1) === ($totalHorariosCadastrados - 1)) {
                    $proximoHorarioAulaLoop = $horarios[$i + 1];
                    $proximoDiaLoop         = self::$diasDaSemana[$proximoHorarioAulaLoop->getDiaSemana()]['ordem'];
                    if ($proximoDiaLoop === $dia) {
                        continue;
                    }
                } else {
                    $horarioRetorno = $horarioORM;
                    break;
                }
            } else {
                continue;
            }//end if
        }//end for

        return $horarioRetorno;
    }

    /**
     * Verifica se a data informada é no mesmo dia da semana informado no horário
     *
     * @param \App\Entity\Principal\Horario $horario
     * @param \DateTime $data
     *
     * @return boolean
     */
    private static function primeiraAula ($horario, $data)
    {
        $dia = (int) $data->format('w');
        return self::$diasDaSemana[$horario->getDiaSemana()]['ordem'] === $dia;
    }

    /**
     * Realiza a busca da turma e coloca o dado buscado no $resultadoORM
     *
     * @param \App\Repository\Principal\TurmaRepository $turmaRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Turma $resultadoORM
     *
     * @return boolean
     */
    public static function verificaTurmaExiste(\App\Repository\Principal\TurmaRepository $turmaRepository, $id, &$mensagemErro, &$resultadoORM)
    {
        $resultadoORM = $turmaRepository->find($id);
        if (is_null($resultadoORM) === true) {
            $mensagemErro .= "Turma não foi encontrada na base de dados.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se ao menos existe 1 vaga para um aluno ser adicionado
     *
     * @param \App\Entity\Principal\Turma $turmaORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public static function verificaSePodeAdicionarAluno(\App\Entity\Principal\Turma $turmaORM, &$mensagemErro)
    {
        $contratosTurma   = $turmaORM->getContratos();
        $quantidadeAlunos = 0;
        foreach ($contratosTurma as $contratoORM) {
            if ($contratoORM->getSituacao() === SituacoesSistema::SITUACAO_CONTRATO_VIGENTE) {
                $quantidadeAlunos++;
            }
        }

        $quantidadeMaximaAlunos = $turmaORM->getMaximoAlunos();
        $resultado = $quantidadeMaximaAlunos - $quantidadeAlunos;
        if ($resultado >= 1) {
            return true;
        }

        $mensagemErro = "A turma não aceita novos alunos, pois a turma atual possui: " . $quantidadeAlunos . " e o maximo de alunos permitido para ela é de: " . $quantidadeMaximaAlunos;
        return false;
    }


}
