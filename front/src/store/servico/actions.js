import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

const converterDadosParaEnvio = item => {
  const data = Object.assign({}, item)
  data.aluno = item.aluno ? item.aluno.id : null
  data.item = item.item ? item.item.id : null
  data.funcionario = item.funcionario ? item.funcionario : null
  return data
}
const url = 'servico'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
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

  buscar ({state, commit}, id) {
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_ITEM_SELECIONADO_ID', id)

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
          resolve()
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
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar/${state.itemSelecionado.id}`, converterDadosParaEnvio(state.itemSelecionado))
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
