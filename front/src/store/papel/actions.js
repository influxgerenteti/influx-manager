import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/papel'

export default {
  criar ({ state }) {
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, state.itemSelecionado)
        .then(response => {
          resolve(response.body.corpo.objetoORM)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Papel adicionado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  atualizar ({ state }) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.itemSelecionadoID}`, state.itemSelecionado)
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Papel atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  buscarPapeis ({ state, commit }) {
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`)
        .then(response => {
          resolve(response.body.corpo.itens)
        })
        .catch(reject)
    })
  },

  buscarPapel ({ commit }, id) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${id}`)
        .then(response => {
          resolve(response.body.corpo)
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
        })
        .catch(reject)
        .finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  }

}
