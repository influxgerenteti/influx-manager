<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate>
      <div v-if="carregando" class="form-loading screen-load">
        <load-placeholder :loading="carregando" />
      </div>

      <div class="content-sector sector-primary p-3">
        <div class="form-group row">
          <div class="col-md-12">
            <label for="nome_fantasia" class="col-form-label">Nome Fantasia *</label>
            <input id="nome_fantasia" v-model="nome_fantasia" type="text" class="form-control" disabled>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <label for="razao_social" class="col-form-label">Razão social *</label>
            <input id="razao_social" v-model="razao_social" :class="!isValid && !razao_social ? 'invalid-input' : 'valid-input'" type="text" class="form-control">
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="cnpj" class="col-form-label">CNPJ *</label>
            <input v-mask="'##.###.###/####-##'" id="cnpj" v-model="cnpj_cpf" :class="!isValid && (!cnpj_cpf || cnpj_cpf.length < 18) ? 'invalid-input' : 'valid-input'" type="text" class="form-control">
          </div>
          <div class="col-md-3">
            <label for="inscricao_municipal" class="col-form-label">Inscrição municipal</label>
            <input id="inscricao_municipal" v-model="itemSelecionado.inscricao_municipal" type="text" class="form-control">
          </div>
          <div class="col-md-3">
            <label for="inscricao_estadual" class="col-form-label">Inscrição estadual</label>
            <input id="inscricao_estadual" v-model="itemSelecionado.inscricao_estadual" type="text" class="form-control">
          </div>
        </div>

        <g-form-endereco id="form_convenio" :cep-data="cep_data" :callback-cep-data="setCepData" :campos-obrigatorios="camposEnderecoObrigatorios" :campos-invalidos="camposEnderecoInvalidos"/>

        <div class="form-group row">
          <div class="col-md-6">
            <b-form-group label="Abrangência *">
              <b-form-radio-group
                id="agrangencia_radio"
                v-model="itemSelecionado.abrangencia_nacional"
                :options="abrangenciaOpcoes"
                name="radio-options"
                required
              />
            </b-form-group>
          </div>
          <div class="col-md-6">
            <b-form-group label="Beneficiário *">
              <b-form-checkbox v-model="itemSelecionado.beneficiario_colaboradores" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button">Colaboradores</b-form-checkbox>
              <b-form-checkbox v-model="itemSelecionado.beneficiario_dependentes" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button" >Dependentes</b-form-checkbox>
              <b-form-checkbox v-model="itemSelecionado.beneficiario_alunos" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button" >Alunos</b-form-checkbox>
              <b-form-checkbox v-model="itemSelecionado.beneficiario_associados" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button" >Associados</b-form-checkbox>
              <b-form-checkbox v-model="itemSelecionado.beneficiario_estagiarios" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button" >Estagiários</b-form-checkbox>
              <b-form-checkbox v-model="itemSelecionado.beneficiario_terceiros" :required="beneficiarioIsRequired()" :unchecked-value="null" name="check-button" >Terceiros</b-form-checkbox>
            </b-form-group>
          </div>
        </div>

        <div class="form-group row">
          <div class="d-flex">
            <div class="m-2">
              <input id="anexar_contrato" v-model="nomeArquivoContrato" type="text" class="form-control" placeholder="Buscar um arquivo" disabled>
            </div>
            <div class="m-2">
              <b-btn type="button" variant="verde" @click="uploadFile()">
                <font-awesome-icon icon="upload"/>
                Buscar
              </b-btn>

              <!-- <b-btn type="button" variant="azul" @click="download()">Download</b-btn> -->
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="situacao" class="col-form-label">Situação *</label>
            <template v-if="itemSelecionado.situacao === 'ATI'">
              <g-select id="segmento"
                        :select="setSituacao"
                        :value="situacao"
                        :options="listaNegociacaoParceriaWorkflow"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </template>
            <template v-else>
              <input id="situacao" v-model="situacao" type="text" class="form-control" disabled readonly="readonly">
            </template>
          </div>
        </div>
      </div>
      <div class="form-group pt-2">
        <template v-if="objFranqueada.franqueadora">
          <template v-if="itemSelecionado.situacao ==='ATI'">
            <b-btn :disabled="carregando" variant="roxo" @click="fecharConvenio({observacao: 'Atualizado'})">{{ carregando ? 'Salvando...': 'Salvar' }}</b-btn>
          </template>
          <template v-else>
            <b-btn :disabled="carregando" variant="roxo" @click="toApprove({situacao: 'ATI', fechar_convenio: !itemSelecionado.situacao === 'ATI'})">{{ carregando ? 'Salvando...': 'Aprovar' }}</b-btn>
            <b-btn :disabled="carregando" variant="roxo" @click="notApprove({situacao: 'PNR', fechar_convenio: false, observacao: 'Parceiria não foi realizada' })">{{ carregando ? 'Salvando...': 'Não aprovar' }}</b-btn>
          </template>
        </template>
        <template v-else>
          <template v-if="itemSelecionado.situacao ==='PV'">
            <b-btn :disabled="carregando" variant="verde" @click="fecharConvenio({fechar_convenio: true, observacao: 'Atualizado'})">{{ carregando ? 'Salvando...' : 'Salvar' }}</b-btn>
          </template>
          <template v-else>
            <b-btn :disabled="carregando" variant="roxo" @click="fecharConvenio({fechar_convenio: true, observacao: 'Enviado para aprovação'})">{{ carregando ? 'Salvando...' : 'Fechar convênio' }}</b-btn>
          </template>
          <template v-if="podeInativarConvenio(itemSelecionado)">
            <b-btn :disabled="carregando" variant="vermelho" @click.prevent="inativarConvenio(itemSelecionado)">{{ carregando ? 'Salvando...' : 'Inativar convênio' }}</b-btn>
          </template>
        </template>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>
