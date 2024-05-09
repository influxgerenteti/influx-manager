export default {
  lista: [],
  estaCarregando: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  disponibilidades: [],
  itemSelecionado: {
    id: null,
    franqueada_id: null,
    sala: null,
    sala_id: null,
    lotacao_maxima: null,
    personal: null,
    situacao: null
  },
  filtros: {
    apenas_sala_ativa: false,
    personal: false
  }
}
