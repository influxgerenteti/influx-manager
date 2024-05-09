import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {
  getListaCategorias ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/categoria/listar', { pagina: state.paginaAtual })
        .then(response => {
          commit('listaCategorias', response.body.corpo.itens)
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

  criarCategoria ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/categoria/criar', state.objCategoria)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Categoria criada com sucesso!'
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

  atualizarCategoria ({state}) {
    return new Promise((resolve, reject) => {
      const body = {
        nome: state.objCategoria.nome,
        exclusao: state.objCategoria.exclusao || 0
      }

      Request.patch(`/categoria/atualizar/${state.objCategoria.id}`, body)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Categoria atualizada com sucesso!'
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

  getCategoria ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/categoria/${state.categoriaSelecionadaId}`)
        .then(response => {
          commit('setCategoria', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  removerCategoria ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/categoria/remover/${state.categoriaSelecionadaId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Categoria removida com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover a categoria.'
          })
        })
    })
  }
}
