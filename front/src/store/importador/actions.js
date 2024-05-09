/* import Request from '../../utils/request' */

export default {
  enviarArquivo ({state}) {
    return new Promise((resolve, reject) => {
      const data = {
        'Resultados': []
      }

      resolve(data)
      /* Request.post('/importador/enviar', {arquivo: state.arquivoSelecionado}) */
      /*   .then(resolve) */
      /*   .catch(reject) */
    })
  }
}
