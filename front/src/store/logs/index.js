import state from './state'
import mutations from './mutations'
import actions from './actions'

const logs = {
  namespaced: true,
  state,
  actions,
  mutations
}

export default logs
