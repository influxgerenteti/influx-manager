<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class AgendaCompromissoBO extends GenericBO
{
    private const TIPO_AGENDAMENTO_ID        = "tipo_agendamento_id";
    private const TIPO_AGENDAMENTO_DESCRICAO = "tipo_agendamento_descricao";
    private const TIPO_AGENDAMENTO_TIPO      = "tipo_agendamento_tipo";
    private const TIPO_AGENDAMENTO_COR       = "tipo_agendamento_cor";
    private const FUNCIONARIO_ID          = "funcionario_id";
    private const FUNCIONARIO_APELIDO     = "funcionario_apelido";
    private const DESABILITAR_EDICAO      = "desabilitar_edicao";
    private const POSSUI_PERIODO_ATRELADO = "possui_periodo_atrelado";
    private const MENSAGEM_CAMPO          = "mensagem";
    private const RESULTADOS_CAMPO        = "resultados";
    private const ERRO_BOOLEANO_CAMPO     = "error";

    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository"          => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "tipoAgendamentoRepository"     => $entityManager->getRepository(\App\Entity\Principal\TipoAgendamento::class),
                "usuarioRepository"             => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "funcionarioRepository"         => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "atividadeExtraRepository"      => $entityManager->getRepository(\App\Entity\Principal\AtividadeExtra::class),
                "ocorrenciaAcademicaRepository" => $entityManager->getRepository(\App\Entity\Principal\OcorrenciaAcademica::class),
                "agendaCompromissoRepository"   => $entityManager->getRepository(\App\Entity\Principal\AgendaCompromisso::class),
            ]
        );
    }

    /**
     * Verifica se os parametros de relacionamentos obrigatórios estão ok
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if (self::verificaTipoAgendamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO]) === true) {
                if (self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
                    if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica os parametros opcionais a tabela
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetornoAtividadeExtraExiste      = true;
        $bRetornoOcorrenciaAcademicaExiste = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]) === false)) {
            $bRetornoAtividadeExtraExiste = self::verificaAtividadeExtraExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_ATIVIDADE_EXTRA]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]) === false)) {
            $bRetornoOcorrenciaAcademicaExiste = self::verificaOcorrenciaAcademicaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_OCORRENCIA_ACADEMICA]);
        }

        return $bRetornoAtividadeExtraExiste && $bRetornoOcorrenciaAcademicaExiste;
    }

    /**
     * Realiza a conversão das datas de string para objetos
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function realizaConversaoDatas(&$parametros, &$mensagemErro)
    {
        $bRetornoDataHoraInicio = self::converteData($parametros[ConstanteParametros::CHAVE_DATA_HORA_INICIO], $mensagemErro, ConstanteParametros::CHAVE_DATA_HORA_INICIO) === true;
        $bRetornoDataHoraFim    = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM]) === false)) {
            $bRetornoDataHoraFim = self::converteData($parametros[ConstanteParametros::CHAVE_DATA_HORA_FIM], $mensagemErro, ConstanteParametros::CHAVE_DATA_HORA_FIM) === true;
        }

        return $bRetornoDataHoraInicio && $bRetornoDataHoraFim;
    }

    /**
     * Verifica parametros de relacionamento que podem ser alterados
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function parametrosRelacionamentosAlteraveis(&$parametros, &$mensagemErro)
    {
        $bRetornoTipoAgendamento = true;
        $bRetornoFuncionario     = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO]) === false)) {
            $bRetornoTipoAgendamento = self::verificaTipoAgendamentoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_TIPO_AGENDAMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false)) {
            $bRetornoFuncionario = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
        }

        return $bRetornoTipoAgendamento && $bRetornoFuncionario;
    }

    /**
     * Monta template de registro para AgendaCompromisso
     *
     * @param boolean $bAgendaVazia
     * @param \App\Entity\Principal\AgendaCompromisso $agendaCompromissoORM
     *
     * @return array
     */
    protected function montaAgendaArray($bAgendaVazia, $agendaCompromissoORM)
    {
        $possuiPeriodo = is_null($agendaCompromissoORM->getPeriodoPai()) === false;
        if ($possuiPeriodo === false) {
            $possuiPeriodo = $agendaCompromissoORM->getAgendaCompromissos()->count() > 0;
        }

        $descricaoAgenda = [
            ConstanteParametros::CHAVE_ID               => $agendaCompromissoORM->getId(),
            ConstanteParametros::CHAVE_TITULO           => "Indisponível",
            ConstanteParametros::CHAVE_DESCRICAO        => "",
            ConstanteParametros::CHAVE_DATA_HORA_INICIO => $agendaCompromissoORM->getDataHoraInicio(),
            ConstanteParametros::CHAVE_DATA_HORA_FIM    => $agendaCompromissoORM->getDataHoraFim(),
            ConstanteParametros::CHAVE_FUNCIONARIO      => [
                ConstanteParametros::CHAVE_ID      => $agendaCompromissoORM->getFuncionario()->getId(),
                ConstanteParametros::CHAVE_APELIDO => $agendaCompromissoORM->getFuncionario()->getApelido(),
            ],
            ConstanteParametros::CHAVE_PRIVADO          => $agendaCompromissoORM->getPrivado(),
            ConstanteParametros::CHAVE_TIPO_AGENDAMENTO => [
                ConstanteParametros::CHAVE_ID        => $agendaCompromissoORM->getTipoAgendamento()->getId(),
                ConstanteParametros::CHAVE_DESCRICAO => $agendaCompromissoORM->getTipoAgendamento()->getDescricao(),
                ConstanteParametros::CHAVE_TIPO      => $agendaCompromissoORM->getTipoAgendamento()->getTipo(),
                ConstanteParametros::CHAVE_COR       => $agendaCompromissoORM->getTipoAgendamento()->getCor(),
            ],
            self::DESABILITAR_EDICAO                    => true,
            self::POSSUI_PERIODO_ATRELADO               => $possuiPeriodo,
        ];
        if ($bAgendaVazia === false) {
            $descricaoAgenda[ConstanteParametros::CHAVE_TITULO]    = $agendaCompromissoORM->getTitulo();
            $descricaoAgenda[ConstanteParametros::CHAVE_DESCRICAO] = $agendaCompromissoORM->getDescricao();
            $descricaoAgenda[self::DESABILITAR_EDICAO] = false;
        }

        return $descricaoAgenda;
    }

    /**
     *
     * @param \App\Entity\Principal\AgendaCompromisso[] $listaAgendaCompromissoORM
     * @param int $usuarioId
     *
     * @return array
     */
    protected function montaListaAgendamentoCustomizado($listaAgendaCompromissoORM, $usuarioId)
    {
        $funcionarioLogadoId = self::buscaFuncionarioIdDoUsuario($usuarioId);
        $listaRetornoAgenda  = [];
        foreach ($listaAgendaCompromissoORM as $agendaCompromissoORM) {
            $funcionarioIdDaAgenda = $agendaCompromissoORM->getFuncionario()->getId();
            $registroAgenda        = $this->montaAgendaArray(false, $agendaCompromissoORM);
            if ($agendaCompromissoORM->getPrivado() === true) {
                if ($funcionarioLogadoId !== $funcionarioIdDaAgenda) {
                    $registroAgenda = $this->montaAgendaArray(true, $agendaCompromissoORM);
                }
            }

            $listaRetornoAgenda[] = $registroAgenda;
        }

        return $listaRetornoAgenda;
    }

    /**
     * Verifica regras para criar registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeCriar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
                if ($this->realizaConversaoDatas($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica as regras para alterar registro
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            if ($this->parametrosRelacionamentosAlteraveis($parametros, $mensagemErro) === true) {
                if ($this->realizaConversaoDatas($parametros, $mensagemErro) === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retorna AgendaCustomizada para o front-end
     *
     * @param array $parametros
     * @param int $usuarioId
     *
     * @return array
     */
    public function retornarAgendaCustomizada($parametros, $usuarioId)
    {
        $retorno       = [
            self::MENSAGEM_CAMPO      => "",
            self::RESULTADOS_CAMPO    => [],
            self::ERRO_BOOLEANO_CAMPO => false,
        ];
        $funcionarioId = self::buscaFuncionarioIdDoUsuario($usuarioId);
        if (is_null($funcionarioId) === true) {
            $retorno[self::MENSAGEM_CAMPO]      = "Não foi localizado funcionario para o usuario logado.";
            $retorno[self::ERRO_BOOLEANO_CAMPO] = true;
        } else {
            if (((int) $parametros[ConstanteParametros::CHAVE_TIPO] === 0) && ((isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) || (empty($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true))) {
                $parametros[ConstanteParametros::CHAVE_FUNCIONARIO] = $funcionarioId;
            }

            $listaAgendaCompromissoObjeto    = self::getAgendaCompromissoRepository()->buscaObjetosAtravesDosFiltros($parametros);
            $retorno[self::RESULTADOS_CAMPO] = $this->montaListaAgendamentoCustomizado($listaAgendaCompromissoObjeto, $usuarioId);
        }

        return $retorno;
    }

    /**
     * Verifica se existe a AgendaCompromisso no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\AgendaComercialRepository $agendaCompromissoRepository Repositorio da AgendaCompromisso
     * @param integer $id Chave primaria da AgendaCompromisso
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\AgendaCompromisso|null $agendaCompromissoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaAgendaCompromissoExiste(\App\Repository\Principal\AgendaCompromissoRepository $agendaCompromissoRepository, $id, &$mensagemErro, &$agendaCompromissoORM)
    {
        $agendaCompromissoORM = $agendaCompromissoRepository->find($id);
        if (is_null($agendaCompromissoORM) === true) {
            $mensagemErro = "AgendaCompromisso não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
