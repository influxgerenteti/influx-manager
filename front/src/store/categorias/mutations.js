export default {
  listaCategorias (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaCategorias = lista
      return
    }

    state.listaCategorias = state.listaCategorias.concat(lista)
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaCategorias.length
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

  setCategoria (state, categoria) {
    state.objCategoria = categoria
  },

  setCategoriaSelecionada (state, categoriaId) {
    state.categoriaSelecionadaId = categoriaId
  },

  limparCategoria (state) {
    state.objCategoria = {
      nome: '',
      exclusao: 0
    }
  },

  setNome (state, nome) {
    state.objCategoria.nome = nome
  },

  setExclusao (state, exclusao) {
    state.objCategoria.exclusao = exclusao
  }
}
