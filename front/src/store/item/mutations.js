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

  SET_SELECIONADO (state, id) {
    state.itemSelecionadoID = id
  },

  LIMPAR_ITEM (state) {
    state.itemSelecionadoID = null
    state.item = {
      id: null,
      franqueada: null,
      descricao: '',
      unidade_medida: '',
      narrativa: '',
      situacao: '',
      tipo_item: {
        tipo: null
      },
      itemFranqueadas: [
        {
          movimentoEstoques: [],
          saldo_estoque: '',
          estoque_minimo: '',
          valor_compra: 0,
          valor_venda: 0,
          valor_venda_2: 0,
          valor_venda_3: 0,
          valor_venda_4: 0,
          valor_venda_5: 0,
          valor_venda_6: 0
        }
      ],
      tipoItem: {
        tipo: null
      }
    }
  },

  SET_ITEM (state, item) {
    state.item = { ...state.item, ...item }
  },

  SET_FRANQUEADA (state, franqueadaId) {
    state.item.franqueada = franqueadaId
  },

  SET_UNIDADE_MEDIDA (state, unidadeMedida) {
    state.item.unidade_medida = unidadeMedida
  },

  SET_DESCRICAO (state, descricao) {
    state.item.descricao = descricao
  },

  SET_NARRATIVA (state, narrativa) {
    state.item.narrativa = narrativa
  },

  SET_SALDO_ESTOQUE (state, saldo) {
    state.item.saldo_estoque = saldo
  },

  SET_VALOR_VENDA (state, valor) {
    state.item.valor_venda = valor
  },

  SET_VALOR_COMPRA (state, valor) {
    state.item.valor_compra = valor
  },

  SET_ESTOQUE_MINIMO (state, estoque) {
    state.item.estoque_minimo = estoque
  },

  SET_DATA_CADASTRO (state, data) {
    state.item.data_cadastro = data
  },

  SET_SITUACAO (state, situacao) {
    state.item.situacao = situacao
  },

  SET_TIPO_ITEM_ID (state, tipoItemId) {
    state.item.tipo_item = tipoItemId
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

  SET_ESTA_CARREGANDO (state, estaCarregando) {
    state.estaCarregando = estaCarregando
  },

  SET_FILTRO_DESCRICAO (state, valor) {
    state.filtros.descricao = valor
  },

  SET_FILTRO_UNIDADE_MEDIDA (state, valor) {
    state.filtros.unidade_medida = valor
  },

  SET_FILTRO_SALDO_ESTOQUE_INICIAL (state, valor) {
    state.filtros.saldo_estoque_inicial = valor
  },

  SET_FILTRO_SALDO_ESTOQUE_FINAL (state, valor) {
    state.filtros.saldo_estoque_final = valor
  },

  SET_FILTRO_ESTOQUE_MINIMO_INICIAL (state, valor) {
    state.filtros.estoque_minimo_inicial = valor
  },

  SET_FILTRO_ESTOQUE_MINIMO_FINAL (state, valor) {
    state.filtros.estoque_minimo_final = valor
  },

  SET_FILTRO_VALOR_COMPRA_INICIAL (state, valor) {
    state.filtros.valor_compra_inicial = valor
  },

  SET_FILTRO_VALOR_COMPRA_FINAL (state, valor) {
    state.filtros.valor_compra_final = valor
  },

  SET_FILTRO_VALOR_VENDA_INICIAL (state, valor) {
    state.filtros.valor_venda_inicial = valor
  },

  SET_FILTRO_VALOR_VENDA_FINAL (state, valor) {
    state.filtros.valor_venda_final = valor
  },

  SET_FILTRO_SITUACAO (state, valor) {
    state.filtros.situacao = valor
  },

  SET_TIPO_ITEM (state, valor) {
    state.item.tipo_item = valor
  },

  SET_FILTRO_TIPO_ITEM (state, valor) {
    state.filtros.tipo_item = valor
  },

  SET_FILTRO_FRANQUEADA (state, valor) {
    state.filtros.filtro_franqueada = valor
  }

}
