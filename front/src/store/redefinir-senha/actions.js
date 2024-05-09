import Request from '../../utils/request'

export default {

  validarToken ({state}) {
    return new Promise((resolve, reject) => {
      Request.get('/token/buscar', {token: state.token})
        .then(resolve)
        .catch(reject)
    })
  },

  registrarSenha ({state}) {
    return new Promise((resolve, reject) => {
      const body = {
        senha: state.senha,
        confirmarSenha: state.confirmarSenha,
        token: state.token
      }

      Request.post('/token/setar-senha', body)
        .then(resolve)
        .catch(reject)
    })
  }

}
