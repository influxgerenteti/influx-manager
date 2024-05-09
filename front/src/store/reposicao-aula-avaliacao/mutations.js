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

  SET_FILTRO_DATA_AGENDAMENTO_DE (state, value) {
    state.filtros.data_agendamento_de = value
  },

  SET_FILTRO_DATA_AGENDAMENTO_ATE (state, value) {
    state.filtros.data_agendamento_ate = value
  },

  SET_FILTRO_ITEM (state, value) {
    state.filtros.item = value
  },

  SET_FILTRO_RESPONSAVEL_PELA_EXECUCAO (state, value) {
    state.filtros.responsavel_execucao = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = ""
    if(value != null){
      state.filtros.situacao = [value]
    }
  },

  SET_MAKE_UP_MID_TERM_ORAL (state, value) {
    state.itemSelecionado.nota_mid_term_oral = value
  },

  SET_MAKE_UP_MID_TERM_TEST (state, value) {
    state.itemSelecionado.nota_mid_term_test = value
  },

  SET_MAKE_UP_MID_TERM_COMPOSITION (state, value) {
    state.itemSelecionado.nota_mid_term_composition = value
  },

  SET_MAKE_UP_MID_TERM_ESCRITA (state, value) {
    state.itemSelecionado.nota_mid_term_escrita = value
  },

  SET_MAKE_UP_FINAL_ORAL (state, value) {
    state.itemSelecionado.nota_final_oral = value
  },

  SET_MAKE_UP_FINAL_TEST (state, value) {
    state.itemSelecionado.nota_final_test = value
  },

  SET_MAKE_UP_FINAl_COMPOSITION (state, value) {
    state.itemSelecionado.nota_final_composition = value
  },

  SET_MAKE_UP_FINAL_ESCRITA (state, value) {
    state.itemSelecionado.nota_final_escrita = value
  },

  SET_RETAKE_MID_TERM_ORAL (state, value) {
    state.itemSelecionado.nota_retake_mid_term_oral = value
  },

  SET_RETAKE_MID_TERM_TEST (state, value) {
    state.itemSelecionado.nota_retake_mid_term_test = value
  },

  SET_RETAKE_MID_TERM_COMPOSITION (state, value) {
    state.itemSelecionado.nota_retake_mid_term_composition = value
  },

  SET_RETAKE_MID_TERM_ESCRITA (state, value) {
    state.itemSelecionado.nota_retake_mid_term_escrita = value
  },

  SET_RETAKE_FINAL_ORAL (state, value) {
    state.itemSelecionado.nota_retake_final_oral = value
  },

  SET_RETAKE_FINAL_TEST (state, value) {
    state.itemSelecionado.nota_retake_final_test = value
  },

  SET_RETAKE_FINAL_COMPOSITION (state, value) {
    state.itemSelecionado.nota_retake_final_composition = value
  },

  SET_RETAKE_FINAL_ESCRITA (state, value) {
    state.itemSelecionado.nota_retake_final_escrita = value
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
