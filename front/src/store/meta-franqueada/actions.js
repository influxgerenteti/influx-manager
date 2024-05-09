import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/meta_franqueada'

export default {
  listar ({state, commit}, filtros) {
    commit('SET_ESTA_CARREGANDO', true)

    const data = {
      estado: filtros.estado,
      ano: filtros.ano,
      mes: filtros.mes
    }

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, {...data, pagina: state.paginaAtual})
        .then(response => {
          const items = response.body.corpo.itens.map(item => {
            if (!item.metaFranqueadas.length) {
              item.metaFranqueadas.push({ ano: filtros.ano, mes: filtros.mes, meta_1: null, meta_2: null, meta_3: null, meta_franqueadora_1: null, meta_franqueadora_2: null, meta_franqueadora_3: null })
            }

            return item
          })

          commit('SET_LISTA', items)
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

  atualizar ({ state, commit }, data) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/alterar/${data.id}`, data)
        .then(() => {
          resolve()
          // commit('LIMPAR_ITEM_SELECIONADO')
          /* EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Meta atualizada com sucesso!'
          }) */
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao atualizar meta.`
          })
        })
    })
  }
}
