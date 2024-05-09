<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="true" class="form-loading screen-load">
        <load-placeholder :loading="verificaCarregamento(loadCount,3)" />
      </div>
      <div class="content-sector sector-primary p-3">
        <h5 class="title-module mb-2">Follow-Up</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <label for="nome_empresa" class="col-form-label">Nome</label>
            <input id="nome_empresa" v-model="nomeEmpresaDescricao" type="text" class="form-control" disabled readonly="readonly">
          </div>
          <div class="col-md-6">
            <label for="proximo_contato" class="col-form-label">Data 1 atendimento</label>
            <template v-if="possuiPrimeiroAtendimento === true">
              <input id="data_primeiro_atendimento" v-model="data_primeiro_atendimento" type="text" class="form-control" disabled readonly="readonly">
            </template>
            <template v-else>
              <g-datepicker :element-id="'data_primeiro_atendimento'"
                            :value="data_primeiro_atendimento"
                            :selected="setDataPrimeiroAtendimento"
                            class="valid-input"
                            placeholder="Data"/>
            </template>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="segmento_empresa" class="col-form-label">Segmento</label>
            <input id="segmento_empresa" v-model="segmentoEmpresaDescricao" type="text" class="form-control" disabled readonly="readonly">
          </div>
          <div class="col-md-6">
            <label for="nomeContato" class="col-form-label">Contato</label>
            <input id="nomeContato" v-model="nomeContato" type="text" class="form-control" disabled readonly="readonly">
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="telefoneTxt" class="col-form-label">Telefone</label>
            <input id="telefoneTxt" v-model="telefoneTxt" type="text" class="form-control" disabled readonly="readonly">
          </div>
          <div class="col-md-6">
            <label for="emailContato" class="col-form-label">E-mail Contato</label>
            <input id="emailContato" v-model="emailContato" type="text" class="form-control" disabled readonly="readonly">
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label for="proximo_contato" class="col-form-label">Próximo contato {{ $v.proximo_contato_temporario.isNotRequired === false || proximo_contato_temporario && horario_proximo_contato ? '*' : '' }}</label>
            <div class="row">
              <div class="col-md-6">
                <g-datepicker :element-id="'proximo_contato_temporario'"
                              :value="proximo_contato_temporario"
                              :selected="setProximoContatoTemporario"
                              :class="!isValid && $v.proximo_contato_temporario.isNotRequired === false && !proximo_contato_temporario ? 'invalid-input' : 'valid-input'"
                              :required="$v.proximo_contato_temporario.isNotRequired === false && !proximo_contato_temporario"
                              placeholder="Data"/>
              </div>
              <div class="col-md-6">
                <input v-mask="'##:##'" v-model="horario_proximo_contato" :class="!$v.horario_proximo_contato.validateHour ? 'is-invalid' : null" :required="$v.horario_proximo_contato.isNotRequired === false" type="text" class="form-control" maxlength="5" placeholder="Horario">
                <div class="invalid-feedback">
                  {{ (!$v.horario_proximo_contato.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <label for="consultor_responsavel_pessoa" class="col-form-label">Consultor responsável (próximo atendimento) {{ $v.consultorResponsavelFuncionario.isNotRequired === false || consultorResponsavelFuncionario.id ? '*' : '' }}</label>
            <g-select
              id="consultor_responsavel_pessoa"
              :value="consultorResponsavelFuncionario"
              :select="setConsultorResponsavelFuncionario"
              :options="listaFuncionarios"
              :class="!isValid && (!$v.consultorResponsavelFuncionario.$model || !$v.consultorResponsavelFuncionario.$model.id) && $v.consultorResponsavelFuncionario.$invalid ? 'invalid-input' : 'valid-input'"
              class="multiselect-truncate valid-input"
              label="apelido"
              track-by="id" />

            <div v-if="!isValid && (!$v.consultorResponsavelFuncionario.$model || !$v.consultorResponsavelFuncionario.$model.id) && $v.consultorResponsavelFuncionario.$invalid" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-4">
            <label for="situacaoConvenio" class="col-form-label">Situação</label>
            <input :value="retornaSituacao(itemSelecionado.situacao)" type="text" disabled="disabled" class="form-control">
          </div>
          <div class="col-md-4">
            <label for="situacaoConvenio" class="col-form-label">Situação Nova *</label>
            <g-select
              id="situacaoConvenio"
              :value="situacaoSelecionada"
              :select="setSituacao"
              :options="situacaoOpcoes"
              :disabled="bloqueiaCampoSituacao()"
              :readonly="bloqueiaCampoSituacao()"
              :class="!isValid && !situacaoSelecionada.value ? 'invalid-input' : 'valid-input'"
              :placeholder="retornaSituacao(itemSelecionado.situacao)"
              class="multiselect-truncate valid-input"
              label="text"
              track-by="value"
              required/>
            <div v-if="!isValid && !situacaoSelecionada.value" class="multiselect-invalid">
              Selecionar uma situação valida!
            </div>
          </div>
          <div class="col-md-4">
            <label for="etapas_convenio_filtro" class="col-form-label">Etapas Convenio *</label>
            <g-select
              id="etapas_convenio_filtro"
              :value="etapasConvenioSelecionado"
              :select="setEtapasConvenioSelecionado"
              :options="listaEtapasConvenio"
              :readonly="bloqueiaCampoEtapaConvenio()"
              :disabled="bloqueiaCampoEtapaConvenio()"
              :class="!isValid && (!etapasConvenioSelecionado.id || !situacaoSelecionada.value) ? 'invalid-input' : 'valid-input'"
              :placeholder="descricaoEtapaConvenio"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id"
              required/>
            <div v-if="!isValid && (!etapasConvenioSelecionado.id || !situacaoSelecionada.value)" class="multiselect-invalid">
              Selecionar uma etapa de convenio valida!
            </div>
          </div>
        </div>

        <template v-if="situacaoSelecionada.value === 'SC' || motivoNaoFechamento.id !== null">
          <div class="form-group row">
            <div class="col-md-6">
              <label for="motivo_nao_fechamento" class="col-form-label">Motivo para não fechamento *</label>
              <g-select
                id="motivo_nao_fechamento"
                :value="motivoNaoFechamento"
                :select="setMotivoNaoFechamento"
                :options="listaMotivosNaoFechamentoConvenio"
                :class="!isValid && !motivoNaoFechamento.id ? 'invalid-input' : 'valid-input'"
                :disabled="situacaoSelecionada.value !== 'SC'"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
                required/>
              <div v-if="!isValid && !motivoNaoFechamento.id" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </div>
          </div>
        </template>
        <template v-if="situacaoSelecionada.value === 'NE' || situacaoSelecionada.value === 'EN' || justificativaFranqueadora.length > 0">
          <div class="form-group row">
            <div class="col-md-12">
              <label for="observacao" class="col-form-label">Justificativa Franqueadora *</label>
              <textarea id="observacao" v-model="justificativaFranqueadora" :disabled="situacaoSelecionada.value !== 'NE' && situacaoSelecionada.value !== 'EN'" class="form-control full-textarea" maxlength="5000" rows="10" required></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - justificativaFranqueadora.length }}</span>
            </div>
          </div>
        </template>
      </div>

      <div class="content-sector sector-secondary p-3">
        <h5 class="title-module mb-2">Histórico</h5>
        <div class="form-group row">
          <div class="col-md-6">
            <button v-b-modal.followUpGeral type="button" class="btn btn-azul">
              Adicionar Follow-Up
            </button>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <textarea id="observacao_follow_up" v-model="followUpsAdicionados" class="form-control" rows="10" readonly></textarea>
          </div>
        </div>
      </div>

      <div class="form-group pt-2">
        <b-btn :disabled="isEnviando" variant="verde" @click="bPermaneceTela = true, salvar()">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn :disabled="isEnviando" variant="verde" @click="bPermaneceTela = false, salvar()">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <!-- Modal Follow Up -->
    <b-modal id="followUpGeral" ref="followUpGeral" v-model="visibleFollowUpGeral" size="md" centered no-close-on-backdrop hide-header hide-footer @show="abrirModalGeral">
      <modal-follow-up ref="ffGeral" :seta-dados-envio="configuraDadosFollowUpBackEnd" :seta-follow-up-callback="adicionarNovoFollowUp" :filtra-formularios="false" :formulario-convenio="true" :formulario-inicial-apenas="false" @hide="visibleFollowUpGeral = false" />
    </b-modal>
  </div>
</template>
<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {validateHour} from '../../utils/validators'
import {required, requiredIf} from 'vuelidate/lib/validators'
import {stringToISODate, dateToString, converteHorarioParaBanco, converteHorarioBancoParaInputText} from '../../utils/date'
import ModalFollowUp from '../interessados/ModalFollowUp.vue'

function isNotRequired (value, vm) {
  return ((!vm.proximo_contato_temporario && !vm.horario_proximo_contato && !vm.consultorResponsavelFuncionario.id) ||
  (!!vm.proximo_contato_temporario && !!vm.horario_proximo_contato && !!vm.consultorResponsavelFuncionario.id))
}

function etapaConvenioValido (value, vm) {
  return vm.etapasConvenioSelecionado.id !== null
}

export default {
  name: 'FormularioFollowupConvenio',
  components: {
    ModalFollowUp
  },
  data () {
    return {
      loadCount: 0,
      isValid: true,
      bPermaneceTela: false,
      bVeioListagemFollowup: false,
      visibleFollowUpGeral: false,
      isEnviando: false,
      possuiPrimeiroAtendimento: false,
      descricaoEtapaConvenio: 'Selecione',
      justificativaFranqueadora: '',
      nomeEmpresaDescricao: '',
      followUpsAdicionados: '',
      segmentoEmpresaDescricao: '',
      nomeContato: '',
      telefoneTxt: '',
      emailContato: '',
      data_primeiro_atendimento: '',
      proximo_contato_temporario: '',
      horario_proximo_contato: '',
      situacaoSelecionada: '',
      etapasConvenioSelecionado: {id: null, descricao: 'Selecione'},
      motivoNaoFechamento: {id: null, descricao: 'Selecione'},
      consultorResponsavelFuncionario: {id: null, apelido: 'Selecione'},
      dadosFollowUps: [],
      listaEtapasConvenio: [],
      situacaoOpcoes: [
        {text: 'Ativo', value: 'ATI'},
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
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),
    ...mapState('motivoNaoFechamentoConvenio', {listaMotivoNaoFechamentoConvenioRequisicao: 'lista'}),

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

    bloqueiaAlterarSituacao: {
      get () {
        return this.$store.state.root.usuarioLogado.pertenceFranqueadora === false
      }
    }
  },
  validations: {
    consultorResponsavelFuncionario: { isNotRequired },
    situacaoSelecionada: { required },
    etapasConvenioSelecionado: { required, etapaConvenioValido },
    proximo_contato_temporario: { isNotRequired },
    horario_proximo_contato: { validateHour, isNotRequired },
    justificativaFranqueadora: {
      required: requiredIf(function () {
        return ((this.situacaoSelecionada.value === 'NE') || (this.situacaoSelecionada.value === 'EN'))
      })
    },
    motivoNaoFechamento: {
      required: requiredIf(function () {
        return this.situacaoSelecionada.value === 'SC'
      })
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
    if (this.$route.query.bVeioListagemFollowup) {
      this.bVeioListagemFollowup = true
    }
    this.preparaDadosTela()
  },
  methods: {
    ...mapMutations('convenio', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('convenio', ['buscar', 'followup']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      if (this.bPermaneceTela === true) {
        window.location.reload()
      } else {
        if (this.bVeioListagemFollowup === true) {
          this.$router.push('/comercial/follow-up')
          return
        }
        this.$router.push('/cadastros/convenio')
      }
    },

    retornaSituacao (situacao) {
      let sitDesc = ''
      switch (situacao) {
        case 'ATI':
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

    bloqueiaCampoSituacao () {
      if ((this.bloqueiaAlterarSituacao === true) && (this.itemSelecionado.situacao === 'PV')) {
        return true
      }

      return false
    },

    bloqueiaCampoEtapaConvenio () {
      const bloqueiaPorSituacaoNova = (this.situacaoSelecionada.value === 'PV') || (this.situacaoSelecionada.value === 'NE')
      const bloqueiaPorSituacaoNegadaENova = (this.itemSelecionado.situacao === 'NE') && bloqueiaPorSituacaoNova

      const bloqueiaPorSituacao = (this.bloqueiaAlterarSituacao === true) && (this.itemSelecionado.situacao === 'PV')

      if ((bloqueiaPorSituacao) || (bloqueiaPorSituacaoNegadaENova) || (this.situacaoSelecionada === '')) {
        return true
      }

      return false
    },

    aplicaFiltroSituacoes () {
      if (this.itemSelecionado.situacao === 'PV') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'EN') || (item.value === 'NE')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'EN') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'ATI') || (item.value === 'EN') || (item.value === 'RF') || (item.value === 'SC')
        })
        this.situacaoOpcoes = listaSituacao
      } else if ((this.itemSelecionado.situacao === 'RF') || (this.itemSelecionado.situacao === 'SC')) {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'EN')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'ATI') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'I')
        })
        this.situacaoOpcoes = listaSituacao
      } else if (this.itemSelecionado.situacao === 'I') {
        let listaSituacao = this.situacaoOpcoes.filter((item) => {
          return (item.value === 'ATI')
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
        if (this.situacaoSelecionada.value === 'ATI') {
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
      if ((this.itemSelecionado.situacao === 'ATI') || (this.itemSelecionado.situacao === 'I')) {
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

    setSituacao (value) {
      this.situacaoSelecionada = value
      if ((this.itemSelecionado.situacao !== 'NE') && (this.situacaoSelecionada.value !== 'PV')) {
        this.etapasConvenioSelecionado = {id: null, descricao: 'Selecione'}
      }
      this.retornaListaRequisicaoEtapasFranqueada()
    },

    setProximoContatoTemporario (value) {
      this.proximo_contato_temporario = value
    },

    setDataPrimeiroAtendimento (value) {
      this.data_primeiro_atendimento = value
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

    setConsultorResponsavelFuncionario (value) {
      this.consultorResponsavelFuncionario = value
    },

    listaCamposDinamicos () {
      this.$store.commit('etapasConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('etapasConvenio/buscarTodos').then(this.countCarregamento)
      this.$store.commit('motivoNaoFechamentoConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('motivoNaoFechamentoConvenio/listar').then(this.countCarregamento)
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('funcionario/listar').then(this.countCarregamento)
    },

    preparaDadosTela () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.listaCamposDinamicos()
      const id = this.$route.params.id
      if (id) {
        this.SET_ITEM_SELECIONADO_ID(id)
        this.setConsultorResponsavelFuncionario({id: null, apelido: 'Selecione'})
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
    montaDadosRequisicaoParaTela () {
      if (this.itemSelecionado.data_primeiro_atendimento !== undefined) {
        this.possuiPrimeiroAtendimento = true
        this.data_primeiro_atendimento = this.itemSelecionado.data_primeiro_atendimento ? dateToString(new Date(this.itemSelecionado.data_primeiro_atendimento)) : null
      }
      if (this.itemSelecionado.data_proximo_contato !== undefined) {
        this.proximo_contato_temporario = this.itemSelecionado.data_proximo_contato ? dateToString(new Date(this.itemSelecionado.data_proximo_contato)) : null
      }
      if (this.itemSelecionado.horario_proximo_contato !== undefined) {
        this.horario_proximo_contato = this.itemSelecionado.horario_proximo_contato ? converteHorarioBancoParaInputText(this.itemSelecionado.horario_proximo_contato) : null
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
        this.consultorResponsavelFuncionario = this.listaFuncionarios.filter((item) => {
          return this.itemSelecionado.consultor_funcionario.id === item.id
        })[0]
      }
      if (this.itemSelecionado.motivo_nao_fechamento_convenio !== undefined) {
        this.motivoNaoFechamento = this.listaMotivosNaoFechamentoConvenio.filter(item => { return item.id === this.itemSelecionado.motivo_nao_fechamento_convenio.id })[0]
      }
      if (this.itemSelecionado.justificativa_franqueadora !== undefined) {
        this.justificativaFranqueadora = this.itemSelecionado.justificativa_franqueadora
      }
      if (this.itemSelecionado.segmento_empresa_convenio !== undefined) {
        this.segmentoEmpresaDescricao = this.itemSelecionado.segmento_empresa_convenio.descricao
      }
      if (this.itemSelecionado.etapas_convenio !== undefined) {
        this.descricaoEtapaConvenio = this.itemSelecionado.etapas_convenio.descricao
      }
      this.nomeContato = this.itemSelecionado.nome_contato
      this.telefoneTxt = (this.itemSelecionado.telefone_contato_secundario ? this.itemSelecionado.telefone_contato + '/' + this.itemSelecionado.telefone_contato_secundario : this.itemSelecionado.telefone_contato)
      this.emailContato = this.itemSelecionado.email_contato
      this.nomeEmpresaDescricao = this.itemSelecionado.pessoa.nome_contato
    },

    montaParametros () {
      let dataFormatadaProximoContato = this.proximo_contato_temporario ? stringToISODate(this.proximo_contato_temporario, true) : null
      let dataFormatadaPrimeiroAtendimento = this.data_primeiro_atendimento ? stringToISODate(this.data_primeiro_atendimento, true) : null
      return {
        data_primeiro_atendimento: dataFormatadaPrimeiroAtendimento,
        consultor_funcionario: this.consultorResponsavelFuncionario.id,
        etapas_convenio: this.etapasConvenioSelecionado.id,
        motivo_nao_fechamento_convenio: this.motivoNaoFechamento.id,
        justificativa_franqueadora: this.justificativaFranqueadora,
        data_proximo_contato: dataFormatadaProximoContato,
        horario_proximo_contato: converteHorarioParaBanco(this.horario_proximo_contato),
        situacao: this.situacaoSelecionada.value,
        follow_ups: this.dadosFollowUps
      }
    },

    trataErroConsole () {
      this.isEnviando = false
      console.info('ocorreu um erro nao tratado')
    },

    salvar (bSalvarESair) {
      this.isEnviando = true

      if (this.$v.$invalid || (this.situacaoSelecionada.value === 'SC' && this.motivoNaoFechamento.id === null)) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      let parametros = this.montaParametros()
      this.followup(parametros).then(this.voltar).catch(this.trataErroConsole)
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
