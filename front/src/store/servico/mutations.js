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

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      aluno: null,
      data_solicitacao: null,
      data_conclusao: null,
      item: null,
      concluido: null,
      quantidade: null,
      funcionario: null,
      descricao: null
    }
  },

  SET_ALUNO (state, value) {
    state.itemSelecionado.aluno = value
  },

  SET_DATA_SOLICITACAO (state, value) {
    state.itemSelecionado.data_solicitacao = value
  },

  SET_DATA_PREVISTA (state, value) {
    state.itemSelecionado.data_conclusao = value
  },

  SET_SERVICO (state, value) {
    state.itemSelecionado.item = value
  },

  SET_CONCLUIDO (state, value) {
    state.itemSelecionado.concluido = value
  },

  SET_CANCELAMENTO (state, value) {
    state.itemSelecionado.cancelamento = value
  },

  SET_QUANTIDADE (state, value) {
    state.itemSelecionado.quantidade = value
  },

  SET_FORMA_PAGAMENTO (state, value) {
    state.itemSelecionado.forma_cobranca = value
  },

  SET_VALOR (state, value) {
    state.itemSelecionado.valor = value
  },

  SET_FILTRO_PROTOCOLO (state, value) {
    state.filtros.protocolo = value
  },

  SET_FUNCIONARIO (state, value) {
    state.itemSelecionado.funcionario = value
  },

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_FILTRO_ALUNO_ID (state, value) {
    state.filtros.aluno = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_DATA_SOLICITACAO_DE (state, value) {
    state.filtros.data_solicitacao_de = value
  },

  SET_DATA_SOLICITACAO_ATE (state, value) {
    state.filtros.data_solicitacao_ate = value
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
