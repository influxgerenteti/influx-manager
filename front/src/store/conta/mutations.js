export default {

  SET_LISTA (state, lista) {
    if (state.paginaAtual === 1) {
      state.lista = lista
      return
    }
    state.lista = state.lista.concat(lista)
  },

  SET_SELECIONADO (state, id) {
    state.itemId = id
  },

  LIMPAR_ITEM (state) {
    state.itemId = null
    state.item = {}
  },

  SET_ITEM (state, item) {
    state.item = item
  },

  SET_DESCRICAO (state, descricao) {
    state.item.descricao = descricao
  },

  SET_FRANQUEADA (state, franqueadaId) {
    state.item.franqueada = franqueadaId
  },

  SET_BANCO (state, bancoId) {
    state.item.banco = bancoId
  },

  SET_NUMERO_AGENCIA (state, numeroAgencia) {
    state.item.numero_agencia = numeroAgencia
  },

  SET_DIGITO_AGENCIA (state, digitoAgencia) {
    state.item.digito_agencia = digitoAgencia
  },

  SET_CONTA_CORRENTE (state, contaCorrente) {
    state.item.conta_corrente = contaCorrente
  },

  SET_DIGITO_CONTA_CORRENTE (state, digitoContaCorrente) {
    state.item.digito_conta_corrente = digitoContaCorrente
  },

  SET_OBSERVACAO (state, observacao) {
    state.item.observacao = observacao
  },

  SET_CONSIDERA_FLUXO_CAIXA (state, consideraFluxoCaixa) {
    state.item.considera_fluxo_caixa = consideraFluxoCaixa
  },

  SET_EMPRESA_NO_BANCO (state, empresaNoBanco) {
    state.item.empresa_no_banco = empresaNoBanco
  },

  SET_BANCO_EMITE_BOLETO (state, descrbancoEmiteBoletoicao) {
    state.item.banco_emite_boleto = descrbancoEmiteBoletoicao
  },

  SET_PRIMEIRA_INSTRUCAO (state, primeiraInstrucao) {
    state.item.primeira_instrucao = primeiraInstrucao
  },

  SET_SEGUNDA_INSTRUCAO (state, segundaInstrucao) {
    state.item.segunda_instrucao = segundaInstrucao
  },

  // SET_NUMERO_DIAS_PROTESTO (state, numeroDiasProtesto) {
  //   state.item.numero_dias_protesto = numeroDiasProtesto
  // },

  SET_OBSERVACAO_BOLETO (state, observacaoBoleto) {
    state.item.observacao_boleto = observacaoBoleto
  },

  SET_VARIACAO_CARTEIRA (state, variacaoCarteira) {
    state.item.variacao_carteira = variacaoCarteira
  },

  // SET_NUMERO_DIAS_DEVOLUCAO (state, numeroDiasDevolucao) {
  //   state.item.numero_dias_devolucao = numeroDiasDevolucao
  // },

  SET_MODALIDADE (state, modalidade) {
    state.item.modalidade = modalidade
  },

  SET_NUMERO_DIAS_FLOATING (state, numeroDiasFloating) {
    state.item.numero_dias_floating = numeroDiasFloating
  },

  SET_CARTEIRA (state, carteira) {
    state.item.carteira = carteira
  },

  SET_TELEFONE_CONTATO (state, telefoneContato) {
    state.item.telefone_contato = telefoneContato
  },

  SET_VALOR_SALDO (state, valorSaldo) {
    state.item.valor_saldo = valorSaldo
  },

  SET_TAXA_MULTA (state, taxaMulta) {
    state.item.taxa_multa = taxaMulta
  },

  SET_TAXA_JURO_DIA (state, taxaJuroDia) {
    state.item.taxa_juro_dia = taxaJuroDia
  },

  SET_PERCENTUAL_DESCONTO_ANTECIPADO (state, percentualDescontoAntecipado) {
    state.item.percentual_desconto_antecipado = percentualDescontoAntecipado
  },

  SET_ULTIMO_NOSSO_NUMERO (state, ultimoNossoNumero) {
    state.item.ultimo_nosso_numero = ultimoNossoNumero
  },

  SET_NUMERO_SEQUENCIA_ARQUIVO_COBRANCA (state, numeroSequenciaArquivoCobranca) {
    state.item.numero_sequencia_arquivo_cobranca = numeroSequenciaArquivoCobranca
  },

  SET_TEXTO_MORA_DIARIA (state, textoMoraDiaria) {
    state.item.texto_mora_diaria = textoMoraDiaria
  },

  SET_TEXTO_MULTA_ATRASO (state, textoMultaAtraso) {
    state.item.texto_multa_atraso = textoMultaAtraso
  },

  SET_NUMERO_DIAS_DESCONTO_ANTECIPADO (state, numeroDiasDescontoAntecipado) {
    state.item.numero_dias_desconto_antecipado = numeroDiasDescontoAntecipado
  },

  SET_NUMERO_DIAS_MAX_PAGAMENTO_APOS_VENCIMENTO (state, numeroDiasMaxPagamentoAposVencimento) {
    state.item.numero_dias_max_pagamento_apos_vencimento = numeroDiasMaxPagamentoAposVencimento
  },

  SET_SITUACAO (state, situacao) {
    state.item.situacao = situacao
  },

  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.lista.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  SET_ESTA_CARREGANDO (state, estaCarregando) {
    state.estaCarregando = estaCarregando
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  }

}
