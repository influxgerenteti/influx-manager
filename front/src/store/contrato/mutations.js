export default {
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
      situacao: 'V',
      semestre: null,
      aluno: null,
      livro: null,
      turma: null,
      curso: null,
      sequencia_contrato: null,
      tipo_contrato: 'M',
      responsavel_venda_funcionario: null,
      responsavel_carteira_funcionario: null,
      data_matricula: '',
      data_inicio_contrato: '',
      data_termino_contrato: '',
      responsavel_financeiro_pessoa: null,
      observacao: null,
      bolsista: false,
      familiar_desconto: null,
      convenio_desconto: null,
      motivo_cancelamento: '',
      data_cancelamento: '',
      aplica_desconto_super_amigos: false,
      aplica_desconto_super_amigos_turbinado: false,
      aluno_indicador: null,
      modalidade_turma: null,
      instrutor: null,
      creditos_personal: null,
      agendamento_personal: [],
      sala_franqueada: null,
      creditos_personal_avulso: {
        quantidade: 0,
        aula_por_semana: 0
      }
    }
  },

  SET_VALOR_TOTAL_ITENS (state, value) {
    state.valorTotalItens = value
  },

  SET_VALOR_TOTAL_PARCELAS (state, value) {
    state.valorTotalParcelas = value
  },

  SET_TITULOS_RECEBER (state, value) {
    state.titulosReceber = value
  },

  SET_TITULO_RECEBER (state, {index, value}) {
    state.titulosReceber.splice(index, 1, value)
  },

  REMOVER_TITULO_RECEBER (state, index) {
    state.titulosReceber.splice(index, 1)
  },

  SET_TEXTO_CONTRATO (state, value) {
    state.textoContrato = value
  },

  LIMPAR_PARAMETROS_PARCELAMENTO (state) {
    state.parametrosParcelamento = [
      {
        forma_cobranca: null,
        conta: null,
        data_vencimento: '',
        dias_subsequentes: 5,
        numero_parcelas: 1,
        valor_parcela: 0,
        valor_total: 0,
        observacao: null
      },
      {
        forma_cobranca: null,
        conta: null,
        data_vencimento: '',
        dias_subsequentes: 5,
        numero_parcelas: 1,
        valor_parcela: 0,
        valor_total: 0,
        observacao: null
      },
      {
        forma_cobranca: null,
        conta: null,
        data_vencimento: '',
        dias_subsequentes: 5,
        numero_parcelas: 1,
        valor_parcela: 0,
        valor_total: 0,
        observacao: null
      }
    ]
  },

  SET_PARAMETROS_PARCELAMENTO (state, {index, value}) {
    state.parametrosParcelamento[index] = value
  }
}
