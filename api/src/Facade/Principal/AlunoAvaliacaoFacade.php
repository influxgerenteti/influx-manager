<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\AlunoAvaliacaoBO;
use App\Facade\Principal\ContratoFacade;
use App\Helper\SituacoesSistema;
use App\Repository\Principal\LicaoRepository;

/**
 *
 * @author Luiz A Costa
 */
class AlunoAvaliacaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunoAvaliacaoRepository
     */
    private $alunoAvaliacaoRepository;
    
    /**
     *
     * @var \App\Repository\Principal\LicaoRepository
     */
    private $licaoRepository;

     /**
     *
     * @var \App\Repository\Principal\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoAvaliacaoBO
     */
    private $alunoAvaliacaoBO;

    /**
     *
     * @var \App\Facade\Principal\ContratoFacade
     */
    private $contratoFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->licaoRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\Licao::class);
        $this->alunoAvaliacaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoAvaliacao::class);
        $this->alunoAvaliacaoBO         = new AlunoAvaliacaoBO(self::getEntityManager());
        $this->contratoFacade           = new ContratoFacade($managerRegistry);
        $this->alunoDiarioRepository    = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiario::class);
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
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param \App\Entity\Principal\AlunoAvaliacao $retornoORM retorno do objeto
     *
     * @return bool
     */
    public function criar(&$mensagemErro, $parametros=[], &$retornoORM=null)
    {
        $objetoORM = null;
        if ($this->alunoAvaliacaoBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunoAvaliacao::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
            $retornoORM = $objetoORM;
        }

        return (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
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
        $objetoORM = $this->alunoAvaliacaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AlunoAvaliacao não encontrado na base de dados.";
        } else {
            if ($this->alunoAvaliacaoBO->podeCriar($parametros, $mensagemErro) === true) {
                $objetoORM->setNotaFinalComposition(null);
                $objetoORM->setNotaFinalEscrita(null);
                $objetoORM->setNotaFinalTest(null);
                $objetoORM->setNotaMidTermComposition(null);
                $objetoORM->setNotaMidTermEscrita(null);
                $objetoORM->setNotaMidTermTest(null);
                $objetoORM->setNotaRetakeFinalEscrita(null);
                $objetoORM->setNotaRetakeMidTermEscrita(null);
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Lancamento/Atualizacao de Notas para Aluno Avaliacao
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return bool
     */
    public function lancarAtualizarNotas(&$mensagemErro, $parametros)
    {
        $bPossuiAlunoAvaliacaoId = isset($parametros[ConstanteParametros::CHAVE_ID]);
        if ($bPossuiAlunoAvaliacaoId === true) {
            $alunoAvaliacaoId = $parametros[ConstanteParametros::CHAVE_ID];
            unset($parametros[ConstanteParametros::CHAVE_ID]);
            $bSuccesso = $this->atualizar($mensagemErro, $alunoAvaliacaoId, $parametros);
        } else {
            $bSuccesso = $this->criar($mensagemErro, $parametros);
        }

        return $bSuccesso;
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
        return empty($mensagemErro);
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
        return $this->alunoAvaliacaoRepository->prepararDadosRelatorio($parametros);
    }

    /**
     * Gera as linhas do excel pro relatório de notas Personal
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array  $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioNotasPersonal(&$mensagemErro, $parametros)
    {

        $dadosRelatorio = $this->alunoAvaliacaoRepository->buscarDadosRelatorioNotasPersonal($mensagemErro, $parametros);
        
        if (empty($mensagem) === false) {
            return false;
        }

        $cabecalho = [
            "Nome do Aluno",
            "Livro",
            "Mid Term",
            "Final Test",
            "Professor",
            "Frequência",
        ];

        $linhas_excel = [$cabecalho];

        $retorno = [
            "contratos" => $dadosRelatorio,
            "totais"    => [],
        ];
        if ($parametros['aluno'] == null) {    
             foreach ($retorno["contratos"] as $k => $contrato) {
                    $linha = [
                        $contrato["nome_aluno"],
                        $contrato["livro"],
                        $contrato["mid_term"],
                        $contrato["final_test"],
                    ];

                    $instrutorORM = $this->contratoFacade->getInstrutorContratoPersonal($contrato["contrato_id"]);
                    if ($instrutorORM === null) {
                        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                            unset($retorno["contratos"][$k]);
                            continue;
                        }

                        $instrutor = '';
                    } else {
                        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                            if ($instrutorORM->getId() !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                                unset($retorno["contratos"][$k]);
                                continue;
                            }
                        }

                        $instrutor = $instrutorORM->getApelido();
                    }

                    //buscar frequencia aluno
                    $AlunoID = $contrato["aluno_id"];
                    $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($mensagemErro, $AlunoID);
        
                    if(count($alunoDiarioORM) > 0) {
                        $frequencia = $alunoDiarioORM[0]['frequencia'];
                        $frequencia = round($frequencia, 2).'%';
                    }else {
                        $frequencia = '';
                    }
                    
                    $linha[]        = $instrutor;
                    $linha[]        = $frequencia;

                    $linhas_excel[] = $linha;
                }//end foreach

                $pontuacaoTotal  = 0;
                $quantidadeNotas = 0;
                foreach ($dadosRelatorio as $dado) {
                    if (is_null($dado["mid_term"]) === false) {
                        $pontuacaoTotal += $dado["mid_term"];
                        $quantidadeNotas++;
                    }

                    if (is_null($dado["final_test"]) === false) {
                        $pontuacaoTotal += $dado["final_test"];
                        $quantidadeNotas++;
                    }
                }

                $retorno["totais"]["Total de registros"] = count($dadosRelatorio);
                if ($quantidadeNotas === 0) {
                    $retorno["totais"]["Media geral"] = 0;
                } else {
                    $retorno["totais"]["Media geral"] = $pontuacaoTotal / $quantidadeNotas;
                }

                foreach ($retorno["totais"] as $k => $v) {
                    $linha = [
                        "",
                        "",
                        "",
                        "",
                    ];

                    $linha[] = "$k: $v";

                    $linhas_excel[] = $linha;
                }
            } else {
                $dadosRelatorioCount = 0;
                foreach ($retorno["contratos"] as $k => $contrato) {
                    $AlunoID = $contrato["aluno_id"];
                    if ($parametros['aluno'] == $AlunoID ) { 
                        $dadosRelatorioCount++; 
                       $linha = [
                            $contrato["nome_aluno"],
                            $contrato["livro"],
                            $contrato["mid_term"],
                            $contrato["final_test"],
                        ];

                        $instrutorORM = $this->contratoFacade->getInstrutorContratoPersonal($contrato["contrato_id"]);
                        if ($instrutorORM === null) {
                            if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                                unset($retorno["contratos"][$k]);
                                continue;
                            }

                            $instrutor = '';
                        } else {
                            if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                                if ($instrutorORM->getId() !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                                    unset($retorno["contratos"][$k]);
                                    continue;
                                }
                            }

                            $instrutor = $instrutorORM->getApelido();
                        }

                        //buscar frequencia aluno
                        $AlunoID = $contrato["aluno_id"];
                        $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($mensagemErro, $AlunoID);
            
                        if(count($alunoDiarioORM) > 0) {
                            $frequencia = $alunoDiarioORM[0]['frequencia'];
                            $frequencia = round($frequencia, 2).'%';
                        }else {
                            $frequencia = '';
                        }
                        
                        $linha[]        = $instrutor;
                        $linha[]        = $frequencia;

                        $linhas_excel[] = $linha;
                    }
                }//end foreach

                    $pontuacaoTotal  = 0;
                    $quantidadeNotas = 0;
                    foreach ($dadosRelatorio as $dado) {
                        $AlunoID = $dado["aluno_id"];
                        if ($dado['aluno_id'] == $parametros['aluno'] ) {  
 
                            if (is_null($dado["mid_term"]) === false) {
                                $pontuacaoTotal += $dado["mid_term"];
                                $quantidadeNotas++;
                            }

                            if (is_null($dado["final_test"]) === false) {
                                $pontuacaoTotal += $dado["final_test"];
                                $quantidadeNotas++;
                            }
                        }
                    }

                    $retorno["totais"]["Total de registros"] = $dadosRelatorioCount;
                    if ($quantidadeNotas === 0) {
                        $retorno["totais"]["Media geral"] = 0;
                    } else {
                        $retorno["totais"]["Media geral"] = $pontuacaoTotal / $quantidadeNotas;
                    }

                    foreach ($retorno["totais"] as $k => $v) {
                        $linha = [
                            "",
                            "",
                            "",
                            "",
                        ];

                        $linha[] = "$k: $v";

                        $linhas_excel[] = $linha;
                    }


            }        
        return $linhas_excel;
    }

    public function gerarDadosRelatorioNotasTurmasAgrupadoTurma($parametros)
    {
        $result = $this->alunoAvaliacaoRepository->buscarDadosRelatorioNotasTurmasAlunosAgrupadoTurma($mensagemErro, $parametros);
        /*
            foreach ($dadosRelatorio as $alunoAvaliacao) {
                $turmaId = $alunoAvaliacao["turma_id"];

                if(!isset($response[$turmaId])) {
                    $response[$turmaId]['turma'] = $alunoAvaliacao['turma_descricao'];
                    $response[$turmaId]['livro'] = $alunoAvaliacao['livro_descricao'];
                    $response[$turmaId]['professor'] = $alunoAvaliacao['instrutor_apelido'];
                }
            // Parte do código que adicionava os alunos da turma;
            // A princípio foi removida do código pois não está implementado o uso dessa dados no front;
            // Essa funcionalidade tornava a geração do relatório demorada, sendo que esses dados não estão sendo usados.
            
                // $AlunoID = $alunoAvaliacao["aluno_id"];
                
                // $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($AlunoID);
        
                // if(count($alunoDiarioORM) > 0) {
                //     $frequencia = round($alunoDiarioORM[0]['frequencia'],2);
                // }else {
                //     $frequencia = '';
                // } 

                // $aluno = [
                //     'nome_aluno' => $alunoAvaliacao["nome_aluno"],
                //     'mid_term_test' => $alunoAvaliacao["mid_term_test"],
                //     'mid_term_composition' => $alunoAvaliacao["mid_term_composition"],
                //     'mid_term_retake' => $alunoAvaliacao['mid_term_retake'],
                //     'final_test' => $alunoAvaliacao['final_test'],
                //     'final_composition' => $alunoAvaliacao['final_composition'],
                //     'final_retake' => $alunoAvaliacao['final_retake'],
                //     'frequencia' => $frequencia
                // ];

                // $response[$turmaId]['alunos'][] = $aluno;

            
            }
        */
        return $result;
    }

    public function gerarDadosRelatorioNotasTurmasAlunos($parametros)
    {
        $dadosRelatorio = $this->alunoAvaliacaoRepository->buscarDadosRelatorioNotasTurmasAlunosNovo($parametros);
        

        // Estrutura retirada pois estava tornando a rota muito demorada 
        // Agora a frenquencia estou buscando diretamente na mesma consulta onde busco as notas dos alunos
        /*
            $response = [];
            
            foreach($dadosRelatorio as $aluno) {
                $AlunoID = $aluno["aluno_id"];
                $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($AlunoID);
                
                if(count($alunoDiarioORM) > 0) {
                    $frequencia = $alunoDiarioORM[0]['frequencia'];
                    $frequencia = round($frequencia, 2).'%';
                }else {
                    $frequencia = '';
                }
                $response[] = [
                    'nome_aluno' => $aluno['nome_aluno'],
                    'livro' => $aluno['livro_descricao'],
                    'professor' => $aluno['instrutor_apelido'],
                    'turma' => $aluno['turma_descricao'],
                    'modalidade_turma' => $aluno['modalidade_turma'],
                    'frequencia' => $frequencia,
                    'mid_term_test' => $aluno["mid_term_test"],
                    'mid_term_composition' => $aluno["mid_term_composition"],
                    'mid_term_retake' => $aluno['mid_term_retake'],
                    'final_test' => $aluno['final_test'],
                    'final_composition' => $aluno['final_composition'],
                    'final_retake' => $aluno['final_retake']
                ];
            }
        */

        return $dadosRelatorio;
    }

    /**
     * Gera as linhas do excel pro relatório de notas agrupado por turma
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array  $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioNotasAgrupadoTurma(&$mensagemErro, $parametros)
    {

        $dadosRelatorio = $this->alunoAvaliacaoRepository->buscarDadosRelatorioNotasAlunosAgrupadoTurma($mensagemErro, $parametros);

        if (empty($mensagem) === false) {
            return false;
        }

        $cabecalho    = [
            "",
            "",
            "Notas de alunos por turma",
            "",
        ];
        $linhas_excel = [$cabecalho];

        $turmaAnterior = null;
        if ($parametros['aluno'] == null) {    
            foreach ($dadosRelatorio as $alunoAvaliacao) {
                $turmaId = $alunoAvaliacao["turma_id"];
                if ($turmaAnterior !== $turmaId) {
                    $linhas_excel[] = [
                        "Turma: " . $alunoAvaliacao["turma_descricao"],
                        "Livro: " . $alunoAvaliacao["livro_descricao"],
                        "Professor: " . $alunoAvaliacao["instrutor_apelido"],
                        "Media da turma: " . $this->alunoAvaliacaoRepository->getMediaTurma($turmaId),
                    ];

                    $linhas_excel[] = [
                        "Nome do aluno",
                        "Mid term",
                        "Final test",
                        "",
                        "Frequência",
                    ];

                    $turmaAnterior = $turmaId;
                }

            //buscar frequencia aluno
            $AlunoID = $alunoAvaliacao["aluno_id"];
            $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($mensagemErro, $AlunoID);
            
            if(count($alunoDiarioORM) > 0) {
                $frequencia = $alunoDiarioORM[0]['frequencia'];
                $frequencia = round($frequencia, 2).'%';
            }else {
                $frequencia = '';
            } 

                $linhas_excel[] = [
                    $alunoAvaliacao["nome_aluno"],
                    $alunoAvaliacao["mid_term"],
                    $alunoAvaliacao["final_test"],
                    "",
                    $frequencia,
                ];
            
            }

            $dadosRelatorioCount = count($dadosRelatorio);
        } else {
            $dadosRelatorioCount = 0;
            foreach ($dadosRelatorio as $alunoAvaliacao) {
                $AlunoID = $alunoAvaliacao["aluno_id"];
                if ($parametros['aluno'] == $AlunoID ) {  
                    $dadosRelatorioCount++;
                    $turmaId = $alunoAvaliacao["turma_id"];
                    if ($turmaAnterior !== $turmaId) {
                        $linhas_excel[] = [
                            "Turma: " . $alunoAvaliacao["turma_descricao"],
                            "Livro: " . $alunoAvaliacao["livro_descricao"],
                            "Professor: " . $alunoAvaliacao["instrutor_apelido"],
                            "Media da turma: " . $this->alunoAvaliacaoRepository->getMediaTurma($turmaId),
                        ];

                        $linhas_excel[] = [
                            "Nome do aluno",
                            "Mid term",
                            "Final test",
                            "",
                            "Frequência",
                        ];

                        $turmaAnterior = $turmaId;
                    }

                        //buscar frequencia aluno
                        $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAluno($mensagemErro, $AlunoID);
                        
                        if(count($alunoDiarioORM) > 0) {
                            $frequencia = $alunoDiarioORM[0]['frequencia'];
                            $frequencia = round($frequencia, 2).'%';
                       }else {
                            $frequencia = '';
                        } 

                    $linhas_excel[] = [
                        $alunoAvaliacao["nome_aluno"],
                        $alunoAvaliacao["mid_term"],
                        $alunoAvaliacao["final_test"],
                        "",
                        $frequencia,
                    ];
                
                }
            }
        }
 
        $linhas_excel[] = [
            "",
            "",
            "",
            "Total de registros: " . $dadosRelatorioCount ,
        ];

        return $linhas_excel;
    }

    /**
     * Gera as linhas do excel pro relatório de notas por aluno
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array  $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioNotasAlunos(&$mensagemErro, $parametros)
    {
        $dadosRelatorio = $this->alunoAvaliacaoRepository->buscarDadosRelatorioNotasAlunosNovo($mensagemErro, $parametros);

        if (empty($mensagem) === false) {
            return false;
        }
 
        $linhas_excel = [];

        $linhas_excel[] = [
            "",
            "",
            "Notas de Alunos",
            "",
            "",
        ];

        $linhas_excel[] = [
            "Nome do aluno",
            "Livro",
            "Mid term",
            "Final test",
            "Professor",
            "Frequência",
            "Faltas",
            "Turma",
            "Situação da Turma",
        ];
        if ($parametros['aluno'] == null) {    
            foreach ($dadosRelatorio as $k => $alunoAvaliacao) {
                $linha = [
                    $alunoAvaliacao["nome_aluno"],
                    $alunoAvaliacao["livro_descricao"],
                    $alunoAvaliacao["mid_term"],
                    $alunoAvaliacao["final_test"],
                ];

                if ($alunoAvaliacao["modalidade_turma"] === SituacoesSistema::MODALIDADE_PERSONAL) {
                    $instrutorORM = $this->contratoFacade->getInstrutorContratoPersonal($alunoAvaliacao["contrato_id"]);
                    if ($instrutorORM === null) {
                        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                            unset($dadosRelatorio[$k]);
                            continue;
                        }

                        $instrutor = '';
                    } else {
                        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                            if ($instrutorORM->getId() !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                                unset($dadosRelatorio[$k]);
                                continue;
                            }
                        }

                        $instrutor = $instrutorORM->getApelido();
                    }
                } else {
                    if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                        if (empty($alunoAvaliacao["instrutor_id"]) === true || (int) $alunoAvaliacao["instrutor_id"] !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                            unset($dadosRelatorio[$k]);
                            continue;
                        }
                    }

                    $instrutor = $alunoAvaliacao["instrutor_apelido"];
                }//end if
                
                //buscar frequencia aluno
                $AlunoID = $alunoAvaliacao["aluno_id"];
                $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAlunoNovo($mensagemErro, $AlunoID, $alunoAvaliacao['turma_id']);

                if(count($alunoDiarioORM) > 0) {
                    $frequencia = $alunoDiarioORM[0]['frequencia'];
                    $frequencia = round($frequencia, 2).'%';
                }else {
                    $frequencia = '';
                }

                $faltas = 0;
                $faltas = round($alunoAvaliacao['faltas'], 0);

                $nomeTurma = '';
                $nomeTurma = $alunoAvaliacao['turma_descricao'];

                $situacaoTurma = '';
                $situacaoTurma = $alunoAvaliacao['situacao'];;


                $linha[]        = $instrutor;
                $linha[]        = $frequencia;
                $linha[]        = $faltas;
                $linha[]        = $nomeTurma;
                $linha[]        = $situacaoTurma;
                
                $linhas_excel[] = $linha;
            }//end foreach

            $pontuacaoTotal  = 0;
            $quantidadeNotas = 0;
            foreach ($dadosRelatorio as $dado) {
                if (is_null($dado["mid_term"]) === false) {
                    $pontuacaoTotal += $dado["mid_term"];
                    $quantidadeNotas++;
                }

                if (is_null($dado["final_test"]) === false) {
                    $pontuacaoTotal += $dado["final_test"];
                    $quantidadeNotas++;
                }
            }

            $totais = [];
            $totais["Total de registros"] = count($dadosRelatorio);
            if ($quantidadeNotas === 0) {
                $totais["Media geral"] = 0;
            } else {
                $totais["Media geral"] = $pontuacaoTotal / $quantidadeNotas;
            }

            foreach ($totais as $k => $v) {
                $linhas_excel[] = [
                    "",
                    "",
                    "",
                    "",
                    "$k: $v",
                ];
            }

        } else {
            //por aluno
            $dadosRelatorioCount = 0;
            foreach ($dadosRelatorio as $k => $alunoAvaliacao) {
                $AlunoID = $alunoAvaliacao["aluno_id"];
                if ($parametros['aluno'] == $AlunoID ) { 
                    $dadosRelatorioCount++;
                     $linha = [
                        $alunoAvaliacao["nome_aluno"],
                        $alunoAvaliacao["livro_descricao"],
                        $alunoAvaliacao["mid_term"],
                        $alunoAvaliacao["final_test"],
                    ];

                    if ($alunoAvaliacao["modalidade_turma"] === SituacoesSistema::MODALIDADE_PERSONAL) {
                        $instrutorORM = $this->contratoFacade->getInstrutorContratoPersonal($alunoAvaliacao["contrato_id"]);
                        if ($instrutorORM === null) {
                            if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                                unset($dadosRelatorio[$k]);
                                continue;
                            }

                            $instrutor = '';
                        } else {
                            if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                                if ($instrutorORM->getId() !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                                    unset($dadosRelatorio[$k]);
                                    continue;
                                }
                            }

                            $instrutor = $instrutorORM->getApelido();
                        }
                    } else {
                        if (isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true && empty($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === false) {
                            if (empty($alunoAvaliacao["instrutor_id"]) === true || (int) $alunoAvaliacao["instrutor_id"] !== (int) $parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) {
                                unset($dadosRelatorio[$k]);
                                continue;
                            }
                        }

                        $instrutor = $alunoAvaliacao["instrutor_apelido"];
                    }//end if
                    
                    //buscar frequencia aluno
                    $alunoDiarioORM = $this->alunoDiarioRepository->buscarDadosRelatorioFrequenciaAlunoNovo($mensagemErro, $AlunoID,$alunoAvaliacao['turma_id']);

                    if(count($alunoDiarioORM) > 0) {
                        $frequencia = $alunoDiarioORM[0]['frequencia'];
                        $frequencia = round($frequencia, 2).'%';
                    }else {
                        $frequencia = '';
                    }
                
                    $faltas = 0;
                    $faltas = round($alunoAvaliacao['faltas'], 0);
    
                    $nomeTurma = '';
                    $nomeTurma = $alunoAvaliacao['turma_descricao'];
    
                    $situacaoTurma = '';
                    $situacaoTurma = $alunoAvaliacao['situacao'];;
    
    
                    $linha[]        = $instrutor;
                    $linha[]        = $frequencia;
                    $linha[]        = $faltas;
                    $linha[]        = $nomeTurma;
                    $linha[]        = $situacaoTurma;
                    
                    $linhas_excel[] = $linha;

                    if (count($dadosRelatorio) == 1){
                        $alunoNotasvaliacaoConceituaORM = $this->alunoAvaliacaoRepository->buscarNotasAlunoAvaliacaoConceitualID($AlunoID,$alunoAvaliacao['turma_id']);
                        $alunoNotasvaliacaoORM = $this->alunoAvaliacaoRepository->buscarNotasAlunoAvaliacaoID($AlunoID,$alunoAvaliacao['turma_id']);
                        
                        // colocar as Lições Aplicadas
                        $licoesAplicadas = $this->licaoRepository->buscarLicoesFaltasPorTurmaIdAlunoID($AlunoID, $alunoAvaliacao['turma_id']);
                        
                        $linhas_excel = [];
                        $linhas_excel[] = [""];
    
                        $linhas_excel[] = [
                            "",
                            "Nome do Aluno:",
                            $alunoAvaliacao["nome_aluno"],
                        ];

                        $linhas_excel[] = [""];

                        $linhas_excel[] = [
                            "",
                            "Turma:",
                            $alunoAvaliacao["turma_descricao"],
                        ];
     
                        $linhas_excel[] = [
                            "",
                            "Livro:",
                            $alunoAvaliacao["livro_descricao"],
                        ];
                        $linhas_excel[] = [
                            "",
                            "Professor:",
                            $alunoAvaliacao["instrutor_apelido"],
                        ];
                        $linhas_excel[] = [
                            "",
                            "Data Inicio:",
                            $alunoAvaliacao["turma_data_inicio"],
                        ];
                        $linhas_excel[] = [
                            "",
                            "Data Fim:",
                            $alunoAvaliacao["turma_data_fim"],
                        ];
                        $linhas_excel[] = [""];
                        $linhas_excel[] = [""];
                        $linhas_excel[] = [
                            "",
                            "Frequência:",
                            $frequencia ,
                        ];
                        $linhas_excel[] = [
                            "",
                            "Faltas:",
                            $faltas ,
                        ];

                        foreach ($licoesAplicadas as $k => $turmaLicaoAplicada) {
                            $linhas_excel[] = [
                                "",
                                "",
                                $turmaLicaoAplicada['descricao'],
                            ];
                        } 
                        
                        if($alunoNotasvaliacaoConceituaORM != null) {
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [
                                "",
                                "1A. AVALIAÇÃO (LISTENING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_listening_1_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "1A. AVALIAÇÃO (SPEAKING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_speaking_1_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "1A. AVALIAÇÃO (WRITING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_writing_1_descricao'],
                            ];
                        }

                        if($alunoNotasvaliacaoORM != null) {
                            $linhas_excel[] = [
                                "",
                                "Mid-Term (OG):",
                                $alunoNotasvaliacaoORM[0]['nota_mid_term_oral_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Mid-Term (T):",
                                $alunoNotasvaliacaoORM[0]['mid_term_t'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Mid-Term (C):",
                                $alunoNotasvaliacaoORM[0]['mid_term_c'],
                            ];
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [
                                "",
                                "Mid-Term (WG):",
                                $alunoNotasvaliacaoORM[0]['mid_term_wg'],
                            ];
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [
                                "",
                                "Retake (OG):",
                                $alunoNotasvaliacaoORM[0]['nota_retake_mid_term_oral_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Retake (WG):",
                                $alunoNotasvaliacaoORM[0]['mid_term_retake_wg'],
                            ];
                        }

                        if($alunoNotasvaliacaoConceituaORM != null) {
                            $linhas_excel[] = [
                                "",
                                "2A. AVALIAÇÃO (LISTENING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_listening_2_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "2A. AVALIAÇÃO (SPEAKING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_speaking_2_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "2A. AVALIAÇÃO (WRITING):",
                                $alunoNotasvaliacaoConceituaORM[0]['nota_writing_2_descricao'],
                            ];
                        }

                        if($alunoNotasvaliacaoORM != null) {
                            $linhas_excel[] = [
                                "",
                                "Final Test (OG):",
                                $alunoNotasvaliacaoORM[0]['nota_final_oral_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Final Test (T):",
                                $alunoNotasvaliacaoORM[0]['final_test_t'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Final Test (C):",
                                $alunoNotasvaliacaoORM[0]['final_test_c'],
                            ];
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [
                                "",
                                "Final Test (WG):",
                                $alunoNotasvaliacaoORM[0]['final_test_wg'],
                            ];
                            $linhas_excel[] = [""];
                            $linhas_excel[] = [
                                "",
                                "Retake (OG):",
                                $alunoNotasvaliacaoORM[0]['nota_retake_final_oral_descricao'],
                            ];
                            $linhas_excel[] = [
                                "",
                                "Retake (WG):",
                                $alunoNotasvaliacaoORM[0]['final_test_retake_wg'],
                            ];
                        }

                        return $linhas_excel;
                    }
                
            }

            }//end foreach

            $pontuacaoTotal  = 0;
            $quantidadeNotas = 0;
            foreach ($dadosRelatorio as $dado) {
                if ($dado['aluno_id'] == $parametros['aluno'] ) { 
                     if (is_null($dado["mid_term"]) === false) {
                        $pontuacaoTotal += $dado["mid_term"];
                        $quantidadeNotas++;
                    }

                    if (is_null($dado["final_test"]) === false) {
                         $pontuacaoTotal += $dado["final_test"];
                        $quantidadeNotas++;
                    }
                 }
            }

            $totais = [];
            $totais["Total de registros"] = $dadosRelatorioCount;
            if ($quantidadeNotas === 0) {
                $totais["Media geral"] = 0;
            } else {
                $totais["Media geral"] = $pontuacaoTotal / $quantidadeNotas;
            }
            foreach ($totais as $k => $v) {
                $linhas_excel[] = [
                    "",
                    "",
                    "",
                    "",
                    "$k: $v",
                ];
            }

        }

        return $linhas_excel;
    }


}
