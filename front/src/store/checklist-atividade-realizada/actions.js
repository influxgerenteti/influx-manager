import Request from '../../utils/request'

const url = '/checklist_atividade_realizada'

export default {
  buscarAtividadeRealizada ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_por_usuario`, {usuario: state.usuario, atividades_diarias: state.atividades_diarias, atividades_semanais: state.atividades_semanais, atividades_mensais: state.atividades_mensais, atividades_atemporais: state.atividades_atemporais})
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  checarAtividade ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, {usuario: state.usuario, checklist_atividade: state.checklist_atividade, checklist: state.checklistId})
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo.objetoORM)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  removerChecado ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.delete(`${url}/remover/${state.checkedId}`)
        .then(() => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  }
}
