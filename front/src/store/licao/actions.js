import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/licao/listar', {pagina: state.paginaAtual})
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

  buscarLicoesPorLivro ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/licao/buscar_por_livro/${state.livroSelecionadoID}`)
        .then(response => {
          let listaLicoes = response.body.corpo
          commit('SET_ESTA_CARREGANDO', false)
          resolve(listaLicoes)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  /*   buscar ({state, commit}) {
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
  }, */

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post('/licao/criar', state.itemSelecionado)
        .then(response => {
          resolve(response.body.corpo.objetoORM.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Lição adicionada com sucesso!'
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

  excluir ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/licao/remover/${state.itemSelecionadoID}`)
        .then(response => {
          resolve()
          // EventBus.$emit('criarAlerta', {
          //   tipo: 'S',
          //   mensagem: 'Lição excluída com sucesso!'
          // })
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
