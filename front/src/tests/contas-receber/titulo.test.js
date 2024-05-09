/* globals describe, it, expect */
import {gerarTitulo
  /*, gerarParcelasParaCadaParcelamento, mesclarParcelasGeradas, validarTitulosGerados */
} from '../../views/titulo-receber/titulo'
// import {round} from '../../utils/number'

const formasCobranca = [
  {
    id: 1,
    descricao: 'Cartão de crédito',
    forma_cartao: true,
    forma_cheque: false,
    forma_boleto: false
  },
  {
    id: 2,
    descricao: 'Cheque',
    forma_cartao: false,
    forma_cheque: true,
    forma_boleto: false
  },
  {
    id: 3,
    descricao: 'Boleto',
    forma_cartao: false,
    forma_cheque: false,
    forma_boleto: true
  }
]

describe('#gerarTitulo()', () => {
  const tituloEntrada = {
    forma_cobranca: null,
    conta_receber: null,
    aluno: null,
    valor_original: 0,
    data_vencimento: (new Date()).toISOString(),
    data_prorrogacao: (new Date()).toISOString()
  }

  describe('[CARTÃO] Quando cartão, se não houver transação de cartão (nova transação)', () => {
    it('Deve conter propriedade para dados do cartão de crédito', () => {
      tituloEntrada.forma_cobranca = formasCobranca[0]
      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.transacao_cartao).toBeInstanceOf(Object)
    })
  })

  describe('[CARTÃO] E se houver transação de cartão (edição do transação)', () => {
    it('Deve transformar os dados da transação para cartão', () => {
      tituloEntrada.forma_cobranca = formasCobranca[0]
      tituloEntrada.transacao_cartao = null
      tituloEntrada.transacao_cartao = {
        id: null
      }

      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.transacao_cartao).toBeInstanceOf(Object)
    })
  })

  describe('[CHEQUE] Quando cheque, se não houver cheque vinculado (novo cheque)', () => {
    it('Deve conter propriedade para dados do cheque', () => {
      tituloEntrada.forma_cobranca = formasCobranca[1]
      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.cheque).toBeInstanceOf(Object)
    })
  })

  describe('[CHEQUE] E se houver (edição do cheque)', () => {
    it('Deve conter propriedade para dados do cheque', () => {
      tituloEntrada.forma_cobranca = formasCobranca[1]
      tituloEntrada.cheque = {
        id: null
      }

      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.cheque).toBeInstanceOf(Object)
    })
  })

  describe('[BOLETO] Quando boleto, se não houver boleto vinculado (novo boleto)', () => {
    it('Deve conter propriedade para dados do boleto', () => {
      tituloEntrada.forma_cobranca = formasCobranca[2]
      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.boleto).toBeInstanceOf(Object)
    })
  })

  describe('[BOLETO] E se houver (edição do boleto)', () => {
    it('Deve conter propriedade para dados do boleto', () => {
      tituloEntrada.forma_cobranca = formasCobranca[2]
      tituloEntrada.boleto = {
        id: null
      }

      const tituloSaida = gerarTitulo(tituloEntrada)
      expect(tituloSaida.boleto).toBeInstanceOf(Object)
    })
  })
})

// describe('#gerarParcelasParaCadaParcelamento()', () => {
//   const descricoesItens = [
//     { descricao: 'Taxa de matrícula' },
//     { descricao: 'Valor do curso regular' },
//     { descricao: 'Book 01, Dicionário de Inglês' }
//   ]

//   const parametrosParcelamento = [
//     {
//       forma_cobranca: formasCobranca[0],
//       data_vencimento: '2019-02-14T12:00:00.000Z',
//       dias_subsequentes: {numero_dia: 10},
//       numero_parcelas: 1,
//       valor_parcela: 150,
//       valor_total: 150,
//       observacao: descricoesItens[0].descricao
//     },
//     {
//       forma_cobranca: formasCobranca[1],
//       data_vencimento: '2019-02-15T12:00:00.000Z',
//       dias_subsequentes: {numero_dia: 10},
//       numero_parcelas: 6,
//       valor_parcela: 279.33,
//       valor_total: 1676,
//       observacao: descricoesItens[1].descricao
//     },
//     {
//       forma_cobranca: formasCobranca[1],
//       data_vencimento: '2019-02-15T12:00:00.000Z',
//       dias_subsequentes: {numero_dia: 10},
//       numero_parcelas: 3,
//       valor_parcela: 110,
//       valor_total: 330,
//       observacao: descricoesItens[2].descricao
//     }
//   ]

//   describe('Cálculo dos títulos (parcelas)', () => {
//     const parcelas = gerarParcelasParaCadaParcelamento(parametrosParcelamento)

//     describe('Calcular as parcelas conforme cada parâmetro de item', () => {
//       it('Deve ter gerado 10 parcelas (não agrupadas)', () => {
//         expect(parcelas.length).toEqual(10)
//       })

//       it('A segunda parcela do 2º parâmetro (3ª ao todo) deve ter vencimento para dia 10/03/2019 e a terceira (4ª ao todo) para dia 10/04/2019', () => {
//         expect(parcelas[2].data_vencimento).toEqual('10/03/2019')
//         expect(parcelas[3].data_vencimento).toEqual('10/04/2019')
//       })
//     })

//     const titulos = mesclarParcelasGeradas(parcelas)
//     describe('Mesclar as parcelas geradas, agrupando por data de vencimento e forma de cobrança', () => {
//       it('Número total de títulos deve ser 7', () => {
//         expect(titulos.length).toEqual(7)
//       })

//       it('Número da parcela do último título deve ser 7', () => {
//         expect(titulos[titulos.length - 1].numero_parcela_documento).toEqual(7)
//       })

//       it('Primeiro título tem a forma de cobrança cartão de crédito, precisa ter as propriedades necessárias para cartão', () => {
//         expect(titulos[0].forma_cobranca).toEqual(formasCobranca[0])
//         expect(titulos[0].transacao_cartao).toBeInstanceOf(Object)
//       })

//       it('Segundo título tem a forma de cobrança cheque, precisa ter as propriedades necessárias para cheque', () => {
//         expect(titulos[1].forma_cobranca).toEqual(formasCobranca[1])
//         expect(titulos[1].cheque).toBeInstanceOf(Object)
//       })

//       it('Valor do 1º título gerado deve ser R$ 150, o do 2º deve ser R$ 389 e o último R$279', () => {
//         expect(titulos[0].valor_original).toEqual(150)
//         expect(titulos[2].valor_original).toEqual(389.33)
//         expect(titulos[6].valor_original).toEqual(279.33)
//       })

//       it('Valor da soma dos títulos gerados deve ser igual ao total dos parametros de parcelamento (R$ 2156,00)', () => {
//         validarTitulosGerados(titulos, parametrosParcelamento)
//         const valorTotalTitulos = titulos.reduce((acc, curr) => acc + curr.valor_original, 0)
//         const valorTotalParametros = parametrosParcelamento.reduce((acc, curr) => acc + (curr.numero_parcelas * curr.valor_parcela), 0)
//         expect(round(valorTotalTitulos)).toEqual(round(valorTotalParametros))
//       })
//     })
//   })
// })
