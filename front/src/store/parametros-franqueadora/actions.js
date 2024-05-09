import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {

  getItem ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/parametros_franqueadora/listar`)
        .then(response => {
          commit('SET_ITEM', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/parametros_franqueadora/atualizar/${state.item.id}`, state.item)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: `Parâmetros alterados com sucesso!`
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }

}
