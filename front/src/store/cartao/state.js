export default {
  paginaAtual: 1,
  estaCarregando: false,
  todosItensCarregados: false,
  cartaoSelecionadoId: null,
  objCartao: null,
  totalItens: null,
  listaCartao: [],
  order: '',
  direcao: '',
  filtros: {
    numero_lancamento: null,
    operadora_cartao: null,
    situacao: null,
    tipo_transacao: null,
    sacado_pessoa: null,
    identificador: null,
    valor_liquido_inicio: null,
    valor_liquido_fim: null,
    previsao_repasse_inicio: null,
    previsao_repasse_fim: null,
    data_estorno_inicio: null,
    data_estorno_fim: null
  }
}
