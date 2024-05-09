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
    data_solicitacao: null,
    data_conclusao: null,
    item: null,
    concluido: null,
    cancelamento: null,
    quantidade: null,
    funcionario: null,
    descricao: null,
    forma_cobranca: null,
    valor: null
  },
  filtros: {
    protocolo: null,
    aluno: null,
    situacao: null,
    data_solicitacao_de: null,
    data_solicitacao_ate: null
  }
}
