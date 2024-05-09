export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  listaSelect: [],
  itemSelecionado: {
    id: null,
    item: null,
    usuario: null,
    responsaveis_execucao: null,
    sala_franqueada: null,
    descricao_atividade: null,
    data: null,
    hora_inicio: null,
    hora_final: null,
    concluido: null,
    quantidade_maxima_alunos: null,
    forma_cobranca: null,
    valor: null,
    dados_alunos: null,
    isenta: null
  },
  filtros: null
}
