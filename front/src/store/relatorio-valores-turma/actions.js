import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'


export default {
  listar({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    let params = state.parametros

    return new Promise((resolve, reject) => {
        Request.get('/relatorios/valores-turma' + (params ? '?' + params : ''), null).then(
            response => {
                commit('SET_LISTA', response.body)
                console.log(response.body)
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
