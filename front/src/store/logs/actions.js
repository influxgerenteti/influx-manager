import Request from '../../utils/request'

export default {

  getListaLogs ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/log/listar', { pagina: state.paginaAtual, order: state.order, direcao: state.direcao })
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  getLog ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/log/${state.logSelecionado}`)
        .then(response => {
          commit('setLog', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  }

}
