import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import store from '../index'

const url = 'item_servico'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens
          const listaSerialize = lista.map(item => {
            let id = store.state.root.usuarioLogado.franqueadaSelecionada
            let objItem = item.itemFranqueadas.find(itemFranqueada => itemFranqueada.franqueada.id === id)
            const arr = objItem ? [objItem] : []
            item.itemFranqueadas = arr
            return item
          })

          commit('SET_LISTA', listaSerialize)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
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
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        }).finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  criar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        }).finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        }).finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  atualizarValorServico ({state, commit}, item) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/atualizar_valor_venda/${item.id}`, { valor_venda: item.valor_venda, filtro_franqueada: item.filtro_franqueada })
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        }).finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  }
}
