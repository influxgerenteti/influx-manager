import Request from '../../utils/request'
import {stringToISODate} from '../../utils/date'
import EventBus from '../../utils/event-bus'

const url = 'movimento_conta'

function converterFiltros (data) {
  const filtros = Object.assign({}, data)
  filtros.conta = filtros.conta ? filtros.conta.id : null
  filtros.forma_pagamento = filtros.forma_cobranca ? filtros.forma_cobranca.id : null
  filtros.mes = filtros.mes && !filtros.avancado ? filtros.mes.value : null
  filtros.data_lancamento_inicio = filtros.data_lancamento_inicio && filtros.avancado ? stringToISODate(filtros.data_lancamento_inicio) : null
  filtros.data_lancamento_fim = filtros.data_lancamento_fim && filtros.avancado ? stringToISODate(filtros.data_lancamento_fim) : null
  filtros.origem = filtros.origem && filtros.avancado ? filtros.origem.id : null
  filtros.usuario = filtros.usuario && filtros.avancado ? filtros.usuario.id : null

  return filtros
}

function prepararEnvio (data) {
  const result = Object.assign({}, data)

  result.conta = data.conta ? data.conta.id : null
  result.forma_pagamento = result.forma_pagamento ? result.forma_pagamento.id : null
  result.plano_conta = result.plano_conta ? result.plano_conta.id : null
  result.data_contabil = result.data_contabil ? stringToISODate(result.data_contabil, true) : null
  result.data_deposito = result.data_deposito ? stringToISODate(result.data_deposito, true) : null
  result.data_movimento = result.data_movimento ? stringToISODate(result.data_movimento, true) : null
  result.tipo_movimento_conta = result.operacao === 'C' ? 2 : 1
  result.valor_montante = result.valor_lancamento
  result.usuario = null
  result.titulo_pagar = null
  result.titulo_receber = null

  return result
}

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, ...converterFiltros(state.filtros)})
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('SET_SALDO_INICIAL', response.body.corpo.saldo_inicial || 0)
          commit('SET_TOTAL_ENTRADAS', response.body.corpo.total_entradas || 0)
          commit('SET_TOTAL_SAIDAS', response.body.corpo.total_saidas || 0)
          commit('SET_TOTAL_NAO_CONCILIADOS', response.body.corpo.total_nao_conciliados || 0)

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

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, prepararEnvio(state.itemSelecionado))
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Movimentação de conta efetuada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar a movimentação de conta'
          })
        })
    })
  },

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar/${state.itemSelecionadoID}`, prepararEnvio(state.itemSelecionado))
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Movimentação de conta atualizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar a atualização da movimentação de conta'
          })
        })
    })
  },

  transferir ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.transferir)
      data.conta_origem = data.conta_origem ? data.conta_origem.id : null
      data.conta_destino = data.conta_destino ? data.conta_destino.id : null
      data.data_contabil = data.data_contabil ? stringToISODate(data.data_contabil, true) : null

      Request.post(`/${url}/transferir`, data)
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Transferência efetuada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar a transferência'
          })
        })
    })
  },

  estornar ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.estornar)
      data.data_estorno = data.data_estorno ? stringToISODate(data.data_estorno, true) : null

      Request.post(`/${url}/estornar`, data)
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Estorno de lançamento efetuado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar o estorno do lançamento'
          })
        })
    })
  },

  conciliarVarios ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.dadosConciliarVarios)
      data.data_contabil = data.data_contabil ? stringToISODate(data.data_contabil, true) : null
      data.data_deposito = data.data_deposito ? stringToISODate(data.data_deposito, true) : null

      Request.patch(`/${url}/conciliar`, data)
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conciliação de lançamentos efetuado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar a conciliação'
          })
        })
    })
  },

  efetuarTransferenciaExistente ({state, commit}) {
    return new Promise((resolve, reject) => {
      const data = {}
      data.conta = state.transferirExistente.conta_destino ? state.transferirExistente.conta_destino.id : null

      Request.patch(`/${url}/transferir_existente/${state.transferirExistente.id}`, data)
        .then(response => {
          resolve()

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Transferência de lançamento efetuada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)

          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Houve um erro ao efetuar a transferência de movimento'
          })
        })
    })
  }
}
