<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\BonusClassBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Dayan Fretias
 */
class BonusClassFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\BonusClassRepository
     */
    private $bonusClassRepository;

    /**
     *
     * @var \App\BO\Principal\BonusClassBO
     */
    private $bonusClassBO;

    /**
     *
     * @var \App\Repository\Principal\TipoOcorrenciaRepository
     */
    private $tipoOcorrenciaRepository;

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
        $this->bonusClassRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\BonusClass::class);
        $this->bonusClassBO         = new BonusClassBO(self::getEntityManager());
        $this->ocorrenciaAcademicaFacade         = new OcorrenciaAcademicaFacade(self::getManagerRegistry());
        $this->ocorrenciaAcademicaDetalhesFacade = new OcorrenciaAcademicaDetalhesFacade(self::getManagerRegistry());
        $this->tipoOcorrenciaRepository          = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoOcorrencia::class);
    }

    /**
     * Gera ocorrencia academica
     *
     * @param string $mensagemErro
     * @param int $usuarioId
     * @param string $tipoOcorrencia
     * @param string $origemOcorrencia
     * @param int $alunoId
     * @param string $observacaoTexto
     *
     * @return boolean
     */
    private function gerarOcorrenciaAcademica(&$mensagemErro, $usuarioId, $tipoOcorrencia, $origemOcorrencia, $alunoId, $observacaoTexto)
    {
        $bRetorno          = true;
        $tipoOcorrenciaORM = null;
        $retornaIdDoTipoOcorrencia     = null;
        $retornaFuncionarioIdDoUsuario = $this->ocorrenciaAcademicaFacade->retornaFuncionarioIdDoUsuario($usuarioId);
        $tipoOcorrenciaORM = $this->tipoOcorrenciaRepository->findOneBy([ConstanteParametros::CHAVE_TIPO => $tipoOcorrencia]);
        if (is_null($retornaFuncionarioIdDoUsuario) === true) {
            $mensagemErro .= "Não foi encontrado um funcionario cadastrado para o usuario informado.\n";
        }

        if (is_null($tipoOcorrenciaORM) === true) {
            $mensagemErro .= "Não foi encontrado uma Ocorrencia para o tipo informado.\n";
        } else {
            $retornaIdDoTipoOcorrencia = $tipoOcorrenciaORM->getId();
        }

        if (($bRetorno = empty($mensagemErro)) === true) {
            $parametrosOcorrenciaAcademica = [
                ConstanteParametros::CHAVE_FRANQUEADA             => VariaveisCompartilhadas::$franqueadaID,
                ConstanteParametros::CHAVE_ALUNO                  => $alunoId,
                ConstanteParametros::CHAVE_ORIGEM_OCORRENCIA_TIPO => $origemOcorrencia,
                ConstanteParametros::CHAVE_USUARIO                => $usuarioId,
                ConstanteParametros::CHAVE_FUNCIONARIO            => $retornaFuncionarioIdDoUsuario,
                ConstanteParametros::CHAVE_TIPO_OCORRENCIA        => $retornaIdDoTipoOcorrencia,
                ConstanteParametros::CHAVE_DATA_CONCLUSAO         => new \DateTime(),
                ConstanteParametros::CHAVE_SITUACAO               => SituacoesSistema::OCORRENCIA_ABERTA,
                ConstanteParametros::CHAVE_TEXTO                  => $observacaoTexto,
            ];
            $ocorrenciaAcademicaORM        = $this->ocorrenciaAcademicaFacade->criar($mensagemErro, $parametrosOcorrenciaAcademica);
            if ((is_null($ocorrenciaAcademicaORM) === true) || (empty($mensagemErro) === false)) {
                $bRetorno = false;
            } else {
                $ocorrenciaAcademicaDetalhesORM = $this->ocorrenciaAcademicaDetalhesFacade->criar($mensagemErro, $ocorrenciaAcademicaORM, $parametrosOcorrenciaAcademica, false);
                if ((is_null($ocorrenciaAcademicaDetalhesORM) === true) || (empty($mensagemErro) === false)) {
                    $bRetorno = false;
                }
            }
        }//end if

        return $bRetorno;
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
        $retornoRepositorio = $this->bonusClassRepository->filtrarBonusClassPorPagina($parametros);
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
    public function buscarPorId(&$mensagemErro, $id, $find=false)
    {
        $objetoORM = null;
        $this->bonusClassBO->verificarBonusClassExite($id, $mensagemErro, $objetoORM, $this->bonusClassRepository);

        return $objetoORM;
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
            if ($this->bonusClassBO->podeSalvar($parametros, $mensagemErro) === true) {
                $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\BonusClass::class, true, $parametros);
                self::criarRegistro($objetoORM, $mensagemErro);
            }
         return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param int $usuarioID usuarioId
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $usuarioID, $parametros=[])
    {
        $objetoORM = $this->bonusClassRepository->find($id);

        if (is_null($objetoORM) === true) {
            $mensagemErro = "Bonus class não encontrado na base de dados.";
        } else {
            if ($this->bonusClassBO->podeSalvar($parametros, $mensagemErro) === true) {
                if ((bool) $parametros[ConstanteParametros::CHAVE_CONCLUIDO] === true) {
                    $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CONFIRMADO);
                    $listaDeParticipantes = $objetoORM->getAlunosBonusClasses();
                    $indexAluno           = 0;
                    foreach ($listaDeParticipantes as $alunoORM) {
                        $diaMesAno = $alunoORM->getBonusClass()->getDataAula()->format("d/m/Y");
                        $horario   = $alunoORM->getHorarioAula()->format("H:i");
                        $texto     = "****BONUS CLASS****\n";
                        if ($alunoORM->getPresenca() !== SituacoesSistema::ALUNO_PRESENCA) {
                            $texto .= "Bonus Class agendada para dia \"" . $diaMesAno . "\" às \"" . $horario . "\", aluno(a) faltou.";
                        } else {
                            $texto .= "Bonus Class ministrada no ddia \"" . $diaMesAno . "\" às \"" . $horario . "\" pelo(a) instrutor(a) \"" . $objetoORM->getFuncionario()->getPessoa()->getNomeContato() . "\", viu o assunto: \"" . $alunoORM->getConteudo() . "\"";
                        }

                        $bRetorno = $this->gerarOcorrenciaAcademica($mensagemErro, $usuarioID, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_BONUS_CLASSES, SituacoesSistema::ORIGEM_OCORRENCIA_REAGENDAMENTO_PERSONAL, $alunoORM->getAluno()->getId(), $texto);
                        if ($bRetorno === false) {
                            $mensagemErro .= "Erro no index:" . $indexAluno;
                            break;
                        }

                        $indexAluno++;
                    }
                }//end if

                self::getFctHelper()->setParams($parametros, $objetoORM);
                self::flushSeguro($mensagemErro);
            }//end if
        }//end if

        return empty($mensagemErro);
    }

       /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function cancelarAula(&$mensagemErro, $id)
    {
        $objetoORM = $this->bonusClassRepository->find($id);
        // echo('entrou');
        // var_dump($objetoORM);
        // die;
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Bonus class não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_CANCELADO);

            // $dataAtual = new \DateTime();

            // $diaMesAno = $dataAtual->format("d/m/Y");
            // $horario   = $dataAtual->format("H:i");

            // $texto = "Cancelado Bonus Class \"" . $diaMesAno . "\" às \"" . $horario . "\" pelo(a) instrutor(a) \"" . $objetoORM->getFuncionario()->getPessoa()->getNomeContato();
            

            // $bRetorno = $this->gerarOcorrenciaAcademica($mensagemErro, $usuarioID, SituacoesSistema::TIPO_OCORRENCIA_TIPO_ITEM_BONUS_CLASSES, SituacoesSistema::ORIGEM_OCORRENCIA_REAGENDAMENTO_PERSONAL, null, $texto);
            // if ($bRetorno === false) {
            //     $mensagemErro .= "Erro no index:";
            // }
            
            //self::getFctHelper()->setParams($parametros, $objetoORM);
          //  self::getFctHelper()->setParams($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
            
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
