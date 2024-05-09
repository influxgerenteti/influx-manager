const meses = [
  {text: 'Janeiro', value: 0},
  {text: 'Fevereiro', value: 1},
  {text: 'Março', value: 2},
  {text: 'Abril', value: 3},
  {text: 'Maio', value: 4},
  {text: 'Junho', value: 5},
  {text: 'Julho', value: 6},
  {text: 'Agosto', value: 7},
  {text: 'Setembro', value: 8},
  {text: 'Outubro', value: 9},
  {text: 'Novembro', value: 10},
  {text: 'Dezembro', value: 11}
]

export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  saldoInicial: null,
  totalEntradas: null,
  totalSaidas: null,
  totalConciliados: null,
  totalNaoConciliados: null,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    data_contabil: '',
    data_deposito: '',
    numero_documento: '',
    valor_lancamento: '',
    plano_conta: null,
    forma_pagamento: null,
    operacao: 'C',
    conciliado: 'S',
    observacao: null
  },

  transferir: {
    data_contabil: '',
    conta_destino: null,
    conta_origem: null,
    valor_lancamento: '',
    observacao: null
  },

  estornar: {
    id: null,
    data_estorno: '',
    observacao: null
  },

  dadosConciliarVarios: {
    data_contabil: '',
    data_deposito: '',
    ids: []
  },

  transferirExistente: {
    id: null,
    conta_destino: null
  },

  filtros: {
    avancado: false,
    conta: null,
    mes: {value: (new Date()).getMonth(), text: (new Date()).toLocaleString('pt-br', {month: 'long'})},
    ano: (new Date()).getFullYear(),
    tipo: null,
    conciliado: null,
    data_lancamento_inicio: new Date(new Date().setMonth((new Date()).getMonth() - 1)).format("DD/MM/YYYY"),
    data_lancamento_fim: new Date().format("DD/MM/YYYY"),
    valor_lancamento_de: '',
    valor_lancamento_ate: '',
    forma_cobranca: null,
    usuario: null,
    numero_lancamento: null,
    numero_cheque_cartao: null,
    categoria: null,
    origem: null,
    origem_avancado: null
  },
  meses
}
