import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = 'movimento_estoque'

export default {
  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, {...state.parametros})
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Estoque ajustado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar o ajuste de estoque'
          })
        })
    })
  }
}
