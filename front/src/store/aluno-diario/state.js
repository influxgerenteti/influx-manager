export default {
  lista: [],
  listaAvaliacaoTurma: [],
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

  turmaAulaId: null,
  turmaId: null,
  alunoAvaliacao: [],
  alunoAvaliacaoConceituals: [],
  observacao: '',
  dadosAlunos: [],
  licaos: [],
  funcionarioId: null
}
