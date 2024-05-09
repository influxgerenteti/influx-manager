import { dateToString } from '../../utils/date'

export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_LISTA_LICOES_APLICADAS_TURMA (state, lista) {
    state.listaLicoesAplicadasTurma = lista
  },

  SET_LISTA_HISTORICO_AULAS (state, lista) {
    state.listaHistoricoAulas = lista
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
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

  SET_TURMA_ID (state, value) {
    state.settedTurmaId = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      data_aula: null,
      finalizada: false,
      franqueada: {},
      licao: {},
      turma: {
        livro: {},
        sala_franqueada: {sala: {}},
        funcionario: {}
      }
    }
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_FILTRO_DESCRICAO (state, value) {
    state.filtros.descricao = value
  },

  SET_FILTRO_HORARIO (state, value) {
    state.filtros.horario = value
  },

  SET_FILTRO_MODALIDADE_TURMA (state, value) {
    state.filtros.modalidade_turma = value
  },

  SET_FILTRO_SALA_FRANQUEADA (state, value) {
    state.filtros.sala_franqueada = value
  },

  SET_FILTRO_FUNCIONARIO (state, value) {
    state.filtros.funcionario = value
  },

  SET_FILTRO_CURSO (state, value) {
    state.filtros.curso = value
  },

  SET_FILTRO_LIVRO (state, value) {
    state.filtros.livro = value
  },

  SET_FILTRO_SEMESTRE (state, value) {
    state.filtros.semestre = value
  },

  SET_FILTRO_DATA_INICIO (state, value) {
    state.filtros.data_inicio = value
  },

  SET_FILTRO_DATA_FIM (state, value) {
    state.filtros.data_fim = value
  },

  SET_DATA_FIM (state, value) {
    state.itemSelecionado.data_fim = dateToString(new Date(value))
  }

}
