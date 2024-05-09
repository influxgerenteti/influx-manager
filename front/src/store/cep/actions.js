import Request from '../../utils/request'

const url = '/cep'

export default {

  buscar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar/${state.numero_cep}`)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  }

}
