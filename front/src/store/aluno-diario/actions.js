import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

const url = 'aluno_diario'

export default {

  buscarAvaliacaoAlunosPorTurma ({state, commit}, data) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_avaliacoes_turma/${data.turma}`, data)
        .then(response => {
          commit('SET_LISTA_AVALIACOES_TURMA', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  lancarAtualizarFrequencias ({state, commit}, data) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.post(`/${url}/lancar_atualizar_frequencias`, data)
        .then(response => {
          // console.log('RESPONSE', response.body.corpo)
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(true)
          // if (!isHomework) {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })
          // }
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          if (error.status <= 409) {
            EventBus.$emit('criarAlerta', {
              tipo: 'A',
              mensagem: error.body.mensagem
            })
          }
          reject(error)
        })
    })
  },

  lancarAtualizarNotas ({state, commit}, data) {
    // commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/lancar_atualizar_notas`, data)
        .then(response => {
          commit('LIMPAR_CAMPOS')
          // commit('SET_ITEM_SELECIONADO', response.body.corpo)
          // commit('SET_ESTA_CARREGANDO', false)
          resolve(true)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })
        })
        .catch(error => {
          // commit('SET_ESTA_CARREGANDO', false)
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }

}
