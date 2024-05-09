export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_LISTA_CONVENIO (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaConvenio = lista
      return
    }

    state.listaConvenio = state.listaConvenio.concat(lista)
  },

  SET_LISTA_INTERESSADO_ALUNO (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaInteressadoAluno = lista
      return
    }

    state.listaInteressadoAluno = state.listaInteressadoAluno.concat(lista)
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_TOTAL_ITENS_CONVENIO (state, totalItens) {
    state.totalItensConvenio = totalItens
    state.todosItensCarregadosConvenios = state.totalItensConvenio <= state.listaConvenio.length
  },

  SET_TOTAL_ITENS_INTERESSADO_ALUNO (state, totalItens) {
    state.totalItensInteressadoAluno = totalItens
    state.todosItensCarregadosInteressadoAluno = state.totalItensInteressadoAluno <= state.listaInteressadoAluno.length
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

  SET_FILTROS (state, value) {
    state.filtros = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null
    }
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
