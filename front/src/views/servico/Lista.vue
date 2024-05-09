<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
        </div>
        <button v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" type="button" class="btn btn-azul" @click="adicionarNovo">
          <font-awesome-icon icon="plus" /> Adicionar
        </button>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">

            <b-col md="3">
              <label class="col-form-label" for="numero_protocolo">Nº Protocolo</label>
              <typeahead id="numero_protocolo" :item-hit="setProtocolo" source-path="/api/servico/buscar-numero-protocolo" key-name="servico.protocolo" />
            </b-col>

            <b-col md="4">
              <label class="col-form-label" for="nome_aluno">Aluno</label>
              <typeahead id="nome_aluno" :item-hit="setNomeAluno" source-path="/api/interessado/buscar-nome" key-name="nome" />
            </b-col>

            <b-col md="auto">
              <label class="col-form-label" for="filtroRapido-servico_situacao">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="filtroRapido-servico_situacao"
                  v-model="situacaoSelecionada"
                  :options="listaSituacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  @change="setSituacao"
                />
              </div>
            </b-col>

            <b-col md="6">
              <label v-help-hint="'filtroRapido-servico_data_solicitacao'" class="col-form-label" for="data_solicitacao_de" >Solicitado entre</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <calendar id="data_solicitacao_de" v-model="data_solicitacao_de_temporaria" @input="setDataSolicitacaoDeTemporario" />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <calendar id="data_solicitacao_ate" v-model="data_solicitacao_ate_temporaria" @input="setDataSolicitacaoAteTemporario" />
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_solicitacao_de_temporaria,data_solicitacao_ate_temporaria)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

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
            <th data-column="s.protocolo">Nº Protocolo</th>
            <th data-column="p.nome_contato">Aluno</th>
            <th data-column="i.descricao">Serviço</th>
            <th data-column="s.data_solicitacao">Data solicitação</th>
            <th data-column="s.data_conclusao">Data prevista</th>
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

            <tr v-for="itemServico in listaItens" :key="itemServico.id" @dblclick="editar(itemServico.id)">
              <td data-label="Nº Protocolo">{{ itemServico.protocolo }}</td>
              <td data-label="Aluno">{{ itemServico.aluno.pessoa.nome_contato }}</td>
              <td data-label="Serviço">{{ itemServico.item.descricao }}</td>
              <td data-label="Data solicitação">{{ itemServico.data_solicitacao | formatarData }}</td>
              <td data-label="Data prevista">{{ itemServico.data_conclusao | formatarData }}</td>
              <td data-label="">
                {{ mostraSituacao(itemServico.situacao) }}
                <!-- <span v-b-tooltip.viewport.left.hover :title="itemServico.situacao" :class="'circle-badge-' + itemServico.situacao.toLowerCase()" class="circle-badge"></span> -->
              </td>
              <td class="d-flex coluna-icones">
                <a v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="itemServico.situacao === 'EA' ? null : 'disable-icon'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterar(itemServico)">
                  <font-awesome-icon icon="pen" />
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

    <formulario-adicionar ref="modalFormularioAdicionar" @filtrar="filtrar()" />
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'
import {beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
import FormularioAdicionar from './FormularioAdicionar'

export default {
  name: 'ListaServico',
  components: {
    'formulario-adicionar': FormularioAdicionar
  },
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selected: 0,
      protocoloTemporario: null,
      alunoTemporario: null,
      situacaoSelecionada: [],
      listaSituacao: [
        {id: 1, text: 'Em andamento', value: 'EA'},
        {id: 2, text: 'Concluído', value: 'C'},
        {id: 3, text: 'Cancelada', value: 'CAN'}
      ],
      data_solicitacao_de_temporaria: undefined,
      data_solicitacao_ate_temporaria: undefined
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('servico', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.selected = 0
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
  },
  methods: {
    ...mapActions('servico', {listarItens: 'listar'}),
    ...mapMutations('servico', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_PROTOCOLO', 'SET_FILTRO_SITUACAO', 'SET_FILTRO_ALUNO_ID', 'SET_DATA_SOLICITACAO_DE', 'SET_DATA_SOLICITACAO_ATE', 'SET_ORDER_BY']),

    mostraSituacao (value) {
      if (value === 'EA') {
        return 'Em andamento'
      }
      if (value === 'C') {
        return 'Concluído'
      }
      if (value === 'CAN') {
        return 'Cancelado'
      }
    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== undefined
    },

    carregarMais () {
      this.listarItens()
    },

    setNomeAluno (value) {
      this.alunoTemporario = value
      this.filtrar()
    },

    setProtocolo (value) {
      this.protocoloTemporario = value
      this.filtrar()
    },

    setSituacao (value) {
      this.situacaoSelecionada = value
      this.filtrar()
    },

    setDataSolicitacaoDeTemporario (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_solicitacao_de_temporaria || this.data_solicitacao_de_temporaria.length === 10) {
        this.filtrar()
      }
    },

    setDataSolicitacaoAteTemporario (value) {
      if (this.estaCarregando) {
        return
      }

      if (!this.data_solicitacao_ate_temporaria || this.data_solicitacao_ate_temporaria.length === 10) {
        this.filtrar()
      }
    },

    limparStateAnterior () {
      // TODO: Adicionar os Mutations do modulo para retornar os valores para nulo
    },

    executaFiltroRapido () {
      let protocolo = this.protocoloTemporario ? this.protocoloTemporario.protocolo : null
      let alunoId = this.alunoTemporario ? this.alunoTemporario.id : null
      let situacao = this.situacaoSelecionada ? this.situacaoSelecionada : null

      let dataSolicitacaoDe = this.data_solicitacao_de_temporaria ? beginOfDay(this.data_solicitacao_de_temporaria) : null
      let dataSolicitacaoAte = this.data_solicitacao_ate_temporaria ? endOfDay(this.data_solicitacao_ate_temporaria) : null

      this.SET_FILTRO_PROTOCOLO(protocolo)
      this.SET_FILTRO_ALUNO_ID(alunoId)
      this.SET_FILTRO_SITUACAO(situacao)
      this.SET_DATA_SOLICITACAO_DE(dataSolicitacaoDe)
      this.SET_DATA_SOLICITACAO_ATE(dataSolicitacaoAte)
    },

    filtrar () {
      this.limparStateAnterior()
      this.executaFiltroRapido()
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItens()
    },

    limparFiltros () {
      // TODO: Adicionar os states para realizar a limpeza do filtro
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'EA') {
          return
        }
      }

      this.SET_ITEM_SELECIONADO_ID(item.id)
      this.$refs.modalFormularioAdicionar.$refs.formAdicionar = true
      EventBus.$emit('form-incluir:abrir')
    },

    adicionarNovo () {
      this.limparFormAdicionar()
      this.$refs.modalFormularioAdicionar.$refs.formAdicionar = true
      EventBus.$emit('form-incluir:abrir')
    },

    limparFormAdicionar () {
      this.$store.commit('servico/LIMPAR_ITEM_SELECIONADO')
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
