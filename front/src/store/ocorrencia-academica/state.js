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
    tipo_ocorrencia: null,
    usuario: null,
    franqueada: null,
    funcionario: null,
    data_conclusao: null,
    data_proximo_contato: null,
    horario: null,
    situacao: null,
    texto: null,
    ocorrenciaAcademicaDetalhes: null
  },
  filtros: {
    aluno: null,
    tipo_ocorrencia: null,
    usuario: null,
    funcionario: null,
    situacao: null,
    data_abertura: null,
    data_fechamento: null,
    data_movimentacao_de: null,
    data_movimentacao_ate: null
  }
}
