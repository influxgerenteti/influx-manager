import Request from '../../utils/request'

export default {

  enviarEmail ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post('/usuario/enviar-email-redefinir-senha', {cpfEmail: state.email})
        .then(response => {
          commit('setMensagemSucesso', response.body.mensagem)
          resolve()
        })
        .catch(error => {
          commit('setMensagemErro', error.body.mensagem)
          reject(new Error(error.message))
        })
    })
  }

}
