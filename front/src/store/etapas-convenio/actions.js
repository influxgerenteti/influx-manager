import Request from '../../utils/request'

/**
 * Avaliar se é necessario, caso não seja, realizar os passos abaixo:
 * 1 - Remover este arquivo(deletar no caso)
 * 2 - Excluir referencia no index.js do modulo
 * 3 - Alterar o Lista.vue/Formulario.vue para remover a referencia desta ação
 **/
const url = 'etapas_convenio'

export default {
  buscarTodos ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`)
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  }
}
