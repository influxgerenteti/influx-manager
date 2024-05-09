import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import { converterParaEnvio, montarArvore } from '../../utils/permissao'

const url = '/permissao'

export default {
  listar ({ state, commit }, options = null) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: state.paginaAtual, ...state.filtros })
        .then(response => {
          if (!options || options.efetuarCommit === true) {
            commit('SET_LISTA', response.body.corpo)
            commit('SET_ARVORE_ITENS', montarArvore(response.body.corpo))
          }

          commit('SET_TOTAL_ITENS', response.body.corpo.length)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.permissaoSelecionadaId}`)
        .then(response => {
          const data = response.body.corpo
          commit('SET_PERMISSAO', data)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(data)
        })
        .catch(reject)
    })
  },

  buscarPermissaoPorUsuario ({ state, commit }, id) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/usuario/${id}`)
        .then(response => {
          const data = response.body.corpo
          resolve(data)
        })
        .catch(reject)
    })
  },

  verificaPermissaoModulo ({ state, commit }, data) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/usuario/verifica_permissao`, data, true, {redirecionar: false})
        .then(response => {
          resolve(response.body.corpo.usuario)
        })
        .catch(reject)
    })
  },

  alterarAcaoModulo ({commit}, obj) {
    commit('SET_MODULO_ACAO', obj)
  },

  remontarArvore ({state, commit}) {
    commit('SET_ARVORE_ITENS', montarArvore([...state.listaPermissao]))
  },

  atualizar ({state}, {moduloID, papelID, usuarioID}) {
    return new Promise((resolve, reject) => {
      const dados = {
        modulo: moduloID,
        papel: papelID,
        usuario: usuarioID,
        dados: converterParaEnvio(state.listaPermissao)
      }

      Request.patch(`${url}/atualizar`, dados)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Permissões salvas!'
          })
          resolve(response)
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
          reject(error)
        })
    })
  }
}
