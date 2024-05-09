<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <!-- <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div> -->
        </div>

        <button v-b-modal.reposicao-aula-avaliacao v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" type="button" class="btn btn-azul" @click.prevent="adiconarReposicao">
          <font-awesome-icon icon="plus" /> Adicionar
        </button>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group">
            <div class="row">
              <div class="col-md-4">
                <label v-help-hint="'filtroRapido-reposicao_data_agendamento'" class="col-form-label" for="lista_reposicao_data_agendamento" >Data agendamento</label>
                <div class="row">
                  <div class="col">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">De</div>
                      </div>
                      <calendar :element-id="'lista_reposicao_data_agendamento'" v-model="data_agendamento_de" @input="setDataAgendamentoDe" />
                    </div>
                  </div>
                  <div class="col">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">Até</div>
                      </div>
                      <!-- <g-datepicker
                        :element-id="'data_movimentacao_ate'"
                        :value="data_agendamento_ate"
                        :selected="setDataAgendamentoAte"/> -->
                      <calendar :element-id="'lista_reposicao_data_movimentacao_ate'" v-model="data_agendamento_ate" @input="setDataAgendamentoAte" />
                    </div>
                  </div>
                </div>
                <div v-if="dataFiltroInvalida(data_agendamento_de,data_agendamento_ate)" class="floating-message bg-danger">
                  Data inicial deve ser menor que a data final!
                </div>
              </div>
              <div class="col-md-4">
                <label v-help-hint="'filtroRapido-resposicao-responsavel'" for="lista_reposicao_responsavel" class="col-form-label">Responsável</label>
                <g-select id="lista_reposicao_responsavel"
                          :select="setFuncionarioResponsavel"
                          :value="funcionario_responsavel"
                          :options="listaFuncionario"
                          class="multiselect-truncate"
                          label="apelido"
                          track-by="id"
                />
              </div>
              <div class="col-md-4">
                <label v-help-hint="'filtroRapido-resposicao-item'" for="lista_reposicao_tipo_atividade" class="col-form-label">Tipo de Make-up</label>
                <g-select id="lista_reposicao_tipo_atividade"
                          :select="setTipoAtividade"
                          :value="tipoAtividade"
                          :options="listaDeItem"
                          class="multiselect-truncate"
                          label="descricao"
                          track-by="id"
                />
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label for="situacao_filtro_rapido" class="col-form-label">Situação</label>
                <div class="d-block">
                  <b-form-checkbox-group
                    id="situacao_filtro_rapido"
                    v-model="situacaoFiltro"
                    :options="situacao"
                    buttons
                    button-variant="cinza"
                    class="checkbtn-line"
                    @change="setSituacao"
                  />
                </div>
              </div>
            </div>

          </div>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Data Agendamento</th>
            <th data-column="">Nome aluno</th>
            <th data-column="">Tipo de Atividade</th>
            <th data-column="">Resumo da Atividade</th>
            <th data-column="">Responsável</th>
            <th data-column="">Sala</th>
            <th data-column="">Situação</th>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <!-- <perfect-scrollbar> -->
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item)">
              <!-- <td data-label="#" class="size-27">#</td> -->
              <td data-label="Data Agendamento">{{ item.data_hora_inicio ? item.data_hora_inicio : '--' | formatarDataHora }}</td>
              <td data-label="Nome aluno">{{ item.aluno.pessoa ? item.aluno.pessoa.nome_contato : '--' }}</td>
              <td data-label="Tipo de Atividade">{{ item.item ? item.item.descricao : '--' }}</td>
              <td data-label="Resumo da Atividade">{{ item.descricao_atividade ? item.descricao_atividade : '--' }}</td>
              <td data-label="Responsável">{{ item.responsavel_execucao ? item.responsavel_execucao.apelido : '--' }}</td>
              <td data-label="Sala">{{ item.sala_franqueada ? item.sala_franqueada.sala.descricao : '--' }}</td>
              <td data-label=" ">
                <PillSituation 
                    :situation="itemSituacao[item.situacao]" 
                    :situationClass="item.situacao.toLowerCase() === 'c' ? 'con' : item.situacao.toLowerCase() " 
                    :textTooltip="itemSituacao[item.situacao]"
                  >
                </PillSituation>
              </td>
              <td class="d-flex coluna-icones">

                <a v-b-tooltip.viewport.left.hover v-b-modal="'reposicao-aula-avaliacao'" v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :title="item.situacao === 'P' ? 'Alterar' : 'Visualizar'" href="javascript:void(0)" class="icone-link" @click.prevent="alterar(item)">
                  <font-awesome-icon :icon="item.situacao === 'P' ? 'pen' : 'eye'"/>
                </a>

                <!-- Concluir  -->
                <a v-b-tooltip.left v-if="item.situacao === 'P'" title="Concluir" href="javascript: void(0)" class="icone-link" @click="concluir(item)">
                  <font-awesome-icon icon="check" />
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

    <!-- Formulario de Reposição -->
    <reposicao-aula-avaliacao ref="reposicaoAulaAvaliacao" :read-only="readOnly" @filtrar="filtrar()"/>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import FormularioAvaliacao from './Formulario'
