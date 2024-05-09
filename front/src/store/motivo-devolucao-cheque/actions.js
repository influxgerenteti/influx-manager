import Request from '../../utils/request'

const url = '/motivo_devolucao_cheque'

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: state.paginaAtual })
        .then(response => {
          commit('SET_LISTA_MOTIVO_DEVOLUCAO_CHEQUE', response.body.corpo.itens)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  }
}
