/* global it, describe, expect, jest */
import actions from '../../../store/modulos/actions'

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
  }
}))

describe('getListaModulo', () => {
  const state = { paginaAtual: 1 }
  const commit = jest.fn()

  it('Carregou a lista de módulos', () => {
    mockResponse.body.corpo = {
      totalItens: 15,
      itens: new Array(10)
    }

    return actions.getListaModulo({ state, commit })
      .then(() => {
        expect(commit).toBeCalledWith('SET_LISTA', mockResponse.body.corpo.itens)
        expect(commit).toBeCalledWith('SET_TOTAL_ITENS', mockResponse.body.corpo.total)
        expect(commit).toBeCalledWith('INCREMENTAR_PAGINA_ATUAL')
      })
  })

  it('Não carregou a lista, houve um erro na requisição', () => {
    mockError = new Error()
    return actions.getListaModulo({ state, commit })
      .catch(error => {
        expect(error).toEqual(mockError)
      })
  })
})
