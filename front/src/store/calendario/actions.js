import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

export default {
  listar ({state, commit}, ano) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get('/calendario/listar', {ano: ano})
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_TOTAL_ITENS', response.body.corpo.length)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  verificaFeriadoBancario ({ state, commit }) {
    if (!state.itemSelecionado.dataFeriado) {
      return
    }
    return new Promise((resolve, reject) => {
      const dataObj = state.itemSelecionado.dataFeriado
      // var isoDateTime = new Date(dataObj.getTime() - (dataObj.getTimezoneOffset() * 60000)).toISOString()
      var dateStr = dataObj.toISOString()
      const dataFormatada = dateStr.split('T')[0]
      Request.get(`/calendario/buscaFeriadoBancario`, {dataFeriado: dataFormatada})
        .then(response => {
          commit('SET_DATA', response.body.corpo)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  buscar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.get(`/calendario/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state, commit}, cardEvento) {
    return new Promise((resolve, reject) => {
      Request.post('/calendario/criar', cardEvento)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Evento criado com sucesso!'
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

  atualizar ({state, commit}, cardEvento) {
    return new Promise((resolve, reject) => {
      Request.patch(`/calendario/atualizar/${cardEvento.id}`, cardEvento)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Evento atualizado com sucesso!'
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

  excluir ({state, commit}, cardId) {
    return new Promise((resolve, reject) => {
      Request.delete(`/calendario/remover/${cardId}`)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Evento excluído com sucesso!'
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
