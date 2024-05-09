<?php

namespace App\Controller\Principal\AlunoDiario;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Swagger\Annotations as SWG;
use App\Factory\ResponseFactory;
use App\Controller\Principal\Base\GenericController;
use FOS\RestBundle\Request\ParamFetcher;
use App\Facade\Principal\AlunoDiarioFacade;
use App\Facade\Principal\ContratoFacade;
use App\Facade\Principal\TurmaAulaFacade;
use App\Facade\Principal\TurmaFacade;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\AlunoAvaliacaoFacade;
use App\Facade\Principal\AlunoAvaliacaoConceitualFacade;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use Symfony\Component\HttpFoundation\Request;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\BO\Principal\ParametrosFranqueadoraBO;
use App\Helper\FunctionHelper;

/**
 *
 * @author        Luiz A Costa
 * @Route("/api")
 */
class AlunoDiarioController extends GenericController
{
    /**
     *
     * @var \App\Facade\Principal\AlunoDiarioFacade
     */
    private $alunoDiarioFacade;

    /**
     *
     * @var \App\Facade\Principal\TurmaAulaFacade
     */
    private $turmaAulaFacade;

    /**
     *
     * @var \App\Facade\Principal\ContratoFacade
     */
    private $contratoFacade;

    /**
     *
     * @var \App\Facade\Principal\TurmaFacade
     */
    private $turmaFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoFacade
     */
    private $alunoAvaliacaoFacade;

    /**
     *
     * @var \App\Facade\Principal\AlunoAvaliacaoConceitualFacade
     */
    private $alunoAvaliacaoConceitualFacade;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrencia;

    /**
     *
     * @var \App\Repository\Principal\TurmaAulaRepository
     */
    private $turmaAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade;

    /**
     *
     * @var \App\BO\Principal\ParametrosFranqueadoraBO
     */
    private $parametrosFranqueadoraBO;

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\Repository\Principal\ConceitoAvaliacaoRepository
     */
    private $conceitoAvaliacaoRepository;

