import {stringToISODate, dateToString} from '../../utils/date'
import {round} from '../../utils/number'

const defaultData = {
  titulo: {
    id: null,
    franqueada: null,
    conta_receber: null,
    sacado_pessoa: null,
    aluno: null,
    forma_cobranca: null,
    forma_recebimento: null,
    transacao_cartao: null,
    transferencia_bancaria: null,
    cheque: null,
    boleto: null,
    data_vencimento: null,
    data_prorrogacao: null,
    data_emissao: null,
    valor_original: null,
    valor_despesas: null,
    taxa_multa: null,
    taxa_juro_dia: null,
    valor_saldo_devedor: null,
    observacao: null,
    situacao: 'PEN',
    numero_parcela_documento: null
  },

  transferencia_bancaria: {
    id: null,
    franqueada: null,
    titulo_receber: null,
    agencia: '',
    conta: '',
    situacao: 'PEN',
    tipo_transacao: 'C',
    valor: null,
    data_estorno: null
  },

  transacao_cartao: {
    id: null,
    franqueada: null,
    titulo_receber: null,
    numero_lancamento: null,
    operadora_cartao: null,
    parcelamento_operadora_cartao: null,
    identificador: '',
    situacao: 'PEN',
    tipo_transacao: 'C',
    valor_liquido: '',
    taxa: null,
    previsao_repasse: null,
    data_pagamento: null,
    data_estorno: null
  },

  cheque: {
    id: null,
    franqueada: null,
    banco: null,
    atendente_usuario: null,
    pessoa: null,
    motivo_devolucao_cheque: null,
    titulo_pagar: null,
    titulo_receber: null,
    numero: '',
    titular: null,
    agencia: null,
    data_entrada: null,
    data_bom_para: null,
    data_baixa: null,
    data_devolucao: null,
    valor: '',
    situacao: 'P',
    complemento: null,
    observacao: null,
    excluido: null,
    tipo: 'R'
  },

  boleto: {
    id: null,
    franqueada: null,
    titulo_receber: null,
    nosso_numero: null,
    data_vencimento: null,
    valor: null,
    valor_desconto: null,
    situacao_cobranca: null
  }
}

function gerarTitulo (tituloEntrada) {
  const tituloSaida = Object.assign({}, defaultData.titulo, tituloEntrada)

  if (tituloEntrada.forma_cobranca instanceof Object) {
    if (tituloEntrada.forma_cobranca.forma_cartao === true) {
      if (tituloEntrada.transacao_cartao === undefined) {
        tituloSaida.transacao_cartao = Object.assign({}, defaultData.transacao_cartao, {})
      } else {
        tituloSaida.transacao_cartao = Object.assign({}, defaultData.transacao_cartao, tituloEntrada.transacao_cartao)
      }
    }

    if (tituloEntrada.forma_cobranca.forma_cheque === true) {
      if (tituloEntrada.cheque === undefined) {
        tituloSaida.cheque = Object.assign({}, defaultData.cheque, {})
      } else {
        tituloSaida.cheque = Object.assign({}, defaultData.cheque, tituloEntrada.cheque)
      }
    }

    if (tituloEntrada.forma_cobranca.forma_transferencia === true) {
      tituloSaida.transacao_cartao = null
      tituloSaida.cheque = null
      tituloSaida.boleto = null
      if (tituloEntrada.transferencia_bancaria === undefined) {
        tituloSaida.transferencia_bancaria = Object.assign({}, defaultData.transferencia_bancaria, {})
      } else {
        tituloSaida.transferencia_bancaria = Object.assign({}, defaultData.transferencia_bancaria, tituloEntrada.transferencia_bancaria)
      }
    }

    if (tituloEntrada.forma_cobranca.forma_boleto === true) {
      if (tituloEntrada.boleto === undefined) {
        tituloSaida.boleto = Object.assign({}, defaultData.boleto, {})
      } else {
        tituloSaida.boleto = Object.assign({}, defaultData.boleto, tituloEntrada.boleto)
      }
    }
  }

  return tituloSaida
}

