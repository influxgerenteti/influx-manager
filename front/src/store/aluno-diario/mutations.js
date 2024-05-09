export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  },

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_TURMA_AULA_ID (state, value) {
    state.turmaAulaId = value
  },

  SET_TURMA_ID (state, value) {
    state.turmaId = value
  },

  SET_LISTA_AVALIACAO (state, objeto) {
    state.alunoAvaliacao.push(objeto)
  },

  SET_LISTA_AVALIACAO_CONCEITUAL (state, objeto) {
    state.alunoAvaliacaoConceituals.push(objeto)
  },

  SET_LISTA_AVALIACOES_TURMA (state, lista) {
    state.listaAvaliacaoTurma = lista
  },

  SET_OBSERVACAO (state, value) {
    state.observacao = value
  },

  SET_FUNCIONARIO (state, value) {
    state.funcionarioId = value
  },

  SET_LISTA_DADOS_ALUNOS (state, objeto) {
    state.dadosAlunos.push(objeto)
  },

  SET_LISTA_LICAO (state, lista) {
    state.licaos = lista
  },

  LIMPAR_CAMPOS (state) {
    state.turmaAulaId = null
    state.turmaId = null
    state.alunoAvaliacao = []
    state.alunoAvaliacaoConceituals = []
    state.observacao = ''
    state.dadosAlunos = []
    state.licaos = []
    state.funcionarioId = null
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      data_aula: null,
      finalizada: false,
      franqueada: {},
      licao: {},
      turma: {
        livro: {},
        sala_franqueada: {sala: {}},
        funcionario: {}
      }
    }
  }

}
