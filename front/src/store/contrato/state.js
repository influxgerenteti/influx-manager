export default {
  lista: [],
  estaCarregando: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    situacao: 'V',
    semestre: null,
    aluno: null,
    livro: null,
    turma: null,
    curso: null,
    sequencia_contrato: null,
    tipo_contrato: 'M',
    responsavel_venda_funcionario: null,
    responsavel_carteira_funcionario: null,
    data_matricula: '',
    data_inicio_contrato: '',
    data_termino_contrato: '',
    responsavel_financeiro_pessoa: null,
    observacao: null,
    bolsista: false,
    familiar_desconto: null,
    convenio_desconto: null,
    motivo_cancelamento: '',
    data_cancelamento: '',
    aplica_desconto_super_amigos: false,
    aplica_desconto_super_amigos_turbinado: false,
    aluno_indicador: null,
    modalidade_turma: null,
    instrutor: null,
    creditos_personal: null,
    agendamento_personal: [],
    sala_franqueada: null,
    creditos_personal_avulso: {
      quantidade: 0,
      aula_por_semana: 0
    }
    // !Atenção: Sempre que colocar um novo campo aqui, deve ser colocado também na função LIMPAR_ITEM_SELECIONADO do mutations.js
  },

  filtros: {
    aluno: null,
    numero_contrato: null,
    data_inicio_contrato_inicio: '',
    data_inicio_contrato_fim: '',
    data_termino_contrato_inicio: '',
    data_termino_contrato_fim: '',
    situacao: ['V'],
    responsavel_venda_funcionario: null,
    responsavel_carteira_funcionario: null
  },

  valorTotalItens: 0,
  valorTotalParcelas: 0,
  parametrosParcelamento: [],
  titulosReceber: [],
  textoContrato: null,
  codigoMatricula: null
}
