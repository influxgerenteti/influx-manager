export default {
  lista: [],
  estaCarregando: false,
  order: '',
  direcao: '',
  paginaAtual: 1,
  totalItens: null,
  todosItensCarregados: false,
  itemSelecionadoID: null,
  filtros: {
    nome: null,
    telefone: null,
    idade: null,
    idioma: null,
    consultor: null,
    data_cadastro_de: null,
    data_cadastro_ate: null,
    data_proximo_contato_de: null,
    data_proximo_contato_ate: null,
    horario_proximo_contato_de: null,
    horario_proximo_contato_ate: null,
    tipo_lead: null,
    grau_interesse: null,
    situacao: null
  }
}
