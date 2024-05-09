export default {
  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_TOTAL_ITENS (state, total) {
    state.totalItens = total
    state.todosItensCarregados = state.totalItens <= state.listaClassificacaoAluno.length
  },

  SET_LISTA (state, itens) {
    if (state.paginaAtual === 1) {
      state.listaClassificacaoAluno = itens
      return
    }

    state.listaClassificacaoAluno = state.listaClassificacaoAluno.concat(itens)
  },

  SET_CLASSIFICACAO_ALUNO_SELECIONADA (state, classificacaoAlunoId) {
    state.classificacaoAlunoSelecionadaId = classificacaoAlunoId
  },

  LIMPAR_CLASSIFICACAO_ALUNO (state) {
    state.objClassificacaoAluno = {}
  },

  SET_CLASSIFICACAO_ALUNO (state, classificacaoAluno) {
    state.objClassificacaoAluno = classificacaoAluno
  },

  SET_ICONE (state, icone) {
    state.objClassificacaoAluno.icone = icone
  },

  SET_NOME (state, nome) {
    state.objClassificacaoAluno.nome = nome
  }

}
