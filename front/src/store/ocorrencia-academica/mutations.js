export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
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

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      aluno: null,
      tipo_ocorrencia: null,
      usuario: null,
      franqueada: null,
      funcionario: null,
      data_conclusao: null,
      situacao: null,
      texto: null,
      ocorrenciaAcademicaDetalhes: null
    }
  },

  SET_HISTORICO_DE_DETALHES (state, value) {
    state.itemSelecionado.ocorrenciaAcademicaDetalhes = value
  },

  SET_ID (state, value) {
    state.itemSelecionado.id = value
  },

  SET_ALUNO_ID (state, value) {
    state.itemSelecionado.aluno = value
  },

  SET_TIPO_OCORRENCIA_ID (state, value) {
    state.itemSelecionado.tipo_ocorrencia = value
  },

  SET_USUARIO_ID (state, value) {
    state.itemSelecionado.usuario = value
  },

  SET_FUNCIONARIO_ID (state, value) {
    state.itemSelecionado.funcionario = value
  },

  SET_DATA_CONCLUSAO (state, value) {
    state.itemSelecionado.data_conclusao = value
  },

  SET_DATA_PROXIMO_CONTATO (state, value) {
    state.itemSelecionado.data_proximo_contato = value
  },

  SET_HORARIO (state, value) {
    state.itemSelecionado.horario = value
  },

  SET_SITUACAO (state, value) {
    state.itemSelecionado.situacao = value
  },

  SET_TEXTO (state, value) {
    state.itemSelecionado.texto = value
  },

  SET_FRANQUEADA_ID (state, value) {
    state.itemSelecionado.franqueada = value
  },
  SET_FILTRO_ALUNO (state, value) {
    state.filtros.aluno = value
  },

  SET_FILTRO_TIPO_OCORRENCIA (state, value) {
    state.filtros.tipo_ocorrencia = value
  },

  SET_FILTRO_USUARIO (state, value) {
    state.filtros.usuario = value
  },

  SET_FILTRO_FUNCIONARIO_RESPONSAVEL (state, value) {
    state.filtros.funcionario = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_FILTRO_DATA_ABERTURA (state, value) {
    state.filtros.data_abertura = value
  },

  SET_FILTRO_DATA_FECHAMENTO (state, value) {
    state.filtros.data_fechamento = value
  },

  SET_FILTRO_DATA_MOVIMENTACAO_DE (state, value) {
    state.filtros.data_movimentacao_de = value
  },

  SET_FILTRO_DATA_MOVIMENTACAO_ATE (state, value) {
    state.filtros.data_movimentacao_ate = value
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }
}
