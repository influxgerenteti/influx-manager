export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    aluno: null,
    aluno_avaliacao: null,
    turma: null,
    livro: null,
    licao: null,
    sala_franqueada: null,
    usuario_solicitante: null,
    responsavel_execucao: null,
    franqueada: null,
    descricao_atividade: null,
    data: null,
    hora_inicio: null,
    concluido: null,
    forma_cobranca: null,
    valor: null,
    presenca: null,
    nota_mid_term_oral: null,
    nota_mid_term_escrita: null,
    nota_mid_term_test: null,
    nota_mid_term_composition: null,
    nota_final_oral: null,
    nota_final_escrita: null,
    nota_final_test: null,
    nota_final_composition: null,
    nota_retake_mid_term_oral: null,
    nota_retake_mid_term_escrita: null,
    nota_retake_mid_term_test: null,
    nota_retake_mid_term_composition: null,
    nota_retake_final_oral: null,
    nota_retake_final_escrita: null,
    nota_retake_final_test: null,
    nota_retake_final_composition: null,
    observacao_ocorrencia_academicas: null
  },
  filtros: {
    data_agendamento_de: null,
    data_agendamento_ate: null,
    item: null,
    responsavel_execucao: null,
    situacao: null
  }
}
