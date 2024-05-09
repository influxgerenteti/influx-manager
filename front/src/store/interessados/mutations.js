import { converteFormatoBrasilParaAmericano } from "@/utils/date"
export default {
  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }

    state.lista = state.lista.concat(lista)
  },

  SET_FUNIL_VENDAS (state, objeto) {
    state.funilVendas = objeto
    /* if (state.paginaAtual === 1) {
      state.funilVendas = lista
      return
    }

    state.funilVendas = state.funilVendas.concat(lista) */
  },

  SET_COLUNAS_FUNIL_VENDAS (state, objeto) {
    state.colunasFunilVendas = objeto
  },
//beto
  SET_PESSOA_INDICOU (state, value) {
    if(value === null) {
      state.itemSelecionado.pessoa_indicou_nome = value
    } else {
      state.itemSelecionado.pessoa_indicou_nome = value.nome_contato
      state.itemSelecionado.pessoa_indicou = value.id
    }
  },

    //beto
    SET_FILTRO_PESSOA_INDICOU (state, value) {
      state.filtros.pessoa_indicou_nome = value
    },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, value) {
    state.estaCarregando = value
  },

  SET_ITEM_SELECIONADO_ID (state, value) {
    state.itemSelecionadoID = value
  },

  SET_ITEM_SELECIONADO (state, value) {
    state.itemSelecionado = value
  },

  SET_TIPO_CONTATO (state, value) {
    state.itemSelecionado.tipo_contato = value
  },

  SET_TIPO_PROSPECCAO (state, value) {
    state.itemSelecionado.tipo_prospeccao = value
  },

  
  SET_FILTRO_EMAIL (state, value) {
    state.filtros.email = value
    state.parametros.email = value ? value: null
  },
  
  SET_FILTRO_INTERESSADO (state, value) {
    state.filtros.interessado = value
  },
  
  SET_FILTRO_TELEFONE (state, value) {
    state.filtros.telefone = value
  },
  
  SET_FILTRO_DATA_CADASTRO_DE_TEMPORARIO (state, value) {
   state.filtros.data_cadastro_de = value
    state.parametros.data_cadastro_de = value ? converteFormatoBrasilParaAmericano(value) : null
   
  },
  
  SET_FILTRO_DATA_CADASTRO_ATE_TEMPORARIO (state, value) {

    state.filtros.data_cadastro_ate = value
    state.parametros.data_cadastro_ate = value ? converteFormatoBrasilParaAmericano(value)  : null
  },
  
  SET_FILTRO_DATA_PROXIMO_CONTATO_DE_TEMPORARIO (state, value) {
   state.filtros.data_proximo_contato_de = value
    state.parametros.data_proximo_contato_de = value ? converteFormatoBrasilParaAmericano(value) : null
   
  },
  
  SET_FILTRO_DATA_PROXIMO_CONTATO_ATE_TEMPORARIO (state, value) {

    state.filtros.data_proximo_contato_ate = value
    state.parametros.data_proximo_contato_ate = value ? converteFormatoBrasilParaAmericano(value)  : null
  },
  
  SET_FILTRO_VALIDADE_PROMOCAO_DE (state, value) {
     state.filtros.data_validade_promocao_de = value
    state.parametros.data_validade_promocao_de = value ? converteFormatoBrasilParaAmericano(value)  : null
  },
  
  SET_FILTRO_VALIDADE_PROMOCAO_ATE (state, value) {

    state.filtros.data_validade_promocao_ate = value
    state.parametros.data_validade_promocao_ate = value ? converteFormatoBrasilParaAmericano(value)  : null
  },
  
  SET_FILTRO_GRAU_INTERESSE (state, value) {
    state.filtros.grau_interesse = value
    if (value) {
      state.parametros.grau_interesse = Array.isArray(value) ? value : [value];
    } else {
      state.parametros.grau_interesse = null;
    } 
  },
  

  SET_FILTRO_PROXIMO_CONTATO_HORARIO_DE (state, value) {
    state.filtros.horario_proximo_contato_de = value
    state.parametros.horario_proximo_contato_de = value ? value: null
  },
  
  SET_FILTRO_PROXIMO_CONTATO_HORARIO_ATE (state, value) {
    state.filtros.horario_proximo_contato_ate = value
    state.parametros.horario_proximo_contato_ate = value ? value: null
  },
  
  SET_FILTRO_NOME (state, value) {
    state.filtros.nome = value
    state.parametros.nome = value ? value.nome: null
  },

  SET_FILTRO_IDADE (state, value) {
     state.filtros.idade = value
    state.parametros.idade = value ? value.value: null
  },
  
  SET_FILTRO_IDIOMA (state, value) {
    state.filtros.idioma = value
    state.parametros.idioma = value ? value.id: null
  },

  SET_FILTRO_CONSULTOR (state, value) {
    state.filtros.consultor = value
    state.parametros.consultor = value ? value.id: null
  },

  SET_FILTRO_TIPO_LEAD(state, value) {
     state.filtros.tipo_lead = value
    if (value) {
      state.parametros.tipo_lead = Array.isArray(value) ? value : [value];
    } else {
      state.parametros.tipo_lead = null;
    } 
  },

  SET_FILTRO_SITUACAO (state, value) {
    state.filtros.situacao = value
    if (value) {
      state.parametros.situacao = Array.isArray(value) ? value : [value];
    } else {
      state.parametros.situacao = null;
    } 
  },

  SET_FILTRO_APENAS_HOTLIST (state, value) {
    state.filtros.apenas_hotlist = value
  },

  SET_FILTRO_FRANQUEADA (state, value) {
    state.filtros.franqueada = value
  },

  SET_FILTRO_WORKFLOW (state, value) {
    state.filtros.workflow = value
  },

  SET_FILTRO_TIPO_PROSPECCAO (state, value) {
    state.filtros.tipo_prospeccao = value
    state.parametros.tipo_prospeccao = value ? value.value: null
  },
  SET_FILTRO_TIPO_CONTATO (state, value) {
    state.filtros.tipo_contato = value
    state.parametros.tipo_contato = value ? value.value: null
  },

  SET_FILTRO_PERIODO_PRETENDIDO (state, value) {
    state.filtros.periodo_pretendido = value
   state.parametros.periodo_pretendido = value ? value.value: null
   },

  SET_FILTRO_ETAPA_FUNIL (state, value) {
    state.filtros.workflow = value
    state.parametros.workflow = value ? value.id: null
  },

  SET_FILTRO_MOTIVO_MATRICULA_PERDIDA (state, value) {
    state.filtros.motivo_nao_fechamento = value
    state.parametros.motivo_nao_fechamento = value ? value.id: null
  },

  SET_MANTER_FILTROS(state, value){
    state.filtros.manterFiltros =value
  },

  LIMPAR_ITEM_SELECIONADO (state) {
        
    if (!state.filtros.manterFiltros){
     
    state.itemSelecionadoID = null
    state.itemSelecionado = {
      grau_interesse: null,
      data_proximo_contato: null,
      data_primeiro_atendimento: null,
      horario_proximo_contato: null,
      consultor_funcionario: null,
      consultor_responsavel_funcionario: null,
      workflow: {},
      idiomas: null,
      franqueada: null,
      aluno: null,
      nome: '',
      idade: null,
      tipo_lead: null,
      tipo_contato: null,
      tipo_prospeccao: null,
      email_contato: null,
      email_secundario: null,
      telefone_contato: null,
      telefone_secundario: null,
      sexo: null,
      periodo_pretendido: null,
      curso: null,
      data_validade_promocao: null,
      interessadoAtividadeExtras: [],
      workflow_acao: {}
    }
  } else {
    console.log('caiu else')

  }
}
}
