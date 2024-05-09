/* global it, describe, expect */
import mutations from '../../../store/banco/mutations'

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
