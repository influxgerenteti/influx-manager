import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

function converterObjetoParaEnvio (item) {
  const data = Object.assign({}, item)
  data.cpf = data.cpf.replace(/\D/g, '')
  data.franqueada_padrao = data.franqueada_padrao.id

  return data
}

export default {

  getListaUsuarios ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/usuario/listar', {pagina: state.paginaAtual, order: state.order, direcao: state.direcao})
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

  criarUsuario ({state}) {
    return new Promise((resolve, reject) => {
      const data = converterObjetoParaEnvio(state.objUsuario)
      Request.post('/usuario/criar', data)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Usuário criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao criar o usuário.'
          })
        })
    })
  },

  atualizarUsuario ({state}) {
    return new Promise((resolve, reject) => {
      const data = converterObjetoParaEnvio(state.objUsuario)
      Request.patch(`/usuario/atualizar/${state.objUsuario.id}`, data)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Usuário alterado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao alterar o usuário.'
          })
        })
    })
  },

  getUsuario ({state, commit}, {transformID} = {}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/usuario/${state.usuarioSelecionadoId}`)
        .then(response => {
          const data = response.body.corpo
          if (transformID) {
            data.franqueada_padrao_data = data.franqueada_padrao
            data.franqueada_padrao = data.franqueada_padrao.id
          }
          if (data.papels !== undefined) {
            let papelArray = []
            data.papels.map(papel => {
              papelArray.push(papel.id)
            })
            data.papels = papelArray
          }

          commit('setUsuario', data)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  removerUsuario ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/usuario/remover/${state.usuarioSelecionadoId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Usuário removido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao remover o usuário.'
          })
        })
    })
  }

}
