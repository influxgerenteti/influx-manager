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
// use App\Helper\EmailHelper;
use App\Helper\SituacoesSistema;

class LembrancaCobrancaAlunoCommand extends Command
{
    protected static $defaultName = 'cron:lembranca-cobranca-aluno';

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
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository = null;

    // /**
    //  *
    //  * @var \App\Helper\EmailHelper
    //  */
    // private $emailHelper = null;

    protected function configure()
    {
        $this->setDescription('O envio de e-mail para lembrar o aluno, que existe parcelas perto do vencimento.');
    }

    /**
     * Configura a base a ser pega as informações
     *
     * @param string $nomeBase Nome da configuração da base, baseada no YML em 'connection_name'
     */
    private function configuraObjetos($nomeBase='base_principal')
    {
        $this->entityManager   = $this->managerRegistry->getManager($nomeBase);
        $this->alunoRepository = $this->entityManager->getRepository(\App\Entity\Principal\Aluno::class);
        // $this->emailHelper     = new EmailHelper($this->managerRegistry);
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
     * Retorna a lista de franqueadas ativas
     *
     * @return \App\Entity\Principal\Franqueada[]
     */
    private function retornaListaDeFranqueadaAtiva()
    {
        $franqueadaRepository = $this->entityManager->getRepository(\App\Entity\Principal\Franqueada::class);
        return $franqueadaRepository->findBy([ConstanteParametros::CHAVE_SITUACAO => SituacoesSistema::SITUACAO_ATIVO]);
    }

    /**
     * Busca lista de alunos por franqueada
     *
     * @param int $franqueadaId
     *
     * @return \App\Entity\Principal\Aluno[]
     */
    private function retornaListaDeAlunosPorFranqueada($franqueadaId)
    {
        return $this->alunoRepository->buscarTodosAlunosORMPorFranqueada($franqueadaId);
    }

    /**
     * Parcela está proxima de vencer
     *
     * @param \DateTime $dataProrrogacao
     * @param \DateTime $dataAtual
     * @param int $diasRestantesParaDisparoLembrete
     *
     * @return boolean
     */
    private function parcelaEstaProximaDeVencer($dataProrrogacao, $dataAtual, $diasRestantesParaDisparoLembrete)
    {
        $bRetorno          = false;
        $diferencaInterval = $dataProrrogacao->diff($dataAtual);
        $diasDeDiferanca   = (int) $diferencaInterval->format('%a');
        if ($diasDeDiferanca < $diasRestantesParaDisparoLembrete) {
            $bRetorno = true;
        }

        return $bRetorno;
    }

    /**
     * Verifica os titulos do aluno
     *
     * @param \App\Entity\Principal\TituloReceber[] $titulosReceberORM
     * @param \DateTime $dataAtual
     * @param int $diasParaEnvioDeLembrete
     * @param \App\Entity\Principal\Usuario $usuarioSistema
     */
    private function verificaTitulosDoAluno(&$titulosReceberORM, $dataAtual, $diasParaEnvioDeLembrete, $usuarioSistema)
    {
        foreach ($titulosReceberORM as &$tituloReceberORM) {
            if ($tituloReceberORM->getSituacao() === SituacoesSistema::SITUACAO_PENDENTE) {
                if ($this->parcelaEstaProximaDeVencer($tituloReceberORM->getDataProrrogacao(), $dataAtual, $diasParaEnvioDeLembrete) === true) {
                    $titulosReceberORM->setLembreteEnvio(true);
                    $nomePessoa        = $titulosReceberORM->getAluno()->getPessoa()->getNomeContato();
                    $emailPreferencial = $titulosReceberORM->getAluno()->getPessoa()->getEmailPreferencial();
                    $mensagem          = "Sua parcela está proxima de vencer, favor realizar o pagamento dentro do prazo, para que evite transtornos.";
                    $mensagem         .= "<br>";
                    $mensagem         .= "Informações da parcela:<br>";
                    $mensagem         .= "Valor: " . money_format('%n', $titulosReceberORM->getValorSaldoDevedor()) . "<br>";
                    $mensagem         .= "Data Vencimento: " . $titulosReceberORM->getDataProrrogacao()->format("d/m/Y") . "<br>";
                    $this->emailHelper->setPara([$emailPreferencial => $nomePessoa]);
                    $this->emailHelper->setAssunto("Lembrete de vencimento de Parcela");
                    $this->emailHelper->setBody($mensagem);
                    $this->emailHelper->enviarMensagem($usuarioSistema->getId());
                }
            }
        }
    }

    /**
     * Verifica conta receber do aluno
     *
     * @param \App\Entity\Principal\ContaReceber[] $contasReceberORM
     * @param \DateTime $dataAtual
     * @param int $diasParaEnvioDeLembrete
     * @param \App\Entity\Principal\Usuario $usuarioSistema
     */
    private function verificaParcelasDoAlunoContaReceber(&$contasReceberORM, $dataAtual, $diasParaEnvioDeLembrete, $usuarioSistema)
    {
        foreach ($contasReceberORM as &$contaReceberORM) {
            $titulosReceberORM = $contaReceberORM->getTituloRecebers();
            $this->verificaTitulosDoAluno($titulosReceberORM, $dataAtual, $diasParaEnvioDeLembrete, $usuarioSistema);
        }
    }

    public function __construct(?string $name=null, ManagerRegistry $managerRegistry)
    {
        parent::__construct($name);
        setlocale(LC_MONETARY, "pt_BR", "ptb");
        $this->managerRegistry = $managerRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->configuraObjetos();
        $dataAtual          = new \DateTime();
        $usuarioSistema     = $this->buscaUsuarioSistema();
        $listaDeFranqueadas = $this->retornaListaDeFranqueadaAtiva();
        foreach ($listaDeFranqueadas as $franqueadaORM) {
            $diasParaEnvioDeLembrete     = $franqueadaORM->getDiasLembreteCobranca();
            $listaDeAlunosParaFranqueada = $this->retornaListaDeAlunosPorFranqueada($franqueadaORM->getId());
            foreach ($listaDeAlunosParaFranqueada as &$alunoORM) {
                $contasReceberAluno = $alunoORM->getAlunoContaReceber();
                $this->verificaParcelasDoAlunoContaReceber($contasReceberAluno, $dataAtual, $diasParaEnvioDeLembrete, $usuarioSistema);
            }
        }

        $this->entityManager->flush();
        $io->success('CRON executada com sucesso!');
    }


}
