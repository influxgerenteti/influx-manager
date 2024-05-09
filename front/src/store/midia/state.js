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
    descricao: null,
    tipo: null,
    situacao: null
  },

  filtros: {
    descricao: null,
    tipo: null,
    situacao: null
  }
}
