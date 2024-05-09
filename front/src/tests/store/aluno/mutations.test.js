/* global it, describe, expect */
import mutations from '../../../store/aluno/mutations'

describe('SET_LISTA', () => {
  const state = {
    paginaAtual: 1,
    lista: []
  }

  it('Adiciona itens à lista pela primeira vez', () => {
    mutations.SET_LISTA(state, new Array(10))
    mutations.INCREMENTAR_PAGINA_ATUAL(state)
    expect(state.paginaAtual).toEqual(2)
    expect(state.lista.length).toEqual(10)
  })

  it('Adiciona itens à lista pela segunda vez', () => {
    mutations.SET_LISTA(state, new Array(5))
    mutations.INCREMENTAR_PAGINA_ATUAL(state)
    expect(state.paginaAtual).toEqual(3)
    expect(state.lista.length).toEqual(15)
  })
})

describe('SET_PAGINA_ATUAL', () => {
  const state = {
    paginaAtual: 0
  }

  it('Define a página "1" como atual', () => {
    mutations.SET_PAGINA_ATUAL(state, 1)
    expect(state).toEqual({ paginaAtual: 1 })
  })

  it('Define a página "3" como atual', () => {
    mutations.SET_PAGINA_ATUAL(state, 3)
    expect(state).toEqual({ paginaAtual: 3 })
  })
})

describe('SET_ESTA_CARREGANDO', () => {
  const state = {
    estaCarregando: false
  }

  it('Define que a lista está sendo carregada', () => {
    mutations.SET_ESTA_CARREGANDO(state, true)
    expect(state).toEqual({ estaCarregando: true })
  })

  it('Define que a lista não está arregando', () => {
    mutations.SET_ESTA_CARREGANDO(state, false)
    expect(state).toEqual({ estaCarregando: false })
  })
})

describe('INCREMENTAR_PAGINA_ATUAL', () => {
  const state = {
    paginaAtual: 0
  }

  it('Incrementa a página atual em um', () => {
    mutations.INCREMENTAR_PAGINA_ATUAL(state)
    expect(state).toEqual({ paginaAtual: 1 })
  })

  it('Incrementa a página atual em um, novamente', () => {
    mutations.INCREMENTAR_PAGINA_ATUAL(state)
    expect(state).toEqual({ paginaAtual: 2 })
  })
})

describe('SET_TOTAL_ITENS', () => {
  const itens = new Array(10)
  const state = {
    totalItens: 0,
    todosItensCarregados: false,
    lista: itens
  }

  it('Define o total de itens da lista e todos os itens devem estar carregados', () => {
    mutations.SET_TOTAL_ITENS(state, 10)
    expect(state).toEqual({
      totalItens: 10,
      todosItensCarregados: true,
      lista: itens
    })
  })

  it('Redefine o total de itens da lista e não deve constar como todos os itens carregados', () => {
    mutations.SET_TOTAL_ITENS(state, 15)
    expect(state).toEqual({
      totalItens: 15,
      todosItensCarregados: false,
      lista: itens
    })
  })
})

describe('SET_ITEM_SELECIONADO', () => {
  const state = {
    itemSelecionadoID: null
  }

  it('Atribui a ID do item selecionado', () => {
    mutations.SET_ITEM_SELECIONADO(state, 1)
    expect(state).toEqual({ itemSelecionadoID: 1 })
  })
})

describe('SET_DETALHES_ITEM_SELECIONADO', () => {
  const item = {
    id: 1,
    pessoa: {
      id: 1,
      nome_contato: 'Pessoa teste'
    }
  }
  const state = {
    itemSelecionado: null
  }

  it('Atribui detalhes do item selecionado', () => {
    mutations.SET_DETALHES_ITEM_SELECIONADO(state, item)
    expect(state).toEqual({ itemSelecionado: item })
  })
})

describe('LIMPAR_ITEM_SELECIONADO', () => {
  const state = {
    itemSelecionadoID: 1,
    itemSelecionado: {
      id: 1,
      pessoa: {
        nome: 'Pessoa teste'
      }
    }
  }

  it('Elimina o item selecionado', () => {
    mutations.LIMPAR_ITEM_SELECIONADO(state)
    expect(state.itemSelecionadoID).toEqual(null)
    expect(state.itemSelecionado.id).toEqual(null)
  })
})
