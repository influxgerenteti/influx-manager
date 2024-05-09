export default {
  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_TOTAL_ITENS (state, total) {
    state.totalItens = total
    state.todosItensCarregados = state.totalItens <= state.listaChequesPagarReceber.length
  },

  SET_LISTA (state, itens) {
    if (state.paginaAtual === 1) {
      state.listaChequesPagarReceber = itens
      return
    }

    state.listaChequesPagarReceber = state.listaChequesPagarReceber.concat(itens)
  },

  SET_CHEQUE_SELECIONADO (state, chequeID) {
    state.chequeSelecionadoId = chequeID
  },

  LIMPAR_CHEQUE (state) {
    state.objCheque = {
      id: null,
      numero: null,
      banco: null,
      data_bom_para: null,
      data_entrada: null,
      valor: '',
      situacao: '',
      agencia: '',
      conta: '',
      observacao: '',
      titular: '',
      complemento: ''
    }
  },

  SET_CHEQUE (state, chequeObj) {
    state.objCheque = chequeObj
  },

  SET_FILTRO_TIPO (state, value) {
    state.filtros.tipo = value
  },

  SET_FILTRO_MES_ENTRADA (state, value) {
    state.filtros.mes_entrada = value
  },

  SET_FILTRO_MES_BOM_PARA (state, value) {
    state.filtros.mes_bom_para = value
  },
  SET_FILTRO_DATA_ENTRADA_INICIAL (state, value) {
    state.filtros.data_entrada_inicial = value
  },
  SET_FILTRO_DATA_ENTRADA_FINAL (state, value) {
    state.filtros.data_entrada_final = value
  },
  SET_FILTRO_DATA_BOM_PARA_INICIAL (state, value) {
    state.filtros.data_bom_para_inicial = value
  },
  SET_FILTRO_DATA_BOM_PARA_FINAL (state, value) {
    state.filtros.data_bom_para_final = value
  },
  SET_FILTRO_DATA_DEVOLVIDO_INICIAL (state, value) {
    state.filtros.data_devolvido_inicial = value
  },
  SET_FILTRO_DATA_DEVOLVIDO_FINAL (state, value) {
    state.filtros.data_devolvido_final = value
  },
  SET_FILTRO_NUMERO_CHEQUE (state, value) {
    state.filtros.numero_cheque = value
  },
  SET_FILTRO_ALUNO (state, value) {
    state.filtros.aluno = value
  },
  SET_FILTRO_CONTA (state, value) {
    state.filtros.conta = value
  },
  SET_FILTRO_BANCO (state, value) {
    state.filtros.banco = value
  },
  SET_FILTRO_VALOR_INICIAL (state, value) {
    state.filtros.valor_inicial = value
  },
  SET_FILTRO_VALOR_FINAL (state, value) {
    state.filtros.valor_final = value
  },
  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  }
}
