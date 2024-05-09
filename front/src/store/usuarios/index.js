import state from './state'
import mutations from './mutations'
import actions from './actions'

const usuarios = {
  namespaced: true,
  state,
  actions,
  mutations
}

export default usuarios
