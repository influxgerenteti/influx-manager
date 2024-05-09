import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

function converterObjetoParaEnvio (item) {
  const data = Object.assign({}, item)
  data.idioma = data.idioma.id
  data.servico = data.servico.id
  data.modalidade_turma = data.modalidade_turma.id
  data.intensidade = data.intensidadeSelecionada
  delete data.intensidadeSelecionada
  return data
}

export default {
  listar ({state, commit}, buscarTodos) {
    commit('SET_ESTA_CARREGANDO', true)
    const data = {pagina: state.paginaAtual, order: state.order, direcao: state.direcao}
    if (buscarTodos) {
      data.buscar_todos = true
    }

    return new Promise((resolve, reject) => {
      Request.get('/curso/listar', data)
        .then(response => {
          const lista = response.body.corpo.itens
          lista.forEach(element => {
            element.intensidadeSelecionada = null
            if (element.intensidade_regular === true) {
              element.intensidadeSelecionada = 'R'
            }

            if (element.intensidade_semi_intensivo === true) {
              element.intensidadeSelecionada = 'S'
            }

            if (element.intensidade_intensivo === true) {
              element.intensidadeSelecionada = 'I'
            }
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
      Request.get(`/curso/${state.itemSelecionadoID}`)
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
      const data = converterObjetoParaEnvio(state.itemSelecionado)

      Request.post('/curso/criar', data)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Curso criado com sucesso!'
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
      const data = converterObjetoParaEnvio(state.itemSelecionado)

      Request.patch(`/curso/atualizar/${state.itemSelecionado.id}`, data)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Curso atualizado com sucesso!'
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
