export default {
    SET_LISTA (state, lista) {
        state.lista = lista
        let matriculas = 0
        let rematriculas = 0
        lista.forEach(el => {
            if(el.tipo_contrato == 'R') {
                rematriculas++
            } else {
                matriculas++
            }
        });
        state.matriculas = matriculas
        state.rematriculas = rematriculas
    },
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_PARAMETROS (state, value) {
        state.parametros = value
    },
}