import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const converterDadosParaEnvio = (item) => {
  const data = Object.assign({}, item)
  data.valor_hora_linhas = item.valor_hora_linhas.id
  data.nivel_instrutor = item.nivel_instrutor.id

  return data
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/valor_hora/listar', {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
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

  criar ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/valor_hora/criar', converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Valor hora atualizado com sucesso!'
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
      Request.patch(`/valor_hora/atualizar/${state.itemSelecionado.id}`, converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Valor hora atualizado com sucesso!'
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
