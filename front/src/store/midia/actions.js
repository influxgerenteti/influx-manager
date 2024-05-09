import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import store from '../index'

const url = 'midia'

const converterDados = (item) => {
  const data = {...item}

  data.tipo = data.tipo ? data.tipo.value : null
  data.situacao = data.situacao ? data.situacao.value : null

  return data
}

const converterDadosFiltro = (item) => {
  const data = {...item}

  data.tipo = item.tipo ? item.tipo.value : null

  return data
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {pagina: state.paginaAtual, ...converterDadosFiltro(state.filtros), order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens.map((item) => {
            item.podeEditarSituacao = item.franqueada.id === store.state.root.usuarioLogado.franqueadaSelecionada
            item.objetoVisibilidade = item.midiaFranqueadas.find(m => m.franqueada.id === store.state.root.usuarioLogado.franqueadaSelecionada)

            return item
          })

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
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, converterDados(state.itemSelecionado))
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
        })
    })
  },

  atualizar ({state, commit}, convert = true) {
    if (convert) {
      state.itemSelecionado = converterDados(state.itemSelecionado)
    }

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
        })
    })
  }
}