    /**
     *
     * @var \App\Repository\Principal\OcorrenciaAcademicaRepository
     */
    private $ocorrenciaAcademicaRepository;

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     * {@inheritdoc}
     */
    protected function constroiFacades()
    {
        // Para criar o LogFacade do GenericController
        parent::constroiFacades();
        $this->alunoDiarioFacade    = new AlunoDiarioFacade(self::getManagerRegistry());
        $this->turmaAulaFacade      = new TurmaAulaFacade(self::getManagerRegistry());
        $this->contratoFacade       = new ContratoFacade(self::getManagerRegistry());
        $this->turmaFacade          = new TurmaFacade(self::getManagerRegistry());
        $this->alunoAvaliacaoFacade = new AlunoAvaliacaoFacade(self::getManagerRegistry());
        $this->alunoAvaliacaoConceitualFacade    = new AlunoAvaliacaoConceitualFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->funcionarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->tipoOcorrencia        = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoOcorrencia::class);
        $this->turmaAulaRepository   = self::getEntityManager()->getRepository(\App\Entity\Principal\TurmaAula::class);
        $this->turmaRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Turma::class);
        $this->alunoRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $this->alunoDiarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiario::class);
        $this->conceitoAvaliacaoRepository   = self::getEntityManager()->getRepository(\App\Entity\Principal\ConceitoAvaliacao::class);
        $this->ocorrenciaAcademicaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\OcorrenciaAcademica::class);
        $this->contratoRepository            = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->parametrosFranqueadoraBO      = new ParametrosFranqueadoraBO(self::getEntityManager());
    }

    /**
     * Retorna a id do primeiro funcionario que tiver o usuario informado caso não encontre, retornará null
     *
     * @param string $usuarioId
     *
     * @return number|NULL
     */
    private function retornaFuncionarioIdDoUsuario($usuarioId)
    {
        $funcionarios = $this->funcionarioRepository->findBy([ConstanteParametros::CHAVE_USUARIO => $usuarioId]);
        if (count($funcionarios) > 0) {
            $funcionarioORM = $funcionarios[0];
            return $funcionarioORM->getId();
        }

        return null;
    }

    /**
     * Retorna para o usuario a informação de se a nota que ele tirou é inferior ou igual ao que está parametrizado
     *
     * @param int $idConceitoAvaliacao
     *
     * @return boolean
     */
    private function verificaNotaMenorIgualParametro($idConceitoAvaliacao, &$tempObj=null)
    {
        if ($idConceitoAvaliacao !== null) {
            if (is_null($tempObj) === false) {
                $tempObj = null;
            }

            $tempObj = $this->conceitoAvaliacaoRepository->find($idConceitoAvaliacao);
            if ($tempObj->getValor() <= $this->parametrosFranqueadoraBO->retornaNotaDeCorteMediaConceitual()) {
                return true;
            }
        }

        return false;
    }

    /***
     * Gera ocorrencia academica para o aluno
     *
     * @param string $mensagemErro
     * @param int $alunoId
     * @param int $contratoId
     * @param string $origemOcorrencia
     * @param array $parametros
     * @param int $usuarioId
     * @param string $observacaoTexto
     * @param string $tipoItem
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademicaAluno(&$mensagemErro, $alunoId, $contratoId, $origemOcorrencia, $parametros, $usuarioId, $observacaoTexto="", $tipoItem="", $turmaAulaORM=null)
    {
        $bRetorno          = true;
        $tipoOcorrenciaORM = null;
        $idTipoOcorrencia  = null;

        $tipoOcorrenciaORM = $this->tipoOcorrencia->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoItem]);

        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro .= "Não foi encontrado um TipoOcorrencia para o tipo informado.\n";
        }

        $retornaFuncionarioIdDoUsuario = $this->retornaFuncionarioIdDoUsuario($usuarioId);

        if (is_null($retornaFuncionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario cadastrado para o usuario logado.\n";
        }

        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro .= "Não foi encontrado uma Ocorrencia para o tipo informado.\n";
        } else {
            $idTipoOcorrencia = $tipoOcorrenciaORM->getId();
        }

        $contratoORM = $this->contratoRepository->find($contratoId);

        if (is_null($contratoORM) === true) {
            $mensagemErro .= "Não foi encontrado o contrato informado.\n";
        }

        if (empty($mensagemErro) === false) {
            return false;
        }

        // Checando se é pra apenas concatenar a uma ocorrencia aberta, ou se é pra criar novas
        $ocorrenciasContrato   = null;
        $tipoOcorrencia        = $tipoOcorrenciaORM->getTipo();
        $ocorrenciasConcatenar = [
            SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_FALTA,
            SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CA,
            SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CE,
        ];
        $deveApenasAdicionarDetalhes = in_array($tipoOcorrencia, $ocorrenciasConcatenar);
        if ($deveApenasAdicionarDetalhes === true) {
            $parametrosBuscaOcorrenciasAbertas = [
                ConstanteParametros::CHAVE_CONTRATO        => $contratoId,
                ConstanteParametros::CHAVE_SITUACAO        => SituacoesSistema::OCORRENCIA_ABERTA,
                ConstanteParametros::CHAVE_TIPO_OCORRENCIA => $idTipoOcorrencia,
            ];
            $ocorrenciasContrato = $this->ocorrenciaAcademicaFacade->getOcorrenciasContrato($mensagemErro, $parametrosBuscaOcorrenciasAbertas);
            if (empty($mensagemErro) === false) {
                return false;
            }
        }

        $parametrosDetalhes = [
            ConstanteParametros::CHAVE_TIPO_OCORRENCIA => $tipoItem,
            ConstanteParametros::CHAVE_CONTRATO        => $contratoORM,
            ConstanteParametros::CHAVE_TURMA_AULA      => $turmaAulaORM,
        ];

        $possuiOcorrenciaAula = $this->ocorrenciaAcademicaDetalhesFacade->possuiOcorrencia($mensagemErro, $parametrosDetalhes);
        if (empty($mensagemErro) === false) {
            return false;
        }

        // Se já existe este tipo de ocorrencia pra esta turma_aula e contrato, não deve criar!
        // if ($possuiOcorrenciaAula === true) {
        //     return true;
        // }

        if (is_null($ocorrenciasContrato) === false && count($ocorrenciasContrato) > 0) {
            $ocorrenciaAcademicaORM = $this->ocorrenciaAcademicaRepository->find($ocorrenciasContrato[0]["id"]);
        } else {
            $parametrosOcorrenciaAcademica = [
                ConstanteParametros::CHAVE_FRANQUEADA             => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ConstanteParametros::CHAVE_ALUNO                  => $alunoId,
                ConstanteParametros::CHAVE_CONTRATO               => $contratoId,
                ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO => $origemOcorrencia,
                ConstanteParametros::CHAVE_USUARIO                => $usuarioId,
                ConstanteParametros::CHAVE_FUNCIONARIO            => $retornaFuncionarioIdDoUsuario,
                ConstanteParametros::CHAVE_TIPO_OCORRENCIA        => $idTipoOcorrencia,
                ConstanteParametros::CHAVE_DATA_CONCLUSAO         => new \DateTime(),
                ConstanteParametros::CHAVE_SITUACAO               => SituacoesSistema::OCORRENCIA_ABERTA,
                ConstanteParametros::CHAVE_TEXTO                  => $observacaoTexto,
            ];
            $ocorrenciaAcademicaORM        = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                return false;
            }
        }

        if ($deveApenasAdicionarDetalhes === true) {
            $parametrosOcorrenciaAcademica = [
                ConstanteParametros::CHAVE_FUNCIONARIO => $retornaFuncionarioIdDoUsuario,
                ConstanteParametros::CHAVE_TEXTO       => $observacaoTexto,
            ];
        }

        $parametrosOcorrenciaAcademica[ConstanteParametros::CHAVE_TURMA_AULA] = $turmaAulaORM;

        $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
        if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
            return false;
        }

        return $bRetorno;
    }

    /**
     * Retorna o texto da ocorrencia mid term
     *
     * @param array $alunoAvaliacaoMetaData
     *
     * @return string
     */
    private function retornaTextoOcorrenciaMidTerm($alunoAvaliacaoMetaData)
    {
        $texto  = "****NOTAS****\nAluno(a) obteve nota abaixo do requerido para aprovação.\n";
        $texto .= "Prova escrita: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_COMPOSITION]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_COMPOSITION]) === false)) {
            $texto .= number_format($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_COMPOSITION], 2);
        }

        $texto .= "\nRedação: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_TEST]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_TEST]) === false)) {
            $texto .= number_format($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_TEST], 2);
        }

        $texto .= "\nNota oral: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]) === false)) {
            $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ORAL]);
            $texto      .= $conceitoORM->getDescricao();
        }

        return $texto;
    }

    /**
     * Retorna o texto da ocorrencia final test
     *
     * @param array $alunoAvaliacaoMetaData
     *
     * @return string
     */
    private function retornaTextoOcorrenciaFinalTest($alunoAvaliacaoMetaData)
    {
        $texto  = "****NOTAS****\nAluno(a) obteve nota abaixo do requerido para aprovação.\n";
        $texto .= "Prova escrita: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_COMPOSITION]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_COMPOSITION]) === false)) {
            $texto .= number_format($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_COMPOSITION], 2);
        }

        $texto .= "\nRedação: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_TEST]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_TEST]) === false)) {
            $texto .= number_format($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_TEST], 2);
        }

        $texto .= "\nNota oral: ";
        if ((isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === true)&&(empty($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]) === false)) {
            $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ORAL]);
            $texto      .= $conceitoORM->getDescricao();
        }

        return $texto;
    }

    /**
     * Retorna o texto da ocorrencia da avaliacao parcial
     *
     * @param array $alunoAvaliacaoConceitualMetaData
     * @param boolean $bAvaliacao23
     *
     * @return string
     */
    private function retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData, $bAvaliacao23=false)
    {
        if ($bAvaliacao23 === true) {
            $texto  = "****NOTAS****\nAluno(a) obteve os seguintes conceitos abaixo do requerido para aprovação.\n";
            $texto .= "Writing: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2]);
                $texto      .= $conceitoORM->getDescricao();
            }

            $texto .= "\nListening: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_2]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_2]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_2]);
                $texto      .= $conceitoORM->getDescricao();
            }

            $texto .= "\nSpeaking: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]);
                $texto      .= $conceitoORM->getDescricao();
            }
        } else {
            $texto  = "****NOTAS****\nAluno(a) obteve os seguintes conceitos abaixo do requerido para aprovação.\n";
            $texto .= "Writing: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1]);
                $texto      .= $conceitoORM->getDescricao();
            }

            $texto .= "\nListening: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_1]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_1]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_1]);
                $texto      .= $conceitoORM->getDescricao();
            }

            $texto .= "\nSpeaking: ";
            if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]) === true)&&(empty($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]) === false)) {
                $conceitoORM = $this->conceitoAvaliacaoRepository->find($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]);
                $texto      .= $conceitoORM->getDescricao();
            }
        }//end if

        return $texto;
    }

    /**
     * Verifica se a turma só possui a aula no SABADO
     *
     * @param \App\Entity\Principal\TurmaAula[] $turmaAulasORM
     *
     * @return boolean
     */
    private function turmaApenasComAulasSabado($turmaAulasORM)
    {
        $contadorSabado = 0;
        foreach ($turmaAulasORM as $turmaAulaORM) {
            if ($turmaAulaORM->getDataAula()->format("D") === "Sat") {
                $contadorSabado++;
            }
        }

        $bRetorno = $contadorSabado === $turmaAulasORM->count();
        return $bRetorno;
    }

    /**
     * Retorna lista de turma aula
     *
     * @param int $turmaId
     *
     * @return \App\Entity\Principal\TurmaAula[]
     */
    private function retornaTurmaAulaLista($turmaId)
    {
        $turmaORM = $this->turmaRepository->findOneBy([ConstanteParametros::CHAVE_ID => $turmaId]);
        $turmaORM->getTurmaAulas()->getIterator()->uasort(
            function ($a, $b) {
                return $a->getDataAula() < $b->getDataAula();
            }
        );
        return $turmaORM->getTurmaAulas();
    }

    /**
     * Verifica se extourou a cota de faltas seguidas
     *
     * @param int $turmaId
     * @param int $alunoId
     * @param int $contadorDiaSeguidos
     * @param int $totAulasMinistradas
     *
     * @return boolean
     */
    private function verificaExtourouCotaFaltaSeguidas($turmaId, $alunoId, &$contadorDiaSeguidos, &$totAulasMinistradas)
    {
        $diasSeguidosPodeFaltar = 1;
        // Dois dias seguidos contados(o segundo dia é o lançamento da falta)
        $turmaAulasLista = $this->retornaTurmaAulaLista($turmaId);
        // $bExisteApenasAulaSabado = $this->turmaApenasComAulasSabado($turmaAulasLista);
        // if ($bExisteApenasAulaSabado === true) {
        // $diasSeguidosPodeFaltar = 3;
        // Quatro dias seguidos(o quarto dia, é o do lançamento da falta)
        // }
        $contadorDiaSeguidos = 0;
        foreach ($turmaAulasLista as $turmaAulaORM) {
            if ($turmaAulaORM->getFinalizada() === true) {
                $totAulasMinistradas++;
                // Filtra diario do aluno
                $alunosDiarioFinalizados = $turmaAulaORM->getAlunoDiarios()->filter(
                    function ($alunoDiarioORM) use ($alunoId) {
                        return ($alunoDiarioORM->getAluno()->getId() === (int) $alunoId);
                    }
                );

                // Verifica se tem diario para aquela turma (pode ser que seja um aluno transferido)
                if ($alunosDiarioFinalizados->count() > 0) {
                    $primeiraKey = FunctionHelper::getPrimeiraKeyArray($alunosDiarioFinalizados->toArray());

                    if ($primeiraKey === null) {
                        $mensagemErro = "Não foi possivel prosseguir com o lançamento/Atualização.\nErro inesperado.\n" . $mensagemErro;
                        return ResponseFactory::badRequest(["parametros" => []], $mensagemErro);
                    }

                    $alunoDiarioORM = $alunosDiarioFinalizados->get($primeiraKey);
                    if ($alunoDiarioORM->getPresenca() === SituacoesSistema::ALUNO_FALTA) {
                        $contadorDiaSeguidos++;
                    } else {
                        $contadorDiaSeguidos = 0;
                    }
                }
            }//end if

            continue;
        }//end foreach

        return ($contadorDiaSeguidos >= $diasSeguidosPodeFaltar);
    }

    /**
     * Verifica se o aluno não entregou alguma atividade
     *
     * @param \App\Entity\Principal\TurmaAula $turmaAulaAtualORM
     * @param int $alunoId
     * @param string $sAtividadeValidada
     * @param int $quantidadeNaoEntregues
     * @param \App\Entity\Principal\AlunoDiario $alunoDiarioORM
     *
     * @return boolean
     */
    private function estourouQuantidadeHomeworkNaoEntregue($turmaAulaAtualORM, $alunoId, $sAtividadeValidada, &$quantidadeNaoEntregues, $alunoDiarioORM)
    {
        // Quantidade de atividades a partir da qual gera ocorrencia
        $atividadesGeraOcorrencia = 2;
        $turmaAulasLista          = $this->retornaTurmaAulaLista($turmaAulaAtualORM->getTurma()->getId());
        // Por algum bug do ORM não considera a turmaAula atual como finalizada, mas só entra aqui quando não foi entregue
        // A variável abaixo tem que permanecer como 1 para o funcionamento correto
        $quantidadeNaoEntregues = 0;
        foreach ($turmaAulasLista as $turmaAulaORM) {
            if ($turmaAulaORM->getFinalizada() === false) {
                continue;
            }

            // Filtra diario do aluno
            $alunoDiarioTurmaAula = $turmaAulaORM->getAlunoDiarios()->filter(
                function ($alunoDiarioORM) use ($alunoId) {
                    return ($alunoDiarioORM->getAluno()->getId() === (int) $alunoId);
                }
            );

            // Verifica se tem diario para aquela turma (pode ser que seja um aluno transferido)
            if ($alunoDiarioTurmaAula->count() === 0) {
                if ($turmaAulaAtualORM->getId() === $turmaAulaORM->getId()) {
                    $alunoDiarioTurmaAula[] = $alunoDiarioORM;
                } else {
                    continue;
                }
            }

            $primeiraKey = FunctionHelper::getPrimeiraKeyArray($alunoDiarioTurmaAula->toArray());

            if ($primeiraKey === null) {
                $mensagemErro = "Não foi possivel prosseguir com o lançamento/Atualização.\nErro inesperado.\n";
                return ResponseFactory::badRequest(["parametros" => []], $mensagemErro);
            }

            $alunoDiarioORM = $alunoDiarioTurmaAula->get($primeiraKey);
            if ($sAtividadeValidada === ConstanteParametros::CHAVE_ATIVIDADE_CA) {
                $atividadeValidada = $alunoDiarioORM->getAtividadeCa();
            } else {
                $atividadeValidada = $alunoDiarioORM->getAtividadeCe();
            }

            if ($atividadeValidada === SituacoesSistema::ALUNO_ATIVIDADE_NAO_ENTREGUE) {
                $quantidadeNaoEntregues++;
            } else {
                $quantidadeNaoEntregues = 0;
            }
        }//end foreach

        return ($quantidadeNaoEntregues >= $atividadesGeraOcorrencia);
    }

    /**
     * Existe ocorrencia Academica para o tipo
     *
     * @param int $alunoId
     * @param int $contratoId
     * @param string $tipoOrigem
     *
     * @return boolean
     */
    private function existeOcorrenciaRegistradaParaOrigem($alunoId, $contratoId, $tipoOrigem)
    {
        return is_null($this->ocorrenciaAcademicaRepository->buscaOcorrenciaAlunoContratoTipo($alunoId, $contratoId, $tipoOrigem)) == false;
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno_diario/listar",
     *     summary="Listar aluno_diario",
     *     description="Lista as aluno_diario do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os aluno_diario"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="pagina", strict=false, allowBlank=false, default="1", description="Pagina para realizar o scroll", requirements="\d+")
     *
     * @FOSRest\Get("/aluno_diario/listar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lista(ParamFetcher $request)
    {
        $parametros = $request->all();
        $mensagem   = "";
        $resultados = "";
        if ($resultados === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno_diario/buscar_avaliacoes_turma/{turmaId}",
     *     summary="Listar AlunoDiario",
     *     description="Lista as avaliacoes da Turma do banco",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna os turma_aula"
     *     ),
     * @SWG\Response(
     *         response="400",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\QueryParam(name="franqueada", strict=true, allowBlank=false, description="Franqueada ID", requirements="\d+")
     * @FOSRest\QueryParam(name="livro",      nullable=true, strict=true, allowBlank=true, description="Livro ID", requirements="\d+")
     *
     * @FOSRest\Get("/aluno_diario/buscar_avaliacoes_turma/{turmaId}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscarAvaliacoesTurma($turmaId, ParamFetcher $request)
    {
        $mensagem   = "";
        $parametros = $request->all();
        $resultados = $this->turmaAulaFacade->listarAvaliacoesPorTurma($turmaId, $parametros);
        if (empty($mensagem) === false) {
            return ResponseFactory::badRequest([], $mensagem);
        }

        return ResponseFactory::ok($resultados);
    }

    /**
     *
     * @SWG\Get(
     *     path="/api/aluno_diario/{id}",
     *     summary="Buscar a aluno_diario",
     *     description="Busca a aluno_diario através da ID",
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="200",
     *         description="Retorna a aluno_diario"
     *     ),
     * @SWG\Response(
     *         response="404",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\Get("/aluno_diario/{id}")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buscar($id)
    {
        $objetoORM = null;
        if (is_null($objetoORM) === true) {
            return ResponseFactory::notFound([], "OBJETO ORM não encontrada.");
        }

        return ResponseFactory::ok($objetoORM);
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/aluno_diario/lancar_atualizar_frequencias",
     *     summary="Cria uma aluno_diario",
     *     description="Cria uma aluno_diario no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="dados_alunos", strict=true, nullable=false, allowBlank=false, description="Array Dados Alunos", map=true)
     * @FOSRest\RequestParam(name="licaos",       strict=true, nullable=false, allowBlank=false, description="Array Licao Aplicada", map=true)
     * @FOSRest\RequestParam(name="turma_aula",   strict=true, nullable=true, allowBlank=true, description="Turma Aula ID", requirements="\d+")
     * @FOSRest\RequestParam(name="funcionario",  strict=true, nullable=true, description="Funcionario que aplicou a aula", requirements="\d+")
     * @FOSRest\RequestParam(name="franqueada",   strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",        strict=false, nullable=true, description="ID da turma", requirements="\d+")
     * @FOSRest\RequestParam(name="observacao",   strict=true, nullable=true, allowBlank=true, description="Observacao")
     * @FOSRest\RequestParam(name="data_aula",    strict=false, nullable=false, allowBlank=false, description="Data da aula")
     *
     * @FOSRest\Post("/aluno_diario/lancar_atualizar_frequencias")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lancarAtualizarFrequencias(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros     = $paramFetcher->all();
        $usuarioID      = $request->headers->get('Authorization-User-ID');
        $mensagem       = "";
        $bSucesso       = true;
        $objetoTurmaORM = null;

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA_AULA]) === false)) {
            $dadosAlunosTemp     = [];
            $idTurmaAula         = $parametros[ConstanteParametros::CHAVE_TURMA_AULA];
            $parametrosTurmaAula = [
                ConstanteParametros::CHAVE_FUNCIONARIO => $parametros[ConstanteParametros::CHAVE_FUNCIONARIO],
                ConstanteParametros::CHAVE_OBSERVACAO  => $parametros[ConstanteParametros::CHAVE_OBSERVACAO],
                ConstanteParametros::CHAVE_DATA_AULA   => $parametros[ConstanteParametros::CHAVE_DATA_AULA],
            ];
            $bSucesso            = $this->turmaAulaFacade->atualizaCamposTelaDiarioClasse($mensagem, $idTurmaAula, $parametrosTurmaAula);
            if ($bSucesso === false) {
                $mensagem = "Não foi possivel prosseguir com o registro dos diarios para as lições.\nErro Inesperado.\n" . $mensagem;
                return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
            }

            for ($i = 0;$i < count($parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS]);$i++) {
                $dadosAluno = $parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS][$i];
                $dadosAluno[ConstanteParametros::CHAVE_TURMA_AULA] = $parametros[ConstanteParametros::CHAVE_TURMA_AULA];
                if (isset($dadosAluno[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && empty($dadosAluno[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) {
                    $dadosAluno[ConstanteParametros::CHAVE_SALA_FRANQUEADA] = null;
                }

                $dadosAlunosTemp[ConstanteParametros::CHAVE_DADOS_ALUNOS][] = $dadosAluno;
            }

            $parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS] = $dadosAlunosTemp[ConstanteParametros::CHAVE_DADOS_ALUNOS];
        }//end if

        if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
            $objetoTurmaORM    = $this->turmaRepository->find($parametros[ConstanteParametros::CHAVE_TURMA]);
            $primeiraTurmaAula = $objetoTurmaORM->getTurmaAulas()->get(0);
            $ultimaTurmaAula   = $objetoTurmaORM->getTurmaAulas()->get($objetoTurmaORM->getTurmaAulas()->count() - 1);

            if ((bool) $primeiraTurmaAula->getFinalizada() === true) {
                $objetoTurmaORM->setSituacao(SituacoesSistema::SITUACAO_TURMA_ABERTA);
            }

            if ((bool) $ultimaTurmaAula->getFinalizada() === true) {
                $bEncerrado = true;

                foreach ($objetoTurmaORM->getTurmaAulas() as $turmaAulaItemORM) {
                    if ($turmaAulaItemORM->getFinalizada() === false) {
                        $bEncerrado = false;
                        break;
                    }
                }

                if ($bEncerrado === true) {
                    $objetoTurmaORM->setSituacao(SituacoesSistema::SITUACAO_TURMA_ENCERRADA);
                }
            }
        }//end if

        $turmaORM = null;
        if ($bSucesso === true) {
            foreach ($parametros[ConstanteParametros::CHAVE_DADOS_ALUNOS] as &$alunoDiarioMetaData) {
                $alunoDiarioMetaData[ConstanteParametros::CHAVE_FUNCIONARIO] = $parametros[ConstanteParametros::CHAVE_FUNCIONARIO];
                $alunoDiarioMetaData[ConstanteParametros::CHAVE_LICAOS]      = $parametros[ConstanteParametros::CHAVE_LICAOS];
                $alunoDiarioMetaData[ConstanteParametros::CHAVE_FRANQUEADA]  = $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
                $turmaAulaORM = $this->turmaAulaRepository->find($parametros[ConstanteParametros::CHAVE_TURMA_AULA]);

                if (is_null($turmaORM) === true) {
                    $turmaORM = $turmaAulaORM->getTurma();
                }

                $contratoId  = $alunoDiarioMetaData[ConstanteParametros::CHAVE_CONTRATO];
                $contratoORM = $this->contratoRepository->find($contratoId);
                if (is_null($contratoORM) === true) {
                    $mensagem = 'Contrato do aluno não encontrato.';
                    $bSucesso = false;
                    break;
                }

                $alunoDiarioMetaData[ConstanteParametros::CHAVE_CONTRATO] = $contratoORM;
                $alunoDiarioORMNovo = null;
                $objetoORM          = $this->alunoDiarioFacade->lancarAtualizarFrequencias($mensagem, $alunoDiarioMetaData, $alunoDiarioORMNovo);
                if ($objetoORM === false) {
                    $bSucesso = false;
                    break;
                }

                // verificar se o aluno estiver só no sabado permitir que ele falta até "4" x seguidas
                if ($alunoDiarioMetaData[ConstanteParametros::CHAVE_PRESENCA] === SituacoesSistema::ALUNO_FALTA) {
                    $totAulasFaltadas    = 0;
                    $totAulasMinistradas = 0;
                    if ($this->verificaExtourouCotaFaltaSeguidas($turmaAulaORM->getTurma()->getId(), $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], $totAulasFaltadas, $totAulasMinistradas) === true) {
                        $totAulasFaltadas++;
                        $observacao = "****FALTAS****\nAluno(a) faltou \"" . $totAulasFaltadas . "\" dias consecutivos.";
                        if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagem, $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_FALTAS_SEGUIDAS, $parametros, $usuarioID, $observacao, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_FALTA, $turmaAulaORM)) === false) {
                            $bSucesso = false;
                            break;
                        }
                    }
                }

                // verificar duas aulas seguidas exemplo = 2 CA gera ocorrencia, 2 CE gera ocorrencia
                $possuiCampoID = (isset($alunoDiarioMetaData[ConstanteParametros::CHAVE_ID]) === true)&&(empty($alunoDiarioMetaData[ConstanteParametros::CHAVE_ID]) === false);
                $possuiAtividadeCANaoEntregue = $alunoDiarioMetaData[ConstanteParametros::CHAVE_ATIVIDADE_CA] === SituacoesSistema::ALUNO_ATIVIDADE_NAO_ENTREGUE;
                $possuiAtividadeCENaoEntregue = $alunoDiarioMetaData[ConstanteParametros::CHAVE_ATIVIDADE_CE] === SituacoesSistema::ALUNO_ATIVIDADE_NAO_ENTREGUE;
                if ((($possuiCampoID === true) || (is_null($alunoDiarioORMNovo) === false)) && (($possuiAtividadeCANaoEntregue === true) || ($possuiAtividadeCENaoEntregue === true))) {
                    $qtdeCA = 0;
                    $qtdeCE = 0;

                    if (($possuiAtividadeCANaoEntregue === true) && ($this->estourouQuantidadeHomeworkNaoEntregue($turmaAulaORM, $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], ConstanteParametros::CHAVE_ATIVIDADE_CA, $qtdeCA, $alunoDiarioORMNovo) === true)) {
                        $texto = "****Atividade CA****\nAluno(a) não apresentou o Homework CA por $qtdeCA aulas seguidas.";
                        if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagem, $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_HOMEWORK_CA, $parametros, $usuarioID, $texto, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CA, $turmaAulaORM)) === false) {
                            $bSucesso = false;
                            break;
                        }
                    }

                    if (($possuiAtividadeCENaoEntregue === true) && ($this->estourouQuantidadeHomeworkNaoEntregue($turmaAulaORM, $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], ConstanteParametros::CHAVE_ATIVIDADE_CE, $qtdeCE, $alunoDiarioORMNovo) === true)) {
                        $texto = "****Atividade CE****\nAluno(a) não apresentou o Homework CE por $qtdeCE aulas seguidas.";
                        if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagem, $alunoDiarioMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_HOMEWORK_CE, $parametros, $usuarioID, $texto, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CE, $turmaAulaORM)) === false) {
                            $bSucesso = false;
                            break;
                        }
                    }
                }//end if
            }//end foreach
        }//end if

        if ($bSucesso === false || empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        self::getEntityManager()->flush();

        if (is_null($turmaORM) === false) {
            $situacaoTurma = null;
            if ($turmaORM->getSituacao() === SituacoesSistema::SITUACAO_TURMA_EM_FORMACAO) {
                $situacaoTurma = SituacoesSistema::SITUACAO_TURMA_ABERTA;
            }

            $estaCompleta = true;
            foreach ($turmaORM->getTurmaAulas() as $aula) {
                if (count($aula->getAlunoDiarios()) === 0) {
                    $estaCompleta = false;
                }
            }

            if ($estaCompleta === true) {
                $this->turmaFacade->encerrar($mensagem, $turmaORM);
            } else if (is_null($situacaoTurma) === false) {
                $turmaORM->setSituacao($situacaoTurma);
            }
        }//end if

        if (empty($mensagem) === false) {
            return ResponseFactory::conflict(["parametros" => $parametros], $mensagem);
        }

        self::getEntityManager()->flush();

        return ResponseFactory::created([], "Registro no diário de classe lançado com sucesso!");
    }

    /**
     *
     * @SWG\Post(
     *     path="/api/aluno_diario/lancar_atualizar_notas",
     *     summary="Cria uma aluno_diario",
     *     description="Cria uma aluno_diario no banco",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Response(
     *         response="201",
     *         description="Retorna criado com sucesso"
     *     ),
     * @SWG\Response(
     *         response="202",
     *         description="Ocorreu algum erro no servidor",
     *     )
     * )
     *
     * @FOSRest\RequestParam(name="alunos_avaliacao",            strict=true, nullable=true, allowBlank=false, description="Array Alunos Avaliacao", map=true)
     * @FOSRest\RequestParam(name="alunos_avaliacao_conceitual", strict=true, nullable=true, allowBlank=false, description="Array Alunos Avaliacao Conceitual", map=true)
     * @FOSRest\RequestParam(name="franqueada",                  strict=true, nullable=false, description="ID da franqueada", requirements="\d+")
     * @FOSRest\RequestParam(name="turma",                       strict=true, nullable=true, allowBlank=true, description="ID da turma")
     *
     * @FOSRest\Post("/aluno_diario/lancar_atualizar_notas")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lancarAtualizarNotas(ParamFetcher $paramFetcher, Request $request)
    {
        $parametros   = $paramFetcher->all();
        $mensagemErro = "";
        $usuarioID    = $request->headers->get('Authorization-User-ID');
        $bRetorno     = false;

        if (isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true) {
            $turmaORM = $this->turmaRepository->find($parametros[ConstanteParametros::CHAVE_TURMA]);
            if (is_null($turmaORM) === false) {
                $bRetorno = true;
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO]) > 0) && ($bRetorno === true)) {
            $alunosAvaliacao = $parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO];
            foreach ($alunosAvaliacao as &$alunoAvaliacaoMetaData) {
                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_PERSONAL]   = false;
                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_TURMA]      = $turmaORM;
                $contratoId  = $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_CONTRATO];
                $contratoORM = $this->contratoRepository->find($contratoId);
                if (is_null($contratoORM) === true) {
                    $mensagemErro .= "Contrato não encontrado.";
                    $bRetorno      = false;
                    break;
                }

                $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_CONTRATO] = $contratoORM;
                $bRetorno = $this->alunoAvaliacaoFacade->lancarAtualizarNotas($mensagemErro, $alunoAvaliacaoMetaData);
                if ($bRetorno === false) {
                    break;
                }

                $bExisteOcorrenciaJaRegistradaMidTerm   = $this->existeOcorrenciaRegistradaParaOrigem($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_MID_TERM);
                $bExisteOcorrenciaJaRegistradaFinalTest = $this->existeOcorrenciaRegistradaParaOrigem($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_FINAL_TEST);

                $bExisteNotaMidTermEscritaPost       = isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ESCRITA]);
                $bExisteNotaFinalEscritaPost         = isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ESCRITA]);
                $bExisteNotaRetakeMidTermEscritaPost = isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ESCRITA]);
                $bExisteNotaRetakeFinalEscritaPost   = isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ESCRITA]);
                if ($bExisteNotaMidTermEscritaPost === true) {
                    $texto = "Mid-Term.\n";
                    if ($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ESCRITA] < $this->parametrosFranqueadoraBO->retornaNotaDeCorteMedia()) {
                        $texto = $this->retornaTextoOcorrenciaMidTerm($alunoAvaliacaoMetaData);
                    }
                    if ($bExisteOcorrenciaJaRegistradaMidTerm === false && 
                            isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_MID_TERM_ESCRITA]) === true) {
                        if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagemErro, $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_MID_TERM, $parametros, $usuarioID, $texto, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_AVALIACOES)) === false) {
                            break;
                        }
                    }
                }

                if ($bExisteNotaFinalEscritaPost === true) {
                    $texto = "Final Test.\n";
                    if ($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ESCRITA] < $this->parametrosFranqueadoraBO->retornaNotaDeCorteMedia()) {
                        $texto = $this->retornaTextoOcorrenciaFinalTest($alunoAvaliacaoMetaData);
                    }
                    if ($bExisteOcorrenciaJaRegistradaFinalTest === false && 
                            isset($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_FINAL_ESCRITA]) === true) {
                        if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagemErro, $alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_FINAL_TEST, $parametros, $usuarioID, $texto, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_AVALIACOES)) === false) {
                            break;
                        }
                    }
                }

                if (($bExisteNotaRetakeMidTermEscritaPost === true) && ($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_RETAKE_MID_TERM_ESCRITA] < $this->parametrosFranqueadoraBO->retornaNotaDeCorteMedia())) {
                    // TODO: possibilidade de gerar para retake
                }

                if (($bExisteNotaRetakeFinalEscritaPost === true) && ($alunoAvaliacaoMetaData[ConstanteParametros::CHAVE_NOTA_RETAKE_FINAL_ESCRITA] < $this->parametrosFranqueadoraBO->retornaNotaDeCorteMedia())) {
                    // TODO: possibilidade de gerar para retake
                }
            }//end foreach
        }//end if

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL]) === true)&&(count($parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL]) > 0) && ($bRetorno === true)) {
            $alunosAvaliacaoConceitual = $parametros[ConstanteParametros::CHAVE_ALUNOS_AVALIACAO_CONCEITUAL];
            foreach ($alunosAvaliacaoConceitual as $alunoAvaliacaoConceitualMetaData) {
                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_PERSONAL]   = false;
                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_TURMA]      = $turmaORM;
                $contratoId  = $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_CONTRATO];
                $contratoORM = $this->contratoRepository->find($contratoId);
                if (is_null($contratoORM) === true) {
                    $mensagemErro .= "Contrato não encontrado.";
                    $bRetorno      = false;
                    break;
                }

                $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_CONTRATO] = $contratoORM;
                $bRetorno = $this->alunoAvaliacaoConceitualFacade->lancarAtualizarNotas($mensagemErro, $alunoAvaliacaoConceitualMetaData);
                if ($bRetorno === false) {
                    break;
                }

                $bExisteOcorrenciaJaRegistradaParcial7  = $this->existeOcorrenciaRegistradaParaOrigem($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_7);
                $bExisteOcorrenciaJaRegistradaParcial23 = $this->existeOcorrenciaRegistradaParaOrigem($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_23);
                $textoParcial7  = "";
                $textoParcial23 = "";
                $tempObj        = null;
                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1], $tempObj) === true)) {
                    $textoParcial7 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData);
                }

                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_1]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_1], $tempObj) === true)) {
                    $textoParcial7 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData);
                }

                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_1]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_1], $tempObj) === true)) {
                    $textoParcial7 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData);
                }

                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2], $tempObj) === true)) {
                    $textoParcial23 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData, true);
                }

                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_2]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_WRITING_2], $tempObj) === true)) {
                    $textoParcial23 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData, true);
                }

                if ((isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_2]) === true) && ($this->verificaNotaMenorIgualParametro($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_SPEAKING_2], $tempObj) === true)) {
                    $textoParcial23 = $this->retornaTextoOcorrenciaAvaliacaoParcial($alunoAvaliacaoConceitualMetaData, true);
                }

                if ($bExisteOcorrenciaJaRegistradaParcial7 === false && isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_1]) === true) {
                    $textoParcial7  =  "1ª Avaliação Parcial.\n".$textoParcial7;
                    if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagemErro, $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_7, $parametros, $usuarioID, $textoParcial7, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_AVALIACOES)) === false) {
                        break;
                    }
                }

                if ($bExisteOcorrenciaJaRegistradaParcial23 === false && isset($alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_NOTA_LISTENING_2]) === true) {
                    $textoParcial23  =  "2ª Avaliação Parcial.\n".$textoParcial23;
                    if (($bRetorno = $this->gerarOcorrenciaAcademicaAluno($mensagemErro, $alunoAvaliacaoConceitualMetaData[ConstanteParametros::CHAVE_ALUNO], $contratoId, SituacoesSistema::ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_23, $parametros, $usuarioID, $textoParcial23, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_AVALIACOES)) === false) {
                        break;
                    }
                }
            }//end foreach
        }//end if

        if ($bRetorno === false) {
            $mensagemErro = "Não foi possivel prosseguir com o lançamento/Atualização.\nErro inesperado.\n" . $mensagemErro;
            return ResponseFactory::badRequest(["parametros" => $parametros], $mensagemErro);
        }

        self::getEntityManager()->flush();
        return ResponseFactory::ok([], "Notas aplicadas com sucesso!");
    }


}
