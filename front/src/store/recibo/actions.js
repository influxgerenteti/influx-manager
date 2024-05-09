import Request from '../../utils/request'

const url = '/recibo'

export default {

  gerarRecibo ({ state, commit }, parametros) {
    return new Promise((resolve, reject) => {
      Request.get(`${url}/gerar_recibo`, parametros)
        .then(pdf => {
          resolve(pdf)
        })
        .catch(reject)
    })
  }

}
