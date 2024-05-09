import state from './state'
import mutations from './mutations'
import actions from './actions'

const pessoas = {
  namespaced: true,
  state,
  actions,
  mutations
}

export default pessoas
