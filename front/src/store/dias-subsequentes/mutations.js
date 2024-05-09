export default {
  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_LISTA_DIAS_SUBSEQUENTES (state, lista) {
    state.listaDias = lista
  },

  SET_LISTA_DIAS_FRANQUEADA_SELECIONADA (state, lista) {
    state.listaDiasDaFranqueada = lista
  }
}
