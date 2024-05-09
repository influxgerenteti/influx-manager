function buscarFilhos (pai, lista) {
  pai.filhos = lista.filter(item => item.pai.id === pai.id)
  pai.filhos.map(item => buscarFilhos(item, lista))
  return pai
}

function montarArvore (lista) {
  if (!lista) {
    return []
  }

  const arvore = lista.filter(item => typeof item.pai === 'undefined')
  return arvore.map(pai => buscarFilhos(pai, lista.filter(item => typeof item.pai !== 'undefined')))
}

export {
  montarArvore
}
