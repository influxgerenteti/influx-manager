/* global it, describe, expect */
import mutations from '../../../store/condicao-pagamento/mutations'

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

describe('SET_LISTA', () => {
  let lista = new Array(10)
  const state = {
    paginaAtual: 1,
    lista: []
  }

  it('Se a página atual for 1, a lista deve ser substituída com o conteúdo novo', () => {
    mutations.SET_LISTA(state, lista)
    expect(state.lista.length).toEqual(lista.length)
  })

  it('Se a página atual for maior que 1, a lista deve ser incrementada', () => {
    state.paginaAtual = 2
    mutations.SET_LISTA(state, lista)
    expect(state.lista.length).toEqual(20)
  })
})

describe('SET_ESTA_CARREGANDO', () => {
  const state = {
    estaCarregando: false
  }

  it('Define que está carregando a lista', () => {
    mutations.SET_ESTA_CARREGANDO(state, true)
    expect(state.estaCarregando).toEqual(true)
  })

  it('Define que não está carregando a lista', () => {
    mutations.SET_ESTA_CARREGANDO(state, false)
    expect(state.estaCarregando).toEqual(false)
  })
})

describe('SET_ITEM_SELECIONADO_ID', () => {
  const state = {
    itemSelecionadoID: false
  }

  it('Define a ID do item selecionado', () => {
    mutations.SET_ITEM_SELECIONADO_ID(state, 1)
    expect(state.itemSelecionadoID).toEqual(1)
  })
})

describe('LIMPAR_ITEM_SELECIONADO', () => {
  const state = {
    itemSelecionado: {
      id: 1,
      descricao: 'Teste',
      quantidade_parcelas: 2,
      parcelas: [{}, {}]
    }
  }

  it('Limpa os dados do item selecionado', () => {
    mutations.LIMPAR_ITEM_SELECIONADO(state)
    expect(state.itemSelecionado.id).toBe(null)
  })
})

describe('SET_ITEM_SELECIONADO', () => {
  const data = {
    id: 1,
    descricao: 'Teste'
  }
  const state = {
    itemSelecionado: {
      id: null,
      descricao: null
    }
  }

  it('Define os dados do item selecionado', () => {
    mutations.SET_ITEM_SELECIONADO(state, data)
    expect(state.itemSelecionado).toEqual(data)
  })
})

describe('SET_DESCRICAO', () => {
  const descricao = 'Teste'
  const state = {
    itemSelecionado: {
      id: null,
      descricao: null
    }
  }

  it('Define a descrição do item selecionado', () => {
    mutations.SET_DESCRICAO(state, descricao)
    expect(state.itemSelecionado.descricao).toEqual(descricao)
  })
})

describe('SET_PARCELA_DIAS_VENCIMENTO', () => {
  const state = {
    itemSelecionado: {
      parcelas: [
        { numero_parcela: 1, dias_vencimento: null, percentual_parcela: null },
        { numero_parcela: 2, dias_vencimento: null, percentual_parcela: null }
      ]
    }
  }

  it('Define os dias de vencimento de uma parcela na condição de pagamento', () => {
    const parcela = 0
    const valor = 30
    mutations.SET_PARCELA_DIAS_VENCIMENTO(state, {parcela, valor})
    expect(state.itemSelecionado.parcelas[parcela].dias_vencimento).toEqual(valor)
  })
})

describe('SET_PARCELA_PERCENTUAL_PARCELA', () => {
  const state = {
    itemSelecionado: {
      parcelas: [
        { numero_parcela: 1, dias_vencimento: null, percentual_parcela: null },
        { numero_parcela: 2, dias_vencimento: null, percentual_parcela: null }
      ]
    }
  }

  it('Define o percentual de uma parcela na condição de pagamento', () => {
    const parcela = 0
    const valor = 50
    mutations.SET_PARCELA_PERCENTUAL_PARCELA(state, {parcela, valor})
    expect(state.itemSelecionado.parcelas[parcela].percentual_parcela).toEqual(valor)
  })
})

describe('SET_QUANTIDADE_PARCELAS', () => {
  const state = {
    itemSelecionado: {
      quantidade_parcelas: 0,
      parcelas: []
    }
  }

  it('Define a quantidade de parcelas como 2', () => {
    mutations.SET_QUANTIDADE_PARCELAS(state, 2)
    expect(state.itemSelecionado.parcelas.length).toEqual(2)
    expect(state.itemSelecionado.parcelas[0].percentual_parcela).toEqual(50)
  })

  it('Define a quantidade de parcelas como 3', () => {
    mutations.SET_QUANTIDADE_PARCELAS(state, 3)
    expect(state.itemSelecionado.parcelas.length).toEqual(3)
    expect(state.itemSelecionado.parcelas[0].percentual_parcela).toEqual(33.33)

    expect(state.itemSelecionado.parcelas[2].percentual_parcela).toEqual(33.34)
  })
})
