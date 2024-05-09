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
    id: null
  },
  filtros: {
    funcionario: null,
    cargo: null,
    situacao: [],
    data_aniversario_de: undefined,
    data_aniversario_ate: undefined,
    data_cadastro_de: '',
    data_cadastro_ate: ''
  }
}
