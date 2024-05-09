export default {
  // SET_LISTA(state, lista) {
  //   let listaTemp = [];
  //   if ("turmas" in lista) {
  //     lista.turmas.forEach((element) => {
  //       let alunoTemp = { turma: element.turma };
  //       element.alunos.forEach((aluno) => {
  //         alunoTemp.Valor_Devedor = aluno.Valor_Devedor;
  //         alunoTemp.Valor_Pago = aluno.Valor_Pago;
  //         alunoTemp.Valor_Total = aluno.Valor_Total;
  //         alunoTemp.nome_Aluno = aluno.nome_Aluno;
  //         alunoTemp.parcelas_Curso = aluno.parcelas_Curso;
  //         listaTemp.push(alunoTemp);
  //       });
  //     });
  //    state.lista = listaTemp;
     
  //   }
 
  // },
  SET_LISTA (state, lista) {
    state.lista = lista
  },

  SET_TOTAL_ITENS(state, totalItens) {
    state.totalItens = totalItens;
    state.todosItensCarregados = state.totalItens <= state.lista.length;
  },

  SET_PAGINA_ATUAL(state, pagina) {
    state.paginaAtual = pagina;
  },

  INCREMENTAR_PAGINA_ATUAL(state) {
    state.paginaAtual++;
  },

  SET_ESTA_CARREGANDO(state, value) {
    state.estaCarregando = value;
  },

  SET_ITEM_SELECIONADO_ID(state, value) {
    state.itemSelecionadoID = value;
  },

  SET_ITEM_SELECIONADO(state, value) {
    state.itemSelecionado = value;
  },

  SET_FILTROS(state, value) {
    state.filtros = value;
  },

  LIMPAR_ITEM_SELECIONADO(state) {
    state.itemSelecionadoID = null;
    state.itemSelecionado = {
      id: null,
    };
  },

  SET_ORDER_BY(state, value) {
    state.order = value.order;
    state.direcao = value.direcao;
  },

  SET_PARAMETROS(state, value) {
    state.parametros = value;
  },

  SET_SITUACAO(state, value) {
    state.situacao = value;
  },
};
