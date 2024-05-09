
export default {
    SET_LISTA (state, lista) {
        state.lista = lista
    },
    
    SET_RESUMO(state, media_frequencia) {
        if (typeof media_frequencia === 'number') {
                 state.media_frequencia = [{media_frequencia}];
        } else {
                state.media_frequencia = media_frequencia;
        }
      
      },
   
    SET_ESTA_CARREGANDO(state, value) {
        state.estaCarregando = value
    },
    SET_FILTROS (state, value) {
        state.filtros = value
    },
    SET_PARAMETROS (state, value) {
        state.parametros = value
    },
  
    
}