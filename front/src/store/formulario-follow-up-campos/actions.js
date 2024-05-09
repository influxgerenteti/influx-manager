import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/formulario_follow_up_campos'

export default {
  listarPorFormulario ({ state, commit }, formularioId) {
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscarPorFormulario/${formularioId}`)
        .then(response => {
          resolve(response.body.corpo)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criarCampoFormulario ({ state, commit }, objCampo) {
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, objCampo)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Campo criado com sucesso!'
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

  remover ({state, commit}, campoId) {
    return new Promise((resolve, reject) => {
      Request.delete(`${url}/remover/${campoId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Campo removido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover o Campo. ' + error.body.mensagem
          })
        })
    })
  }
}
