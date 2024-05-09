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

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
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

  SET_FRANQUEADA (state, value) {
    state.itemSelecionado.franqueada_id = value
  },

  SET_SALA (state, value) {
    state.itemSelecionado.sala_id = value
  },

  SET_LOTACAO (state, value) {
    state.itemSelecionado.lotacao_maxima = value
  },

  SET_PERSONAL (state, value) {
    state.itemSelecionado.personal = value
  },

  SET_SITUACAO (state, value) {
    state.itemSelecionado.situacao = value
  },

  SET_FILTRO_APENAS_SALA_ATIVA (state, value) {
    state.filtros.apenas_sala_ativa = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      franqueada_id: null,
      sala: null,
      sala_id: null,
      lotacao_maxima: null,
      personal: null,
      situacao: null
    }
    state.filtros = {
      apenas_sala_ativa: false
    }
  },

  SET_DISPONIBILIDADES(state, value) {
    state.disponibilidades = value
  },

  SET_FILTRO_PERSONAL(state, value) {
    state.filtros.personal = value
  }
}
