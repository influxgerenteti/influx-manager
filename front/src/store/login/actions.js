import Request from '../../utils/request'

export default {

  doLogin ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/usuario/login', {cpfEmail: state.emailCPF, senha: state.senha}, true)
        .then(resolve)
        .catch(reject)
    })
  }

}
