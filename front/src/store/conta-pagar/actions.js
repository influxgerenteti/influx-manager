import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import {stringToISODate, dateToString} from '../../utils/date'
import {currencyToNumber, round} from '../../utils/number'
import {defaultData} from '../../views/titulo-pagar/titulo'

function converterObjetoParaEnvio (item) {
  const data = Object.assign({}, item)
  data.usuario = null
  data.fornecedor_pessoa = data.fornecedor_pessoa ? data.fornecedor_pessoa.id : null
  data.forma_cobranca = data.forma_cobranca ? data.forma_cobranca.id : null
  data.conta = data.conta ? data.conta.id : null

  data.data_vencimento = stringToISODate(data.data_vencimento, true)

  if (data.parcelas) {
    const parcelas = {}
    for (let i in data.parcelas) {
      const dataVencimento = stringToISODate(data.parcelas[i].data_vencimento, true)

      if (data.parcelas[i].cheque) {
        data.parcelas[i].cheque.data_bom_para = dataVencimento
      }

      parcelas[i] = {
        id: data.parcelas[i].id || '',
        data_vencimento: dataVencimento,
        data_prorrogacao: dataVencimento,
        numero_parcela_documento: data.parcelas[i].numero_parcela_documento,
        valor_documento: currencyToNumber(data.parcelas[i].valor_documento),
        forma_cobranca: data.parcelas[i].forma_cobranca ? data.parcelas[i].forma_cobranca.id : null,
        cheque: data.parcelas[i].cheque || null,
        narrativa_plano_conta: data.parcelas[i].narrativa_plano_conta || null
      }

      if (parcelas[i].cheque) {
        parcelas[i].cheque.tipo = 'P' // A pagar
      }
    }

    data.parcelas = Object.assign({}, parcelas)
    data.titulo_pagar = null
    data.plano_contas_conta_pagar = null
  }

  if (data.plano_conta) {
    const planos = []
    for (let i = 0; i < data.plano_conta.length; i++) {
      const plano = Object.assign({}, data.plano_conta[i])
      plano.plano_conta = plano.plano_conta.id
      planos.push(plano)
    }

    data.plano_conta = Object.assign([], planos)
  }

  return data
}

function converte (lista) {
  const convertido = []

  lista.map(item => {
    item.titulo_pagar.map(titulo => {
      const itemInserir = Object.assign({}, item)
      titulo.situacao = titulo.situacao.toUpperCase()

      const estaVencido = stringToISODate(dateToString(new Date(titulo.data_prorrogacao)), true) < stringToISODate(dateToString(new Date()), true)
      if (titulo.situacao === 'PEN' && estaVencido === true) {
        titulo.situacao = 'VEN'
      } else if (titulo.situacao === 'PEN' && titulo.forma_cobranca.forma_cheque === true && titulo.cheque) {
        titulo.situacao = 'LIQ-PEN'
      }

      titulo.podeSerPago = titulo.situacao === 'PEN' || titulo.situacao === 'VEN' || titulo.situacao === 'LIQ-PEN'
      titulo.podeSerExcluido = titulo.podeSerPago && titulo.valor_saldo === titulo.valor_documento

      itemInserir.titulo_pagar = titulo

      convertido.push(itemInserir)
    })
  })

  return convertido
}

