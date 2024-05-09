import Request from "@/utils/request";

export default {

    // Lista os dados para preenchimento do relatório
    listar({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)
        commit('SET_LISTA', [])

        let params = ''
        let filtros = state.filtros
        
        for(const [key, value] of Object.entries(filtros)) {
            if(value){
                let idValue = value
                if(typeof(value) == 'object') {
                    idValue = value.id
                }
                if(idValue){
                    params += (params ? '&' : params) + key + '=' + idValue
                }
                
            }
        }
        return new Promise((resolve, reject) => {
            Request.get('/relatorios/aulas_ocorridas' + (params ? '?' + params : ''), null).then(
                response => {
                    commit('SET_LISTA', response.body)
                    commit('SET_ESTA_CARREGANDO', false)
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