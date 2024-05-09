import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import {dateToString, stringToISODate, converteHorarioBancoParaInputText} from '../../utils/date'

const url = '/funcionario'

const converterDadosParaEnvio = (item) => {
  const data = Object.assign({}, item)
  data.data_admissao = data.data_admissao ? stringToISODate(data.data_admissao, true) : null
  data.data_demissao = data.data_demissao ? stringToISODate(data.data_demissao, true) : null
  data.cargo = data.cargo ? data.cargo.id : null
  data.banco = data.banco ? data.banco.id : null
  data.nivel_instrutor = data.nivel_instrutor ? data.nivel_instrutor.id : null
  data.gestor_comercial_funcionario = data.gestor_comercial_funcionario ? data.gestor_comercial_funcionario.id : null
  data.usuario = data.usuario ? data.usuario.id : null
  if (typeof data.pessoa !== 'number') {
    data.pessoa = data.pessoa ? data.pessoa.id : null
  }

  return data
}

const converterFiltros = (filtros) => {
  const f = {...filtros}
  f.cargo = f.cargo ? f.cargo.id : null
  f.nivel_instrutor = f.nivel_instrutor ? f.nivel_instrutor.id : null
  f.funcionario = f.funcionario ? f.funcionario.id : null
  f.data_admissao = f.data_admissao ? stringToISODate(f.data_admissao, true) : null
  f.data_demissao = f.data_demissao ? stringToISODate(f.data_demissao, true) : null
  f.tipo_pagamento = f.tipo_pagamento ? f.tipo_pagamento.join(',') : null
  f.apenas_funcionarios_ativos = f.apenas_funcionarios_ativos === undefined ? true : f.apenas_funcionarios_ativos

  return f
}

const converterDadosDisponibilidade = (arDisponibilidade) => arDisponibilidade.map(disponibilidade => {
  disponibilidade.hora_inicial = disponibilidade.hora_inicial ? converteHorarioBancoParaInputText(disponibilidade.hora_inicial) : null
  disponibilidade.hora_final = disponibilidade.hora_final ? converteHorarioBancoParaInputText(disponibilidade.hora_final) : null

  return disponibilidade
})

export default {
  listar ({state, commit}, listarTodos) {
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_PAGINA_ATUAL',1)

    const paginaBuscada = state.paginaAtual
    const data = {order: state.order, direcao: state.direcao, ...converterFiltros(state.filtros)}
    if (!listarTodos) {
      data.pagina = paginaBuscada
    }
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, data)
        .then(response => {
          const listaObj = {lista: response.body.corpo.itens}
          if (!listarTodos) {
            listaObj.pagina = paginaBuscada
          }
          commit('SET_LISTA', listaObj)
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

  buscarConsultores ({state, commit}, objParametrosExtra) {
    let extraParams = {}
    if (!objParametrosExtra) {
      extraParams = objParametrosExtra
    }
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_consultores_ativos`, { extra_params: extraParams })
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarGestores ({state, commit}, objParametrosExtra) {
    let extraParams = {}
    if (!objParametrosExtra) {
      extraParams = objParametrosExtra
    }
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_gestores_ativos`, { extra_params: extraParams })
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarInstrutores ({state, commit}, objParametrosExtra) {
    let extraParams = {}
    if (!objParametrosExtra) {
      extraParams = objParametrosExtra
    }
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_instrutores_ativos`, { extra_params: extraParams })
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscarInstrutoresPersonal ({state, commit}, objParametrosExtra) {
    let extraParams = {}
    if (!objParametrosExtra) {
      extraParams = objParametrosExtra
    }
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_instrutores_personal_ativos`, { extra_params: extraParams })
        .then(response => {
          commit('SET_LISTA', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  verificaDisponibilidade ({state, commit}, parametros) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/verificar-disponibilidade`, {...parametros})
        .then(response => {
          commit('SET_FUNCIONARIO_DISPONIVEL', response.body.corpo)
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
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          const data = response.body.corpo
          data.data_admissao = data.data_admissao ? dateToString(new Date(data.data_admissao)) : ''
          data.data_demissao = data.data_demissao ? dateToString(new Date(data.data_demissao)) : ''

          data.disponibilidades = data.disponibilidades.length > 0 ? converterDadosDisponibilidade(data.disponibilidades) : []

          commit('SET_ITEM_SELECIONADO', data)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(data.pessoa)
        })
        .catch(reject)
    })
  },

  criar ({ state }) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado)
      Request.post(`${url}/criar`, data)
        .then(response => {
          resolve(response.body.corpo.objetoORM)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Funcionário criado com sucesso!'
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

  atualizar ({ state }) {
    return new Promise((resolve, reject) => {
      const data = converterDadosParaEnvio(state.itemSelecionado)
      Request.patch(`${url}/atualizar/${state.itemSelecionadoID}`, data)
        .then(response => {
          resolve(response.body.corpo)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Funcionário alterado com sucesso!'
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
  }
}
