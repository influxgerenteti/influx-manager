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

  SET_FILTRO_NACIONAL_EMPRESA_ID (state, value) {
    state.filtrosNacionais.pessoa = value
  },

  SET_FILTRO_NACIONAL_CNPJ (state, value) {
    state.filtrosNacionais.cnpj = value
  },

  SET_FILTRO_NACIONAL_TIPO_ABRANGENCIA (state, value) {
    state.filtrosNacionais.tipo_abrangencia = value
  },

  SET_FILTRO_EMPRESA_PESSOA_ID (state, value) {
    state.filtros.pessoa = value
  },

  SET_FILTRO_ETAPAS_CONVENIO_ID (state, value) {
    state.filtros.etapas_convenio = value
  },

  SET_FILTRO_CONSULTOR_FUNCIONARIO_ID (state, value) {
    state.filtros.consultor_funcionario = value
  },

  SET_FILTRO_SEGMENTO_EMPRESA_CONVENIO_ID (state, value) {
    state.filtros.segmento_empresa_convenio = value
  },

  SET_FILTRO_NOME_CONTATO (state, value) {
    state.filtros.nome_contato = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_FILTRO_DATA_PROXIMO_CONTATO_DE (state, value) {
    state.filtros.data_proximo_contato_de = value
  },

  SET_FILTRO_DATA_PROXIMO_CONTATO_ATE (state, value) {
    state.filtros.data_proximo_contato_ate = value
  },

  SET_FILTRO_HORARIO_PROXIMO_CONTATO_DE (state, value) {
    state.filtros.horario_proximo_contato_de = value
  },

  SET_FILTRO_HORARIO_PROXIMO_CONTATO_ATE (state, value) {
    state.filtros.horario_proximo_contato_ate = value
  },

  SET_FILTRO_USUARIO_FRANQUEADORA (state, value) {
    state.filtros.usuario_franqueadora = value
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

  SET_NOME_FANTASIA (state, value) {
    state.itemSelecionado.nome_fantasia = value
  },

  SET_SEGMENTO (state, value) {
    state.itemSelecionado.segmento_empresa_convenio = value
  },

  SET_PESSOA_CONTATO (state, value) {
    state.itemSelecionado.pessoa = value
  },

  SET_EMAIL_CONTATO (state, value) {
    state.itemSelecionado.email_contato = value
  },

  SET_TELEFONE_CONTATO (state, value) {
    state.itemSelecionado.telefone_contato = value
  },

  SET_TELEFONE_SECUNDARIO (state, value) {
    state.itemSelecionado.telefone_contato_secundario = value
  },

  SET_PROXIMO_CONTATO (state, value) {
    state.itemSelecionado.data_proximo_contato = value
  },

  SET_HORARIO (state, value) {
    state.itemSelecionado.horario_proximo_contato = value
  },

  SET_CONSULTOR_RESPONSAVEL (state, value) {
    state.itemSelecionado.consultor_funcionario = value
  },

  SET_SITUACAO (state, value) {
    state.itemSelecionado.situacao = value
  },

  SET_NOVA_SITUACAO (state, value) {
    state.itemSelecionado.negociacao_parceria_workflow = value
  },

  SET_STATUS (state, value) {
    state.itemSelecionado.etapas_convenio = value
  },

  SET_NOME_CONTATO (state, value) {
    state.itemSelecionado.nome_contato = value
  },

  SET_OBSERVACAO (state, value) {
    state.itemSelecionado.observacao = value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      justificativa_franqueadora: '',
      observacao: '',
      arquivo: '',
      nome_fantasia: null,
      segmento_empresa_convenio: null,
      nome_contato: null,
      pessoa: null,
      email_contato: null,
      telefone_contato: null,
      telefone_contato_secundario: null,
      data_proximo_contato: null,
      horario_proximo_contato: null,
      consultor_responsavel: null,
      situacao: null,
      nova_situacao: null,
      etapas_convenio: null,
      historico: null,
      follow_up: null,
      beneficiario_colaboradores: true,
      beneficiario_dependentes: null,
      beneficiario_associados: null,
      beneficiario_estagiarios: null,
      beneficiario_terceiros: null,
      beneficiario_alunos: null,
      inscricao_municipal: '',
      inscricao_estadual: '',
      fechar_convenio: null
    }
  },

  LIMPAR_FILTROS_NACIONAIS (state) {
    state.filtrosNacionais = {
      pessoa: null,
      cidade: null,
      tipo_abrangencia: 0,
      unidade_responsavel: null,
      razao_social: null,
      segmento_empresa: null,
      data_de_cadastro_de: null,
      data_de_cadastro_ate: null,
      situacao: null,
      etapas_convenio: null,
      beneficiario_colaboradores: true,
      beneficiario_dependentes: null,
      beneficiario_associados: null,
      beneficiario_estagiarios: null,
      beneficiario_terceiros: null,
      beneficiario_alunos: null,
      inscricao_municipal: '',
      inscricao_estadual: '',
      fechar_convenio: null
    }
  }
}
