import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    const pagina = state.paginaAtual
    return new Promise((resolve, reject) => {
      Request.get('/horario/listar', {pagina: pagina, order: state.order, direcao: state.direcao})
        .then(response => {
          commit('SET_LISTA', {pagina: pagina, lista: response.body.corpo.itens})
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

  buscarTodos ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get('/horario/buscar_todos', { order: state.order, direcao: state.direcao })
        .then(response => {
          // Mandando sempre como sendo a primeira página, pois por ser todos, tem que sempre sobrescrever o restante da lista
          commit('SET_LISTA', {lista: response.body.corpo.itens, pagina: 1})
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
      Request.get(`/horario/${state.itemSelecionadoID}`)
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
      Request.post('/horario/criar', state.itemSelecionado)
        .then(response => {
          resolve(response.body.corpo.objetoORM)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Horário criado com sucesso!'
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

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/horario/atualizar/${state.itemSelecionado.id}`, {situacao: state.itemSelecionado.situacao})
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Horário atualizado com sucesso!'
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

  atualizarHorarioAula ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/horario/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Horário atualizado com sucesso!'
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
