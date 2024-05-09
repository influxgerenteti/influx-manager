export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = []
    }
    // if (state.paginaAtual === 1) {
    //   state.lista = lista
    //   return
    // }

    // state.lista = state.lista.concat(lista)

    lista.forEach(newItem => {
      var index = state.lista.findIndex(x => x.id==newItem.id); 
      if ( index === -1){
        state.lista.push(newItem) ;
      } 
    });
    
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

  SET_CONCLUIDO (state, value) {
    state.itemSelecionado.concluido = value
  },

  SET_DATA_AULA (state, value) {
    state.itemSelecionado.data_aula = value
  },

  SET_FILTROS (state, value) {
    state.filtros = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      funcionario: null,
      data_aula: '',
      sala_franqueada: null,
      horario_inicio: '',
      horario_termino: ''
    }
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
