// import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
// import store from '../index'
// import { stringToISODate, dateToString } from '../../utils/date'

const url = 'turma_aula'

const converterDadosParaEnvio = (item) => {
  let data = {}
  data.turma = item.settedTurmaId

  if (item.itemSelecionadoID !== null) {
    data.turma_aula = item.itemSelecionadoID
  }

  return data
}

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state)
      Request.get(`/${url}/listar`, data)
        .then(response => {
          const corpo = response.body.corpo

          corpo.turma.contratos = corpo.turma.contratos.map(contrato => {
            if (contrato.aluno.alunoAvaliacaos.length) {
              // contrato.aluno.alunoAvaliacaos[0].id = contrato.aluno.alunoAvaliacaos[0].id
              contrato.aluno.alunoAvaliacaos[0].nota_mid_term_oral = contrato.aluno.alunoAvaliacaos[0].nota_mid_term_oral * 1 || 0

              contrato.aluno.alunoAvaliacaos[0].nota_mid_term_test = contrato.aluno.alunoAvaliacaos[0].nota_mid_term_test * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_mid_term_composition = contrato.aluno.alunoAvaliacaos[0].nota_mid_term_composition * 1 || 0

              contrato.aluno.alunoAvaliacaos[0].nota_mid_term_escrita = contrato.aluno.alunoAvaliacaos[0].nota_mid_term_escrita * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_retake_mid_term_oral = contrato.aluno.alunoAvaliacaos[0].nota_retake_mid_term_oral * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_retake_mid_term_escrita = contrato.aluno.alunoAvaliacaos[0].nota_retake_mid_term_escrita * 1 || 0

              contrato.aluno.alunoAvaliacaos[0].nota_final_oral = contrato.aluno.alunoAvaliacaos[0].nota_final_oral * 1 || 0

              contrato.aluno.alunoAvaliacaos[0].nota_final_test = contrato.aluno.alunoAvaliacaos[0].nota_final_test * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_final_composition = contrato.aluno.alunoAvaliacaos[0].nota_final_composition * 1 || 0

              contrato.aluno.alunoAvaliacaos[0].nota_final_escrita = contrato.aluno.alunoAvaliacaos[0].nota_final_escrita * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_retake_final_oral = contrato.aluno.alunoAvaliacaos[0].nota_retake_final_oral * 1 || 0
              contrato.aluno.alunoAvaliacaos[0].nota_retake_final_escrita = contrato.aluno.alunoAvaliacaos[0].nota_retake_final_escrita * 1 || 0
            } else {
              contrato.aluno.alunoAvaliacaos.push({
                nota_mid_term_oral: 0,
                nota_mid_term_escrita: 0,
                nota_retake_mid_term_oral: 0,
                nota_retake_mid_term_escrita: 0,
                nota_final_oral: 0,
                nota_final_escrita: 0,
                nota_retake_final_oral: 0,
                nota_retake_final_escrita: 0
              })
            }

            if (contrato.aluno.alunoAvaliacaoConceituals.length) {
              // contrato.aluno.alunoAvaliacaoConceituals[0].id = contrato.aluno.alunoAvaliacaoConceituals[0].id
              contrato.aluno.alunoAvaliacaoConceituals[0].nota_listening_1 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_listening_1 || ''
              contrato.aluno.alunoAvaliacaoConceituals[0].nota_speaking_1 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_speaking_1 || ''
              contrato.aluno.alunoAvaliacaoConceituals[0].nota_writing_1 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_writing_1 || ''

              contrato.aluno.alunoAvaliacaoConceituals[0].nota_listening_2 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_listening_2 || ''
              contrato.aluno.alunoAvaliacaoConceituals[0].nota_speaking_2 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_speaking_2 || ''
              contrato.aluno.alunoAvaliacaoConceituals[0].nota_writing_2 = contrato.aluno.alunoAvaliacaoConceituals[0].nota_writing_2 || ''
            } else {
              contrato.aluno.alunoAvaliacaoConceituals.push({
                nota_listening_1: '',
                nota_speaking_1: '',
                nota_writing_1: '',
                nota_listening_2: '',
                nota_speaking_2: '',
                nota_writing_2: ''
              })
            }

            return contrato
          })

          commit('SET_ITEM_SELECIONADO', corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarAulasTurma ({ state, commit }, data) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_aulas_turma/${data.turmaId}`, data)
        .then(response => {
          const corpo = response.body.corpo
          corpo.map(aula => {
            aula.data_aula = new Date(aula.data_aula)
            return aula
          })
          commit('SET_LISTA', corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarLicoes ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state)
      Request.get(`/${url}/buscar_licoes/${data.turma}`)
        .then(response => {
          commit('SET_LISTA_LICOES_APLICADAS_TURMA', response.body.corpo)
          // commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, item))
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  buscarHistoricoAulasPorTurma ({state, commit}, turmaId) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      // const data = converterDadosParaEnvio(state)
      Request.get(`/${url}/buscar_historico_aulas/${turmaId}`)
        .then(response => {
          // console.log('LISTA HISTORICO', response.body.corpo)
          commit('SET_LISTA_HISTORICO_AULAS', response.body.corpo)
          // commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, item))
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  buscaHomeworkPorTurma ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state)
      Request.get(`/${url}/buscar_home_work/${data.turma}`)
        .then(response => {
          // console.info('RESPONSE', response.body.corpo)
          // commit('SET_LISTA_HISTORICO_AULAS', response.body.corpo)
          // commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, item))
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  buscarLicoesRealizadas ({state, commit}, data) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/buscar_licoes_realizadas/${data.turma}`, {turma_aula: data.turma_aula})
        .then(response => {
          // commit('SET_LISTA_HISTORICO_AULAS', response.body.corpo)
          // commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, item))
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  }

}
