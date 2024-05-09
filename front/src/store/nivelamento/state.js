export default {
  lista: [],
  estaCarregando: false,
  estaCarregandoFormulario: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    item: null,
    usuario: null,
    sala_franqueada: null,
    descricao_atividade: null,
    data: null,
    hora_inicio: null,
    hora_final: null,
    concluido: null,
    interessado: null,
    livro: null
  },
  filtros: {}
}
