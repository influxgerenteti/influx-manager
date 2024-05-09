export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
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

  SET_CONCILIAR_VARIOS (state, value) {
    state.dadosConciliarVarios = value
  },

  LIMPAR_CONCILIAR_VARIOS (state, value) {
    state.dadosConciliarVarios = {
      data_contabil: '',
      data_deposito: '',
      ids: []
    }
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      data_contabil: '',
      data_deposito: '',
      numero_documento: '',
      valor_lancamento: '',
      plano_conta: null,
      forma_pagamento: null,
      operacao: 'C',
      conciliado: 'S',
      observacao: null
    }
  },

  LIMPAR_TRANSFERIR (state) {
    state.transferir = {
      data_contabil: '',
      conta_destino: null,
      conta_origem: null,
      valor_lancamento: '',
      observacao: null
    }
  },

  LIMPAR_TRANSFERIR_EXISTENTE (state) {
    state.transferirExistente = {
      id: null,
      conta_destino: null
    }
  },

  LIMPAR_ESTORNAR (state) {
    state.estornar = {
      id: null,
      data_estorno: '',
      observacao: null
    }
  },

  SET_SALDO_INICIAL (state, value) {   
    console.log('saldo',value) 
    state.saldoInicial = value
  },

  SET_TOTAL_ENTRADAS (state, value) {    
    state.totalEntradas = value
  },

  SET_TOTAL_SAIDAS (state, value) {
    state.totalSaidas = value
  },

  SET_TOTAL_CONCILIADOS (state, value) {
    state.totalConciliados = value
  },

  SET_TOTAL_NAO_CONCILIADOS (state, value) {
    state.totalNaoConciliados = value
  },
  SET_ORIGENS(state, origem = null, origemAvancada = null){
    state.filtros.origem = origem
  },

  LIMPAR_FILTROS (state) {
    state.filtros.data_lancamento_inicio = new Date(new Date().setMonth((new Date().getMonth()) - 1)).format("DD/MM/YYYY")
    state.filtros.data_lancamento_fim = (new Date()).format("DD/MM/YYYY")
    state.filtros.valor_lancamento_de = ''
    state.filtros.valor_lancamento_ate = ''
    state.filtros.forma_cobranca = null
    state.filtros.usuario = null
    state.filtros.numero_lancamento = null
    state.filtros.numero_cheque_cartao = null
    state.filtros.categoria = null
    state.filtros.avancado = false
    state.filtros.mes = {value: (new Date()).getMonth(), text: (new Date()).toLocaleString('pt-br', {month: 'long'})}
    state.filtros.ano = (new Date()).getFullYear()
    state.filtros.avancado = true
  }
}
