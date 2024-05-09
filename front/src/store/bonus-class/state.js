export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  order: '',
  direcao: '',
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    funcionario: null,
    data_aula: '',
    sala_franqueada: null,
    horario_inicio: '',
    horario_termino: '',
    concluido: null
  },
  filtros: {}
}
