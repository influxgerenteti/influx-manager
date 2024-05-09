import Request from "@/utils/request";

export default {

    // Lista os dados para preenchimento do relatório
    listar({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)
      

        let params = state.parametros

        return new Promise((resolve, reject) => {
            Request.get('/relatorios/consulta-conversao' + (params ? '?' + params : ''), null).then(
                response => {
                    commit("SET_LISTA", response.body);
                    commit("SET_CONTATO", response.body);
                    commit("SET_ESTA_CARREGANDO", false);
                
                    resolve()
                }
            ).catch(
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            )
        })
     
    }
}