import Request from '../../utils/request'

const url = '/agendamento_personal'

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { ...state.filtros })
        .then(response => {
          const lista = response.body.corpo.itens

          commit('SET_LISTA', lista)
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

  buscarInfoPorContrato ({state, commit}, id) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_info_por_contrato/${id}`)
        .then(response => {
          const lista = response.body.corpo
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  setFiltrosAgenda({state, commit}, filtros) {
    commit('SET_FILTROS_AGENDA', filtros)
  },

  buscarAgendamentos ({state, commit}) {
    const infoCarregando = 'buscarAgendamentos'
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_CARREGANDO_INFO', infoCarregando)

    let filtrosAgenda = ''
    for (const [key, value] of Object.entries(state.filtrosAgenda)) {
      filtrosAgenda += value ? (filtrosAgenda ? '&' : '?') +key+'='+value : ''
    }

    return new Promise((resolve, reject) => {
      Request.get(`${url}/consulta_disponibilidade${filtrosAgenda}`)
        .then(response => {
          const lista = response.body.corpo
          resolve(lista)
        })
        .catch(error => {
          reject(error)
        }).finally(() => {
          if(state.carregandoInfo == infoCarregando) {
            commit('SET_ESTA_CARREGANDO', false)
            commit('SET_CARREGANDO_INFO', '')
          }
        })
    })
  },

  listarDisponibilidade({state, commit}, filtros) {
    const infoCarregando = 'listarDisponibilidade'
    commit('SET_ESTA_CARREGANDO', true)
    commit('SET_CARREGANDO_INFO', infoCarregando)

    let filtrosAgenda = ''
    for (const [key, value] of Object.entries(filtros)) {
      filtrosAgenda += value ? (filtrosAgenda ? '&' : '?') +key+'='+value : ''
    }

    return new Promise((resolve, reject) => {
        Request.get(`/relatorios/instrutores_disponiveis${filtrosAgenda}`, null).then(
            response => {
              const lista = response.body
              resolve(lista)
            }
        ).catch(
            error => {
                reject(error)
            }
        ).finally(() => {
          if(state.carregandoInfo == infoCarregando) {
            commit('SET_ESTA_CARREGANDO', false)
            commit('SET_CARREGANDO_INFO', '')
          }
        })
    })
  }
}
