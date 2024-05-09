export default {
    SET_LISTA (state, lista) {
        state.lista = lista
    },
    SET_ESTA_CARREGANDO (state, value) {
        state.estaCarregando = value
    },
    SET_PARAMETROS (state, value) {
        state.parametros = value
    },
    SET_TOTAL_ITENS (state, totalItens) {
        state.totalItens = totalItens
        state.todosItensCarregados = state.totalItens <= state.lista.length
      },
      SET_CONTATO(state, value) {
        let contatos = {};
        let contatosAtivo = {}
        let contatosReceptivo = {}
       
        value.forEach((element) => {
          if(element.tipo_contato){
          if (element.tipo_contato in contatos) {
            contatos[element.tipo_contato].push(element);
          } else {
            contatos[element.tipo_contato] = [element];
          }
        }})
        for(let [key, value]of Object.entries(contatos)) {
          
          if(key == "R"){
          value.forEach(element => {
            if (element.contato in contatosReceptivo) {
              contatosReceptivo[element.contato].push(element);
            } else {
              contatosReceptivo[element.contato] = [element];
            }
          })
          }
          if(key == "A"){
            value.forEach(element => {
              if (element.prospeccao in contatosAtivo) {
                contatosAtivo[element.prospeccao].push(element);
              } else {
                contatosAtivo[element.prospeccao] = [element];
              }
            })
          }
    
        }
        function contarContatos(grupoContatos, lead){
          
          let temp = []
      for(let [key, value] of Object.entries(grupoContatos)){
        let obj = {
          name:key,
          count:value.length,
          lead: lead
        }
      temp.push(obj)
      console.log(obj)
      }
      return temp
        }
        state.contatosAtivo = contarContatos(contatosAtivo,'Ativo').concat(contarContatos(contatosReceptivo, 'Receptivo'))  
      },
}