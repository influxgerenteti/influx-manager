export default {
  SET_LISTA(state, lista) {
    state.lista = lista;
  },
  SET_ESTA_CARREGANDO(state, value) {
    state.estaCarregando = value;
  },
  SET_PARAMETROS(state, value) {
    state.parametros = value;
  },
  SET_FILTRO_TIPO_PROSPECCAO(state, value) {
    state.filtros.tipo_prospeccao = value;
  },
  SET_CONTATO(state, value) {
    let contatos = {};
    let contatosAtivo = {};
    let contatosReceptivo = {};

    let consultor = {};

    value.forEach((element) => {
      if (element.consultor in consultor) {
        consultor[element.consultor].conversaoTotal += parseInt(
          element.conversao
        );
        consultor[element.consultor].push(element);
      } else {
        consultor[element.consultor] = [element];
        consultor[element.consultor].conversaoTotal = parseInt(
          element.conversao
        );
      }
    })
      function separarTipoLead(atendimentos) {
     let resposta = {}
        for (let [key, element] of Object.entries(atendimentos)) {
          let consultorTemp = {};
          element.forEach (el => {
          if (el.tipo_lead) {
            if (el.tipo_lead in consultorTemp) {
              consultorTemp[el.tipo_lead].push(el);
            } else {
              consultorTemp[el.tipo_lead] = [el];
            }
          }
          resposta[key] = consultorTemp
        })
 
        };
      return resposta
      }
 
      consultor = separarTipoLead(consultor);


   
    for (let [keyConsultor, valueLead] of Object.entries(consultor)) {

      for (let [key, value] of Object.entries(valueLead)) {

      if (key == "R") {
        value.forEach((element) => {
          if(!(keyConsultor in contatosReceptivo)){
            
            contatosReceptivo[keyConsultor] = {}
            }
        
          if (element.contato in contatosReceptivo[keyConsultor]) {
            contatosReceptivo[keyConsultor][element.contato].conversaoTotal += parseInt(
              element.conversao
            );
            contatosReceptivo[keyConsultor][element.contato].push(element);
          } else {
            contatosReceptivo[keyConsultor][element.contato] = [element];
            contatosReceptivo[keyConsultor][element.contato].conversaoTotal = parseInt(
              element.conversao
            );
          }
        });
      }
      if (key == "A") {
        value.forEach((element) => {
          if(!(keyConsultor in contatosAtivo)){

          contatosAtivo[keyConsultor] = {}
        
        }
          if (element.prospeccao in contatosAtivo[keyConsultor]) {
            contatosAtivo[keyConsultor][element.prospeccao].conversaoTotal += parseInt(
              element.conversao
            );
            contatosAtivo[keyConsultor][element.prospeccao].push(element);
          } else {
            contatosAtivo[keyConsultor][element.prospeccao] = [element];
            contatosAtivo[keyConsultor][element.prospeccao].conversaoTotal = parseInt(
              element.conversao
            );
          }
        });
      }
    }
    }
 
    function contarContatos(grupoContatos, lead) {

      let temp = [];
      for (let [key, value] of Object.entries(grupoContatos)) {
     
       
        for (let [keyContato, valueContato] of Object.entries(value)) {
          let obj = {
            consultor: key,
            lead: lead,
            name : keyContato,
            total : valueContato.length,
            conversaoTotal : valueContato.conversaoTotal 
          };
        
         temp.push(obj);
     
        }
       
      }

      return temp;
    }
    state.contatosAtivo = contarContatos(contatosAtivo, "Ativo").concat(
      contarContatos(contatosReceptivo, "Receptivo")
    );
  },
};
