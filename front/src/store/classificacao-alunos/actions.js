import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {

  getListaClassificacaoAluno ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/classificacao_aluno/listar', { pagina: state.paginaAtual, order: state.order, direcao: state.direcao })
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

  getClassificacaoAluno ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/classificacao_aluno/${state.classificacaoAlunoSelecionadaId}`)
        .then(response => {
          commit('SET_CLASSIFICACAO_ALUNO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criarClassificacaoAluno ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/classificacao_aluno/criar', state.objClassificacaoAluno)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Classificação de aluno criada com sucesso!'
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

  atualizarClassificacaoAluno ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/classificacao_aluno/atualizar/${state.objClassificacaoAluno.id}`, state.objClassificacaoAluno)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Classificação de aluno atualizada com sucesso!'
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

  removerClassificacaoAluno ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/classificacao_aluno/remover/${state.classificacaoAlunoSelecionadaId}`)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Classificação de aluno removido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover classificação de aluno.'
          })
        })
    })
  }

}
