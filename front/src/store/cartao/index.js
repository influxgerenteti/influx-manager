import state from './state'
import mutations from './mutations'
import actions from './actions'

const categorias = {
  namespaced: true,
  state,
  actions,
  mutations
}

export default categorias
