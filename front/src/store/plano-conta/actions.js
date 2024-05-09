import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import store from '../index'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/plano_conta/listar', {pagina: state.paginaAtual})
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_ARVORE_ITENS', response.body.corpo)
          commit('SET_TOTAL_ITENS', response.body.corpo.length)
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
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/plano_conta/${state.itemSelecionadoID}`)
        .then(response => {
          const object = Object.assign({}, response.body.corpo)

          if (transformID) {
            object.pai_data = object.pai
            object.pai = object.pai ? object.pai.id : null
          }

          commit('SET_ITEM_SELECIONADO', object)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state}) {
    return new Promise((resolve, reject) => {
      state.itemSelecionado.franqueada = store.state.root.franqueadaSelecionada
      Request.post('/plano_conta/criar', state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Plano de conta criado com sucesso!'
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

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/plano_conta/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Plano de conta atualizado com sucesso!'
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
  }
}
