<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="true" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount,4)" />
      </div>
      <div class="content-sector sector-azul p-3">
        <h5 class="title-module mb-2">Empresa</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="busca_empresa" class="col-form-label">Empresa *</label>
            <template v-if="isEdit === false">
              <div class="d-flex">
                <div class="w-100">
                  <template v-if="empresaId === null">
                    <typeahead id="busca_empresa" :item-hit="setEmpresaId" source-path="/api/pessoa/buscar/empresa" key-name="nome_contato" required />
                  </template>

                  <template v-else>
                    <div class="d-flex">
                      <span class="form-control form-control-disabled w-100 truncate">{{ descricaoEmpresa }}</span>

                      <b-btn variant="link" @click="limparPessoa()">Limpar</b-btn>
                    </div>
                  </template>
                </div>

                <b-btn v-b-modal.modalPessoa v-if="empresaId === null" variant="roxo">
                  <font-awesome-icon icon="plus" />
                </b-btn>
              </div>
            </template>
            <template v-if="isEdit === true">
              <input id="busca_empresa" v-model="descricaoEmpresa" type="text" name="busca_empresa" class="form-control" disabled readonly>
            </template>
          </div>
          <div class="col-md-6">
            <label for="segmento_empresa" class="col-form-label">Segmento</label>
            <g-select
              id="segmento_empresa"
              :value="segmentoEmpresa"
              :select="setSegmentoEmpresa"
              :options="listaSegmentoEmpresa"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id" />
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="nomeContato" class="col-form-label">Contato *</label>
            <input id="nomeContato" v-model="nomeContato" type="text" class="form-control" maxlength="150" required>
          </div>
          <div class="col-md-6">
            <label for="emailContato" class="col-form-label">E-mail Contato *</label>
            <input id="emailContato" v-model="emailContato" :class="{ 'is-invalid' : email_contatoInvalido }" type="text" class="form-control" maxlength="50" required @blur="email_contatoInvalido = $v.emailContato.$invalid">
            <div v-if="email_contatoInvalido" class="input-invalid">Preencha corretamente o e-mail!</div>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="telefoneContato" class="col-form-label">Telefone Contato *</label>
            <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefoneContato" v-model="telefoneContato" type="text" class="form-control" maxlength="20" required>
          </div>
          <div class="col-md-6">
            <label for="telefoneContatoSecundario" class="col-form-label">Telefone Contato Secundário</label>
            <input v-mask="['(##) ####-####', '(##) #####-####']" id="telefoneContatoSecundario" v-model="telefoneContatoSecundario" type="text" class="form-control" maxlength="20">
          </div>
        </div>
      </div>
      <div class="content-sector sector-laranja p-3">
        <h5 class="title-module mb-2">Convênio</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <b-form-group label="Abrangência *">
              <b-form-radio-group
                id="agrangencia_radio"
                v-model="abrangenciaSelected"
                :options="abrangenciaOpcoes"
                name="radio-options"
                required
              />
            </b-form-group>
          </div>
          <div class="col-md-6">
            <b-form-group label="Beneficiário">
              <b-form-checkbox v-model="beneficiarioFuncionario" name="check-button">Funcionários</b-form-checkbox>
              <b-form-checkbox v-model="beneficiarioDependente" name="check-button">Dependentes</b-form-checkbox>
              <b-form-checkbox v-model="beneficiarioAssociado" name="check-button">Associados</b-form-checkbox>
            </b-form-group>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <label for="observacoes" class="col-form-label">Observações</label>
            <textarea id="observacoes" v-model="observacoesTexto" class="form-control full-textarea" maxlength="5000" rows="10"></textarea>
            <span class="text-secondary">Limite de caracteres: {{ 5000 - observacoesTexto.length }}</span>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <template v-if="isEdit && itemSelecionado.contrato_digitalizado">
              <label for="arquivo_contrato" class="col-form-label">Arquivo de contrato</label>
              <a :href="contratoDigitalizadoArray[1]">{{ contratoDigitalizadoArray[0] }}</a>
            </template>
            <input id="arquivo_contrato" type="file" accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" @change="alteraArquivo">
          </div>
        </div>
      </div>
      <div class="content-sector sector-secondary p-3">
        <h5 class="title-module mb-2">Follow-Up</h5>
        <div class="form-group row">
          <div class="col-md-12">
            <textarea id="observacao_follow_up" v-model="followUpsAdicionados" class="form-control" rows="10" readonly></textarea>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <button v-b-modal.followUpGeral type="button" class="btn btn-azul">
              Adicionar Follow-Up
            </button>
          </div>
        </div>
      </div>

      <div class="content-sector sector-vermelho p-3">
        <h5 class="title-module mb-2">Processo Comercial</h5>
        <div class="form-group row">
          <div class="col-md-12">
            <label for="proximo_contato" class="col-form-label">Próximo contato</label>
            <div class="row">
              <div class="col-md-2">
                <g-datepicker :element-id="'proximo_contato_temporario'" :value="proximo_contato_temporario" :selected="setProximoContatoTemporario" placeholder="Data"/>
              </div>
              <div class="col-md-2">
                <input v-mask="'##:##'" v-model="horario_proximo_contato" :class="horario_proximo_contato.length === 5 && $v.horario_proximo_contato.$invalid ? 'is-invalid' : null" type="text" class="form-control" maxlength="5" placeholder="Horario">
                <div class="invalid-feedback">Horário inválido</div>
              </div>
            </div>
          </div>
        </div>
        <template v-if="isEdit">
          <div class="form-group row">
            <div class="col-md-6">
              <label for="situacaoConvenio" class="col-form-label">Situação</label>
              <input :value="retornaSituacao(itemSelecionado.situacao)" type="text" disabled="disabled" class="form-control">
            </div>
          </div>
        </template>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="situacaoConvenio" class="col-form-label">Situação Nova *</label>
            <g-select
              id="situacaoConvenio"
              :value="situacaoSelecionada"
              :select="setSituacao"
              :options="situacaoOpcoes"
              :disabled="bloqueiaCampoSituacao()"
              :readonly="bloqueiaCampoSituacao()"
              :class="!isValid && !situacaoSelecionada ? 'invalid-input' : 'valid-input'"
              class="multiselect-truncate valid-input"
              label="text"
              track-by="value"
              required/>
          </div>
          <template v-if="situacaoSelecionada.value === 'SC'">
            <div class="col-md-6">
              <label for="motivo_nao_fechamento" class="col-form-label">Motivo para não fechamento *</label>
              <g-select
                id="motivo_nao_fechamento"
                :value="motivoNaoFechamento"
                :select="setMotivoNaoFechamento"
                :options="listaMotivosNaoFechamentoConvenio"
                :class="!isValid && !motivoNaoFechamento ? 'invalid-input' : 'valid-input'"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
                required/>
              <div v-if="!isValid && !motivoNaoFechamento" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>
          </template>
        </div>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="etapas_convenio_filtro" class="col-form-label">Etapas Convenio *</label>
            <g-select
              id="etapas_convenio_filtro"
              :value="etapasConvenioSelecionado"
              :select="setEtapasConvenioSelecionado"
              :options="listaEtapasConvenio"
              :readonly="bloqueiaCampoEtapaConvenio()"
              :disabled="bloqueiaCampoEtapaConvenio()"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id"
              required/>
          </div>
        </div>
        <template v-if="situacaoSelecionada.value === 'NE' || situacaoSelecionada.value === 'EN'">
          <div class="form-group row">
            <div class="col-md-12">
              <label for="observacao" class="col-form-label">Justificativa Franqueadora *</label>
              <textarea id="observacao" v-model="justificativaFranqueadora" class="form-control full-textarea" maxlength="5000" rows="10" required></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - justificativaFranqueadora.length }}</span>
            </div>
          </div>
        </template>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="consultor_pessoa" class="col-form-label">Consultor</label>
            <g-select
              id="consultor_pessoa"
              :value="consultorFuncionario"
              :select="setConsultorFuncionario"
              :options="listaFuncionarios"
              class="multiselect-truncate valid-input"
              label="apelido"
              track-by="id" />
          </div>
        </div>

      </div>

      <div class="form-group pt-2">
        <b-btn :disabled="isEnviando || !isCamposObrigatoriosPreenchidos()" type="submit" variant="verde">Salvar</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <!-- Modal Follow Up -->
    <b-modal id="followUpGeral" ref="followUpGeral" v-model="visibleFollowUpGeral" size="md" centered no-close-on-backdrop hide-header hide-footer @show="abrirModalGeral">
      <modal-follow-up ref="ffGeral" :seta-dados-envio="configuraDadosFollowUpBackEnd" :seta-follow-up-callback="adicionarNovoFollowUp" :filtra-formularios="false" :formulario-convenio="true" :formulario-inicial-apenas="false" @hide="visibleFollowUpGeral = false" />
    </b-modal>

    <b-modal id="modalPessoa" ref="modalPessoa" size="md" title="Cadastrar Empresa" centered no-close-on-backdrop hide-footer>
      <formulario-pessoa :load-categories="false" :is-modal="true" :tipo-pessoa-fixo="'J'" @resolve="pessoaSalva" @reject="cancelarPessoa()" />
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {validateHour} from '../../utils/validators'
import {required, requiredIf, email} from 'vuelidate/lib/validators'
import {stringToISODate, dateToString} from '../../utils/date'
import ModalFollowUp from '../interessados/ModalFollowUp.vue'
import FormularioPessoa from '../pessoas/Formulario.vue'

