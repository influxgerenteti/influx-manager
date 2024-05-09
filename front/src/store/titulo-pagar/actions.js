import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import {stringToISODate} from '../../utils/date'

let success = 0
let error = 0
function resposta (response, index) {
  if (response.erro === false) {
    success++
  } else {
    error++
  }

  if (index === 0) {
    if (success > 0) {
      EventBus.$emit('criarAlerta', {
        tipo: 'S',
        mensagem: 'Pagamento realizado com sucesso!'
      })
    }

    if (error > 0) {
      const message = response.mensagem || 'Erro ao efetuar pagamento.'
      EventBus.$emit('criarAlerta', {
        tipo: error.status > 500 ? 'E' : 'A',
        mensagem: message
      })
    }

    error = 0
    success = 0
  }
}

export default {
  calcular ({state, commit}) {
    if (!state.condicaoPagamento || !state.valorTitulo) {
      return
    }

    return new Promise((resolve, reject) => {
      const data = {
        condicao_pagamento: state.condicaoPagamento,
        valor_titulo: state.valorTitulo,
        data_emissao: stringToISODate(state.dataEmissao)
      }

      Request.post('/titulo_pagar/calcular', data)
        .then(response => {
          commit('SET_PARCELAS', response.body.corpo.parcelas)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  getLista ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      EventBus.$emit('carregarPagina', {
        show: true
      })
      Request.get('/titulo_pagar/listar', state.filtros)
        .then(response => {
          const itens = response.body.corpo.itens
          itens.map(item => {
            item.valor_montante = item.valor_saldo
            item.valor_desconto = ''
            item.valor_juros = ''
            item.valor_diferenca_baixa = ''
          })
          commit('SET_LISTA', itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('carregarPagina', {
            show: false
          })
          resolve()
        })
        .catch(error => {
          // commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  pagar ({commit}, lista) {
    return new Promise((resolve, reject) => {
      for (let i = lista.length; i--;) {
        (function (i) {
          setTimeout(function () {
            Request.post('/movimento_conta/criar', lista[i])
              .then(response => {
                if (i === 0) {
                  resolve()
                }

                resposta(response.body, i)
              })
              .catch(error => {
                if (i === 0) {
                  reject(error.body)
                }

                resposta(error.body, i)
              })
          }, 100)
        })(i)
      }
    })
  }

}
