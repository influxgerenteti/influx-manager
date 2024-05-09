export default {

  setEmail (state, email) {
    state.email = email
  },

  setMensagemSucesso (state, mensagem) {
    state.mensagemSucesso = mensagem
    state.mensagemErro = null
  },

  setMensagemErro (state, mensagem) {
    state.mensagemErro = mensagem
    state.mensagemSucesso = null
  }

}
