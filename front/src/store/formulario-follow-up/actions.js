import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/formulario_follow_up'

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: state.paginaAtual, ...state.filtros })
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, state.objFormularioFollowUp)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Formulario criada com sucesso!'
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
      Request.patch(`${url}/atualizar/${state.itemSelecionadoID}`, state.objFormularioFollowUp)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Formulario atualizado com sucesso!'
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
