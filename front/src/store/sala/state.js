export default {
  lista: [],
  estaCarregando: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    descricao: null,
    sala_franqueada: {
      id: null,
      franqueada_id: null,
      sala_id: null,
      lotacao_maxima: null,
      personal: null,
      situacao: null
    }
  }
}
