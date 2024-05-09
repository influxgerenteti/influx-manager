export default {
  listaCartao (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaCartao = lista
      return
    }

    state.listaCartao = state.listaCartao.concat(lista)
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaCartao.length
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

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  setCartao (state, cartao) {
    state.objCartao = cartao
  },

  setCartaoSelecionado (state, cartaoId) {
    state.cartaoSelecionadoId = cartaoId
  },

  SET_FILTRO_ALUNO_ID (state, pessoaId) {
    state.filtros.aluno_pessoa = pessoaId
  },

  SET_FILTRO_OPERADORA_CARTAO (state, operadoraCartao) {
    state.filtros.operadora_cartao = operadoraCartao
  },

  SET_FILTRO_TIPO_TRANSACAO (state, tipoTransacao) {
    state.filtros.tipo_transacao = tipoTransacao
  },

  SET_FILTRO_NUMERO_LANCAMENTO (state, numeroLancamento) {
    state.filtros.numero_lancamento = numeroLancamento
  },

  SET_FILTRO_IDENTIFICADOR (state, identificadorCartao) {
    state.filtros.identificador = identificadorCartao
  },

  SET_FILTRO_SITUACAO (state, situacao) {
    state.filtros.situacao = situacao
  },

  SET_FILTRO_DATA_ESTORNO_INICIO (state, dataInicio) {
    state.filtros.data_estorno_inicio = dataInicio
  },

  SET_FILTRO_DATA_ESTORNO_FIM (state, dataFim) {
    state.filtros.data_estorno_fim = dataFim
  },

  SET_FILTRO_PREVISAO_REPASSE_INICIO (state, previsaoRepasseInicio) {
    state.filtros.previsao_repasse_inicio = previsaoRepasseInicio
  },

  SET_FILTRO_PREVISAO_REPASSE_FIM (state, previsaoRepasseFim) {
    state.filtros.previsao_repasse_fim = previsaoRepasseFim
  },

  SET_FILTRO_VALOR_LIQUIDO_INICIO (state, valorLiquidoInicio) {
    state.filtros.valor_liquido_inicio = valorLiquidoInicio
  },

  SET_FILTRO_VALOR_LIQUIDO_FIM (state, valorLiquidoFim) {
    state.filtros.valor_liquido_fim = valorLiquidoFim
  }
}
