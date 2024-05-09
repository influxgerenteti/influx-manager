export default {
  paginaAtual: 1,
  estaCarregando: false,
  order: '',
  direcao: '',
  todosItensCarregados: false,
  totalItens: null,
  lista: [],
  itemSelecionadoID: null,
  item: {
    id: null,
    tipoItem: {
      tipo: null
    },
    tipo_item: {
      tipo: null
    },
    franqueada: null,
    descricao: '',
    unidade_medida: '',
    narrativa: '',
    situacao: '',
    itemFranqueadas: [
      {
        movimentoEstoques: [],
        saldo_estoque: 0,
        estoque_minimo: 0,
        valor_compra: 0,
        valor_venda: 0,
        valor_venda_2: 0,
        valor_venda_3: 0,
        valor_venda_4: 0,
        valor_venda_5: 0,
        valor_venda_6: 0
      }
    ]
  },
  filtros: {
    descricao: '',
    unidade_medida: '',
    saldo_estoque_inicial: null,
    saldo_estoque_final: null,
    estoque_minimo_inicial: null,
    estoque_minimo_final: null,
    valor_compra_inicial: null,
    valor_compra_final: null,
    valor_venda_inicial: null,
    valor_venda_final: null,
    situacao: [],
    tipo_item: null,
    filtro_franqueada: null
  },
  itemSelecionado: {
    tipoItem: {
      tipo: null
    },
    tipo_item: {
      tipo: null
    },
    movimentoEstoques: []
  }
}