<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {requiredIf, required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'

export default {
  name: 'FormularioIniciarConvenio',
  data () {
    return {
      isValid: true,
      arquivoContrato: null,
      nomeArquivoContrato: '',
      situacao: null,
      abrangenciaOpcoes: [
        {text: 'Nacional', value: true},
        {text: 'Estadual', value: false}
      ],
      cep_data: {
        cep_endereco: '',
        endereco: '',
        numero_endereco: '',
        complemento_endereco: '',
        bairro_endereco: '',
        estado: '',
        cidade: ''
      },
      nome_fantasia: '',
      razao_social: '',
      cnpj_cpf: '',
      camposEnderecoObrigatorios: {
        endereco: true
      }
    }
  },
  computed: {
    ...mapState('convenio', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('pessoas', {pessoaSeleciona: 'objPessoa', estaCarregandoPessoa: 'estaCarregando'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),
    ...mapState('negociacaoParceriaWorkflow', {listaNegociacaoParceriaWorkflow: 'lista'}),
    ...mapState('franqueadas', {objFranqueada: 'objFranqueada', estaCarregandoFranqueada: 'estaCarregando'}),

    carregando: {
      get () {
        return this.estaCarregando || this.estaCarregandoPessoa || this.estaCarregandoFranqueada
      }
    },
    camposEnderecoInvalidos: {
      get () {
        if (this.isValid) {
          return {}
        }
        return {
          endereco: !this.cep_data.endereco || this.cep_data.endereco.length === 0
        }
      }
    }
  },
  validations: {
    itemSelecionado: {
      beneficiario_colaboradores: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      },
      beneficiario_estagiarios: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      },
      beneficiario_terceiros: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      },
      beneficiario_alunos: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      },
      beneficiario_dependentes: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      },
      beneficiario_associados: {
        required: requiredIf(function () {
          return this.beneficiarioIsRequired()
        })
      }
    },
    razao_social: {required},
    cnpj_cpf: {required}
  },

  mounted () {
    this.listaCamposDinamicos()
    this.preparaDadosTela()
  },
  methods: {
    ...mapMutations('convenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('convenio', ['buscar', 'atualizar', 'inativar']),

    beneficiarioIsRequired () {
      if (this.itemSelecionado.beneficiario_colaboradores ||
          this.itemSelecionado.beneficiario_dependentes ||
          this.itemSelecionado.beneficiario_terceiros ||
          this.itemSelecionado.beneficiario_estagiarios ||
          this.itemSelecionado.beneficiario_alunos ||
          this.itemSelecionado.beneficiario_associados) {
        return false
      }

      return true
    },

    toApprove (op) {
      this.fecharConvenio(op)
    },

    notApprove (op) {
      this.fecharConvenio(op)
    },

    setSituacao (value) {
      this.situacao = value.id ? value : null
    },

    setCepData (value) {
      this.cep_data = value
    },

    uploadFile () {
      var input = document.createElement('input')
      input.type = 'file'
      input.accept = 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'

      input.onchange = e => {
        const reader = new FileReader()
        reader.onload = (e, t) => {
          this.arquivoContrato = input.files[0]
        }
        reader.readAsDataURL(input.files[0])
        this.nomeArquivoContrato = input.files[0].name
      }
      input.click()
    },

    limparDados (op) {
      if (op) {
        if (op.pessoa === true) {
          this.$store.commit('pessoas/limparPessoa')
        }

        if (op.itemSelecionado === true) {
          this.LIMPAR_ITEM_SELECIONADO()
        }
      } else {
        this.$store.commit('pessoas/limparPessoa')
        this.LIMPAR_ITEM_SELECIONADO()
      }
    },

    podeInativarConvenio (convenio) {
      if (!convenio.id) {
        return false
      }

      const franqueadaValida = this.objFranqueada.id === convenio.franqueada.id || !!this.objFranqueada.franqueadora
      const estaAtivo = convenio.situacao === 'ATI'
      return franqueadaValida && estaAtivo
    },

    inativarConvenio (convenio) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.inativar(convenio.id).then(() => {
            this.itemSelecionado.situacao = 'I'
            this.voltar()
          })
        }
      }, `Deseja inativar este convenio?`)
    },

    download () {

    },

    voltar () {
      this.limparDados()
      this.$router.push('/cadastros/convenio')
    },

    listaCamposDinamicos () {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('funcionario/listar')

      this.$store.commit('negociacaoParceriaWorkflow/SET_PAGINA_ATUAL', 1)
      this.$store.commit('negociacaoParceriaWorkflow/SET_LISTA', [])
      this.$store.dispatch('negociacaoParceriaWorkflow/listar')

      this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
      this.$store.commit('franqueadas/setFranqueadaSelecionada', this.$store.state.root.usuarioLogado.franqueadaSelecionada)
      this.$store.dispatch('franqueadas/buscarParametros')
    },

    preparaDadosTela () {
      this.limparDados()
      const id = this.$route.params.id
      if (id) {
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar()
          .then(this.montaDadosRequisicaoParaTela)
          .catch(e => console.log(e))
      }
    },

    buscarPessoa () {
      this.$store.commit('pessoas/setPessoaSelecionada', this.itemSelecionado.pessoa.id)
      this.$store.dispatch('pessoas/getPessoa').then(this.montaDadosRequisicaoParaTela).catch(e => console.log(e))
    },

    montaDadosRequisicaoParaTela () {
      let obj = {descricao: ''}
      obj = this.listaNegociacaoParceriaWorkflow.find(item => item.tipo_workflow === this.itemSelecionado.situacao)
      this.situacao = this.itemSelecionado.situacao === 'A' ? obj : obj.descricao

      const pessoa = this.itemSelecionado.pessoa
      this.nome_fantasia = pessoa.nome_fantasia || ''
      this.razao_social = pessoa.razao_social || ''
      this.cnpj_cpf = pessoa.cnpj_cpf || ''

      this.cep_data = {
        cep_endereco: pessoa.cep_endereco || '',
        endereco: pessoa.endereco || '',
        numero_endereco: pessoa.numero_endereco || '',
        complemento_endereco: pessoa.complemento_endereco || '',
        bairro_endereco: pessoa.bairro_endereco || '',
        estado: pessoa.estado || '',
        cidade: pessoa.cidade || ''
      }
    },

    trataErroConsole (erro) {
      if (erro && erro.body && erro.body.mensagem) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: erro.body.mensagem
        })
      } else {
        console.info('Ocorreu um erro nao tratado', erro)
      }
    },

    montaParametrosPessoa () {
      this.pessoaSeleciona.id = this.itemSelecionado.pessoa.id

      this.$store.commit('pessoas/setPessoa', this.itemSelecionado.pessoa ? this.itemSelecionado.pessoa : null)
      this.$store.commit('pessoas/setRazaoSocial', this.razao_social ? this.razao_social : null)
      this.$store.commit('pessoas/setCnpjCpf', this.cnpj_cpf ? this.cnpj_cpf.replace(new RegExp('[.//-]', 'gm'), '') : null)

      // Parametros endereço
      this.$store.commit('pessoas/setTipoPessoa', 'J')
      this.$store.commit('pessoas/setEndereco', this.cep_data.endereco ? this.cep_data.endereco : null)
      this.$store.commit('pessoas/setNumeroEndereco', this.cep_data.numero_endereco ? this.cep_data.numero_endereco : null)
      this.$store.commit('pessoas/SET_COMPLEMENTO_ENDERECO', this.cep_data.complemento_endereco ? this.cep_data.complemento_endereco : null)
      this.$store.commit('pessoas/SET_CEP_ENDERECO', this.cep_data.cep_endereco ? this.cep_data.cep_endereco : null)
      this.$store.commit('pessoas/SET_BAIRRO_ENDERECO', this.cep_data.bairro_endereco ? this.cep_data.bairro_endereco : null)
      this.$store.commit('pessoas/SET_CIDADE', this.cep_data.cidade ? this.cep_data.cidade.id : null)
      this.$store.commit('pessoas/SET_ESTADO', this.cep_data.estado ? this.cep_data.estado.id : null)
    },

    montaParametrosConvenio (op) {
      this.itemSelecionado.etapas_convenio = this.itemSelecionado.etapas_convenio ? this.itemSelecionado.etapas_convenio.id : null
      this.itemSelecionado.segmento_empresa_convenio = this.itemSelecionado.segmento_empresa_convenio ? this.itemSelecionado.segmento_empresa_convenio.id : null
      this.itemSelecionado.consultor_funcionario = this.itemSelecionado.consultor_funcionario ? this.itemSelecionado.consultor_funcionario.id : null
      this.itemSelecionado.pessoa = this.itemSelecionado.pessoa ? this.itemSelecionado.pessoa.id : null
      this.itemSelecionado.negociacao_parceria_workflow = this.itemSelecionado.negociacao_parceria_workflow ? this.itemSelecionado.negociacao_parceria_workflow.id : null
      this.itemSelecionado.followupConvenios = null

      this.itemSelecionado.data_proximo_contato = null
      this.itemSelecionado.horario_proximo_contato = null

      // Comandos de opcoes
      if (op.observacao) {
        this.itemSelecionado.observacao = op.observacao
      }

      if (op.fechar_convenio === true) {
        this.itemSelecionado.fechar_convenio = true
      }

      if (this.arquivoContrato !== null) {
        this.itemSelecionado.contrato = this.arquivoContrato
      }

      if (op.situacao || this.itemSelecionado.situacao === 'ATI') {
        if (this.itemSelecionado.situacao === 'ATI') {
          op.situacao = this.situacao.tipo_workflow
        }
        this.itemSelecionado.situacao = op.situacao
      }
    },

    validate () {
      if (!this.cep_data.endereco || this.cep_data.endereco.length === 0) {
        return false
      }
      if (!this.cnpj_cpf.length || this.cnpj_cpf.length < 18) {
        return false
      }
      return true
    },

    fecharConvenio (op = {}) {
      if (this.$v.$invalid || !this.validate()) {
        this.isValid = false
        return
      }
      let itsMime = this.$store.state.root.usuarioLogado.franqueadaSelecionada === this.itemSelecionado.franqueada.id
      if (itsMime) {
        this.montaParametrosPessoa()
        this.$store.dispatch('pessoas/atualizarPessoa', true)
          .then((resp) => {
            this.montaParametrosConvenio(op)
            this.atualizar().then(this.voltar).catch(this.trataErroConsole)
          })
          .catch(this.trataErroConsole)
      } else {
        this.montaParametrosConvenio(op)
        this.atualizar().then(this.voltar).catch(this.trataErroConsole)
      }
    }
  }
}
</script>
