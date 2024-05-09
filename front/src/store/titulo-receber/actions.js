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
        mensagem: 'Recebimento realizado com sucesso!'
      })
    }

    if (error > 0) {
      EventBus.$emit('criarAlerta', {
        tipo: error.status > 500 ? 'E' : 'A',
        mensagem: response.mensagem || 'Erro ao efetuar recebimento.'
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

      Request.post('/titulo_receber/calcular', data)
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
      Request.get('/titulo_receber/listar', {paginaAtual: state.paginaAtual, ...state.filtros})
        .then(response => {
          const itens = response.body.corpo.itens
          itens.map(item => {
            item.valor_montante = item.valor_saldo_devedor
            item.valor_desconto = ''
            item.valor_juros = ''
            item.valor_multa = ''
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

  receber (store, lista) {
    return new Promise((resolve, reject) => {
      for (let i = lista.length; i--;) {
        (function (i) {
          const titulo = { ...lista[i] }
          Request.patch(`/titulo_receber/atualizar/${titulo.titulo_receber}`, titulo)
            .then(response => {
              if (i === 0) {
                resolve()
              }

              resposta(response.body, i)
            })
            .catch(error => {
              if (i === 0) {
                reject(error)
              }
              resposta(error.body, i)
            })
        })(i)
      }
    })
  },
  receber_novo (store, dados) {
    return new Promise((resolve, reject) => {          
          Request.post(`/titulo_receber/receber/${dados.titulo_id}`, dados)
            .then(response => {
                resolve()
                response.body
            })
            .catch(error => {
              EventBus.$emit('criarAlerta', {
                tipo: 'A',
                mensagem: error.body.mensagem}
              )
                reject(error)
                error.body       
            })
        })
  },
  

  movimentarConta (store, lista) {
    return new Promise((resolve, reject) => {
      for (let i = lista.length; i--;) {
        (function (i) {
          const titulo = { ...lista[i] }
          Request.post(`/movimento_conta/criar`, titulo)
            .then(response => {
              if (i === 0) {
                resolve()
              }

              resposta(response.body, i)
            })
            .catch(error => {
              if (i === 0) {
                reject(error)
              }
              resposta(error.body, i)
            })
        })(i)
      }
    })
  },

  efetuarEstorno ({commit}, lista) {
    return new Promise((resolve, reject) => {
      for (let i = lista.length; i--;) {
        (function (i) {
          const item = Object.assign({}, lista[i])
          item.data_contabil = item.data_recebimento
          Request.post('/movimento_conta/estorno', item)
            .then(response => {
              if (i === 0) {
                resolve()
              }

              resposta(response.body, i)
            })
            .catch(error => {
              if (i === 0) {
                reject(error)
              }
              resposta(error.body, i)
            })
        })(i)
      }
    })
  },

  atualizarTitulos (store, titulos) {
    return new Promise((resolve, reject) => {
      const length = titulos.length
      const plural = length > 1 ? 's' : ''
      for (let i = length; i--;) {
        (function (i) {
          const titulo = titulos[i]
          Request.patch(`/titulo_receber/atualizar/${titulo.titulo_receber}`, titulo)
            .then(response => {
              let mensagem = []
              if (response.mensagem) {
                mensagem.push(response.mensagem)
              }

              if (i === 0) {
                resolve()

                EventBus.$emit('criarAlerta', {
                  tipo: 'S',
                  mensagem: `${mensagem.join(' ')} Conta${plural} a receber atualizada${plural} com sucesso!`
                })
              }
            })
            .catch(error => {
              if (i === 0) {
                reject(error)

                EventBus.$emit('criarAlerta', {
                  tipo: error.status > 500 ? 'E' : 'A',
                  mensagem: `Erro ao atualizar a${plural} conta${plural} a receber`
                })
              }

              resposta(error.body, i)
            })
        })(i)
      }
    })
  },
    

  cancelarTitulos (store, {titulos, usuarioID} ) {
    return new Promise((resolve, reject) => {
      const data = {
        titulos_receber: titulos,
        usuario: usuarioID
      }
      Request.post('/titulo_receber/cancelar', data)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Titulos cancelados com sucesso!'
          })
          resolve()
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: 'E',
            mensagem: error
          })
          reject(error)
        })
    })
  },

  consultarDetalhesTitulo({state, commit}, id){
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_TITULO_DETALHES', {})
    if(typeof(id) !== 'string') {
      return;
    }
    return new Promise((resolve, reject) => {
      Request.get('/titulo_receber/' + id)
        .then(response => {
          commit('SET_TITULO_DETALHES', response.body.corpo[0])
          resolve()
        })
        .catch(error => {
          reject(error)
        })
        .finally(() => {
          
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },
  consultarMovimentosTitulo({state, commit}, id){
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_TITULO_MOVIMENTOS', {})
    commit('SET_TITULO_PAGAMENTOS', {})
    if(typeof(id) !== 'string') {
      return;
    }
    return new Promise((resolve, reject) => {
      Request.get('/titulo_receber_movimentos/' + id)
        .then(response => {
          commit('SET_TITULO_MOVIMENTOS', response.body.corpo['movimentos'])
          commit('SET_TITULO_PAGAMENTOS', response.body.corpo['pagamentos'])
          resolve()
        })
        .catch(error => {
          reject(error)
        })
        .finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },
  aplicarDescontoManual(store, {titulo, valor, motivo} ) {

    return new Promise((resolve, reject) => {
      const data = {
        valor_desconto_manual: valor,
        motivo_desconto_manual: motivo
      }
      let url = '/titulo_receber/aplica_desconto_manual' + titulo;
      console.log(url);
      Request.post(url, data)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Desconto aplicado!'
          })
          resolve()
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: 'E',
            mensagem: error
          })
          reject(error)
        })
    })
  }


}
