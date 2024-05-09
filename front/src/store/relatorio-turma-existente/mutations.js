export default {
    SET_LISTA (state, lista) {
        state.lista = lista
      
    },
    SET_RESUMO(state, value) {
        if (!value) {
          const defaultValue = {
            media_aluno_por_turma: 0,
            total_alunos: 0,
            total_turmas: 0
          };
      
          state.resumo = [defaultValue]; // Convertendo o objeto em um array
          console.log(state.resumo);
          return;
        }
      
        state.resumo = [value]; // Convertendo o objeto em um array
      },
    
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_PARAMETROS (state, value) {
        state.parametros = value
    },
}