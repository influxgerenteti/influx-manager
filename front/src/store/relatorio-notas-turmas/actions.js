import Request from "@/utils/request";

export default {

    // Lista os dados para preenchimento do relatório
    listar({state, commit}, params) {
        commit('SET_ESTA_CARREGANDO', true)

        return new Promise((resolve, reject) => {
            Request.get('/relatorios/nota-turma' + (params ? '?' + params : ''), null).then(
                response => {
                    commit('SET_LISTA', response.body)
                    commit('SET_LISTA_EXCEL', response.body)
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