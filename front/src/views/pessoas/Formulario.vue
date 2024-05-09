<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="true" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount,2)" />
      </div>
      <div class="body-sector">

        <div v-if="objPessoa && objPessoa.id" class="head-content-sector p-3">
          <span class="font-weight-bold text-muted">Pessoa {{ tipo_pessoa === 'F' ? 'Física' : 'Jurídica' }}</span>
        </div>

        <div v-if="tipoPessoaFixo === null" class="head-content-sector p-3">

          <b-form-radio-group id="radio-group-2" v-model="selected_pessoa" name="radio-sub-component">
            <b-form-radio name="some-radios" value="F" @change="verificarTipoPessoa('F')">Física</b-form-radio>
            <b-form-radio name="some-radios" value="J" @change="verificarTipoPessoa('J')">Jurídica</b-form-radio>
          </b-form-radio-group>

        </div>

        <div class="content-sector sector-verde-c p-3">
          <div class="form-group row">
            <div class="col-md-12">
              <label v-help-hint="'form-pessoa_nome_contato'" for="nome_contato" class="col-form-label">Nome *</label>
              <input id="nome_contato" v-model="nome_contato" type="text" class="form-control" required maxlength="255">
              <div class="invalid-feedback">Preencha o nome!</div>
            </div>
          </div>
        </div>

        <template v-if="formularioCompleto">
          <div v-if="tipo_pessoa === 'F'" class="content-sector sector-verde-c p-3">
            <div class="form-group row">
              <div class="col-md-6">
                <!-- <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="cnpj_cpf" type="text" class="form-control"> -->

                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.cnpj_cpf.$invalid" required>
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>
              <div class="col-md-3">
                <label v-help-hint="'form-peselected_pessoassoa_numero_identidade'" for="numero_identidade" class="col-form-label">Identidade (RG)</label>
                <input id="numero_identidade" v-model="numero_identidade" type="text" class="form-control" maxlength="15">
              </div>
              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_orgao_emissor'" for="orgao_emissor" class="col-form-label">Órgão Emissor</label>
                <input id="orgao_emissor" v-model="orgao_emissor" type="text" class="form-control" placeholder="ex.: SSP" maxlength="10">
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_data_nascimento'" for="data_nascimento" class="col-form-label">Data de nascimento *</label>
                <g-datepicker :value="data_nascimento" :class="!isValid && $v.data_nascimento.$invalid ? 'invalid-input' : 'valid-input'" :selected="selectDataNascimento" maxlength="10" required />
                <div v-if="!isValid && $v.data_nascimento.$invalid" class="multiselect-invalid">
                  Informe a data de nascimento!
                </div>
              </div>

              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_sexo'" for="sexo" class="col-form-label">Sexo</label>
                <select id="sexo" v-model="sexo" class="custom-select form-control">
                  <option value="N">Não Informar</option>
                  <option value="M">Masculino</option>
                  <option value="F">Feminino</option>
                  <option value="O">Outro</option>
                </select>
              </div>
              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_estado_civil'" for="estado_civil" class="col-form-label">Estado Civil</label>
                <select id="estado_civil" v-model="estado_civil" class="custom-select form-control">
                  <option value="N">Selecione</option>
                  <option value="S">Solteiro(a)</option>
                  <option value="C">Casado(a)</option>
                  <option value="D">Divorciado</option>
                </select>
              </div>
            </div>
          </div>

          <div v-else class="content-sector sector-verde-c p-3">
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.cnpj_cpf.$invalid">
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_razao_social'" for="razao_social" class="col-form-label">Razão Social</label>
                <input id="razao_social" v-model="razao_social" type="text" class="form-control" maxlength="60">
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_nome_fantasia'" for="nome_fantasia" class="col-form-label">Nome Fantasia</label>
                <input id="nome_fantasia" v-model="nome_fantasia" type="text" class="form-control" maxlength="40">
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_estadual'" for="inscricao_estadual" class="col-form-label">Inscrição Estadual</label>
                <input id="inscricao_estadual" v-model="inscricao_estadual" type="text" class="form-control" maxlength="15">
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_inscricao_municipal'" for="inscricao_municipal" class="col-form-label">Inscrição Municipal</label>
                <input id="inscricao_municipal" v-model="inscricao_municipal" type="text" class="form-control" maxlength="15">
              </div>
            </div>
          </div>

          <div class="content-sector sector-roxo-c p-3">
            <h5 class="title-module mb-2">Informações Bancárias</h5>
            <div class="form-group row">
              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_banco'" for="banco" class="col-form-label">Banco</label>
                <select id="banco" v-model="bancoSelected" class="custom-select form-control">
                  <option value="">Nenhum</option>
                  <template v-for="(item, index) in listaBancos">
                    <option :key="index" :value="item.id">{{ item.descricao }}</option>
                  </template>
                </select>
                <!-- <div class="invalid-feedselected_pessoaback">Selecione um banco!</div> -->
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_agencia'" for="agencia" class="col-form-label">Agência</label>
                <input id="agencia" v-model="agencia" type="text" class="form-control" maxlength="10">
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_conta'" for="conta" class="col-form-label">Conta</label>
                <input id="conta" v-model="conta" type="text" class="form-control" maxlength="10">
              </div>
            </div>
          </div>

          <div class="content-sector sector-azul p-3">
            <h5 class="title-module mb-2">Contatos</h5>
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone</label>
                <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="telefone_preferencial" type="text" class="form-control">
                <div v-if="!isValid && $v.telefone_preferencial.$invalid" class="input-invalid">Preencha corretamente!</div>
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-email_preferencial'" for="email_preferencial" class="col-form-label">E-mail</label>
                <input id="email_preferencial" v-model="email_preferencial" type="email" class="form-control" maxlength="50">
                <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
              </div>
            </div>

            <div class="content-sector-extra p-3">
              <div class="d-flex align-items-center">
                <a v-b-toggle.contatos-extra class="btn-contatos-extra align-self-center">Contatos adicionais <font-awesome-icon icon="plus" /></a>
              </div>
              <b-collapse id="contatos-extra" class="mt-2">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-telefone_contato'" for="telefone_contato" class="col-form-label">Telefone (contato)</label>
                    <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_contato" v-model="telefone_contato" type="text" class="form-control">
                    <div v-if="!isValid && $v.telefone_contato.$invalid" class="input-invalid">Preencha corretamente!</div>
                  </div>
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-email_contato'" for="email_contato" class="col-form-label">E-mail (contato)</label>
                    <input id="email_contato" v-model="email_contato" type="email" class="form-control" maxlength="50">
                    <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-telefone_profissional'" for="telefone_profissional" class="col-form-label">Telefone (empresarial)</label>
                    <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_profissional" v-model="telefone_profissional" type="text" class="form-control">
                    <div v-if="!isValid && $v.telefone_profissional.$invalid" class="input-invalid">Preencha corretamente!</div>
                  </div>
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-email-profissional'" for="email_profissional" class="col-form-label">E-mail (empresarial)</label>
                    <input id="email_profissional" v-model="email_profissional" type="email" class="form-control" maxlength="50">
                    <div class="invalid-feedback">Preencha corretamente o e-mail!</div>
                  </div>
                </div>
              </b-collapse>
            </div>

          </div>

          <g-form-endereco :cep-data="cep_data" :callback-cep-data="setCepData" />

          <div class="content-sector sector-roxo-c p-3">
            <h5 class="title-module mb-2">Outros</h5>
            <div class="form-group row">

              <!--
              <div class="col-md-6">
                <label for="categoria" class="col-form-label">Categoria</label>
                <select id="categoria" v-model="categoria" class="custom-select form-control">
                  <option value>Nenhum</option>
                  <template v-for="(categoria, index) in listaCategorias">
                    <option :key="index" :value="categoria.id">{{ categoria.nome }}</option>
                  </template>
                </select>
                <div class="invalid-feedback">Selecione uma categoria!</div>
              </div>
              -->

              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-plano_conta'" for="plano_conta" class="col-form-label">Categoria de despesa padrão</label>
                <!-- <g-select
                  :value="plano_conta"
                  :select="setPlanoConta"
                  :options="planoContas"
                  label="descricao"
                  track-by="id" /> -->

                <g-treeselect
                  id="plano_conta"

                  :value="plano_conta"
                  :select="setPlanoConta"
                  :options="planoContas"
                  clearable
                />

                <span v-if="!isValid && plano_conta && plano_conta.filhos && plano_conta.filhos.length" class="multiselect-invalid">
                  Selecione uma categoria de último nível
                </span>
              </div>

            </div>
            <div class="form-group row">
              <div class="col-md-12">
                <label v-help-hint="'form-pessoa-obervacao'" for="observacao" class="col-form-label">Observações</label>
                <textarea id="observacao" v-model="observacao" class="form-control full-textarea mb-0" rows="6" maxlength="5000"></textarea>
                <span class="text-secondary">Limite de caracteres: {{ 5000 - (observacao || '').length }}</span>
              </div>
            </div>
          </div>

          <div v-if="isModal" class="px-3 py-2">
            <b-btn type="button" variant="link" @click="formularioCompleto = false">Mostrar formulário simples</b-btn>
          </div>
        </template>

        <template v-else>
          <div class="animated fadeIn p-3">
            <div class="row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.cnpj_cpf.$invalid">
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone</label>
                <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="telefone_preferencial" class="form-control">
                <div v-if="!isValid && $v.telefone_preferencial.$invalid" class="input-invalid">Preencha corretamente!</div>
              </div>

              <!--
              <div class="col-md-6">
                <label for="categoria" class="col-form-label">Categoria</label>
                <select id="categoria" v-model="categoria" class="custom-select form-control">
                  <option value>Nenhum</option>
                  <template v-for="(categoria, index) in listaCategorias">
                    <option :key="index" :value="categoria.id">{{ categoria.nome }}</option>
                  </template>
                </select>
                <div class="invalid-feedback">Selecione uma categoria!</div>
              </div>
              -->
            </div>
            <div class="form-group row">
              <div v-if="isModal === false || (isModal === true && selected_pessoa === 'F')" class="col-md-6">
                <label v-help-hint="'form-pessoa_data_nascimento'" for="data_nascimento" class="col-form-label">Data de nascimento *</label>
                <g-datepicker :value="data_nascimento" :class="!isValid && $v.data_nascimento.$invalid ? 'invalid-input' : 'valid-input'" :selected="selectDataNascimento" maxlength="10" required />
                <div v-if="!isValid && $v.data_nascimento.$invalid" class="multiselect-invalid">
                  Informe a data de nascimento!
                </div>
              </div>
            </div>
          </div>

          <div class="px-3 py-2">
            <b-btn type="button" variant="link" @click="formularioCompleto = true">Mostrar formulário completo</b-btn>
          </div>
        </template>
      </div>

      <div :class="{'pb-3': !isModal}">
        <button :disabled="salvando" type="submit" class="btn btn-verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</button>
        <b-btn type="button" variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required, email, minLength, requiredIf} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import {stringToISODate, dateToString} from '../../utils/date'
