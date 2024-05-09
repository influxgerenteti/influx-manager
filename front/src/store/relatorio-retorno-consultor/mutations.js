export default {
    SET_LISTA(state, lista) {
        state.lista = lista
    
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
            if (element.nome_contato in consultores) {
                consultores[element.nome_contato].push(element);
            } else {
                consultores[element.nome_contato] = [element];
            }
        });
        
        for (const [key, value] of Object.entries(consultores)) {
            let consultorTemp = {}
            consultorTemp.nome_contato = key
            consultorTemp.retorno = value.length

            let callbackEfetivos = retorno => retorno.situacao == 'C'
            let efetivo = value.filter(callbackEfetivos)
            consultorTemp.efetivo = efetivo.length;
            
            consultorTemp.retornosEfetivo =  efetivo.length ? Math.round((efetivo.length / value.length) * 100) : 0;
            resumo.push(consultorTemp);
        }
        state.resumo = resumo
        //console.log(state.resumo)
    }
}