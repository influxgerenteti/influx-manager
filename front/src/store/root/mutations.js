
export default {

  setListaMenus (state, menus) {
    state.listaMenus = menus
  },

  setMenuCarregado (state, carregou) {
    state.menuCarregado = carregou
  },

  setListaFavoritos (state, lista) {
    state.listaFavoritos = lista
  },

  setFavorito (state, id) {
    state.objFavorito.modulo = id
  },

  setFavoritoSelecionado (state, favoritoId) {
    state.favoritoSelecionadoId = favoritoId
  },

  setUsuarioLogado (state, usuario) {
    if(usuario){
      state.usuarioLogado = usuario
    }
    
  },

  setRotaAtual (state, rota) {
    state.rotaAtual = rota
  },

  setFranqueadaSelecionada (state, franqueadaId) {
    state.franqueadaSelecionada = franqueadaId
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_PERMISSAO_MODULO_ID (state, value) {
    state.permissaoModuloId = value
  },

  SET_SHOW_BREADCRUMBS (state, value) {
    state.showBreadcrumbs = value
  }
}
