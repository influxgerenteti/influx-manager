export default {
  lista: [],
  listaAtuais: {
    lista: [],
    paginaAtual: 1,
    totalItens: null,
    todosItensCarregados: false
  },
  estaCarregando: false,
  paginaAtual: 1,
  listarProximos: false,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    descricao: null,
    data_inicio: '',
    data_termino: ''
  },
  filtros: {
    anterior_atual_proximo: false
  }
}