function gerarParcelasParaCadaParcelamento (parametrosParcelamento = []) {
  const parcelas = []

  parametrosParcelamento.forEach(parametro => {
    let date = new Date(stringToISODate(parametro.data_vencimento, true))

    if (!parametro.valor_total) {
      return
    }

    for (let index = 0; index < parametro.numero_parcelas; index++) {
      if (index > 0) {
        date.setDate(1)
        date.setMonth(date.getMonth() + 2)

        if (parametro.dias_subsequentes && parametro.dias_subsequentes.ultimo_dia_mes !== undefined && parametro.dias_subsequentes.ultimo_dia_mes === true) {
          date.setDate(0)
        } else {
          date.setMonth(date.getMonth() - 1)
          if (parametro.dias_subsequentes && parametro.dias_subsequentes.numero_dia) {
            date.setDate(parametro.dias_subsequentes.numero_dia)
          }
        }
      }

      parcelas.push({
        data_vencimento: dateToString(date, true),
        valor: parametro.valor_parcela,
        forma_cobranca: parametro.forma_cobranca,
        observacao: `${parametro.observacao} (${index + 1}/${parametro.numero_parcelas})`,
        desconto_antecipacao: parametro.desconto_antecipacao,
        valorTituloGeradoAutomatico: true
      })
    }
  })

  return parcelas
}

function transformarParcelasEmTitulos (parcelas) {
  const titulos = parcelas.map((parcela, index) => {
    if (!parcela.valor_original) {
      parcela.valor_original = parcela.valor
    }
    parcela.numero_parcela_documento = index + 1
    parcela.valor = round(parcela.valor)
    parcela.valor_original = round(parcela.valor_original)
    if (parcela.valor_saldo_devedor) {
      parcela.valor_saldo_devedor = parcela.valor
    }
    parcela.valor_saldo_devedor = round(parcela.valor_saldo_devedor)

    return gerarTitulo(parcela)
  })
  return titulos
}

function mesclarParcelasGeradas (parcelas) {
  const parcelasPorChave = {}

  parcelas.map(parcela => {
    parcela.valor_original = parcela.valor
    parcela.valor_saldo_devedor = parcela.valor

    const chave = [stringToISODate(parcela.data_vencimento, true), parcela.forma_cobranca.id].join('-')
    if (parcelasPorChave[chave] !== undefined) {
      parcelasPorChave[chave].valor += parcela.valor_original
      parcelasPorChave[chave].valor_original += parcela.valor_original
      parcelasPorChave[chave].valor_saldo_devedor += parcela.valor_saldo_devedor
      parcelasPorChave[chave].desconto_antecipacao = parseFloat(parcelasPorChave[chave].desconto_antecipacao) + parseFloat(parcela.desconto_antecipacao)
      parcelasPorChave[chave].observacao = [parcelasPorChave[chave].observacao, parcela.observacao].join(', ')
    } else {
      parcelasPorChave[chave] = parcela
    }
  })

  const keys = Object.keys(parcelasPorChave).sort()
  let parcelasMescladas = []

  for (let i = 0; i < keys.length; i++) {
    parcelasMescladas.push(parcelasPorChave[keys[i]])
  }

  return parcelasMescladas
}

function validarTitulosGerados (titulos, parametrosParcelamento) {
  const indexesInvalidas = []
  let valorTotal = 0

  titulos.map((titulo, index) => {
    valorTotal = round(valorTotal) + titulo.valor_original

    if (!titulo.forma_cobranca ||
      !titulo.data_vencimento ||
      !titulo.valor_original
    ) {
      indexesInvalidas.push(index)
    }
  })

  return { indexesInvalidas, valorTotal: round(valorTotal) }
}

export {
  defaultData,
  gerarTitulo,
  gerarParcelasParaCadaParcelamento,
  transformarParcelasEmTitulos,
  mesclarParcelasGeradas,
  validarTitulosGerados
}
