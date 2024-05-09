import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import store from '../index'
import { stringToISODate, dateToString } from '../../utils/date'

const converterDadosParaEnvio = (item, listaTurmaAula) => {
  const data = Object.assign({}, item)

  data.modalidade_turma = data.modalidade_turma ? data.modalidade_turma.id : null
  data.curso = data.curso ? data.curso.id : null
  data.livro = data.livro ? data.livro.id : null
  data.sala_franqueada = data.sala_franqueada ? data.sala_franqueada.salaFranqueadaId : null
  data.funcionario = data.funcionario ? data.funcionario.id : null
  data.horario = data.horario ? data.horario.id : null
  data.valor_hora_linhas = data.valor_hora_linhas ? data.valor_hora_linhas.id : null
  data.semestre = data.semestre ? data.semestre.id : null

  if (data.data_inicio instanceof Date) {
    data.data_inicio = dateToString(data.data_inicio)
  }
  data.data_inicio = stringToISODate(data.data_inicio, true)

  if (data.data_fim instanceof Date) {
    data.data_fim = dateToString(data.data_fim)
  }
  data.data_fim = stringToISODate(data.data_fim, true)
  data.situacao = data.situacao.valor

  if (listaTurmaAula) {
    data.lista_turma_aula = []
    listaTurmaAula.forEach(aula => {
      data.lista_turma_aula.push({
        licao: aula.licao,
        data_aula: stringToISODate(dateToString(aula.data_aula))
      })
    })
  }

  return data
}

const url = 'turma'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_LISTA_TODA_CARREGADA', false)
    const paginaBuscada = state.paginaAtual
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, { pagina: paginaBuscada, order: state.order, direcao: state.direcao, ...state.filtros })
        .then(response => {
          let lista = response.body.corpo.itens
          let totalItens = response.body.corpo.total
          commit('SET_LISTA', {lista: lista, pagina: paginaBuscada})
          commit('SET_TOTAL_ITENS', totalItens)
          if (paginaBuscada === state.paginaAtual) {
            commit('INCREMENTAR_PAGINA_ATUAL')
          }
          commit('SET_ESTA_CARREGANDO', false)
          if (state.lista.length >= totalItens) {
            commit('SET_LISTA_TODA_CARREGADA', true)
          }
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/${state.itemSelecionadoID}`)
        .then(response => {
          const item = response.body.corpo

          item.intensidade = item.turmaIntensidade
          item.maximo_alunos = item.turmaMaximoAlunos
          item.observacao = item.turmaObservacao
          item.descricao = item.turmaDescricao
          item.situacao = item.turmaSituacao
          item.semestre = item.semestreId

          item.curso = {descricao: item.cursoDescricao}
          item.modalidade_turma = {descricao: item.modalidadeDescricao}
          item.valor_hora_linhas = {descricao: item.valorHoraDescricao}
          item.livro = {descricao: item.livroDescricao}
          item.sala_franqueada = {descricao: item.salaDescricao}
          item.horario = {descricao: item.horarioDescricao}
          item.funcionario = {apelido: item.funcionarioApelido}

          item.id = state.itemSelecionadoID
          item.data_inicio = new Date(item.turmaDataInicio)
          item.data_fim = dateToString(new Date(item.turmaDataFim))

          delete item.sala

          commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, item))
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  salvar ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado)
      data.franqueada = store.state.root.franqueadaSelecionada
      Request.post(`/${url}/criar`, data)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma criada com sucesso!'
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

  salvarHybrid ({state, commit}, params) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado, params.listaTurmaAula)
      data.franqueada = store.state.root.franqueadaSelecionada

      Request.post(`/${url}/criar`, data)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma criada com sucesso!'
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

  atualizarHybrid ({state, commit}, params) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado, params.listaTurmaAula)
      Request.patch(`/${url}/atualizar/${state.itemSelecionadoID}`, data)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma atualizada com sucesso!'
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

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado)
      Request.patch(`/${url}/atualizar/${state.itemSelecionadoID}`, data)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma atualizada com sucesso!'
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

  buscarAlunos ({state, commit}, filtros) {
    return new Promise((resolve, reject) => {
      if (!filtros) {
        filtros = {}
      }
      Request.get(`/${url}/buscar_alunos_turma/${state.itemSelecionadoID}`, filtros)
        .then(response => {
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  },

  reativar ({state, commit}, idTurma) {
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/reativar/${idTurma}`)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma atualizada com sucesso!'
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

  remover ({state, commit}, idTurma) {
    return new Promise((resolve, reject) => {
      Request.delete(`/${url}/remover/${idTurma}`)
        .then(response => {
          resolve(response.body.corpo.id)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Turma atualizada com sucesso!'
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

  excluir ({state, commit}, idTurma) {
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/excluirTurma/${idTurma}`)
        .then(response => {
          var tipoAlerta = response.body.erro ? 'E' : 'S'
          EventBus.$emit('criarAlerta', {
            tipo: tipoAlerta,
            mensagem: response.body.mensagem
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
  },

  checarPodeExcluir ({state, commit}, idTurma) {
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/podeExcluir/${idTurma}`)
        .then(response => {
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
  },

  calcularDataTermino ({ state, commit }) {
    if (!state.itemSelecionado.horario || !state.itemSelecionado.data_inicio || !state.itemSelecionado.livro) {
      return
    }

    const horario = state.itemSelecionado.horario && state.itemSelecionado.horario.id ? state.itemSelecionado.horario.id : state.itemSelecionado.horarioId
    const livro = state.itemSelecionado.livro && state.itemSelecionado.livro.id ? state.itemSelecionado.livro.id : state.itemSelecionado.livroId

    let dataInicio = state.itemSelecionado.data_inicio
    if (dataInicio instanceof Date) {
      dataInicio = dateToString(dataInicio, true)
    }
    dataInicio = stringToISODate(dataInicio)

    const data = {
      horario: horario,
      livro: livro,
      data_inicio: dataInicio
    }

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/calcular_data_termino`, data)
        .then(response => {
          commit('SET_DATA_FIM', response.body.corpo)
          resolve()
        })
        .catch(reject)
    })
  },

  buscarDiario ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    const data = {
      turma_aula: state.itemSelecionado.turma_aula
    }

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/diario_classe/${state.itemSelecionadoID}`, data)
        .then(response => {
          const item = response.body.corpo
          commit('SET_ITEM_SELECIONADO', item)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(item)
        })
        .catch(reject)
    })
  },

  buscarTurmas ({state, commit}, data) {
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/consultar_turmas`, data)
        .then(response => {
          resolve(response.body.corpo.itens)
        })
        .catch(reject)
    })
  },

  gerarTurmaAulasTurma ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/gerar_turma_aula`, converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  listarTodos ({state, commit}, data) {
    commit('SET_ESTA_CARREGANDO', true)
    if (!data) {
      data = {}
    }
    return new Promise((resolve, reject) => {
      Request.get(`/turma/listarTodos`, data)
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          const resp = {
            pagina: 1,
            lista: response.body.corpo.itens
          }
          commit('SET_LISTA', resp)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          resolve(response.body.corpo)
        })
        .catch(reject)
    })
  }

}
