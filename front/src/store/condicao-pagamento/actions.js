import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/condicao_pagamento/listar?pagina=${state.paginaAtual}`)
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

  buscar ({state, commit}, {transformID} = {}) {
    return new Promise((resolve, reject) => {
      Request.get(`/condicao_pagamento/${state.itemSelecionadoID}`)
        .then(response => {
          const object = Object.assign({}, response.body.corpo)

          commit('SET_ITEM_SELECIONADO', object)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/condicao_pagamento/criar', state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Condição de pagamento criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao criar a condição de pagamento: ${error.body.mensagem}`
          })
        })
    })
  },

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/condicao_pagamento/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Condição de pagamento atualizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao atualizar a condição de pagamento.'
          })
        })
    })
  }
}
