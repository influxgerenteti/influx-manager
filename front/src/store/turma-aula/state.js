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
    data_aula: null,
    finalizada: false,
    franqueada: null,
    licao: {},
    turma: {
      livro: {},
      sala_franqueada: {sala: {}},
      funcionario: {}
    }
  },
  settedTurmaId: null,
  listaLicoesAplicadasTurma: [],
  listaHistoricoAulas: [],
  filtros: {
    situacao: [],
    descricao: '',
    horario: null,
    modalidade: null,
    sala_franqueada: null,
    instrutor: null,
    curso: null,
    livro: null,
    semestre: null,
    data_inicio: null,
    data_fim: null
  },
  situacoes: [
    { valor: 'ABE', descricao: 'Aberta', cor: 'success' },
    { valor: 'FOR', descricao: 'Em formação', cor: 'info' },
    { valor: 'ENC', descricao: 'Encerrada', cor: 'danger' }
  ]
}
