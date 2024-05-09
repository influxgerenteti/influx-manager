import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'

/**
 * Avaliar se é necessario, caso não seja, realizar os passos abaixo:
 * 1 - Remover este arquivo(deletar no caso)
 * 2 - Excluir referencia no index.js do modulo
 * 3 - Alterar o Lista.vue/Formulario.vue para remover a referencia desta ação
 **/
const url = 'agenda_compromisso'

export default {
  listar ({state, commit}, endDate) {
    commit('SET_ESTA_CARREGANDO', true)
    if (state.filtros.funcionario === null) {
      delete state.filtros.funcionario
    }

    const _endDate = null || endDate

    return new Promise((resolve, reject) => {
      Request.get(`/${url}/listar`, {...state.filtros, ..._endDate})
        .then(response => {
          const error = response.body.corpo.error
          if (error === true) {
            EventBus.$emit('criarAlerta', {
              tipo: 'E',
              mensagem: response.body.corpo.mensagem
            })
          } else {
            if (!_endDate) { commit('SET_LISTA', response.body.corpo.resultados) }
          }
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo.resultados)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  verificaDisponibilidadeFuncionario ({state, commit}, parametros) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/${url}/verifica_disponibilidade`, {funcionario: parametros.funcionario, data_hora_inicio: parametros.data_hora_inicio})
        .then(() => {
          // TODO: se estiver disponivel, permitir prosseguir com a criação do registro na data e hora selecionado
          // TODO: se não estiver disponivel, solicitar confirmação do usuario informando algo do tipo:
          // "Este horario se encontra indisponivel para este funcionario, deseja prosseguir mesmo assim?"
          commit('SET_ESTA_CARREGANDO', false)
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
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/criar`, state.itemSelecionado)
        .then(response => {
          resolve()
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  atualizar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.patch(`/${url}/alterar/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(response => {
          resolve()
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
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
  },

  excluir ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.post(`/${url}/remover/${state.itemSelecionado.id}`, state.itemSelecionado)
        .then(() => {
          resolve()
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Compromisso excluído com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          commit('SET_ESTA_CARREGANDO', false)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao remover o compromisso.'
          })
        })
    })
  }
}
