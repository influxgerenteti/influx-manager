import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import {stringToISODate} from '../../utils/date'
const url = 'bonus_class'

const converterDados = (item) => {
  const data = Object.assign({}, item)
  data.funcionario = data.funcionario ? data.funcionario.id : null
  data.sala_franqueada = data.sala_franqueada.salaFranqueadaId ? data.sala_franqueada.salaFranqueadaId : (data.sala_franqueada.id ? data.sala_franqueada.id : null)

  data.data_aula = data.data_aula ? stringToISODate(data.data_aula, true) : null
  data.horario_inicio = `2000-01-01T${data.horario_inicio}:00.000Z`
  data.horario_termino = `2000-01-01T${data.horario_termino}:00.000Z`

  return data
}

const converterParaEdicao = (item) => {
  const data = Object.assign({}, item)
  let obj = {id: null, descricao: 'Selecione'}

  data.data_aula = new Date(data.data_aula)
  data.horario_inicio = data.horario_inicio.match(/(\d{2,2}):(\d{2,2})/)[0]
  data.horario_termino = data.horario_termino.match(/(\d{2,2}):(\d{2,2})/)[0]
  if (data.sala_franqueada) {
    obj.id = data.sala_franqueada.salaFranqueadaId ? data.sala_franqueada.salaFranqueadaId : data.sala_franqueada.id
    obj.descricao = data.sala_franqueada.sala.descricao
  }

  data.sala_franqueada = obj
  return data
}

const converterDadosAlunos = (item, dadosAlunos) => {
  const data = Object.assign({}, item)

  data.sala_franqueada = data.sala_franqueada ? data.sala_franqueada.id : null
  data.funcionario = data.funcionario ? data.funcionario.id : null
  data.dados_alunos = dadosAlunos

  return data
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
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

  buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', converterParaEdicao(response.body.corpo))
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, converterDados(state.itemSelecionado))
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Criado com sucesso!'
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizar ({state, commit}, convert = true) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      if (convert) {
        state.itemSelecionado = converterDados(state.itemSelecionado)
      }

      Request.patch(`/${url}/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  cancelar ({state, commit}, convert = true) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      if (convert) {
        state.itemSelecionado = converterDados(state.itemSelecionado)
      }

      Request.patch(`/${url}/cancelar_aula_bonus/${state.itemSelecionado.id}`)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Aula Cancelada com sucesso!'
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizarAlunos ({state, commit}, listaDeAlunos) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar_alunos/${state.itemSelecionado.id}`, converterDadosAlunos(state.itemSelecionado, listaDeAlunos))
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Alunos atualizados com sucesso!'
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizarCampos ({state, commit}, objLinhas) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar_campos/${state.itemSelecionado.id}`, objLinhas)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Aluno atualizado com sucesso!'
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizarDados ({state, commit}, listaAlunos) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar_dados/${state.itemSelecionado.id}`, { ...converterDados(state.itemSelecionado), lista_alunos: listaAlunos })
        .then(response => {
          resolve()
           if (response.statusText == "Accepted") {
            EventBus.$emit('criarAlerta', {
              tipo: 'I',
              mensagem: response.body.mensagem,
              tempo: 20
            })           
          } else {
            EventBus.$emit('criarAlerta', {
              tipo: 'S',
              mensagem: 'Aluno atualizado com sucesso!'
            })
          }
          commit('SET_ESTA_CARREGANDO', false)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  }
}
