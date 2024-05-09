<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" aria-controls="filtros-rapidos" aria-expanded="true" class="btn filtro-selecionado" @click="filtroRapido = !filtroRapido, className = filtroRapido ? 'filtro-open' : null, filtroSelecionado = 1">
            Filtros
          </div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/cadastros/convenio/adicionar" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="busca_empresa" class="col-form-label">Nome fantasia</label>
              <typeahead id="busca_empresa" :item-hit="setEmpresaId" source-path="/api/convenio/buscar/empresa" key-name="pessoa.nome_fantasia" />
            </div>
            <div class="col-md-4">
              <label for="pessoa_contato" class="col-form-label">Pessoa de Contato</label>
              <input id="pessoa_contato" v-model="pessoaContato" type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="segmento_empresa" class="col-form-label">Segmento</label>
              <g-select
                id="segmento_empresa"
                :value="segmentoEmpresaFiltro"
                :select="setSegmentoEmpresa"
                :options="listaSegmentoEmpresa"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
          </div>
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="consultor_funcionario_filtro_rapido" class="col-form-label">Consultor</label>
              <g-select
                id="consultor_funcionario_filtro_rapido"
                :value="consultorFiltro"
                :select="setConsultorFiltro"
                :options="listaFuncionarios"
                class="multiselect-truncate valid-input"
                label="apelido"
                track-by="id" />
            </div>
            <div class="col-md-4">
              <label for="etapas_convenio_filtro" class="col-form-label">Etapas Convenio</label>
              <g-select
                id="etapas_convenio_filtro"
                :value="etapasConvenioFiltro"
                :select="setEtapasConvenioFiltro"
                :options="listaEtapasConvenio"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
            <div class="col-md-4">
              <label for="situacaoConvenio" class="col-form-label">Situação</label>
              <g-select
                id="situacaoConvenio"
                :value="situacaoSelecionada"
                :select="setSituacao"
                :options="listaDeSituacao"
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id" />
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-md-6">
              <label class="col-form-label">Data de próximo contato</label>

              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_proximo_contato_de_proximo_de'" :value="data_proximo_contato_de_temporario" :selected="setDataProximoContatoDeTemporario"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_proximo_contato_de_proximo_ate'" :value="data_proximo_contato_ate_temporario" :selected="setDataProximoContatoAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dateToCompare(data_proximo_contato_de_temporario) > dateToCompare(data_proximo_contato_ate_temporario) && data_proximo_contato_ate_temporario !== ''" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-6">
              <label class="col-form-label">Horário de próximo contato</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <input v-mask="'##:##'" v-model="horario_proximo_contato_de" :class="{'is-invalid' : $v.horario_proximo_contato_de.$invalid}" type="text" class="form-control" maxlength="5" >
                    <div class="invalid-feedback">
                      <span v-if="horaAMaiorHoraB(horario_proximo_contato_de, horario_proximo_contato_ate) && horario_proximo_contato_ate !== ''">Hora inicial deve ser menor que a hora final!</span>
                      <span v-else>Horário inválido</span>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <input v-mask="'##:##'" v-model="horario_proximo_contato_ate" :class="{'is-invalid' : $v.horario_proximo_contato_ate.$invalid}" type="text" class="form-control" maxlength="5" >
                    <div class="invalid-feedback">Horário inválido</div>
                  </div>
                </div>
              </div>

            </div>

          </div>
          <button :disabled="dataFiltroInvalida() || horarioFiltroInvalido()" type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroRapido=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="p.nome_fantasia">Nome fantasia</th>
            <th v-b-tooltip.viewport.down data-column="conv.nome_contato" title="Pessoa de Contato" class="d-block text-truncate">Pessoa de Contato</th>
            <th data-column="conv.telefone_contato">Telefone</th>
            <th data-column="cf.apelido">Consultor</th>
            <th v-b-tooltip data-column="conv.data_proximo_contato, conv.horario_proximo_contato" title="Próximo contato" class="coluna-data d-block text-truncate size-205 m-size-150">Próximo contato</th>
            <th data-column="ec.descricao">Estado atual</th>
            <th data-column="conv.situacao" class="coluna-situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in listaItems" :key="item.id" @dblclick="alterarConvenio(item)">
              <td data-label="Empresa">
                {{ item.pessoa.nome_fantasia }}
              </td>
              <td v-b-tooltip :title="item.nome_contato" data-label="Pessoa de Contato" class="d-block text-truncate">
                {{ item.nome_contato }}
              </td>
              <td data-label="Telefone">
                {{ getNumeroContato(item) }}
                <!-- {{ item.telefone_contato ? (item.telefone_contato_secundario ? item.telefone_contato + '/' + item.telefone_contato_secundario : item.telefone_contato) : '' }} -->
              </td>
              <td data-label="Consultor">
                {{ item.consultor_funcionario ? item.consultor_funcionario.apelido : '' }}
              </td>
              <td data-label="Próximo contato" class="coluna-data size-205 m-size-150">
                {{ item.data_proximo_contato | formatarData }} {{ (item.horario_proximo_contato ? item.horario_proximo_contato : "") | formatarHoraDB }}
              </td>
              <td data-label="Estado atual">
                {{ item.etapas_convenio ? item.etapas_convenio.descricao : '' }}
              </td>
              <td data-label="Situação" class="coluna-situacao">
                {{ item.situacao ? getSituacao(item) : '' }}
              </td>
              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Atualizar" class="icone-link" @click.prevent="alterarConvenio(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <a href="javascript:void(0)" title="Visualizar follow up" class="icone-link" @click.prevent="openFollowUp(item)">
                  <font-awesome-icon icon="file" />
                </a>
                <template v-if="objFranqueada.franqueadora">
                  <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true) && item.situacao === 'PV' || item.situacao ==='EN'" href="javascript:void(0)" title="Aprovar" class="icone-link" @click.prevent="verStatus(item)">
                    <font-awesome-icon icon="thumbs-up" />
                  </a>
                </template>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="info-btn"></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>

    <b-modal id="visualizacaoFollowUp" ref="refVisualizacaoFollowUp" v-model="visualizacaoFollowUp" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <formulario-convenio ref="modelFormularioConvenio" @closeModel="closeModel"/>
    </b-modal>

    <!-- <b-modal id="convenioAprovacaoModal" ref="convenioAprovacaoModal" v-model="visibleAprovarConvenioModal" size="md" centered no-close-on-backdrop hide-header hide-footer>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvarModal()">
        <div class="form-group row">
          <div class="col-md-12">
            <label for="justificativa_modal" class="col-form-label">Justificativa Franqueadora *</label>
            <textarea id="justificativa_modal" v-model="justificativaFranqueadoraModal" class="form-control full-textarea" maxlength="5000" rows="10" required></textarea>
            <span class="text-secondary">Limite de caracteres: {{ 5000 - justificativaFranqueadoraModal.length }}</span>
          </div>
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <label for="etapas_convenio_modal" class="col-form-label">Etapas Convenio *</label>
            <g-select
              id="etapas_convenio_modal"
              :value="etapasConvenioModal"
              :select="setEtapasConvenioSelecionadoModal"
              :options="listaEtapasConvenioModal"
              class="multiselect-truncate valid-input"
              label="descricao"
              track-by="id"
              required/>
          </div>
        </div>
        <div class="p-3 d-flex justify-content-center">
          <b-btn :disabled="isEnviando || !isCamposObrigatoriosPreenchidosModal()" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
          <b-btn variant="link" @click="finalizar()">Cancelar</b-btn>
        </div>
      </form>
    </b-modal> -->
  </div>
