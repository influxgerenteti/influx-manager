import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

/**
 * Avaliar se é necessario, caso não seja, realizar os passos abaixo:
 * 1 - Remover este arquivo(deletar no caso)
 * 2 - Excluir referencia no index.js do modulo
 * 3 - Alterar o Lista.vue/Formulario.vue para remover a referencia desta ação
 **/
const url = 'diario_personal'

export default {

  buscarLicoesAplicadasPorContrato ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_licoes_aplicadas_por_contrato/${state.contratoSelecionadoID}`)
        .then(response => {
          const lista = response.body.corpo
          commit('SET_LICOES_REALIZADAS', lista)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          console.info(error)
          reject(error)
        })
    })
  },

  buscarDiarioPorContrato ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_diario_por_contrato/${state.contratoSelecionadoID}`, {agendamento_personal: state.agendamento_personal})
        .then(response => {
          const lista = response.body.corpo
          commit('SET_LISTA_DIARIO', lista)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarHistoricoPorContrato ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_historico_por_contrato/${state.contratoSelecionadoID}`)
        .then(response => {
          const lista = response.body.corpo
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarAvaliacoesPorContrato ({state, commit}, data) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_avaliacoes_por_contrato/${state.contratoSelecionadoID}`, data)
        .then(response => {
          const lista = response.body.corpo
          commit('SET_LISTA_AVALIACAO_ALUNO_PERSONAL', lista)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  lancarAtualizarFrequencias ({state, commit}, parametrosBackEnd) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/lancar_atualizar_frequencia`, {...parametrosBackEnd})
        .then(response => {
          const lista = response.body.corpo
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  lancarAtualizarNotas ({state, commit}, parametrosBackEnd) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/lancar_atualizar_notas`, {...parametrosBackEnd})
        .then(response => {
          const lista = response.body.corpo
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: response.body.mensagem
          })
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }
}
