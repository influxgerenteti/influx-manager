<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1">Filtro Rápido</div>
        </div>

        <button v-b-modal.formulario_agendamento_professor v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" type="button" class="btn btn-azul" @click="adicionar">
          <font-awesome-icon icon="plus" /> Adicionar
        </button>

      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label v-help-hint="'filtro_rapido-bonus-class_data-agendamento'" class="col-form-label" for="data_agendamento_bonus_class_de">Data agendamento</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <calendar :element-id="'data_agendamento_bonus_class_de'" v-model="data_agendamento_de" @input="setDataDe()"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <calendar :element-id="'data_agendamento_bonus_class_ate'" v-model="data_agendamento_ate" @input="setDataAte()"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_agendamento_de,data_agendamento_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-4">
              <label v-help-hint="'filtros_rapido-s-class_professor_responsavel'" for="professor_responsavel" class="col-form-label">Instrutor</label>
              <!-- " -->
              <g-select
                :id="'professor_responsavel'"
                :value="funcionario"
                :options="listaDeFuncionario"
                :select="selectFuncionario"
                class="multiselect-truncate"
                label="apelido"
                track-by="id"/>
            </div>

            <div class="col-md-4">
              <label for="filtro_rapido-bonus-class_situacao" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="situacao_filtro_rapido"
                  v-model="situacaoTemp"
                  :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  @change="setSituacao"
                />
              </div>
            </div>
          </div>
        </form>
      </b-collapse>

      <!-- <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada = true, filtrar()">
          <div class="form-group row">
            <div class="col-md-12">
              <label for="situacao_filtro_avancado" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-radio-group
                  id="situacao_filtro_avancado"
                  v-model="selected" :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_filtro_avancado"
                />
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse> -->
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Data agendamento</th>
            <th data-column="">Período agendado</th>
            <th data-column="">Professor</th>
            <th data-column="">Sala</th>
            <th data-column="">Situação</th>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item)">
              <td data-label="Data agendamento">{{ item.data_aula ? item.data_aula : '' | formatarData }}</td>
              <td data-label="Período agendado">{{ item.horario_inicio | formatarHoraDB }} - {{ item.horario_termino | formatarHoraDB }} </td>
              <td data-label="Professor">{{ item.funcionario ? item.funcionario.apelido : '' }}</td>
              <td data-label="Sala">{{ item.sala_franqueada ? item.sala_franqueada.sala.descricao : '' }}</td>
              <td data-label="Situação">
                <PillSituation 
                    :situation="situacao.find(situacaoIndex => situacaoIndex.value === item.situacao).text" 
                    :situationClass="item.situacao.toLowerCase(item.situacao)" 
                    :textTooltip="situacao.find(situacaoIndex => situacaoIndex.value === item.situacao).text"
                  >
                </PillSituation>
              </td>
              <td class="d-flex coluna-icones">
                <!-- Ações -->
                <a v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true) && item.situacao === 'PEN'" title="Alterar" class="icone-link ml-2" @click.prevent="editar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <template v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)">
                  <a v-b-tooltip.viewport.left.hover :title="item.situacao === 'PEN' ? 'Configurações de alunos' : 'Ver'" class="icone-link ml-2" @click.prevent="openControlBonusClass(item)">
                    <font-awesome-icon :icon="item.situacao === 'PEN' ? 'list-ul' :'eye'" />
                  </a>
                </template>

                <!-- Concluir  -->
                <a v-b-tooltip.left v-if="item.situacao === 'PEN'" title="Concluir" href="javascript: void(0)" class="icone-link ml-2" @click="concluir(item)">
                  <font-awesome-icon icon="check" />
                </a>

                <!-- Cancelar  -->
                <a v-b-tooltip.left v-if="item.situacao === 'PEN'" title="cancelar" href="javascript: void(0)" class="icone-link ml-2" @click="cancelaAulaBonus(item)">
                  <font-awesome-icon icon="trash" />
                </a>

              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>

    <!-- Modais -->
    <b-modal id="formulario_agendamento_professor" ref="modal_agendamento_professor" v-model="visibilidade_agendamento_professor" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <FormularioAgendamentoProfessor ref="form_agendamento_professor" @cancelar="cancelarModalAgendamentoProfessor" @modalAgendamentoProfessorReady="modalAgendamentoProfessorReady"/>
    </b-modal>

   <b-modal id="formulario_controle_agendamento" ref="modal_controle_agendamento" v-model="visibilidade_controle_agendamento" size="lg" centered no-close-on-backdrop hide-header hide-footer>
      <FormularioControleAgendamento ref="form_controle_agendamento" @cancelar="cancelarModalControleAgendamento"  @modalControleAgendamentoReady="modalControleAgendamentoReady" />
    </b-modal>

    <!-- <b-modal id="formulario_controle_agendamento" ref="modal_controle_agendamento" v-model="visibilidade_controle_agendamento" size="lg" centered no-close-on-backdrop hide-header hide-footer> -->
    <!--<FormularioControleAgendamento ref="form_controle_agendamento" @cancelar="cancelarModalControleAgendamento"  @modalControlBonusReady="modalControlBonusReady" /> -->
    <!-- </b-modal> -->

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {dateToCompare, beginOfDay, endOfDay, stringToISODate, converteHorarioParaBanco, converteHorarioBancoParaInputText} from '../../utils/date'
import FormularioAgendamentoProfessor from './AgendamentoProfessor'
import FormularioControleAgendamento from './ControleAgendamento'
import EventBus from '../../utils/event-bus'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaBonusClass',
  components: {
    FormularioAgendamentoProfessor,
    FormularioControleAgendamento,
    PillSituation
  },
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      itemToOpen: null,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      situacao: [
        {text: 'Pendente', value: 'PEN'},
        {text: 'Concluído', value: 'CON'}
      ],
      visibilidade_agendamento_professor: false,
      visibilidade_controle_agendamento: false,

      data_agendamento_de: undefined,
      data_agendamento_ate: undefined,
      funcionario: {id: null, apelido: 'Selecione'},
      situacaoTemp: ['PEN']
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('bonusClass', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados', filtros: 'filtros'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaDeFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioRequisicao]
      }
    }
  },

  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listarCamposSelects()
    this.filtrar()
  },

  methods: {
    ...mapActions('bonusClass', {listarItens: 'listar', atualizar: 'atualizar', cancelarAulasBonus: 'cancelar'}),
    ...mapMutations('bonusClass', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'SET_FILTROS']),

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.commit('funcionario/SET_FILTROS', { instrutor_ou_coordenador_pedagogico: true, consultor_ou_gestor_comercial: false })
      this.$store.dispatch('funcionario/listar')
      // this.$store.commit('funcionario/LIMPAR_FILTROS')
    },
    setDataDe () {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_de || this.data_agendamento_de.length === 10) {
        this.filtrar()
      }
    },

    setDataAte () {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_ate || this.data_agendamento_ate.length === 10) {
        this.filtrar()
      }
    },

    setSituacao (value) {
      this.situacaoTemp = value
      this.filtrar()
    },

    selectFuncionario (value) {
      this.funcionario = value
      this.filtrar()
    },

    filtrar () {
      this.SET_PAGINA_ATUAL(1)
      this.filtrosRapido()
      this.listarItens()
    },

    filtrosRapido () {
      let filtro = {}
      filtro.data_agendamento_de = this.data_agendamento_de ? beginOfDay(this.data_agendamento_de) : null
      filtro.data_agendamento_ate = this.data_agendamento_ate ? endOfDay(this.data_agendamento_ate) : null
      filtro.funcionario = this.funcionario.id ? this.funcionario.id : null
      filtro.situacao = this.situacaoTemp

      this.SET_FILTROS(filtro)
    },
    limparFiltros () {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    adicionar () {
      this.$store.commit('bonusClass/SET_DATA_AULA', this.getFriday())
    },

    getFriday () {
      const valueOfFriday = 5
      let now = new Date()
      return now.addDays(valueOfFriday - now.getDay())
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    editar (item) {
       this.itemToOpen = item;   
       this.visibilidade_agendamento_professor = true   
    },
     openControlBonusClass (item) {
      this.itemToOpen = item;
      this.visibilidade_controle_agendamento = true
      
      
    },

    cancelarModalAgendamentoProfessor () {
      this.itemToOpen = null;
      this.$refs.modal_agendamento_professor.hide()
      this.visibilidade_agendamento_professor = false
      this.filtrar()
    },

    cancelarModalControleAgendamento () {
      this.itemToOpen = null;
      this.$refs.modal_controle_agendamento.hide() 
      this.visibilidade_controle_agendamento = false
      this.filtrar()
    },
     modalAgendamentoProfessorReady () {
     if(this.itemToOpen != null){
        if (this.itemToOpen.situacao === 'PEN') {
                this.$refs.form_agendamento_professor.openEdit(this.itemToOpen.id)
              } else {
                this.$refs.form_agendamento_professor.openView(this.itemToOpen.id)
              }
      }
      
     
      
    },
    modalControleAgendamentoReady () {
      if(this.itemToOpen != null){     
        if (this.itemToOpen.situacao === 'PEN') {          
          this.$refs.form_controle_agendamento.openEdit(this.itemToOpen.id)
        } else {          
          this.$refs.form_controle_agendamento.openEdit(this.itemToOpen.id, true)
        }
      }
     
      
    },
   

    concluir (item) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO_ID(item.id)
          const obj = {
            id: item.id,
            funcionario: item.funcionario.id,
            sala_franqueada: item.sala_franqueada.id,
            data_aula: stringToISODate(item.data_aula),
            horario_inicio: converteHorarioParaBanco(converteHorarioBancoParaInputText(item.horario_inicio)),
            horario_termino: converteHorarioParaBanco(converteHorarioBancoParaInputText(item.horario_termino)),
            concluido: true
          }

          this.SET_ITEM_SELECIONADO(obj)
          this.atualizar(false).then(() => {
            this.filtrar()
            this.SET_ITEM_SELECIONADO_ID(null)
            this.LIMPAR_ITEM_SELECIONADO()
          }).catch(error => {
            console.error(error)
          })
        }
      }, `Deseja concluir o bonus class ?`)
    },
    cancelaAulaBonus (item) {
      const mensagem = 'Deseja cancelar item selecionado?'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO_ID(item.id)
          const obj = {
            id: item.id
          }
          this.SET_ITEM_SELECIONADO(obj)
          this.cancelarAulasBonus(false).then(() => {
            this.filtrar()
            this.SET_ITEM_SELECIONADO_ID(null)
            this.LIMPAR_ITEM_SELECIONADO()
          }).catch(error => {
            console.error(error)
          })
        }
      }, mensagem)
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

td.coluna-icones{
  padding-right: 7rem!important;
}
</style>
