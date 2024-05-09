import nivelamento from "../nivelamento";

export default {
    SET_LISTA (state, lista) {
        state.lista = lista
    },
    SET_TOTAIS (state, lista) {

        let numerosSaldo = 0;
        let total = 0; 
        lista.forEach(element => {
            if(element.situacao != 'CAN'){
                if(element.tipo == "e"){
                    numerosSaldo += parseFloat(element.saldo);
                    total += element.total ? parseFloat(element.total) : 0;
                } else {
                    numerosSaldo -= parseFloat(element.saldo);
                    total -= element.total ? parseFloat(element.total) : 0;
                }
            }

        });

        state.totais.total = total;
        state.totais.saldo = numerosSaldo;
        //state.lista = state.lista.push({total: total, saldo: numerosSaldo });
    },
    SET_ESTA_CARREGANDO(state, value) {
        state.estaCarregando = value
    },
    SET_FILTROS (state, value) {
        state.filtros = value
    },

    SET_EXCEL_LIST(state, value) {
        let temporario = []
        temporario = Array.from(state.lista);
        temporario.push({vencimento: 'Totais', saldo: state.totais.saldo, total: state.totais.total})
        state.excelList = temporario;
    },

    SET_FILTROS_FORMA_COBRANCA (state, value) {
        state.filtros.forma_pagamento = value.id
    },
    SET_FILTROS_SITUACAO (state, value) {
        state.filtros.situacao = value
    },
    SET_FILTROS_CONTA (state, value) {
        state.filtros.conta = value
    },
    SET_FORNECEDOR_PESSOA (state, value) {
        state.filtros.contato = value
    },
    SET_FILTROS_DATA_INICIAL_PAGAMENTO (state, value) {
        state.filtros.data_inicial_pagamento = value
    },
    SET_FILTROS_DATA_FINAL_PAGAMENTO (state, value) {
        state.filtros.data_final_pagamento = value
    },
    SET_FILTROS_DATA_INICIAL_VENCIMENTO (state, value) {
        state.filtros.data_inicial_vencimento = value
    },
    SET_FILTROS_DATA_FINAL_VENCIMENTO (state, value) {
        state.filtros.data_final_vencimento = value
    },
}