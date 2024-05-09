export default {

  setToken (state, token) {
    state.token = token
  },

  setSenha (state, senha) {
    state.senha = senha
  },

  setConfirmarSenha (state, senha) {
    state.confirmarSenha = senha
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
