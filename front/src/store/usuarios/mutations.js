export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaUsuarios = lista
      return
    }

    state.listaUsuarios = state.listaUsuarios.concat(lista)
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },
  setUsuario (state, usuario) {
    state.objUsuario = usuario
  },

  setUsuarioSelecionado (state, usuarioId) {
    state.usuarioSelecionadoId = usuarioId
  },

  limparUsuario (state) {
    state.objUsuario = {
      nome: '',
      cpf: '',
      email: '',
      senha: '',
      franqueada_padrao: '',
      franqueadas: [],
      situacao: '',
      dataCriacao: '',
      dataUltimoLogin: '',
      errorMessage: '',
      papeis: [],
      dados_permissao: null
    }
  },

  setNome (state, nome) {
    state.objUsuario.nome = nome
  },

  setCpf (state, cpf) {
    state.objUsuario.cpf = cpf
  },

  setEmail (state, email) {
    state.objUsuario.email = email
  },

  setFranqueadoraPadrao (state, franqueadaPadraoId) {
    state.objUsuario.franqueada_padrao = franqueadaPadraoId
  },

  setSituacao (state, situacao) {
    state.objUsuario.situacao = situacao
  },

  SET_FRANQUEADAS (state, value) {
    state.objUsuario.franqueadas = value
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaUsuarios.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, estaCarregando) {
    state.estaCarregando = estaCarregando
  },

  SET_PERMISSOES_PARAMETROS (state, permissoes) {
    state.objUsuario.dados_permissao = permissoes
  },

  SET_PAPEIS_PARAMETROS (state, papeis) {
    state.objUsuario.papels = papeis
  }
}
