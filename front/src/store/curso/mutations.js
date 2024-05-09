export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }
    state.lista = state.lista.concat(lista)
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

  SET_SIGLA (state, value) {
    state.itemSelecionado.sigla = value
  },

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_IDIOMA (state, value) {
    state.itemSelecionado.idioma = value
  },
  
  SET_MODALIDADE_TURMA (state, value) {
    state.itemSelecionado.modalidade_turma = value
  },

  SET_SERVICO (state, value) {
    state.itemSelecionado.servico = value
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      sigla: null,
      descricao: null,
      idioma: null,
      idade_minima: null,
      idade_maxima: null,
      modalidade_turma: null,
      servico: null,
      intensidade_regular: null,
      intensidade_semi_intensivo: null,
      intensidade_intensivo: null,
      intensidadeSelecionada: null,
      situacao: null
    }
  }
}
