import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

const urlImportacao = 'importacao_boleto'
const urlBoleto = 'boleto'
const urlRetorno = 'retorno'

export default {
  listar ({state, commit}, listaDoFiltro) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${urlBoleto}/listar`, {pagina: state.paginaAtual, ...state.filtros, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          if (!listaDoFiltro) {
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

  listarAll ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`/${urlBoleto}/listar`, {pagina: state.paginaAtual})
        .then(response => {
          const lista = response.body.corpo.itens

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
      Request.get(`/${urlImportacao}/${state.itemSelecionadoID}`)
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
      Request.post(`/${urlImportacao}/criar`, state.itemSelecionado)
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

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`/${urlImportacao}/atualizar/${state.itemSelecionado.id}`, state.itemSelecionado)
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
  },

  importar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.post(`/${urlRetorno}/importar`, state.itemSelecionado)
        .then(response => {
          resolve()
          commit('SET_ESTA_CARREGANDO', false)
          const boletosProcessados = response.body.corpo.boletos.map(boleto => {
            boleto.foiProcessado = true
            return boleto
          })
          const boletosNE = response.body.corpo.boletosNE.map(boleto => {
            boleto.foiProcessado = false
            return boleto
          })

          commit('SET_LISTA_DE_BOLETOS', boletosProcessados)
          commit('SET_LISTA_DE_BOLETOS_NE', boletosNE)

          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })

          if (state.totalItensDeBoletosNE > 0) {
            EventBus.$emit('criarAlerta', {
              tipo: 'A',
              mensagem: `Erro no processo ${state.totalItensDeBoletosNE} boleto${state.totalItensDeBoletosNE === 1 ? '' : 's'}`
            })
          }
        })
        .catch(error => {
          reject(error)

          commit('SET_ESTA_CARREGANDO', false)

          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  }
}
