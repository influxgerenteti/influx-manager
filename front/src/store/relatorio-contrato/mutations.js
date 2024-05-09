export default {
  SET_LISTA (state, lista) {
      state.lista = lista
  },
  SET_ESTA_CARREGANDO (state, value) {
      state.estaCarregando = value
  },
  SET_PARAMETROS (state, value) {
      state.parametros = value
  },
  LIMPAR_ITEM_SELECIONADO (state) {
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      id: null
    }
    state.filtros = {
        livro: null,
        curso: null,
        idioma: [],
        instrutor: null,
        tipo_movimentacao: [],
        semestre: null,
        situacao_aluno:[],
        situacao_contrato:[],
        listaIdiomasCheckboxes:[],
        responsavel: null,
        responsavel_carteira:null
   
    }
  },
}