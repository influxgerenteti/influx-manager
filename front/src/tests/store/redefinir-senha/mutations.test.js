/* global test, expect */
import mutations from '../../../store/redefinir-senha/mutations'

test('Adiciona token ao state', () => {
  const state = {token: null}

  mutations.setToken(state, 'Teste')

  expect(state.token).not.toBe(null)
})

test('Adiciona senha ao state', () => {
  const state = {senha: null}

  mutations.setSenha(state, 'Teste')

  expect(state.senha).not.toBe(null)
})

test('Adiciona confirmarSenha ao state', () => {
  const state = {confirmarSenha: null}

  mutations.setConfirmarSenha(state, 'Teste')

  expect(state.confirmarSenha).not.toBe(null)
})

test('Adiciona mensagem de erro e elimina a de sucesso', () => {
  const state = {mensagemErro: null, mensagemSucesso: 'Teste!'}

  mutations.setMensagemErro(state, 'Erro!')

  expect(state.mensagemErro).not.toBe(null)
  expect(state.mensagemSucesso).toBe(null)
})

test('Adiciona mensagem de sucesso e elimina a de erro', () => {
  const state = {mensagemErro: 'Teste!', mensagemSucesso: null}

  mutations.setMensagemSucesso(state, 'Erro!')

  expect(state.mensagemErro).toBe(null)
  expect(state.mensagemSucesso).not.toBe(null)
})
