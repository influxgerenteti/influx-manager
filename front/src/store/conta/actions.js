import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {

  getLista ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/conta/listar', {pagina: state.paginaAtual, order: state.order, direcao: state.direcao})
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

  getItem ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/conta/${state.itemId}`)
        .then(response => {
          commit('SET_ITEM', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criar ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/conta/criar', state.item)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conta criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao criar conta.'
          })
        })
    })
  },

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/conta/atualizar/${state.item.id}`, state.item)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: `Conta atualizada com sucesso!`
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

  /* atualizarSituacao ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/tipo_movimento_estoque/atualizarSituacao/${state.item.id}`, state.item)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })
        })
        .catch(error => {
          reject(error)
          console.log(error.body.mensagem)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  } */

}
