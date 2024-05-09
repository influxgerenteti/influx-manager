<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\ReposicaoAulaBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

/**
 *
 * @author Luiz A Costa
 */
class ReposicaoAulaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\ReposicaoAulaRepository
     */
    private $reposicaoAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoAvaliacaoRepository
     */
    private $alunoAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\ConceitoAvaliacaoRepository
     */
    private $conceitoAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaAulaRepository
     */
    private $turmaAulaRepository;

    /**
     *
     * @var \App\BO\Principal\ReposicaoAulaBO
     */
    private $reposicaoAulaBO;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoDiarioFacade
     */
    private $alunoDiarioFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoFacade
     */
    private $alunoAvaliacaoFacade;

    /**
     * Configura notas finais antes e depois para historico
     *
     * @param \App\Entity\Principal\HistoricoReposicaoAula $historicoReposicaoAulaORM
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     */
    private function configuraNotaFinalAnteriorAtual(&$historicoReposicaoAulaORM, $alunoAvaliacaoORM, $reposicaoAulaORM)
    {
        $historicoReposicaoAulaORM->setFinalCompositionAnterior($alunoAvaliacaoORM->getNotaFinalComposition());
        $historicoReposicaoAulaORM->setFinalEscritaAnterior($alunoAvaliacaoORM->getNotaFinalEscrita());
        $historicoReposicaoAulaORM->setFinalOralAnterior($alunoAvaliacaoORM->getNotaFinalOral());
        $historicoReposicaoAulaORM->setFinalTestAnterior($alunoAvaliacaoORM->getNotaFinalTest());
        $historicoReposicaoAulaORM->setFinalCompositionAtual($reposicaoAulaORM->getNotaFinalComposition());
        $historicoReposicaoAulaORM->setFinalEscritaAtual($reposicaoAulaORM->getNotaFinalEscrita());
        $historicoReposicaoAulaORM->setFinalOralAtual($reposicaoAulaORM->getNotaFinalOral());
        $historicoReposicaoAulaORM->setFinalTestAtual($reposicaoAulaORM->getNotaFinalTest());
    }

    /**
     * Configura notas mid-term antes e depois para historico
     *
     * @param \App\Entity\Principal\HistoricoReposicaoAula $historicoReposicaoAulaORM
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     */
    private function configuraNotaMidTermAnteriorAtual(&$historicoReposicaoAulaORM, $alunoAvaliacaoORM, $reposicaoAulaORM)
    {
        $historicoReposicaoAulaORM->setMidTermCompositionAnterior($alunoAvaliacaoORM->getNotaMidTermComposition());
        $historicoReposicaoAulaORM->setMidTermEscritaAnterior($alunoAvaliacaoORM->getNotaMidTermEscrita());
        $historicoReposicaoAulaORM->setMidTermOralAnterior($alunoAvaliacaoORM->getNotaMidTermOral());
        $historicoReposicaoAulaORM->setMidTermTestAnterior($alunoAvaliacaoORM->getNotaMidTermTest());
        $historicoReposicaoAulaORM->setMidTermCompositionAtual($reposicaoAulaORM->getNotaMidTermComposition());
        $historicoReposicaoAulaORM->setMidTermEscritaAtual($reposicaoAulaORM->getNotaMidTermEscrita());
        $historicoReposicaoAulaORM->setMidTermOralAtual($reposicaoAulaORM->getNotaMidTermOral());
        $historicoReposicaoAulaORM->setMidTermTestAtual($reposicaoAulaORM->getNotaMidTermTest());
    }

    /**
     * Configura notas finais antes e depois para historico
     *
     * @param \App\Entity\Principal\HistoricoReposicaoAula $historicoReposicaoAulaORM
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     */
    private function configuraNotaRetakeFinalAnteriorAtual(&$historicoReposicaoAulaORM, $alunoAvaliacaoORM, $reposicaoAulaORM)
    {
        $historicoReposicaoAulaORM->setRetakeFinalCompositionAnterior($alunoAvaliacaoORM->getNotaRetakeFinalComposition());
        $historicoReposicaoAulaORM->setRetakeFinalEscritaAnterior($alunoAvaliacaoORM->getNotaRetakeFinalEscrita());
        $historicoReposicaoAulaORM->setRetakeFinalOralAnterior($alunoAvaliacaoORM->getNotaRetakeFinalOral());
        $historicoReposicaoAulaORM->setRetakeFinalTestAnterior($alunoAvaliacaoORM->getNotaRetakeFinalTest());
        $historicoReposicaoAulaORM->setRetakeFinalCompositionAtual($reposicaoAulaORM->getNotaRetakeFinalComposition());
        $historicoReposicaoAulaORM->setRetakeFinalEscritaAtual($reposicaoAulaORM->getNotaRetakeFinalEscrita());
        $historicoReposicaoAulaORM->setRetakeFinalOralAtual($reposicaoAulaORM->getNotaRetakeFinalOral());
        $historicoReposicaoAulaORM->setRetakeFinalTestAtual($reposicaoAulaORM->getNotaRetakeFinalTest());
    }

    /**
     * Configura notas mid-term antes e depois para historico
     *
     * @param \App\Entity\Principal\HistoricoReposicaoAula $historicoReposicaoAulaORM
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     */
    private function configuraNotaRetakeMidTermAnteriorAtual(&$historicoReposicaoAulaORM, $alunoAvaliacaoORM, $reposicaoAulaORM)
    {
        $historicoReposicaoAulaORM->setRetakeMidTermCompositionAnterior($alunoAvaliacaoORM->getNotaRetakeMidTermComposition());
        $historicoReposicaoAulaORM->setRetakeMidTermEscritaAnterior($alunoAvaliacaoORM->getNotaRetakeMidTermEscrita());
        $historicoReposicaoAulaORM->setRetakeMidTermOralAnterior($alunoAvaliacaoORM->getNotaRetakeMidTermOral());
        $historicoReposicaoAulaORM->setRetakeMidTermTestAnterior($alunoAvaliacaoORM->getNotaRetakeMidTermTest());
        $historicoReposicaoAulaORM->setRetakeMidTermCompositionAtual($reposicaoAulaORM->getNotaRetakeMidTermComposition());
        $historicoReposicaoAulaORM->setRetakeMidTermEscritaAtual($reposicaoAulaORM->getNotaRetakeMidTermEscrita());
        $historicoReposicaoAulaORM->setRetakeMidTermOralAtual($reposicaoAulaORM->getNotaRetakeMidTermOral());
        $historicoReposicaoAulaORM->setRetakeMidTermTestAtual($reposicaoAulaORM->getNotaRetakeMidTermTest());
    }

    /**
     * Cria o Historico de Reposição e persiste em memoria
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     *
     * @return boolean
     */
    private function criarHistoricoReposicaoAula(&$mensagemErro, &$objetoORM)
    {
        $historicoReposicaoAulaORM = new \App\Entity\Principal\HistoricoReposicaoAula();

        $this->configuraNotaFinalAnteriorAtual($historicoReposicaoAulaORM, $objetoORM->getAlunoAvaliacao(), $objetoORM);
        $this->configuraNotaMidTermAnteriorAtual($historicoReposicaoAulaORM, $objetoORM->getAlunoAvaliacao(), $objetoORM);
        $this->configuraNotaRetakeFinalAnteriorAtual($historicoReposicaoAulaORM, $objetoORM->getAlunoAvaliacao(), $objetoORM);
        $this->configuraNotaRetakeMidTermAnteriorAtual($historicoReposicaoAulaORM, $objetoORM->getAlunoAvaliacao(), $objetoORM);
        $historicoReposicaoAulaORM->setReposicaoAula($objetoORM);
        self::persistSeguro($historicoReposicaoAulaORM, $mensagemErro);

        return (empty($mensagemErro) === true);
    }

    /**
     * Configura as notas a serem aplicadas no alunoAvaliacao
     *
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     */
    private function configuraNotasParaAlunoAvaliacao(&$alunoAvaliacaoORM, &$objetoORM)
    {
        $tipoAvaliacao = $objetoORM->getItem()->getTipoItem()->getTipo();
        if ($tipoAvaliacao === SituacoesSistema::TIPO_AVALIACAO_MID_TERM) {
            $this->configurarNotasMidTerm($alunoAvaliacaoORM, $objetoORM);
        } else if ($tipoAvaliacao === SituacoesSistema::TIPO_AVALIACAO_FINAL) {
            $this->configurarNotasFinal($alunoAvaliacaoORM, $objetoORM);
        } else if ($tipoAvaliacao === SituacoesSistema::TIPO_AVALIACAO_RETAKE_MID_TERM) {
            $this->configurarNotasRetakeMidTerm($alunoAvaliacaoORM, $objetoORM);
        } else if ($tipoAvaliacao === SituacoesSistema::TIPO_AVALIACAO_RETAKE_FINAL) {
            $this->configurarNotasRetakeFinal($alunoAvaliacaoORM, $objetoORM);
        }
    }

    /**
     * Configura campos de notas makeup midTerm
     *
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     */
    private function configurarNotasMidTerm(&$alunoAvaliacaoORM, &$objetoORM)
    {
        if (is_null($objetoORM->getNotaMidTermOral()) === false) {
            if ((is_null($alunoAvaliacaoORM->getNotaMidTermOral()) === true) || ($objetoORM->getNotaMidTermOral()->getValor() > $alunoAvaliacaoORM->getNotaMidTermOral()->getValor())) {
                $alunoAvaliacaoORM->setNotaMidTermOral($objetoORM->getNotaMidTermOral());
            }
        }

        if ($objetoORM->getNotaMidTermTest() > $alunoAvaliacaoORM->getNotaMidTermTest()) {
            $alunoAvaliacaoORM->setNotaMidTermTest($objetoORM->getNotaMidTermTest());
        }

        if ($objetoORM->getNotaMidTermComposition() > $alunoAvaliacaoORM->getNotaMidTermComposition()) {
            $alunoAvaliacaoORM->setNotaMidTermComposition($objetoORM->getNotaMidTermComposition());
        }

        $alunoAvaliacaoORM->setNotaMidTermEscrita($alunoAvaliacaoORM->getNotaMidTermComposition() + $alunoAvaliacaoORM->getNotaMidTermTest());
    }

    /**
     * Configura campos de notas makeup final
     *
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     */
    private function configurarNotasFinal(&$alunoAvaliacaoORM, &$objetoORM)
    {
        if (is_null($objetoORM->getNotaFinalOral()) === false) {
            if ((is_null($alunoAvaliacaoORM->getNotaFinalOral()) === true) || ($objetoORM->getNotaFinalOral()->getValor() > $alunoAvaliacaoORM->getNotaFinalOral()->getValor())) {
                $alunoAvaliacaoORM->setNotaMidTermOral($objetoORM->getNotaFinalOral());
            }
        }

        if ($objetoORM->getNotaFinalTest() > $alunoAvaliacaoORM->getNotaFinalTest()) {
            $alunoAvaliacaoORM->setNotaFinalTest($objetoORM->getNotaFinalTest());
        }

        if ($objetoORM->getNotaFinalComposition() > $alunoAvaliacaoORM->getNotaFinalComposition()) {
            $alunoAvaliacaoORM->setNotaFinalComposition($objetoORM->getNotaFinalTest());
        }

        $alunoAvaliacaoORM->setNotaFinalEscrita($alunoAvaliacaoORM->getNotaFinalComposition() + $alunoAvaliacaoORM->getNotaFinalTest());
    }

    /**
     * Configura campos de notas retake midTerm
     *
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     */
    private function configurarNotasRetakeMidTerm(&$alunoAvaliacaoORM, &$objetoORM)
    {
        if (is_null($objetoORM->getNotaRetakeMidTermOral()) === false) {
            if ((is_null($alunoAvaliacaoORM->getNotaRetakeMidTermOral()) === true) || ($objetoORM->getNotaRetakeMidTermOral()->getValor() > $alunoAvaliacaoORM->getNotaRetakeMidTermOral()->getValor())) {
                $alunoAvaliacaoORM->setNotaRetakeMidTermOral($objetoORM->getNotaRetakeMidTermOral());
            }
        }

        if ($objetoORM->getNotaRetakeMidTermTest() > $alunoAvaliacaoORM->getNotaRetakeMidTermTest()) {
            $alunoAvaliacaoORM->setNotaRetakeMidTermTest($objetoORM->getNotaRetakeMidTermTest());
        }

        if ($objetoORM->getNotaRetakeMidTermComposition() > $alunoAvaliacaoORM->getNotaRetakeMidTermComposition()) {
            $alunoAvaliacaoORM->setNotaRetakeMidTermComposition($objetoORM->getNotaRetakeMidTermComposition());
        }

        if ($objetoORM->getNotaRetakeMidTermEscrita() > $alunoAvaliacaoORM->getNotaRetakeMidTermEscrita()) {
            $alunoAvaliacaoORM->setNotaRetakeMidTermEscrita($objetoORM->getNotaRetakeMidTermEscrita());
        }
    }

    /**
     * Configura campos de notas retake final
     *
     * @param \App\Entity\Principal\AlunoAvaliacao $alunoAvaliacaoORM
     * @param \App\Entity\Principal\ReposicaoAula $objetoORM
     */
    private function configurarNotasRetakeFinal(&$alunoAvaliacaoORM, &$objetoORM)
    {
        if (is_null($objetoORM->getNotaRetakeFinalOral()) === false) {
            if ((is_null($alunoAvaliacaoORM->getNotaRetakeFinalOral()) === true) || ($objetoORM->getNotaRetakeFinalOral()->getValor() > $alunoAvaliacaoORM->getNotaRetakeFinalOral()->getValor())) {
                $alunoAvaliacaoORM->setNotaRetakeFinalOral($objetoORM->getNotaRetakeFinalOral());
            }
        }

        if ($objetoORM->getNotaRetakeFinalTest() > $alunoAvaliacaoORM->getNotaRetakeFinalTest()) {
            $alunoAvaliacaoORM->setNotaRetakeFinalTest($objetoORM->getNotaRetakeFinalTest());
        }

        if ($objetoORM->getNotaRetakeFinalComposition() > $alunoAvaliacaoORM->getNotaRetakeFinalComposition()) {
            $alunoAvaliacaoORM->setNotaRetakeFinalComposition($objetoORM->getNotaRetakeFinalComposition());
        }

        if ($objetoORM->getNotaRetakeFinalEscrita() > $alunoAvaliacaoORM->getNotaRetakeFinalEscrita()) {
            $alunoAvaliacaoORM->setNotaRetakeFinalEscrita($objetoORM->getNotaRetakeFinalEscrita());
        }
    }

    /**
     * Busca um aluno avaliacao ou cria um novo
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     *
     * @return NULL|\App\Entity\Principal\AlunoAvaliacao
     */
    private function buscarObjetoAlunoAvaliacao(&$mensagemErro, $reposicaoAulaORM)
    {
        $franqueadaId      = $reposicaoAulaORM->getFranqueada()->getId();
        $alunoId           = $reposicaoAulaORM->getAluno()->getId();
        $turmaId           = $reposicaoAulaORM->getTurma()->getId();
        $livroId           = $reposicaoAulaORM->getLivro()->getId();
        $alunoAvaliacaoORM = $this->alunoAvaliacaoRepository->buscaAlunoAvalicaoPorFranqueadaAlunoLivroTurma($franqueadaId, $alunoId, $livroId, $turmaId);
        if (is_null($alunoAvaliacaoORM) === true) {
            $parametrosAlunoAvaliacaoORM = [
                ConstanteParametros::CHAVE_FRANQUEADA => $franqueadaId,
                ConstanteParametros::CHAVE_ALUNO      => $alunoId,
                ConstanteParametros::CHAVE_LIVRO      => $livroId,
                ConstanteParametros::CHAVE_TURMA      => $turmaId,
                ConstanteParametros::CHAVE_PERSONAL   => false,
            ];
            if ($this->alunoAvaliacaoFacade->criar($mensagemErro, $parametrosAlunoAvaliacaoORM, $alunoAvaliacaoORM) === false) {
                $alunoAvaliacaoORM = null;
                $mensagemErro      = "Ocorreu um erro na criação de alunoAvaliacao:" . $mensagemErro;
            }
        }

        return $alunoAvaliacaoORM;
    }

    /**
     * Busca um AlunoDiario ou cria
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM
     *
     * @return NULL|mixed|\App\Entity\Principal\AlunoDiario
     */
    private function buscarObjetoAlunoDiario(&$mensagemErro, $reposicaoAulaORM)
    {
        $franqueadaId     = $reposicaoAulaORM->getFranqueada()->getId();
        $alunoId          = $reposicaoAulaORM->getAluno()->getId();
        $cursoId          = $reposicaoAulaORM->getTurma()->getCurso()->getId();
        $funcionarioId    = $reposicaoAulaORM->getResponsavelExecucao()->getId();
        $salaFranqueadaId = $reposicaoAulaORM->getSalaFranqueada()->getId();
        $livroId          = $reposicaoAulaORM->getLivro()->getId();
        $turmaId          = $reposicaoAulaORM->getTurma()->getId();
        $licaoId          = $reposicaoAulaORM->getLicao()->getId();
        $turmaAulaORM     = $this->turmaAulaRepository->buscarTurmaAulaPorFranqueadaTurmaLicao($franqueadaId, $turmaId, $licaoId);
        $alunoDiarioORM   = null;
        if (is_null($turmaAulaORM) === true) {
            $mensagemErro .= "TurmaAula não encontrado, para poder prosseguir com a reposição, precisa existir ao menos uma turmaAula.";
        } else {
            $alunoDiarioORM = $this->alunoDiarioRepository->buscaAlunoDiarioPorFranqueadaAlunoCursoTurmaAulaLivro($franqueadaId, $alunoId, $cursoId, $turmaAulaORM->getId(), $livroId, $licaoId);
            if (is_null($alunoDiarioORM) === true) {
                $parametrosAlunoDiario = [
                    ConstanteParametros::CHAVE_LICAOS          => [$licaoId],
                    ConstanteParametros::CHAVE_FRANQUEADA      => $franqueadaId,
                    ConstanteParametros::CHAVE_ALUNO           => $alunoId,
                    ConstanteParametros::CHAVE_CURSO           => $cursoId,
                    ConstanteParametros::CHAVE_TURMA_AULA      => $turmaAulaORM->getId(),
                    ConstanteParametros::CHAVE_FUNCIONARIO     => $funcionarioId,
                    ConstanteParametros::CHAVE_LIVRO           => $livroId,
                    ConstanteParametros::CHAVE_SALA_FRANQUEADA => $salaFranqueadaId,
                ];
                if ($this->alunoDiarioFacade->criar($mensagemErro, $parametrosAlunoDiario, $alunoDiarioORM) === false) {
                    $alunoDiarioORM = null;
                    $mensagemErro   = "Ocorreu um erro na criação de alunoDiario:" . $mensagemErro;
                }
            }

            if (is_null($alunoDiarioORM) === false) {
                $alunoDiarioORM->setPresenca($alunoDiarioORM->getPresenca());
                $alunoDiarioORM->setAtividadeCa("E");
                $alunoDiarioORM->setAtividadeCe("E");
                $alunoDiarioORM->setReposicaoAula(true);
            }
        }//end if

        return $alunoDiarioORM;
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade($managerRegistry);
        $this->alunoDiarioFacade        = new AlunoDiarioFacade($managerRegistry);
        $this->alunoAvaliacaoFacade     = new AlunoAvaliacaoFacade($managerRegistry);
        $this->reposicaoAulaBO          = new ReposicaoAulaBO(self::getEntityManager());
        $this->reposicaoAulaRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\ReposicaoAula::class);
        $this->alunoAvaliacaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoAvaliacao::class);
        $this->alunoDiarioRepository    = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiario::class);
        $this->turmaAulaRepository      = self::getEntityManager()->getRepository(\App\Entity\Principal\TurmaAula::class);
        $this->conceitoAvaliacaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class);
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
        $pagina = 1;
        if(isset($parametros["pagina"])){
            $pagina = $parametros["pagina"];
        }
        
        $retornoRepositorio = $this->reposicaoAulaRepository->filtrarReposicaoAulaPorPagina($parametros,$pagina);
                
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
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
        $objetoORM = $this->reposicaoAulaRepository->buscarPorId($id);

        if (empty($objetoORM) === true) {
            $mensagemErro = "Reposição não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\ReposicaoAula
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        
        $objetoORM         = null;
        $alunoAvaliacaoORM = null;
        $alunoDiarioORM    = null;
        if ($this->reposicaoAulaBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ReposicaoAula::class, true, $parametros);
            
            if ((bool)$parametros[ConstanteParametros::CHAVE_CONCLUIDO]) {
                if (in_array($objetoORM->getItem()->getTipoItem()->getTipo(), SituacoesSistema::TIPO_ITEMS_NOTAS_AVALIACOES) === true) {
                    $alunoAvaliacaoORM = $this->buscarObjetoAlunoAvaliacao($mensagemErro, $objetoORM);
                } else {
                    $alunoDiarioORM = $this->buscarObjetoAlunoDiario($mensagemErro, $objetoORM);
                }

                if (is_null($alunoAvaliacaoORM) === false) {
                    $objetoORM->setAlunoAvaliacao($alunoAvaliacaoORM);
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                    $this->criarHistoricoReposicaoAula($mensagemErro, $objetoORM);
                    $this->configuraNotasParaAlunoAvaliacao($alunoAvaliacaoORM, $objetoORM);
                }

                if (is_null($alunoDiarioORM) === false) {
                    $alunoDiarioORM->setReposicaoAula(true);
                    $objetoORM->setAlunoDiario($alunoDiarioORM);
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                }
                if ((is_null($alunoAvaliacaoORM) === false) || (is_null($alunoDiarioORM) === false)) {
                    self::persistSeguro($objetoORM, $mensagemErro);
                }
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
            }//end if
        }//end if
        return $objetoORM;
    }
    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param int $funcionarioId Id do funcionario Logado
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\ReposicaoAula $reposicaoAulaORM Reposicao de retorno
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $funcionarioId, $parametros=[], &$reposicaoAulaORM=null)
    {
        $objetoORM         = $this->reposicaoAulaRepository->find($id);
        $alunoAvaliacaoORM = null;
        $alunoDiarioORM    = null;
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ReposicaoAula não encontrado na base de dados.";
        } else {
            if ($this->reposicaoAulaBO->podeCriar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
                if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                    if (in_array($objetoORM->getItem()->getTipoItem()->getTipo(), SituacoesSistema::TIPO_ITEMS_NOTAS_AVALIACOES) === true) {
                        $alunoAvaliacaoORM = $this->buscarObjetoAlunoAvaliacao($mensagemErro, $objetoORM);
                    } else {
                        $alunoDiarioORM = $this->buscarObjetoAlunoDiario($mensagemErro, $objetoORM);
                    }

                    if (is_null($alunoAvaliacaoORM) === false) {
                        $objetoORM->setAlunoAvaliacao($alunoAvaliacaoORM);
                        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                        $this->criarHistoricoReposicaoAula($mensagemErro, $objetoORM);
                        $this->configuraNotasParaAlunoAvaliacao($alunoAvaliacaoORM, $objetoORM);
                    }

                    if (is_null($alunoDiarioORM) === false) {
                        $alunoDiarioORM->setReposicaoAula(true);
                        $objetoORM->setAlunoDiario($alunoDiarioORM);
                        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                    }
                } else {
                    if ((bool) $parametros[ConstanteParametros::CHAVE_CANCELAMENTO] === true) {
                        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CANCELAMENTO);
                    }
                }//end if

                $ocorrenciaORM = $objetoORM->getOcorrenciaAcademica();
                foreach ($parametros[ConstanteParametros::CHAVE_OBSERVACAO_OCORRENCIA_ACADEMICAS] as $observacaoTexto) {
                    $parametrosOcorrenciaAcademica  = [
                        ConstanteParametros::CHAVE_TEXTO       => $observacaoTexto,
                        ConstanteParametros::CHAVE_FUNCIONARIO => $funcionarioId,
                    ];
                    $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaORM, $parametrosOcorrenciaAcademica, false);
                    if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                        $mensagemErro .= "Não foi possivel atribuir o texto da ocorrencia.";
                        break;
                    }
                }

                $reposicaoAulaORM = $objetoORM;
            }//end if
        }//end if

        return empty($mensagemErro);
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


}
