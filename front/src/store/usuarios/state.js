export default {
  paginaAtual: 1,
  estaCarregando: false,
  order: '',
  direcao: '',
  todosItensCarregados: false,
  totalItens: null,
  listaUsuarios: [],
  usuarioSelecionadoId: null,
  objUsuario: {
    nome: '',
    email: '',
    cpf: '',
    senha: '',
    franqueada_padrao: '',
    franqueadas: [],
    situacao: '',
    errorMessage: '',
    papels: [],
    dados_permissao: null
  }
}
