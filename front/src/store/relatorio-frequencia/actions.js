import Request from "@/utils/request";

export default {

    // Lista os dados para preenchimento do relatório
    listar({
        state,
        commit
    }) {
        commit('SET_ESTA_CARREGANDO', true)

        let params = state.parametros

        return new Promise((resolve, reject) => {
            Request.get('/relatorios/frequencia' + (params ? '?' + params : ''), null).then(
                response => {
                    commit('SET_LISTA', response.body.dados)
                    commit('SET_RESUMO', response.body.media_frequencia)
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