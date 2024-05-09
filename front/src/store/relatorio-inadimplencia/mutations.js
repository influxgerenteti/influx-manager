export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
 
  },

  SET_RESUMO(state, resumo) {
    if (state.paginaAtual === 1) {
      state.resumo = resumo;
     } 
   else {
      // Utilize Vue.set para garantir a reatividade
      Vue.set(state, 'resumo', resumo);
    }
  
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


  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },
  SET_PARAMETROS (state, value) {
    state.parametros = value
  },
}
