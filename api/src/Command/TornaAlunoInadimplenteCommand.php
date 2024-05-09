<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;

class TornaAlunoInadimplenteCommand extends Command
{
    protected static $defaultName = 'cron:tornar-aluno-inadimplente';

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
     * Configura a base a ser pega as informações e o WorkflowBO
     *
     * @param string $nomeBase Nome da configuração da base, baseada no YML em 'connection_name'
     */
    private function configuraObjetos($nomeBase='base_principal')
    {
        $this->entityManager = $this->managerRegistry->getManager($nomeBase);
    }

    /**
     * Busca a configuração de inadimplencia
     *
     * @todo Conforme conversa com Juscelino e Marcelo, futuramente será avaliado se virá de uma tabela
     *
     * @return number
     */
    private function buscaConfiguracaoDiasInadimplente()
    {
        return 10;
    }

    /**
     * Verifica se o periodo passou da quantidade de dias permitido para inadimplencia
     *
     * @param \DateTime $dataProrrogacao
     * @param int $diasInadimplentes
     *
     * @return boolean
     */
    private function verificaEstaInadimplente(\DateTime $dataProrrogacao, int $diasInadimplentes)
    {
        $dataAtual    = new \DateTime();
        $timeInterval = $dataProrrogacao->diff($dataAtual);
        $diasNaoPagos = (int) $timeInterval->format("%a");
        if ($diasNaoPagos > $diasInadimplentes) {
            return true;
        }

        return false;
    }

    /**
     * Busca todos os titulos receber com situacao pendente
     *
     * @return \App\Entity\Principal\Aluno[]
     */
    private function buscaAlunosComTituloReceberPendente()
    {
        $alunoRepository = $this->entityManager->getRepository(\App\Entity\Principal\Aluno::class);
        return $alunoRepository->buscaAlunosTitulosPendentes();
    }

    /**
     * Configura Inadimplencia para o Aluno
     *
     * @param \App\Entity\Principal\Aluno $alunoORM
     * @param int $diasInadimplente
     */
    private function configuraInadimplencia(\App\Entity\Principal\Aluno &$alunoORM, int $diasInadimplente)
    {
        $bAlunoInadimplente = false;
        $titulosReceber     = $alunoORM->getAlunoTituloRecebers();
        foreach ($titulosReceber as &$tituloReceberORM) {
            if ($this->verificaEstaInadimplente($tituloReceberORM->getDataProrrogacao(), $diasInadimplente) === true) {
                $bAlunoInadimplente = true;
            }
        }

        if ($bAlunoInadimplente === true) {
            $pessoaORM = $alunoORM->getPessoa();
            $pessoaORM->setInadimplente(true);
        }
    }

    public function __construct(?string $name=null, ManagerRegistry $managerRegistry)
    {
        parent::__construct($name);

        $this->managerRegistry = $managerRegistry;
    }

    protected function configure()
    {
        $this->setDescription("Executa a checagem de alunos que possuem parcelas atrasadas, tornando eles em inadimplentes;");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->configuraObjetos();
        $configuracaoDiasInadimplentes = $this->buscaConfiguracaoDiasInadimplente();
        $alunosTituloReceberPendentes  = $this->buscaAlunosComTituloReceberPendente();
        foreach ($alunosTituloReceberPendentes as &$alunoORM) {
            $this->configuraInadimplencia($alunoORM, $configuracaoDiasInadimplentes);
        }

        $this->entityManager->flush();
        $io->success('Executado com sucesso.');
    }


}
