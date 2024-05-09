import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import store from '../index'

const URL = 'item_produto'
const URL_ITEM = 'item'

export default {

  getLista ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${URL}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          let franqueada = store.state.root.usuarioLogado.franqueadaSelecionada
          const itens = response.body.corpo.itens.map(item => {
            const temItemFranqueada = item.itemFranqueadas.find(f => f.franqueada.id === franqueada)
            if (temItemFranqueada) {
              item.itemFranqueadas = [ temItemFranqueada ]
            } else {
              item.itemFranqueadas = []
            }

            return item
          })

          commit('SET_LISTA', itens)
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

  getListaProdutosServicos ({state, commit}, { franqueada }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${URL_ITEM}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const itens = response.body.corpo.itens.map(item => {
            const temItemFranqueada = item.itemFranqueadas.find(f => f.franqueada.id === franqueada)
            // console.log('temItemFranqueada', franqueada, temItemFranqueada)
            if (temItemFranqueada) {
              item.itemFranqueadas = [ temItemFranqueada ]
            }

            return item
          })

          commit('SET_LISTA', itens)
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

  getItem ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${URL}/${state.itemSelecionadoID}`)
        .then(response => {
          const franqueada = store.state.root.usuarioLogado.franqueadaSelecionada
          const corpo = response.body.corpo
          let temItemFranqueada = corpo.itemFranqueadas.find(f => f.franqueada.id === franqueada)
          if (temItemFranqueada) {
            corpo.itemFranqueadas = [ temItemFranqueada ]
          } else {
            corpo.itemFranqueadas = []
          }

          corpo.itemFranqueadas = corpo.itemFranqueadas.map((item) => {
            item.valor_compra = item.valor_compra.split('.').join(',')
            item.valor_venda = item.valor_venda.split('.').join(',')
            item.valor_venda_2 = item.valor_venda_2.split('.').join(',')
            item.valor_venda_3 = item.valor_venda_3.split('.').join(',')
            item.valor_venda_4 = item.valor_venda_4.split('.').join(',')
            item.valor_venda_5 = item.valor_venda_5.split('.').join(',')
            item.valor_venda_6 = item.valor_venda_6.split('.').join(',')
            return item
          })

          commit('SET_ITEM', corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criar ({state}) {
    return new Promise((resolve, reject) => {
      Request.post(`/${URL_ITEM}/criar`, state.item)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Item criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao criar item.'
          })
        })
    })
  },

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      const data = Object.assign({}, state.item)
      if (data.movimentoEstoques !== undefined) {
        delete data.movimentoEstoques
      }
      Request.patch(`/${URL_ITEM}/atualizar/${state.item.id}`, data)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: `Item alterado com sucesso!`
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }
}
