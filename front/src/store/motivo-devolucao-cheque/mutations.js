export default {

  SET_LISTA_MOTIVO_DEVOLUCAO_CHEQUE (state, motivos) {
    state.lista = motivos
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  }
}