export default {
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/conta_pagar/listar', {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const itens = converte(response.body.corpo.itens)
          itens.map(item => {
            item.valor_montante = item.titulo_pagar.valor_saldo
            item.valor_desconto = ''
            item.valor_juros = ''
            item.valor_diferenca_baixa = ''
            item.erro_valor = false
          })
          commit('SET_LISTA', itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          if (state.totalItens > state.lista.length) {
            commit('INCREMENTAR_PAGINA_ATUAL')
          }

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
    return new Promise((resolve, reject) => {
      Request.get(`/conta_pagar/${state.itemSelecionadoID}`)
        .then(response => {
          const object = Object.assign({}, response.body.corpo)

          object.possui_titulos_pagos = false
          const parcelas = {}
          object.titulo_pagar.map((titulo, index) => {
            if (index === 0) {
              object.data_vencimento = dateToString(new Date(titulo.data_prorrogacao))
              object.forma_cobranca = titulo.forma_cobranca
              object.conta = titulo.conta
            }

            titulo.data_vencimento = dateToString(new Date(titulo.data_prorrogacao))
            titulo.valor_documento = titulo.valor_documento * 1
            titulo.verDetalhes = false

            if (titulo.movimento_conta && titulo.movimento_conta.length) {
              object.possui_titulos_pagos = true
            }

            titulo.situacao = titulo.situacao.toUpperCase()

            const estaVencido = stringToISODate(dateToString(new Date(titulo.data_prorrogacao)), true) < stringToISODate(dateToString(new Date()), true)
            if (titulo.situacao === 'PEN' && estaVencido === true) {
              titulo.situacao = 'VEN'
            } else if (titulo.situacao === 'PEN' && titulo.forma_cobranca.forma_cheque === true && titulo.cheque) {
              titulo.situacao = 'LIQ-PEN'
            }

            if (titulo.forma_cobranca.forma_cheque === true && titulo.cheque === undefined) {
              titulo.cheque = defaultData.cheque
            }

            parcelas[titulo.numero_parcela_documento] = Object.assign({}, titulo)
          })
          object.parcelas = parcelas

          object.plano_conta = object.plano_contas_conta_pagar || []
          if (!object.plano_contas_conta_pagar) {
            object.plano_contas_conta_pagar = []
          }

          object.plano_conta = object.plano_contas_conta_pagar.map(item => {
            item.valor *= 1
            return item
          })

          if (!object.plano_conta || !object.plano_conta.length) {
            object.plano_conta.push({ plano_conta: null, valor: undefined, complemento: null, numero_sequencia: 1 })
          }

          object.numero_parcelas = (object.numero_parcelas || 1) * 1
          object.valor_total = (object.valor_total || 0) * 1
          object.valor_parcela = (object.valor_parcela || 0) * 1

          commit('SET_ITEM_SELECIONADO', object)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({ state }) {
    return new Promise((resolve, reject) => {
      const data = converterObjetoParaEnvio(state.itemSelecionado)

      Request.post('/conta_pagar/criar', data)
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conta a pagar criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao criar a conta a pagar: ${error.body.mensagem}`
          })
        })
    })
  },

  atualizar ({ state }) {
    return new Promise((resolve, reject) => {
      const data = converterObjetoParaEnvio(state.itemSelecionado)

      Request.patch(`/conta_pagar/atualizar/${state.itemSelecionado.id}`, data)
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conta a pagar atualizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao atualizar a conta a pagar.'
          })
        })
    })
  },

  remover ({ state }) {
    return new Promise((resolve, reject) => {
      Request.delete(`/titulo_pagar/remover/${state.itemSelecionadoID}`)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Exclusão realizada com sucesso!'
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

  calcularValorNota ({ state, commit }) {
    return new Promise(resolve => {
      const itens = state.itemSelecionado.itens
      let valorTotalNota = 0
      let valorTotalTitulo = 0

      if (!itens || !itens.length) {
        return resolve({valorTotalTitulo})
      }

      itens.map(item => {
        if (item.valor_item) {
          valorTotalNota += item.valor_item

          if (item.tipo_nota && item.tipo_nota.gera_titulo) {
            valorTotalTitulo += item.valor_item
          }
        }
      })

      commit('SET_VALOR_TOTAL_NOTA', valorTotalNota)
      commit('SET_VALOR_TITULO', valorTotalTitulo)

      resolve({valorTotalTitulo})
    })
  },

  calcularValorTotalTitulos ({ state, commit }) {
    const parcelas = state.itemSelecionado.parcelas
    let totalParcelas = 0

    for (let index in parcelas) {
      totalParcelas += round(currencyToNumber(parcelas[index].valor_documento))
    }

    commit('SET_VALOR_TOTAL_PARCELAS_CALCULADO', round(totalParcelas))
  },

  listarPagamentoInstrutor ({ state, commit }, data) {
    return new Promise((resolve, reject) => {
      Request.get('/pagamento_funcionario/listar', {pagina: state.paginaAtual, ...data})
        .then(response => {
          resolve(response.body.corpo)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criarPagamentoInstrutor (store, data) {
    return new Promise((resolve, reject) => {
      Request.post('/pagamento_funcionario/criar', data)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Pagamento de funcionário gerado com sucesso'
          })
          resolve(response.body.corpo)
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao atualizar a conta a pagar.'
          })
          reject(error)
        })
    })
  }
}
