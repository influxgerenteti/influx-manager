export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    fornecedor_pessoa: null,
    forma_cobranca: null,
    data_vencimento: '',
    valor_parcela: 0,
    numero_parcelas: 0,
    valor_total: 0,
    observacao: null,
    conta: null,
    itens: [],
    parcelas: {},
    plano_conta: [{ plano_conta: null, valor: 0, complemento: null, numero_sequencia: 1 }],
    quitar_primeira_parcela: false
  },
  filtros: {
    data_inicial_vencimento: '',
    data_final_vencimento: '',
    data_inicial_pagamento: '',
    data_final_pagamento: '',
    valor_inicial: null,
    valor_final: null,
    fornecedor_pessoa: null,
    mes: '',
    ano: '',
    forma_pagamento: '',
    situacao: []
  }
}
