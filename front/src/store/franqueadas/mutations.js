import {toString, toNumber} from '../../utils/number'

export default {
  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_ESTA_CARREGANDO (state, carregando) {
    state.estaCarregando = carregando
  },

  SET_TOTAL_ITENS (state, total) {
    state.totalItens = total
    state.todosItensCarregados = state.totalItens <= state.listaFranqueada.length
  },

  SET_LISTA (state, itens) {
    if (state.paginaAtual === 1) {
      state.listaFranqueada = itens
      return
    }

    state.listaFranqueada = state.listaFranqueada.concat(itens)
  },

  setFranqueadaSelecionada (state, franqueadaId) {
    state.franqueadaSelecionadaId = franqueadaId
  },

  limparFranqueada (state) {
    state.objFranqueada = {
      nome: '',
      cnpj: '',
      cpf: '',
      dias_em_abertos_movimentos: '',
      dias_lembrete_cobranca: '',
      dias_para_negativacao: '',
      sabado_dia_util: '',
      desconto_super_amigos_ativo: true,
      desconto_super_amigos_turbinado_ativo: true,
      razao_social: '',
      endereco: '',
      numero_endereco: '',
      bairro_endereco: '',
      complemento_endereco: '',
      cep_endereco: '',
      estado: null,
      cidade: null,
      inscricao_estadual: '',
      telefone: '',
      telefone_secundario: '',
      email: '',
      email_direcao: '',
      email_comercial: '',
      tipo_movimento_conta_receber: null,
      tipo_movimento_conta_pagar: null,
      situacao: '',
      limite_dias_alteracao_documento: '',
      percentual_desconto_a_vista: null
    }
  },

  setFranqueada (state, franqueada) {
    state.objFranqueada = franqueada
  },

  setNome (state, nome) {
    state.objFranqueada.nome = nome
  },

  setCNPJ (state, cnpj) {
    state.objFranqueada.cnpj = cnpj
  },

  setCPF (state, cpf) {
    state.objFranqueada.cpf = cpf
  },

  setDiasEmAberto (state, diasEmAberto) {
    state.objFranqueada.dias_em_abertos_movimentos = diasEmAberto
  },

  setDiasLembreteCobranca (state, value) {
    state.objFranqueada.dias_lembrete_cobranca = value
  },

  setDiasParaNegativacao (state, diasParaNegativacao) {
    state.objFranqueada.dias_para_negativacao = diasParaNegativacao
  },

  setSabadoDiasUteis (state, sabadoDiaUtil) {
    state.objFranqueada.sabado_dia_util = sabadoDiaUtil
  },

  setDescontoTurbinadoAtivo (state, value) {
    state.objFranqueada.desconto_super_amigos_turbinado_ativo = value
  },

  setDescontoSuperAmigosAtivo (state, value) {
    state.objFranqueada.desconto_super_amigos_ativo = value
  },

  setRazaoSocial (state, razaoSocial) {
    state.objFranqueada.razao_social = razaoSocial
  },

  setEndereco (state, endereco) {
    state.objFranqueada.endereco = endereco
  },

  setInscricaoEstadual (state, inscricaoEstadual) {
    state.objFranqueada.inscricao_estadual = inscricaoEstadual
  },

  setTelefone (state, telefone) {
    state.objFranqueada.telefone = telefone
  },

  setTelefoneSecundario (state, telefoneSecundario) {
    state.objFranqueada.telefone_secundario = telefoneSecundario
  },

  setEmail (state, email) {
    state.objFranqueada.email = email
  },

  setEmailDirecao (state, email) {
    state.objFranqueada.email_direcao = email
  },

  setEmailComercial (state, email) {
    state.objFranqueada.email_comercial = email
  },

  setDescontoSuperAmigos (state, descontoSuperAmigos) {
    state.objFranqueada.desconto_super_amigos = toNumber(toString(descontoSuperAmigos).replace('.', '').replace(',', '.'))
  },

  setDescontoSuperAmigosTurbinado (state, value) {
    state.objFranqueada.desconto_super_amigos_turbinado = toNumber(toString(value).replace('.', '').replace(',', '.'))
  },

  setPercentualMulta (state, percentual) {
    state.objFranqueada.percentual_multa = toNumber(toString(percentual).replace('.', '').replace(',', '.'))
  },

  setPercentualJuroDia (state, percentual) {
    state.objFranqueada.percentual_juro_dia = percentual
  },

  setPercentualDescontoAVista (state, value) {
    state.objFranqueada.percentual_desconto_a_vista = value
  },

  setMovimentoContasReceber (state, movimentoContasReceber) {
    state.objFranqueada.tipo_movimento_conta_receber = movimentoContasReceber
  },

  setMovimentoContasPagar (state, movimentoContasPagar) {
    state.objFranqueada.tipo_movimento_conta_pagar = movimentoContasPagar
  },

  setSituacao (state, situacao) {
    state.objFranqueada.situacao = situacao
  },

  SET_LIMITE_DIAS_ALTERACAO_DOCUMENTO (state, value) {
    state.objFranqueada.limite_dias_alteracao_documento = value
  }
}
