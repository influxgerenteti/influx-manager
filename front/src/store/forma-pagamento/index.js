import state from './state'
import mutations from './mutations'
import actions from './actions'

const franqueadas = {
  namespaced: true,
  state,
  actions,
  mutations
}

export default franqueadas
