<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\SituacoesSistema;

class NegativacaoAlunosCommand extends Command
{
    protected static $defaultName = 'cron:negativacao-alunos';

    /**
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $managerRegistry = null;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager = null;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaFacade
     */
    private $ocorrenciaAcademicaFacade = null;

    /**
     *
     * @var \App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade
     */
    private $ocorrenciaAcademicaDetalhesFacade = null;

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository = null;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository = null;

    /**
     *
     * @var \App\Repository\Principal\ItemRepository
     */
    private $itemRepository = null;

    /**
     * Apenas para ser usado na criação de ocorrencia
     *
     * @var integer
     */
    private $idItemCobranca;

    protected function configure()
    {
        $this->setDescription('Executa a negativacao de alunos, partindo do campo "dias_reativacao_interessado" configurado na ParametrosFranqueadora.');
    }

    /**
     * Configura a base a ser pega as informações e o WorkflowBO
     *
     * @param string $nomeBase Nome da configuração da base, baseada no YML em 'connection_name'
     */
    private function configuraObjetos($nomeBase='base_principal')
    {
        $this->entityManager = $this->managerRegistry->getManager($nomeBase);
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade($this->managerRegistry);
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade($this->managerRegistry);
        $this->franqueadaRepository = $this->entityManager->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->alunoRepository      = $this->entityManager->getRepository(\App\Entity\Principal\Aluno::class);
        $this->itemRepository       = $this->entityManager->getRepository(\App\Entity\Principal\Item::class);
    }

    private function configuraIdItemCobranca()
    {
        $itemORM = $this->itemRepository->findOneBy([ConstanteParametros::CHAVE_TIPO_ITEM => SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_COBRANCA]);
        if (is_null($itemORM) === true) {
            throw new \ErrorException("Nao foi possivel encontrar um tipo de item CB para geracao de ocorrencia.");
        }

        $this->idItemCobranca = $itemORM->getId();
    }

    /**
     * Retorna o usuario do sistema
     *
     * @return \App\Entity\Principal\Usuario|NULL
     */
    private function buscaUsuarioSistema()
    {
        $usuarioRepository = $this->entityManager->getRepository(\App\Entity\Principal\Usuario::class);
        $usuarioORM        = $usuarioRepository->findOneBy([ConstanteParametros::CHAVE_CPF => "SISTEMA"]);
        return $usuarioORM;
    }

    /**
     * Gera OcorrenciaAcademica para o Aluno
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $usuarioId
     * @param int $alunoId
     * @param int $itemId
     * @param string $obsevacaoOcorrencia
     * @param \App\Entity\Principal\OcorrenciaAcademica $retornoORM
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademicaParaAluno(&$mensagemErro, $franqueadaId, $usuarioId, $alunoId, $itemId, $obsevacaoOcorrencia, &$retornoORM)
    {
        $parametrosOcorrenciaAcademica = $this->ocorrenciaAcademicaFacade->gerarParametrosOcorrenciaAcademica($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia);
        $bSuccesso = empty($mensagemErro) === true;
        if ($bSuccesso === true) {
            $retornoORM = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($retornoORM) === true) || (empty($mensagemErro) === false)) {
                $bSuccesso = false;
            }
        }

        return $bSuccesso;
    }

    /**
     * Adiciona uma nova OcorrenciaAcademicaDetalhes para a OcorrenciaAcademica do aluno
     *
     * @param string $mensagemErro
     * @param int $franqueadaId
     * @param int $usuarioId
     * @param int $alunoId
     * @param int $itemId
     * @param string $obsevacaoOcorrencia
     * @param \App\Entity\Principal\OcorrenciaAcademica $ocorrenciaAcademicaORM
     *
     * @return boolean
     */
    private function criarUmNovoOcorrenciaDetalhes(&$mensagemErro, $franqueadaId, $usuarioId, $alunoId, $itemId, $obsevacaoOcorrencia, &$ocorrenciaAcademicaORM)
    {
        $parametrosOcorrenciaAcademica = $this->ocorrenciaAcademicaFacade->gerarParametrosOcorrenciaAcademica($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia);
        $bSuccesso = empty($mensagemErro) === true;
        $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
        if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
            $bSuccesso = false;
        }

