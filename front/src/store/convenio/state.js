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
    justificativa_franqueadora: '',
    observacao: '',
    nome_fantasia: null,
    segmento_empresa_convenio: null,
    nome_contato: null,
    pessoa: null,
    email_contato: null,
    telefone_contato: null,
    telefone_contato_secundario: null,
    data_proximo_contato: null,
    horario_proximo_contato: null,
    consultor_responsavel: null,
    situacao: null,
    negociacao_parceria_workflow: null,
    etapas_convenio: null,
    historico: null,
    follow_up: null,
    beneficiario_colaboradores: true,
    beneficiario_dependentes: null,
    beneficiario_associados: null,
    beneficiario_estagiarios: null,
    beneficiario_terceiros: null,
    beneficiario_alunos: null,
    inscricao_municipal: '',
    inscricao_estadual: '',
    fechar_convenio: null
  },
  filtros: {
    pessoa: null,
    etapas_convenio: null,
    consultor_funcionario: null,
    segmento_empresa_convenio: null,
    nome_contato: null,
    data_proximo_contato_de: null,
    data_proximo_contato_ate: null,
    horario_proximo_contato_de: null,
    horario_proximo_contato_ate: null,
    usuario_franqueadora: null,
    situacao: null
  },
  filtrosNacionais: {
    pessoa: null,
    cidade: null,
    tipo_abrangencia: 0,
    unidade_responsavel: null,
    razao_social: null,
    segmento_empresa: null,
    data_de_cadastro_de: null,
    data_de_cadastro_ate: null,
    situacao: null,
    etapas_convenio: null
  }
}
