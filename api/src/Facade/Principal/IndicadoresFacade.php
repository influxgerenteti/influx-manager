<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\Facade\Principal\AlunoFacade;

/**
 *
 * @author Marcelo A Naegeler
 */
class IndicadoresFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\InteressadoRepository
     */
    private $interessadoRepository;

    /**
     *
     * @var \App\Repository\Principal\FollowupComercialRepository
     */
    private $followupComercialRepository;

    /**
     *
     * @var \App\Facade\Principal\AlunoFacade
     */
    private $alunoFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->followupComercialRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\FollowupComercial::class);
        $this->interessadoRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Interessado::class);
        $this->alunoFacade = new AlunoFacade($managerRegistry);
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
        $ano = $parametros[ConstanteParametros::CHAVE_ANO];
        $mes = $parametros[ConstanteParametros::CHAVE_MES];

        $periodo = [
            \Carbon\Carbon::parse("$ano-$mes-01", "America/Sao_Paulo")->startOfMonth(),
            \Carbon\Carbon::parse("$ano-$mes-01", "America/Sao_Paulo")->endOfMonth(),
        ];

        $parametros[ConstanteParametros::CHAVE_PERIODO] = $periodo;

        $followups = $this->followupComercialRepository->buscarFollowupsIndicadores($parametros);

        $corBranco    = '#fff';
        $corCinza     = '#333';
        $corAtivo     = '#F6A819';
        $corReceptivo = '#5881ff';

        function indicadorFactory ($descricao, $cor, $corTexto)
        {
            return [
                ConstanteParametros::CHAVE_DESCRICAO => $descricao,
                ConstanteParametros::CHAVE_COR       => $cor,
                ConstanteParametros::CHAVE_COR_TEXTO => $corTexto,
                ConstanteParametros::CHAVE_TOTAL     => 0,
                ConstanteParametros::CHAVE_DATAS     => [],
            ];
        }
        // Além de ser ativo/receptivo, também temos retornos que podem ser
        // efetivos (teve algum contato, efeito, etc)
        // não efetivos (não conseguiu falar, caiu na caixa postal, etc) - INDEPENDENTE DE SER POSITIVO
        $indicadores = [
            'Contatos Totais'                         => indicadorFactory('Contatos Totais', $corBranco, $corCinza),
            'Retornos Totais Efetivos'                => indicadorFactory('Retornos Totais Efetivos', $corBranco, $corCinza),
            'Matrículas Totais'                       => indicadorFactory('Matrículas Totais', $corBranco, $corCinza),
            // Receptivos
            'Contatos Receptivos'                     => indicadorFactory('Contatos Receptivos', $corReceptivo, $corCinza),
            'Retornos Agendados'                      => indicadorFactory('Retornos Agendados', $corReceptivo, $corCinza),
            'Retornos Efetivos'                       => indicadorFactory('Retornos Efetivos', $corReceptivo, $corCinza),
            'Visitas Agendadas'                       => indicadorFactory('Visitas Agendadas', $corReceptivo, $corCinza),
            'Visitas Efetivas 1ª Visita'              => indicadorFactory('Visitas Efetivas 1ª Visita', $corReceptivo, $corCinza),
            'Visitas Efetivas 2ª Visita'              => indicadorFactory('Visitas Efetivas 2ª Visita', $corReceptivo, $corCinza),
            'Matrículas'                              => indicadorFactory('Matrículas', $corReceptivo, $corCinza),
            'Contatos Receptivos Pessoal'             => indicadorFactory('Contatos Receptivos Pessoal', $corReceptivo, $corCinza),
            'Contatos Receptivos Telefone'            => indicadorFactory('Contatos Receptivos Telefone', $corReceptivo, $corCinza),
            'Contatos Receptivos Mídias Sociais'      => indicadorFactory('Contatos Receptivos Mídias Sociais', $corReceptivo, $corCinza),
            'Contatos Receptivos Fone'                => indicadorFactory('Contatos Receptivos Fone', $corReceptivo, $corCinza),
            'Contatos Receptivos E-mail'              => indicadorFactory('Contatos Receptivos E-mail', $corReceptivo, $corCinza),
            // Ativos
            'Contatos Ativos'                         => indicadorFactory('Contatos Ativos', $corAtivo, $corCinza),
            'Retornos Agendados Ativo'                => indicadorFactory('Retornos Agendados', $corAtivo, $corCinza),
            'Retornos Efetivos Ativo'                 => indicadorFactory('Retornos Efetivos', $corAtivo, $corCinza),
            'Visitas Agendadas Ativo'                 => indicadorFactory('Visitas Agendadas', $corAtivo, $corCinza),
            'Visitas Efetivas 1ª Visita Ativo'        => indicadorFactory('Visitas Efetivas 1ª Visita', $corAtivo, $corCinza),
            'Visitas Efetivas 2ª Visita Ativo'        => indicadorFactory('Visitas Efetivas 2ª Visita', $corAtivo, $corCinza),
            'Matrículas Ativo'                        => indicadorFactory('Matrículas Ativas', $corAtivo, $corCinza),
            'Prospecção de Indicações'                => indicadorFactory('Prospecção de Indicações', $corAtivo, $corCinza),
            'Prospecção com Ex-aluno'                 => indicadorFactory('Prospecção com Ex-aluno', $corAtivo, $corCinza),
            'Prospecção em Condomínios'               => indicadorFactory('Prospecção em Condomínios', $corAtivo, $corCinza),
            'Prospecção em Feiras/Eventos'            => indicadorFactory('Prospecção em Feiras/Eventos', $corAtivo, $corCinza),
            'Prospecção em Escolas/Faculdades'        => indicadorFactory('Prospecção em Escolas/Faculdades', $corAtivo, $corCinza),
            'Prospecção em Empresas de Pequeno Porte' => indicadorFactory('Prospecção em Empresas de Pequeno Porte', $corAtivo, $corCinza),
            'Prospecção na Rua'                       => indicadorFactory('Prospecção na Rua', $corAtivo, $corCinza),
            'Prospecção em Empresas de Grande Porte'  => indicadorFactory('Prospecção em Empresas de Grande Porte', $corAtivo, $corCinza),
            'Interessados Antigos'                    => indicadorFactory('Interessados Antigos', $corAtivo, $corCinza),
        ];

        function incrementarDia (&$indicadores, $key, $data)
        {
            if (isset($indicadores[$key][ConstanteParametros::CHAVE_DATAS][$data]) === false) {
                $indicadores[$key][ConstanteParametros::CHAVE_DATAS][$data] = 0;
            }

            $indicadores[$key][ConstanteParametros::CHAVE_DATAS][$data]++;
            $indicadores[$key][ConstanteParametros::CHAVE_TOTAL]++;
        }

        $interessadosArray = [];
        foreach ($followups as $followup) {
            $data  = \Carbon\Carbon::parse($followup['data_registro'])->format('Y-m-d');
            $ativo = '';

            // Receptivos
            if ($followup[ConstanteParametros::CHAVE_TIPO_LEAD] === SituacoesSistema::TIPO_CONTATO_RECEPTIVO) {
                // Tipos de contato, buscar a chave do indicador
                if (isset($followup[ConstanteParametros::CHAVE_TIPO_CONTATO]) === true && isset($followup[ConstanteParametros::CHAVE_TIPO_CONTATO][ConstanteParametros::CHAVE_NOME]) === true) {
                    $key = 'Contatos Receptivos ' . $followup[ConstanteParametros::CHAVE_TIPO_CONTATO][ConstanteParametros::CHAVE_NOME];
                    incrementarDia($indicadores, $key, $data);
                }
            }

            $interessadoId = $followup[ConstanteParametros::CHAVE_INTERESSADO]["id"];
            if (in_array($interessadoId, $interessadosArray) === false) {
                $interessadosArray[] = $interessadoId;
                incrementarDia($indicadores, 'Contatos Totais', $data);
                if ($followup[ConstanteParametros::CHAVE_TIPO_LEAD] === SituacoesSistema::TIPO_CONTATO_RECEPTIVO) {
                    incrementarDia($indicadores, 'Contatos Receptivos', $data);
                } else {
                    incrementarDia($indicadores, 'Contatos Ativos', $data);
                }
            }

            // Ativos
            if ($followup[ConstanteParametros::CHAVE_TIPO_LEAD] === SituacoesSistema::TIPO_CONTATO_ATIVO) {
                $ativo = ' Ativo';

                // Tipos de prospecção, buscar a chave do indicador
                if (isset($followup[ConstanteParametros::CHAVE_TIPO_PROSPECCAO]) === true && isset($followup[ConstanteParametros::CHAVE_TIPO_PROSPECCAO][ConstanteParametros::CHAVE_DESCRICAO]) === true) {
                    $descricao = $followup[ConstanteParametros::CHAVE_TIPO_PROSPECCAO][ConstanteParametros::CHAVE_DESCRICAO];

                    if ($descricao === 'Interessados Antigos') {
                        $key = $descricao;
                    } else if ($descricao === 'SuperAmigos Turbinado') {
                        $key = "Prospecção de Indicações";
                    } else if ($descricao === 'Ex-aluno') {
                        $key = "Prospecção com Ex-aluno";
                    } else if ($descricao === 'Rua/Praça') {
                        $key = "Prospecção na Rua";
                    } else if ($descricao === 'Eventos e Feiras') {
                        $key = "Prospecção em Feiras/Eventos";
                    } else {
                        $matches = [];
                        preg_match('/(Empresas de Grande Porte|Escolas\/Faculdades).*/', $descricao, $matches);
                        if (count($matches) > 1) {
                            $descricao = $matches[1];
                        }

                        $key = "Prospecção em $descricao";
                    }

                    incrementarDia($indicadores, $key, $data);
                }//end if
            }//end if

            if (isset($followup[ConstanteParametros::CHAVE_AGENDA_COMERCIAL]) === true) {
                $agendaConcluida           = $followup[ConstanteParametros::CHAVE_AGENDA_COMERCIAL][ConstanteParametros::CHAVE_SITUACAO] === SituacoesSistema::SITUACAO_CONCLUIDA;
                $agendaEhRetornoTelefonico = $followup[ConstanteParametros::CHAVE_AGENDA_COMERCIAL][ConstanteParametros::CHAVE_TIPO_AGENDAMENTO][ConstanteParametros::CHAVE_TIPO] === SituacoesSistema::WORKFLOW_RETORNO_TELEFONICO;
                if ($agendaEhRetornoTelefonico === true) {
                    incrementarDia($indicadores, "Retornos Agendados$ativo", $data);

                    if ($agendaConcluida === true) {
                        $followupEfetivo = isset($followup[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true && $followup[ConstanteParametros::CHAVE_WORKFLOW_ACAO][ConstanteParametros::CHAVE_EFETIVO] === true;
                        $motivoNaoFechamentoMatriculaEhEfetivo = isset($followup[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA]) === true && $followup[ConstanteParametros::CHAVE_MOTIVO_NAO_FECHAMENTO_MATRICULA][ConstanteParametros::CHAVE_EFETIVO] === true;
                        if ($followupEfetivo === true || $motivoNaoFechamentoMatriculaEhEfetivo === true) {
                            incrementarDia($indicadores, "Retornos Efetivos$ativo", $data);
                            incrementarDia($indicadores, "Retornos Totais Efetivos", $data);
                        }
                    }
                }

                $agendaEhApresentacaoPessoal = $followup[ConstanteParametros::CHAVE_AGENDA_COMERCIAL][ConstanteParametros::CHAVE_TIPO_AGENDAMENTO][ConstanteParametros::CHAVE_TIPO] === SituacoesSistema::WORKFLOW_APRESENTACAO_PESSOAL;
                if ($agendaEhApresentacaoPessoal === true) {
                    incrementarDia($indicadores, "Visitas Agendadas$ativo", $data);
                }

                if ($agendaConcluida === true && $agendaEhApresentacaoPessoal === true) {
                    $interessadoORM       = $this->interessadoRepository->find($followup[ConstanteParametros::CHAVE_INTERESSADO]['id']);
                    $followupsInteressado = $interessadoORM->getFollowupComercials();
                    $visitasEfetivasArray = [];
                    foreach ($followupsInteressado as $followupInteressado) {
                        $agendaFollowup = $followupInteressado->getAgendaComercial();
                        $workflowAcao   = $followupInteressado->getWorkflowAcao();

                        if (is_null($workflowAcao) === true || is_null($agendaFollowup) === true) {
                            continue;
                        }

                        $agendaFollowupConcluida   = $agendaFollowup->getSituacao() === SituacoesSistema::SITUACAO_CONCLUIDA;
                        $ehTipoApresentacaoPessoal = $agendaFollowup->getTipoAgendamento()->getTipo() === SituacoesSistema::WORKFLOW_APRESENTACAO_PESSOAL;
                        $acaoEfetiva = $workflowAcao->getEfetivo();

                        $visitaFoiEfetiva = $agendaFollowupConcluida === true && $ehTipoApresentacaoPessoal === true && $acaoEfetiva === true;

                        if ($visitaFoiEfetiva === true) {
                            $visitasEfetivasArray[] = $followupInteressado->getId();
                        }
                    }

                    $ehPrimeiraVisitaEfetiva = count($visitasEfetivasArray) > 0 && min($visitasEfetivasArray) === $followup['id'];

                    if ($ehPrimeiraVisitaEfetiva === true) {
                        $visita = '1';
                    } else {
                        $visita = '2';
                    }

                    if (is_null($visita) === false) {
                        $key = "Visitas Efetivas ${visita}ª Visita$ativo";
                        incrementarDia($indicadores, $key, $data);
                    }
                }//end if
            }//end if

            $interessadoConvertido = isset($followup[ConstanteParametros::CHAVE_WORKFLOW_ACAO]) === true && $followup[ConstanteParametros::CHAVE_WORKFLOW_ACAO][ConstanteParametros::CHAVE_DESTINO_WORKFLOW][ConstanteParametros::CHAVE_TIPO_WORKFLOW] === SituacoesSistema::WORKFLOW_MATRICULA_CONVERTIDO;
            $interessadoEhAluno    = isset($followup[ConstanteParametros::CHAVE_INTERESSADO]) === true && isset($followup[ConstanteParametros::CHAVE_INTERESSADO][ConstanteParametros::CHAVE_ALUNO]) === true && isset($followup[ConstanteParametros::CHAVE_INTERESSADO][ConstanteParametros::CHAVE_ALUNO]['id']) === true;
            if ($interessadoConvertido === true && $interessadoEhAluno === true) {
                $interessadoPossuiContratoValido = $this->alunoFacade->alunoPossuiContratoValido($followup[ConstanteParametros::CHAVE_INTERESSADO][ConstanteParametros::CHAVE_ALUNO]['id']);
                if ($interessadoPossuiContratoValido === true) {
                    $key = "Matrículas$ativo";
                    incrementarDia($indicadores, $key, $data);
                    incrementarDia($indicadores, 'Matrículas Totais', $data);
                }
            }
        }//end foreach

        return [
            ConstanteParametros::CHAVE_PERIODO     => $periodo,
            ConstanteParametros::CHAVE_INDICADORES => array_values($indicadores),
        ];
    }


}
