import EventBus from '../../utils/event-bus'

export default {
  SET_LISTA (state, lista) {
    state.lista = lista || []

    EventBus.$emit('loaded:help-list', state.lista)
  }
}
