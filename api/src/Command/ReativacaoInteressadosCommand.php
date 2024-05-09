<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\FollowupComercial;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\WorkflowBO;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReativacaoInteressadosCommand extends Command
{
    protected static $defaultName = 'cron:reativacao-interessado';

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
     * @var \App\BO\Principal\WorkflowBO
     */
    private $workflowBO = null;

    protected function configure()
    {
        $this->setDescription('Executa a reativacao de interessados, partindo do campo "dias_reativacao_interessado" configurado na ParametrosFranqueadora.');
    }

    /**
     * Configura a base a ser pega as informações e o WorkflowBO
     *
     * @param string $nomeBase Nome da configuração da base, baseada no YML em 'connection_name'
     */
    private function configuraObjetos($nomeBase='base_principal')
    {
        $this->entityManager = $this->managerRegistry->getManager($nomeBase);
        $this->workflowBO    = new WorkflowBO($this->entityManager);
    }

    /**
     * Busca os dias para a reativação do interessado
     *
     * @return int
     */
    private function retornaDiasConfiguradosTabelaParametrosFranqueadora()
    {
        $parametrosFranqueadoraRepository = $this->entityManager->getRepository(\App\Entity\Principal\ParametrosFranqueadora::class);
        $parametrosFranqueadoraORM        = $parametrosFranqueadoraRepository->find(1);
        return $parametrosFranqueadoraORM->getDiasReativacaoInteressado();
    }

    /**
     * Realiza a verificação baseada se a data do registro, passou o periodo configurado da base
     *
     * @param \DateTime $dataMatriculaPerdida
     * @param int $diasConfigurados
     *
     * @return boolean
     */
    private function verificaDataCadastroPassouQuantidadeDiasConfigurados(\DateTime $dataMatriculaPerdida, int $diasConfigurados)
    {
        $dataAtualServidor   = new \DateTime();
        $dateTimeInterval    = $dataMatriculaPerdida->diff($dataAtualServidor);
        $quantosDiasPassaram = (int) $dateTimeInterval->format("%a");
        if ($quantosDiasPassaram > $diasConfigurados) {
            return true;
        }

        return false;
    }

    /**
     * Cria um followUp comercial para informar que o Interessado foi reativado após X dias inativo
     *
     * @param \App\Entity\Principal\Usuario $usuarioSistema
     * @param \App\Entity\Principal\Interessado $interessado
     * @param int $diasConfigurados
     *
     * @return \ErrorException|NULL
     */
    private function criaFollowUpComercialReativacao(\App\Entity\Principal\Usuario $usuarioSistema, \App\Entity\Principal\Interessado &$interessado, int $diasConfigurados)
    {
        try {
            $dataReativacao = new \DateTime();
            $data           = $dataReativacao->format("d/m/Y H:m:i");
            $mensagem       = "[SISTEMA]" . $usuarioSistema->getNome() . ": Interessado reativado após periodo de " . $diasConfigurados . " dia(s). Data de reativação:" . $data;
            $followUpComercial = new FollowupComercial();
            $followUpComercial->setUsuario($usuarioSistema);
            $followUpComercial->setInteressado($interessado);
            $followUpComercial->setFollowup($mensagem);
            $this->entityManager->persist($followUpComercial);
        } catch (\Exception $e) {
            $msgErro = "\nNao foi possivel inserir o registro de followup_comercial.<br>Erro:" . $e->getMessage();
            throw new \ErrorException($msgErro);
        }
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
     * Retorna todos os interessados com a Situação Marcada como "Perdido"
     *
     * @return \App\Entity\Principal\Interessado[]
     */
    private function buscaTodosInteressadosPerdidos()
    {
        $interesadoRepository = $this->entityManager->getRepository(\App\Entity\Principal\Interessado::class);
        return $interesadoRepository->findBy([ConstanteParametros::CHAVE_SITUACAO => SituacoesSistema::SITUACAO_PERDIDO]);
    }

    /**
     * Altera a situacao para aberto e volta o fluxo de workflow caso tenha ou não telefone cadastrado.
     *
     * @param \App\Entity\Principal\Interessado $interessadoORM
     *
     * @return NULL|\Exception
     */
    private function alteraSituacaoEWorkflow(\App\Entity\Principal\Interessado &$interessadoORM)
    {
        $interessadoORM->setSituacao(SituacoesSistema::SITUACAO_ABERTO);
        $interessadoORM->setDataMatriculaPerdida(null);
        $parametrosWorkflow = [
            ConstanteParametros::CHAVE_TELEFONE_CONTATO    => $interessadoORM->getTelefoneContato(),
            ConstanteParametros::CHAVE_TELEFONE_SECUNDARIO => $interessadoORM->getTelefoneSecundario(),
            ConstanteParametros::CHAVE_WORKFLOW            => null,
        ];
        if ($this->workflowBO->verificaWorkflowParaAplicar($parametrosWorkflow) === true) {
            $interessadoORM->setWorkflow($parametrosWorkflow[ConstanteParametros::CHAVE_WORKFLOW]);
            $interessadoORM->setWorkflowAcao(null);
            return null;
        }

        throw new \Exception("Nao foi possivel configurar o workflow, pois nao existe workflow de telefone ou sem telefone cadastrado na base.");
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
        $diasConfigurados = $this->retornaDiasConfiguradosTabelaParametrosFranqueadora();
        $usuarioSistema   = $this->buscaUsuarioSistema();
        $interessadosComStatusPerdido = $this->buscaTodosInteressadosPerdidos();
        $interessadosReativados       = 0;
        foreach ($interessadosComStatusPerdido as &$interessadoORM) {
            if ($this->verificaDataCadastroPassouQuantidadeDiasConfigurados($interessadoORM->getDataMatriculaPerdida(), $diasConfigurados) === true) {
                $this->alteraSituacaoEWorkflow($interessadoORM);
                $this->criaFollowUpComercialReativacao($usuarioSistema, $interessadoORM, $diasConfigurados);
                $interessadosReativados++;
            }
        }

        $this->entityManager->flush();
        $io->success('CRON executada com sucesso.\nTotal Interessados reativados:' . $interessadosReativados);
    }


}
