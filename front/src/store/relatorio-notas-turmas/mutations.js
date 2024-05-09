export default {
    SET_LISTA (state, lista) {

        function obterMedia(valores) {
            let media = valores.reduce((a, b) => parseFloat(a || 0) + parseFloat(b || 0), 0) / valores.length;
            return (Math.round(media * 100) / 100)
        }

        let data = []
        if(lista.nomeRelatorio == 'notas-agrupado-turma') {
            Object.values(lista.data).forEach(turma => {
                let temp = {
                    'turma' : turma.turma,
                    'professor' : turma.professor,
                    'livro' : turma.livro
                }
                let FinalTest = []
                let MidTermTest = []
                let Frequencia = []
                turma.data.forEach(notas => {
                    FinalTest.push(notas.final_test)
                    MidTermTest.push(notas.mid_term_test)
                    Frequencia.push(notas.frequencia)
                })
                temp.mediaFinalTest = obterMedia(FinalTest)
                temp.mediaMidTermTest = obterMedia(MidTermTest)
                temp.mediaFrequencia = obterMedia(Frequencia)
                data.push(temp)
            });
            state.lista = {data: data, nomeRelatorio: lista.nomeRelatorio}
        } else {
            state.lista = lista
        }
    },
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_LISTA_EXCEL(state, value) {

        function obterMedia(valores) {
            let media = valores.reduce((a, b) => parseFloat(a || 0) + parseFloat(b || 0), 0) / valores.length;
            return (Math.round(media * 100) / 100)
        }

        state.listaExcel = []
        let data = []
        if(value.nomeRelatorio == 'notas-agrupado-turma') {
            Object.values(value.data).forEach(turma => {
                let temp = {
                    'turma' : turma.turma,
                    'professor' : turma.professor,
                    'livro' : turma.livro
                }
                let FinalTest = []
                let MidTermTest = []
                let Frequencia = []
                turma.data.forEach(notas => {
                    FinalTest.push(notas.final_test)
                    MidTermTest.push(notas.mid_term_test)
                    Frequencia.push(notas.frequencia)
                })
                temp.mediaFinalTest = obterMedia(FinalTest)
                temp.mediaMidTermTest = obterMedia(MidTermTest)
                temp.mediaFrequencia = obterMedia(Frequencia)
                data.push(temp)
            });
        } else {
            data = value.data
        }        
        state.listaExcel = data
    }
}