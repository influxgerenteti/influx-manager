export default {
  SET_LISTA (state, lista) {
      state.lista = lista
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

  /* SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  }, */

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      data_inicial_entrada: '',
      data_final_entrada: '',
      data_inicial_bom_para: '',
      data_final_bom_para: '',
      data_inicial_baixa: '',
      data_final_baixa: '',
      data_inicial_devolucao: '',
      data_final_devolucao: '',
      motivo_devolucao: '',
      conta: '',
      plano_conta: '',
      situacao: null,
      tipo: null,
      detalhado: false,
      agrupadoBanco: false,
      excel: false
    }
  },
  SET_PARAMETROS (state, value) {
    state.parametros = value
},
}
