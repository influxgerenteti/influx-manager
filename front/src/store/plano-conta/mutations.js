import {montarArvore} from '../../views/plano-conta/plano-conta'

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

  SET_DESCRICAO (state, value) {
    state.itemSelecionado.descricao = value
  },

  SET_TIPO_MOVIMENTO_NOTA (state, value) {
    state.itemSelecionado.tipo_movimento_nota = value
  },

  SET_PAI (state, value) {
    state.itemSelecionado.pai = value
  },

  SET_ARVORE_ITENS (state, value) {
    state.arvoreItens = montarArvore(value)

    const despesas = value.filter(item => item.tipo_movimento_nota === 'E')
    state.selectDespesas = montarArvore(despesas)

    const receitas = value.filter(item => item.tipo_movimento_nota === 'S')
    state.selectReceitas = montarArvore(receitas)
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      descricao: null,
      pai: null,
      tipo_movimento_nota: null,
      situacao: null
    }
  }
}
