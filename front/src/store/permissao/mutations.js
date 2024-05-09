export default {
  SET_LISTA (state, lista) {
    state.listaPermissao = lista
  },

  SET_ARVORE_ITENS (state, lista) {
    state.arvoreItens = lista
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaPermissao.length
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

  SET_PERMISSAO (state, permissao) {
    state.objPermissao = permissao
  },

  SET_PERMISSAO_SELECIONADA_ID (state, permissaoId) {
    state.permissaoSelecionadaId = permissaoId
  },

  SET_FILTROS (state, filtros) {
    state.filtros = filtros
  },

  LIMPAR_ITEM_SELECIONADO (state) {
    state.permissaoSelecionadaId = null
    state.objPermissao = null
  },

  SET_MODULO_ACAO (state, {checked, moduloID, papelID, usuarioID, acaoID}) {
    const index = state.listaPermissao.findIndex(item => item.id === moduloID)
    const modulo = {...state.listaPermissao[index]}

    if (checked === true) {
      if (papelID !== undefined) {
        modulo.modulo_papel_acao.push({ modulo_id: moduloID, papel_id: papelID, acao_sistema_id: acaoID })
      } else if (usuarioID !== undefined) {
        modulo.moduloUsuarioAcaos.push({ modulo_id: moduloID, usuario_id: usuarioID, acao_sistema_id: acaoID })
      } else {
        throw new Error('Deve ser informado ao menos um papel ou usuário')
      }
    } else {
      if (papelID !== undefined) {
        modulo.modulo_papel_acao = modulo.modulo_papel_acao.filter(item => item.acao_sistema_id !== acaoID)
      } else if (usuarioID !== undefined) {
        modulo.moduloUsuarioAcaos = modulo.moduloUsuarioAcaos.filter(item => item.acao_sistema_id !== acaoID)
      } else {
        throw new Error('Deve ser informado ao menos um papel ou usuário')
      }
    }

    if (modulo.modulo_papel_acao !== undefined) {
      modulo.modulo_papel_acao = modulo.modulo_papel_acao.map(item => ({...item}))
    } else if (modulo.moduloUsuarioAcaos !== undefined) {
      modulo.moduloUsuarioAcaos = modulo.moduloUsuarioAcaos.map(item => ({ ...item }))
    }

    state.listaPermissao.splice(index, 1, modulo)
  }
}
