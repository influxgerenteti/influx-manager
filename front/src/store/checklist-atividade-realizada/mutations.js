export default {
  /* SET_LISTA (state, value) {
    state.lista.push(value)
  } */
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_USUARIO_ID (state, value) {
    state.usuario = value
  },

  SET_CHECKED_ID (state, value) {
    state.checkedId = value
  },

  SET_CHECKLIST_ID (state, value) {
    state.checklistId = value
  },

  SET_CHECKLIST_ATIVIDADE (state, value) {
    state.checklist_atividade = value
  },

  SET_ATIVIDADES_DIARIAS (state, value) {
    state.atividades_diarias = value
  },

  SET_ATIVIDADES_SEMANAIS (state, value) {
    state.atividades_semanais = value
  },

  SET_ATIVIDADES_MENSAIS (state, value) {
    state.atividades_mensais = value
  },

  SET_ATIVIDADES_ATEMPORAIS (state, value) {
    state.atividades_atemporais = value
  },

  RESET_LISTAS_ATIVIDADES (state, value) {
    state.atividades_diarias = []
    state.atividades_semanais = []
    state.atividades_mensais = []
    state.atividades_atemporais = []
  }
}
