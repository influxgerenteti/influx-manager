<?php
namespace App\Helper;

/**
 *
 * @author Dayan Orlando de Freitas
 */
class SituacoesSistema
{

    public const SITUACAO_ATIVO    = 'A';
    public const SITUACAO_ABERTO   = 'A';
    public const SITUACAO_FECHADO  = 'F';
    public const SITUACAO_INATIVO  = 'I';
    public const SITUACAO_REMOVIDO = 'R';
    public const ALUNO_ATIVO       = 'ATI';
    public const ALUNO_INATIVO     = 'INA';
    public const ALUNO_INTERESSADO = 'INT';
    public const ALUNO_TRANCADO    = 'TRA';
    public const SITUACAO_PENDENTE = 'PEN';
    public const SITUACAO_PENDENTE_VALIDACAO = 'PV';
    public const SITUACAO_RECEBIDO           = 'REC';
    public const SITUACAO_ENVIADO            = 'ENV';
    public const SITUACAO_CONFIRMADO         = 'CON';
    public const SITUACAO_VENCIDAS           = 'VEN';
    public const SITUACAO_NEGATIVADAS        = 'NEG';
    public const SITUACAO_QUITADAS           = 'QUI';
    public const SITUACAO_LIQUIDADO          = 'LIQ';
    public const SITUACAO_BAIXADO            = 'BAI';
    public const SITUACAO_SUBSTITUIDO        = 'SUB';
    public const SITUACAO_CANCELADO          = 'CAN';
    public const SITUACAO_CANCELAMENTO       = 'CC';
    public const SITUACAO_DEVOLVIDO          = 'DEV';
    public const SITUACAO_REJEITADO          = 'REJ';
    public const SITUACAO_EXCLUIDO           = 'EXC';
    public const SITUACAO_CREDITADO          = 'CRE';
    public const SITUACAO_ESTORNADO          = 'EST';
    public const SITUACAO_PAGO = 'PAG';
    public const SITUACAO_PAGAMENTO_ANDAMENTO = 'PEA';
    public const SITUACAO_CHEQUE_BAIXADO      = 'B';
    public const SITUACAO_CHEQUE_DEVOLVIDO    = 'D';
    public const SITUACAO_CHEQUE_PENDENTE     = 'P';
    public const SITUACAO_CHEQUE_CANCELADO    = 'C';
    public const SITUACAO_CONTRATO_VIGENTE    = 'V';
    public const SITUACAO_CONTRATO_ENCERRADO  = 'E';
    public const SITUACAO_CONTRATO_RESCINDIDO = 'R';
    public const SITUACAO_CONTRATO_CANCELADO  = 'C';
    public const SITUACAO_CONTRATO_RASCUNHO   = 'S';
    public const SITUACAO_CONTRATO_TRANCADO   = 'T';
    public const SITUACAO_CONVERTIDO          = 'C';
    public const SITUACAO_PERDIDO       = "P";
    public const SITUACAO_NEGADO        = "NE";
    public const SITUACAO_EM_ANDAMENTO  = 'EA';
    public const SITUACAO_EM_NEGOCIACAO = "EN";
    public const SITUACAO_SEM_COVENIO   = "SC";
    public const SITUACAO_RETORNAR_FUTURAMENTE            = "RF";
    public const SITUACAO_PENDENTE_VALIDACAO_FRANQUEADORA = "PV";
    public const SITUACAO_CONCLUIDA     = "C";
    public const SITUACAO_NAO_CONCLUIDO = "NC";

    public const OPERACAO_TRANSFERENCIA = 'T';
    public const OPERACAO_DEBITO        = 'D';
    public const OPERACAO_CREDITO       = 'C';

    public const SITUACAO_TURMA_ABERTA      = 'ABE';
    public const SITUACAO_TURMA_EM_FORMACAO = 'FOR';
    public const SITUACAO_TURMA_ENCERRADA   = 'ENC';

