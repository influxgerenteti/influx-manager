import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

const url = 'convenio'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, order: state.order, direcao: state.direcao, ...state.filtros})
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  listarConveniosNacionais ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar_convenios_nacionais`, {...state.filtrosNacionais, pagina: state.paginaAtual, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
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
      Request.get(`/${url}/${state.itemSelecionadoID}`)
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
      Request.post(`/${url}/criar`, state.itemSelecionado)
        .then(response => {
          resolve({id: response.body.corpo.objetoORM})
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Criado com sucesso!'
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
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          commit('SET_ESTA_CARREGANDO', false)

          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  inativar ({state, commit}, convenioId) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/inativar/${convenioId}`)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          commit('SET_ESTA_CARREGANDO', false)

          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  followup ({state, commit}, parametros) {
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/followup/${state.itemSelecionado.id}`, { ...parametros })
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
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
