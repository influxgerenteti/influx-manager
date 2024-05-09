import store from '../store/permissao'

function converterParaEnvio (dados) {
  return dados
    .map(modulo => {
      if (modulo.modulo_papel_acao !== undefined) {
        return modulo.modulo_papel_acao.map(mpa => ({ modulo: mpa.modulo_id, papel: mpa.papel_id, acao_sistema: mpa.acao_sistema_id }))
      }

      if (modulo.moduloUsuarioAcaos !== undefined) {
        return modulo.moduloUsuarioAcaos.map(mua => ({ modulo: mua.modulo_id, usuario: mua.usuario_id, acao_sistema: mua.acao_sistema_id }))
      }
    })
    .flat()
    .filter(modulo => !modulo.disabled)
}

function buscarFilhos (pai, lista) {
  pai.filhos = lista.filter(item => item.modulo_pai.id === pai.id).sort((a, b) => a.nome < b.nome)
  pai.filhos.map(item => buscarFilhos(item, lista))

  if (pai.url !== '/dashboard') {
    if (pai.filhos && pai.filhos.length) {
      let isDisabled = true
      pai.filhos.map(filho => {
        if (filho.moduloUsuarioAcaos && filho.moduloUsuarioAcaos.length) {
          isDisabled = false
        }
      })

      if (isDisabled) {
        store.state.listaPermissao.find(item => {
          if (item.id === pai.id) {
            item.disabled = isDisabled
            item.moduloUsuarioAcaos = []
          }
        })
      }
    }
  }

  return pai
}

function montarArvore (lista) {
  if (!lista) {
    return []
  }

  const arvore = lista.filter(item => item.situacao !== 'I' && typeof item.modulo_pai === 'undefined').sort((a, b) => a.nome < b.nome)
  if (arvore.length === 0) {
    return lista
  }
  return arvore.map(pai => buscarFilhos({ ...pai }, lista.filter(item => typeof item.modulo_pai !== 'undefined')))
}

export {
  converterParaEnvio,
  buscarFilhos,
  montarArvore
}
