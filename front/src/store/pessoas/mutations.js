export default {
  SET_TOTAL_ITENS (state, totalItens) {
    state.totalItens = totalItens
    state.todosItensCarregados = state.totalItens <= state.listaPessoas.length
  },

  SET_PAGINA_ATUAL (state, pagina) {
    state.paginaAtual = pagina
  },

  INCREMENTAR_PAGINA_ATUAL (state) {
    state.paginaAtual++
  },

  SET_ESTA_CARREGANDO (state, estaCarregando) {
    state.estaCarregando = estaCarregando
  },

  setListaPessoas (state, lista) {
    if (state.paginaAtual === 1) {
      state.listaPessoas = lista
      return
    }

    state.listaPessoas = state.listaPessoas.concat(lista)
  },

  setPessoaSelecionada (state, pessoaId) {
    state.pessoaSelecionadaId = pessoaId
  },

  limparPessoa (state) {
    state.objPessoa = {
      bairro_endereco: null,
      categoria: [],
      cd_tipo: null,
      cep_endereco: null,
      cnpj_cpf: null,
      complemento_endereco: null,
      data_cadastramento: null,
      endereco: null,
      excluido: null,
      franqueada: null,
      id: null,
      id_importado: null,
      negativado: null,
      nome_contato: null,
      estado_civil: null,
      sexo: null,
      telefone_preferencial: null,
      tipo_pessoa: 'F',
      data_nascimento: ''
    }
  },

  SET_ID (state, value) {
    state.objPessoa.id = value
  },

  setPessoa (state, pessoa) {
    state.objPessoa = pessoa
  },

  setTipoPessoa (state, tipoPessoa) {
    state.objPessoa.tipo_pessoa = tipoPessoa
  },

  setNumeroIdentidade (state, numeroIdentidade) {
    state.objPessoa.numero_identidade = numeroIdentidade
  },

  setOrgaoEmissor (state, orgaoEmissor) {
    state.objPessoa.orgao_emissor = orgaoEmissor
  },

  setSexo (state, sexo) {
    state.objPessoa.sexo = sexo
  },

  setEstadoCivil (state, estadoCivil) {
    state.objPessoa.estado_civil = estadoCivil
  },

  setRazaoSocial (state, razaoSocial) {
    state.objPessoa.razao_social = razaoSocial
  },

  setNomeFantasia (state, nomeFantasia) {
    state.objPessoa.nome_fantasia = nomeFantasia
  },

  setInscricaoEstadual (state, inscricaoEstadual) {
    state.objPessoa.inscricao_estadual = inscricaoEstadual
  },

  setInscricaoMunicipal (state, inscricaoMunicipal) {
    state.objPessoa.inscricao_municipal = inscricaoMunicipal
  },

  setBanco (state, value) {
    state.objPessoa.banco = value
  },

  setAgencia (state, value) {
    state.objPessoa.agencia = value
  },

  setConta (state, value) {
    state.objPessoa.conta = value
  },

  setDataNascimento (state, value) {
    state.objPessoa.data_nascimento = value
  },

  setNomeContato (state, nomeContato) {
    state.objPessoa.nome_contato = nomeContato
  },

  setCnpjCpf (state, cnpjCpf) {
    state.objPessoa.cnpj_cpf = cnpjCpf
  },

  setEmailPreferencial (state, emailPreferencial) {
    state.objPessoa.email_preferencial = emailPreferencial
  },

  setEmailContato (state, emailContato) {
    state.objPessoa.email_contato = emailContato
  },

  SET_HOME_PAGE (state, homePage) {
    state.objPessoa.home_page = homePage
  },

  setEmailProfissional (state, emailProfissional) {
    state.objPessoa.email_profissional = emailProfissional
  },

  setTelefonePreferencial (state, telefonePreferencial) {
    state.objPessoa.telefone_preferencial = telefonePreferencial
  },

  setTelefoneContato (state, telefoneContato) {
    state.objPessoa.telefone_contato = telefoneContato
  },

  setTelefoneProfissional (state, telefoneProfissional) {
    state.objPessoa.telefone_profissional = telefoneProfissional
  },

  setPlanoConta (state, value) {
    state.objPessoa.plano_conta = value
  },

  SET_CEP_ENDERECO (state, cep) {
    state.objPessoa.cep_endereco = cep
  },

  setEndereco (state, endereco) {
    state.objPessoa.endereco = endereco
  },

  setNumeroEndereco (state, numero) {
    state.objPessoa.numero_endereco = numero
  },

  SET_COMPLEMENTO_ENDERECO (state, complemento) {
    state.objPessoa.complemento_endereco = complemento
  },

  SET_BAIRRO_ENDERECO (state, bairro) {
    state.objPessoa.bairro_endereco = bairro
  },

  SET_ESTADO (state, estadoId) {
    state.objPessoa.estado = estadoId
  },

  SET_CIDADE (state, cidadeId) {
    state.objPessoa.cidade = cidadeId
  },

  SET_OBSERVACAO (state, observacao) {
    state.objPessoa.observacao = observacao
  },

  SET_FILTRO_ALUNO (state, value) {
    state.filtros.aluno = value
  },

  SET_ORDER_BY (state, value) {
    state.order = value.order
    state.direcao = value.direcao
  },

  SET_FILTRO_RAPIDO (state, filtroRapido) {
    state.filtroRapido = filtroRapido
  },
  SET_LISTA (state, lista) {
    state.lista = lista
  },

  LIMPAR_RESPONSAVEL (state) {
    state.responsavel = {
      bairro_endereco: null,
      categoria: [],
      cd_tipo: null,
      cep_endereco: null,
      cnpj_cpf: null,
      complemento_endereco: null,
      data_cadastramento: null,
      endereco: null,
      excluido: null,
      franqueada: null,
      id: null,
      id_importado: null,
      negativado: null,
      nome_contato: '',
      estado_civil: null,
      sexo: null,
      telefone_preferencial: null,
      telefone_contato: null,
      telefone_profissional: null,
      email_preferencial: null,
      email_contato: null,
      email_profissional: null,
      estado: [],
      cidade: [],
      tipo_pessoa: 'F',
      plano_conta: null,
      data_nascimento: '',
      numero_identidade: '',
      orgao_emissor: '',
      nome_fantasia: '',
      razao_social: '',
      inscricao_estadual: '',
      inscricao_municipal: '',
      agencia: '',
      conta: '',
      telefona_preferencial: '',
      observacao: ''
    }
  }

}
