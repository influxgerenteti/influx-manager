import { converteFormatoBrasilParaAmericano } from "@/utils/date"

export default {
    SET_LISTA(state, lista) {
        state.lista = lista
        console.log(state.lista)
    },
    SET_ESTA_CARREGANDO(state, value) {
        state.estaCarregando = value
    },
    SET_PARAMETROS(state, value) {
        state.parametros = value
    },
    SET_RESUMO(state, value) {

        let consultores = {}
        let resumo = []
        value.forEach(element => {
            if (element.consultor in consultores) {
                consultores[element.consultor].push(element);
            } else {
                consultores[element.consultor] = [element];
            }
        });

        for (const [key, value] of Object.entries(consultores)) {
            let consultorTemp = {}
            consultorTemp.consultor = key
            consultorTemp.retornos = value.length

            let callbackEfetivos = retorno => retorno.situacao == 'C'
            let efetivos = value.filter(callbackEfetivos)
            consultorTemp.efetivos = efetivos.length;
            
            consultorTemp.retornosEfetivos =  efetivos.length ? Math.round((efetivos.length / value.length) * 100) : 0;
            resumo.push(consultorTemp);
        }
        state.resumo = resumo
        console.log(state.resumo)
    }
}