<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="content-sector sector-azul p-3">
        <template v-if="!resumoFollowUp">
          <div class="form-group row">
            <div class="col-md-6">
              <label for="nome_fantasia" class="col-form-label">Nome fantasia *</label>
              <input id="nome_fantasia" v-model="form.nome_fantasia" type="text" name="nome_fantasia" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="segmento" class="col-form-label">Segmento</label>
              <g-select id="segmento"
                        :select="setSegmento"
                        :value="segmento"
                        :options="listaSegmentoEmpresa"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label for="pessoa_de_contato" class="col-form-label">Pessoa de contato *</label>
              <input id="pessoa_de_contato" v-model="form.nome_contato" type="text" name="nome_contato" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="col-form-label">Email *</label>
              <input id="email" v-model="form.email_preferencial" type="text" name="email" class="form-control" required>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-6">
              <label for="telefone" class="col-form-label">Telefone *</label>
              <input v-mask="'(##) #####-####'" id="telefone" v-model="form.telefone_contato" type="text" name="telefone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="telefone_secundario" class="col-form-label">Telefone secundário</label>
              <input v-mask="'(##) #####-####'" id="telefone_secundario" v-model="form.telefone_contato_secundario" type="text" name="telefone_secundario" class="form-control" >
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-3">
              <label for="proximo_contato" class="col-form-label">Próximo contato *</label>
              <v-date-picker
                v-model="form.data_proximo_contato"
                :input-props="{ id: 'proximo_contato', class: 'form-control', placeholder: 'Data', required: true, autocomplete: 'off' }"
                :popover="{ visibility: 'click' }"
                :min-date="new Date()"
                :attributes="attributes"
                is-required
              />
            </div>
            <div class="col-md-3">
              <label for="horario" class="col-form-label">Horário *</label>
              <input v-mask="'##:##'" id="horario" v-model="form.horario_proximo_contato" type="text" name="horario" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="consultor_responsavel_proximo_atendimento" class="col-form-label">Consultor responsável pelo próximo atendimento *</label>
              <g-select id="consultor_responsavel_proximo_atendimento"
                        :select="setConsultorResponsavelPeloProximoAtendimento"
                        :value="consultor_responsavel"
                        :options="listaFuncionarios"
                        :required="true"
                        :class="$v.consultor_responsavel.$invalid ? 'invalid-input' : 'valid-input'"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-4">
              <label for="situacao" class="col-form-label">Situação</label>
              <input id="situacao" v-model="form.situacao" type="text" name="situacao" class="form-control" disabled>
            </div>
            <div class="col-md-4">
              <label for="nova_situacao" class="col-form-label">Nova situação *</label>
              <g-select id="nova_situacao"
                        :select="setNovaSituacao"
                        :value="nova_situacao"
                        :options="listaWorkflow"
                        :required="true"
                        :class="$v.nova_situacao.$invalid ? 'invalid-input' : 'valid-input'"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>
            <div class="col-md-4">
              <label for="status" class="col-form-label">Status *</label>
              <g-select id="status"
                        :select="setStatus"
                        :value="status"
                        :options="listaDeStatus"
                        :required="true"
                        :class="$v.status.$invalid ? 'invalid-input' : 'valid-input'"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>
        </template>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="historico" class="col-form-label">Histórico</label>
            <textarea id="historico" v-model="form.historico" name="historico" cols="30" rows="10" class="form-control area-text" disabled></textarea>
          </div>
        </div>
        <div v-if="!resumoFollowUp" class="form-group row">
          <div class="col-md-12">
            <label for="follow_up" class="col-form-label">Follow up</label>
            <textarea id="follow_up" v-model="form.observacao" name="follow_up" cols="30" rows="10" class="form-control area-text"></textarea>
          </div>
        </div>
      </div>

      <div class="form-group pt-2">
        <template v-if="!resumoFollowUp">
          <div v-if="!itsMine()" class="list-group-item list-group-item-accent-warning  list-group-item-warning border-0">
            Não é possível alterar essas informações da {{ getName() }}
          </div>

          <b-btn v-if="itsMine()" :disabled="enviando" variant="verde" type="submit">Salvar</b-btn>
          <b-btn v-if="!itsMine()&&objFranqueada.franqueadora" :disabled="enviando" variant="azul" @click="irParaTelaEditarConvenio()">Ir para tela de editar convênio</b-btn>
          <!-- <b-btn :disabled="enviando" :variant="itsMine() === true ? 'verde' : 'azul'" type="submit"> {{ itsMine() === true ? 'Salvar' : 'Ir para tela de aprovação' }}</b-btn> -->
          <template v-if="itsMine()">
            <b-btn v-if="canStart()" :disabled="enviando" variant="roxo" @click="iniciarConvenio()">{{ 'Iniciar Convênio' }}</b-btn>
            <b-btn v-if="itemSelecionado.situacao ==='PV' || itemSelecionado.situacao === 'ATI'" :disabled="enviando" variant="roxo" @click="iniciarConvenio()">{{ canStart() === true ? 'Iniciar Convênio': 'Editar convênio' }}</b-btn>
          </template>
        </template>

        <b-btn v-if="resumoFollowUp" variant="link" @click="$emit('closeModel')">{{ 'Voltar' }}</b-btn>
        <b-btn v-else variant="link" @click="voltar()">{{ 'Cancelar' }}</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required, minLength, email, requiredIf} from 'vuelidate/lib/validators'
