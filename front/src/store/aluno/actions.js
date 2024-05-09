import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/aluno'

function converterObjetoParaEnvio (state) {
  const data = {
    id: state.itemSelecionadoID,
    pessoa: state.itemSelecionado.pessoa.id,
    classificacao_aluno: state.itemSelecionado.classificacao_aluno ? state.itemSelecionado.classificacao_aluno.id : null,
    tipo_visibilidade: state.itemSelecionado.tipo_visibilidade ? state.itemSelecionado.tipo_visibilidade.map(i => i.id) : [],
    emancipado: state.itemSelecionado.emancipado,
    escolaridade: state.itemSelecionado.escolaridade ? state.itemSelecionado.escolaridade.id : null,
    responsavel_financeiro_pessoa: state.itemSelecionado.responsavel_financeiro_pessoa ? state.itemSelecionado.responsavel_financeiro_pessoa.id : null,
    responsavel_financeiro_relacionamento_aluno: state.itemSelecionado.responsavel_financeiro_relacionamento_aluno ? state.itemSelecionado.responsavel_financeiro_relacionamento_aluno.id : null,
    responsavel_didatico_pessoa: state.itemSelecionado.responsavel_didatico_pessoa ? state.itemSelecionado.responsavel_didatico_pessoa.id : null,
    responsavel_didatico_relacionamento_aluno: state.itemSelecionado.responsavel_didatico_relacionamento_aluno ? state.itemSelecionado.responsavel_didatico_relacionamento_aluno.id : null,
    interessado: state.itemSelecionado.interessado
  }

  if (typeof state.itemSelecionado.foto === 'string') {
    data.foto = state.itemSelecionado.foto
  } else {
    data.foto_arquivo = state.itemSelecionado.foto
  }

  return data
}

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao })
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
  listarHeader ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    console.log("listarHeader");
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar-header`, { pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao })
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

  buscar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    // console.log('BUSCAR DATA', data)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          const data = response.body.corpo.item
          const item = {
            id: data.id,
            excluido: data.excluido,
            emancipado: data.emancipado,
            classificacao_aluno: data.classificacao_aluno,
            tipo_visibilidade: data.tipo_visibilidade,
            foto: data.foto,
            escolaridade: data.escolaridade,
            responsavel_financeiro_pessoa: data.responsavel_financeiro_pessoa,
            responsavel_financeiro_relacionamento_aluno: data.responsavel_financeiro_relacionamento_aluno,
            responsavel_didatico_pessoa: data.responsavel_didatico_pessoa,
            responsavel_didatico_relacionamento_aluno: data.responsavel_didatico_relacionamento_aluno
          }
          commit('SET_DETALHES_ITEM_SELECIONADO', item)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(data.pessoa)
        })
        .catch(reject)
    })
  },

  buscarAluno ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_DETALHES_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response)
        })
        .catch(reject)
    })
  },

  buscarComPessoa ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`, {apenas_proxima_licao: true})
        .then(response => {
          const data = response.body.corpo.item
          commit('SET_ESTA_CARREGANDO', false)
          resolve(data)
        })
        .catch(reject)
    })
  },

  criar ({ state, commit }) {
    return new Promise((resolve, reject) => {
      const body = converterObjetoParaEnvio(state)

      Request.post(`${url}/criar`, body)
        .then(response => {
          resolve(response)
          commit('LIMPAR_ITEM_SELECIONADO')
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Aluno criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao criar o aluno: ${error.body.mensagem}`
          })
        })
    })
  },

  atualizar ({ state, commit }) {
    return new Promise((resolve, reject) => {
      const body = converterObjetoParaEnvio(state)

      Request.post(`${url}/atualizar/${state.itemSelecionadoID}`, body)
        .then(() => {
          resolve()
          commit('LIMPAR_ITEM_SELECIONADO')
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Aluno atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao atualizar o aluno.`
          })
        })
    })
  },

  remover ({ state, commit }) {
    return new Promise((resolve, reject) => {
      Request.delete(`${url}/remover/${state.itemSelecionadoID}`)
        .then(() => {
          resolve()
          commit('LIMPAR_ITEM_SELECIONADO')
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Aluno removido com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover aluno.'
          })
        })
    })
  },

  buscarCPFCNPJ ({ state, commit }) {
    return new Promise((resolve) => {
      Request.get(`/pessoa/buscar/${state.buscaCPFCNPJ}`)
        .then(response => {
          resolve()
          commit('ATRIBUI_PESSOA_SELECIONADA', response.body.corpo)
        })
    })
  },

  buscarAlunoComPessoa ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          const data = response.body.corpo.item
          commit('SET_DETALHES_ITEM_SELECIONADO', data)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(data)
        })
        .catch(reject)
    })
  },

  transfereAluno ({state, commit}, data) {
    return new Promise((resolve, reject) => {
      // ID DO CONTRATO, DATA
      Request.post(`/aluno/transfere_turma/${data.contrato}`, data)
        .then(response => {
          resolve(response.body.corpo.itens)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Transferência de turma realizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem ? error.body.mensagem : 'Erro ao realizar a transferência de turma.'
          })
        })
    })
  }

}