        return $bSuccesso;
    }

    /**
     * Busca as configurações para cada franqueada
     *
     * @return array[][]
     */
    private function buscaArrayConfiguracoesFranqueada()
    {
        $todasFranqueadas  = $this->franqueadaRepository->findAll();
        $retornoFranqueada = [];
        foreach ($todasFranqueadas as $franqueadaORM) {
            $retornoFranqueada[] = [
                ConstanteParametros::CHAVE_FRANQUEADA            => $franqueadaORM->getId(),
                ConstanteParametros::CHAVE_DIAS_PARA_NEGATIVACAO => $franqueadaORM->getDiasParaNegativacao(),
            ];
        }

        return $retornoFranqueada;
    }

    /**
     * Busca todos os alunos por franqueada
     *
     * @param int $franqueadaId
     *
     * @return \App\Entity\Principal\Aluno[]
     */
    private function buscaAlunosPorFranqueada($franqueadaId)
    {
        return $this->alunoRepository->buscarTodosAlunosORMPorFranqueada($franqueadaId);
    }

    /**
     * Vericia se a parcela já passou pela quantidade de dias para poder se tornar negativada
     *
     * @param \DateTime $dataProrrogacao
     * @param \DateTime $dataAtual
     * @param int $diasParaPoderNegativar
     *
     * @return boolean
     */
    private function parcelaEstaNegativada($dataProrrogacao, $dataAtual, $diasParaPoderNegativar)
    {
        $bRetorno          = false;
        $diferencaInterval = $dataProrrogacao->diff($dataAtual);
        $diasDeDiferanca   = (int) $diferencaInterval->format('%a');
        if ($diasDeDiferanca >= $diasParaPoderNegativar) {
            $bRetorno = true;
        }

        return $bRetorno;
    }

    /**
     * Verifica se o aluno possui alguma conta receber negativada
     *
     * @param \App\Entity\Principal\ContaReceber[] $contasReceberDoAluno
     * @param \DateTime $dataAtual
     * @param int $diasParaPoderNegativar
     * @param \App\Entity\Principal\Usuario $usuarioSistema
     *
     * @return boolean
     */
    private function verificaContasReceberNegativadoAluno(&$contasReceberDoAluno, $dataAtual, $diasParaPoderNegativar, $usuarioSistema)
    {
        $bPossuiAlgumaContaReceberNegativado = false;
        $ocorrenciaAcademicaORM = null;
        foreach ($contasReceberDoAluno as &$contaReceberORM) {
            $titulosReceberORM = $contaReceberORM->getTituloRecebers();
            if ($this->verificaTitulosReceberNegativadoAluno($titulosReceberORM, $dataAtual, $diasParaPoderNegativar, $usuarioSistema, $ocorrenciaAcademicaORM) === true) {
                $contaReceberORM->setSituacao(SituacoesSistema::SITUACAO_NEGATIVADAS);
                $bPossuiAlgumaContaReceberNegativado = true;
            }
        }

        return $bPossuiAlgumaContaReceberNegativado;
    }

    /**
     * Verifica se existe algum tituloReceber negativado para o aluno
     *
     * @param \App\Entity\Principal\TituloReceber[] $titulosReceberORM
     * @param \DateTime $dataAtual
     * @param int $diasParaPoderNegativar
     * @param \App\Entity\Principal\Usuario $usuarioSistema
     * @param \App\Entity\Principal\OcorrenciaAcademica $retornoOcorrenciaAcademicaAluno
     *
     * @return boolean
     */
    private function verificaTitulosReceberNegativadoAluno(&$titulosReceberORM, $dataAtual, $diasParaPoderNegativar, $usuarioSistema, &$retornoOcorrenciaAcademicaAluno)
    {
        $bPossuiAlgumTituloNegativado = false;
        foreach ($titulosReceberORM as &$tituloReceberORM) {
            $franqueadaId = $tituloReceberORM->getFranqueada()->getId();
            $usuarioId    = $usuarioSistema->getId();
            $alunoId      = $tituloReceberORM->getAluno()->getId();
            $mensagemErro = "";
            if ($titulosReceberORM->getSituacao() === SituacoesSistema::SITUACAO_VENCIDAS) {
                if ($this->parcelaEstaNegativada($tituloReceberORM->getDataProrrogacao(), $dataAtual, $diasParaPoderNegativar) === true) {
                    $observacaoOcorrenciaTexto = "O aluno: " . $tituloReceberORM->getAluno()->getPessoa()->getNomeContato() . " está com a parcela " . $tituloReceberORM->getNumeroParcelaDocumento() . " do dia:" . $tituloReceberORM->getDataProrrogacao()->format("d/m/Y") . " para o contrato " . $tituloReceberORM->getContaReceber()->getContrato()->getSequenciaContrato();
                    if (is_null($retornoOcorrenciaAcademicaAluno) === true) {
                        $this->gerarOcorrenciaAcademicaParaAluno($mensagemErro, $franqueadaId, $usuarioId, $alunoId, $this->idItemCobranca, $observacaoOcorrenciaTexto, $retornoOcorrenciaAcademicaAluno);
                    }

                    $tituloReceberORM->setNegativado(true);
                    $bPossuiAlgumTituloNegativado = true;
                    $this->criarUmNovoOcorrenciaDetalhes($mensagemErro, $franqueadaId, $usuarioId, $alunoId, $this->idItemCobranca, $observacaoOcorrenciaTexto, $retornoOcorrenciaAcademicaAluno);
                    if (empty($mensagemErro) === false) {
                        throw new \ErrorException($mensagemErro);
                    }
                }
            }
        }//end foreach

        return $bPossuiAlgumTituloNegativado;
    }

    public function __construct(?string $name=null, ManagerRegistry $managerRegistry)
    {
        parent::__construct($name);

        $this->managerRegistry = $managerRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->configuraObjetos();
        $this->configuraIdItemCobranca();
        $configuracoesFranqueada = $this->buscaArrayConfiguracoesFranqueada();
        $usuarioSistema          = $this->buscaUsuarioSistema();
        $alunosNegativados       = 0;
        $dataAtual = new \DateTime();
        foreach ($configuracoesFranqueada as $configuracaoPorFranqueada) {
            $alunosPorFranqueada = $this->buscaAlunosPorFranqueada($configuracaoPorFranqueada[ConstanteParametros::CHAVE_FRANQUEADA]);
            foreach ($alunosPorFranqueada as &$alunoORM) {
                $contasReceberDoAluno = $alunoORM->getAlunoContaReceber();
                if ($this->verificaContasReceberNegativadoAluno($contasReceberDoAluno, $dataAtual, $configuracaoPorFranqueada[ConstanteParametros::CHAVE_DIAS_PARA_NEGATIVACAO], $usuarioSistema) === true) {
                    $alunoORM->getPessoa()->setNegativado(true);
                    $alunosNegativados++;
                }
            }
        }

        $this->entityManager->flush();
        $io->success('CRON executada com sucesso.\nTotal alunos negativados:' . $alunosNegativados);
    }


}