import {beginOfDay, endOfDay, dateToCompare, stringToISODate, converteHorarioParaBanco, converteHorarioBancoParaInputText} from '../../utils/date'
import EventBus from '../../utils/event-bus'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaReposicaoAulaAvaliacao',

  components: {
    'reposicao-aula-avaliacao': FormularioAvaliacao,
    PillSituation
  },

  data () {
    return {
      readOnly: false,
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      situacaoFiltro: null,
      data_agendamento_de: undefined,
      data_agendamento_ate: undefined,
      funcionario_responsavel: null,
      tipoAtividade: null,
      selected: 0,
      situacao: [
        {text: 'Pendente', value: 'P'},
        {text: 'Concluido', value: 'C'},
        {text: 'Cancelado', value: 'CC'}
      ],
      itemSituacao: {
        P: 'Pendente',
        CC: 'Cancelado',
        C: 'Concluido'
      }
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('reposicaoAulaAvaliacao', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados', itemSelecionado: 'itemSelecionado'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),
    ...mapState('cadastroServico', {listaItemRequisicao: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionarioRequisicao]
      }
    },

    listaDeItem: {
      get () {
        return [{id: null, descricao: 'Selecione', tipo_item: null}, ...this.listaItemRequisicao.filter((item) => {
          if ((item.tipo_item.tipo === 'MC') || (item.tipo_item.tipo === 'MT')) {
            return item
          }
        })]
      }
    }
  },

  mounted () {
    this.selected = 0
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    this.listarCamposSelects()
  },

  methods: {
    ...mapActions('reposicaoAulaAvaliacao', {listarItens: 'listar', buscarReposicao: 'buscar', atualizar: 'atualizar'}),
    ...mapMutations('reposicaoAulaAvaliacao', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_DATA_AGENDAMENTO_DE', 'SET_FILTRO_DATA_AGENDAMENTO_ATE', 'SET_FILTRO_ITEM', 'SET_FILTRO_RESPONSAVEL_PELA_EXECUCAO', 'SET_FILTRO_SITUACAO', 'SET_ORDER_BY']),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('cadastroServico', {listarItem: 'listar'}),

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('funcionario/SET_FILTROS', { instrutor_ou_coordenador_pedagogico: true, consultor_ou_gestor_comercial: false })
      this.listarFuncionario()
      this.$store.commit('cadastroServico/SET_FILTRO_FRANQUEADA', [this.$store.state.root.usuarioLogado.franqueadaSelecionada])
      this.listarItem()
    },

    setSituacao (value) {
      this.situacaoFiltro = value
      this.filtrar()
    },

    setTipoAtividade (value) {
      this.tipoAtividade = value
      this.filtrar()
    },

    setFuncionarioResponsavel (value) {
      this.funcionario_responsavel = value
      this.filtrar()
    },

    setDataAgendamentoDe (value) {
      this.data_agendamento_de = value
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_de || this.data_agendamento_de.length === 10) {
        this.filtrar()
      }
    },

    setDataAgendamentoAte (value) {
      this.data_agendamento_ate = value
      if (this.estaCarregando) {
        return
      }

      if (!this.data_agendamento_ate || this.data_agendamento_ate.length === 10) {
        this.filtrar()
      }
    },

    limparStateAnterior () {
      // TODO: Adicionar os Mutations do modulo para retornar os valores para nulo
    },

    executaFiltroRapido () {
      let dataAgendamentoDe = this.data_agendamento_de ? beginOfDay(this.data_agendamento_de) : null
      let dataAgendamentoAte = this.data_agendamento_ate ? endOfDay(this.data_agendamento_ate) : null
      let itemId = this.tipoAtividade ? this.tipoAtividade.id : null
      let responsavelId = this.funcionario_responsavel ? this.funcionario_responsavel.id : null
      let situacao = this.situacaoFiltro ? this.situacaoFiltro : null

      this.SET_FILTRO_DATA_AGENDAMENTO_DE(dataAgendamentoDe)
      this.SET_FILTRO_DATA_AGENDAMENTO_ATE(dataAgendamentoAte)
      this.SET_FILTRO_ITEM(itemId)
      this.SET_FILTRO_RESPONSAVEL_PELA_EXECUCAO(responsavelId)
      this.SET_FILTRO_SITUACAO(situacao)
    },

    filtrar () {
      if (this.estaCarregando) {
        return
      }

      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      }

      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItens()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    alterar (item) {
      if (item.situacao !== 'P') {
        this.readOnly = true
      } else {
        this.readOnly = false
      }

      this.SET_ITEM_SELECIONADO_ID(item.id)
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.alterar(item)
        this.$refs.reposicaoAulaAvaliacao.reposicaoAulaAvaliacao = true
      }
    },

    adiconarReposicao () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$refs.reposicaoAulaAvaliacao.reposicaoAulaAvaliacao = true
      this.readOnly = false
    },

    concluir (item) {
      let msg = item.item.tipo_item === 'R' ? 'o make up class' : 'make up class & test'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM_SELECIONADO_ID(item.id)
          const obj = {
            id: item.id,
            aluno: item.aluno ? item.aluno.id : null,
            turma: item.turma ? item.turma.id : null,
            livro: item.livro ? item.livro.id : null,
            licao: item.licao ? item.licao.id : null,
            item: item.item ? item.item.id : null,
            isenta: item.isenta ? item.isenta : null,
            valor: item.valor ? item.valor : null,

            sala_franqueada: item.sala_franqueada ? item.sala_franqueada.id : null,
            usuario_solicitante: item.usuario_solicitante ? item.usuario_solicitante.id : null,
            responsavel_execucao: item.responsavel_execucao ? item.responsavel_execucao.id : null,
            descricao_atividade: item.descricao_atividade ? item.descricao_atividade : null,
            forma_cobranca: item.forma_cobranca ? item.forma_cobranca.id : null,
            ocorrencia_academica: item.ocorrencia_academica ? item.ocorrencia_academica.id : null,
            data: stringToISODate(item.data_hora_inicio),
            hora_inicio: converteHorarioParaBanco(converteHorarioBancoParaInputText(item.data_hora_inicio)),
            concluido: true
          }

          this.SET_ITEM_SELECIONADO(obj)

          this.atualizar().then(() => {
            this.filtrar()
          }).catch(error => {
            console.error(error)
          })
        }
      }, `Deseja concluir ${msg} ?`)
      this.SET_ITEM_SELECIONADO_ID(null)
      this.LIMPAR_ITEM_SELECIONADO()
    }
  }
}
</script>
<style scoped>
</style>
