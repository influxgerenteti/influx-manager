export default {
  SET_LISTA (state, res) {
    let lista = res instanceof Array ? res : res.lista
    let paginaBuscada = res instanceof Array ? 1 : res.pagina

    if (!paginaBuscada) {
      paginaBuscada = state.paginaAtual
    }
    if (paginaBuscada === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_FUNCIONARIO_DISPONIVEL (state, bFuncionarioDisponivel) {
    state.bFuncionarioDisponivel = bFuncionarioDisponivel
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
    state.itemSelecionado = {...state.itemSelecionado, ...value}
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      pessoa: null,
      cargo: null,
      nivel_instrutor: null,
      banco: null,
      gestor_comercial_funcionario: null,
      tipo_pagamento: 'H',
      apelido: null,
      data_admissao: '',
      data_demissao: '',
      agencia: null,
      digito_agencia: null,
      conta_corrente: null,
      digito_conta_corrente: null,
      recebe_emails: null,
      instrutor: false,
      instrutor_personal: null,
      gestor_comercial: false,
      consultor: false,
      atendente: false,
      coordenador_pedagogico: false,
      situacao: null,
      usuario: null,
      disponibilidades: [
        {
          id: null,
          dia_semana: null,
          hora_inicial: '',
          hora_final: ''
        }
      ]

    }
  },

  LIMPAR_FILTROS (state, value) {
    state.filtros = {
      funcionario: null,
      apelido: null,
      cnpj_cpf: null,
      data_admissao: '',
      data_demissao: '',
      cargo: null,
      tipo_pagamento: [],
      nivel_instrutor: null,
      consultor: null,
      gestor_comercial: null,
      coordenador_pedagogico: null,
      atendente: null,
      instrutor: null,
      instrutor_personal: null,
      email_usuario: null,
      apenas_funcionarios_ativos: null,
      franqueada_personalizada: null
    }
  },

  SET_PESSOA (state, value) {
    state.itemSelecionado.pessoa = value
  },

  SET_FILTROS (state, value) {
    state.filtros = value
  },

  SET_LIMPAR_FILTROS (state, value) {
    state.filtros = {
      funcionario: null,
      apelido: null,
      cnpj_cpf: null,
      data_admissao: '',
      data_demissao: '',
      cargo: null,
      tipo_pagamento: [],
      nivel_instrutor: null,
      consultor: null,
      coordenador_pedagogico: null,
      gestor_comercial: null,
      atendente: null,
      instrutor: null,
      instrutor_personal: null,
      email_usuario: null,
      apenas_funcionarios_ativos: null
    }
  },

  setFranqueadaPersonalizada (state, value) {
    state.filtros.franqueada_personalizada = value
  }
}
