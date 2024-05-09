export default {
  estaCarregando: false,
  excelList: [],
  resumo:"",
  listTemp:"",
  lista: { 
    agrupado: false,
    data: [],
    listTemp:[]
   },
  filtros: {
    situacao: [],
    data_inicial_vencimento: '',
    data_final_vencimento: '',
    data_inicial_pagamento: '',
    data_final_pagamento: '',
    agrupar_por: null,
    formas_cobranca: [],
    formas_pagamento: [],
    plano_conta: null
  }
}
