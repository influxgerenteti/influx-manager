export default {
  lista: [],
  estaCarregando: false,
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  filtros: {
    data_inicial_vencimento: '',
    data_final_vencimento: '',
    data_inicial_pagamento: '',
    data_final_pagamento: '',
    valor_inicial: 0,
    valor_final: 0,
    favorecido_pessoa: null,
    numero_parcela_documento: null,
    plano_conta: null,
    conta: null,
    forma_cobranca: null,
    forma_pagamento: null,
    situacao: [],
    agrupamento: null,
    campanha: null,
    apenas_folhas_pagamento: null,
    excel: false
  }
}
