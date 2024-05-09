<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\FunctionHelper;
use App\BO\Principal\FuncionarioBO;
use App\BO\Principal\TurmaBO;
use LDAP\Result;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

/**
 *
 * @author Luiz A Costa
 */
class FuncionarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioValorHoraRepository
     */
    private $funcionarioValorHoraRepository;

    /**
     *
     * @var \App\Repository\Principal\HorarioRepository
     */
    private $horarioRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Principal\ValorHoraRepository
     */
    private $valorHoraRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioDisponibilidadeRepository
     */
    private $funcionarioDisponibilidadeRepository;

    /**
     *
     * @var \App\BO\Principal\FuncionarioBO
     */
    private $funcionarioBO;

    /**
     *
     * @var \App\BO\Principal\TurmaBO
     */
    private $turmaBO;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaAulaRepository
     */
    private $turmaAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioPersonalRepository
     */
    private $alunoDiarioPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\FormaPagamentoRepository
     */
    private $formaPagamentoRepository;

    /**
     *
     * @var \App\Repository\Principal\PlanoContaRepository
     */
    private $planoContaRepository;

    /**
     *
     * @var \App\Repository\Principal\AtividadeExtraRepository
     */
    private $atividadeExtraRepository;

    /**
     *
     * @var \App\Repository\Principal\ReposicaoAulaRepository
     */
    private $reposicaoAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\BonusClassRepository
     */
    private $bonusClassRepository;

    /**
     *
     * @var \App\Repository\Principal\NivelInstrutorRepository
     */
    private $nivelInstrutorRepository;

    /**
     *
     * @var \App\Facade\Principal\ContaPagarFacade
     */
    private $contaPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\TituloPagarFacade
     */
    private $tituloPagarFacade;

    /**
     *
     * @var \App\Facade\Principal\PlanoContasContaPagarFacade
     */
    private $planoContasContaPagarFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->franqueadaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->turmaAulaRepository           = self::getEntityManager()->getRepository(\App\Entity\Principal\TurmaAula::class);
        $this->alunoDiarioPersonalRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiarioPersonal::class);
        $this->atividadeExtraRepository      = self::getEntityManager()->getRepository(\App\Entity\Principal\AtividadeExtra::class);
        $this->reposicaoAulaRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\ReposicaoAula::class);
        $this->bonusClassRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\BonusClass::class);

        $this->funcionarioRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->funcionarioValorHoraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FuncionarioValorHora::class);
        $this->funcionarioDisponibilidadeRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FuncionarioDisponibilidade::class);
        $this->horarioRepository        = self::getEntityManager()->getRepository(\App\Entity\Principal\Horario::class);
        $this->turmaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Turma::class);
        $this->valorHoraRepository      = self::getEntityManager()->getRepository(\App\Entity\Principal\ValorHora::class);
        $this->formaPagamentoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FormaPagamento::class);
        $this->planoContaRepository     = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanoConta::class);
        $this->nivelInstrutorRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\NivelInstrutor::class);

        $this->funcionarioBO     = new FuncionarioBO(self::getEntityManager());
        $this->turmaBO           = new TurmaBO(self::getEntityManager());
        $this->contaPagarFacade  = new ContaPagarFacade($managerRegistry);
        $this->tituloPagarFacade = new TituloPagarFacade($managerRegistry);
        $this->planoContasContaPagarFacade = new PlanoContasContaPagarFacade($managerRegistry);
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
        $retornoRepositorio = $this->funcionarioRepository->filtrarFuncionarioPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Filtra funcionario por flag e parametros extras(como nome por exemplo)
     *
     * @param array $parametros
     * @param array $extraParams Verificar como realizar a implementação do extraParam na sessão 'see'
     *
     * @see \App\Repository\Principal\FuncionarioRepository::filtrarFlagsFuncionario()
     *
     * @return array|NULL
     */
    public function buscaDeFuncionarioPorFlag($parametros, $extraParams=[])
    {
        return $this->funcionarioRepository->buscaFuncionarioAtivosPorFlags($parametros, $extraParams);
    }

    /**
     * Verifica se o Funcionario está ocupado na hora
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function verificarDisponibilidade($parametros, &$mensagemErro)
    {
        $funcionario     = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $horario         = $this->horarioRepository->find($parametros[ConstanteParametros::CHAVE_HORARIO]);
        $disponibilidade = $funcionario->getDisponibilidades();
        $horarioAulas    = $horario->getHorarioAulas();

        $funcionarioDisponivel = false;

        foreach ($disponibilidade as $dia) {
            foreach ($horarioAulas as $horario) {
                if ($horario->getDiaSemana() === $dia->getDiaSemana() && $funcionarioDisponivel === false) {
                    $horarioInicio  = $horario->getHorarioInicio();
                    $horarioTermino = \Carbon\Carbon::parse($horarioInicio)->add(1, 'hour');
                    if ($dia->getHoraInicial() <= $horarioInicio && $dia->getHoraFinal() >= $horarioTermino) {
                        $funcionarioDisponivel = true;
                    } else {
                        $funcionarioDisponivel = false;
                    }
                }
            }
        }

        return $funcionarioDisponivel;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Funcionario
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->funcionarioRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Funcionario não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Funcionario
     */
    public function buscarId(&$mensagemErro, $id)
    {
        $objetoORM = $this->funcionarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Funcionario
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Funcionario::class);
        if ($this->funcionarioBO->podeSalvar($parametros, $mensagemErro, $objetoORM) === true) {
            $this->funcionarioBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
            if (empty($mensagemErro) === false) {
                $objetoORM = null;
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
                // $this->verificarNivelInstrutorPorDataAdmissao($parametros, $objetoORM);
                $this->configuraValorHora($parametros[ConstanteParametros::CHAVE_FUNCIONARIO_VALOR_HORA], $objetoORM, $mensagemErro);
                $this->configuraDisponibilidades($parametros[ConstanteParametros::CHAVE_DISPONIBILIDADES], $objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
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
        $objetoORM = $this->funcionarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario não encontrado na base de dados.";
        } else {
            if ($this->funcionarioBO->podeAlterar($parametros, $mensagemErro, $objetoORM) === true) {
                $this->funcionarioBO->configuraParametros($parametros, $objetoORM, $mensagemErro);
                // $this->verificarNivelInstrutorPorDataAdmissao($parametros, $objetoORM);
                $this->configuraValorHora($parametros[ConstanteParametros::CHAVE_FUNCIONARIO_VALOR_HORA], $objetoORM, $mensagemErro);
                $this->configuraDisponibilidades($parametros[ConstanteParametros::CHAVE_DISPONIBILIDADES], $objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = $this->funcionarioRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Funcionario não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Cria e edita valores hora do funcionário
     *
     * @param array $valoresHora
     * @param \App\Entity\Principal\Funcionario $funcionarioORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraValorHora ($valoresHora, $funcionarioORM, &$mensagemErro)
    {
        $valoresHoraExistentes = $funcionarioORM->getFuncionarioValorHoras();
        $idsExistentes         = [];

        if (is_null($valoresHora) === false) {
            foreach ($valoresHora as $valorHora) {
                $valorHoraORM = null;
                $valorHora[ConstanteParametros::CHAVE_VALOR_HORA]  = $this->valorHoraRepository->find($valorHora[ConstanteParametros::CHAVE_VALOR_HORA]);
                $valorHora[ConstanteParametros::CHAVE_FUNCIONARIO] = $funcionarioORM;

                if ((isset($valorHora[ConstanteParametros::CHAVE_ID]) === true) && (empty($valorHora[ConstanteParametros::CHAVE_ID]) === false)) {
                    $valorHoraORM = $this->funcionarioValorHoraRepository->find($valorHora[ConstanteParametros::CHAVE_ID]);
                    $idsExistentes[$valorHoraORM->getId()] = true;
                    $valorHoraORM->setValor($valorHora[ConstanteParametros::CHAVE_VALOR]);
                    $valorHoraORM->setValorExtra($valorHora[ConstanteParametros::CHAVE_VALOR_EXTRA]);
                    $valorHoraORM->setValorBonus($valorHora[ConstanteParametros::CHAVE_VALOR_BONUS]);
                } else {
                    $valorHoraORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FuncionarioValorHora::class, true, $valorHora);
                    self::persistSeguro($valorHoraORM, $mensagemErro);
                    $funcionarioORM->addFuncionarioValorHora($valorHoraORM);
                    $idsExistentes[$valorHoraORM->getId()] = true;
                }
            }
        }

        foreach ($valoresHoraExistentes as $valorHoraExistente) {
            if ($funcionarioORM->getInstrutor() === false || isset($idsExistentes[$valorHoraExistente->getId()]) === false) {
                self::removerSeguro($valorHoraExistente, $mensagemErro);
            }
        }

        return empty($mensagemErro) === true;
    }


    /**
     * Calcular o nivel de instrutor baseado na data de admissão
     *
     * @param array $parametros array de parametros do front end
     * @param \App\Entity\Principal\Funcionario $objetoORM objeto
     *
     * @return void
     */
    private function verificarNivelInstrutorPorDataAdmissao(&$parametros, &$objetoORM)
    {
        $dataAdmissao = null;
        $dataAtual    = \Carbon\Carbon::now();

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]) === false)) {
            $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO] = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]);
            $dataAdmissao = $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO];
        } else {
            $dataAdmissao = \Carbon\Carbon::now();
        }

        $interval = $dataAdmissao->diff($dataAtual);
        $ano      = $interval->y;
        if ($ano > 2) {
            $ano = 2;
        }

        $objetoNivelInstrutorORM = $this->nivelInstrutorRepository->findOneBy(['ano' => $ano]);
        $objetoORM->setNivelInstrutor($objetoNivelInstrutorORM);
        // dd($objetoORM->getNivelInstrutor()->getId());
    }


    /**
     * Busca registros de funcionários por nome
     *
     * @param string $nome Nome da pessoa a ser buscado
     *
     * @return \App\Entity\Principal\Pessoa[]
     */
    public function buscarPorNome($nome)
    {
        return $this->funcionarioRepository->buscarPorNome($nome);
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->funcionarioRepository->prepararDadosRelatorio($parametros);
    }


    /**
     * Cria e edita disponibilidades do funcionário
     *
     * @param array $disponibilidades
     * @param \App\Entity\Principal\Funcionario $funcionarioORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraDisponibilidades ($disponibilidades, $funcionarioORM, &$mensagemErro)
    {
        $disponibilidadesExistentes = $funcionarioORM->getDisponibilidades();
        $idsExistentes = [];

        if (is_null($disponibilidades) === false) {
            foreach ($disponibilidades as $disponibilidade) {
                $disponibilidadeORM = null;
                if (isset($disponibilidade[ConstanteParametros::CHAVE_ID]) === false) {
                    $disponibilidade[ConstanteParametros::CHAVE_ID] = '';
                }

                $disponibilidade[ConstanteParametros::CHAVE_FUNCIONARIO]  = $funcionarioORM;
                $disponibilidade[ConstanteParametros::CHAVE_HORA_INICIAL] = FunctionHelper::formataCampoDateTimeJS($disponibilidade[ConstanteParametros::CHAVE_HORA_INICIAL]);
                $disponibilidade[ConstanteParametros::CHAVE_HORA_FINAL]   = FunctionHelper::formataCampoDateTimeJS($disponibilidade[ConstanteParametros::CHAVE_HORA_FINAL]);

                if ($disponibilidade[ConstanteParametros::CHAVE_HORA_INICIAL] === false || $disponibilidade[ConstanteParametros::CHAVE_HORA_FINAL] === false || empty($disponibilidade[ConstanteParametros::CHAVE_DIA_SEMANA]) === true) {
                    continue;
                }

                $disponibilidadeORM = $this->funcionarioDisponibilidadeRepository->find($disponibilidade[ConstanteParametros::CHAVE_ID]);
                if (is_null($disponibilidadeORM) === false) {
                    $disponibilidadeORM->setDiaSemana($disponibilidade[ConstanteParametros::CHAVE_DIA_SEMANA]);
                    $disponibilidadeORM->setHoraInicial($disponibilidade[ConstanteParametros::CHAVE_HORA_INICIAL]);
                    $disponibilidadeORM->setHoraFinal($disponibilidade[ConstanteParametros::CHAVE_HORA_FINAL]);
                } else {
                    $disponibilidadeORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\FuncionarioDisponibilidade::class, true, $disponibilidade);
                    self::persistSeguro($disponibilidadeORM, $mensagemErro);
                }

                $idsExistentes[$disponibilidadeORM->getId()] = true;
            }//end foreach
        }//end if

        foreach ($disponibilidadesExistentes as $disponibilidade) {
            if (isset($idsExistentes[$disponibilidade->getId()]) === false) {
                self::removerSeguro($disponibilidade, $mensagemErro);
            }
        }

        self::flushSeguro($mensagemErro);

        return empty($mensagemErro) === true;
    }


     /**
     * Cria e edita disponibilidades do funcionário
     *
     * @param array $disponibilidades
     * @param \App\Entity\Principal\Funcionario $funcionarioORM
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function buscaDisponibilidadeInstrutores ($filtros, &$mensagemErro)
    {
        date_default_timezone_set('America/Sao_Paulo');

        if (empty($filtros['data_inicial']) == false && empty($filtros['data_final']) == false) {
            $dataIni = explode("/", $filtros['data_inicial']);
            $data_inicial   = $dataIni[2].'-'.$dataIni[1].'-'.$dataIni[0];
            $data_inicial   = new \DateTime($data_inicial);

            $dataFim = explode("/", $filtros['data_final']);
            $data_final   = $dataFim[2].'-'.$dataFim[1].'-'.$dataFim[0];
            $data_final   = new \DateTime($data_final);
        } else {
            $data_inicial   = new \DateTime();
            $data_final   = date('y-m-d', strtotime('1 month', strtotime($data_inicial->format("m/d/Y"))));
            $data_final   = new \DateTime( $data_final);
        }

        if (empty($filtros['instrutor']) == true) {
            $intrutoresORM = $this->funcionarioRepository->buscaInstrutoresAtivos($filtros[ConstanteParametros::CHAVE_FRANQUEADA]);
        } else {
            $intrutorORM = $this->funcionarioRepository->buscarPorId($filtros['instrutor']);
            $intrutoresORM = [];
            $intrutoresORM[] = $intrutorORM;
        }
        
        $retorno = [];
        foreach ($intrutoresORM as $instrutor) {
            $horariosDisponivelInstrutorORM = $this->funcionarioRepository->buscarHorariosDisponiveisInstrutor($instrutor['id']);
            $agendaInstrutorDisponivelVazio = [];
            $agendaInstrutorDisponivelRetorno = [];
            $agendaInstrutorDisponivel = [];
           if (empty($horariosDisponivelInstrutorORM) == false) { 
                //criar array dos horarios              
                foreach ($horariosDisponivelInstrutorORM as $datasHorariosInst) {
                    $dtINIDisp = date('y-m-d', strtotime('-1 day', strtotime($data_inicial->format("m/d/Y"))));
                    $dtINIDisp = new \DateTime( $dtINIDisp); 
                    while  (($dtINIDisp <= $data_final ) !== false) {
                        switch ($datasHorariosInst['dia_semana']) {
                            case 'SEG':
                                $dtINIDisp = $dtINIDisp->modify( 'next monday' );
                                break;
                            case 'TER':
                                $dtINIDisp = $dtINIDisp->modify( 'next Tuesday' );
                                break;
                            case 'QUA':
                                $dtINIDisp = $dtINIDisp->modify( 'next Wednesday' );
                                break;
                            case 'QUI':
                                $dtINIDisp = $dtINIDisp->modify( 'next Thursday' );
                            break;
                            case 'SEX':
                                $dtINIDisp = $dtINIDisp->modify( 'next Friday' );
                                break;
                            case 'SAB':
                                $dtINIDisp = $dtINIDisp->modify( 'next Saturday' );
                                break;
                            case 'DOM':
                                $dtINIDisp = $dtINIDisp->modify( 'next Sunday' );                                                                                                                   
                                break;
                            default:
                            $mensagemErro = " não existe dias da semana.";
                        break;
                        }//switch 

                        if ($dtINIDisp <= $data_final ) {
                            $valor = [];
                            $valor['instrutor_id'] = $datasHorariosInst['id'];
                            $valor['nome'] = $datasHorariosInst['nome_contato'];
                            $valor['dia_semana'] = $datasHorariosInst['dia_semana'];
                            $valor['data'] = $dtINIDisp->format("m/d/Y");
                            $valor['hora_inicial'] = $datasHorariosInst['hora_inicial'];
                            $valor['hora_final'] = $datasHorariosInst['hora_final'];
                            
                            $agendaInstrutorDisponivel[] =$valor;
                        }
                    }//while
                }//foreach
               
                foreach ($agendaInstrutorDisponivel as $horarioInstrutor) {
                    $horariosIntrutorTurmaORM = $this->funcionarioRepository->buscarHorariosTurmaInstrutor($horarioInstrutor);
                    $horariosInstrutorPersonalORM = $this->funcionarioRepository->consultaPersonalInstrutorDiaSemana($horarioInstrutor);
                    $horariosInstrutorAtivExtraORM = $this->funcionarioRepository->consultaAtivExtraInstrutorDiaSemana($horarioInstrutor);
 
                    $hora_inicio_instrutor = $horarioInstrutor['data'].' '.$horarioInstrutor['hora_inicial'];
                    $hora_inicio_instrutor = new \DateTime($hora_inicio_instrutor);
                    $hora_final_instrutor = $horarioInstrutor['data'].' '.$horarioInstrutor['hora_final'];
                    $hora_final_instrutor = new \DateTime($hora_final_instrutor);
                
                    $agendaInstrutorDisponivelTurma = [];
                    $agendaInstrutorDisponivelPersonal = [];
                    $agendaInstrutorDisponivelExtra = [];
                    
                    if ((empty($horariosIntrutorTurmaORM) == true) && 
                    (empty($horariosInstrutorPersonalORM) == true) &&
                    (empty($horariosInstrutorAtivExtraORM) == true)) { 
                        $agendaInstrutorDisponivelTempTVazio = [];
                        $valor = [];
                        $valor['instrutor_id'] = $instrutor['id'];
                        $valor['nome'] = $instrutor['apelido'];
                        $valor['dia_semana'] = $horarioInstrutor['dia_semana'];
                        $valor['data'] = $hora_inicio_instrutor->format("m/d/Y");
                        $valor['hora_inicial'] = $horarioInstrutor['hora_inicial'];
                        $valor['hora_final'] = $horarioInstrutor['hora_final'];                                      
                        $agendaInstrutorDisponivelTempTVazio[] =$valor;                                
                        
                        if (empty($agendaInstrutorDisponivelTempTVazio) == false) { 
                            $agendaInstrutorDisponivelVazio[] = $agendaInstrutorDisponivelTempTVazio;
                        }
                    }
                        
            
                    if (empty($horariosIntrutorTurmaORM) == false) {    
                        // $agendaInstrutorDisponivelTempTurma = $agendaInstrutorDisponivelTemp;
                        foreach ($horariosIntrutorTurmaORM as $horarioTurma) {                                
                            $agendaInstrutorDisponivelTempTurma = [];                                
                            $hora_inicial_turma = new \DateTime($horarioInstrutor['data']);
                            $hora_inicial_turma = new \DateTime($hora_inicial_turma->format("y-m-d").' '.$horarioTurma['horario_inicio']);
                            $hora_final_turma = new \DateTime($hora_inicial_turma->format("y-m-d H:i:s"));
                            $hora_final_turma = $hora_final_turma->modify( '1 hours' );
                            
                            if (empty($agendaInstrutorDisponivelTurma) == true) {
                                $agendaInstrutorDisponivelTurma[] = $horarioInstrutor;
                            }
                            
                            foreach ($agendaInstrutorDisponivelTurma as $horarioTur) {
                                $hora_inicio_instrutor_tur = $horarioTur['data'].' '.$horarioTur['hora_inicial'];
                                $hora_inicio_instrutor_tur = new \DateTime($hora_inicio_instrutor_tur);
                                $hora_final_instrutor_tur = $horarioTur['data'].' '.$horarioTur['hora_final'];
                                $hora_final_instrutor_tur = new \DateTime($hora_final_instrutor_tur);
                                
                                $hora = new \DateTime($hora_inicio_instrutor_tur->format("y-m-d H:i:s"));
                                $hora = $hora->modify( '-15 minutes' );
                                
                                $hr_ini = new \DateTime($hora_inicio_instrutor_tur->format("y-m-d H:i:s"));
                                $status = '';
                                if ($hora_inicio_instrutor_tur <= $hora_inicial_turma && $hora_final_instrutor_tur >= $hora_final_turma) {
                                    while  (($hr_ini <= $hora_final_instrutor_tur) !== false) {
                                        if ($hora->format("H:i") >= $hora_inicial_turma->format("H:i") && $hora->format("H:i") <= $hora_final_turma->format("H:i")) {
                                            $hr_ini = $hr_ini->modify( '+15 minutes' );
                                            if ($status == 'Disponivel') {
                                                if ($hr_ini >= $hora_final_instrutor_tur) {
                                                    $hr_ini = $hora_final_instrutor_tur;
                                                };
                                                $valor['hora_final'] = $hr_ini->format("H:i:s");                                       
                                                $agendaInstrutorDisponivelTempTurma[] =$valor;                
                                            } 
                                            $status = 'Indisponivel';
                                            
                                        } else {
                                            if ($status == '' || $status == 'Indisponivel') {
                                                // //fazer registro novo                                         
                                                $valor = [];
                                                $valor['instrutor_id'] = $instrutor['id'];
                                                $valor['nome'] = $instrutor['apelido'];
                                                $valor['dia_semana'] = $horarioTur['dia_semana'];
                                                $valor['data'] = $hr_ini->format("m/d/Y");
                                                $valor['hora_inicial'] = $hr_ini->format("H:i:s");
                                                
                                                $hr_ini = $hr_ini->modify( '-15 minutes' );
                                                $status = 'Disponivel';
                                            } else {
                                                //adicionar registro existente
                                                if ($hr_ini->format("H:i") >= $hora_final_instrutor->format("H:i:s")) {
                                                    break;  
                                                } else {
                                                    $hr_ini = $hr_ini->modify( '+15 minutes' );
                                                }
                                            }                                 
                                        }        
                                        $hora = $hora->modify( '+15 minutes' );                                 
                                    } 
                                    if ($hr_ini >= $hora_final_instrutor_tur) {
                                        $hr_ini = $hora_final_instrutor_tur;
                                    };
                                    $valor['hora_final'] = $hr_ini->format("H:i:s");
                                    $agendaInstrutorDisponivelTempTurma[] =$valor;
                                } else {
                                    $valor = [];
                                    $valor['instrutor_id'] = $instrutor['id'];
                                    $valor['nome'] = $instrutor['apelido'];
                                    $valor['dia_semana'] = $horarioTur['dia_semana'];
                                    $valor['data'] = $hr_ini->format("m/d/Y");
                                    $valor['hora_inicial'] = $hora_inicio_instrutor_tur->format("H:i:s"); 
                                    $valor['hora_final'] = $hora_final_instrutor_tur->format("H:i:s");                                       
                                    $agendaInstrutorDisponivelTempTurma[] =$valor;  
                                }
                            }
                            $agendaInstrutorDisponivelTurma = $agendaInstrutorDisponivelTempTurma;
                        }; 
                        
                    };//if horariosIntrutorTurmaORM empty 
                    
                    if (empty($horariosInstrutorPersonalORM) == false) {
                        $agendaInstrutorDisponivelTempPersonal = [];
                        foreach ($horariosInstrutorPersonalORM as $horarioPersonal) {
                            //   $hora_ini = new \DateTime()
                            $hora_inicial_turma_pesonal = new \DateTime($horarioPersonal['inicio']);
                            $hora_final_turma_personal = new \DateTime($hora_inicial_turma_pesonal->format("y-m-d H:i:s"));
                            $hora_final_turma_personal = $hora_final_turma_personal->modify( '1 hours' );
                            
                            $status = '';
                            
                            
                            if (empty($agendaInstrutorDisponivelTurma) == false) {
                                $agendaInstrutorDisponivelPersonal = $agendaInstrutorDisponivelTurma;
                                $agendaInstrutorDisponivelTurma = [];
                            }
                            if (empty($agendaInstrutorDisponivelPersonal) == true) {
                                $agendaInstrutorDisponivelPersonal[] = $horarioInstrutor;
                            }
                            
                            foreach ($agendaInstrutorDisponivelPersonal as $horarioPer) {
                                
                                $hora_inicio_instrutor_per = $horarioPer['data'].' '.$horarioPer['hora_inicial'];
                                $hora_inicio_instrutor_per = new \DateTime($hora_inicio_instrutor_per);
                                $hora_final_instrutor_per = $horarioPer['data'].' '.$horarioPer['hora_final'];
                                $hora_final_instrutor_per = new \DateTime($hora_final_instrutor_per);
                                
                                $hora = new \DateTime($horarioPer['data']. ' ' . $horarioPer['hora_inicial']);
                                $hora = $hora->modify( '-15 minutes' );
                                $hr_ini = new \DateTime($horarioPer['data']. ' '. $horarioPer['hora_inicial']);
                                $hora_final_instrutor_temp = new \DateTime($horarioPer['data']. ' '. $horarioPer['hora_final']);
                                if ($hora_inicio_instrutor_per <= $hora_inicial_turma_pesonal && $hora_final_instrutor_per >= $hora_final_turma_personal) {
                                    while  (($hr_ini <= $hora_final_instrutor_temp) !== false) {
                                        if ($hora->format("H:i") >= $hora_inicial_turma_pesonal->format("H:i") && $hora->format("H:i") <= $hora_final_turma_personal->format("H:i")) {
                                            
                                            $hr_ini = $hr_ini->modify( '+15 minutes' );
                                            if ($status == 'Disponivel') {
                                                if ($hr_ini >= $hora_final_instrutor_per) {
                                                    $hr_ini = $hora_final_instrutor_per;
                                                };
                                                $valor['hora_final'] = $hr_ini->format("H:i:s");                                       
                                                $agendaInstrutorDisponivelTempPersonal[] =$valor;                
                                            } 
                                            $status = 'Indisponivel';
                                            
                                        } else {
                                            if ($status == '' || $status == 'Indisponivel') {
                                                // //fazer registro novo                                         
                                                $valor = [];
                                                $valor['instrutor_id'] = $horarioPer['instrutor_id'];
                                                $valor['nome'] = $horarioPer['nome'];
                                                $valor['dia_semana'] = $horarioPer['dia_semana'];
                                                $valor['data'] = $hr_ini->format("m/d/Y");
                                                $valor['hora_inicial'] = $hr_ini->format("H:i:s");
                                                
                                                $hr_ini = $hr_ini->modify( '-15 minutes' );
                                                $status = 'Disponivel';
                                            } else {
                                                //adicionar registro existente
                                                if ($hr_ini->format("H:i") >= $hora_final_instrutor_temp->format("H:i:s")) {
                                                    break;  
                                                } else {
                                                    $hr_ini = $hr_ini->modify( '+15 minutes' );
                                                }
                                            }                                 
                                        }        
                                        $hora = $hora->modify( '+15 minutes' );                                 
                                    } 
                                    if ($hr_ini >= $hora_final_instrutor_per) {
                                        $hr_ini = $hora_final_instrutor_per;
                                    };
                                    $valor['hora_final'] = $hr_ini->format("H:i:s");
                                    $agendaInstrutorDisponivelTempPersonal[] =$valor;
                                } else {
                                    $valor = [];
                                    $valor['instrutor_id'] = $horarioPer['instrutor_id'];
                                    $valor['nome'] = $horarioPer['nome'];
                                    $valor['dia_semana'] = $horarioPer['dia_semana'];
                                    $valor['data'] = $hr_ini->format("m/d/Y");
                                    $valor['hora_inicial'] = $hora_inicio_instrutor_per->format("H:i:s");
                                    $valor['hora_final'] = $hora_final_instrutor_per->format("H:i:s");
                                    $agendaInstrutorDisponivelTempPersonal[] =$valor;
                                }
                            } 
                            $agendaInstrutorDisponivelPersonal = $agendaInstrutorDisponivelTempPersonal;
                        }
                        
                    }//if horariosInstrutorPersonalORM empty                     
                    
                    //ativiade extra
                    if (empty($horariosInstrutorAtivExtraORM) == false) {
                        $agendaInstrutorDisponivelTempExtra = [];
                        foreach ($horariosInstrutorAtivExtraORM as $horarioExtra) {
                            $hora_inicial_extra = new \DateTime($horarioExtra['data_hora_inicio']);
                            $hora_final_extra = new \DateTime($horarioExtra['data_hora_fim']);
                            
                            $status = '';
                            
                            if (empty($agendaInstrutorDisponivelPersonal) == false) {
                                $agendaInstrutorDisponivelExtra = $agendaInstrutorDisponivelPersonal;
                                $agendaInstrutorDisponivelPersonal = [];
                            }
                            if (empty($agendaInstrutorDisponivelTurma) == false) {
                                $agendaInstrutorDisponivelExtra = $agendaInstrutorDisponivelTurma;
                                $agendaInstrutorDisponivelTurma = [];
                            }
                            if (empty($agendaInstrutorDisponivelExtra) == true) {
                                $agendaInstrutorDisponivelExtra[] = $horarioInstrutor;
                            }
                            
                            
                            foreach ($agendaInstrutorDisponivelExtra as $horarioEx) {
                                $hora_inicio_instrutor_ext = $horarioEx['data'].' '.$horarioEx['hora_inicial'];
                                $hora_inicio_instrutor_ext = new \DateTime($hora_inicio_instrutor_ext);
                                $hora_final_instrutor_ext = $horarioEx['data'].' '.$horarioEx['hora_final'];
                                $hora_final_instrutor_ext = new \DateTime($hora_final_instrutor_ext);
                                
                                $hora = new \DateTime($horarioEx['data']. ' ' . $horarioEx['hora_inicial']);
                                $hora = $hora->modify( '-15 minutes' );
                                $hr_ini = new \DateTime($horarioEx['data']. ' '. $horarioEx['hora_inicial']);
                                $hora_final_instrutor_temp = new \DateTime($horarioEx['data']. ' '. $horarioEx['hora_final']);
                                if ($hora_inicio_instrutor_ext <= $hora_inicial_extra && $hora_final_instrutor_ext >= $hora_final_extra) {
                                    while  (($hr_ini <= $hora_final_instrutor_temp) !== false) {
                                        if ($hora->format("H:i") >= $hora_inicial_extra->format("H:i") && $hora->format("H:i") <= $hora_final_extra->format("H:i")) {
                                            
                                            $hr_ini = $hr_ini->modify( '+15 minutes' );
                                            if ($status == 'Disponivel') {
                                                if ($hr_ini >= $hora_final_instrutor_ext) {
                                                    $hr_ini = $hora_final_instrutor_ext;
                                                };
                                                $valor['hora_final'] = $hr_ini->format("H:i:s");                                       
                                                $agendaInstrutorDisponivelTempExtra[] =$valor;                
                                            } 
                                            $status = 'Indisponivel';
                                            
                                        } else {
                                            if ($status == '' || $status == 'Indisponivel') {
                                                // //fazer registro novo                                         
                                                $valor = [];
                                                $valor['instrutor_id'] = $horarioEx['instrutor_id'];
                                                $valor['nome'] = $horarioEx['nome'];
                                                $valor['dia_semana'] = $horarioEx['dia_semana'];
                                                $valor['data'] = $hr_ini->format("m/d/Y");
                                                $valor['hora_inicial'] = $hr_ini->format("H:i:s");
                                                
                                                $hr_ini = $hr_ini->modify( '-15 minutes' );
                                                $status = 'Disponivel';
                                            } else {
                                                //adicionar registro existente
                                                if ($hr_ini->format("H:i") >= $hora_final_instrutor_temp->format("H:i:s")) {
                                                    break;  
                                                } else {
                                                    $hr_ini = $hr_ini->modify( '+15 minutes' );
                                                }
                                            }                                 
                                        }        
                                        $hora = $hora->modify( '+15 minutes' );                                 
                                    } 
                                    if ($hr_ini >= $hora_final_instrutor_ext) {
                                        $hr_ini = $hora_final_instrutor_ext;
                                    };
                                    $valor['hora_final'] = $hr_ini->format("H:i:s");
                                    $agendaInstrutorDisponivelTempExtra[] =$valor; 
                                } else {
                                    $valor = [];
                                    $valor['instrutor_id'] = $horarioEx['instrutor_id'];
                                    $valor['nome'] = $horarioEx['apelido'];
                                    $valor['dia_semana'] = $horarioEx['dia_semana'];
                                    $valor['data'] = $hr_ini->format("m/d/Y");
                                    $valor['hora_inicial'] = $hora_inicio_instrutor_ext->format("H:i:s"); 
                                    $valor['hora_final'] = $hora_final_instrutor_ext->format("H:i:s");
                                    $agendaInstrutorDisponivelTempExtra[] =$valor;       
                                }
                            } 
                            $agendaInstrutorDisponivelExtra = $agendaInstrutorDisponivelTempExtra;    
                        }
                        
                    }//if horariosInstrutorAtivExtraORM empty 

                    if (empty($agendaInstrutorDisponivelPersonal) == false) {
                        $agendaInstrutorDisponivelVazio[] = $agendaInstrutorDisponivelPersonal;
                    }
                    if (empty($agendaInstrutorDisponivelTurma) == false) {
                        $agendaInstrutorDisponivelVazio[]  = $agendaInstrutorDisponivelTurma;
                    }
                    if (empty($agendaInstrutorDisponivelExtra) == false) {
                        $agendaInstrutorDisponivelVazio[] = $agendaInstrutorDisponivelExtra;
                    }   
                } 
                $agendaInstrutorDisponivelRetorno[] = $agendaInstrutorDisponivelVazio;
            } 
          
            if (empty($agendaInstrutorDisponivelRetorno) == false) {
                $retornoArray[] = $agendaInstrutorDisponivelRetorno; 
            }
        } 

        $retorno = [];        

        if (empty($retornoArray) == false) {
            foreach ($retornoArray as $array1) {
                foreach ($array1 as $array2 ) {
                    foreach ($array2 as $array3 ) {
                        foreach ($array3 as $key ) {
                            $retorno[] = $key;                       
                        }                   
                    }               
                }            
            };
        };

       return $retorno;
    }
 

    /**
     * Configura parametros de data
     *
     * @param array $parametros parametros passados pelo front
     */
    private function validarParametrosDeDatas(&$parametros)
    {
        $funcionario = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $dataAdmissaoFuncionario = $funcionario->getDataAdmissao();

        $dataInicial = (isset($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === true || empty($parametros[ConstanteParametros::CHAVE_DATA_INICIO]) === false);
        $dataFinal   = (isset($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === true || empty($parametros[ConstanteParametros::CHAVE_DATA_FIM]) === false);

        if ($dataInicial === false && $dataFinal === false) {
            $dataInicial = \Carbon\Carbon::now()->firstOfMonth();
            $dataFinal   = \Carbon\Carbon::now()->endOfMonth();

            if (is_null($dataAdmissaoFuncionario) === false) {
                $dataInicial = $dataAdmissaoFuncionario;
                $dataFinal   = \Carbon\Carbon::now();
            }
        }

        if ($dataInicial === true && $dataFinal === false) {
            $dataInicial = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_INICIO])->firstOfMonth();
            $dataFinal   = \Carbon\Carbon::now();
        }

        if ($dataInicial === false && $dataFinal === true) {
            $dataFinal   = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_FIM])->endOfMonth();
            $dataInicial = \Carbon\Carbon::now()->firstOfMonth();

            if (is_null($dataAdmissaoFuncionario) === false) {
                $dataInicial = $dataAdmissaoFuncionario;
            }
        }

        if ($dataInicial === true && $dataFinal === true) {
            $dataInicial = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_INICIO])->setHour(0)->setMinutes(0)->setSecond(0);
            $dataFinal   = \Carbon\Carbon::parse($parametros[ConstanteParametros::CHAVE_DATA_FIM])->setHour(23)->setMinutes(59)->setSecond(59);
        }

        $parametros[ConstanteParametros::CHAVE_DATA_INICIO] = $dataInicial;
        $parametros[ConstanteParametros::CHAVE_DATA_FIM]    = $dataFinal;
    }
    /**
     * Lista os registros de aulas e atividades para pagamento do professor
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function consultaAulasParaPagamento($parametros)
    {
        $this->validarParametrosDeDatas($parametros);
        $response = [
            'erro'        => null,
            'valor_total' => 0,
            'registros'   => [],
        ];

        $turmas          = [];
        $personal        = [];
        $atividadesExtra = [];
        $reposicoes      = [];
        $bonusClasses    = [];

        if ((is_null($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
            if (in_array(SituacoesSistema::MODALIDADE_TURMAS, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $modalidade        = SituacoesSistema::MODALIDADE_TURMAS;
                $turmasFuncionario = $this->turmaRepository->consultaAulasTurmaFuncionarioParaPagamento($parametros);

                if (empty($turmasFuncionario) === false) {
                    foreach ($turmasFuncionario as $turma) {
                        $turmaAulas = $turma['turmaAulas'];

                        foreach ($turmaAulas as $key => $turmaAula) {
                            if (empty($turmaAula['pagamentoTurmaAulas']) === false) {
                                $size      = count($turmaAula['pagamentoTurmaAulas']) - 1;
                                $pagamento = $turmaAula['pagamentoTurmaAulas'][$size];
                                if ($pagamento[ConstanteParametros::CHAVE_SITUACAO] !== SituacoesSistema::SITUACAO_CANCELADO) {
                                    unset($turma['turmaAulas'][$key]);
                                  }
                            }
                        }


                        if (count($turma['turmaAulas']) === 0) {
                            continue;
                        }
                       
                        $cabecalhoPagamento = new \App\Entity\Principal\CabecalhoPagamento();
                        $cabecalhoPagamento->setDescricao($turma['descricao']);
                        $cabecalhoPagamento->setTipoPagamento($modalidade);
                        $quantidadeAulas = count($turma['turmaAulas']);
                        $cabecalhoPagamento->setQuantidadeRegistros($quantidadeAulas);

                        $quantidadeContratos = count($turma['contratos']);
                        $valores = $this->funcionarioRepository->consultaValorHoraFuncionario($parametros[ConstanteParametros::CHAVE_FUNCIONARIO], $modalidade, $quantidadeContratos);

                        $valorHora = null;
                        if (empty($valores['funcionarioValorHoras']) === false) {
                            $valorHora = $valores['funcionarioValorHoras'][0];
                        } else if (empty($valores['nivel_instrutor']['valorHoras']) === false) {
                            $valorHora = $valores['nivel_instrutor']['valorHoras'][0];
                        }

                        if (is_null($valorHora) === true) {
                            $response['erro'] = 'Este funcionário não tem valores hora cadastrados';
                            return $response;
                        }

                        $cabecalhoPagamento->setValorHora($valorHora['valor']);
                        $cabecalhoPagamento->setValorExtra($valorHora['valor_extra']);
                        $cabecalhoPagamento->setValorBonus($valorHora['valor_bonus']);

                        $cabecalhoPagamento->setTotalValorHora(($cabecalhoPagamento->getValorHora() + $cabecalhoPagamento->getValorExtra()) * $quantidadeAulas);
                        $cabecalhoPagamento->setTotalValorBonus($cabecalhoPagamento->getValorBonus() * $quantidadeAulas);
                        $cabecalhoPagamento->setValorTotal($cabecalhoPagamento->getTotalValorHora());

                        $turmaAulas = array_map(
                            function ($i) {
                                return $i['id'];
                            },
                            $turma['turmaAulas']
                        );

                        $turmas[] = [
                            'descricao'              => $cabecalhoPagamento->getDescricao(),
                            'tipo_pagamento'         => $cabecalhoPagamento->getTipoPagamento(),
                            'quantidade_registros'   => $cabecalhoPagamento->getQuantidadeRegistros(),
                            'valor_hora'             => $cabecalhoPagamento->getValorHora(),
                            'valor_extra'            => $cabecalhoPagamento->getValorExtra(),
                            'valor_bonus'            => $cabecalhoPagamento->getValorBonus(),
                            'total_valor_hora'       => $cabecalhoPagamento->getTotalValorHora(),
                            'total_valor_bonus'      => $cabecalhoPagamento->getTotalValorBonus(),
                            'valor_total'            => $cabecalhoPagamento->getValorTotal(),
                            'registros_considerados' => $turmaAulas,
                        ];

                        $response['valor_total'] += $cabecalhoPagamento->getValorTotal();
                    }//end foreach
                }//end if
            }//end if

            if (in_array(SituacoesSistema::MODALIDADE_PERSONAL, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $modalidade  = SituacoesSistema::MODALIDADE_PERSONAL;
                $funcionario = $this->funcionarioRepository->consultaAulasPersonalParaPagamento($parametros);

                if (empty($funcionario) === false) {
                    $funcionario = $funcionario[0];
                    $diarios     = $funcionario['alunoDiarioPersonals'];
                    $horariosJaRegistrados = [];
                    $diariosRegistrados    = [];
                    $dataTemp              = '';
                    $diariosRegistradosAgrupados = 0; 

                    foreach ($diarios as $diario) {
                        if (empty($diario['pagamentoAlunoDiarioPersonals']) === false) {
                            $filter = array_filter(
                                $diario['pagamentoAlunoDiarioPersonals'],
                                function ($i) {
                                    return in_array($i[ConstanteParametros::CHAVE_SITUACAO], [ SituacoesSistema::SITUACAO_PAGO, SituacoesSistema::SITUACAO_PAGAMENTO_ANDAMENTO, SituacoesSistema::SITUACAO_PENDENTE ]);
                                }
                            );
                            
                            if (count($filter) > 0) {
                                continue;
                            }
                        }
                        
                        if ($diario['data_aula']->format('Y-m-d h:i') !== $dataTemp) {
                            $dataTemp = $diario['data_aula']->format('Y-m-d h:i');
                            $diariosRegistradosAgrupados++;
                        }

                        $diariosRegistrados[] = $diario['id'];

                        if (in_array($diario['data_aula'], $horariosJaRegistrados) === true) {
                            continue;
                        }

                        $horariosJaRegistrados[] = $diario['data_aula'];
                    }//end foreach

                    $cabecalhoPagamento = new \App\Entity\Principal\CabecalhoPagamento();
                    $cabecalhoPagamento->setDescricao('Aulas Personal');
                    $cabecalhoPagamento->setTipoPagamento($modalidade);
                    if ($diariosRegistradosAgrupados > 0) {
                        // $quantidadeAulas = count($diariosRegistrados);
                        $quantidadeAulas = $diariosRegistradosAgrupados;
                        $cabecalhoPagamento->setQuantidadeRegistros($quantidadeAulas);

                        $valores = $this->funcionarioRepository->consultaValorHoraFuncionario($funcionario['id'], $modalidade, 1);

                        $valorHora = null;
                        if (empty($valores['funcionarioValorHoras']) === false) {
                            $valorHora = $valores['funcionarioValorHoras'][0];
                        } else if (empty($valores['nivel_instrutor']['valorHoras']) === false) {
                            $valorHora = $valores['nivel_instrutor']['valorHoras'][0];
                        }

                        if (is_null($valorHora) === true) {
                            $response['erro'] = 'Este funcionário não tem valores hora cadastrados';
                            return $response;
                        }

                        $cabecalhoPagamento->setValorHora($valorHora['valor']);
                        $cabecalhoPagamento->setValorExtra($valorHora['valor_extra']);
                        $cabecalhoPagamento->setValorBonus($valorHora['valor_bonus']);

                        $cabecalhoPagamento->setTotalValorHora(($cabecalhoPagamento->getValorHora() + $cabecalhoPagamento->getValorExtra()) * $quantidadeAulas);
                        $cabecalhoPagamento->setTotalValorBonus($cabecalhoPagamento->getValorBonus() * $quantidadeAulas);
                        $cabecalhoPagamento->setValorTotal($cabecalhoPagamento->getTotalValorHora());

                        $personal[] = [
                            'descricao'              => $cabecalhoPagamento->getDescricao(),
                            'tipo_pagamento'         => $cabecalhoPagamento->getTipoPagamento(),
                            'quantidade_registros'   => $cabecalhoPagamento->getQuantidadeRegistros(),
                            'valor_hora'             => $cabecalhoPagamento->getValorHora(),
                            'valor_extra'            => $cabecalhoPagamento->getValorExtra(),
                            'valor_bonus'            => $cabecalhoPagamento->getValorBonus(),
                            'total_valor_hora'       => $cabecalhoPagamento->getTotalValorHora(),
                            'total_valor_bonus'      => $cabecalhoPagamento->getTotalValorBonus(),
                            'valor_total'            => $cabecalhoPagamento->getValorTotal(),
                            'registros_considerados' => $diariosRegistrados,
                        ];

                        $response['valor_total'] += $cabecalhoPagamento->getValorTotal();
                    }
                }//end if
            }//end if

            if (in_array(SituacoesSistema::MODALIDADE_ATIVIDADE_EXTRA, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $modalidade  = SituacoesSistema::MODALIDADE_ATIVIDADE_EXTRA;
                $funcionario = $this->funcionarioRepository->consultaAtividadesExtraParaPagamento($parametros);

                if (empty($funcionario) === false) {
                    $funcionario = $funcionario[0];
                    foreach ($funcionario['atividadeExtrasPendentes'] as $atividade) {
                        if (empty($atividade['pagamentoAtividadeExtras']) === false) {
                            $filter = array_filter(
                                $atividade['pagamentoAtividadeExtras'],
                                function ($i) {
                                    return in_array($i[ConstanteParametros::CHAVE_SITUACAO], [ SituacoesSistema::SITUACAO_PAGO, SituacoesSistema::SITUACAO_PAGAMENTO_ANDAMENTO, SituacoesSistema::SITUACAO_PENDENTE ]);
                                }
                            );

                            if (count($filter) > 0) {
                                continue;
                            }
                        }

                        $cabecalhoPagamento = new \App\Entity\Principal\CabecalhoPagamento();

                        $calculo = new \App\Helper\FunctionHelper;
                        
                        if (isset($atividade['descricao_atividade'])){
                            $cabecalhoPagamento->setDescricao($atividade['descricao_atividade']);
                        }else{
                            $cabecalhoPagamento->setDescricao('Descricao de Atividade nao informada');
                        }

                        $horaInicial = $atividade['data_hora_inicio']->format("H:i");
                        $horaFinal = $atividade['data_hora_fim']->format("H:i");
                        $horasTrabalhadasEmSegundos = $calculo->calculaTempoSegundos($horaInicial, $horaFinal);
                        // $horasTrabalhadas = $calculo->calculaTempo($horaInicial, $horaFinal);

                        $cabecalhoPagamento->setTipoPagamento($modalidade);
                        $cabecalhoPagamento->setQuantidadeRegistros(1);

                        $quantidadeContratos = count($atividade['alunoAtividadeExtras']);
                        $valores = $this->funcionarioRepository->consultaValorHoraFuncionario($funcionario['id'], $modalidade, $quantidadeContratos);

                        $valorHora = null;
                        if (empty($valores['funcionarioValorHoras']) === false) {
                            $valorHora = $valores['funcionarioValorHoras'][0];
                        } else if (empty($valores['nivel_instrutor']['valorHoras']) === false) {
                            $valorHora = $valores['nivel_instrutor']['valorHoras'][0];
                        }

                        if (is_null($valorHora) === true) {
                            $response['erro'] = 'Este funcionário não tem valores hora cadastrados';
                            return $response;
                        }

                        $cabecalhoPagamento->setValorHora($valorHora['valor']);
                        $cabecalhoPagamento->setValorExtra($valorHora['valor_extra']);
                        $cabecalhoPagamento->setValorBonus($valorHora['valor_bonus']);

                        $totalValorHoraCalculada = ($cabecalhoPagamento->getValorHora()/3600) * $horasTrabalhadasEmSegundos;
                        
                        $cabecalhoPagamento->setTotalValorHora( $totalValorHoraCalculada + $cabecalhoPagamento->getValorExtra());
                        
                        $cabecalhoPagamento->setTotalValorBonus($cabecalhoPagamento->getValorBonus());
                        $cabecalhoPagamento->setValorTotal($cabecalhoPagamento->getTotalValorHora());

                        $atividades = [ $atividade['id'] ];

                        $atividadesExtra[] = [
                            'descricao'              => $cabecalhoPagamento->getDescricao(),
                            'tipo_pagamento'         => $cabecalhoPagamento->getTipoPagamento(),
                            'quantidade_registros'   => $cabecalhoPagamento->getQuantidadeRegistros(),
                            'valor_hora'             => $cabecalhoPagamento->getValorHora(),
                            'valor_extra'            => $cabecalhoPagamento->getValorExtra(),
                            'valor_bonus'            => $cabecalhoPagamento->getValorBonus(),
                            'total_valor_hora'       => $cabecalhoPagamento->getTotalValorHora(),
                            'total_valor_bonus'      => $cabecalhoPagamento->getTotalValorBonus(),
                            'valor_total'            => $cabecalhoPagamento->getValorTotal(),
                            'registros_considerados' => $atividades,
                        ];

                        $response['valor_total'] += $cabecalhoPagamento->getValorTotal();
                    }//end foreach
                }//end if
            }//end if

            if (in_array(SituacoesSistema::MODALIDADE_REPOSICAO_AULA, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $modalidade  = SituacoesSistema::MODALIDADE_REPOSICAO_AULA;
                $funcionario = $this->funcionarioRepository->consultaReposicoesAulaParaPagamento($parametros);

                if (empty($funcionario) === false) {
                    $funcionario = $funcionario[0];
                    foreach ($funcionario['responsavelReposicaoAulas'] as $atividade) {
                        if (empty($atividade['pagamentoReposicaoAulas']) === false) {
                            $filter = array_filter(
                                $atividade['pagamentoReposicaoAulas'],
                                function ($i) {
                                    return in_array($i[ConstanteParametros::CHAVE_SITUACAO], [ SituacoesSistema::SITUACAO_PAGO, SituacoesSistema::SITUACAO_PAGAMENTO_ANDAMENTO, SituacoesSistema::SITUACAO_PENDENTE ]);
                                }
                            );

                            if (count($filter) > 0) {
                                continue;
                            }
                        }

                        $cabecalhoPagamento = new \App\Entity\Principal\CabecalhoPagamento();
                        
                        if (isset($atividade['descricao_atividade'])){
                            $descricaoAtividade = $atividade['descricao_atividade'];
                        }else{
                            $descricaoAtividade = ('Descricao de Atividade nao informada');
                        }
                        
                        if (empty($descricaoAtividade) === true || is_null($descricaoAtividade) === true) {
                            $descricaoAtividade = "Reposição";
                        }

                        $cabecalhoPagamento->setDescricao($descricaoAtividade);
                        $cabecalhoPagamento->setTipoPagamento($modalidade);
                        $cabecalhoPagamento->setQuantidadeRegistros(1);

                        $quantidadeContratos = 1;
                        $valores = $this->funcionarioRepository->consultaValorHoraFuncionario($funcionario['id'], $modalidade, $quantidadeContratos);

                        $valorHora = null;
                        if (empty($valores['funcionarioValorHoras']) === false) {
                            $valorHora = $valores['funcionarioValorHoras'][0];
                        } else if (empty($valores['nivel_instrutor']['valorHoras']) === false) {
                            $valorHora = $valores['nivel_instrutor']['valorHoras'][0];
                        }

                        if (is_null($valorHora) === true) {
                            $response['erro'] = 'Este funcionário não tem valores hora cadastrados';
                            return $response;
                        }

                        $cabecalhoPagamento->setValorHora($valorHora['valor']);
                        $cabecalhoPagamento->setValorExtra($valorHora['valor_extra']);
                        $cabecalhoPagamento->setValorBonus($valorHora['valor_bonus']);

                        $cabecalhoPagamento->setTotalValorHora($cabecalhoPagamento->getValorHora() + $cabecalhoPagamento->getValorExtra());
                        $cabecalhoPagamento->setTotalValorBonus($cabecalhoPagamento->getValorBonus());
                        $cabecalhoPagamento->setValorTotal($cabecalhoPagamento->getTotalValorHora());

                        $reposicao = [ $atividade['id'] ];

                        $reposicoes[] = [
                            'descricao'              => $cabecalhoPagamento->getDescricao(),
                            'tipo_pagamento'         => $cabecalhoPagamento->getTipoPagamento(),
                            'quantidade_registros'   => $cabecalhoPagamento->getQuantidadeRegistros(),
                            'valor_hora'             => $cabecalhoPagamento->getValorHora(),
                            'valor_extra'            => $cabecalhoPagamento->getValorExtra(),
                            'valor_bonus'            => $cabecalhoPagamento->getValorBonus(),
                            'total_valor_hora'       => $cabecalhoPagamento->getTotalValorHora(),
                            'total_valor_bonus'      => $cabecalhoPagamento->getTotalValorBonus(),
                            'valor_total'            => $cabecalhoPagamento->getValorTotal(),
                            'registros_considerados' => $reposicao,
                        ];

                        $response['valor_total'] += $cabecalhoPagamento->getValorTotal();
                    }//end foreach
                }//end if
            }//end if

            if (in_array(SituacoesSistema::MODALIDADE_BONUS_CLASS, $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) {
                $modalidade  = SituacoesSistema::MODALIDADE_BONUS_CLASS;
                $funcionario = $this->funcionarioRepository->consultaBonusClassParaPagamento($parametros);

                if (empty($funcionario) === false) {
                    $funcionario = $funcionario[0];
                    foreach ($funcionario['bonusClasses'] as $bonusClass) {
                        if (empty($bonusClass['pagamentoBonusClasses']) === false) {
                            $filter = array_filter(
                                $bonusClass['pagamentoBonusClasses'],
                                function ($i) {
                                    return in_array($i[ConstanteParametros::CHAVE_SITUACAO], [ SituacoesSistema::SITUACAO_PAGO, SituacoesSistema::SITUACAO_PAGAMENTO_ANDAMENTO, SituacoesSistema::SITUACAO_PENDENTE ]);
                                }
                            );

                            if (count($filter) > 0) {
                                continue;
                            }
                        }

                        $cabecalhoPagamento = new \App\Entity\Principal\CabecalhoPagamento();
                        $cabecalhoPagamento->setTipoPagamento($modalidade);

                        $dataAula      = $bonusClass[ConstanteParametros::CHAVE_DATA_AULA]->format('d/m/Y');
                        $horarioInicio = $bonusClass[ConstanteParametros::CHAVE_HORARIO_INICIO]->format('H:i');
                        $horarioFim    = $bonusClass[ConstanteParametros::CHAVE_HORARIO_TERMINO]->format('H:i');
                        $descricao     = "Bônus Class $dataAula $horarioInicio - $horarioFim";
                        $cabecalhoPagamento->setDescricao($descricao);

                        $quantidadeRegistros = \Carbon\Carbon::parse($bonusClass[ConstanteParametros::CHAVE_HORARIO_TERMINO])->floatDiffInHours($bonusClass[ConstanteParametros::CHAVE_HORARIO_INICIO]);
                        // Multiplicar por 2 para obter 2 aulas por hora.
                        $quantidadeRegistros *= 2;
                        $cabecalhoPagamento->setQuantidadeRegistros($quantidadeRegistros);

                        $quantidadeContratos = 1;
                        $valores = $this->funcionarioRepository->consultaValorHoraFuncionario($funcionario['id'], $modalidade, $quantidadeContratos);

                        $valorHora = null;
                        if (empty($valores['funcionarioValorHoras']) === false) {
                            $valorHora = $valores['funcionarioValorHoras'][0];
                        } else if (empty($valores['nivel_instrutor']['valorHoras']) === false) {
                            $valorHora = $valores['nivel_instrutor']['valorHoras'][0];
                        }

                        if (is_null($valorHora) === true) {
                            $response['erro'] = 'Este funcionário não tem valores hora cadastrados';
                            return $response;
                        }

                        $cabecalhoPagamento->setValorHora($valorHora['valor']);
                        $cabecalhoPagamento->setValorExtra($valorHora['valor_extra']);
                        $cabecalhoPagamento->setValorBonus($valorHora['valor_bonus']);

                        $cabecalhoPagamento->setTotalValorHora(($cabecalhoPagamento->getValorHora() + $cabecalhoPagamento->getValorExtra()) * $quantidadeRegistros / 2);
                        $cabecalhoPagamento->setTotalValorBonus($cabecalhoPagamento->getValorBonus() * $quantidadeRegistros);
                        $cabecalhoPagamento->setValorTotal($cabecalhoPagamento->getTotalValorHora());

                        $bonus = [ $bonusClass['id'] ];

                        $bonusClasses[] = [
                            'descricao'              => $cabecalhoPagamento->getDescricao(),
                            'tipo_pagamento'         => $cabecalhoPagamento->getTipoPagamento(),
                            'quantidade_registros'   => $cabecalhoPagamento->getQuantidadeRegistros(),
                            'valor_hora'             => $cabecalhoPagamento->getValorHora(),
                            'valor_extra'            => $cabecalhoPagamento->getValorExtra(),
                            'valor_bonus'            => $cabecalhoPagamento->getValorBonus(),
                            'total_valor_hora'       => $cabecalhoPagamento->getTotalValorHora(),
                            'total_valor_bonus'      => $cabecalhoPagamento->getTotalValorBonus(),
                            'valor_total'            => $cabecalhoPagamento->getValorTotal(),
                            'registros_considerados' => $bonus,
                        ];

                        $response['valor_total'] += $cabecalhoPagamento->getValorTotal();
                    }//end foreach
                }//end if
            }//end if
        }//end if

        $response['registros'] = array_merge($turmas, $personal, $atividadesExtra, $reposicoes, $bonusClasses);
        return $response;
    }

    /**
     * Gera contas a pagar para um funcionário com base nas ids de aulas
     *
     * @param array $parametros
     * @param string $mensagem
     */
    public function geraContaPagarFuncionario ($parametros, &$mensagem)
    {
        $dados       = $this->consultaAulasParaPagamento($parametros);
        $funcionario = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        $observacaoContaPagar = "Conta à pagar referente a: ";

        $pagamentoFuncionario = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoFuncionario::class);
        $pagamentoFuncionario->setFranqueada($this->franqueadaRepository->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID));
        $pagamentoFuncionario->setFuncionario($funcionario);
        $pagamentoFuncionario->setDataPagamento(\Carbon\Carbon::now());
        $pagamentoFuncionario->setSituacao(SituacoesSistema::SITUACAO_PENDENTE);
        $pagamentoFuncionario->setValor($dados[ConstanteParametros::CHAVE_VALOR_TOTAL]);
        self::persistSeguro($pagamentoFuncionario, $mensagem);

        foreach ($dados['registros'] as $registro) {
            $registro[ConstanteParametros::CHAVE_PAGAMENTO_FUNCIONARIO] = $pagamentoFuncionario;
            $cabecalho = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\CabecalhoPagamento::class, true, $registro);
            self::persistSeguro($cabecalho, $mensagem);

            $observacaoContaPagar .= $cabecalho->getDescricao() . '; ';

            foreach ($registro['registros_considerados'] as $id) {
                $pagamento = [ConstanteParametros::CHAVE_CABECALHO_PAGAMENTO => $cabecalho];

                if ($cabecalho->getTipoPagamento() === SituacoesSistema::MODALIDADE_TURMAS) {
                    $pagamento[ConstanteParametros::CHAVE_TURMA_AULA] = $this->turmaAulaRepository->find($id);
                    $pagamento = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoTurmaAula::class, true, $pagamento);
                } else if ($cabecalho->getTipoPagamento() === SituacoesSistema::MODALIDADE_PERSONAL) {
                    $pagamento[ConstanteParametros::CHAVE_ALUNO_DIARIO_PERSONAL] = $this->alunoDiarioPersonalRepository->find($id);
                    $pagamento = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoAlunoDiarioPersonal::class, true, $pagamento);
                } else if ($cabecalho->getTipoPagamento() === SituacoesSistema::MODALIDADE_ATIVIDADE_EXTRA) {
                    $pagamento[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA] = $this->atividadeExtraRepository->find($id);
                    $pagamento = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoAtividadeExtra::class, true, $pagamento);
                } else if ($cabecalho->getTipoPagamento() === SituacoesSistema::MODALIDADE_REPOSICAO_AULA) {
                    $pagamento[ConstanteParametros::CHAVE_REPOSICAO_AULA] = $this->reposicaoAulaRepository->find($id);
                    $pagamento = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoReposicaoAula::class, true, $pagamento);
                } else if ($cabecalho->getTipoPagamento() === SituacoesSistema::MODALIDADE_BONUS_CLASS) {
                    $pagamento[ConstanteParametros::CHAVE_BONUS_CLASS] = $this->bonusClassRepository->find($id);
                    $pagamento = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PagamentoBonusClass::class, true, $pagamento);
                }

                self::persistSeguro($pagamento, $mensagem);
            }//end foreach
        }//end foreach

        $planoContaSalario = $this->planoContaRepository->findOneBy([ConstanteParametros::CHAVE_CHAVE_CONSULTA => 'SAL']);
        $formaCobranca     = $this->formaPagamentoRepository->findOneBy([ConstanteParametros::CHAVE_LIQUIDACAO_IMEDIATA => true, ConstanteParametros::CHAVE_DESCRICAO_ABREVIADA => 'Dinheiro']);
        $franqueada        = $this->franqueadaRepository->find(\App\Helper\VariaveisCompartilhadas::$franqueadaID);

        $parcelas = [
            [
                ConstanteParametros::CHAVE_NF_DATA_EMISSAO              => \Carbon\Carbon::now()->format('Y-m-d\TH:i:s.uP'),
                ConstanteParametros::CHAVE_TIT_DATA_VENCIMENTO          => (\Carbon\Carbon::now())->add(1, 'day')->format('Y-m-d\TH:i:s.uP'),
                ConstanteParametros::CHAVE_TIT_NUMERO_PARCELA_DOCUMENTO => 1,
                ConstanteParametros::CHAVE_TIT_VALOR_DOCUMENTO          => $pagamentoFuncionario->getValor(),
                ConstanteParametros::CHAVE_VALOR_ORIGINAL               => $pagamentoFuncionario->getValor(),
                ConstanteParametros::CHAVE_TIT_NARRATIVA_PLANO_CONTA    => $observacaoContaPagar,
                ConstanteParametros::CHAVE_FORMA_COBRANCA               => $formaCobranca->getId(),
            ],
        ];

        $parametrosContaPagar = [
            ConstanteParametros::CHAVE_USUARIO           => $parametros[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_FRANQUEADA        => $franqueada->getId(),
            ConstanteParametros::CHAVE_CONTA             => $franqueada->getContaPadrao()->getId(),
            ConstanteParametros::CHAVE_FORNECEDOR_PESSOA => $funcionario->getPessoa()->getId(),
            ConstanteParametros::CHAVE_VALOR_TOTAL       => $pagamentoFuncionario->getValor(),
            ConstanteParametros::CHAVE_PLANO_CONTA       => [
                [
                    ConstanteParametros::CHAVE_PLANO_CONTA      => $planoContaSalario->getId(),
                    ConstanteParametros::CHAVE_COMPLEMENTO      => $planoContaSalario->getDescricao(),
                    ConstanteParametros::CHAVE_NUMERO_SEQUENCIA => 1,
                    ConstanteParametros::CHAVE_VALOR            => $pagamentoFuncionario->getValor(),
                ],
            ],
            ConstanteParametros::CHAVE_PARCELA           => $parcelas,
            ConstanteParametros::CHAVE_NUMERO_PARCELAS   => 1,
            ConstanteParametros::CHAVE_FORMA_COBRANCA    => $formaCobranca->getId(),
            ConstanteParametros::CHAVE_VALOR_PARCELA     => $pagamentoFuncionario->getValor(),
            ConstanteParametros::CHAVE_OBSERVACAO        => $observacaoContaPagar,
        ];

        $contaPagarORM   = $this->contaPagarFacade->criar($mensagem, $parametrosContaPagar);
        $planoContaORM   = $this->planoContasContaPagarFacade->criarMultiplos($mensagem, $contaPagarORM, $parametrosContaPagar);
        $titulosPagarORM = $this->tituloPagarFacade->criar($mensagem, $contaPagarORM, $parametrosContaPagar, false);

        $pagamentoFuncionario->setContaPagar($contaPagarORM);

        self::flushSeguro($mensagem);

        return empty($mensagem);
    }

    /**
     * Busca por usuário com base no usuário e franqueada logados
     *
     * @param string $mensagemErro
     *
     * @return boolean|\App\Entity\Principal\Funcionario
     */
    public function buscarPorUsuarioFranqueadaLogado(&$mensagemErro, $parametro)
    {
       
        if (is_null($parametro['usuario']) === true) {
            $usuario = \App\Helper\VariaveisCompartilhadas::$usuarioID;
        } else {
            $usuario = $parametro['usuario'];     
        }
       
        $parametros     = [
            ConstanteParametros::CHAVE_USUARIO    =>  $usuario,
            ConstanteParametros::CHAVE_FRANQUEADA => \App\Helper\VariaveisCompartilhadas::$franqueadaID,
        ];

        $funcionarioORM = $this->funcionarioRepository->findOneBy($parametros);
        if (is_null($funcionarioORM) === true) {
            $mensagemErro = "Funcionário não encontrado para o usuário logado.";
            return false;
        }

        return $funcionarioORM;
    }


}
