import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'
import { retornarHora } from '../../utils/date'

const converterDadosParaEnvio = (item) => {
  const data = Object.assign({}, item)
  data.sala = data.id

  return data
}

export default {
  listar ({state, commit}, carregar = true) {
    if (carregar) {
      commit('SET_ESTA_CARREGANDO', true)
    }

    return new Promise((resolve, reject) => {
      Request.get('/sala/listar', {pagina: state.paginaAtual, ...state.filtros, sala_franqueada: true, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens.map(item => {
            if (item.lotacao_maxima === undefined) {
              // item.lotacao_maxima = 1
            }
            item.personal = !!(1 * item.personal)
            return item
           
          })
          //Ordena as salas em ordem alfabética
    
          lista.sort((a, b) => {
            if (a.descricao < b.descricao) return -1
            if (a.descricao > b.descricao) return 1
            return 0
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

  /*   buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/sala_franqueada/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  }, */

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post('/sala_franqueada/criar', converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response.body.corpo.objetoORM)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Sala atualizada com sucesso!'
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
      Request.patch(`/sala_franqueada/atualizar/${state.itemSelecionado.salaFranqueadaId}`, converterDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Sala atualizada com sucesso!'
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

  listarDisponibilidade ({state, commit}, filtros = {}) {
    commit('SET_ESTA_CARREGANDO', true)

    let filtrosAgenda = ''
    for (const [key, value] of Object.entries(filtros)) {
      filtrosAgenda += value ? (filtrosAgenda ? '&' : '?') +key+'='+value : ''
    }
    
    return new Promise((resolve, reject) => {
      Request.get(`/sala_agendamento_personal/${state.itemSelecionado.salaFranqueadaId}${filtrosAgenda}`)
        .then(response => {
          response.body.corpo.forEach(elem => {
            elem.hora_inicial = retornarHora(elem.hora_inicial)
            elem.hora_final = retornarHora(elem.hora_final)
          });
          commit('SET_DISPONIBILIDADES', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  atualizarDisponibilidade ({state, commit}, disponibilidades) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/sala_agendamento_personal/atualizar/${state.itemSelecionado.salaFranqueadaId}`, converterDadosParaEnvio({dados: disponibilidades}))
        .then(response => {
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },
}
