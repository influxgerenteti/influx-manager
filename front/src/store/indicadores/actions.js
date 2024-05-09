import Request from '../../utils/request'

const url = '/indicadores'

export default {

  listar ({ state, commit }, filtros) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { ...filtros })
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          commit('SET_LISTA', response.body.corpo.indicadores)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  }
}