    public const CONCILIADO_SIM = 'S';
    public const CONCILIADO_NAO = 'N';

    public const TIPO_ATIVIDADE_ATEMPORAL = "A";
    public const TIPO_ATIVIDADE_DIARIA    = "D";
    public const TIPO_ATIVIDADE_MENSAL    = "M";
    public const TIPO_ATIVIDADE_SEMANAL   = "S";
    public const TIPO_NOTA_ENTRADA        = 'E';
    public const TIPO_NOTA_SAIDA          = 'S';
    public const TIPO_OPERACAO_CREDITO    = "C";
    public const TIPO_OPERACAO_DEBITO     = "D";
    public const TIPO_OPERACAO_SAQUE      = "S";

    public const TIPO_MOVIMENTO_ESTOQUE_EXCLUSAO_NOTA_ENTRADA = '2';
    public const TIPO_MOVIMENTO_ESTOQUE_ENTRADA        = 'E';
    public const TIPO_MOVIMENTO_ESTOQUE_SAIDA          = 'S';
    public const TIPO_MOVIMENTO_ESTOQUE_AJUSTE_ENTRADA = "AE";
    public const TIPO_MOVIMENTO_ESTOQUE_AJUSTE_SAIDA   = "AS";

    public const OPERACAO_TITULO_PAGAR   = 'TP';
    public const OPERACAO_TITULO_RECEBER = 'TR';
    public const OPERACAO_TIPO_NOTA      = 'TN';

    public const WORKFLOW_CONTATO_INICIAL      = "WCI";
    public const WORKFLOW_RETORNO_TELEFONICO   = "WRTFL";
    public const WORKFLOW_APRESENTACAO_PESSOAL = "WRAP";
    public const WORKFLOW_MATRICULA_PERDIDA    = "WRMP";
    public const WORKFLOW_MATRICULA_CONVERTIDO = "WRMC";
    public const ALUNO_FALTA    = "F";
    public const ALUNO_PRESENCA = "P";

    public const TIPO_ITEM_TRANSFERENCIA_TURMA = "TT";

    public const TIPO_ITEM_SERVICO     = "S";
    public const TIPO_ITEM_PRODUTO     = "P";
    public const TIPO_ITEM_VALOR_CURSO = "V";

    public const TIPO_ITEMS_NOTAS_AVALIACOES = [
        "MT",
        "MF",
        "RM",
        "RF",
    ];

    public const TIPO_AVALIACAO_MID_TERM        = "MT";
    public const TIPO_AVALIACAO_FINAL           = "MF";
    public const TIPO_AVALIACAO_RETAKE_MID_TERM = "RM";
    public const TIPO_AVALIACAO_RETAKE_FINAL    = "RF";

    public const TIPO_OCORRENCIA_TIPO_ITEM_BONUS_CLASSES    = "BC";
    public const TIPO_OCORRENCIA_TIPO_ITEM_INSATISFACOES    = "IN";
    public const TIPO_OCORRENCIA_TIPO_ITEM_SUGESTOES        = "S";
    public const TIPO_OCORRENCIA_TIPO_ITEM_OUTROS           = "O";
    public const TIPO_OCORRENCIA_TIPO_ITEM_FALTA            = "F";
    public const TIPO_OCORRENCIA_TIPO_ITEM_ATIVIDADES_EXTRA = "AE";
    public const TIPO_OCORRENCIA_TIPO_ITEM_REPOSICOES       = "R";
    public const TIPO_OCORRENCIA_TIPO_ITEM_AVALIACOES       = "A";
    public const TIPO_OCORRENCIA_TIPO_ITEM_COBRANCA         = "C";
    public const TIPO_OCORRENCIA_TIPO_ITEM_TRANSFERENCIA_TURMAS      = "TR";
    public const TIPO_OCORRENCIA_TIPO_ITEM_ACOMPANHAMENTO_PEDAGOGICO = "AP";
    public const TIPO_OCORRENCIA_TIPO_ITEM_REAGENDAMENTO_PERSONAL    = "RP";
    public const TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CE     = "CE";
    public const TIPO_OCORRENCIA_TIPO_ITEM_ENTREGA_ATIVIDADES_CA     = "CA";

