export default {
  lista: [],
  resumo: [],
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
     situacao: [],
    classificacao: null,
    forma_cobranca: null,

    exibicao: 0,
    tipo_relatorio: 0,
    tipo_ocorrencia: null
  }
}
