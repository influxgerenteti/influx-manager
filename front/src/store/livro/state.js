export default {
  lista: [],
  estaCarregando: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    sistema_avaliacao: null,
    idioma: null,
    curso: null,
    item: null,
    planejamento_licao: null,
    proximo_livro: null,
    idade_minima: null,
    maximo_alunos: null,
    numero_aulas: 0,
    situacao: null,
    descricao: null
  },
  filtros: {
    descricao: null,
    idioma: null,
    curso: null
  }
}
