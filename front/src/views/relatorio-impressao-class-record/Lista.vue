<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="abrirFiltroRapido">Filtro Rápido</div>
          <!-- <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="abrirFiltroAvancado">Avançado</div> -->
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true) && mostarBtns()" :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon  icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida = true, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label v-help-hint="'filtro-turma_descricao'" for="descricao" class="col-form-label">Descrição</label>
              <div class="d-flex">
                <input id="descricao-filtro-rapido" v-model="descricaoTemp" type="text" class="form-control" maxlength="255">
                <button type="submit" class="btn btn-azul">
                  <font-awesome-icon icon="search" />
                </button>
              </div>
            </div>

            <div class="col-auto">
              <label v-help-hint="'filtro-turma_situacao_rapido'" for="situacao_rapido" class="col-form-label">Situação</label>
              <div>
                <b-form-checkbox-group id="situacao_rapido" v-model="selectedRapidos" :options="situacao" buttons button-variant="cinza" name="situacao_rapido" class="checkbtn-line" @input="buscaRapida = true, filtrar()"/>
              </div>
            </div>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_modalidade_turma'" for="modalidade_turma" class="col-form-label">Modalidade</label>
              <g-select
                id="modalidade_turma"
                :value="modalidadeSelecionada"
                :select="setModalidadeTurma"
                :options="listaModalidadesTurma"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_curso'" for="curso" class="col-form-label">Curso</label>
              <g-select
                id="curso"
                :value="cursoSelecionado"
                :select="setCurso"
                :options="listaCursos"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_livro'" for="livro" class="col-form-label">Livro</label>
              <g-select
                id="livro"
                :value="livroSelecionado"
                :select="setLivro"
                :options="listaTemporaria"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_sala_franqueada'" for="sala_franqueada" class="col-form-label">Sala</label>
              <g-select
                id="sala_franqueada"
                :value="salaFranqueadaSelecionada"
                :select="setSalaFranqueada"
                :options="listaSalasFranqueada"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_form-horario'" for="horario" class="col-form-label">Horário</label>
              <g-select
                id="horario"
                :value="horarioSelecionado"
                :select="setHorario"
                :options="listaHorarios"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_funcionario'" for="funcionario" class="col-form-label">Instrutor</label>
              <g-select
                id="funcionario"
                :value="funcionarioSelecionado"
                :select="setFuncionario"
                :options="listaFuncionarios"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
            </div>

            <div class="col-md-6">
              <label v-help-hint="'filtro-turma_data_inicial'" class="col-form-label">Inicio entre</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>

                          <div class="datepicker-input">
                              <v-date-picker v-model="data_inicial_temporario" >
                                <template v-slot="{ inputValue, inputEvents }">
                                  <input class="form-control"
                                    :element-id="'data_inicial_temporario'"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                  />
                                </template>
                              </v-date-picker>
                            </div> 

                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
 
                          <div class="datepicker-input">
                              <v-date-picker v-model="data_final_temporario" >
                                <template v-slot="{ inputValue, inputEvents }">
                                  <input class="form-control"
                                    :element-id="'data_final_temporario'"
                                    :value="inputValue"
                                    v-on="inputEvents"
                                  />
                                </template>
                              </v-date-picker>
                            </div> 

                    </div>
                  </div>
                </div>
              </div>
              <div v-if="data_inicial_temporario !== null && data_final_temporario !== null && dateToCompare(dateToString(data_inicial_temporario)) > dateToCompare(dateToString(data_final_temporario))" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_descricao'" for="descricao" class="col-form-label">Descrição</label>
              <input id="descricao" v-model="descricaoTemp" type="text" class="form-control" maxlength="255">
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-turma_semestre'" for="semestre" class="col-form-label">Semestre(ano/semestre)</label>
              <input v-mask="'####/##'" id="semestre" v-model="semestreSelecionado" placeholder="___/__" type="text" class="form-control" maxlength="8">
            </div>

            <div class="col-md-6" style="z-index: 0">
              <label v-help-hint="'filtro-turma_situacao'" for="situacao_avancado" class="col-form-label">Situação</label>
              <div>
                <b-form-checkbox-group id="situacao_avancado" v-model="selectedAvancados" :options="situacao" buttons button-variant="cinza" name="situacao_avancado" class="checkbtn-line"/>
              </div>
            </div>

            <div class="col-md-6" style="z-index: 0">
              <label v-help-hint="'filtro-turma_dia_da_semana'" for="turma_dia_da_semana" class="col-form-label">Dia da semana</label>
              <div>
                <b-form-checkbox-group id="turma_dia_da_semana" v-model="selectedAvancadosDiaSemana" :options="diaSemana" buttons button-variant="cinza" name="situacao_avancado" class="checkbtn-line"/>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null, filtrar()">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="" class="coluna-checkbox">
              <b-form-checkbox :disabled="!lista.length" v-model="checkAll" :indeterminate="indeterminate" 
                class="m-0" aria-describedby="selected" aria-controls="selected" 
                @change="toggleAll"/>
            </th>
            <th data-column="t.descricao" style="max-width:35%" >Descrição</th>
            <th data-column="func.apelido" style="max-width:10%">Instrutor</th>
            <th data-column="l.descricao" style="max-width:20%">Livro</th>
            <th data-column="t.maximo_alunos" style="max-width: 5%">Alunos</th>
            <th data-column="t.data_inicio" style="max-width: 5%">Início</th>
            <th data-column="t.data_fim" style="max-width: 5%">Término</th>
            <th data-column="t.sala_franqueada" style="max-width: 8%">Sala</th>
            <th data-column="t.situacao" style="max-width: 10%">Situação</th>
            <th class="coluna-icones" style="max-width:4%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <div v-if="listaTurmasEstaCarregada" id="lista-wrapper">
              <tr v-for="item in lista" :key="item.turmaId" @dblclick="editar(item)">
                <td data-label="Selecione" class="coluna-checkbox ">
                  <b-form-checkbox v-model="listaDeTurma" :value="item"/>
                </td>
                <td :title="item.turmaDescricao" class="d-block text-truncate" style="max-width:35%">{{ item.turmaDescricao }}</td>
                <td :title="item.funcionarioApelido ? item.funcionarioApelido : null" style="max-width:10%">{{ item.funcionarioApelido ? item.funcionarioApelido : null }}</td>
                <td :title="item.livroDescricao" style="max-width: 20%">{{ item.livroDescricao }}</td>
                <td style="max-width: 5%">{{ item.qtdContratoTurma }}/{{ item.maximoAlunos }}</td>
                <td style="max-width: 5%">{{ item.dataInicioTurma | formatarData }}</td>
                <td style="max-width: 5%">{{ item.dataFimTurma | formatarData }}</td>
                <td style="max-width: 8%">{{ item.salaDescricao ? item.salaDescricao : null }}</td>
                <td style="max-width: 10%">
                  <PillSituation 
                    :situation="situacoes[item.situacaoTurma].descricao" 
                    :situationClass="item.situacaoTurma.toLowerCase()"
                    :textTooltip="situacoes[item.situacaoTurma].descricao"
                >
                </PillSituation>
                </td>

                <td class="d-flex coluna-icones" v-if="!mostarBtns()">

                </td>

                <td class="d-flex coluna-icones" v-if="mostarBtns()" style="max-width:4%">
                  <!-- <router-link :to="`${$route.path}/visualizar/${item.id}`" class="icone-link" title="Visualizar"> -->
                  <!--   <font-awesome-icon icon="eye" /> -->
                  <!-- </router-link> -->

                  <router-link v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.turmaId}`" class="icone-link" title="Atualizar">
                    <font-awesome-icon icon="pen" />
                  </router-link>

                  <!-- VERIFICAR PERMISSÕES -->
                  <!-- <button v-b-modal.diario-classe class="btn btn-roxo mt-3" ><font-awesome-icon icon="address-book" /></button> -->
                  <b-link :href="linkDiarioClasse(item.turmaId)" class="icone-link" title="Diário de classe" @click="diarioClasse(item.turmaId)"><font-awesome-icon icon="book-open" /></b-link>

                  <!--<a v-if="item.excluido === false" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item, false)">
                    <font-awesome-icon icon="ban" />
                  </a>
                  <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item, true)">
                    <font-awesome-icon icon="check-circle" />
                  </a>!-->
                </td>
              </tr>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

 
   <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
          <b-form-group>
            <b-form-radio-group v-model="opcaoDeRelatorio" required>
              <b-form-radio value="D">Diário de Aulas</b-form-radio>
              <b-form-radio value="C">Cronograma de Aulas</b-form-radio>
            </b-form-radio-group>
          </b-form-group>
         
    
        <div class="info-btn">
          <b-btn :disabled="!listaDeTurma.length || listaDeTurma.length < 1" type="button" variant="verde" @click="imprimirRelatorio(opcaoDeRelatorio)">Imprimir</b-btn>
        </div> 
        <!--   <b-btn  class="btn btn-verde" target="_blank" @click="limparSelects()" >
          Imprimir
        </b-btn> -->
      </div>
 
      <div id="total-container" class="d-flex justify-content-between align-items-center">
        <div></div>

        <div class="info-label d-flex flex-column align-items-end">
          <div>
            <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
            <small v-else>Nenhum item encontrado</small>
          </div>
        </div>

          <div>
            <small>
              <template v-if="listaDeTurma.length >= 1">{{ listaDeTurma.length }} ite{{ listaDeTurma.length > 1 ? 'ns' : 'm' }} selecionado{{ listaDeTurma.length > 1 ? 's' : '' }}</template>
              <template v-else>Nenhum item selecionado</template>
            </small>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {getDateFromISO, dateToCompare, beginOfDay, endOfDay, dateToString} from '../../utils/date'
import DatePicker from '../../components/fields/DatePicker'
import EventBus from '../../utils/event-bus'
import open from '../../utils/open'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaRelatorioClassRecord',
  components: {
    DatePicker,
    PillSituation
  },

  data () {
    return {
      diarioTurmaId: null,
      className: 'rapido-open',
      cursoSelecionado: null,
      modalidadeSelecionada: null,
      salaFranqueadaSelecionada: null,
      funcionarioSelecionado: null,
      horarioSelecionado: null,
      livroSelecionado: null,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      indeterminate: false,
      descricaoTemp: '',
      checkAll: false,
      semestreSelecionado: '',
      semestreSelecionadoFormatado: '',
      data_inicial: '',
      data_final: '',
      data_inicial_temporario: null,
      data_final_temporario: null,
      selectedRapidos: ['ABE', 'FOR'],
      selectedAvancados: ['ABE', 'FOR'],
      selectedAvancadosDiaSemana: [],
      listaTemporaria: [],
      listaDeTurma: [],
      situacao: [
        {text: 'Aberta', value: 'ABE'},
        {text: 'Em formação', value: 'FOR'},
        {text: 'Encerrada', value: 'ENC'}
      ],
      diaSemana: [
        {text: 'Seg', value: 'SEG'},
        {text: 'Ter', value: 'TER'},
        {text: 'Qua', value: 'QUA'},
        {text: 'Qui', value: 'QUI'},
        {text: 'Sex', value: 'SEX'},
        {text: 'Sáb', value: 'SAB'}
      ],
      situacoes: {
        ABE: {descricao: 'Aberta', cor: 'success'},
        FOR: {descricao: 'Em formação', cor: 'info'},
        ENC: {descricao: 'Encerrada', cor: 'danger'}
      },

      modalFormularioAtivo: 'diario-classe',
      opcaoDeRelatorio: 'D'
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('turma', ['lista', 'estaCarregando', 'filtros', 'todosItensCarregados', 'totalItens']),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('modalidadeTurma', {listaModalidadesTurma: 'lista'}),
    ...mapState('horario', {listaHorarios: 'lista'}),
    ...mapState('salaFranqueada', {listaSalasFranqueada: 'lista'}),
    ...mapState('funcionario', {listaFuncionariosRequisicao: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),
    ...mapState('semestre', {listaSemestres: 'lista'}),

    listaModalidadesTurma: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.modalidadeTurma.lista)
      }
    },
    listaCursos: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.curso.lista)
      }
    },

    listaHorarios: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.horario.lista)
      }
    },
    listaSalasFranqueada: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.salaFranqueada.lista.filter(item => {
          let arrDescricao = item.descricao.split(' ')

          if (arrDescricao.indexOf('Personal') >= 0) {
            return false
          }

          return true
        }))
      }
    },
    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}].concat(this.listaFuncionariosRequisicao.filter(funcionario => funcionario.instrutor === true || funcionario.instrutor_personal === true))
      }
    },
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },
    listaTurmasEstaCarregada: {
      // Enquanto não carrega a lista fica um array de 1 item com o valor undefined por algum motivo, por isto tem este workaround
      get () {
        return this.lista.length > 0 && this.lista[0] !== undefined
      }
    }
  },
    watch: {
    listaDeTurma (value, oldVal) {
      if (value.length === 0) {
        this.indeterminate = false
        this.checkAll = false
      }
      if (value.length === this.lista.length) {
        this.indeterminate = false
        this.checkAll = true
      } else {
        this.indeterminate = true
        this.checkAll = false
      }

      // console.log("VaLUE::",value)
      // console.log("olvval",oldVal)
    }
  },

  mounted () {
    this.listarCamposSelects()
    this.filtrar()
    this.mostarBtns()
  },

  methods: {
    ...mapActions('turma', ['listar', 'reativar', 'remover']),
    ...mapMutations('turma', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    getDateFromISO: getDateFromISO,

    dateToCompare: dateToCompare,
    dateToString: dateToString,

    carregarMais () {
       this.listar()
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push(`${this.$route.path}/atualizar/${item.turmaId}`)
      }
    },
    setDataInicial (value) {
      this.data_inicial_temporario = value
    },
    mostarBtns(){
      if(this.$route.path == '/relatorios/impressao-class-record'){
        return true
      }
      return false
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    setDataFinal (value) {
      this.data_final_temporario = value
    },

    setModalidadeTurma (value) {
      this.modalidadeSelecionada = value
    },

    setCurso (value) {
      this.cursoSelecionado = value
      let lista = []
      if (this.cursoSelecionado && this.cursoSelecionado.id) {
        lista = this.listaLivros.filter(livro => {
          return livro.curso.filter(curso => {
            return this.cursoSelecionado.id === curso.id
          }).length > 0
        })
      } else {
        lista = this.listaLivros
      }

      this.listaTemporaria = [{id: null, descricao: lista.length > 0 ? 'Selecione' : 'Selecione o curso'}].concat(lista)
      this.livroSelecionado = this.listaTemporaria[0]
    },

    setSalaFranqueada (value) {
      this.salaFranqueadaSelecionada = value
    },

    setFuncionario (value) {
      this.funcionarioSelecionado = value
    },

    setHorario (value) {
      this.horarioSelecionado = value
    },

    setLivro (value) {
      this.livroSelecionado = value
    },

    setSemestre (value) {
      if (value.length === 6) {
        var semestre = value.substring(5, 6)
        var ano = value.substring(0, 5)
        var resultado = ano + '0' + semestre

        this.semestreSelecionado = resultado
      } else {
        this.semestreSelecionado = value
      }
    },

    listarCamposSelects () {
      this.$store.commit('funcionario/SET_FILTROS', { consultor_ou_gestor_comercial: false })
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
      this.$store.commit('horario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('salaFranqueada/SET_PAGINA_ATUAL', 1)
      this.$store.commit('salaFranqueada/SET_FILTRO_APENAS_SALA_ATIVA', true)
      this.$store.commit('modalidadeTurma/SET_PAGINA_ATUAL', 1)
      this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
      this.$store.commit('semestre/SET_PAGINA_ATUAL', 1)

      this.$store.dispatch('funcionario/listar')
      this.$store.dispatch('curso/listar')
      this.$store.dispatch('horario/listar')
      this.$store.dispatch('salaFranqueada/listar')
      this.$store.dispatch('modalidadeTurma/listar')
      this.$store.dispatch('livro/listar')
      this.$store.dispatch('semestre/listar')
        .then(() => {
          this.setCurso(null)
        })
    },

    limparFiltros () {
      this.semestreSelecionado = this.listaSemestres[0]
      this.modalidadeSelecionada = this.listaModalidadesTurma[0]
      this.cursoSelecionado = this.listaCursos[0]
      this.salaFranqueadaSelecionada = this.listaSalasFranqueada[0]
      this.livroSelecionado = this.listaTemporaria[0]
      this.horarioSelecionado = this.listaHorarios[0]
      this.funcionarioSelecionado = this.listaFuncionarios[0]
      this.data_inicial_temporario = null
      this.data_final_temporario = null
    },

    limparStateAnterior () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', [])
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', '')
      this.$store.commit('turma/SET_FILTRO_HORARIO', null)
      this.$store.commit('turma/SET_FILTRO_MODALIDADE_TURMA', null)
      this.$store.commit('turma/SET_FILTRO_SALA_FRANQUEADA', null)
      this.$store.commit('turma/SET_FILTRO_FUNCIONARIO', null)
      this.$store.commit('turma/SET_FILTRO_CURSO', null)
      this.$store.commit('turma/SET_FILTRO_LIVRO', null)
      this.$store.commit('turma/SET_FILTRO_SEMESTRE', null)
      this.$store.commit('turma/SET_FILTRO_DATA_INICIO', null)
      this.$store.commit('turma/SET_FILTRO_DATA_FIM', null)
    },

    abrirFiltroRapido () {
      this.filtroRapido = !this.filtroRapido
      this.filtroAvancado = false
      this.className = this.filtroRapido ? 'rapido-open' : null
      this.filtroSelecionado = 1
      this.limparFiltros()
    },

    abrirFiltroAvancado () {
      this.filtroAvancado = !this.filtroAvancado
      this.filtroRapido = false
      this.className = this.filtroAvancado ? 'filtro-open' : null
      this.filtroSelecionado = 2
      this.limparFiltros()
    },

    executaFiltroRapido () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', this.selectedRapidos)
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', this.descricaoTemp)
    },

    executaFiltroAvancado () {
      this.$store.commit('turma/SET_FILTRO_SITUACAO', this.selectedAvancados)
      this.$store.commit('turma/SET_FILTRO_DIA_SEMANA', this.selectedAvancadosDiaSemana)
      this.$store.commit('turma/SET_FILTRO_DESCRICAO', this.descricaoTemp)
      this.$store.commit('turma/SET_FILTRO_SEMESTRE', this.semestreSelecionado)
      this.$store.commit('turma/SET_FILTRO_MODALIDADE_TURMA', (this.modalidadeSelecionada ? this.modalidadeSelecionada.id : null))
      this.$store.commit('turma/SET_FILTRO_CURSO', (this.cursoSelecionado ? this.cursoSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_SALA_FRANQUEADA', (this.salaFranqueadaSelecionada ? this.salaFranqueadaSelecionada.id : null))
      this.$store.commit('turma/SET_FILTRO_LIVRO', (this.livroSelecionado ? this.livroSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_HORARIO', (this.horarioSelecionado ? this.horarioSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_FUNCIONARIO', (this.funcionarioSelecionado ? this.funcionarioSelecionado.id : null))
      this.$store.commit('turma/SET_FILTRO_DATA_INICIO', this.data_inicial_temporario ? beginOfDay(dateToString(this.data_inicial_temporario)) : null)
      this.$store.commit('turma/SET_FILTRO_DATA_FIM', this.data_final_temporario ? endOfDay(dateToString(this.data_final_temporario)) : null)
    },

    formatoSemestre () {
      if (this.semestreSelecionado.length === 6) {
        var semestre = this.semestreSelecionado.substring(5, 6)
        var ano = this.semestreSelecionado.substring(0, 5)
        var resultado = ano + '0' + semestre

        this.semestreSelecionado = resultado
      }
      return this.semestreSelecionado
    },

    filtrar () {
      this.formatoSemestre()
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      // this.$store.commit('turma/SET_FILTRO_APENAS_SALA', true)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.checkAll = false
      this.listaDeTurma = []
      this.listar()
    },

    inativar (turma, bRemover) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          if (bRemover) {
            this.remover(turma.id)
              .then(() => {
                this.filtrar()
              })
          } else {
            this.reativar(turma.id)
              .then(() => {
                this.filtrar()
              })
          }
        }
      })
    },

    diarioClasse (id) {
      /* if (this.permissoes['EDITAR']) {
      } */
      this.$router.push(this.linkDiarioClasse(id))
    },

     imprimirRelatorio (tipoRelatorio) {
      this.imprimir = true
      let api
      if (tipoRelatorio === 'C') {
        api = 'class_record_cronograma/imprimir'
      } else {
        api = 'class_record_diario/imprimir'
      }
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = '/relatorios/impressao-class-record'
      //this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()

      open(`/api/relatorios/${api}?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`)
    },
    converterDadosParaLink () {
      const ids = this.listaDeTurma.map(turma => turma.turmaId)
      let dados = []

      for (let index in ids) {
        dados.push(`turma[]=${ids[index]}`)
      }

      return dados.join('&')
    },

    linkDiarioClasse (id) {
      return `${this.$route.path}/diario-classe/${id}`
    },
       limparSelects () {
      this.listaDeTurma.splice()
    },
    toggleAll (checked) {
      if (checked) {
        this.listaDeTurma = this.lista
        return
      }

      this.listaDeTurma = []
    }
  }
}
</script>

<style lang="scss" scoped>

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}
.coluna-checkbox{
  padding-left: 0;
}
 .custom-control.custom-checkbox {
   padding-left: 27px;
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

.table-responsive-sm thead{
  background-color: #fdfdfd;
}
.table-responsive-sm thead tr {
  position: relative;
}
.table-responsive-sm tbody {
  display: block;
  overflow: auto;
  overflow-y: scroll;
    overflow-x: hidden;
}
.input-group > .datåepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}
.datepicker {
  padding: 0;
}
[element-id=data_inicial_temporario], [element-id=data_final_temporario] {
  width: 100%;
}


.table-sm .coluna-checkbox {
    max-width: 27px;
}    
.input-group-prepend{
  width: 100%;
}
</style>