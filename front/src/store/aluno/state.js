export default {
  paginaAtual: 1,
  estaCarregando: false,
  order: '',
  direcao: '',
  todosItensCarregados: false,
  totalItens: null,
  lista: [],
  itemSelecionadoID: null,
  itemSelecionado: {
    id: null,
    pessoa_id: null,
    classificacao_aluno_id: null,
    tipo_visibilidade_id: null,
    excluido: null,
    cod_aluno_importado: null,
    foto: null,
    escolaridade: null,
    emancipado: false,
    responsavel_financeiro_pessoa: null,
    responsavel_financeiro_relacionamento_aluno: null,
    responsavel_didatico_pessoa: null,
    responsavel_didatico_relacionamento_aluno: null
  },
  variante: {
    ATI: {title: 'Ativo'},
    INA: {title: 'Inativo'},
    INT: {title: 'Interessado'},
    TRA: {title: 'Trancado'}
  },
  filtros: {
    aluno: null,
    telefone: null,
    situacao: null,
    cnpj_cpf: null,
    pessoa_sexo: null,
    pessoa_estado_civil: null,
    responsavel_financeiro_pessoa: null,
    responsavel_didatico_pessoa: null,
    emancipado: null,
    classificacao_aluno: null,
    curso: null,
    data_cadastro_inicial: null,
    data_cadastro_final: null,
    data_nascimento_inicial: null,
    data_nascimento_final: null
  }
}
