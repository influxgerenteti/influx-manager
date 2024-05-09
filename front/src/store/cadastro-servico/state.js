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
    narrativa: null,
    plano_conta: null,
    situacao: null
  },
  filtros: {
    descricao: null,
    situacao: null,
    tipo_servico: null,
    tipo_item: null,
    filtro_franqueada: null
  }
}
