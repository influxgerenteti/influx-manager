export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  filtros: {
    nivel_instrutor: null
  },
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    franqueada_id: null,
    sala_id: null,
    lotacao_maxima: null,
    personal: null,
    situacao: null
  }
}
