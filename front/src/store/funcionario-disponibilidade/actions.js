import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

const url = '/funcionario_disponibilidade'

export default {
  buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.funcionarioSelecionadoID}`)
        .then(response => {
          const itens = response.body.corpo.itens.map(item => {
            item.funcionario = item.funcionario.id
            item.hora_inicial = item.hora_inicial.replace(/\d{4}-\d{2}-\d{2}T(\d{2}:\d{2}).+/, '$1')
            item.hora_final = item.hora_final.replace(/\d{4}-\d{2}-\d{2}T(\d{2}:\d{2}).+/, '$1')
            item.erro_dia_semana = null
            item.erro_hora_inicial = null
            item.erro_hora_final = null
            return item
          })
          commit('SET_LISTA', itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  atualizar ({ state }) {
    return new Promise((resolve, reject) => {
      const date = (new Date()).toISOString()
      const lista = state.lista.map(disp => {
        const item = Object.assign({}, disp)
        item.hora_inicial = date.replace(/(\d{4}-\d{2}-\d{2}T)\d{2}:\d{2}(.+)/, `$1${item.hora_inicial}$2`)
        item.hora_final = date.replace(/(\d{4}-\d{2}-\d{2}T)\d{2}:\d{2}(.+)/, `$1${item.hora_final}$2`)
        return item
      })

      Request.patch(`${url}/atualizar_multiplos`, {disponibilidades: lista, funcionario: state.funcionarioSelecionadoID})
        .then(response => {
          resolve(response.body.corpo)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Disponibilidade alterada com sucesso!'
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