import {isCpfValido, isCnpjValido} from '../../utils/format'

const tipoDocumento = (value, vm) => {
  if (typeof value === 'string' && value.length > 0) {
    if (vm.tipo_pessoa === 'F') {
      return isCpfValido(value)
    }
    return isCnpjValido(value)
  }

  return true
}

export default {
  name: 'FormularioPessoa',

  props: {
    isModal: {
      type: Boolean,
      required: false,
      default: false
    },

    loadCategories: {
      type: Boolean,
      required: false,
      default: true
    },

    tipoPessoaFixo: {
      type: String,
      required: false,
      default: null
    }

  },

  data () {
    return {
      loadCount: 0,
      isValid: true,
      errorMsg: '',
      iniciado: false,
      isEdit: false,
      label: 'CPF',
      cpfInvalido: false,
      tipo_pessoa: '',
      nome_contato: '',
      cnpj_cpf: '',
      numero_identidade: '',
      orgao_emissor: '',
      sexo: '',
      data_nascimento: '',
      estado_civil: 'N',
      razao_social: '',
      nome_fantasia: '',
      inscricao_estadual: '',
      inscricao_municipal: '',
      plano_conta: null,
      email_preferencial: '',
      email_contato: '',
      email_profissional: '',
      telefone_preferencial: '',
      telefone_contato: '',
      telefone_profissional: '',
      home_page: '',
      cep_data: {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      },
      bancoSelected: null,
      agencia: null,
      conta: null,
      observacao: '',
      formularioCompleto: !this.isModal,
      salvando: false,
      numero_identidade_token: {
        X: {pattern: /[.-\da-zA-Z]/}
      },

      selected_pessoa: 'F'

    }
  },

  validations: {
    nome_contato: {required},
    email_preferencial: {email},
    email_contato: {email},
    email_profissional: {email},
    data_nascimento: {required: requiredIf(function () {
      return this.tipo_pessoa === 'F'
    })
    },
    telefone_preferencial: {minLength: minLength(14)},
    telefone_contato: {minLength: minLength(14)},
    telefone_profissional: {minLength: minLength(14)}
  },


  computed: {
    ...mapState('pessoas', ['listaPessoa', 'listaPessoasPais', 'objPessoa', 'estaCarregando']),
    ...mapState('categorias', ['listaCategorias']),
    ...mapState('planoConta', {listaPlanoConta: 'arvoreItens'}),
    ...mapState('banco', {listaBancos: 'lista'}),

    planoContas: {
      get () {
        return this.listaPlanoConta.filter(item => item.tipo_movimento_nota === 'E')
      }
    }
  },

  watch: {
    bancoSelected (bancoId) {
      this.objPessoa.banco = bancoId
    },

    objPessoa (value) {
      if (this.tipoPessoaFixo !== null && !value.id) {
        this.tipo_pessoa = this.tipoPessoaFixo
      } else {
        this.tipo_pessoa = value.tipo_pessoa || 'F'
      }

      this.nome_contato = value.nome_contato
      this.cnpj_cpf = value.cnpj_cpf
      this.numero_identidade = value.numero_identidade
      this.orgao_emissor = value.orgao_emissor
      this.sexo = value.sexo
      this.estado_civil = value.estado_civil || 'N'
      this.razao_social = value.razao_social
      this.nome_fantasia = value.nome_fantasia
      this.inscricao_estadual = value.inscricao_estadual
      this.inscricao_municipal = value.inscricao_municipal

      if (value.banco !== undefined) {
        this.bancoSelected = value.banco.id
      } else {
        this.bancoSelected = value.banco
      }

      this.agencia = value.agencia
      this.conta = value.conta
      this.email_preferencial = value.email_preferencial
      this.email_contato = value.email_contato
      this.email_profissional = value.email_profissional
      this.telefone_preferencial = value.telefone_preferencial
      this.telefone_contato = value.telefone_contato
      this.telefone_profissional = value.telefone_profissional
      this.home_page = value.home_page
      this.cep_data = {
        cep_endereco: value.cep_endereco,
        endereco: value.endereco,
        numero_endereco: value.numero_endereco,
        complemento_endereco: value.complemento_endereco,
        bairro_endereco: value.bairro_endereco,
        estado: value.estado,
        cidade: value.cidade
      }
      this.observacao = value.observacao
      this.data_nascimento = value.data_nascimento ? dateToString(new Date(value.data_nascimento)) : ''
      this.plano_conta = value.plano_conta
    },

    tipo_pessoa (value) {
      this.setTipoPessoa(value)
    },

    numero_identidade (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.setNumeroIdentidade(value)
      this.isIniciado(value)
    },

    orgao_emissor (value) {
      this.setOrgaoEmissor(value)
      this.isIniciado(value)
    },

    sexo (value) {
      this.setSexo(value)
      this.isIniciado(value)
    },

    estado_civil (value) {
      this.setEstadoCivil(value)
      this.isIniciado(value)
    },

    razao_social (value) {
      this.setRazaoSocial(value)
      this.isIniciado(value)
    },

    nome_fantasia (value) {
      this.setNomeFantasia(value)
      this.isIniciado(value)
    },

    inscricao_estadual (value) {
      this.setInscricaoEstadual(value)
      this.isIniciado(value)
    },

    inscricao_municipal (value) {
      this.setInscricaoMunicipal(value)
      this.isIniciado(value)
    },

    banco (value) {
      this.setBanco(value)
      this.isIniciado(value)
    },

    agencia (value) {
      this.setAgencia(value)
      this.isIniciado(value)
    },

    conta (value) {
      this.setConta(value)
      this.isIniciado(value)
    },

    nome_contato (value) {
      this.setNomeContato(value)
      this.isIniciado(value)
    },

    cnpj_cpf (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.setCnpjCpf(value)
      this.isIniciado(value)
    },

    email_preferencial (value) {
      this.setEmailPreferencial(value)
    },

    email_contato (value) {
      this.setEmailContato(value)
    },

    email_profissional (value) {
      this.setEmailProfissional(value)
    },

    home_page (value) {
      this.SET_HOME_PAGE(value)
    },

    telefone_preferencial (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.setTelefonePreferencial(value)
    },

    telefone_contato (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.setTelefoneContato(value)
    },

    telefone_profissional (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.setTelefoneProfissional(value)
    },

    observacao (value) {
      this.SET_OBSERVACAO(value)
    }
  },
  mounted () {
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('planoConta/listar').then(this.countCarregamento)

    this.$store.commit('banco/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('banco/listar').then(this.countCarregamento)

    this.limparPessoa()

    if (this.$route.params.id && this.$route.name === 'atualizar-pessoa') {
      this.isEdit = true
      this.setPessoaSelecionada(this.$route.params.id)
      this.getPessoa()
        .then((response) => {
          this.tipo_pessoa = response.tipo_pessoa
          this.selected_pessoa = response.tipo_pessoa
        })
        .catch((error) => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Pessoa não encontrada'
          })

          this.voltar()
        })
    } else {
      this.iniciado = false
      this.tipo_pessoa = 'F'
    }
  },
  methods: {
    ...mapActions('pessoas', ['getListaPessoas', 'getPessoa', 'criarPessoa', 'buscarPessoasPais', 'atualizarPessoa']),
    ...mapMutations('pessoas', ['setPessoa', 'setTipoPessoa', 'setNumeroIdentidade', 'setOrgaoEmissor', 'setSexo', 'setEstadoCivil',
      'setRazaoSocial', 'setNomeFantasia', 'setInscricaoEstadual', 'setInscricaoMunicipal', 'setNomeContato',
      'setCnpjCpf', 'setPessoaSelecionada', 'setEmailPreferencial', 'setEmailContato', 'setEmailProfissional', 'setTelefonePreferencial',
      'setDataNascimento', 'setTelefoneContato', 'setTelefoneProfissional', 'setConta', 'setAgencia', 'setBanco', 'SET_HOME_PAGE', 'SET_CEP_ENDERECO', 'setEndereco',
      'setNumeroEndereco', 'SET_COMPLEMENTO_ENDERECO', 'SET_BAIRRO_ENDERECO', 'SET_ESTADO', 'SET_CIDADE', 'SET_CATEGORIA',
      'SET_TIPO', 'SET_OBSERVACAO', 'limparPessoa', 'SET_ESTA_CARREGANDO']),

    ...mapActions('categorias', ['getListaCategorias']),

    isIniciado (value) {
      this.iniciado = value !== '' && value !== null
    },

    selectDataNascimento (value) {
      this.data_nascimento = value
      this.setDataNascimento(stringToISODate(value, true))
    },

    setPlanoConta (value) {
      this.plano_conta = value
      this.$store.commit('pessoas/setPlanoConta', value.id)
    },

    voltar (pessoaID = null) {
      this.salvando = false
      this.setPessoaSelecionada(null)
      this.limparPessoa()

      if (this.isModal) {
        if (pessoaID) {
          return this.$emit('resolve', Number(pessoaID))
        }

        return this.$emit('reject')
      }

      this.$router.push('/cadastros/pessoa')
    },

    verificarTipoPessoa (tipo) {
      this.tipo_pessoa = tipo
      const selected = this.selected_pessoa
      if (tipo != this.selected_pessoa) {
        if (this.iniciado && this.isModal === false) {
          EventBus.$emit('chamarModal', {
            resolve: success => {
              this.limparTipoPessoa(tipo)
            },
            reject: () => {
              this.selected_pessoa = selected
            }
          }, 'Se alterar o tipo de pessoa perderá todos os dados preenchidos. Deseja continuar?')
        } else {
          this.limparTipoPessoa(tipo)
        }
      }
    },

    limparTipoPessoa (tipo) {
      this.nome_contato = ''
      this.cnpj_cpf = ''
      this.data_nascimento = ''
      if (tipo === 'F') {
        this.objPessoa.tipo_pessoa = 'F'

        this.tipo_pessoa = 'F'
        this.selected_pessoa = 'F'

        this.razao_social = ''
        this.nome_fantasia = ''
        this.inscricao_estadual = ''
        this.inscricao_municipal = ''
      } else {
        this.objPessoa.tipo_pessoa = 'J'

        this.tipo_pessoa = 'J'
        this.selected_pessoa = 'J'

        this.numero_identidade = ''
        this.orgao_emissor = ''
        this.sexo = ''
        this.estado_civil = ''
      }
    },

    setCepData (value) {
      this.cep_data = value
    },

    atualizaDadosCepPessoa () {
      this.SET_BAIRRO_ENDERECO(this.cep_data.bairro_endereco)
      this.SET_COMPLEMENTO_ENDERECO(this.cep_data.complemento_endereco)
      this.SET_CEP_ENDERECO(this.cep_data.cep_endereco)
      this.setNumeroEndereco(this.cep_data.numero_endereco)
      this.setEndereco(this.cep_data.endereco)
      if (this.cep_data.estado !== undefined) {
        this.SET_ESTADO(this.cep_data.estado.id)
      }
      if (this.cep_data.cidade !== undefined) {
        this.SET_CIDADE(this.cep_data.cidade.id)
      }
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.salvando = true

      this.atualizaDadosCepPessoa()
      if (this.isEdit) {
        this.atualizarPessoa()
          .then(() => {
            this.voltar()
          })
          .catch(() => {
            this.salvando = false
          })
      } else {
        this.criarPessoa()
          .then((retorno) => {
            this.voltar(retorno.pessoa)
          })
          .catch(() => {
            this.salvando = false
          })
      }
    },

    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    }
  }
}
</script>
