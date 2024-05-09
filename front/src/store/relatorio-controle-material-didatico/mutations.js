export default {
    SET_LISTA (state, lista) {
        state.lista = lista        
    },

    SET_RESUMO(state, value) {
        // soma itens com id duplicado
        let resumoTemp = value
        let idDuplicados = new Set();
        let produtosDuplicados = []

        resumoTemp.forEach( (element) => {
            let arrayDuplicados = Array.from(idDuplicados)
            if(arrayDuplicados.includes(element.id)){
                return
            }
             
            resumoTemp.forEach(e => {
                if(e == element){
                    return
                }

                if(e.id == element.id && e.tipo_item != element.tipo_item){
                    idDuplicados.add(element.id)

                    element.qnt_cancelado = parseInt(element.qnt_cancelado) + parseInt(e.qnt_cancelado)
                    element.qnt_cancelado_semestre_anterior = parseInt(element.qnt_cancelado_semestre_anterior) + parseInt(e.qnt_cancelado_semestre_anterior)
                    element.qnt_entregar = parseInt(element.qnt_entregar) + parseInt(e.qnt_entregar)
                    element.qnt_entregar_semestre_anterior = parseInt(element.qnt_entregar_semestre_anterior) + parseInt(e.qnt_entregar_semestre_anterior)
                    element.qnt_entregue = parseInt(element.qnt_entregue) + parseInt(e.qnt_entregue)
                    element.qnt_entregue_semestre_anterior = parseInt(element.qnt_entregue_semestre_anterior) + parseInt(e.qnt_entregue_semestre_anterior)
                    element.qnt_total = parseInt(element.qnt_total) + parseInt(e.qnt_total)
                    element.qnt_total_semestre_anterior = parseInt(element.qnt_total_semestre_anterior) + parseInt(e.qnt_total_semestre_anterior)
            
                    produtosDuplicados.push(element);   
                }  
            });
            
        })
        idDuplicados = Array.from(idDuplicados)
        let produtosNaoDuplicados = resumoTemp.filter(item => !(idDuplicados.includes(item.id)))
        resumoTemp = produtosDuplicados.concat(produtosNaoDuplicados)
        

        let resumo = []
        resumoTemp.forEach(element => {
            let controleTemp = {}      
            controleTemp.id = element.id
            controleTemp.item = element.item
            if(element.tipo_item === 'm'){
            controleTemp.quantidade_demandada = 
            (parseInt(element.estoque_minimo) 
            + parseInt(element.qnt_entregar) 
            + (
                parseInt(element.qnt_entregue_semestre_anterior) 
                + parseInt(element.qnt_entregar_semestre_anterior)
            ) 
            - parseInt(element.saldo_estoque)
            );

            } else if(element.tipo_item === 'p'){
                controleTemp.quantidade_demandada  = 
                (parseInt(element.estoque_minimo) 
                + parseInt(element.qnt_entregue_semestre_anterior) 
                - parseInt(element.saldo_estoque)
                )
            } else {
                controleTemp.quantidade_demandada  = "--"
            }
            
            controleTemp.saldo_estoque = element.saldo_estoque
            controleTemp.tipo_item = element.tipo_item
            resumo.push(controleTemp);
        });
            
 
        state.resumo = resumo
        //console.log(state.resumo)
    },
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_FILTROS (state, value) {
        state.filtros = value
    },
}