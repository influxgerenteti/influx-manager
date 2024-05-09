<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\AlunoDiarioPersonalBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;
use App\Facade\Principal\OcorrenciaAcademicaDetalhesFacade;
use App\Facade\Principal\OcorrenciaAcademicaFacade;
use DateTime;

/**
 *
 * @author Luiz A Costa
 */
class DiarioPersonalFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\AgendamentoPersonalRepository
     */
    private $agendamentoPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoDiarioPersonalRepository
     */
    private $alunoDiarioPersonalRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrenciaRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoDiarioPersonalBO
     */
    private $alunoDiarioPersonalBO;

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
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->contratoRepository            = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->agendamentoPersonalRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AgendamentoPersonal::class);
        $this->alunoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $this->alunoDiarioPersonalRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoDiarioPersonal::class);
        $this->usuarioRepository         = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->tipoOcorrenciaRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoOcorrencia::class);
        $this->alunoDiarioPersonalBO     = new AlunoDiarioPersonalBO(self::getEntityManager());
        $this->ocorrenciaAcademicaFacade = new OcorrenciaAcademicaFacade($managerRegistry);
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade($managerRegistry);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param int $contratoId Id do contrato
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function buscarDiarioPersonal($contratoId, $parametros)
    {
        return $this->contratoRepository->buscarDiarioPersonal($contratoId, $parametros);
    }

    /**
     * Busca as avaliacoes por contrato
     *
     * @param int $contratoId
     * @param array $parametros Parametros da busca
     *
     * @return array|NULL
     */
    public function buscarAvaliacoesPorContratoId($contratoId, $parametros)
    {
        return $this->alunoRepository->buscaAvaliacoesPorContratoId($contratoId, $parametros);
    }

    /**
     * Busca historico do diario por ContratoId
     *
     * @param int $contratoId
     *
     * @return array
     */
    public function buscarHistoricoPersonalPorContrato($contratoId)
    {
        return $this->agendamentoPersonalRepository->buscarHistoricoPersonalPorContrato($contratoId);
    }

    /**
     * Buscar as Licoes aplicadas para o contrato
     *
     * @param int $contratoId
     *
     * @return array|NULL
     */
    public function buscarLicoesAplicadasPorContrato($contratoId)
    {
        return $this->agendamentoPersonalRepository->buscarLicoesAplicadasPorContrato($contratoId);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->alunoDiarioPersonalBO->podeCriar($parametros, $mensagemErro) === true) {
            $dataAtual = new DateTime($parametros[ConstanteParametros::CHAVE_DATA_AULA]);
            $dataAtualComHorarioAnterior = $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]->getInicio();
            $dataAtualComHorarioAnterior->setDate($dataAtual->format('Y'), $dataAtual->format('m'), $dataAtual->format('d'));

            $parametros[ConstanteParametros::CHAVE_DATA_AULA] = $dataAtualComHorarioAnterior;
            $parametros[ConstanteParametros::CHAVE_AGENDAMENTO_PERSONAL]->setFinalizado(true);
            $licaosORM = $parametros[ConstanteParametros::CHAVE_LICAOS];
            unset($parametros[ConstanteParametros::CHAVE_LICAOS]);
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunoDiarioPersonal::class, true, $parametros);
            foreach ($licaosORM as $licaoORM) {
                $objetoORM->addAlunoDiarioPersonalLicao($licaoORM);
            }

            $livroORM    = $objetoORM->getLivro();
            $contratoORM = $objetoORM->getAgendamentoPersonal()->getContrato();
            $contratoORM->setLivro($livroORM);
            $creditosPersonal = $contratoORM->getCreditosPersonal();
            if (is_null($creditosPersonal) === true) {
                $mensagemErro = "Não foi encontrado registro de crédito personal para o contrato informado.";
                return null;
            }

            if ($creditosPersonal->getSaldo() < 1) {
                $mensagemErro = "Não há saldo suficiente para lançar a aula no contrato atual.";
                return null;
            }

            $novoSaldo = $creditosPersonal->getSaldo() - 1;
            $creditosPersonal->setSaldo($novoSaldo);
            if ($novoSaldo === 0) {
                $quantidadeCreditos = $creditosPersonal->getQuantidade();
                $nomeAluno          = $contratoORM->getAluno()->getPessoa()->getNomeContato();
                $textoOcorrencia    = "***CRÉDITOS PERSONAL***\nO aluno $nomeAluno não possui créditos personal restantes.\nQuantidade contrata: $quantidadeCreditos\nQuantidade atual: 0";
                $usuarioLogado      = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);
                $funcionarios       = $usuarioLogado->getFuncionarios();
                $tipoOcorrencia     = $this->tipoOcorrenciaRepository->findOneBy([ConstanteParametros::CHAVE_TIPO => SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_ACOMPANHAMENTO_PEDAGOGICO])->getId();
                if (count($funcionarios) === 0) {
                    $mensagemErro = "O usuário logado não possui registro de funcionário.";
                    return null;
                }

                $paramsOcorrencia       = [
                    ConstanteParametros::CHAVE_ALUNO           => $contratoORM->getAluno()->getId(),
                    ConstanteParametros::CHAVE_USUARIO         => VariaveisCompartilhadas::$usuarioID,
                    ConstanteParametros::CHAVE_FUNCIONARIO     => $funcionarios[0]->getId(),
                    ConstanteParametros::CHAVE_TIPO_OCORRENCIA => $tipoOcorrencia,
                    ConstanteParametros::CHAVE_FRANQUEADA      => VariaveisCompartilhadas::$franqueadaID,
                    ConstanteParametros::CHAVE_SITUACAO        => SituacoesSistema::SITUACAO_ATIVO,
                    // ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA => ,
                    ConstanteParametros::CHAVE_CONTRATO        => $contratoORM->getId(),
                    ConstanteParametros::CHAVE_DATA_CRIACAO    => new DateTime(),
                ];
                $ocorrenciaAcademicaORM = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $paramsOcorrencia);
                if (is_null($ocorrenciaAcademicaORM) === true) {
                    return null;
                }

                $paramsOcorrenciaDetalhes       = [
                    ConstanteParametros::CHAVE_FUNCIONARIO  => $funcionarios[0]->getId(),
                    ConstanteParametros::CHAVE_DATA_CRIACAO => new DateTime(),
                    ConstanteParametros::CHAVE_TEXTO        => $textoOcorrencia,
                ];
                $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $paramsOcorrenciaDetalhes, true);
                if (is_null($ocorrenciaAcademicaDetalhesORM) === true) {
                    return null;
                }
            }//end if

            self::criarRegistro($objetoORM, $mensagemErro);
        }//end if

        return $objetoORM;
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
        $objetoORM = $this->alunoDiarioPersonalRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Diario do aluno não encontrado.";
        } else {
            if ($this->alunoDiarioPersonalBO->podeCriar($parametros, $mensagemErro) === true) {
                $dataAtual = new DateTime($parametros[ConstanteParametros::CHAVE_DATA_AULA]);
                $dataAtualComHorarioAnterior = $objetoORM->getDataAula();
                $dataAtualComHorarioAnterior->setDate($dataAtual->format('Y'), $dataAtual->format('m'), $dataAtual->format('d'));

                $parametros[ConstanteParametros::CHAVE_DATA_AULA] = $dataAtualComHorarioAnterior;

                $licaosORM        = $parametros[ConstanteParametros::CHAVE_LICAOS];
                $licoesExistentes = $objetoORM->getAlunoDiarioPersonalLicao();
                $qtdLicoes        = $licoesExistentes->count();
                for ($i = 0;$i < $qtdLicoes;$i++) {
                    $licaoORM = $licoesExistentes->get($i);
                    $objetoORM->removeAlunoDiarioPersonalLicao($licaoORM);
                }

                foreach ($licaosORM as $licaoORM) {
                    $objetoORM->addAlunoDiarioPersonalLicao($licaoORM);
                }

                unset($parametros[ConstanteParametros::CHAVE_LICAOS]);
                self::getFctHelper()->setParams($parametros, $objetoORM);

                $livroORM    = $objetoORM->getLivro();
                $contratoORM = $objetoORM->getAgendamentoPersonal()->getContrato();
                $contratoORM->setLivro($livroORM);

                self::flushSeguro($mensagemErro);
            }//end if
        }//end if

        return empty($mensagemErro);
    }


}
