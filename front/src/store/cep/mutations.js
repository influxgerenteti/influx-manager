export default {
  SET_CEP_SELECIONADO (state, objCep) {
    state.objCep = objCep
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_CEP_NUMERO (state, cep) {
    state.numero_cep = cep
  }
}
