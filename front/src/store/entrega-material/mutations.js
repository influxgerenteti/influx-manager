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

  SET_ITEM_SELECIONADO (state, itemId) {
    state.itemSelecionadoId = itemId
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_FILTRO_ALUNO_ID (state, value) {
    state.filtros.aluno = value
  },

  SET_FILTRO_ITEM_ID (state, value) {
    state.filtros.item = value
  },

  SET_FILTRO_USUARIO_ID (state, value) {
    state.filtros.usuario = value
  },

  SET_FILTRO_VALOR_INICIAL (state, value) {
    state.filtros.valor_inicial = value
  },

  SET_FILTRO_VALOR_FINAL (state, value) {
    state.filtros.valor_final = value
  },

  SET_FILTRO_DATA_AULA_INICIO (state, value) {
    state.filtros.data_aula_inicio = value
  },

  SET_FILTRO_DATA_AULA_FIM (state, value) {
    state.filtros.data_aula_fim = value
  },

  SET_FILTRO_DATA_SAIDA_INICIO (state, value) {
    state.filtros.data_saida_inicio = value
  },

  SET_FILTRO_DATA_SAIDA_FIM (state, value) {
    state.filtros.data_saida_fim = value
  },

  SET_FILTRO_DATA_ENTREGA_INICIO (state, value) {
    state.filtros.data_entrega_inicio = value
  },

  SET_FILTRO_DATA_ENTREGA_FIM (state, value) {
    state.filtros.data_entrega_fim = value
  },

  SET_FILTRO_ENTREGUE (state, value) {
    state.filtros.item_entregue = value
  },

  SET_FILTRO_TURMA (state, value) {
    state.filtros.turma = value
  },

  SET_FILTRO_DATA_INICIO (state, value) {
    state.filtros.data_inicio = value
  },

  SET_FILTRO_DATA_FIM (state, value) {
    state.filtros.data_fim = value
  },

  SET_FILTRO_MODALILADE_TURMA (state, value) {
    state.filtros.modalidade_turma = value
  }

}
