export default {
    SET_LISTA (state, lista) {
        state.lista = lista
    },
    SET_SEMESTRES (state, lista) {
        state.semestres = lista
    },
    SET_FORMA_PAGAMENTO (state, lista) {
        state.formasPagamento = lista
    },
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_FILTROS (state, value) {
        state.filtros.modalidade        = value.modalidade && value.modalidade.length ? value.modalidade : null,
        state.filtros.semestre          = value.semestre ? value.semestre : null,
        state.filtros.situacao          = value.situacao && value.situacao.length ? value.situacao : null,
        state.filtros.formaPagamento    = value.formaPagamento ? value.formaPagamento : null,
        state.filtros.excel             = value.excel ? true : false,
        state.filtros.compararSemestre  = value.compararSemestre ? true : false,
        state.filtros.detalhesAluno     = value.detalhesAluno ? true : false
    },
    SET_RESUMO (state, value) {
        if(!value) {
            state.resumo = {}
            return
        }
        function alunosPorDesconto(faixa_desconto, semestrePassado) {
            if(semestrePassado) {
                let result = value.filter( (aluno) => ( (aluno.desconto > (faixa_desconto - 10)) && aluno.desconto <= faixa_desconto ) )
                return result.length
            } else {
                let result = value.filter( (aluno) => ( (aluno.sub_desconto > (faixa_desconto - 10)) && aluno.sub_desconto <= faixa_desconto ) )
                return result.length
            }
        }

        let resumo = {
            alunosPorDesconto: {
                semestreFiltrado: {}
            },
            descontoMedio: 0,
            descontoMedioAnterior: 0
        };

        let anterior = {};
        for (let i = 10; i <= 100; i += 10) {
            resumo.alunosPorDesconto.semestreFiltrado[i] = alunosPorDesconto(i, true)
            if(state.filtros.compararSemestre){
                anterior[i] = alunosPorDesconto(i, false)
            }
        }
        
        let totalAlunosFiltrados = 0
        for (const [key, value] of Object.entries(resumo.alunosPorDesconto.semestreFiltrado)) {
            totalAlunosFiltrados += value
        }
        Object.assign(resumo.alunosPorDesconto.semestreFiltrado, {total: totalAlunosFiltrados})

        value.map((aluno) => aluno.desconto ? resumo.descontoMedio += parseFloat(aluno.desconto) : null)
        resumo.descontoMedio = (resumo.descontoMedio / value.length)

        
        if(state.filtros.compararSemestre){
            resumo.alunosPorDesconto.semestreAnterior = anterior

            let totalAlunosFiltrados = 0
            for (const [key, value] of Object.entries(resumo.alunosPorDesconto.semestreAnterior)) {
                totalAlunosFiltrados += value
            }
            Object.assign(resumo.alunosPorDesconto.semestreAnterior, {total: totalAlunosFiltrados})

            value.map((aluno) => aluno.sub_desconto ? resumo.descontoMedioAnterior += parseFloat(aluno.sub_desconto) : null)
            let descontosAnterior = value.filter((aluno) => aluno.sub_desconto != '')
            resumo.descontoMedioAnterior = (resumo.descontoMedioAnterior / descontosAnterior.length)
        }
        
        state.resumo = resumo
    }
}