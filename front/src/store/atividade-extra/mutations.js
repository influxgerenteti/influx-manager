export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },
  SET_LISTA_SELECT(state, lista){
    state.listaSelect = lista
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
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

  SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  },

  SET_ITEM_ID (state, value) {
    state.itemSelecionado.id = value
  },

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_FILTROS (state, value) {
    state.filtros = value
  },

  SET_ITEM (state, value) {
    state.itemSelecionado.item = value
  },

  SET_USUARIO (state, value) {
    state.itemSelecionado.usuario = value
  },

  SET_RESPONSAVEL (state, value) {
    state.itemSelecionado.responsaveis_execucao = value
  },

  SET_SALA_FRANQUEADA (state, value) {
    state.itemSelecionado.sala_franqueada = value
  },

  SET_DESCRICAO_ATIVIDADE (state, value) {
    state.itemSelecionado.descricao_atividade = value
  },

  SET_DATA (state, value) {
    state.itemSelecionado.data = value
  },

  SET_HORARIO_INICIO (state, value) {
    state.itemSelecionado.hora_inicio = value
  },

  SET_HORARIO_FINAL (state, value) {
    state.itemSelecionado.hora_final = value
  },

  SET_CONCLUIDO (state, value) {
    state.itemSelecionado.concluido = value
  },

  SET_CANCELAMENTO (state, value) {
    state.itemSelecionado.cancelamento = value
  },

  SET_MAXIMO_DE_ALUNOS (state, value) {
    state.itemSelecionado.quantidade_maxima_alunos = value
  },

  SET_FORMA_COBRANCA (state, value) {
    state.itemSelecionado.forma_cobranca = value
  },

  SET_VALOR (state, value) {
    state.itemSelecionado.valor = value
  },

  SET_ISENTA (state, value) {
    state.itemSelecionado.isenta = value
  },

  SET_DADOS_ALUNO (state, value) {
    state.itemSelecionado.dados_alunos = value
  },

  SET_DADOS_CONVIDADOS (state, value) {
    state.itemSelecionado.dados_convidados = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null
    }
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
