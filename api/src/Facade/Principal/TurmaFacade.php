<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\Turma;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\TurmaBO;
use App\Entity\Principal\Livro;
use App\Entity\Principal\Horario;
use App\Entity\Principal\Franqueada;
use App\Entity\Principal\Licao;
use App\Entity\Principal\TurmaAula;
use App\Helper\FunctionHelper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Marcelo André Naegeler
 */
class TurmaFacade extends GenericFacade
{

    /**
     *
     * @var \App\BO\Principal\TurmaBO $turmaBO
     */
    private $turmaBO;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository $turmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Facade\Principal\ContratoFacade $contratoFacade
     */
    private $contratoFacade;


    /**
     * Remove todas as turmas aulas da turma passada
     *
     * @param \App\Entity\Principal\Turma $turmaORM
     * @param string $mensagemErro
     *
     * @return bool
     */
    private function removeTurmaAulasTurma(&$turmaORM, $mensagemErro)
    {
        $possuiAlunoDiario = false;
        if ($turmaORM->getTurmaAulas()->count() > 0) {
            $turmaAulasDaTurma    = $turmaORM->getTurmaAulas();
            $quantidadeTurmaAulas = $turmaORM->getTurmaAulas()->count();
            for ($i = 0; $i < $quantidadeTurmaAulas; $i++) {
                $turmaAulaORM = $turmaAulasDaTurma->get($i);
                if ($turmaAulaORM->getAlunoDiarios()->count() > 0) {
                    $possuiAlunoDiario = true;
                    break;
                }

                self::removerSeguro($turmaAulaORM, $mensagemErro);
                $turmaORM->removeTurmaAula($turmaAulaORM);
            }
        }

        return $possuiAlunoDiario;
    }


    /**
     * Encerra a turma passada
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Turma $turmaORM
     *
     * @return bool
     */
    public function encerrar($mensagemErro, &$turmaORM)
    {
        foreach ($turmaORM->getContratos() as $contratoORM) {
            $this->contratoFacade->encerrar($mensagemErro, $contratoORM);
        }

        $turmaORM->setSituacao(SituacoesSistema::SITUACAO_TURMA_ENCERRADA);
        return empty($mensagemErro);
    }