    public const ALUNO_ATIVIDADE_ENTREGUE        = "E";
    public const ALUNO_ATIVIDADE_NAO_ENTREGUE    = "NE";
    public const ALUNO_ATIVIDADE_ENTREGUE_ATRASO = "EA";

    public const OCORRENCIA_ABERTA    = "A";
    public const OCORRENCIA_ENCERRADA = "F";
    public const REPOSICAO            = "RE";
    public const REPOSICAO_AVALIACAO  = "RA";
    public const REPOSICAO_RETAKE     = "RT";

    public const MODALIDADE_TURMA_PERSONAL = "PER";
    public const MODALIDADE_PERSONAL       = "PER";
    public const MODALIDADE_TURMAS         = "TUR";
    public const MODALIDADE_TURMA_HYBRID   = "HYB";
    public const MODALIDADE_VIP            = "VIP";
    public const MODALIDADE_ATIVIDADE_EXTRA = "ATI";
    public const MODALIDADE_REPOSICAO_AULA  = "REP";
    public const MODALIDADE_BONUS_CLASS     = "BON";

    public const DIA_SEMANA_DOMINGO = 0;
    public const DIA_SEMANA_SEGUNDA = 1;
    public const DIA_SEMANA_TERCA   = 2;
    public const DIA_SEMANA_QUARTA  = 3;
    public const DIA_SEMANA_QUINTA  = 4;
    public const DIA_SEMANA_SEXTA   = 5;
    public const DIA_SEMANA_SABADO  = 6;

    public const CURSO_INTENSIDADE_REGULAR        = 'R';
    public const CURSO_INTENSIDADE_SEMI_INTENSIVO = 'S';
    public const CURSO_INTENSIDADE_INTENSIVO      = 'I';

    public const ITEM_ENTREGUE     = "E";
    public const ITEM_NAO_ENTREGUE = "N";
    public const ITEM_CANCELADO    = "C";

    public const ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_7  = "AP7";
    public const ORIGEM_OCORRENCIA_AVALIACAO_PARCIAL_23 = "AP23";
    public const ORIGEM_OCORRENCIA_MID_TERM            = "MT";
    public const ORIGEM_OCORRENCIA_FINAL_TEST          = "FT";
    public const ORIGEM_OCORRENCIA_FALTAS_SEGUIDAS     = "FTS";
    public const ORIGEM_OCORRENCIA_HOMEWORK_CA         = "HMWCA";
    public const ORIGEM_OCORRENCIA_HOMEWORK_CE         = "HMWCE";
    public const ORIGEM_OCORRENCIA_TRANSFERENCIA_TURMA = "TFT";
    public const ORIGEM_OCORRENCIA_REAGENDAMENTO_PERSONAL = "RAGP";
    public const ORIGEM_OCORRENCIA_BONUS_CLASS            = "BNC";

    public const ATIVIDADE_REPOSICAO_AULA = 'R';
    public const ATIVIDADE_EXTRA          = 'AE';

    public const TAXA_MATRICULA = 'M';
    public const VALOR_CURSO    = 'M';

    public const TIPOS_SERVICO = [
        "S",
        "SN",
        "V",
        "M",
        "VP32",
        "VP48",
        "VP64",
        "AE",
        "MC",
        "MT",
        "MF",
        "RM",
        "RF",
        "VPA",
    ];

    public const TIPO_CONTATO_ATIVO     = 'A';
    public const TIPO_CONTATO_RECEPTIVO = 'R';
    public const VISITA_EFETIVA         = 'VE';
    public const VISITA_EFETIVA_2       = 'VE2';

    public const SITUACAO_ATIVO_CONVENIO = 'ATI';
}