import { stringToISODate } from '../../utils/date'
import ModalFollowUp from '../interessados/ModalFollowUp.vue'
import FormularioPessoa from '../pessoas/Formulario.vue'
import moment from 'moment'
const Equals = (a, b) => `${a}` === `${b}`

export default {
  name: 'FormularioConvenio',
  components: {
    ModalFollowUp,
    FormularioPessoa
  },
  data () {
    return {
      loadCount: 0,
      abrangenciaSelected: '',
      observacoesTexto: '',
      nomeContato: '',
      telefoneContato: '',
      telefoneContatoSecundario: '',
      emailContato: '',
      descricaoEmpresa: '',
      arquivoData: '',
      isValid: true,
      bIniciarConvenio: false,
      isEnviando: false,
      isEdit: false,
      beneficiarioFuncionario: false,
      beneficiarioDependente: false,
      beneficiarioAssociado: false,
      email_contatoInvalido: false,
      telefoneInvalido: false,
      empresaId: null,
      segmentoEmpresa: null,
      contratoDigitalizadoArray: [],
      segmento: '',
      consultor_responsavel: '',
      nova_situacao: null,
      status: '',
      pessoa: null,
      data_nascimento: new Date(),
      form: {
        nome_fantasia: null,
        nome_contato: null,
        email_preferencial: null,
        telefone_contato: null,
        telefone_contato_secundario: null,
        data_proximo_contato: null,
        horario_proximo_contato: null,
        situacao: null,
        historico: null,
        observacao: null
      },
      abrangenciaOpcoes: [
        {text: 'Nacional', value: true},
        {text: 'Estadual', value: false}
      ],
      attributes: [
        {
          highlight: { class: 'today-mark' },
          dates: new Date()
        }
      ],
      resumoFollowUp: false
    }
  },
  computed: {
    ...mapState('convenio', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('pessoas', {pessoaSeleciona: 'objPessoa', estaCarregandoPessoa: 'estaCarregando'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('segmentoEmpresaConvenio', {listaSegmentoEmpresaRequisicao: 'lista'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),
    ...mapState('negociacaoParceriaWorkflow', {listaNegociacaoParceriaWorkflowRequisicao: 'lista'}),
    ...mapState('franqueadas', {objFranqueada: 'objFranqueada', estaCarregandoFranqueada: 'estaCarregando'}),

    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionariosRequisicao.filter(funcionario => (funcionario.consultor))]
      }
    },

    listaSegmentoEmpresa: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaSegmentoEmpresaRequisicao]
      }
    },

    listaDeStatus () {
      let lista = [{id: null, descricao: 'Selecione'}]

      if (this.nova_situacao) {
        lista = lista.concat(this.listaEtapasConvenioRequisicao.filter(item => item.negociacao_parceria_workflow.tipo_workflow === this.nova_situacao.tipo_workflow))
      }
      return lista
    },

    listaWorkflow: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaNegociacaoParceriaWorkflowRequisicao.filter(item => item.situacao === 'A' && item.mostrar_opcoes_situacao === true)]
      }
    },
    enviando: {
      get () {
        return this.estaCarregando || this.estaCarregandoPessoa
      }
    }
  },
  mounted () {
    this.limparDados()
    this.listaCamposDinamicos()
    const id = this.$route.params.id

    if (this.$route.query) {
      if (this.$route.query.resumo) {
        this.resumoFollowUp = true
      }
    }

    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(item => {
          this.montaDadosRequisicaoParaTela()
        })
    }
  },
  validations: {
    form: {
      nome_fantasia: {required},
      nome_contato: {required},
      email_preferencial: {email},
      telefone_contato: {required, minLength: minLength(14)},
      data_proximo_contato: {
        required: requiredIf(function () {
          return this.itsMine() && this.canStart()
        })},
      horario_proximo_contato: {
        required: requiredIf(function () {
          return this.itsMine() && this.canStart()
        })}
    },
    consultor_responsavel: {required},
    nova_situacao: {required},
    status: {required}
  },
  methods: {
    ...mapMutations('convenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('convenio', ['buscar', 'criar', 'atualizar']),

    getName () {
      const franqueada = this.itemSelecionado.franqueada || {nome: ''}
      return franqueada.nome
    },

    itsMine () {
      if (this.isEdit) {
        const idFranqueadaLogado = this.$store.state.root.usuarioLogado.franqueadaSelecionada
        const idFranqueadaItemSelecionado = this.itemSelecionado.franqueada ? this.itemSelecionado.franqueada.id : null
        return Equals(idFranqueadaLogado, idFranqueadaItemSelecionado)
      }

      return true
    },

    canStart () {
      let show = false
      const isActive = (status) => `${status}` === `ATI`
      const isPendingOfApproval = (s) => `${s}` === 'PV'

      const situacaoItemSelecionado = this.itemSelecionado.situacao ? this.itemSelecionado.situacao : null
      const isPendenteDeValidacao = isPendingOfApproval(situacaoItemSelecionado)
      const situacao = this.nova_situacao ? this.nova_situacao.tipo_workflow : null
      const status = this.status || null

      const isAtivo = isActive(situacao)

      if (this.isEdit) {
        const itsMine = this.itsMine()
        if (!isActive(situacaoItemSelecionado)) {
          if (itsMine && isAtivo && status && !isPendenteDeValidacao) {
            show = true
          }
        }
      }

      if (!this.isEdit) {
        if (isAtivo && status) {
          show = true
        }
      }

      return show
    },

    iniciarConvenio () {
      this.bIniciarConvenio = true
      this.salvar()
    },
    alteraArquivo (event) {
      const input = event.target
      if (input.files && input.files[0]) {
        let reader = new FileReader()
        reader.onload = (e) => {
          this.arquivoData = e.target.result
          this.itemSelecionado.contrato = input.files[0]
        }

        reader.readAsDataURL(input.files[0])
      }
    },

    limpaArquivo () {
      this.arquivoData = ''
      this.itemSelecionado.arquivo = ''
    },

    montaArrayArquivo () {
      if ((this.itemSelecionado.contrato_digitalizado !== undefined) && (this.itemSelecionado.contrato_digitalizado !== '')) {
        let localArquivo = this.itemSelecionado.contrato_digitalizado.replace('./../public', '')
        let nomeArquivo = this.itemSelecionado.contrato_digitalizado.replace('./../public/uploads/', '')
        this.contratoDigitalizadoArray[0] = nomeArquivo
        this.contratoDigitalizadoArray[1] = window.location.origin + localArquivo
      }
    },

    montaDadosRequisicaoParaTela () {
      const pessoa = this.itemSelecionado.pessoa
      this.form.nome_fantasia = pessoa.nome_fantasia ? pessoa.nome_fantasia : ''
      this.form.nome_contato = pessoa.nome_contato ? pessoa.nome_contato : ''
      this.form.email_preferencial = pessoa.email_preferencial ? pessoa.email_preferencial : ''
      this.form.telefone_contato = pessoa.telefone_preferencial ? pessoa.telefone_preferencial : ''
      this.form.telefone_contato_secundario = pessoa.telefone_contato ? pessoa.telefone_contato : ''

      this.form.data_proximo_contato = this.itemSelecionado.data_proximo_contato ? new Date(this.itemSelecionado.data_proximo_contato) : ''
      this.form.horario_proximo_contato = this.itemSelecionado.horario_proximo_contato ? this.itemSelecionado.horario_proximo_contato.match(/(\d{2,2}):(\d{2,2})/)[0] : ''

      const objSituacao = this.listaNegociacaoParceriaWorkflowRequisicao ? this.listaNegociacaoParceriaWorkflowRequisicao.find(item => item.tipo_workflow === this.itemSelecionado.situacao) : null
      this.form.situacao = objSituacao ? objSituacao.descricao : null
      let historico = ''

      this.itemSelecionado.followupConvenios.forEach((followUp) => {
        historico += followUp.followup + '\n'
      })

      this.form.historico = historico
      // Selects:
      this.segmento = this.itemSelecionado.segmento_empresa_convenio ? this.itemSelecionado.segmento_empresa_convenio : null
      this.consultor_responsavel = this.itemSelecionado.consultor_funcionario ? this.itemSelecionado.consultor_funcionario : null
      this.nova_situacao = this.itemSelecionado.negociacao_parceria_workflow ? this.itemSelecionado.negociacao_parceria_workflow : null
      this.status = this.itemSelecionado.etapas_convenio ? this.itemSelecionado.etapas_convenio : null

      this.montaArrayArquivo()
    },

    setEmpresaId (value) {
      if (value) {
        this.empresaId = value.id
        this.descricaoEmpresa = value.nome_contato
      } else {
        this.empresaId = null
        this.descricaoEmpresa = null
      }
    },

    setSegmentoEmpresa (value) {
      this.segmentoEmpresa = value
    },

    listaCamposDinamicos () {
      this.$store.commit('segmentoEmpresaConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.commit('segmentoEmpresaConvenio/SET_LISTA', [])
      this.$store.dispatch('segmentoEmpresaConvenio/listar')

      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.dispatch('funcionario/listar')

      this.$store.commit('etapasConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.commit('etapasConvenio/SET_LISTA', [])
      this.$store.dispatch('etapasConvenio/buscarTodos')

      this.$store.commit('negociacaoParceriaWorkflow/SET_PAGINA_ATUAL', 1)
      this.$store.commit('negociacaoParceriaWorkflow/SET_LISTA', [])
      this.$store.dispatch('negociacaoParceriaWorkflow/listar')

      this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
      this.$store.commit('franqueadas/setFranqueadaSelecionada', this.$store.state.root.usuarioLogado.franqueadaSelecionada)
      this.$store.dispatch('franqueadas/buscarParametros')
    },

    voltar (op = {}) {
      const idConvenio = op.id ? op.id : this.itemSelecionado.id
      this.limpaArquivo()
      this.limparDados({pessoa: true, itemSelecionado: true})

      if (this.bIniciarConvenio === true) {
        this.$router.push(`/cadastros/convenio/iniciar/${idConvenio}`)
      } else {
        this.$router.push('/cadastros/convenio')
      }
      this.bIniciarConvenio = false
      this.isEdit = false
      this.resumoFollowUp = false
    },

    trataErroConsole () {
      console.info('ocorreu um erro nao tratado')
    },

    irParaTelaEditarConvenio (item) {
      this.$router.push(`/cadastros/convenio/iniciar/${this.itemSelecionado.id}`)
    },

    salvar () {
      if (!this.itsMine()) {
        this.bIniciarConvenio = true
        this.voltar()
      }
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        this.montaParametrosPessoa()
        this.pessoaSeleciona.id = this.itemSelecionado.pessoa.id
        this.$store.dispatch('pessoas/atualizarPessoa', true).then(() => {
          this.montaParametrosConvenio()
          this.atualizar().then(this.voltar).catch((e) => { console.log(e) })
        }).catch(e => console.log(e))
      } else {
        this.montaParametrosPessoa()
        this.$store.dispatch('pessoas/criarPessoa', true)
          .then((r) => {
            let id = r.pessoa
            this.$store.commit('pessoas/setPessoaSelecionada', id)
            this.$store.dispatch('pessoas/getPessoa')
              .then(() => {
                this.montaParametrosConvenio()
                this.criar().then((r) => { this.voltar({id: r.id}) }).catch(this.trataErroConsole)
              }).catch(e => console.log(e))
          }).catch(e => console.log(e))
      }
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

    montaParametrosPessoa () {
      if (this.itemSelecionado.pessoa) {
        this.$store.commit('pessoas/setPessoa', this.itemSelecionado.pessoa)
        this.itemSelecionado.pessoa.cidade = this.itemSelecionado.pessoa.cidade ? this.itemSelecionado.pessoa.cidade.id : null
        this.itemSelecionado.pessoa.estado = this.itemSelecionado.pessoa.estado ? this.itemSelecionado.pessoa.estado.id : null
      }

      this.$store.commit('pessoas/setTipoPessoa', 'J')
      this.$store.commit('pessoas/setNomeFantasia', this.form.nome_fantasia)
      this.$store.commit('pessoas/setNomeContato', this.form.nome_contato)
      this.$store.commit('pessoas/setEmailPreferencial', this.form.email_preferencial)
      this.$store.commit('pessoas/setTelefonePreferencial', this.form.telefone_contato)
      this.$store.commit('pessoas/setTelefoneContato', this.form.telefone_contato_secundario)
    },

    montaParametrosConvenio () {
      this.$store.commit('convenio/SET_SEGMENTO', this.segmento ? this.segmento.id : null)
      this.$store.commit('convenio/SET_NOVA_SITUACAO', this.nova_situacao ? this.nova_situacao.id : null)
      this.$store.commit('convenio/SET_CONSULTOR_RESPONSAVEL', this.consultor_responsavel ? this.consultor_responsavel.id : null)
      this.$store.commit('convenio/SET_STATUS', this.status ? this.status.id : null)

      this.$store.commit('convenio/SET_PROXIMO_CONTATO', this.form.data_proximo_contato ? stringToISODate(moment(this.form.data_proximo_contato).format('DD/MM/YYYY'), true) : null)
      this.$store.commit('convenio/SET_HORARIO', this.form.horario_proximo_contato ? `2000-01-01T${this.form.horario_proximo_contato}:00.000Z` : null)

      this.$store.commit('convenio/SET_EMAIL_CONTATO', this.form.email_preferencial)
      this.$store.commit('convenio/SET_TELEFONE_CONTATO', this.form.telefone_contato)
      this.$store.commit('convenio/SET_TELEFONE_SECUNDARIO', this.form.telefone_contato_secundario)
      this.$store.commit('convenio/SET_OBSERVACAO', this.form.observacao)

      this.$store.commit('convenio/SET_PESSOA_CONTATO', this.itemSelecionado.pessoa ? this.itemSelecionado.pessoa.id : this.pessoaSeleciona.id)
      this.$store.commit('convenio/SET_NOME_CONTATO', this.form.nome_contato)
      // this.$store.commit('pessoas/setDataNascimento', this.data_nascimento ? stringToISODate('16/06/2020', true) : null)
    },

    pessoaSalva (pessoaID) {
      this.$refs.modalPessoa.hide()

      if (!pessoaID) {
        return
      }

      this.$store.commit('pessoas/setPessoaSelecionada', pessoaID)
      this.$store.dispatch('pessoas/getPessoa')
        .then(() => {
          this.setEmpresaId(this.$store.state.pessoas.objPessoa)
        })
        .catch(console.error)
    },

    cancelarPessoa () {
      this.$refs.modalPessoa.hide()
    },

    limparPessoa () {
      this.setEmpresaId()
      this.$refs.frmPessoa.voltar()
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    },

    setPessoaContato (value) {
      this.pessoa = value
      this.itemSelecionado.pessoa = value.id || null
    },

    setSegmento (value) {
      this.segmento = value.id === null ? null : value
      this.itemSelecionado.segmento_empresa_convenio = value.id
    },

    setConsultorResponsavelPeloProximoAtendimento (value) {
      this.consultor_responsavel = value.id === null ? null : value
      this.itemSelecionado.consultor_responsavel = value.id
    },

    setNovaSituacao (value) {
      this.status = null
      this.nova_situacao = value.id === null ? null : value
      this.itemSelecionado.negociacao_parceria_workflow = value.id
    },

    setStatus (value) {
      this.status = value.id === null ? null : value
      this.itemSelecionado.etapas_convenio = value.id
    }
  }
}
</script>
<style scoped>
.area-text {
  min-height: 200px;
}
</style>