</template>

<script>
import FormularioConvenio from './Formulario'
import {mapState, mapActions, mapMutations} from 'vuex'
import {validateHour} from '../../utils/validators'
import {requiredIf} from 'vuelidate/lib/validators'
import {beginOfDay, endOfDay, stringToISODate, dateToString, dateToCompare, horaAMaiorHoraB} from '../../utils/date'

const validaHora = (value, vm) => {
  if (value.length === 5) {
    if (vm.horario_proximo_contato_ate.length === 5 && horaAMaiorHoraB(vm.horario_proximo_contato_de, vm.horario_proximo_contato_ate)) {
      return false
    }
    return validateHour(value)
  }
  return true
}

export default {
  name: 'ListaConvenio',
  components: {
    'formulario-convenio': FormularioConvenio
  },
  data () {
    return {
      className: '',
      pessoaContato: '',
      data_proximo_contato_de_temporario: '',
      data_proximo_contato_ate_temporario: '',
      horario_proximo_contato_de: '',
      horario_proximo_contato_ate: '',
      justificativaFranqueadoraModal: '',
      visibleAprovarConvenioModal: false,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: false,
      isValid: true,
      isEnviando: false,
      filtroSelecionado: 1,
      empresaId: null,
      listaEtapasConvenioModal: [],
      situacaoSelecionada: {id: null, descricao: 'Selecione', tipo_workflow: null},
      segmentoEmpresaFiltro: {id: null, descricao: 'Selecione'},
      etapasConvenioFiltro: {id: null, descricao: 'Selecione'},
      etapasConvenioModal: {id: null, descricao: 'Selecione'},
      consultorFiltro: {id: null, apelido: 'Selecione'},
      visualizacaoFollowUp: false
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('root', ['usuarioLogado']),
    ...mapState('convenio', {listaItems: 'lista', estaCarregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados', itemSelecionado: 'itemSelecionado', totalItens: 'totalItens'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('segmentoEmpresaConvenio', {listaSegmentoEmpresaRequisicao: 'lista'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),
    ...mapState('negociacaoParceriaWorkflow', {listaNegociacaoParceriaWorkflow: 'lista'}),
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

    listaDeSituacao: {
      get () {
        return [{id: null, descricao: 'Selecione', tipo_workflow: null}, ...this.listaNegociacaoParceriaWorkflow]
      }
    },

    listaEtapasConvenio: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao]
      }
    },

    bloquearAprovacaoModal: {
      get () {
        return this.usuarioLogado.pertenceFranqueadora === false
      }
    },

    permitirCarregarMais: {
      get () {
        return !!this.listaItems.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.filtrar()
    this.listarCamposSelects()
  },
  validations: {
    justificativaFranqueadoraModal: {
      required: requiredIf(function () {
        return this.visibleAprovarConvenioModal === true
      })
    },
    etapasConvenioModal: {
      required: requiredIf(function () {
        return this.visibleAprovarConvenioModal === true
      })
    },
    horario_proximo_contato_de: {validaHora},
    horario_proximo_contato_ate: {validaHora}
  },
  methods: {
    ...mapActions('convenio', {listarItems: 'listar', atualizar: 'atualizar'}),
    ...mapMutations('convenio', ['SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_ETAPAS_CONVENIO_ID', 'SET_FILTRO_CONSULTOR_FUNCIONARIO_ID', 'SET_FILTRO_SEGMENTO_EMPRESA_CONVENIO_ID', 'SET_FILTRO_NOME_CONTATO', 'SET_FILTRO_SITUACAO', 'SET_FILTRO_DATA_PROXIMO_CONTATO_DE', 'SET_FILTRO_DATA_PROXIMO_CONTATO_ATE', 'SET_FILTRO_HORARIO_PROXIMO_CONTATO_DE', 'SET_FILTRO_HORARIO_PROXIMO_CONTATO_ATE', 'SET_FILTRO_EMPRESA_PESSOA_ID', 'SET_FILTRO_USUARIO_FRANQUEADORA', 'SET_ORDER_BY', 'SET_LISTA']),

    dateToCompare: dateToCompare,
    horaAMaiorHoraB: horaAMaiorHoraB,

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.filtrar()
    },

    openFollowUp (item) {
      item.historico = ''
      item.followupConvenios.forEach((followUp) => {
        item.historico += followUp.followup + '\n'
      })
      this.visualizacaoFollowUp = true
      setTimeout(() => {
        this.$refs.modelFormularioConvenio.form.historico = item.historico
        this.$refs.modelFormularioConvenio.resumoFollowUp = true
      }, 1)
    },

    closeModel () {
      this.visualizacaoFollowUp = false
      setTimeout(() => {
        this.$refs.modelFormularioConvenio.resumoFollowUp = false
        this.LIMPAR_ITEM_SELECIONADO()
      }, 1000)
    },

    getNumeroContato (item) {
      if (item.telefone_contato) {
        return item.telefone_contato
      }
      if (item.telefone_contato_secundario) {
        return item.telefone_contato_secundario
      }
    },

    getSituacao (item) {
      const situacao = item.situacao
      const lista = this.listaNegociacaoParceriaWorkflow || []
      const object = lista.find(wf => wf.tipo_workflow === situacao)

      return object ? object.descricao : ''
    },

    isCamposObrigatoriosPreenchidosModal () {
      if ((this.justificativaFranqueadoraModal.trim().length > 0) && (this.etapasConvenioModal.id !== null)) {
        return true
      }
      return false
    },

    aprovaDesaprova (bAprovar, item) {
      let itemSelecionado = Object.assign({}, item)
      this.SET_ITEM_SELECIONADO(itemSelecionado)
      this.SET_ITEM_SELECIONADO_ID(itemSelecionado.id)
      if (bAprovar === true) {
        this.itemSelecionado.situacao = 'EN'
        this.listaEtapasConvenioModal = this.listaEtapasConvenio.filter((item) => {
          return (((item.parceria_firmada === false) && (item.retira_fluxo === false)) || (item.id === null))
        })
      } else {
        this.itemSelecionado.situacao = 'NE'
        this.listaEtapasConvenioModal = this.listaEtapasConvenio.filter((item) => {
          return (((item.parceria_firmada === false) && (item.retira_fluxo === true)) || (item.id === null))
        })
      }
      this.$refs.convenioAprovacaoModal.show()
    },

    dataFiltroInvalida () {
      return dateToCompare(this.data_proximo_contato_de_temporario) > dateToCompare(this.data_proximo_contato_ate_temporario) && this.data_proximo_contato_ate_temporario !== ''
    },

    horarioFiltroInvalido () {
      return horaAMaiorHoraB(this.horario_proximo_contato_de, this.horario_proximo_contato_ate)
    },

    setDataProximoContatoDeTemporario (value) {
      this.data_proximo_contato_de_temporario = value
    },

    setDataProximoContatoAteTemporario (value) {
      this.data_proximo_contato_ate_temporario = value
    },

    setConsultorFiltro (value) {
      this.consultorFiltro = value
    },

    setEtapasConvenioFiltro (value) {
      this.etapasConvenioFiltro = value
    },

    setEtapasConvenioSelecionadoModal (value) {
      this.etapasConvenioModal = value
      this.itemSelecionado.etapas_convenio = value.id
    },

    setEmpresaId (value) {
      if (value) {
        this.empresaId = value.pessoa.id
      } else {
        this.empresaId = null
      }
    },

    alterarConvenio (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.SET_ITEM_SELECIONADO(item)
        this.SET_ITEM_SELECIONADO_ID(item.id)
        this.$router.push(`/cadastros/convenio/atualizar/${item.id}`)
      }
    },

    verStatus (item) {
      this.$router.push(`/cadastros/convenio/iniciar/${item.id}`)
    },

    listarCamposSelects () {
      this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
      this.$store.commit('franqueadas/setFranqueadaSelecionada', this.$store.state.root.usuarioLogado.franqueadaSelecionada)
      this.$store.dispatch('franqueadas/buscarParametros')

      this.$store.commit('etapasConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.commit('etapasConvenio/SET_LISTA', [])
      this.$store.dispatch('etapasConvenio/buscarTodos')

      // Campos estão sendo importados no formulario-convenio
      // this.$store.commit('segmentoEmpresaConvenio/SET_PAGINA_ATUAL', 1)
      // this.$store.commit('segmentoEmpresaConvenio/SET_LISTA', [])
      // this.$store.dispatch('segmentoEmpresaConvenio/listar')

      // this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      // this.$store.commit('funcionario/SET_LISTA', [])
      // this.$store.dispatch('funcionario/listar')

      // this.$store.commit('negociacaoParceriaWorkflow/SET_PAGINA_ATUAL', 1)
      // this.$store.commit('negociacaoParceriaWorkflow/SET_LISTA', [])
      // this.$store.dispatch('negociacaoParceriaWorkflow/listar')
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
    },

    setSegmentoEmpresa (value) {
      this.segmentoEmpresaFiltro = value
    },

    limparStateAnterior () {
      this.SET_FILTRO_EMPRESA_PESSOA_ID(null)
      this.SET_FILTRO_ETAPAS_CONVENIO_ID(null)
      this.SET_FILTRO_CONSULTOR_FUNCIONARIO_ID(null)
      this.SET_FILTRO_SEGMENTO_EMPRESA_CONVENIO_ID(null)
      this.SET_FILTRO_NOME_CONTATO(null)
      this.SET_FILTRO_SITUACAO(null)
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_DE(null)
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_ATE(null)
      this.SET_FILTRO_HORARIO_PROXIMO_CONTATO_DE(null)
      this.SET_FILTRO_HORARIO_PROXIMO_CONTATO_ATE(null)
      this.SET_FILTRO_USUARIO_FRANQUEADORA(null)
    },

    executaFiltroRapido () {
      let dataIni = (this.data_proximo_contato_de_temporario ? beginOfDay(this.data_proximo_contato_de_temporario) : null)
      let dataFim = (this.data_proximo_contato_ate_temporario ? endOfDay(this.data_proximo_contato_ate_temporario) : null)
      let horaIni = (this.horario_proximo_contato_de.length === 2 ? this.horario_proximo_contato_de + ':00' : this.horario_proximo_contato_de)
      let horaFim = (this.horario_proximo_contato_ate.length === 2 ? this.horario_proximo_contato_ate + ':00' : this.horario_proximo_contato_ate)

      this.SET_FILTRO_EMPRESA_PESSOA_ID(this.empresaId)
      this.SET_FILTRO_ETAPAS_CONVENIO_ID(this.etapasConvenioFiltro.id)
      this.SET_FILTRO_CONSULTOR_FUNCIONARIO_ID(this.consultorFiltro.id)
      this.SET_FILTRO_SEGMENTO_EMPRESA_CONVENIO_ID(this.segmentoEmpresaFiltro.id)
      this.SET_FILTRO_NOME_CONTATO(this.pessoaContato)
      this.SET_FILTRO_SITUACAO(this.situacaoSelecionada.tipo_workflow)
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_DE(dataIni)
      this.SET_FILTRO_DATA_PROXIMO_CONTATO_ATE(dataFim)
      this.SET_FILTRO_HORARIO_PROXIMO_CONTATO_DE(horaIni)
      this.SET_FILTRO_HORARIO_PROXIMO_CONTATO_ATE(horaFim)
      this.SET_FILTRO_USUARIO_FRANQUEADORA(this.usuarioLogado.pertenceFranqueadora)
      if (this.usuarioLogado.pertenceFranqueadora) {
        if (this.usuarioLogado.franqueada_padrao.id !== this.usuarioLogado.franqueadaSelecionada) {
          this.SET_FILTRO_USUARIO_FRANQUEADORA(false)// Se tiver como false, quer dizer que o usuario é da franqueadora
        }
      }
    },

    filtrar () {
      this.limparStateAnterior()
      this.executaFiltroRapido()
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    },

    carregarMais () {
      this.listarItems()
    },

    limparModal () {
      this.isEnviando = false
      this.isValid = true
      this.justificativaFranqueadoraModal = ''
      this.etapasConvenioModal = {id: null, descricao: 'Selecione'}
      this.LIMPAR_ITEM_SELECIONADO()
      this.filtrar()
    },

    trataErroConsole () {
      this.isEnviando = false
      console.info('ocorreu um erro nao tratado')
    },

    salvarModal () {
      this.isEnviando = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      if (typeof this.itemSelecionado.motivo_nao_fechamento_convenio === 'object') {
        this.itemSelecionado.motivo_nao_fechamento_convenio = this.itemSelecionado.motivo_nao_fechamento_convenio.id
      }

      if (typeof this.itemSelecionado.pessoa === 'object') {
        this.itemSelecionado.pessoa = this.itemSelecionado.pessoa.id
      }
      if (this.itemSelecionado.consultor_funcionario !== undefined) {
        if (typeof this.itemSelecionado.consultor_funcionario === 'object') {
          this.itemSelecionado.consultor_funcionario = this.itemSelecionado.consultor_funcionario.id
        }
      }
      if (this.itemSelecionado.segmento_empresa_convenio !== undefined) {
        if (typeof this.itemSelecionado.segmento_empresa_convenio === 'object') {
          this.itemSelecionado.segmento_empresa_convenio = this.itemSelecionado.segmento_empresa_convenio.id
        }
      }

      if (this.itemSelecionado.data_proximo_contato !== undefined) {
        let dataConvertido = this.itemSelecionado.data_proximo_contato ? dateToString(new Date(this.itemSelecionado.data_proximo_contato)) : null
        let dataFormatada = dataConvertido ? stringToISODate(dataConvertido, true) : null
        this.itemSelecionado.data_proximo_contato = dataFormatada
      }
      if (this.itemSelecionado.horario_proximo_contato !== undefined) {
        this.itemSelecionado.horario_proximo_contato = this.itemSelecionado.horario_proximo_contato ? this.itemSelecionado.horario_proximo_contato.match(/(\d{2,2}):(\d{2,2})/)[0] : null
      }

      this.itemSelecionado.justificativa_franqueadora = this.justificativaFranqueadoraModal
      this.itemSelecionado.etapas_convenio = this.etapasConvenioModal.id

      this.atualizar().then(this.finalizar).catch(this.trataErroConsole)
    },

    finalizar (action = 'cancel') {
      this.limparModal()
      this.$refs.convenioAprovacaoModal.hide()
    },

    limparFiltros () {
      this.empresaId = null
      this.pessoaContato = ''
      this.data_proximo_contato_de_temporario = ''
      this.data_proximo_contato_ate_temporario = ''
      this.horario_proximo_contato_de = ''
      this.horario_proximo_contato_ate = ''
      this.etapasConvenioFiltro = {id: null, descricao: 'Selecione'}
      this.segmentoEmpresaFiltro = {id: null, descricao: 'Selecione'}
      this.consultorFiltro = {id: null, apelido: 'Selecione'}
      this.situacaoSelecionada = {id: null, descricao: 'Selecione', tipo_workflow: null}
    }
  }
}
</script>
<style scoped>
span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  /* color: #4a69c5; */
  color: #151B1E;
  background-color: #fff;
  /* cursor: default; */
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: .5rem;
  padding-bottom: .5rem;
  color: #3e515b;
  border-top: 1px solid #EBECF0;
}

.table-sm .coluna-icones {
  max-width: 70px;
}
.m-size-150 {
  min-width: 150px;
}
</style>
