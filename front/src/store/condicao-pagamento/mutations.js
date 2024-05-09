import {round} from '../../utils/number'

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
    state.itemSelecionado = {
      id: null,
      descricao: null,
      quantidade_parcelas: null,
      parcelas: []
    }
  },

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_QUANTIDADE_PARCELAS (state, value) {
    const parcelas = []
    const quantidade = parseFloat(value)
    let total = 0

    for (let index = 0; index < quantidade; index++) {
      let percentual = round(100 / quantidade)

      if ((index + 1) === quantidade) {
        const diff = total + percentual - 100
        percentual = round(percentual - diff)
      }

      total = round(total + percentual)

      parcelas.push({
        numero_parcela: index + 1,
        dias_vencimento: null,
        percentual_parcela: percentual
      })
    }

    state.itemSelecionado.quantidade_parcelas = quantidade
    state.itemSelecionado.parcelas = parcelas
  },

  SET_PARCELA_DIAS_VENCIMENTO (state, {parcela, valor}) {
    state.itemSelecionado.parcelas[parcela].dias_vencimento = valor
  },

  SET_PARCELA_PERCENTUAL_PARCELA (state, {parcela, valor}) {
    state.itemSelecionado.parcelas[parcela].percentual_parcela = valor
  }

}
