import Request from '../../utils/request'

const url = '/cargo'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, {pagina: state.paginaAtual})
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
  }
}