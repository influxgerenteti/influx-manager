import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import store from '../index'

const url = '/notificacoes'

export default {

  toastTime ({state, dispatch, commit}) {
    const datetime = new Date()
    datetime.setMinutes(datetime.getMinutes() + 5)
    return new Promise((resolve, reject) => {
      const parametros = {
        usuario: store.state.root.usuarioLogado.id,
        data_prorrogacao: datetime.toISOString(),
        is_lida: 0
      }

      Request.get(`${url}/listar`, parametros)
        .then(response => {
          const notificacoesData = response.body.corpo
          commit('SET_LISTA_TOAST', notificacoesData)
          resolve()
        })
        .catch(reject)
    })
  },

  atualizarNotificacao ({state, dispatch, commit}, parametros) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.itemSelecionadoID}`, parametros)
        .then(response => {
          resolve()
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
