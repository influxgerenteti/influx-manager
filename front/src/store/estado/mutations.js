export default {
  SET_ESTADO_SELECIONADO (state, estadoObj) {
    state.objEstado = estadoObj
  },

  SET_LISTA_ESTADO (state, estados) {
    state.lista = estados
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  }
}
