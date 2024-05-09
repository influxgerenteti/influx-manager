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
  SET_TIPO_CONTATO(state, value) {
    state.itemSelecionado.tipo_contato = value;
  },

  SET_TIPO_PROSPECCAO(state, value) {
    state.itemSelecionado.tipo_prospeccao = value;
  },

  SET_FILTRO_TIPO_LISTA(state, value) {
    state.filtros.tipo_lista = value;
  },
  SET_RESUMO(state, value) {
    let consultores = {};
    let resumo = [];

    value.forEach((element) => {
      if (element.consultor in consultores) {
        consultores[element.consultor].push(element);
      } else {
        consultores[element.consultor] = [element];
      }
    });

    for (const [key, value] of Object.entries(consultores)) {
      let consultorTemp = {};
      consultorTemp.consultor = key;
      consultorTemp.retorno = value.length;

      let callbackEfetivos = (retorno) => retorno.situacao == "C";
      let efetivo = value.filter(callbackEfetivos);
      consultorTemp.efetivo = efetivo.length;

      consultorTemp.retornosEfetivo = efetivo.length
        ? Math.round((efetivo.length / value.length) * 100)
        : 0;
      resumo.push(consultorTemp);
    }
    state.resumo = resumo;
  },

  SET_GERAL(state, value) {
    let geralUnidade = {};
    let geral = [];

    value.forEach((element) => {
      if (element.data_cadastro in geralUnidade) {
        geralUnidade[element.data_cadastro].push(element);
      } else {
        geralUnidade[element.data_cadastro] = [element];
      }
    });

    for (const [key, value] of Object.entries(geralUnidade)) {
      let consultorTemp = {};
      consultorTemp.data_cadastro = key;
      consultorTemp.retorno = value.length;

      let callbackEfetivos = (retorno) => retorno.situacao == "C";
      let efetivo = value.filter(callbackEfetivos);
      consultorTemp.efetivo = efetivo.length;

      consultorTemp.retornosEfetivo = efetivo.length
        ? Math.round((efetivo.length / value.length) * 100)
        : 0;
      geral.push(consultorTemp);
    }
    state.geral = geral;
  },

  SET_CONTATO(state, value) {
    let contatos = {};
    let contatosAtivo = {}
    let contatosReceptivo = {}
   
    value.forEach((element) => {
      if(element.tipo_lead){
      if (element.tipo_lead in contatos) {
        contatos[element.tipo_lead].push(element);
      } else {
        contatos[element.tipo_lead] = [element];
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
      }
      return temp
    }
    state.contatosAtivo = contarContatos(contatosAtivo,'Ativo').concat(contarContatos(contatosReceptivo, 'Receptivo'))  
  },
};
