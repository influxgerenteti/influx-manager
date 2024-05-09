<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <!-- <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div> -->
        </div>
        <button v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" type="button" class="btn btn-azul" @click="adicionarNovo">
          <font-awesome-icon icon="plus" /> Adicionar
        </button>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-atividade_extra_data_agendamento'" class="col-form-label" for="data_agendamento_de">Data agendamento</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <calendar :element-id="'data_agendamento_de'" v-model="data_agendamento_de" @input="setDataAgendamentoDe" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <calendar :element-id="'data_movimentacao_ate'" v-model="data_agendamento_ate" @input="setDataAgendamentoAte" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_agendamento_de,data_agendamento_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </div>

            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-atividade_extra_responsavel'" for="atividade_extra_responsavel" class="col-form-label">Responsável</label>
              <g-select id="atividade_extra_responsavel"
                        :select="setResponsavel"
                        :value="responsavel"
                        :options="listaDeFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </div>
            <div class="auto">
              <label v-help-hint="'filtroRapido-atividade_extra_situacao'" for="filtro_avancado_situacao" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="filtro_avancado_situacao"
                  v-model="situacaoSelecionada"
                  :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  @change="setSituacao"
                />
              </div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-atividade_extra_tipo_atividade'" for="atividade_extra_tipo_atividade" class="col-form-label">Tipo Atividade</label>
              <g-select id="atividade_extra_tipo_atividade"
                        :select="setTipoAtividade"
                        :value="tipoAtividade"
                        :options="listaTipoAtividade"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Data agendamento</th>
            <th data-column="">Tipo de Atividade</th>
            <th data-column="">Máx. alunos</th>
            <th data-column="">Resumo da Atividade</th>
            <th data-column="">Responsável</th>
            <th data-column="">Sala</th>
            <th data-column="">Situação</th>
            <th class="coluna-icones"></th>
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

            <tr v-for="item in listaItens" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Data agendamento">{{ item.data_hora_inicio ? item.data_hora_inicio : '--' | formatarData }}</td>
              <td data-label="Tipo de Atividade">{{ item.item ? item.item.descricao : '--' }}</td>
              <td data-label="Máximo de alunos">{{ verificarQuantidade(item) }} / {{ item.quantidade_maxima_alunos ? item.quantidade_maxima_alunos : '' }}</td>
              <td data-label="Resumo da Atividade">{{ item.descricao_atividade ? item.descricao_atividade : '--' }}</td>
              <td data-label="Responsável">{{ responsavalPelaExecucao(item.responsaveis_execucacao) }}</td>
              <td data-label="Sala">{{ item.sala_franqueada.sala ? item.sala_franqueada.sala.descricao : '--' }}</td>
              <td data-label="Situação">
                <PillSituation 
                    :situation="situacao.find(situacaoIndex => situacaoIndex.value == item.situacao).text" 
                    :situationClass="item.situacao.toLowerCase(item.situacao) === 'c' ? 'con' : item.situacao.toLowerCase(item.situacao)"
                    :textTooltip="situacao.find(situacaoIndex => situacaoIndex.value == item.situacao).text"
                  >
                </PillSituation>
              </td>
              <td class="d-flex coluna-icones">
                <!-- Ações -->
                <template v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true) && item.situacao === 'P'">
                  <a v-b-tooltip.viewport.left.hover class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                    <font-awesome-icon icon="pen" />
                  </a>
                </template>
                <template v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true) && item.situacao != 'P'">
                  <a v-b-tooltip.viewport.left.hover class="icone-link" title="Ver" @click.prevent="ver(item)">
                    <font-awesome-icon icon="eye" />
                  </a>
                </template>

                <!-- Concluir  -->
                <a v-b-tooltip.left v-if="item.situacao === 'P'" title="Concluir" href="javascript: void(0)" class="icone-link ml-2" @click="concluir(item)">
                  <font-awesome-icon icon="check" />
                </a>

                <a v-b-tooltip.viewport.left.hover class="icone-link ml-2" title="Exportar lista de alunos" @click.prevent="exportaListaDeAlunos(item)">
                  <font-awesome-icon icon="print" />
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

    <Formulario-atividade-extra ref="modalFormularioAtividadeExtra" @cancelar="fecharModal()" @filtra="filtrar()" />
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import FormularioAtividadeExtra from './Formulario'
import EventBus from '../../utils/event-bus'
import open from '../../utils/open'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaAtiviadeExtra',
  components: {
    FormularioAtividadeExtra,
    PillSituation
  },
  data () {
    return {
      visibilidadeModalAtividadeExtra: false,
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      situacao: [
        {text: 'Pendente', value: 'P'},
        {text: 'Concluido', value: 'C'},
        {text: 'Cancelado', value: 'CC'}
      ],
      situacaoLista: [
        {text: 'P', value: 'Pendente'},
        {text: 'C', value: 'Concluido'},
        {text: 'CC', value: 'Cancelado'}
      ],
      data_agendamento_de: undefined,
      data_agendamento_ate: undefined,
      situacaoSelecionada: ['P'],
      responsavel: null,
      tipoAtividade: null
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('atividadeExtra', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),
    ...mapState('cadastroServico', {listaDeItemRequisicao: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaDeFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioRequisicao]
      }
    },

    listaTipoAtividade: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeItemRequisicao.filter(item => item.tipo_item.tipo === 'AE')]
      }
    }
  },
  mounted () {
    this.selected = []
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('atividadeExtra', {listarItens: 'listar', atualizar: 'atualizar', concluirAtividadeExtra: 'concluir'}),
    ...mapMutations('atividadeExtra', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'INCREMENTAR_PAGINA_ATUAL']),

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    responsavalPelaExecucao (responsavel) {
      let listaDeNome = responsavel.map(responsavel => responsavel.apelido)
      return listaDeNome.join(', ')
    },

    carregarMais () {
      this.$store.commit('atividadeExtra/INCREMENTAR_PAGINA_ATUAL')
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.commit('funcionario/SET_LISTA', [])
      this.$store.commit('funcionario/SET_FILTROS', { instrutor_ou_coordenador_pedagogico: true, consultor_ou_gestor_comercial: false })
      this.$store.dispatch('funcionario/listar')

      this.$store.commit('cadastroServico/SET_PAGINA_ATUAL', 1)
      this.$store.commit('cadastroServico/SET_LISTA', [])
      this.$store.commit('cadastroServico/SET_FILTRO_FRANQUEADA', [this.$store.state.root.usuarioLogado.franqueadaSelecionada])
      this.$store.dispatch('cadastroServico/listar')
    },

    setDataAgendamentoDe (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_de || this.data_agendamento_de.length === 10) {
        this.filtrar()
      }
    },

    setDataAgendamentoAte (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_ate || this.data_agendamento_ate.length === 10) {
        this.filtrar()
      }
    },

    setResponsavel (value) {
      this.responsavel = value
      this.filtrar()
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
      this.filtrar()
    },

    setTipoAtividade (value) {
      this.tipoAtividade = value
      this.filtrar()
    },

    executaFiltroRapido () {
      let dataDe = this.data_agendamento_de ? beginOfDay(this.data_agendamento_de) : null
      let dataAte = this.data_agendamento_ate ? endOfDay(this.data_agendamento_ate) : null
      let responsavel = this.responsavel ? this.responsavel.id : null
      let situacao = this.situacaoSelecionada ? this.situacaoSelecionada : null
      let tipoAtividade = this.tipoAtividade ? this.tipoAtividade.id : null
      let tipo = 'AE'

      let filtros = Object.assign({
        data_agendamento_de: dataDe,
        data_agendamento_ate: dataAte,
        item: tipoAtividade,
        responsavel_execucao: responsavel,
        situacao: situacao,
        tipo: tipo
      })
      this.$store.commit('atividadeExtra/SET_FILTROS', filtros)
    },

    verificarQuantidade (item) {
      const tamanhoListaAlunos = this.filtrarListaDeAlunosParticipantes(item.alunoAtividadeExtras)
      const tamanhoListaConvidados = item.convidadoAtividadeExtras.length

      return tamanhoListaAlunos + tamanhoListaConvidados
    },

    filtrarListaDeAlunosParticipantes (lista) {
      let listaAlunos = lista.filter(aluno => {
        return aluno.removido === false
      })
      return listaAlunos.length
    },

    filtrar () {
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      }

      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    ver (item) {
      this.$refs.modalFormularioAtividadeExtra.isReadOnly = true
      this.$refs.modalFormularioAtividadeExtra.visibilidadeModalAtividadeExtra = true
      this.$refs.modalFormularioAtividadeExtra.openEdit(item.id)
    },

    alterar (item) {
      if (item.situacao === 'P') {
        this.$refs.modalFormularioAtividadeExtra.isReadOnly = false
        this.$refs.modalFormularioAtividadeExtra.visibilidadeModalAtividadeExtra = true
        this.$refs.modalFormularioAtividadeExtra.openEdit(item.id)
      } else {
        this.ver(item)
      }
    },

    adicionarNovo () {
      this.$refs.modalFormularioAtividadeExtra.isReadOnly = false
      this.$refs.modalFormularioAtividadeExtra.visibilidadeModalAtividadeExtra = true
    },

    fecharModal () {
      this.$refs.modalFormularioAtividadeExtra.visibilidadeModalAtividadeExtra = false
    },

    exportaListaDeAlunos (item) {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtroAlunoAtividadeExtra = `atividade_extra=${item.id}`
        console.log(franqueada)
        console.log(auth)
        console.log(rota)
        console.log(filtroAlunoAtividadeExtra)
        const listaAlunos = item.alunoAtividadeExtras
        listaAlunos.forEach(element => {
          console.log(element)
        });
  
      open(`/api/relatorios/atividade_extra_participantes?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtroAlunoAtividadeExtra}`, '_blank')
    },

    concluir (item) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO(item)

          this.concluirAtividadeExtra(item.id).then(() => {
            this.filtrar()
          }).catch(error => {
            console.error(error)
          })
        }
      }, `Deseja concluir a atividade extra ?`)
      this.SET_ITEM_SELECIONADO_ID(null)
      this.LIMPAR_ITEM_SELECIONADO()
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
</style>
