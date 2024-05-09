<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AtividadeExtraBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\ContaReceberFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;

/**
 *
 * @author Luiz A Costa
 */
class AtividadeExtraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AtividadeExtraRepository
     */
    private $atividadeExtraRepository;

    /**
     *
     * @var \App\BO\Principal\AtividadeExtraBO
     */
    private $atividadeExtraBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->atividadeExtraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AtividadeExtra::class);
        $this->atividadeExtraBO         = new AtividadeExtraBO(self::getEntityManager());
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
        $retornoRepositorio = $this->atividadeExtraRepository->filtrarAtividadeExtraPorPagina($parametros);
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
        $objetoORM = $this->atividadeExtraRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Atividade Extra não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\AtividadeExtra
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->atividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
            $listaFuncionariosORM = $parametros[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO];
            unset($parametros[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AtividadeExtra::class, true, $parametros);
            if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
            }

            foreach ($listaFuncionariosORM as $funcionarioORM) {
                $objetoORM->addResponsaveisExecucacao($funcionarioORM);
            }

            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     * @param \App\Entity\Principal\AtividadeExtra $atividadeExtraORM
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[], &$atividadeExtraORM=null)
    {
        $objetoORM = $this->atividadeExtraRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AtividadeExtra não encontrado na base de dados.";
        } else {
            $parametros[ConstanteParametros::CHAVE_USUARIO] = $objetoORM->getUsuarioSolicitante()->getId();
            if ($this->atividadeExtraBO->podeCriar($parametros, $mensagemErro) === true) {
                $listaFuncionariosORM = $parametros[ConstanteParametros::CHAVE_RESPONSAVEIS_PELA_EXECUCAO];
                if ((bool) $parametros[ConstanteParametros::CHAVE_CANCELAMENTO] === true) {
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CANCELAMENTO);
                }

                if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
                    unset($parametros[ConstanteParametros::CHAVE_CONCLUIDO]);
                    unset($parametros[ConstanteParametros::CHAVE_SITUACAO]);
                }

                $responsaveisExecucaoAtual = $objetoORM->getResponsaveisExecucacao();
                $totaisAtual = $responsaveisExecucaoAtual->count();
                for ($i = 0;$i < $totaisAtual; $i++) {
                    $tempObj = $responsaveisExecucaoAtual->get($i);
                    $objetoORM->removeResponsaveisExecucacao($tempObj);
                }

                foreach ($listaFuncionariosORM as $funcionarioORM) {
                    $objetoORM->addResponsaveisExecucacao($funcionarioORM);
                }

                $listaDeConvidados  = $objetoORM->getConvidadoAtividadeExtras();
                $numeroDeConvidados = $listaDeConvidados->count();
                for ($i = 0;$i < $numeroDeConvidados; $i++) {
                    $tempObj = $listaDeConvidados->get($i);
                    $objetoORM->removeConvidadoAtividadeExtra($tempObj);
                }

                unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
                self::getFctHelper()->setParams($parametros, $objetoORM);

                $atividadeExtraORM = $objetoORM;
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Seta uma atividade extra como concluída no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param \App\Entity\Principal\AtividadeExtra $atividadeExtraORM
     *
     * @return boolean
     */
    public function concluir(&$mensagemErro, $id, &$atividadeExtraORM=null)
    {
        $atividadeExtraORM = $this->atividadeExtraRepository->find($id);

        if (is_null($atividadeExtraORM) === true) {
            $mensagemErro = "AtividadeExtra não encontrado na base de dados.";
            return false;
        }

        $atividadeExtraORM->setSituacao(SituacoesSistema::SITUACAO_CONCLUIDA);
        $gerarConta = $atividadeExtraORM->getIsenta() === false;

        if ($gerarConta === true) {
            $contaReceberFacade = new ContaReceberFacade(self::getManagerRegistry());
            if (($atividadeExtraORM->getValor() > 0) === false) {
                $mensagemErro = "Valor não preenchido para atividade extra";
                return false;
            }
        }

        $ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());

        $alunosAtividadeExtra = $atividadeExtraORM->getAlunoAtividadeExtras();
        $franqueadaId         = $atividadeExtraORM->getFranqueada()->getId();
        $usuarioId            = $atividadeExtraORM->getUsuarioSolicitante()->getId();
        $itemId = $atividadeExtraORM->getItem()->getId();
        $valor  = $atividadeExtraORM->getValor();
        $obsevacaoOcorrencia = $atividadeExtraORM->getDescricaoAtividade();
        if ($obsevacaoOcorrencia === null) {
            $obsevacaoOcorrencia = "";
        }

        foreach ($alunosAtividadeExtra as $alunoAtividadeExtra) {
            // gerarContaReceber
            $alunoId = $alunoAtividadeExtra->getAluno()->getId();
            if ($gerarConta === true) {
                $formaCobrancaId        = $atividadeExtraORM->getFormaCobranca()->getId();
                $parametrosContaReceber = $contaReceberFacade->gerarParametrosContaReceberTituloReceber($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $valor, $formaCobrancaId, $itemId);
                $contaReceberORM        = $contaReceberFacade->criar($mensagemErro, $parametrosContaReceber);
                if (is_null($contaReceberORM) === true || empty($mensagemErro) === false) {
                    return false;
                }

                $atividadeExtraORM->addContaReceber($contaReceberORM);
                $contaReceberORM->addAtividadeExtra($atividadeExtraORM);
            }

            $parametrosOcorrenciaAcademica = $ocorrenciaAcademicaFacade->gerarParametrosOcorrenciaAcademica($mensagemErro, $franqueadaId, $alunoId, $usuarioId, $itemId, $obsevacaoOcorrencia);
            if (empty($mensagemErro) === false) {
                return false;
            }

            $ocorrenciaAcademicaORM = $ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                return false;
            }

            $ocorrenciaAcademicaDetalhesORM = $ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
            if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                return false;
            }
        }//end foreach

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
    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->atividadeExtraRepository->prepararDadosRelatorio($parametros);
    }

    public function fetch($parametros) {
        return $this->atividadeExtraRepository->fetch($parametros);
    }

    public function gerarDadosRelatorioAtividadesExtras($filtros)
    {
        return $this->atividadeExtraRepository->buscarDadosRelatorioAtividadesExtras($filtros);
    }

}
