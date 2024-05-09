<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Entity\Log\Log;
use App\Facade\Log\LogFacade;
use App\Entity\Log\ConfiguracoesTabelaLog;
use Doctrine\ORM\PersistentCollection;

class LogORMEventSubscriber implements EventSubscriber
{

    /**
     *
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $managerRegistry;

    private const DADOS_ANTERIORES  = "dados_anteriores";
    private const DADOS_ATUAIS      = "dados_atuais";
    private const TABELA_MODIFICADA = "tabela";

    /**
     * Monta um array de informacoes do usuario extraindo dados da sessao
     *
     * @return array
     */
    private function retornaUsuarioInfo()
    {
        return [
            ConstanteParametros::CHAVE_USUARIO    => $_SESSION[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_FRANQUEADA => $_SESSION[ConstanteParametros::CHAVE_FRANQUEADA],
            ConstanteParametros::CHAVE_IP_ORIGEM  => $_SESSION[ConstanteParametros::CHAVE_IP_ORIGEM],
        ];
    }

    /**
     * Verifica se deve permitir o log da tabela especificada
     *
     * @param string $nomeTabela
     * @param \Doctrine\ORM\EntityManager $baseLog
     *
     * @return boolean
     */
    private function tabelaGeraLog($nomeTabela, $baseLog)
    {
        $configuracoesTabelaLogRepository = $baseLog->getRepository(ConfiguracoesTabelaLog::class);
        $configuracao = $configuracoesTabelaLogRepository->findOneBy(["nome_tabela" => $nomeTabela]);
        if (is_null($configuracao) === true) {
            return false;
        }

        return $configuracao->getAtivo();
    }

    /**
     * Monta os dados antes/depois para o log
     *
     * @param array $camposAlteradosArray
     *
     * @return array
     */
    private function montaArrayDadosAnterioresXAtuais($camposAlteradosArray)
    {
        $dados = [
            self::DADOS_ANTERIORES,
            self::DADOS_ATUAIS,
        ];
        foreach ($camposAlteradosArray as $campo => $antesDepoisArray) {
            $dados[self::DADOS_ANTERIORES][$campo] = $antesDepoisArray[0];
            $dados[self::DADOS_ATUAIS][$campo]     = $antesDepoisArray[1];
        }

        return $dados;
    }

    /**
     * Monta os parametros para criacao do log
     *
     * @param array $userInfo Que contem as informacoes da sessao do usuario
     * @param string $tabela Nome da tabel
     * @param string $infoEvento Informacao do evento do Log
     * @param array $dadosAnteriores Array dos dados anteriores a alteracao
     * @param array $dadosAtuais Array dos dados posteriores a alteracao
     * @param string $tipoEvento Constante de Log
     *
     * @return array
     */
    private function montaParametrosLog($userInfo, $tabela=null, $infoEvento=null, $dadosAnteriores=null, $dadosAtuais=null, $tipoEvento='C')
    {
        $parametros = [
            ConstanteParametros::CHAVE_FRANQUEADA  => $userInfo[ConstanteParametros::CHAVE_FRANQUEADA],
            ConstanteParametros::CHAVE_USUARIO     => $userInfo[ConstanteParametros::CHAVE_USUARIO],
            ConstanteParametros::CHAVE_TIPO_EVENTO => $tipoEvento,
            ConstanteParametros::CHAVE_IP_ORIGEM   => $userInfo[ConstanteParametros::CHAVE_IP_ORIGEM],
        ];
        if (is_null($infoEvento) === false) {
            $parametros[ConstanteParametros::CHAVE_INFO_EVENTO] = $infoEvento;
        }

        if (is_null($dadosAnteriores) === false) {
            $parametros[self::DADOS_ANTERIORES] = json_encode($dadosAnteriores);
        }

        if (is_null($dadosAtuais) === false) {
            $parametros[self::DADOS_ATUAIS] = json_encode($dadosAtuais);
        }

        if (is_null($tabela) === false) {
            $parametros[self::TABELA_MODIFICADA] = $tabela;
        }

        return $parametros;
    }

    /**
     * Cria um log para cada entidade atualizada
     *
     * @param array $entidades
     * @param \Doctrine\ORM\UnitOfWork $unitOfWork
     * @param array $userInfo
     * @param \Doctrine\ORM\EntityManager $basePrincipal
     * @param \Doctrine\ORM\EntityManager $baseLog
     */
    private function atualizarEntidades($entidades, $unitOfWork, $userInfo, $basePrincipal, $baseLog)
    {
        foreach ($entidades as $entidade) {
            $nomeTabela = $basePrincipal->getClassMetadata(get_class($entidade))->getTableName();
            if ($this->tabelaGeraLog($nomeTabela, $baseLog) === true) {
                $camposAlteradosArray = $unitOfWork->getEntityChangeSet($entidade);
                if (count($camposAlteradosArray) > 0) {
                    $dadosLog      = $this->montaArrayDadosAnterioresXAtuais($camposAlteradosArray);
                    $parametrosLog = $this->montaParametrosLog($userInfo, $nomeTabela, null, $dadosLog[self::DADOS_ANTERIORES], $dadosLog[self::DADOS_ATUAIS], LogFacade::$LOG_UPDATE);
                    $logORM        = \App\Factory\GeneralORMFactory::criar(Log::class, true, $parametrosLog);
                    $baseLog->persist($logORM);
                    $baseLog->flush();
                }
            }
        }
    }

    /**
     * Cria um log para cada entidade inserida
     *
     * @param array $entidades
     * @param \Doctrine\ORM\UnitOfWork $unitOfWork
     * @param array $usuarioInfo
     * @param \Doctrine\ORM\EntityManager $basePrincipal
     * @param \Doctrine\ORM\EntityManager $baseLog
     */
    private function inserirEntidade($entidades, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog)
    {
        foreach ($entidades as $entidade) {
            $nomeTabela = $basePrincipal->getClassMetadata(get_class($entidade))->getTableName();
            if ($this->tabelaGeraLog($nomeTabela, $baseLog) === true) {
                $camposAlteradosArray = $unitOfWork->getEntityChangeSet($entidade);
                if (count($camposAlteradosArray) > 0) {
                    $dadosLog      = $this->montaArrayDadosAnterioresXAtuais($camposAlteradosArray);
                    $parametrosLog = $this->montaParametrosLog($usuarioInfo, $nomeTabela, null, null, $dadosLog[self::DADOS_ATUAIS], LogFacade::$LOG_CREATE);
                    $logORM        = \App\Factory\GeneralORMFactory::criar(Log::class, true, $parametrosLog);
                    $baseLog->persist($logORM);
                    $baseLog->flush();
                }
            }
        }
    }

    /**
     * Cria um log para cada entidade removida no sistema
     *
     * @param array $entidades
     * @param \Doctrine\ORM\UnitOfWork $unitOfWork
     * @param array $usuarioInfo
     * @param \Doctrine\ORM\EntityManager $basePrincipal
     * @param \Doctrine\ORM\EntityManager $baseLog
     */
    private function removerEntidade($entidades, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog)
    {
        foreach ($entidades as $entidade) {
            $nomeTabela = $basePrincipal->getClassMetadata(get_class($entidade))->getTableName();
            if ($this->tabelaGeraLog($nomeTabela, $baseLog) === true) {
                $parametrosLog = $this->montaParametrosLog($usuarioInfo, $nomeTabela, null, null, null, LogFacade::$LOG_DELETE);
                $logORM        = \App\Factory\GeneralORMFactory::criar(Log::class, true, $parametrosLog);
                $baseLog->persist($logORM);
                $baseLog->flush();
            }
        }
    }

    /**
     * Cria um log para cada entidade relacionada inserida no sistema
     *
     * @param PersistentCollection[] $entidades
     * @param \Doctrine\ORM\UnitOfWork $unitOfWork
     * @param array $usuarioInfo
     * @param \Doctrine\ORM\EntityManager $basePrincipal
     * @param \Doctrine\ORM\EntityManager $baseLog
     */
    private function atualizarRelacionamentos($entidades, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog)
    {
        foreach ($entidades as $persistCollection) {
            $dados            = $persistCollection->getInsertDiff();
            $nomeTabelaOrigem = $basePrincipal->getClassMetadata(get_class($persistCollection->getOwner()))->getTableName();
            foreach ($dados as $classe) {
                $nomeTabelaFilha = $basePrincipal->getClassMetadata(get_class($classe))->getTableName();
                if ($this->tabelaGeraLog($nomeTabelaOrigem, $baseLog) === true) {
                    $informacaoAtualizada = [
                        "tabela_alterada" => $nomeTabelaOrigem,
                        "tabela_filha"    => $nomeTabelaFilha,
                        "id"              => $classe->getId(),
                    ];
                    $parametrosLog        = $this->montaParametrosLog($usuarioInfo, $nomeTabelaOrigem, null, null, $informacaoAtualizada, LogFacade::$LOG_CREATE);
                    $logORM = \App\Factory\GeneralORMFactory::criar(Log::class, true, $parametrosLog);
                    $baseLog->persist($logORM);
                    $baseLog->flush();
                }
            }
        }
    }

    /**
     * Cria um log para cada entidade relacionada removida no sistema
     *
     * @param PersistentCollection[] $entidades
     * @param \Doctrine\ORM\UnitOfWork $unitOfWork
     * @param array $usuarioInfo
     * @param \Doctrine\ORM\EntityManager $basePrincipal
     * @param \Doctrine\ORM\EntityManager $baseLog
     */
    private function removerRelacionamentos($entidades, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog)
    {
        foreach ($entidades as $persistCollection) {
            $dados            = $persistCollection->getDeleteDiff();
            $nomeTabelaOrigem = $basePrincipal->getClassMetadata(get_class($persistCollection->getOwner()))->getTableName();
            foreach ($dados as $classe) {
                $nomeTabelaFilha = $basePrincipal->getClassMetadata(get_class($classe))->getTableName();
                if ($this->tabelaGeraLog($nomeTabelaOrigem, $baseLog) === true) {
                    $informacaoAtualizada = [
                        "tabela_alterada" => $nomeTabelaOrigem,
                        "tabela_filha"    => $nomeTabelaFilha,
                        "id"              => $classe->getId(),
                    ];
                    $parametrosLog        = $this->montaParametrosLog($usuarioInfo, $nomeTabelaOrigem, null, null, $informacaoAtualizada, LogFacade::$LOG_DELETE);
                    $logORM = \App\Factory\GeneralORMFactory::criar(Log::class, true, $parametrosLog);
                    $baseLog->persist($logORM);
                    $baseLog->flush();
                }
            }
        }
    }

    function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        if ($args->getEntityManager()->getConnection()->getDatabase() === getenv("DATABASE_PRINCIPAL_NAME")) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                $usuarioInfo   = $this->retornaUsuarioInfo();
                $basePrincipal = $args->getEntityManager();
                $baseLog       = $this->managerRegistry->getManager("base_log");
                $unitOfWork    = $basePrincipal->getUnitOfWork();
                $entidadesParaAtualizar     = $unitOfWork->getScheduledEntityUpdates();
                $entidadesParaRemover       = $unitOfWork->getScheduledEntityDeletions();
                $relacionamentosAtualizacao = $unitOfWork->getScheduledCollectionUpdates();
                $relacionamentosRemover     = $unitOfWork->getScheduledCollectionDeletions();
                $entidadesParaInserir       = $unitOfWork->getScheduledEntityInsertions();
                $this->inserirEntidade($entidadesParaInserir, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                $this->atualizarEntidades($entidadesParaAtualizar, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                $this->removerEntidade($entidadesParaRemover, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                if (count($relacionamentosAtualizacao) > 0) {
                    $this->atualizarRelacionamentos($relacionamentosAtualizacao, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                    $this->removerRelacionamentos($relacionamentosAtualizacao, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                }

                if (count($relacionamentosRemover) > 0) {
                    $this->removerRelacionamentos($relacionamentosRemover, $unitOfWork, $usuarioInfo, $basePrincipal, $baseLog);
                }

                session_destroy();
            }//end if
        }//end if
    }


}
