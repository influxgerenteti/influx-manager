import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/transacao_cartao'

const mensagens = {
  sucesso: {
    conciliar: 'Conciliação realizada com sucesso!',
    estornar: 'Estorno realizado com sucesso!'
  },
  erro: {
    conciliar: 'Erro ao efetuar a conciliação.',
    estornar: 'Erro ao efetuar o estorno'
  }
}

let success = 0
let error = 0
function resposta (response, index, tipo) {
  if (response.erro === false) {
    success++
  } else {
    error++
  }

  if (index === 0) {
    if (success > 0) {
      EventBus.$emit('criarAlerta', {
        tipo: 'S',
        mensagem: mensagens.sucesso[tipo]
      })
    }

    if (error > 0) {
      EventBus.$emit('criarAlerta', {
        tipo: error.status > 500 ? 'E' : 'A',
        mensagem: response.mensagem || mensagens.erro[tipo]
      })
    }

    error = 0
    success = 0
  }
}

function converteFiltros (filtros) {
  const data = {...filtros}
  data.operadora_cartao = data.operadora_cartao ? data.operadora_cartao.id : null

  return data
}

export default {
  getListaCartao ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: state.paginaAtual, ...converteFiltros(state.filtros), order: state.order, direcao: state.direcao })
        .then(response => {
          // workaround pra lidar com a forma que a lista está vindo do back
          const listaCartao = response.body.corpo.itens.map((item) => {
            item.valor_liquido = item.valor_liquido - (item.taxa || 0)
            return item
          })
          commit('listaCartao', listaCartao)
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

  getCartao ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.cartaoSelecionadoId}`)
        .then(response => {
          commit('setCartao', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  atualizaDataRepasse ({state, commit}, dataPrevisao) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.cartaoSelecionadoId}`, {previsao_repasse: dataPrevisao})
        .then(response => {
          resolve(response)
        })
        .catch(response => {
          reject(response.body.mensagem)
        })
    })
  },

  conciliar (store, transacoes) {
    return new Promise((resolve, reject) => {
      Request.post(`/transacao_cartao/conciliar_varios/`, transacoes)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conciliação realizada com sucesso!'
          })
          resolve()
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.mensagem || 'Erro ao realizar a conciliação.'
          })
          reject(error)
        })
    })
  },

  estornar (store, transacoes) {
    return new Promise((resolve, reject) => {
      for (let i = transacoes.length; i--;) {
        (function (i) {
          Request.post(`/transacao_cartao/estornar/${transacoes[i].transacao_cartao}`)
            .then(response => {
              if (i === 0) {
                resolve()
              }

              resposta(response.body, i, 'estornar')
            })
            .catch(error => {
              if (i === 0) {
                reject(error)
              }
              resposta(error.body, i, 'estornar')
            })
        })(i)
      }
    })
  }

}
