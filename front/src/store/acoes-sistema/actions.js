import Request from '../../utils/request'

const url = '/acao_sistema'

export default {

  buscaAcoesSistema ({ state, commit }) {
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`)
        .then(response => {
          resolve(response.body.corpo.itens)
        })
        .catch(reject)
    })
  }

}
