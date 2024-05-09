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

  SET_ARQUIVO (state, value) {
    state.itemSelecionado.arquivo = value
  },

  SET_LISTA_DE_BOLETOS (state, value) {
    state.listaDeBoletos = value
    state.totalItensDeBoletos = value.length
  },

  SET_LISTA_DE_BOLETOS_NE (state, value) {
    state.listaDeBoletosNE = value
    state.totalItensDeBoletosNE = value.length
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      arquivo: null
    }
    state.filtros = {
      sacado: '',
      vencimento_de: '',
      vencimento_ate: '',
      data_emissao_de: '',
      data_emissao_ate: '',
      situacao_cobranca: ['PEN']
    }
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
