export default {
  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_TOTAL_ITENS (state, total) {
    state.totalItens = total
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_LISTA (state, itens) {
    if (state.paginaAtual === 1) {
      state.lista = itens
      return
    }

    state.lista = state.lista.concat(itens)
  },

  SET_ITEM_SELECIONADO (state, id) {
    state.itemSelecionadoID = id
  },

  SET_DETALHES_ITEM_SELECIONADO (state, item) {
    state.itemSelecionado = item
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null,
      pessoa_id: null,
      classificacao_aluno: null,
      emancipado: false,
      excluido: null,
      cod_aluno_importado: null,
      foto: null,
      escolaridade: null,
      responsavel_financeiro_pessoa: null,
      responsavel_financeiro_relacionamento_aluno: null,
      responsavel_didatico_pessoa: null,
      responsavel_didatico_relacionamento_aluno: null
    }
  },

  SET_CLASSIFICACAO_ALUNO (state, classificacao) {
    state.itemSelecionado.classificacao_aluno = classificacao
  },

  SET_PESSOA (state, pessoa) {
    state.itemSelecionado.pessoa = pessoa
  },

  SET_EMANCIPADO (state, emancipado) {
    state.itemSelecionado.emancipado = emancipado
  },

  SET_INTERESSADO_ID (state, interessadoId) {
    state.itemSelecionado.interessado = interessadoId
  },

  SET_FOTO (state, value) {
    state.itemSelecionado.foto = value
  },

  SET_ESCOLARIDADE (state, value) {
    state.itemSelecionado.escolaridade = value
  },

  SET_RESPONSAVEL_FINANCEIRO (state, value) {
    state.itemSelecionado.responsavel_financeiro_pessoa = value
  },

  SET_RESPONSAVEL_FINANCEIRO_RELACIONAMENTO (state, value) {
    state.itemSelecionado.responsavel_financeiro_relacionamento_aluno = value
  },

  SET_RESPONSAVEL_DIDATICO (state, value) {
    state.itemSelecionado.responsavel_didatico_pessoa = value
  },

  SET_RESPONSAVEL_DIDATICO_RELACIONAMENTO (state, value) {
    state.itemSelecionado.responsavel_didatico_relacionamento_aluno = value
  },

  SET_FILTRO_ALUNO (state, value) {
    state.filtros.aluno = value
  },

  SET_FILTRO_TELEFONE (state, value) {
    state.filtros.telefone = value
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
  },

  SET_FILTRO_CNPJ_CPF (state, value) {
    state.filtros.cnpj_cpf = value
  },

  SET_FILTRO_PESSOA_SEXO (state, value) {
    state.filtros.pessoa_sexo = value
  },

  SET_FILTRO_PESSOA_ESTADO_CIVIL (state, value) {
    state.filtros.pessoa_estado_civil = value
  },

  SET_FILTRO_RESPONSAVEL_FINANCEIRO_PESSOA (state, value) {
    state.filtros.responsavel_financeiro_pessoa = value
  },

  SET_FILTRO_RESPONSAVEL_DIDATICO_PESSOA (state, value) {
    state.filtros.responsavel_didatico_pessoa = value
  },

  SET_FILTRO_EMANCIOPADO (state, value) {
    state.filtros.emancipado = value
  },

  SET_FILTRO_CLASSIFICACAO_ALUNO (state, value) {
    state.filtros.classificacao_aluno = value
  },

  SET_FILTRO_CURSO (state, value) {
    state.filtros.curso = value
  },

  SET_FILTRO_DATA_CADASTRO_INICIAL (state, value) {
    state.filtros.data_cadastro_inicial = value
  },

  SET_FILTRO_DATA_CADASTRO_FINAL (state, value) {
    state.filtros.data_cadastro_final = value
  },

  SET_FILTRO_DATA_NASC_INICIAL (state, value) {
    state.filtros.data_nascimento_inicial = value
  },

  SET_FILTRO_DATA_NASC_FINAL (state, value) {
    state.filtros.data_nascimento_final = value
  }

}
