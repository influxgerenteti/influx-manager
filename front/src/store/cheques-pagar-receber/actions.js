import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import {toNumber} from '../../utils/number'

export default {

  getListaChequesPagarReceber ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get('/cheque/listar', { pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao })
        .then(response => {
          const data = response.body.corpo
          commit('SET_LISTA', data.itens)
          commit('SET_TOTAL_ITENS', data.total)
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

  getChequesPagarReceber ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/cheque/${state.chequeSelecionadoId}`)
        .then(response => {
          const data = {...state.objCheque, ...response.body.corpo}
          data.valor = toNumber(data.valor)

          commit('SET_CHEQUE', data)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  criarChequePagarReceber ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/cheque/criar', state.objCheque)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Cheque criado com sucesso!'
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

  atualizarChequePagarReceber ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/cheque/atualizar/${state.objCheque.id}`, state.objCheque)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Cheque alterado com sucesso!'
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

  removerChequePagarReceber ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/cheque/remover/${state.chequeSelecionadoId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Cheque excluido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao excluir o cheque selecionado.'
          })
        })
    })
  },

  devolverChequePagarReceber ({state}, listaParametros) {
    return new Promise((resolve, reject) => {
      Request.patch('/cheque/devolver-cheques', listaParametros)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Devolução do cheque realizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao efetuar a devolução do cheque selecionado.'
          })
        })
    })
  },

  baixarChequePagarReceber ({state}, listaParametros) {
    return new Promise((resolve, reject) => {
      Request.patch('/cheque/baixar-cheques', listaParametros)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Baixa realizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao efetuar a baixa no cheque selecionado.'
          })
        })
    })
  },

  removerChequesMultiplos ({state}, listaParametros) {
    return new Promise((resolve, reject) => {
      Request.post('/cheque/excluir-multiplos', listaParametros)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Cheques excluidos com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao efetuar a exclusão dos cheques selecionados.'
          })
        })
    })
  }

}
