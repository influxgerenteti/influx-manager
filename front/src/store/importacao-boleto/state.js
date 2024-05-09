export default {
  lista: [],
  listaDeBoletos: [],
  totalItensDeBoletos: null,
  listaDeBoletosNE: [],
  totalItensDeBoletosNE: null,
  estaCarregando: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    arquivo: null
  },
  filtros: {
    vencimento_de: '',
    vencimento_ate: '',
    data_emissao_de: '',
    data_emissao_ate: '',
    situacao_cobranca: ['PEN'],
    pessoa_aluno: ''
  }
}
