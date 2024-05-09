<template>
  <b-modal id="responsavel" ref="responsavel" v-model="responsavelModal" size="lg" centered no-close-on-backdrop hide-header hide-footer>
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="body-sector">
        <div v-if="responsavel && responsavel.id" class="head-content-sector p-3">
          <span class="font-weight-bold text-muted">Pessoa {{ responsavel.tipo_pessoa === 'F' ? 'Física' : 'Jurídica' }}</span>
        </div>

        <div class="head-content-sector p-3">
          <b-form-radio-group id="radio-group-2" v-model="selected_pessoa" name="radio-sub-component">
            <b-form-radio name="some-radios" value="F" @change="verificarTipoPessoa('F')">Física</b-form-radio>
            <b-form-radio name="some-radios" value="J" @change="verificarTipoPessoa('J')">Jurídica</b-form-radio>
          </b-form-radio-group>
        </div>

        <div class="p-3">
          <div class="row">
            <div class="col-md-12">
              <label v-help-hint="'form-pessoa_nome_contato'" for="nome_contato" class="col-form-label">Nome *</label>
              <input id="nome_contato" v-model="responsavel.nome_contato" type="text" class="form-control" required maxlength="150">
              <div class="invalid-feedback">Preencha o nome!</div>
            </div>
          </div>
        </div>

        <!-- FORM COMPLETO -->
        <div v-show="formularioCompleto" class="animated fadeIn">
          <div v-if="responsavel.tipo_pessoa === 'F'" class="animated fadeIn p-3">
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = responsavel.tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="responsavel.tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="responsavel.cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.responsavel.cnpj_cpf.$invalid">
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>
              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_numero_identidade'" for="numero_identidade" class="col-form-label">Identidade (RG)</label>
                <the-mask id="form-pessoa_identidade" :tokens="numero_identidade_token" v-model="responsavel.numero_identidade" mask="XXXXXXXXXXXXXXX" type="text" class="form-control" maxlength="15"/>
              </div>
              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_orgao_emissor'" for="orgao_emissor" class="col-form-label">Órgão Emissor</label>
                <input id="orgao_emissor" v-model="responsavel.orgao_emissor" name="orgao_emissor" type="text" class="form-control" placeholder="ex.: SSP" maxlength="10">
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_data_nascimento'" for="responsavel_data_nascimento" class="col-form-label">Data de nascimento *</label>
                <g-datepicker :value="responsavel_data_nascimento" :class="!isValid && $v.responsavel.data_nascimento.$invalid ? 'invalid-input' : 'valid-input'" :selected="selectResponsavelDataNascimento" maxlength="10" required />
                <div v-if="!isValid && $v.responsavel.data_nascimento.$invalid" class="multiselect-invalid">
                  Informe a data de nascimento!
                </div>
              </div>

              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_sexo'" for="sexo" class="col-form-label">Sexo</label>
                <select id="sexo" v-model="responsavel.sexo" class="custom-select form-control">
                  <option value="N">Não Informar</option>
                  <option value="M">Masculino</option>
                  <option value="F">Feminino</option>
                  <option value="O">Outro</option>
                </select>
              </div>

              <div class="col-md-3">
                <label v-help-hint="'form-pessoa_estado_civil'" for="estado_civil" class="col-form-label">Estado Civil</label>
                <select id="estado_civil" v-model="responsavel.estado_civil" class="custom-select form-control">
                  <option value="N">Selecione</option>
                  <option value="S">Solteiro(a)</option>
                  <option value="C">Casado(a)</option>
                  <option value="D">Divorciado</option>
                </select>
              </div>
            </div>

          </div>

          <div v-else class="animated fadeIn p-3 form-content">
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = responsavel.tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="responsavel.tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="responsavel.cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.responsavel.cnpj_cpf.$invalid">
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_razao_social'" for="razao_social" class="col-form-label">Razão Social</label>
                <input id="razao_social" v-model="responsavel.razao_social" type="text" class="form-control" maxlength="60">
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_nome_fantasia'" for="nome_fantasia" class="col-form-label">Nome Fantasia</label>
                <input id="nome_fantasia" v-model="responsavel.nome_fantasia" type="text" class="form-control" maxlength="40">
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_estadual'" for="inscricao_estadual" class="col-form-label">Inscrição Estadual</label>
                <input id="inscricao_estadual" v-model="responsavel.inscricao_estadual" type="text" class="form-control" maxlength="15">
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa_inscricao_municipal'" for="inscricao_municipal" class="col-form-label">Inscrição Municipal</label>
                <input id="inscricao_municipal" v-model="responsavel.inscricao_municipal" type="text" class="form-control" maxlength="15">
              </div>
            </div>
          </div>

          <div class="content-sector sector-roxo-c p-3">
            <h5 class="title-module mb-2">Informações Bancárias</h5>
            <div class="form-group row">
              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_banco'" for="banco" class="col-form-label">Banco</label>
                <select id="banco" v-model="responsavel.banco" class="custom-select form-control">
                  <option value="">Nenhum</option>
                  <template v-for="(item, index) in listaBancos">
                    <option :key="index" :value="item.id">{{ item.descricao }}</option>
                  </template>
                </select>
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_agencia'" for="agencia" class="col-form-label">Agência</label>
                <input id="agencia" v-model="responsavel.agencia" type="text" class="form-control" maxlength="10">
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-pessoa_conta'" for="conta" class="col-form-label">Conta</label>
                <input id="conta" v-model="responsavel.conta" type="text" class="form-control" maxlength="10">
              </div>
            </div>
          </div>

          <div class="content-sector sector-azul p-3">
            <h5 class="title-module mb-2">Contatos</h5>
            <div class="form-group row">
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone</label>
                <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="responsavel.telefone_preferencial" type="text" class="form-control">
                <div class="invalid-feedback">Informe um telefone para contato!</div>
              </div>
              <div class="col-md-6">
                <label v-help-hint="'form-pessoa-email_preferencial'" for="email_preferencial" class="col-form-label">E-mail</label>
                <input id="email_preferencial" v-model="responsavel.email_preferencial" :class="{ 'is-invalid' : email_preferencialInvalido }" type="email" class="form-control" maxlength="50" @blur="email_preferencialInvalido = $v.responsavel.email_preferencial.$invalid">
                <div v-if="email_preferencialInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
              </div>
            </div>

            <div class="content-sector-extra p-3">
              <div class="d-flex align-items-center">
                <a v-b-toggle.contatos-extra-responsavel class="btn-contatos-extra align-self-center">Contatos adicionais <font-awesome-icon icon="plus" /></a>
              </div>
              <b-collapse id="contatos-extra-responsavel" class="mt-2">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-telefone_contato'" for="telefone_contato" class="col-form-label">Telefone (contato)</label>
                    <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_contato" v-model="responsavel.telefone_contato" type="text" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-email_contato'" for="email_contato" class="col-form-label">E-mail (contato)</label>
                    <input id="email_contato" v-model="responsavel.email_contato" :class="{ 'not-validated invalid-input' : email_contatoInvalido }" type="email" class="form-control" maxlength="50" @blur="email_contatoInvalido = $v.responsavel.email_contato.$invalid">
                    <div v-if="email_contatoInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-telefone_profissional'" for="telefone_profissional" class="col-form-label">Telefone (empresarial)</label>
                    <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_profissional" v-model="responsavel.telefone_profissional" type="text" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label v-help-hint="'form-pessoa-email-profissional'" for="email_profissional" class="col-form-label">E-mail (empresarial)</label>
                    <input id="email_profissional" v-model="responsavel.email_profissional" :class="{ 'not-validated invalid-input' : email_profissionalInvalido }" type="email" class="form-control" maxlength="50" @blur="email_profissionalInvalido = $v.responsavel.email_profissional.$invalid">
                    <div v-if="email_profissionalInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
                  </div>
                </div>
              </b-collapse>
            </div>

          </div>

          <g-form-endereco id="form_responsavel-endereco" ref="refFormEndereco" :cep-data="cep_data" :callback-cep-data="setCepData" :permite-usar-endereco-aluno="bPermiteUsarEnderecoAluno" @setEnderecoResponsavel="alternaLimpaEndereco"/>

          <div class="content-sector sector-roxo-c p-3">
            <h5 class="title-module mb-2">Outros</h5>
            <g-treeselect
              id="responsavel_plano_conta"
              :value="responsavel_plano_conta"
              :select="setResponsavelPlanoConta"
              :options="planoContas"
              clearable
            />
            <span v-if="!isValid && responsavel_plano_conta && responsavel_plano_conta.filhos && responsavel_plano_conta.filhos.length" class="multiselect-invalid">
              Selecione uma categoria de último nível
            </span>
          </div>

          <div class="form-group row">
            <div class="col-md-12">
              <label v-help-hint="'form-pessoa-obervacao'" for="observacao" class="col-form-label">Observações</label>
              <textarea id="observacao" v-model="responsavel.observacao" class="form-control full-textarea mb-0" rows="6" maxlength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - (responsavel.observacao || '').length }}</span>
            </div>
          </div>

          <div v-if="isModal" class="px-3 py-2">
            <b-btn type="button" variant="link" @click="formularioCompleto = false">Mostrar formulário simples</b-btn>
          </div>
        </div>

        <!-- FORM MINIFICADO -->
        <div v-show="!formularioCompleto" class="animated fadeIn">
          <div class="p-3">
            <div class="row">
              <div :class="{'col-md-6' : responsavel.tipo_pessoa === 'J'}" class="col-md-4">
                <label v-help-hint="'form-pessoa_cnpj_cpf'" for="cnpj_cpf" class="col-form-label">{{ label = responsavel.tipo_pessoa === 'F' ? 'CPF' : 'CNPJ' }}</label>
                <input v-mask="responsavel.tipo_pessoa === 'F' ? '###.###.###-##' : '##.###.###/####-##'" id="cnpj_cpf" v-model="responsavel.cnpj_cpf" :class="{ 'is-invalid' : cpfInvalido }" type="text" class="form-control" @blur="cpfInvalido = $v.responsavel.cnpj_cpf.$invalid">
                <div v-if="cpfInvalido" class="input-invalid">{{ label }} informado é inválido!</div>
              </div>

              <div v-show="responsavel.tipo_pessoa === 'F'" class="col-md-4">
                <label v-help-hint="'form-pessoa_data_nascimento'" for="responsavel_data_nascimento" class="col-form-label">Data de nascimento *</label>
                <g-datepicker :value="responsavel_data_nascimento" :class="!isValid && $v.responsavel.data_nascimento.$invalid ? 'invalid-input' : 'valid-input'" :selected="selectResponsavelDataNascimento" maxlength="10" required />
                <div v-if="!isValid && $v.responsavel.data_nascimento.$invalid" class="multiselect-invalid">
                  Informe a data de nascimento!
                </div>
              </div>

              <div :class="{'col-md-6' : responsavel.tipo_pessoa === 'J'}" class="col-md-4">
                <label v-help-hint="'form-pessoa-telefone_preferencial'" for="telefone_preferencial" class="col-form-label">Telefone</label>
                <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefone_preferencial" v-model="responsavel.telefone_preferencial" type="text" class="form-control">
                <div class="invalid-feedback">Informe um telefone para contato!</div>
              </div>

            </div>
          </div>

          <div class="px-3 py-2">
            <b-btn type="button" variant="link" @click="formularioCompleto = true">Mostrar formulário completo</b-btn>
          </div>
        </div>
      </div>

      <div :class="{'pb-3': !isModal}">
        <button :disabled="salvando" type="submit" class="btn btn-verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</button>
        <b-btn type="button" variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </b-modal>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import { required, email, minLength } from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import {stringToISODate} from '../../utils/date'
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

