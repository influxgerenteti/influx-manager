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

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
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
      fornecedor_pessoa: null,
      forma_cobranca: null,
      data_vencimento: '',
      valor_parcela: 0,
      numero_parcelas: 0,
      valor_total: 0,
      observacao: null,
      conta: null,
      itens: [],
      parcelas: {},
      plano_conta: [{ plano_conta: null, valor: 0, complemento: null, numero_sequencia: 1 }],
      quitar_primeira_parcela: false
    }
  },

  SET_FORNECEDOR_PESSOA (state, value) {
    state.itemSelecionado.fornecedor_pessoa = value
  },

  SET_DATA_VENCIMENTO (state, value) {
    state.itemSelecionado.data_vencimento = value
  },

  SET_VALOR_TOTAL (state, value) {
    state.itemSelecionado.valor_total = value
  },

  SET_CONTA (state, value) {
    state.itemSelecionado.conta = value
  },

  SET_PARCELAS (state, value) {
    state.itemSelecionado.parcelas = value
  },

  SET_PARCELA_FORMA_COBRANCA (state, { index, value }) {
    const parcela = state.itemSelecionado.parcelas[index]
    parcela.forma_cobranca = value
    parcela.cheque = {
      data_bom_para: parcela.data_vencimento,
      numero: null,
      titular: null,
      banco: null,
      agencia: null,
      conta: null
    }
    state.itemSelecionado.parcelas[index] = Object.assign({}, parcela)
  },

  SET_PARCELA_DATA_VENCIMENTO (state, { index, value }) {
    state.itemSelecionado.parcelas[index].data_vencimento = value
  },

  SET_PARCELA_VALOR (state, { index, value }) {
    state.itemSelecionado.parcelas[index].valor_documento = value
  },

  SET_FILTROS_MES (state, value) {
    state.filtros.mes = value
  },

  SET_FILTROS_ANO (state, value) {
    state.filtros.ano = value
  },

  SET_FILTROS_FORMA_COBRANCA (state, value) {
    state.filtros.forma_pagamento = value
  },

  SET_FILTROS_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_FILTROS_FORNECEDOR (state, value) {
    state.filtros.fornecedor_pessoa = value
  },

  SET_FILTROS_FAVORECIDO (state, value) {
    state.filtros.favorecido_pessoa = value
  },

  SET_FILTROS_DATA_INICIAL_VENCIMENTO (state, value) {
    state.filtros.data_inicial_vencimento = value
  },

  SET_FILTROS_DATA_FINAL_VENCIMENTO (state, value) {
    state.filtros.data_final_vencimento = value
  },

  SET_FILTROS_DATA_INICIAL_PAGAMENTO (state, value) {
    state.filtros.data_inicial_pagamento = value
  },

  SET_FILTROS_DATA_FINAL_PAGAMENTO (state, value) {
    state.filtros.data_final_pagamento = value
  },

  SET_FILTROS_VALOR_INICIAL (state, value) {
    state.filtros.valor_inicial = value
  },

  SET_FILTROS_PLANO_CONTA (state, value) {
    state.filtros.plano_conta = value
  },

  SET_FILTROS_VALOR_FINAL (state, value) {
    state.filtros.valor_final = value
  },

  SET_ADICIONAR_PLANO_CONTA (state) {
    state.itemSelecionado.plano_conta.push({
      plano_conta: null,
      complemento: null,
      valor: 0,
      numero_sequencia: state.itemSelecionado.plano_conta.length + 1
    })
  },

  SET_REMOVER_PLANO_CONTA (state, index) {
    state.itemSelecionado.plano_conta.splice(index, 1)
  },

  SET_PLANO_CONTA_CATEGORIA (state, { index, value }) {
    state.itemSelecionado.plano_conta[index].plano_conta = value
  },

  SET_PLANO_CONTA_VALOR (state, { index, value }) {
    state.itemSelecionado.plano_conta[index].valor = value
  },

  SET_PLANO_CONTA_COMPLEMENTO (state, { index, value }) {
    state.itemSelecionado.plano_conta[index].complemento = value
  }
}
