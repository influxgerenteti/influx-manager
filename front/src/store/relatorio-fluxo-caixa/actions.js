import Request from "@/utils/request";

export default {
    listar({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)
        let params = ''
        let filtros = state.filtros

        for(const [key, value] of Object.entries(filtros)) {
            if(value){
                params += (params ? '&' : params) + key + '=' + value
            }
        }
        return new Promise((resolve, reject) => {
            Request.get('/relatorios/fluxocaixa' + (params ? '?' + params : '')).then(
                response => {
                    commit('SET_LISTA', response.body)
                    commit('SET_ESTA_CARREGANDO', false)
                    commit('SET_TOTAIS', response.body)
                    commit('SET_EXCEL_LIST', response.body)
                    resolve()
                },
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            );

        }
        
        )}
}