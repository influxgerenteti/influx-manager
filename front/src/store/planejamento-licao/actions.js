import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/planejamento_licao/listar', {pagina: state.paginaAtual, order: state.order, direcao: state.direcao})
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

  buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/planejamento_licao/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post('/planejamento_licao/criar', state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Planejamento criado com sucesso!'
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

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/planejamento_licao/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Planejamento atualizado com sucesso!'
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
