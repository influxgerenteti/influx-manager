import Request from "@/utils/request";

export default {
  listar({state, commit}, params) {
    commit('SET_ESTA_CARREGANDO', true)

        return new Promise((resolve, reject) => {
            Request.get('/relatorios/titulos' + (params ? '?' + params : ''), null).then(
                response => {
                    commit('SET_LISTA', response.body)
                    commit('SET_ESTA_CARREGANDO', false)
                    commit('SET_EXCEL_LIST', response.body)
                    commit('SET_RESUMO', response.body)
              
                  resolve()
                }
            ).catch(
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            )
       
        })
        
  },
}