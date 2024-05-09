export default {

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaModulo = lista
      return
    }
    state.listaModulo = state.listaModulo.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  setListaModulosPais (state, lista) {
    state.listaModulosPais = lista
  },

  setModuloSelecionado (state, moduloId) {
    state.moduloSelecionadoId = moduloId
  },

  limparModulo (state) {
    state.objModulo = {
      nome: null,
      url: null,
      situacao: null,
      modulo_pai_id: null
    }
  },

  setModulo (state, modulo) {
    state.objModulo = modulo
  },

  setNome (state, nome) {
    state.objModulo.nome = nome
  },

  setURL (state, url) {
    state.objModulo.url = url
  },

  setSituacao (state, situacao) {
    state.objModulo.situacao = situacao
  },

  setModuloPai (state, moduloPai) {
    state.objModulo.modulo_pai_id = moduloPai
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaModulo.length
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

  SET_PERMISSAO (state, value) {
    const permissoes = {}

    if (value) {
      value.map(item => {
        permissoes[item.descricao] = item
      })
    }

    state.permissoes = permissoes
  }

}
