export default {
  SET_ESTADO_FILTRO_ID (state, estadoId) {
    state.estadoSelecionadoId = estadoId
  },

  SET_LISTA_CIDADE (state, cidades) {
    state.lista = cidades
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  }
}