export default {
  name: 'FormularioConvenio',
  components: {
    ModalFollowUp,
    FormularioPessoa
  },
  data () {
    return {
      loadCount: 0,
      followUpsAdicionados: '',
      proximo_contato_temporario: '',
      horario_proximo_contato: '',
      justificativaFranqueadora: '',
      abrangenciaSelected: '',
      observacoesTexto: '',
      nomeContato: '',
      telefoneContato: '',
      telefoneContatoSecundario: '',
      emailContato: '',
      descricaoEmpresa: '',
      arquivoData: '',
      isValid: true,
      isEnviando: false,
      isEdit: false,
      visibleFollowUpGeral: false,
      beneficiarioFuncionario: false,
      beneficiarioDependente: false,
      beneficiarioAssociado: false,
      email_contatoInvalido: false,
      empresaId: null,
      situacaoSelecionada: {text: 'Pendente Validação Franqueadora', value: 'PV'},
      motivoNaoFechamento: {id: null, descricao: 'Selecione'},
      segmentoEmpresa: {id: null, descricao: 'Selecione'},
      etapasConvenioSelecionado: {id: null, descricao: 'Selecione'},
      consultorFuncionario: {id: null, apelido: 'Selecione'},
      dadosFollowUps: [],
      listaEtapasConvenio: [],
      contratoDigitalizadoArray: [],
      abrangenciaOpcoes: [
        {text: 'Nacional', value: true},
        {text: 'Cidade', value: false}
      ],
      situacaoOpcoes: [
        {text: 'Ativo', value: 'A'},
        {text: 'Inativo', value: 'I'},
        {text: 'Pendente Validação Franqueadora', value: 'PV'},
        {text: 'Em Negociação', value: 'EN'},
        {text: 'Negado', value: 'NE'},
        {text: 'Retornar Futuramente', value: 'RF'},
        {text: 'Sem Convênio', value: 'SC'}
      ]
    }
  },
  computed: {
    ...mapState('convenio', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('motivoNaoFechamentoConvenio', {listaMotivoNaoFechamentoConvenioRequisicao: 'lista'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('segmentoEmpresaConvenio', {listaSegmentoEmpresaRequisicao: 'lista'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),

    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionariosRequisicao]
      }
    },

    listaMotivosNaoFechamentoConvenio: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaMotivoNaoFechamentoConvenioRequisicao]
      }
    },

    listaSegmentoEmpresa: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaSegmentoEmpresaRequisicao]
      }
    },

    bloqueiaAlterarSituacao: {
      get () {
        return this.$store.state.root.usuarioLogado.pertenceFranqueadora === false
      }
    }
  },
  watch: {
    listaEtapasConvenioRequisicao (lista) {
      if (lista.length > 0) {
        this.retornaListaRequisicaoEtapasFranqueada()
      }
    }
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    this.listaCamposDinamicos()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(item => {
          this.montaDadosRequisicaoParaTela()
          this.aplicaFiltroSituacoes()
          if (this.listaEtapasConvenioRequisicao.length > 0) {
            this.retornaListaRequisicaoEtapasFranqueada()
          }
        })
    }
  },
  validations: {
    nomeContato: {required},
    emailContato: {email},
    telefoneContato: {required},
    empresaId: {required},
    situacaoSelecionada: {required},
    abrangenciaSelected: {required},
    justificativaFranqueadora: {
      required: requiredIf(function () {
        return ((this.situacaoSelecionada.value === 'NE') || (this.situacaoSelecionada.value === 'EN'))
      })
    },
    motivoNaoFechamento: {
      required: requiredIf(function () {
        return this.situacaoSelecionada.value === 'SC'
      })
    },
    horario_proximo_contato: {validateHour}
  },
  methods: {
    ...mapMutations('convenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('convenio', ['buscar', 'criar', 'atualizar']),

    retornaSituacao (situacao) {
      let sitDesc = ''
      switch (situacao) {
        case 'A':
          sitDesc = 'Ativo'
          break
        case 'I':
          sitDesc = 'Inativo'
          break
        case 'PV':
          sitDesc = 'Pendente Validação Franqueadora'
          break
        case 'EN':
          sitDesc = 'Em Negociação'
          break
        case 'NE':
          sitDesc = 'Negado'
          break
        case 'RF':
          sitDesc = 'Retornar Futuramente'
          break
        case 'SC':
          sitDesc = 'Sem Convênio'
          break
      }
      return sitDesc
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

    isCamposObrigatoriosPreenchidos () {
      if ((this.nomeContato.trim().length > 0) && ((this.emailContato.trim().length > 0) && (this.$v.emailContato.$invalid === false)) && (this.telefoneContato.trim().length > 0) && (this.empresaId !== null) && (this.etapasConvenioSelecionado.id !== null) && (this.situacaoSelecionada.value !== null) && (this.abrangenciaSelected !== '')) {
        if (this.situacaoSelecionada.value === 'SC') {
          if (this.motivoNaoFechamento.id === null) {
            return false
          }
        }
        return true
      }
      return false
    },

    bloqueiaCampoSituacao () {
      if (((this.bloqueiaAlterarSituacao === true) && (this.isEdit === true) && (this.itemSelecionado.situacao === 'PV')) || this.isEdit === false) {
        return true
      }

      return false
    },

    bloqueiaCampoEtapaConvenio () {
      // const bloqueiaPorSituacaoAtual = this.itemSelecionado.situacao === 'NE'
      const bloqueiaPorSituacaoNova = (this.situacaoSelecionada.value === 'PV') || (this.situacaoSelecionada.value === 'NE')
      const bloqueiaPorSituacaoNegadaENova = (this.itemSelecionado.situacao === 'NE') && bloqueiaPorSituacaoNova

      const bloqueiaPorSituacao = (this.bloqueiaAlterarSituacao === true) && (this.isEdit === true) && (this.itemSelecionado.situacao === 'PV')

      if ((bloqueiaPorSituacao) || (bloqueiaPorSituacaoNegadaENova)) {
        return true
      }

      return false
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
      if (this.itemSelecionado.data_proximo_contato !== undefined) {
        this.proximo_contato_temporario = this.itemSelecionado.data_proximo_contato ? dateToString(new Date(this.itemSelecionado.data_proximo_contato)) : null
      }
      if (this.itemSelecionado.horario_proximo_contato !== undefined) {
        this.horario_proximo_contato = this.itemSelecionado.horario_proximo_contato ? this.itemSelecionado.horario_proximo_contato.match(/(\d{2,2}):(\d{2,2})/)[0] : null
      }
      if (this.itemSelecionado.followupConvenios !== undefined) {
        let arrayFollowUpConvenio = this.itemSelecionado.followupConvenios
        this.itemSelecionado.followupConvenios = null
        arrayFollowUpConvenio.sort((a, b) => {
          return a.id > b.id
        })
        arrayFollowUpConvenio.forEach((item) => {
          this.adicionarNovoFollowUp(item.followup)
        })
      }
      if (this.itemSelecionado.consultor_funcionario !== undefined) {
        this.consultorFuncionario = this.listaFuncionarios.filter((item) => {
          return this.itemSelecionado.consultor_funcionario.id === item.id
        })[0]
      }
      if (this.itemSelecionado.segmento_empresa_convenio !== undefined) {
        this.segmentoEmpresa = this.listaSegmentoEmpresa.filter(item => { return item.id === this.itemSelecionado.segmento_empresa_convenio.id })[0]
      }
      if (this.itemSelecionado.motivo_nao_fechamento_convenio !== undefined) {
        this.motivoNaoFechamento = this.listaMotivosNaoFechamentoConvenio.filter(item => { return item.id === this.itemSelecionado.motivo_nao_fechamento_convenio.id })[0]
      }
      if (this.itemSelecionado.justificativa_franqueadora !== undefined) {
        this.justificativaFranqueadora = this.itemSelecionado.justificativa_franqueadora
      }
      if (this.itemSelecionado.observacao !== undefined) {
        this.observacoesTexto = this.itemSelecionado.observacao
      }
      this.situacaoSelecionada = this.situacaoOpcoes.filter((item) => item.value === this.itemSelecionado.situacao)[0]
      this.etapasConvenioSelecionado = this.itemSelecionado.etapas_convenio
      this.nomeContato = this.itemSelecionado.nome_contato
      this.emailContato = this.itemSelecionado.email_contato
      this.telefoneContato = this.itemSelecionado.telefone_contato
      this.telefoneContatoSecundario = this.itemSelecionado.telefone_contato_secundario
      this.abrangenciaSelected = !!this.itemSelecionado.abrangencia_nacional
      this.beneficiarioAssociado = !!this.itemSelecionado.beneficiario_associados
      this.beneficiarioFuncionario = !!this.itemSelecionado.beneficiario_colaboradores
      this.beneficiarioDependente = !!this.itemSelecionado.beneficiario_dependentes
      this.descricaoEmpresa = this.itemSelecionado.pessoa.nome_contato
      this.empresaId = this.itemSelecionado.id
      this.montaArrayArquivo()
    },

    aplicaFiltroSituacoes () {
      if (this.itemSelecionado.situacao === 'PV') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'EN') || (item.value === 'NE')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'EN') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'A') || (item.value === 'EN') || (item.value === 'RF') || (item.value === 'SC')
        })
        this.situacaoOpcoes = listaSituacao
      } else if ((this.itemSelecionado.situacao === 'RF') || (this.itemSelecionado.situacao === 'SC')) {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'EN')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'A') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'I')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'I') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'A')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'NE') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'PV')
        })
        this.situacaoOpcoes = listaSituacao
      }
    },

    retornaListaRequisicaoEtapasFranqueada () {
      if (this.isEdit === false) {
        this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
          return (item.parceria_firmada !== true) && (item.retira_fluxo !== true)
        })]
        return
      }

      if (this.itemSelecionado.situacao === 'PV') {
        if (this.situacaoSelecionada.value === 'NE') {
          this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
            return (item.parceria_firmada === false) && (item.retira_fluxo === true)
          })]
        }
        if (this.situacaoSelecionada.value === 'EN') {
          this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
            return (item.parceria_firmada === false) && (item.retira_fluxo === false)
          })]
        }
      }
      if (this.itemSelecionado.situacao === 'EN') {
        if (this.situacaoSelecionada.value === 'A') {
          this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
            return (item.parceria_firmada === true) && (item.retira_fluxo === true)
          })]
        }
        if (this.situacaoSelecionada.value === 'EN') {
          this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
            return (item.parceria_firmada === false) && (item.retira_fluxo === false)
          })]
        }
        if ((this.situacaoSelecionada.value === 'RF') || (this.situacaoSelecionada.value === 'SC')) {
          this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
            return (item.parceria_firmada === false) && (item.retira_fluxo === true)
          })]
        }
      }
      if ((this.itemSelecionado.situacao === 'A') || (this.itemSelecionado.situacao === 'I')) {
        this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
          return (item.parceria_firmada === true) && (item.retira_fluxo === true)
        })]
      }
      if ((this.itemSelecionado.situacao === 'SC') || (this.itemSelecionado.situacao === 'RF')) {
        this.listaEtapasConvenio = [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao.filter((item) => {
          return (item.parceria_firmada === false) && (item.retira_fluxo === false)
        })]
      }
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

    setEtapasConvenioSelecionado (value) {
      this.etapasConvenioSelecionado = value
    },

    setMotivoNaoFechamento (value) {
      this.motivoNaoFechamento = value
    },

    setConsultorFuncionario (value) {
      this.consultorFuncionario = value
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
      if ((this.itemSelecionado.situacao !== 'NE') && (this.situacaoSelecionada.value !== 'PV')) {
        this.etapasConvenioSelecionado = {id: null, descricao: 'Selecione'}
      }
      this.observacoesTexto = ''
      this.retornaListaRequisicaoEtapasFranqueada()
    },

    setProximoContatoTemporario (value) {
      this.proximo_contato_temporario = value
    },

    listaCamposDinamicos () {
      this.$store.commit('etapasConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('etapasConvenio/buscarTodos').then(this.countCarregamento)
      this.$store.commit('segmentoEmpresaConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('segmentoEmpresaConvenio/listar').then(this.countCarregamento)
      this.$store.commit('motivoNaoFechamentoConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('motivoNaoFechamentoConvenio/listar').then(this.countCarregamento)
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('funcionario/listar').then(this.countCarregamento)
    },

    abrirModalGeral () {
      this.$refs.ffGeral.executaRequisicoes()
    },

    adicionarNovoFollowUp (novoFollowUp) {
      if (this.followUpsAdicionados.length > 0) {
        novoFollowUp += '\n' + this.followUpsAdicionados
      } else {
        novoFollowUp += this.followUpsAdicionados
      }
      this.followUpsAdicionados = novoFollowUp
    },

    configuraDadosFollowUpBackEnd (arrayInformacoes) {
      this.dadosFollowUps.push(arrayInformacoes)
    },

    voltar () {
      this.isEnviando = false
      this.limpaArquivo()
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/cadastros/convenio')
    },

    trataErroConsole () {
      this.isEnviando = false
      console.info('ocorreu um erro nao tratado')
    },

    montaParametros () {
      let dataFormatada = this.proximo_contato_temporario ? stringToISODate(this.proximo_contato_temporario, true) : null

      this.itemSelecionado.pessoa = this.empresaId
      this.itemSelecionado.etapas_convenio = this.etapasConvenioSelecionado.id
      this.itemSelecionado.segmento_empresa_convenio = this.segmentoEmpresa.id
      this.itemSelecionado.motivo_nao_fechamento_convenio = this.motivoNaoFechamento.id
      this.itemSelecionado.consultor_funcionario = this.consultorFuncionario.id
      this.itemSelecionado.abrangencia_nacional = this.abrangenciaSelected
      this.itemSelecionado.beneficiario_colaboradores = this.beneficiarioFuncionario
      this.itemSelecionado.beneficiario_dependentes = this.beneficiarioDependente
      this.itemSelecionado.beneficiario_associados = this.beneficiarioAssociado
      this.itemSelecionado.nome_contato = this.nomeContato
      this.itemSelecionado.email_contato = this.emailContato
      this.itemSelecionado.telefone_contato = this.telefoneContato
      this.itemSelecionado.telefone_contato_secundario = this.telefoneContatoSecundario
      this.itemSelecionado.observacao = this.observacoesTexto
      this.itemSelecionado.justificativa_franqueadora = this.justificativaFranqueadora
      this.itemSelecionado.data_proximo_contato = dataFormatada
      this.itemSelecionado.horario_proximo_contato = this.horario_proximo_contato
      this.itemSelecionado.situacao = this.situacaoSelecionada.value
      this.itemSelecionado.follow_ups = this.dadosFollowUps
    },

    salvar () {
      this.isEnviando = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      this.montaParametros()

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(this.trataErroConsole)
      } else {
        this.criar().then(this.voltar).catch(this.trataErroConsole)
      }
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
