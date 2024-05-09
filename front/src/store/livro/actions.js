import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const converterDadosParaEnvio = (item) => {
  const data = Object.assign({}, item)

  data.item = data.item ? data.item.id : null
  data.proximo_livro = data.proximo_livro ? data.proximo_livro.id : null
  data.sistema_avaliacao = data.sistema_avaliacao ? data.sistema_avaliacao.id : null
  data.planejamento_licao = data.planejamento_licao ? data.planejamento_licao.id : null

  return data
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/livro/listar', {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
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
      Request.get(`/livro/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criar ({ state }) {
    return new Promise((resolve, reject) => {
      Request.post('/livro/criar', converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response.body.corpo.objetoORM.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Livro adicionada com sucesso!'
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
      Request.patch(`/livro/atualizar/${state.itemSelecionadoID}`, converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Livro atualizada com sucesso!'
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
