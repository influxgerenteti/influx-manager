export default {
  SET_LISTA (state, res) {
    let paginaAtual
    let lista
    if (res instanceof Array) {
      paginaAtual = state.paginaAtual
      lista = res
    } else {
      paginaAtual = res.paginaAtual
      lista = res.lista
    }
    if (paginaAtual === 1) {
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

  SET_FORMATO_IMPRESSAO (state, value) {
    console.log(value)
    state.filtros.formato_impressao = value
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },
  SET_RELATORIO (state, value) {
    state.relatorio = value
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
      id: null
    }
  },

  LIMPAR_FILTROS (state) {
    state.filtros.data_inicial_vencimento = ''
    state.filtros.data_final_vencimento = ''
    state.filtros.valor_inicial = ''
    state.filtros.valor_final = ''
    state.filtros.formato_impressao = ''
    state.filtros.item = null
    state.filtros.forma_cobranca = null
    state.filtros.turma = null
    state.filtros.contrato = null
    state.filtros.nosso_numero = null
    state.filtros.conta = null
    state.filtros.agencia = null
    state.filtros.busca = ''
    state.filtros.situacao = ['VEN']
    state.filtros.mes = {value: (new Date()).getMonth(), text: (new Date()).toLocaleString('pt-br', {month: 'long'})}
    state.filtros.ano = (new Date()).getFullYear()
    state.filtros.tipo_data = 'VENCIMENTO'
    state.filtros.data_inicio = ''
    state.filtros.tipo_fim = ''
  },

  LIMPAR_DADOS (state) {
    state.totalFaturado = 0
    state.totalRecebido = 0
    state.totalMovimentado = 0
    state.totalRecebidoNaoConciliado = 0
    state.totalReceber = 0
    state.totalVencido = 0
    state.totalCancelado = 0
    
  },


  SET_TOTAL_RECEBIDO (state, value) {
    state.totalRecebido = value
  },
  SET_TOTAL_FATURADO (state, value) {
    state.totalFaturado = value
  },
  SET_TOTAL_RECEBIDO_NAO_CONCILIADO (state, value) {
    state.totalRecebidoNaoConciliado = value
  },
  SET_TOTAL_RECEBER (state, value) {
    state.totalReceber = value
  },
  SET_TOTAL_VENCIDO (state, value) {
    state.totalVencido = value
  },
  SET_TOTAL_CANCELADO (state, value) {
    state.totalCancelado = value
  }
}
