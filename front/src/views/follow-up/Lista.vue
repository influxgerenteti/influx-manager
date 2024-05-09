<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>

        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <b-col md="4">
              <label v-help-hint="'filtroRapido-followup-data_criacao'" class="col-form-label" for="data_criacao_de">Data criação do follow up</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_criacao_de'" v-model="data_criacao_de" @input="setDataCriacaoDe" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_criacao_ate'" v-model="data_criacao_ate" @input="setDataCriacaoAte" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_criacao_de,data_criacao_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtroRapido-tipo-followup'" for="tipo-followup-rapido" class="col-form-label">Tipo follow up</label>
              <g-select :id="'tipo-followup-rapido'"
                        :select="setTipoFollowupSelecionado"
                        :value="tipoFollowupSelecionado"
                        :options="tipoFollowup"
                        class="multiselect-truncate"
                        label="text"
                        track-by="value"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-none'" :for="`nome_aluno${tipoFollowupSelecionado.value}`" class="col-form-label">Nome/Razão social</label>
              <typeahead v-show="tipoFollowupSelecionado.value === 1" id="nome1" ref="tpInteressadoRapido" :item-hit="setNome" source-path="/api/interessado/buscar-nome" key-name="nome" />
              <typeahead v-show="tipoFollowupSelecionado.value === 2" id="nome2" ref="tpConvenioRapido" :item-hit="setConvenioRapido" source-path="/api/convenio/buscar-nome" key-name="pessoa.nome_contato" />
              <typeahead v-show="tipoFollowupSelecionado.value === 3" id="nome3" ref="tpAlunoRapido" :item-hit="setAlunoRapido" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-followup-atendente'" for="atendente" class="col-form-label">Consultor Responsavel</label>
              <g-select :id="'atendente'"
                        :select="setFuncionario"
                        :value="funcionario"
                        :options="listaDeFuncionarioConsultor"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-followup-tipo-contato'" for="tipo-contato" class="col-form-label">Tipo contato</label>
              <g-select :id="'tipo-contato'"
                        :select="setTipoContato"
                        :value="tipo_contato"
                        :options="tipoContato"
                        :disabled="bloquearCamposParaConvenio"
                        class="multiselect-truncate"
                        label="text"
                        track-by="id"
              />
            </b-col>

          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada=true ,filtrar()">
          <div class="form-group row mb-0">
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-followup-data_criacao'" class="col-form-label" for="data_criacao_fp_de">Data criação do follow up</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_criacao_fp_de'" v-model="data_criacao_fp_de" @input="setDataCriacaoFpDe" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_criacao_fp_ate'" v-model="data_criacao_fp_ate" @input="setDataCriacaoFpAte" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_criacao_fp_de,data_criacao_fp_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-tipo-followup'" for="tipo-followup-avancado" class="col-form-label">Tipo follow up</label>
              <g-select :id="'tipo-followup-avancado'"
                        :select="setTipoFollowupSelecionado"
                        :value="tipoFollowupSelecionado"
                        :options="tipoFollowup"
                        class="multiselect-truncate"
                        label="text"
                        track-by="value"
              />
            </b-col>
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-none'" for="nome-avancado" class="col-form-label">Nome/Razão social</label>
              <typeahead v-show="tipoFollowupSelecionado.value === 1" id="nome-avancado1" ref="tpInteressadoAvancado" :item-hit="setNomeAvancado" source-path="/api/interessado/buscar-nome" key-name="nome" />
              <typeahead v-show="tipoFollowupSelecionado.value === 2" id="nome-avancado2" ref="tpConvenioAvancado" :item-hit="setConvenioAvancado" source-path="/api/convenio/buscar-nome" key-name="pessoa.nome_contato" />
              <typeahead v-show="tipoFollowupSelecionado.value === 3" id="nome-avancado3" ref="tpAlunoAvancado" :item-hit="setAlunoAvancado" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
            </b-col>
            <b-col md="3">
              <label v-help-hint="'filtroRapido-followup-consultor-responsavel'" for="consultor-responsavel-avancado" class="col-form-label">Consultor Responsavel</label>
              <g-select :id="'consultor-responsavel-avancado'"
                        :select="setConsultorResponsavelFuncionarioAvancado"
                        :value="consultor_responsavel_funcionario_avancado"
                        :options="listaDeFuncionarioConsultor"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </b-col>
          </div>
          <div class="form-group row mb-0">
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-agendaComercial-data_proximo_contato'" class="col-form-label" for="data_proximo_contato_de">Data do próximo contato</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_proximo_contato_de'" v-model="data_proximo_contato_de" @input="setDataProximoContatoDe" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_proximo_contato_ate'" v-model="data_proximo_contato_ate" @input="setDataProximoContatoAte" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_proximo_contato_de,data_proximo_contato_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-followup-tipo-contato'" for="tipo-contato-avancado" class="col-form-label">Tipo contato</label>
              <g-select :id="'tipo-contato-avancado'"
                        :select="setTipoContatoAvancado"
                        :value="tipo_contato_avancado"
                        :options="tipoContato"
                        :disabled="bloquearCamposParaConvenio"
                        class="multiselect-truncate"
                        label="text"
                        track-by="id"
              />
            </b-col>
            <b-col md="3">
              <b-form-group label="Situação (Interessado)">
                <b-form-checkbox-group
                  id="situacao-interessado-checkbox"
                  v-model="situacaoInteressadoSelecionada"
                  :options="situacaoInteressado"
                  :disabled="bloquearCamposParaConvenio"
                  name="situacao-interessado-checkbox"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                />
              </b-form-group>
            </b-col>
            <b-col md="3">
              <b-form-group label="Grau de Interesse (Interessado)">
                <b-form-checkbox-group
                  id="grau-interesse-interessado-checkbox"
                  v-model="grauInteresseInteressadoSelecionado"
                  :options="grauInteresseInteressado"
                  :disabled="bloquearCamposParaConvenio"
                  name="grau-interesse-interessado-checkbox"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                />
              </b-form-group>
            </b-col>
          </div>
          <div class="form-group row mb-0">
            <b-col md="4">
              <label v-help-hint="'filtroAvancado-aluno-turma_data_termino_contrato'" class="col-form-label" for="data_termino_contrato_de">Data término do contrato</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker :element-id="'data_termino_contrato_de'" v-model="data_termino_contrato_de" :disabled="bloquearCamposParaConvenio" @input="setDataTerminoContratoDe" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker :element-id="'data_termino_contrato_ate'" v-model="data_termino_contrato_ate" :disabled="bloquearCamposParaConvenio" @input="setDataTerminoContratoAte" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_termino_contrato_de,data_termino_contrato_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>
            <b-col md="5">
              <b-form-group label="Situação do contrato">
                <b-form-checkbox-group
                  id="situacao-contrato-aluno-checkbox"
                  v-model="situacaoContratoSelecionado"
                  :options="situacaoContrato"
                  :disabled="bloquearCamposParaConvenio"
                  name="situacao-contrato-aluno-checkbox"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                />
              </b-form-group>
            </b-col>
            <b-col md="3">
              <b-form-group label="Situação do Aluno">
                <b-form-checkbox-group
                  id="situacao-aluno-checkbox"
                  v-model="situacaoAlunoSelecionado"
                  :options="situacaoAluno"
                  :disabled="bloquearCamposParaConvenio"
                  name="situacao-aluno-checkbox"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                />
              </b-form-group>
            </b-col>
          </div>
          <button :disabled="datasFiltrosRapidoInvalidas()" type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroAvancado=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <listagem-followup-interessado v-if="tipoTabela !== 2" :is-followup-aluno="tipoFollowupSelecionado.value === 3" :lista-situacao="listaSituacaoInteressado" :lista-itens="listaInteressadosAlunos"/>
    <listagem-followup-convenio v-if="tipoTabela === 2" :lista-itens="listaConvenios" />
    <!--    <listagem-followup-aluno v-if="tipoFollowupSelecionado.value === 3" /> -->

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="tipoFollowupSelecionado.value !== 2 && totalItensInteressadoAluno > 0">{{ totalItensInteressadoAluno }} ite{{ totalItensInteressadoAluno > 1 ? 'ns' : 'm' }} encontrado{{ totalItensInteressadoAluno > 1 ? 's' : '' }}</small>
          <small v-else-if="tipoFollowupSelecionado.value === 2 && totalItensConvenio > 0">{{ totalItensConvenio }} ite{{ totalItensConvenio > 1 ? 'ns' : 'm' }} encontrado{{ totalItensConvenio > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import ListagemFollowupInteressado from './ListagemFollowupInteressado'
import ListagemFollowupConvenio from './ListagemFollowupConvenio'
// import ListagemFollowupAluno from './ListagemFollowupAluno'

export default {
  name: 'ListaFollowUp',
  components: {
    ListagemFollowupInteressado,
    ListagemFollowupConvenio
    // ListagemFollowupAluno
  },
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      botaoSubmit: false,
      bloquearCamposParaConvenio: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selected: 0,
      situacaoInteressado: [
        { value: 'A', text: 'Aberto' },
        { value: 'C', text: 'Convertido' },
        { value: 'P', text: 'Perdido' }
      ],
      grauInteresseInteressado: [
        { value: 'L', text: 'Lead' },
        { value: 'I', text: 'Interessado' },
        { value: 'H', text: 'Hotlist' }
      ],
      situacaoContrato: [
        { value: 'V', text: 'Vigente' },
        { value: 'E', text: 'Encerrado' },
        { value: 'R', text: 'Rescindido' },
        { value: 'C', text: 'Cancelado' },
        { value: 'T', text: 'Trancado' }
      ],
      situacaoAluno: [
        { value: 'ATI', text: 'Ativo' },
        { value: 'INA', text: 'Inativo' },
        { value: 'TRA', text: 'Trancado' }
      ],
      tipoFollowup: [
        // {value: 0, text: 'Todos'},
        {value: 1, text: 'Interessado'},
        {value: 3, text: 'Aluno'},
        {value: 2, text: 'Convenio'}
      ],
      listaContatoCom: [
        {id: null, descricao: 'Selecione', value: null},
        {id: 1, descricao: 'E-mail', value: 'EMAIL'},
        {id: 2, descricao: 'Telefone', value: 'TELEFONE'}
      ],
      tipoContato: [
        {id: null, text: 'Selecione', value: null},
        {id: 1, text: 'Contato Ativo', value: 'A'},
        {id: 2, text: 'Contato Receptivo', value: 'R'}
      ],
      listaSituacaoInteressado: [
        {id: 1, text: 'Concluido', value: 'C'},
        {id: 2, text: 'Não concluido', value: 'NC'}
      ],
      // Filtros Rapidos
      data_criacao_de: undefined,
      data_criacao_ate: undefined,
      contato_com: null,
      convenio_rapido: null,
      tipo_contato: null,
      funcionario: null,
      interessado: null,
      // Filtros Avançados
      data_criacao_fp_de: undefined,
      data_criacao_fp_ate: undefined,
      data_termino_contrato_de: undefined,
      data_termino_contrato_ate: undefined,
      data_proximo_contato_de: undefined,
      data_proximo_contato_ate: undefined,
      interessado_avancado: null,
      convenio_avancado: null,
      aluno_avancado: null,
      consultor_responsavel_funcionario_avancado: null,
      tipo_contato_avancado: null,
      situacaoInteressadoSelecionada: [],
      grauInteresseInteressadoSelecionado: [],
      situacaoContratoSelecionado: [],
      situacaoAlunoSelecionado: [],
      // Para ambos filtros
      tipoFollowupSelecionado: {value: 1, text: 'Interessado'},

      moke: [],

      tipoTabela: 1
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('followUp', {listaConvenios: 'listaConvenio', listaInteressadosAlunos: 'listaInteressadoAluno', estaCarregando: 'estaCarregando', totalItensInteressadoAluno: 'totalItensInteressadoAluno', totalItensConvenio: 'totalItensConvenio', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados', filtros: 'filtros'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),

    listaDeFuncionarioConsultor: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioRequisicao.filter(funcionario => funcionario.cargo.tipo !== 'ASG')]
      }
    }
  },
  mounted () {
    this.selected = 0
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA_INTERESSADO_ALUNO([])
    this.SET_LISTA_CONVENIO([])
    this.resetCamposSelects()
    this.listarCamposSelects()
    this.filtrar()
  },
  methods: {
    ...mapActions('followUp', {listarItens: 'listar'}),
    ...mapMutations('followUp', ['SET_LISTA_INTERESSADO_ALUNO', 'SET_LISTA_CONVENIO', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO']),

    datasFiltrosRapidoInvalidas () {
      if (this.dataFiltroInvalida(this.data_criacao_fp_de, this.data_criacao_fp_ate)) {
        return true
      }
      if (this.dataFiltroInvalida(this.data_proximo_contato_de, this.data_proximo_contato_ate)) {
        return true
      }
      if (this.dataFiltroInvalida(this.data_termino_contrato_de, this.data_termino_contrato_ate)) {
        return true
      }
      return false
    },

    setTipoFollowupSelecionado (value) {
      this.tipoFollowupSelecionado = value
      this.bloquearCamposParaConvenio = false

      if (this.tipoFollowupSelecionado.value === 2) {
        this.bloquearCamposParaConvenio = true
        this.data_termino_contrato_de = undefined
        this.data_termino_contrato_ate = undefined
        this.interessado_avancado = null
        this.aluno_avancado = null
        this.tipo_contato_avancado = null
        this.situacaoInteressadoSelecionada = []
        this.grauInteresseInteressadoSelecionado = []
        this.situacaoContratoSelecionado = []
        this.situacaoAlunoSelecionado = []
      }

      if (this.filtroRapido === true) {
        this.tipoTabela = value.value
        this.filtrar()
      }
    },

    setDataCriacaoFpDe (value) {
      this.data_criacao_fp_de = value
    },

    setDataCriacaoFpAte (value) {
      this.data_criacao_fp_ate = value
    },

    setDataProximoContatoDe (value) {
      this.data_proximo_contato_de = value
    },

    setDataProximoContatoAte (value) {
      this.data_proximo_contato_ate = value
    },

    setDataTerminoContratoDe (value) {
      this.data_termino_contrato_de = value
    },

    setDataTerminoContratoAte (value) {
      this.data_termino_contrato_ate = value
    },

    setTipoContatoAvancado (value) {
      this.tipo_contato_avancado = value
    },

    limparFiltros () {
      // Filtros Rapidos
      this.data_criacao_de = undefined
      this.data_criacao_ate = undefined
      this.contato_com = null
      this.tipo_contato = null
      this.funcionario = null
      this.interessado = null
      if (this.tipoFollowupSelecionado.value === 1) {
        this.$refs.tpInteressadoRapido.resetSelection()
        this.$refs.tpInteressadoAvancado.resetSelection()
      }

      if (this.tipoFollowupSelecionado.value === 2) {
        this.$refs.tpConvenioRapido.resetSelection()
        this.$refs.tpConvenioAvancado.resetSelection()
      }

      if (this.tipoFollowupSelecionado.value === 3) {
        this.$refs.tpAlunoRapido.resetSelection()
        this.$refs.tpAlunoAvancado.resetSelection()
      }

      // Filtros Avançados
      this.data_criacao_fp_de = undefined
      this.data_criacao_fp_ate = undefined
      this.interessado_avancado = null
      this.consultor_responsavel_funcionario_avancado = null
      this.data_proximo_contato_de = undefined
      this.data_proximo_contato_ate = undefined
      this.tipo_contato_avancado = null
      this.situacaoInteressadoSelecionada = []
      this.grauInteresseInteressadoSelecionado = []
      this.data_termino_contrato_de = undefined
      this.data_termino_contrato_ate = undefined
      this.situacaoContratoSelecionado = []
      this.situacaoAlunoSelecionado = []
    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    setDataCriacaoDe (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_criacao_de || this.data_criacao_de.length === 10) {
        this.filtrar()
      }
    },

    setDataCriacaoAte (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_criacao_ate || this.data_criacao_ate.length === 10) {
        this.filtrar()
      }
    },

    setTipoContato (value) {
      this.tipo_contato = value.id === null ? null : value
      this.filtrar()
    },

    setFuncionario (value) {
      this.funcionario = value.id === null ? null : value
      this.filtrar()
    },

    setNome (value) {
      this.limparTiposFollowup()
      this.interessado = value
      this.filtrar()
    },

    setConvenioRapido (value) {
      this.limparTiposFollowup()
      this.convenio_rapido = value
      this.filtrar()
    },

    setAlunoRapido (value) {
      this.limparTiposFollowup()
      this.aluno_rapido = value
      this.filtrar()
    },

    setNomeAvancado (value) {
      this.limparTiposFollowup()
      this.interessado_avancado = value
    },

    setConvenioAvancado (value) {
      this.limparTiposFollowup()
      this.convenio_avancado = value
    },

    setAlunoAvancado (value) {
      this.limparTiposFollowup()
      this.aluno_avancado = value
    },

    limparTiposFollowup () {
      this.interessado = null
      this.convenio_rapido = null
      this.aluno_rapido = null
      this.interessado_avancado = null
      this.convenio_avancado = null
      this.aluno_avancado = null
    },

    setConsultorResponsavelFuncionarioAvancado (value) {
      this.consultor_responsavel_funcionario_avancado = value
    },

    resetCamposSelects () {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
    },

    listarCamposSelects () {
      this.$store.dispatch('funcionario/listar')
    },

    limparStateAnterior () {
      this.$store.commit('followUp/SET_FILTROS', null)
    },

    executaFiltroRapido () {
      const dataCriacaoDe = this.data_criacao_de ? beginOfDay(this.data_criacao_de) : null
      const dataCriacaoAte = this.data_criacao_ate ? endOfDay(this.data_criacao_ate) : null

      const tipoLead = this.tipo_contato ? this.tipo_contato.value : null

      let interessado = this.interessado ? this.interessado.id : null
      if (this.tipoFollowupSelecionado.value === 3) {
        interessado = this.aluno_rapido && this.aluno_rapido.interessados && this.aluno_rapido.interessados.length ? this.aluno_rapido.interessados[0].id : null
      }
      const funcionario = this.funcionario ? this.funcionario.id : null
      const empresaConvenio = this.convenio_rapido && this.convenio_rapido.pessoa ? this.convenio_rapido.pessoa.id : null

      let filtros = {
        data_cadastro_de: dataCriacaoDe,
        data_cadastro_ate: dataCriacaoAte,
        interessado: interessado,
        tipo_followup_selecionado: this.tipoFollowupSelecionado.value,
        tipo_lead: tipoLead,
        consultor_responsavel_funcionario: null,
        responsavel_venda_funcionario: null,
        conveniado: empresaConvenio
      }

      if (this.tipoFollowupSelecionado.value === 1) {
        filtros.consultor_responsavel_funcionario = funcionario
      }
      if (this.tipoFollowupSelecionado.value === 3) {
        filtros.responsavel_venda_funcionario = funcionario
      }

      this.$store.commit('followUp/SET_FILTROS', filtros)
    },

    executaFiltroAvancado () {
      const dataCriacaoFpDe = this.data_criacao_fp_de ? beginOfDay(this.data_criacao_fp_de) : null
      const dataCriacaoFpAte = this.data_criacao_fp_ate ? endOfDay(this.data_criacao_fp_ate) : null
      let interessado = this.interessado_avancado ? this.interessado_avancado.id : null
      if (this.tipoFollowupSelecionado.value === 3) {
        interessado = this.aluno_avancado && this.aluno_avancado.interessados && this.aluno_avancado.interessados.length ? this.aluno_avancado.interessados[0].id : null
      }
      const consultorResponsavelFuncionarioAvancado = this.consultor_responsavel_funcionario_avancado ? this.consultor_responsavel_funcionario_avancado.id : null
      const dataProximoContatoDe = this.data_proximo_contato_de ? beginOfDay(this.data_proximo_contato_de) : null
      const dataProximoContatoAte = this.data_proximo_contato_ate ? endOfDay(this.data_proximo_contato_ate) : null
      const tipoLeadAvancado = this.tipo_contato_avancado ? this.tipo_contato_avancado.value : null
      const dataTerminoContratoDe = this.data_termino_contrato_de ? beginOfDay(this.data_termino_contrato_de) : null
      const dataTerminoContatoAte = this.data_termino_contrato_ate ? endOfDay(this.data_termino_contrato_ate) : null
      const empresaConvenio = this.convenio_avancado && this.convenio_avancado.pessoa ? this.convenio_avancado.pessoa.id : null

      let filtros = {
        data_cadastro_de: dataCriacaoFpDe,
        data_cadastro_ate: dataCriacaoFpAte,
        data_proximo_contato_de: dataProximoContatoDe,
        data_proximo_contato_ate: dataProximoContatoAte,
        data_termino_contrato_de: dataTerminoContratoDe,
        data_termino_contrato_ate: dataTerminoContatoAte,
        situacao_interessado: this.situacaoInteressadoSelecionada,
        grau_interesse: this.grauInteresseInteressadoSelecionado,
        situacao_contrato: this.situacaoContratoSelecionado,
        situacao_aluno: this.situacaoAlunoSelecionado,
        tipo_lead: tipoLeadAvancado,
        interessado: interessado,
        conveniado: empresaConvenio,
        consultor_responsavel_funcionario: null,
        responsavel_venda_funcionario: null,
        tipo_followup_selecionado: this.tipoFollowupSelecionado.value
      }

      if (this.tipoFollowupSelecionado.value === 1) {
        filtros.consultor_responsavel_funcionario = consultorResponsavelFuncionarioAvancado
      }
      if (this.tipoFollowupSelecionado.value === 3) {
        filtros.responsavel_venda_funcionario = consultorResponsavelFuncionarioAvancado
      }

      this.$store.commit('followUp/SET_FILTROS', filtros)
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else if (this.filtroSelecionado === 2) {
        this.executaFiltroAvancado()
      }
      this.tipoTabela = this.tipoFollowupSelecionado.value
      if (this.estaCarregando === false) {
        this.SET_PAGINA_ATUAL(1)
        this.SET_LISTA_INTERESSADO_ALUNO([])
        this.SET_LISTA_CONVENIO([])
        this.listarItens()
      }
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
  color: #151B1E;
  background-color: #fff;
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
.circle-badge-c { /* Cancelado */
  background-color: #00D1B2;
  color: #00D1B2;
}

.circle-badge-nc {
  background-color: #FF3860;
  color: #FF3860;
}

.table-followup.table-sm tbody tr {
  height: auto;
}
.table-followup.table-sm thead th, .table-followup.table-sm tbody td {
  white-space: unset;
}

.min-size-300 {
  min-width: 300px;
}
</style>
