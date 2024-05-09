export default {
  lista: [],
  estaCarregando: false,
  listaTodaCarregada: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    modalidade_turma: {
      descricao: null
    },
    curso: {
      descricao: null
    },
    livro: {
      descricao: null
    },
    sala_franqueada: null,
    horario: null,
    funcionario: null,
    valor_hora_linhas: null,
    semestre: null,
    descricao: null,
    maximo_alunos: null,
    data_inicio: undefined,
    data_fim: undefined,
    observacao: null,
    situacao: null,
    turma_aula: null,
    excluido: false,
    cursoId: null,
    funcionarioId: null,
    semestreId: null,
    turmaSituacao: null,
    modalidadeId: null,
    livroId: null,
    horarioId: null
  },
  filtros: {
    situacao: [],
    dia_semana: [],
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