const valideDocumento = (value, vm) => {
  if (typeof value === 'string' && value.length > 0) {
    if (vm.tipo_pessoa === 'F') {
      return true
    }
    return false
  }

  return true
}

export default {
  name: 'FormularioResponsavel',

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
    }
  },

  data () {
    return {
      responsavelModal: false,
      bPermiteUsarEnderecoAluno: false,
      isValid: true,
      errorMsg: '',
      iniciado: false,
      isEdit: false,
      label: 'CPF',
      cpfInvalido: false,
      email_preferencialInvalido: false,
      email_contatoInvalido: false,
      email_profissionalInvalido: false,
      dadosEnderecoAlunoTemp: {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      },
      cep_data: {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      },
      responsavel_plano_conta: null,
      responsavel_data_nascimento: '',
      bancoSelected: null,
      formularioCompleto: !this.isModal,
      salvando: false,
      numero_identidade_token: {
        X: {pattern: /[.-\da-zA-Z]/}
      },

      selected_pessoa: 'F'

    }
  },

  validations: {
    responsavel: {
      nome_contato: {required},
      email_preferencial: {email},
      email_contato: {email},
      email_profissional: {email},
      telefone_preferencial: {minLength: minLength(14)},
      telefone_contato: {minLength: minLength(14)},
      telefone_profissional: {minLength: minLength(14)},
      data_nascimento: {valideDocumento},
      cnpj_cpf: {
        tipoDocumento
      }
    }
  },

  computed: {
    ...mapState('pessoas', ['listaPessoa', 'responsavel', 'estaCarregando']),
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

    /* bancoSelected (value) {
      this.iniciado = value !== ''
      this.responsavel.banco = value
    } */

  },
  mounted () {
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('planoConta/listar')

    this.$store.commit('banco/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('banco/listar')

    this.$store.commit('pessoas/LIMPAR_RESPONSAVEL')

    this.responsavel.tipo_pessoa = 'F'
  },
  methods: {
    ...mapActions('pessoas', ['criarResponsavel']),

    selectResponsavelDataNascimento (value) {
      this.responsavel_data_nascimento = value
      this.responsavel.data_nascimento = stringToISODate(value, true)
    },

    setResponsavelPlanoConta (value) {
      this.responsavel_plano_conta = value
      this.responsavel.plano_conta = value.id
    },

    alternaLimpaEndereco (bUsarEnderecoAluno) {
      const defaultEndereco = {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      }

      if (bUsarEnderecoAluno === true) {
        this.cep_data = this.dadosEnderecoAlunoTemp
      } else {
        this.cep_data = defaultEndereco
      }
    },

    setCnpjCpf (dadosCnpjCpf) {
      this.responsavel.cnpj_cpf = dadosCnpjCpf
    },

    setEnderecoAlunoTemp (dadosEnderecoAlunoTemp) {
      this.dadosEnderecoAlunoTemp = dadosEnderecoAlunoTemp
    },

    voltar (pessoaID = null) {
      this.isValid = true
      this.salvando = false

      this.limparDados()

      this.formularioCompleto = false

      if (this.isModal) {
        if (pessoaID) {
          return this.$emit('resolve', Number(pessoaID))
        }

        if (this.bPermiteUsarEnderecoAluno) {
          this.$refs.refFormEndereco.bloquearUtilizandoAluno = false
        }

        return this.$emit('reject')
      }
    },

    verificarTipoPessoa (tipo) {
      const selected = this.selected_pessoa

      Object.keys(this.responsavel).find(key => {
        const value = this.responsavel[key]
        if (key !== 'tipo_pessoa' && value !== '' && value !== null && value.length > 0) {
          this.iniciado = true
        }
      })

      if (tipo !== this.responsavel.tipo_pessoa) {
        if (this.iniciado) {
          EventBus.$emit('chamarModal', {
            resolve: success => {
              this.limparTipoPessoa(tipo)
            },
            reject: () => {
              this.selected_pessoa = selected
              this.iniciado = false
              this.$refs.responsavel.show()
            }
          }, 'Se alterar o tipo de pessoa perderá todos os dados preenchidos. Deseja continuar?')
        } else {
          this.limparTipoPessoa(tipo)
        }
      }
    },

    limparTipoPessoa (tipo) {
      this.limparDados()

      this.responsavel.nome_contato = ''
      this.responsavel.cnpj_cpf = ''
      if (tipo === 'F') {
        this.responsavel.tipo_pessoa = 'F'
        this.selected_pessoa = 'F'
        this.responsavel.razao_social = ''
        this.responsavel.nome_fantasia = ''
        this.responsavel.inscricao_estadual = ''
        this.responsavel.inscricao_municipal = ''
      } else {
        this.responsavel.tipo_pessoa = 'J'
        this.selected_pessoa = 'J'
        this.responsavel.numero_identidade = ''
        this.responsavel.orgao_emissor = ''
        this.responsavel.sexo = ''
        this.responsavel.estado_civil = ''
      }
      this.$refs.responsavel.show()
    },

    limparDados () {
      this.selected_pessoa = 'F'
      this.responsavel_data_nascimento = ''
      this.responsavel_plano_conta = null
      this.$store.commit('pessoas/LIMPAR_RESPONSAVEL')
      // limpar flag
      this.$refs.refFormEndereco.bloquearUtilizandoAluno = false

      this.cep_data = {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      }
      this.iniciado = false
    },

    setCepData (value) {
      this.cep_data = value
    },

    atualizaDadosCepPessoa () {
      this.responsavel.bairro_endereco = this.cep_data.bairro_endereco
      this.responsavel.complemento_endereco = this.cep_data.complemento_endereco
      this.responsavel.cep_endereco = this.cep_data.cep_endereco
      this.responsavel.numero_endereco = this.cep_data.numero_endereco
      this.responsavel.endereco = this.cep_data.endereco
      if (this.cep_data.estado !== undefined) {
        this.responsavel.estado = this.cep_data.estado.id
      }
      if (this.cep_data.cidade !== undefined) {
        this.responsavel.cidade = this.cep_data.cidade.id
      }
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.salvando = true

      this.responsavel.cnpj_cpf = this.responsavel.cnpj_cpf !== null ? this.responsavel.cnpj_cpf.replace(/\D/g, '') : ''

      this.atualizaDadosCepPessoa()
      this.criarResponsavel()
        .then((retorno) => {
          console.log('retorno', retorno)
          this.voltar(retorno.pessoa)
        })
        .catch(() => {
          this.salvando = false
        })
    }

  }
}
</script>
