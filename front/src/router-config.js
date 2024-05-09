
const rotasAbertas = [
  '/',
  '/login',
  '/criar-senha',
  '/recuperar-senha',
  '/redefinir-senha',
  '/configuracoes/modulo'
]

const listaBrancaAPI = [
  '/api/usuario/login',
  '/api/usuario/enviar-email-redefinir-senha',
  '/api/token/setar-senha',
  '/api/token/buscar'
]

const rotasErros = {
  401: {
    redirecionar: '/login',
    mensagem: 'Você precisa fazer login para ter acesso a esta área.'
  },
  403: {
    redirecionar: '/dashboard',
    mensagem: 'Você não tem acesso a esta área.'
  }
}

export {
  rotasAbertas,
  listaBrancaAPI,
  rotasErros
}
