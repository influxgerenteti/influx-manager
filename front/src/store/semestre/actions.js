import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

const url = '/semestre'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, {pagina: state.paginaAtual, listar_proximos: state.listarProximos, ...state.filtros})
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

  listarAtuais ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    const paginaAtual = state.listaAtuais.paginaAtual
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, {pagina: paginaAtual, listar_proximos: true, ...state.listaAtuais.filtros})
        .then(response => {
          const data = {
            lista: response.body.corpo.itens,
            paginaAtual: paginaAtual
          }

          commit('SET_LISTA_ATUAIS', data)
          commit('SET_TOTAL_ITENS_ATUAIS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL_ATUAIS')
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
      Request.get(`${url}/${state.itemSelecionadoID}`)
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
      Request.post(`${url}/criar`, state.itemSelecionado)
        .then(response => {
          resolve(response.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Semestre criado com sucesso!'
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
      Request.patch(`${url}/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve(response.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Semestre atualizado com sucesso!'
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
