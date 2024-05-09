import Request from '../../utils/request'
import store from '../index'
import EventBus from '../../utils/event-bus'

const url = '/dias_subsequentes'

export default {

  listarTodos ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          commit('SET_LISTA_DIAS_SUBSEQUENTES', response.body.corpo)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  buscarPorFranqueadaAtual ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar/`)
        .then(response => {
          commit('SET_LISTA_DIAS_FRANQUEADA_SELECIONADA', response.body.corpo.diasSubsequentes)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo.diasSubsequentes)
        })
        .catch(reject)
    })
  },

  salvarDiasSubsequentesFranqueadaAtual ({state, commit}, listaSelecionados) {
    return new Promise((resolve, reject) => {
      let franqueadaSelecionada = store.state.root.usuarioLogado.franqueadaSelecionada
      Request.patch(`${url}/atualizar/` + franqueadaSelecionada, {dias_subsequentes: listaSelecionados})
        .then(response => {
          resolve()
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }

}
