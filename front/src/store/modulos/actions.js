import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {

  getListaModulo ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/modulo/listar', { pagina: state.paginaAtual, order: state.order, direcao: state.direcao })
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarModulosPais ({commit}) {
    return new Promise((resolve, reject) => {
      Request.get('/modulo/buscar-modulos-pais')
        .then(response => {
          commit('setListaModulosPais', response.body.corpo)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criarModulo ({state}) {
    return new Promise((resolve, reject) => {
      const body = {
        nome: state.objModulo.nome,
        url: state.objModulo.url,
        situacao: state.objModulo.situacao,
        modulo_pai: state.objModulo.modulo_pai_id
      }

      Request.post('/modulo/criar', body)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Módulo criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao criar o módulo.'
          })
        })
    })
  },

  atualizarModulo ({state}) {
    return new Promise((resolve, reject) => {
      const body = {
        nome: state.objModulo.nome,
        url: state.objModulo.url,
        situacao: state.objModulo.situacao,
        modulo_pai: state.objModulo.modulo_pai_id
      }

      Request.patch(`/modulo/atualizar/${state.objModulo.id}`, body)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Módulo alterado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao alterar módulo.'
          })
        })
    })
  },

  removerModulo ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/modulo/remover/${state.moduloSelecionadoId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Módulo removido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover módulo.'
          })
        })
    })
  },

  buscaTodosModulosSemPai ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.get(`/modulo/buscarTodos`)
        .then(response => {
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  getModulo ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/modulo/buscar/${state.moduloSelecionadoId}`)
        .then(response => {
          commit('setModulo', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  buscarPermissaoPorModulo ({ commit }, options) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/permissao/modulo`, {URLModulo: options ? options.URLModulo : null})
        .then(response => {
          if (!options || options.commitMutation !== false) {
            commit('SET_PERMISSAO', response.body.corpo.permissoes)
          }

          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  }

}
