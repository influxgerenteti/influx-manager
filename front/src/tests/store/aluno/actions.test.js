/* global it, describe, expect, jest */
import actions from '../../../store/aluno/actions'

let mockError = false
let mockResponse = { body: { corpo: {} } }

jest.mock('../../../utils/request', () => ({
  get: (_url) => {
    return new Promise((resolve, reject) => {
      if (mockError) {
        return reject(mockError)
      }

      resolve(mockResponse)
    })
  },
  post: (_url) => {
    return new Promise((resolve, reject) => {
      if (mockError) {
        return reject(mockError)
      }

      resolve(mockResponse)
    })
  },
  patch: (_url) => {
    return new Promise((resolve, reject) => {
      if (mockError) {
        return reject(mockError)
      }

      resolve(mockResponse)
    })
  },
  delete: (_url) => {
    return new Promise((resolve, reject) => {
      if (mockError) {
        return reject(mockError)
      }

      resolve(mockResponse)
    })
  }
}))

describe('#listar()', () => {
  const state = { paginaAtual: 1, estaCarregando: false }
  const commit = jest.fn()

  it('Carregou a lista', () => {
    mockError = null
    mockResponse.body.corpo = {
      totalItens: 15,
      itens: new Array(10)
    }

    return actions.listar({ state, commit })
      .then(() => {
        expect(commit).toBeCalledWith('SET_LISTA', mockResponse.body.corpo.itens)
        expect(commit).toBeCalledWith('SET_TOTAL_ITENS', mockResponse.body.corpo.total)
        expect(commit).toBeCalledWith('INCREMENTAR_PAGINA_ATUAL')
        expect(commit).toBeCalledWith('SET_ESTA_CARREGANDO', false)
      })
  })

  it('Não carregou a lista, houve um erro na requisição', () => {
    mockError = new Error()
    return actions.listar({ state, commit })
      .catch(error => {
        expect(commit).toBeCalledWith('SET_ESTA_CARREGANDO', false)
        expect(error).toEqual(mockError)
      })
  })
})

describe('#buscar()', () => {
  const state = { itemSelecionado: 1 }
  const commit = jest.fn()

  it('Não carregou, houve um erro na requisição', () => {
    mockError = new Error()
    return actions.buscar({ state, commit }, {apenas_proxima_licao: 0})
      .catch(error => {
        expect(error).toEqual(mockError)
      })
  })
})

describe('#criar()', () => {
  const state = {
    itemSelecionadoID: 0,
    itemSelecionado: {
      pessoa: 1,
      classificacao_aluno: 1,
      excluido: false
    }
  }
  const commit = jest.fn()

  it('Enviou o item selecionado corretamente', () => {
    mockError = null
    mockResponse.body.corpo = {
      item: {
        id: 1
      }
    }

    return actions.criar({ state, commit })
      .then(() => {
        expect(commit).toBeCalledWith('LIMPAR_ITEM_SELECIONADO')
      })
  })

  it('Houve um erro na requisição', () => {
    mockError = new Error()
    return actions.criar({ state, commit })
      .catch(error => {
        expect(error).toEqual(mockError)
      })
  })
})

describe('#atualizar()', () => {
  const state = {
    itemSelecionadoID: 1,
    itemSelecionado: {
      pessoa: 1,
      classificacao_aluno: 1,
      excluido: false
    }
  }
  const commit = jest.fn()

  it('Enviou o item selecionado corretamente', () => {
    mockError = null
    mockResponse.body.corpo = {
      item: {
        id: 1
      }
    }

    return actions.atualizar({ state, commit })
      .then(() => {
        expect(commit).toBeCalledWith('LIMPAR_ITEM_SELECIONADO')
      })
  })

  it('Houve um erro na requisição', () => {
    mockError = new Error()
    return actions.atualizar({ state, commit })
      .catch(error => {
        expect(error).toEqual(mockError)
      })
  })
})

describe('#remover()', () => {
  const state = {
    itemSelecionadoID: 1
  }
  const commit = jest.fn()

  it('Enviou o item selecionado corretamente', () => {
    mockError = null
    mockResponse.body.corpo = {
      item: {
        id: 1
      }
    }

    return actions.remover({ state, commit })
      .then(() => {
        expect(commit).toBeCalledWith('LIMPAR_ITEM_SELECIONADO')
      })
  })

  it('Houve um erro na requisição', () => {
    mockError = new Error()
    return actions.remover({ state, commit })
      .catch(error => {
        expect(error).toEqual(mockError)
      })
  })
})