    /**
     * Realiza a criação de turmaAula por livro, caso a turma já possua turmaAulas, ele irá remover para criar novos
     *
     * @param array $turmaAulas
     * @param \App\Entity\Principal\Turma $turmaORM
     * @param string $mensagemErro
     *
     * @return bool
     */
    private function preparaTurmaAulasPorLista($turmaAulas, $turmaORM, &$mensagemErro)
    {
        if ($turmaORM->getSituacao() === SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO) {
            $ehUpdate = isset($turmaAulas[0]['id']) === true && is_null($turmaAulas[0]['id']) === false;
            if ($ehUpdate === false) {
                $this->removeTurmaAulasTurma($turmaORM, $mensagemErro);
            }

            $licaosLivro       = $turmaORM->getLivro()->getPlanejamentoLicao()->getLicaos();
            $idsLicaoFaltantes = [];
            foreach ($licaosLivro as $licao) {
                $idsLicaoFaltantes[] = $licao->getId();
            }

            $franqueadaORM = $turmaORM->getFranqueada();
            foreach ($turmaAulas as $k => $aula) {
                FunctionHelper::formataCampoDateTimeJS($aula["data_aula"], $dataAula);
                // Setando pra meio-dia pra não ter problema de transformar 0:00 de um dia pra 21:00 do dia anterior entre o PHP e o JS
                $dataAula->setTime(12, 0);
                if (isset($aula["id"]) === false || is_null($aula["id"]) === true) {
                    $licaoId  = $aula["licao"]["id"];
                    $licaoORM = $this->licaoRepository->find($licaoId);

                    $parametrosTurmaAula = [
                        ConstanteParametros::CHAVE_FRANQUEADA  => $franqueadaORM,
                        ConstanteParametros::CHAVE_LICAO       => $licaoORM,
                        ConstanteParametros::CHAVE_TURMA       => $turmaORM,
                        ConstanteParametros::CHAVE_DATA_AULA   => $dataAula,
                        ConstanteParametros::CHAVE_FUNCIONARIO => $turmaORM->getFuncionario(),
                    ];
                    $turmaAulaORM        = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TurmaAula::class, true, $parametrosTurmaAula);
                } else {
                    $turmaAulaORM = $this->turmaAulaRepository->find($aula["id"]);
                    $turmaAulaORM->setDataAula($dataAula);
                }

                $keyIdsLicao = array_search($turmaAulaORM->getLicao()->getId(), $idsLicaoFaltantes);
                if ($keyIdsLicao !== false) {
                    unset($idsLicaoFaltantes[$keyIdsLicao]);
                }

                self::persistSeguro($turmaAulaORM, $mensagemErro);
            }//end foreach

            // Busca o ID de todas as lições das aulas que estão gravadas
            $turmaAulasORM = $turmaORM->getTurmaAulas();
            $licaosSalvas  = [];
            foreach ($turmaAulasORM as $aula) {
                $licaosSalvas[] = $aula->getLicao()->getId();
            }

            // Depois de inserir tudo que veio do front-end, insere as licoes que não vieram, a princípio aulas do portal
            foreach ($idsLicaoFaltantes as $licao) {
                // Se é update, deve ser checado se a lição já existe no back
                if ($ehUpdate === true) {
                    if (in_array($licao, $licaosSalvas) === true) {
                        continue;
                    }
                }

                $licaoORM = $this->licaoRepository->find($licao);
                // Aulas do portal não possuem funcionário e nem data_aula!
                $dataNula = new DateTime('0000-00-00');
                $dataNula->setTime(0, 0, 0, 0);
                $parametrosTurmaAula = [
                    ConstanteParametros::CHAVE_FRANQUEADA  => $franqueadaORM,
                    ConstanteParametros::CHAVE_LICAO       => $licaoORM,
                    ConstanteParametros::CHAVE_TURMA       => $turmaORM,
                    ConstanteParametros::CHAVE_DATA_AULA   => $dataNula,
                    ConstanteParametros::CHAVE_FUNCIONARIO => null,
                ];
                $turmaAulaORM        = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TurmaAula::class, true, $parametrosTurmaAula);
                self::persistSeguro($turmaAulaORM, $mensagemErro);
            }//end foreach
        }//end if

        return empty($mensagemErro) === true;

    }

    /**
     * Realiza a criação de turmaAula por livro, caso a turma já possua turmaAulas, ele irá remover para criar novos
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Livro $livroORM
     * @param \App\Entity\Principal\Turma $turmaORM
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     *
     * @return bool
     */
    private function preparaTurmaAulasPorLivro(&$mensagemErro, $livroORM, $turmaORM, $franqueadaORM, &$turmaAulas=[])
    {
        if ($this->removeTurmaAulasTurma($turmaORM, $mensagemErro) === true) {
            return true;
        }

        $datasLicoes = [];
        $licoesDoPlanejamentoLicao = $livroORM->getPlanejamentoLicao()->getLicaos();
        $licoesValidasAulas        = new ArrayCollection();

        foreach ($licoesDoPlanejamentoLicao as $licao) {
            $ehModalidadeAoVivo = $licao->getModalidade() === 'V';
            $estaAtiva          = $licao->getSituacao() === 'A';
            if ($ehModalidadeAoVivo === true && $estaAtiva === true) {
                $licoesValidasAulas[] = $licao;
            }
        }

        if (count($licoesValidasAulas) === 0) {
            foreach ($licoesDoPlanejamentoLicao as $licao) {
                $estaAtiva = $licao->getSituacao() === 'A';
                if ($estaAtiva === true) {
                    $licoesValidasAulas[] = $licao;
                }
            }
        }

        $numeroDeAulasLivro = $licoesValidasAulas->count();
        $quantidadeDeLicoes = $licoesValidasAulas->count();
        $parametrosCalculo  = [
            ConstanteParametros::CHAVE_LIVRO       => $livroORM->getId(),
            ConstanteParametros::CHAVE_HORARIO     => $turmaORM->getHorario()->getId(),
            ConstanteParametros::CHAVE_DATA_INICIO => $turmaORM->getDataInicio()->format("Y-m-d\TH:i:s.uP"),
        ];
        if ($this->turmaBO->calcularDataTermino($mensagemErro, $parametrosCalculo, $datasLicoes) === null) {
            $mensagemErro = "Não foi possivel realizar o calculo de término da turma. Possivel erro de parametros.";
            return false;
        }

        for ($i = 0; $i < $numeroDeAulasLivro; $i++) {
            $licaoORM            = $licoesValidasAulas->get($i);
            $dataAula            = $datasLicoes[$i];
            $parametrosTurmaAula = [
                ConstanteParametros::CHAVE_FRANQUEADA  => $franqueadaORM,
                ConstanteParametros::CHAVE_LICAO       => $licaoORM,
                ConstanteParametros::CHAVE_TURMA       => $turmaORM,
                ConstanteParametros::CHAVE_DATA_AULA   => $dataAula,
                ConstanteParametros::CHAVE_FUNCIONARIO => $turmaORM->getFuncionario(),
            ];
            $turmaAulaORM        = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TurmaAula::class, true, $parametrosTurmaAula);
            self::persistSeguro($turmaAulaORM, $mensagemErro);
            $turmaAulas[] = $turmaAulaORM;
            if (($i === ($quantidadeDeLicoes - 1)) || (empty($mensagemErro) === false)) {
                break;
            }
        }

        return empty($mensagemErro) === true;

    }

    /**
     * Realiza a exclusao da turma
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Turma $turmaORM
     *
     * @return bool
     */
    private function excluiTurma(&$mensagemErro, $turmaORM)
    {

        $turmaORM->setExcluido(true);
        self::persistSeguro($turmaORM, $mensagemErro);

        return empty($mensagemErro) === true;

    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->turmaBO         = new TurmaBO(self::getEntityManager());
        $this->licaoRepository = self::getEntityManager()->getRepository(Licao::class);
        $this->turmaAulaRepository  = self::getEntityManager()->getRepository(TurmaAula::class);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(Franqueada::class);
        $this->turmaRepository      = self::getEntityManager()->getRepository(Turma::class);
        $this->contratoFacade       = new ContratoFacade(self::getManagerRegistry());
        $this->livroRepository      = self::getEntityManager()->getRepository(Livro::class);
        $this->horarioRepository    = self::getEntityManager()->getRepository(Horario::class);
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
        $lista = $this->turmaRepository->listar($parametros);

        return [
            ConstanteParametros::CHAVE_TOTAL => $lista->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $lista->getItems(),
        ];
    }

    /**
     * Buscar dados com turma turmaaula
     *
     * @param string $mensagemErro
     * @param int $id
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function buscarDadosTurmaTurmaAulaPorId(&$mensagemErro, $id, $parametros)
    {
        $item = $this->turmaRepository->buscarDadosTurmaTurmaAulaPorId($id, $parametros);

        if (is_null($item) === true) {
            $mensagemErro = 'Objeto não encontrado';
        }

        return $item;
    }

    /**
     * Buscar as turmas abertas por livro
     *
     * @param array $parametros
     *
     * @return array
     */
    public function buscarTurmas($parametros)
    {
        $lista = $this->turmaRepository->buscarTurmas($parametros);
        if (isset($parametros['buscar_todas']) === true && $parametros['buscar_todas'] === '1') {
            $total = count($lista);
        } else {
            $total = $lista->getTotalItemCount();
            $lista = $lista->getItems();
        }

        foreach ($lista as $k => $turma) {
            $quantidadeAlunosVigentes = $this->contratoFacade->quantidadeContratosVigentesTurma($turma["turmaId"]);
            $lista[$k]["quantidadeAlunosVigentes"] = $quantidadeAlunosVigentes;
        }

        return [
            ConstanteParametros::CHAVE_TOTAL => $total,
            ConstanteParametros::CHAVE_ITENS => $lista,
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
        $item = $this->turmaRepository->buscarPorId($id);

        if (is_null($item) === true) {
            $mensagemErro = 'Objeto não encontrado';
        }

        $item["possuiTurmaAulaFinalizada"] = $this->possuiTurmaAulaFinalizada($mensagemErro, $id);

        if (empty($mensagemErro) === false) {
            return null;
        }

        return $item;
    }

    /**
     * Busca se a turma passada possui alguma aula finalizada (diário lançado)
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return null|boolean
     */
    private function possuiTurmaAulaFinalizada(&$mensagemErro, $id)
    {
        $turmaORM = $this->turmaRepository->find($id);
        if (is_null($turmaORM) === true) {
            $mensagemErro = 'Turma não encontrada';
            return null;
        };

        $possuiTurmaAulaFinalizada = false;
        foreach ($turmaORM->getTurmaAulas() as $turmaAulaORM) {
            if ($turmaAulaORM->getFinalizada() === true) {
                $possuiTurmaAulaFinalizada = true;
                break;
            }
        }

        return $possuiTurmaAulaFinalizada;
    }



    /**
     * Busca os alunos da turma pela chave primaria da turma
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $idTurma Chave primaria do registro
     * @param array $parametros
     *
     * @return array|Object
     */
    public function buscarAlunosTurmaPorId(&$mensagemErro, $idTurma, $parametros)
    {
        $item = $this->turmaRepository->buscarAlunosTurmaPorId($idTurma, $parametros);

        if (is_null($item) === true) {
            $mensagemErro = 'Objeto não encontrado';
        }

        return $item;
    }


    /**
     * Busca turmas conforme descrição passada e franqueada selecionada
     *
     * @param string $descricao
     *
     * @return \App\Entity\Principal\Turma[]
     */
    public function buscarPorDescricao ($descricao)
    {
        return $this->turmaRepository->buscarPorDescricao($descricao);
    }

    /**
     * Cálculo de data de término
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return string
     */
    public function calcularDataTermino (&$mensagemErro, $parametros)
    {
        return $this->turmaBO->calcularDataTermino($mensagemErro, $parametros);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Turma
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $turma = null;
        if ($this->turmaBO->verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ($this->turmaBO->podeCriar($parametros, $mensagemErro) === true) {
                $turma = \App\Factory\GeneralORMFactory::criar(Turma::class, true, $parametros);
                self::persistSeguro($turma, $mensagemErro);
                if (isset($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS]) === true && count($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS]) > 0) {
                    $prepararAulas = $this->preparaTurmaAulasPorLista($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS], $turma, $mensagemErro);
                    if ($prepararAulas === true) {
                        self::flushSeguro($mensagemErro);
                    }
                } else if ($this->preparaTurmaAulasPorLivro($mensagemErro, $turma->getLivro(), $turma, $turma->getFranqueada()) === true) {
                    self::flushSeguro($mensagemErro);
                }
            }
        }

        return $turma;
    }

    /**
     * Gera as turma aulas de acordo com as informações da turma, sem salvar nenhum deles
     *
     * @param string $mensagemErro
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Turma
     */
    public function gerarTurmaAula(&$mensagemErro, $parametros=[])
    {
        $turma      = null;
        $turmaAulas = [];
        if ($this->turmaBO->verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ($this->turmaBO->podeCriar($parametros, $mensagemErro) === true) {
                $turma = \App\Factory\GeneralORMFactory::criar(Turma::class, true, $parametros);
                self::persistSeguro($turma, $mensagemErro);
                $this->preparaTurmaAulasPorLivro($mensagemErro, $turma->getLivro(), $turma, $turma->getFranqueada(), $turmaAulas);
                // Não faz o flush, apenas gera as turma_aula de acordo com o que foi passado de informações
            }
        }

        return $turmaAulas;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $turma = $this->turmaRepository->find($id);
        if (is_null($turma) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
        } else {
            $estavaEncerrada = $turma->getSituacao() === SituacoesSistema::SITUACAO_TURMA_ENCERRADA;
            if ($this->turmaBO->podeCriar($parametros, $mensagemErro, $id) === true) {
                self::getFctHelper()->setParams($parametros, $turma);
                if ($estavaEncerrada === false && $parametros[ConstanteParametros::CHAVE_SITUACAO] === SituacoesSistema::SITUACAO_TURMA_ENCERRADA) {
                    $this->encerrar($mensagemErro, $turma);
                }

                if (isset($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS]) === true && count($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS]) > 0) {
                    $prepararAulas = $this->preparaTurmaAulasPorLista($parametros[ConstanteParametros::CHAVE_LISTA_TURMA_AULAS], $turma, $mensagemErro);
                    if ($prepararAulas === true) {
                        self::flushSeguro($mensagemErro);
                    }
                } else if ($this->preparaTurmaAulasPorLivro($mensagemErro, $turma->getLivro(), $turma, $turma->getFranqueada()) === true) {
                    self::flushSeguro($mensagemErro);
                }
            }
        }

        return $turma;
    }

       /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizarTurma(&$mensagemErro, $id, $parametros=[])
    {
        $turma = $this->turmaRepository->find($id);
        if (is_null($turma) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
        } else {
            $estavaEncerrada = $turma->getSituacao() === SituacoesSistema::SITUACAO_TURMA_ENCERRADA;
            if ($this->turmaBO->podeCriar($parametros, $mensagemErro, $id) === true) {

                $parametros['franqueada'] = $turma->getFranqueada();

                self::getFctHelper()->setParams($parametros, $turma);
                if ($estavaEncerrada === false && $parametros[ConstanteParametros::CHAVE_SITUACAO] === SituacoesSistema::SITUACAO_TURMA_ENCERRADA) {
                    $this->encerrar($mensagemErro, $turma);
                }

                   $turmaAulas = [];
                  $this->preparaTurmaAulasPorLivro($mensagemErro, $turma->getLivro(), $turma, $turma->getFranqueada(), $turmaAulas);
               
            }
        }
 
        return $turmaAulas;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function excluir(&$mensagemErro, $id)
    {
        $turma = $this->turmaRepository->find($id);
        if (is_null($turma) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
        } else {
            if ($this->turmaBO->podeExcluir($mensagemErro, $id, $turma) === true) {
                if ($this->excluiTurma($mensagemErro, $turma) === true) {
                    self::flushSeguro($mensagemErro);
                }
            }
        }

        return $turma;
    }

    /**
     * Checa se é possivel excluir uma turma
     *
     * @param string $mensagemErro
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function podeExcluir(&$mensagemErro, $id)
    {
        $turma = $this->turmaRepository->find($id);
        if (is_null($turma) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
            return false;
        }

        return $this->turmaBO->podeExcluir($mensagemErro, $id, $turma);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     * @param boolean $bRemover remover registro ou não
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id, $bRemover=true)
    {
        $turma = $this->turmaRepository->find($id);
        if (is_null($turma) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
        } else {
            if ($bRemover === true) {
                $turma->setExcluido(true);
            } else {
                $turma->setExcluido(false);
            }

            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Busca as turma que devem aparecer nos Selects do filtro de turmas
     * 
     * 
     */
    public function buscarTurmasSelectOptions($parametros)
    {   
        return $this->turmaRepository->buscarTurmasSelectOptions($parametros);
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
        return $this->turmaRepository->prepararDadosRelatorio($parametros);
    }

    public function gerarDadosRelatorioTurmasExistente($filtros)
    {
        return $this->turmaRepository->buscarDadosRelatorioTurmasExistentes($filtros);
    }

    public function gerarDadosRelatorioAlunosPorTurma($filtros)
    {
        return $this->turmaRepository->buscarDadosRelatorioAlunosPorTurma($filtros);
    }

    public function gerarDadosRelatorioMapaSalaTurma($filtros)
    {
        return $this->turmaRepository->buscarDadosRelatorioMapaSalaTurma($filtros);
    }
}
