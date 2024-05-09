import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {

  getListaPessoas ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    const data = {
      pagina: state.paginaAtual,
      order: state.order,
      direcao: state.direcao
    }
    if (state.filtroRapido) {
      data.cpf = state.filtroRapido.cpf
      data.cnpj = state.filtroRapido.cnpj
      data.telefone = state.filtroRapido.telefone
      data.nome = state.filtroRapido.nome
      data.tipo_pessoa = state.filtroRapido.tipoPessoa
    }

    return new Promise((resolve, reject) => {
      Request.get('/pessoa/listar', data)
        .then(response => {
          commit('setListaPessoas', response.body.corpo.itens)
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

  criarPessoa ({state}, offMessage) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.objPessoa)
      data.franqueadas = null
      data.estado_civil = data.estado_civil ? data.estado_civil.value : null
      data.sexo = data.sexo ? data.sexo.value : null

      Request.post('/pessoa/criar', data)
        .then(response => {
          resolve(response.body.corpo)
          if (!offMessage) {
            EventBus.$emit('criarAlerta', {
              tipo: 'S',
              mensagem: 'Pessoa criada com sucesso!'
            })
          }
        })
        .catch(error => {
          reject(error)
          if (!offMessage) {
            EventBus.$emit('criarAlerta', {
              tipo: error.status > 500 ? 'E' : 'A',
              mensagem: error.body.mensagem
            })
          }
        })
    })
  },

  getPessoa ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/pessoa/${state.pessoaSelecionadaId}`)
        .then(response => {
          commit('setPessoa', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  atualizarPessoa ({state}, offMessage) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.objPessoa)
      data.franqueadas = null
      data.estado_civil = data.estado_civil ? data.estado_civil.value : null
      data.sexo = data.sexo ? data.sexo.value : null

      Request.patch(`/pessoa/atualizar/${state.objPessoa.id}`, data)
        .then(response => {
          resolve(response.body.corpo)
          if (!offMessage) {
            EventBus.$emit('criarAlerta', {
              tipo: 'S',
              mensagem: 'Pessoa atualizada com sucesso!'
            })
          }
        })
        .catch(error => {
          reject(error)
          if (!offMessage) {
            EventBus.$emit('criarAlerta', {
              tipo: error.status > 500 ? 'E' : 'A',
              mensagem: error.body.mensagem
            })
          }
        })
    })
  },

  criarResponsavel ({state}) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.responsavel)
      data.franqueadas = null

      Request.post('/pessoa/criar', data)
        .then(response => {
          resolve(response.body.corpo)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Pessoa criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },
  listar({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    let params = state.parametros

    return new Promise((resolve, reject) => {
        Request.get('/pessoa/listar' + (params ? '?' + params : ''), null).then(
            response => {
              commit('SET_LISTA', response.body.corpo.itens)
              commit('SET_TOTAL_ITENS', response.body.corpo.total)
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
