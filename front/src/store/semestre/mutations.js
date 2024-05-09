export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_LISTA_ATUAIS (state, res) {
    if (res.paginaAtual === 1) {
      state.listaAtuais.lista = res.lista
      return
    }

    state.listaAtuais.lista = state.listaAtuais.lista.concat(res.lista)
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_TOTAL_ITENS_ATUAIS (state, totalItens) {
    state.listaAtuais.totalItens = totalItens
    state.listaAtuais.todosItensCarregados = state.listaAtuais.totalItens <= state.listaAtuais.lista.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  SET_PAGINA_ATUAL_ATUAIS (state, pagina) {
    state.listaAtuais.paginaAtual = pagina
  },

  SET_LISTAR_PROXIMOS (state, listarProximos) {
    state.listarProximos = listarProximos
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  INCREMENTAR_PAGINA_ATUAL_ATUAIS (state) {
    state.listaAtuais.paginaAtual++
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

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_ANTERIOR_ATUAL_PROXIMO (state, value) {
    state.filtros.anterior_atual_proximo = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      descricao: null,
      data_inicio: '',
      data_termino: ''
    }

    state.itemSelecionado.filtros = {
      anterior_atual_proximo: false
    }
  }
}
