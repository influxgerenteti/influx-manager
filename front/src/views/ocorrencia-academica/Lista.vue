<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <!-- <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div> -->
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
        <button v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" type="button" class="btn btn-azul" @click="adicionarNovo">
          <font-awesome-icon icon="plus" /> Adicionar
        </button>
      </div>

      <!-- <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="situacao_filtro_rapido" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-radio-group
                  id="situacao_filtro_rapido"
                  v-model="selected" :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_filtro_rapido"
                  @change="setSituacao"
                />
              </div>
            </div>
          </div>
        </form>
      </b-collapse> -->

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida=false, buscaAvancada = true, filtrar()">
          <div class="form-group row">

            <b-col md="3">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_nome_aluno'" for="nome_aluno" class="col-form-label">Aluno</label>
              <typeahead id="nome_aluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
              <!-- <typeahead id="nome_aluno" :item-hit="setNomeAluno" source-path="/api/aluno/buscar_nome_com_contrato" key-name="pessoa.nome_contato" /> -->
            </b-col>

            <b-col md="3">

              <label v-help-hint="'filtroAvancado-ocorrencia_academica_tipo_ocorrencia'" for="filtro_avancado_tipo_ocorrencia" class="col-form-label">Tipo ocorrência</label>
              <g-select id="tipo_ocorrencia"
                        :select="setTipoOcorrencia"
                        :value="tipo_ocorrencia"
                        :options="listaTipoOcorrencia"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </b-col>

            <!-- <b-col md="3">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_funcionario'" for="filtro_avancado_funcionario" class="col-form-label">Funcionário</label>
              <g-select id="funcionario"
                        :select="setFuncionario"
                        :value="funcionario"
                        :options="listaFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </b-col> -->

            <b-col md="3">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_responsavel'" for="filtro_avancado_responsavel" class="col-form-label">Responsável</label>
              <g-select id="responsavel"
                        :select="setFuncionarioResponsavel"
                        :value="funcionario_responsavel"
                        :options="listaFuncionario"
                        class="multiselect-truncate"
                        label="apelido"
                        track-by="id"
              />
            </b-col>

            <b-col md="auto">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_situacao'" for="filtro_avancado_situacao" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group
                  id="filtro_avancado_situacao"
                  v-model="situacaoSelecionada"
                  :options="listaSituacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  @change="setSituacao"
                />
              </div>
            </b-col>

            <b-col md="2">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_data_abertura'" for="data_abertura" class="col-form-label">Data abertura</label>
              <g-datepicker
                :element-id="'data_abertura'"
                :value="data_abertura"
                :selected="setDataAbertura"
              />
            </b-col>

            <b-col md="2">
              <label v-help-hint="'filtroAvancado-ocorrencia_academica_data_fechamento'" for="data_fechamento" class="col-form-label">Data Fechamento</label>
              <g-datepicker
                :element-id="'data_fechamento'"
                :value="data_fechamento"
                :selected="setDataFechamento"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroAvancado-ocorrencia_academcia_data_movimentacao'" class="col-form-label" for="data_movimentacao_de" >Data ultima movimentação</label>

              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_movimentacao_de'"
                      :value="data_movimentacao_de_temporario"
                      :selected="setDataMovimentacaoDeTemporario"/>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_movimentacao_ate'"
                      :value="data_movimentacao_ate_temporario"
                      :selected="setDataMovimentacaoAteTemporario"/>
                  </div>
                </div>
              </div>
              <div v-if="dataFiltroInvalida(data_movimentacao_de_temporario,data_movimentacao_ate_temporario)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="oca.data_criacao" class="size-95">Abertura</th>
            <th data-column="p.nome_contato" class="size-210">Aluno</th>
            <th data-column="to.descricao" class="size-210">Tipo</th>
            <th data-column=""  class="size-210">Resumo</th>
            <th data-column="f.apelido">Responsável</th>
            <th data-column="">Movimentação</th>
            <th data-column="" class="coluna-situacao-icone">Situação</th>
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
            <tr v-for="item in listaItens" :key="item.id" @dblclick="alterar(item)">

              <td data-label="Abertura" class="size-95">{{ item.data_criacao | formatarData }}</td>
              <td data-label="Aluno" v-b-tooltip :title="item.aluno.pessoa.nome_contato" class="size-210 d-block text-truncate">{{ item.aluno.pessoa.nome_contato }}</td>
              <td data-label="Tipo"  v-b-tooltip :title="item.tipo_ocorrencia.descricao"  class="size-210 d-block text-truncate">{{ item.tipo_ocorrencia.descricao }}</td>
              <td data-label="Resumo" v-b-tooltip :title=" item.ocorrenciaAcademicaDetalhes.length? item.ocorrenciaAcademicaDetalhes[0].texto : '--' "  class="size-210 d-block text-truncate">{{ item.ocorrenciaAcademicaDetalhes.length? item.ocorrenciaAcademicaDetalhes[0].texto : "--" }}</td>
              <td data-label="Responsável">{{ item.funcionario.apelido }}</td>
              <td data-label="Movimentação">{{ item.ocorrenciaAcademicaDetalhes.length ? item.ocorrenciaAcademicaDetalhes[0].data_criacao : '' | formatarData }}</td>
              <td data-label="Status" class="coluna-situacao-icone">

                <PillSituation 
                    :situation="item.situacao === 'A' ? 'Aberto' : 'Fechado'" 
                    :situationClass="item.situacao === 'A' ? 'ina' : 'ati'" 
                    :textTooltip="item.situacao === 'A' ? 'Aberto' : 'Fechado'"
                  >
                  </PillSituation>
              </td>

              <td class="d-flex coluna-icones">
                <!-- Ações -->
                <a v-b-tooltip.viewport.left.hover v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :title="item.situacao === 'A' ? 'Alterar' : 'Visualizar'" href="javascript:void(0)" class="icone-link" @click.prevent="alterar(item)">
                  <font-awesome-icon :icon="item.situacao === 'A' ? 'pen' : 'eye'" />
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

    <!-- Form ocorrência acadêmica -->
    <modal-formulario ref="modalFormularioOcorrenciaAcademica" :usuario-logado="usuarioLogado" :read-only="readOnly" :lista-de-tipo-ocorrencia="listaTipoOcorrencia" :lista-de-funcionarios="listaFuncionario" :cancelar-dados="cancelarModal" @filtrar="filtrar()" @hide="visibleFormularioOcorrenciaAcademica = false"/>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import ModalFormulario from './ModalFormulario.vue'
import {dateToString, beginOfDay, endOfDay, dateToCompare} from '../../utils/date'
// import EventBus from '../../utils/event-bus'
import PillSituation from '../../components/PillSituation.vue'

export default {
  name: 'ListaOcorrenciaAcademica',
  components: {
    ModalFormulario,
    PillSituation
  },
  data () {
    return {
      className: 'rapido-open',
      visibleFormularioOcorrenciaAcademica: false,
      detalhesDeOcorrencias: '',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: true,
      filtroRapido: false,
      filtroSelecionado: 2,
      situacaoSelecionada: null,
      aluno: null,
      tipo_ocorrencia: null,
      funcionario: null,
      funcionario_responsavel: null,
      data_abertura: '',
      data_fechamento: '',
      data_movimentacao_de_temporario: '',
      data_movimentacao_ate_temporario: '',
      listaSituacao: [
        {text: 'Aberta', value: 'A'},
        {text: 'Fechado', value: 'F'}
      ],
      readOnly: false
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('ocorrenciaAcademica', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('tipoOcorrencia', {listaTipoOcorrenciaRequisicao: 'lista'}),
    ...mapState('funcionario', {listaFuncionarioRequisicao: 'lista'}),
    ...mapState('root', {usuarioLogado: 'usuarioLogado'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaFuncionario: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaFuncionarioRequisicao]
        // return this.listaFuncionarioRequisicao
      }
    },

    listaTipoOcorrencia: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaTipoOcorrenciaRequisicao.filter(el => el.situacao == 1)]
        // return this.listaTipoOcorrenciaRequisicao
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
    ...mapActions('ocorrenciaAcademica', {listarItens: 'listar'}),
    ...mapActions('funcionario', {listarFuncionario: 'listar'}),
    ...mapActions('tipoOcorrencia', {listarTipoOcorrencia: 'listar'}),
    ...mapMutations('funcionario', {SET_PAGINA_ATUAL_FUNCIONARIO: 'SET_PAGINA_ATUAL', SET_LISTA_FUNCIONARIO: 'SET_LISTA'}),
    ...mapMutations('tipoOcorrencia', {SET_PAGINA_ATUAL_TIPO_OCORRENCIA: 'SET_PAGINA_ATUAL', SET_LISTA_TIPO_OCORRENCIA: 'SET_LISTA'}),
    ...mapMutations('ocorrenciaAcademica', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_FILTRO_ALUNO', 'SET_FILTRO_TIPO_OCORRENCIA', 'SET_FILTRO_USUARIO',
      'SET_FILTRO_FUNCIONARIO_RESPONSAVEL', 'SET_FILTRO_SITUACAO', 'SET_FILTRO_DATA_ABERTURA', 'SET_FILTRO_DATA_FECHAMENTO', 'SET_FILTRO_DATA_MOVIMENTACAO_DE', 'SET_FILTRO_DATA_MOVIMENTACAO_ATE', 'SET_TIPO_OCORRENCIA_ID', 'SET_ID',
      'SET_FUNCIONARIO', 'SET_USUARIO_ID', 'SET_ALUNO_ID', 'SET_ORDER_BY']),

    dateToString: dateToString,
    carregarMais () {
      this.listarItens()
    },

    limparStateAnterior () {
      this.SET_FILTRO_ALUNO(null)
      this.SET_FILTRO_TIPO_OCORRENCIA(null)
      // this.SET_FILTRO_USUARIO(null)
      this.SET_FILTRO_FUNCIONARIO_RESPONSAVEL(null)
      this.SET_FILTRO_SITUACAO(null)
      this.SET_FILTRO_DATA_ABERTURA(null)
      this.SET_FILTRO_DATA_FECHAMENTO(null)
      this.SET_FILTRO_DATA_MOVIMENTACAO_DE(null)
      this.SET_FILTRO_DATA_MOVIMENTACAO_ATE(null)
    },

    listarCamposSelects () {
      this.SET_PAGINA_ATUAL_TIPO_OCORRENCIA(1)
      this.SET_PAGINA_ATUAL_FUNCIONARIO(1)
      this.SET_LISTA_TIPO_OCORRENCIA([])
      this.SET_LISTA_FUNCIONARIO([])
      this.listarTipoOcorrencia()

      this.$store.commit('funcionario/SET_FILTROS', { consultor_ou_gestor_comercial: true })
      this.listarFuncionario()
      // if (this.usuarioLogado.funcionarios) {
      //   let funcionarioLogado = this.usuarioLogado.funcionarios[0]
      //   this.setFuncionario(funcionarioLogado)
      // }
    },

    setNomeAluno (value) {
      this.aluno = value
    },

    setTipoOcorrencia (value) {
      this.tipo_ocorrencia = value
    },

    setSituacao (value) {
      this.selected = value
    },

    setFuncionario (value) {
      this.funcionario = value
    },

    setFuncionarioResponsavel (valeu) {
      this.funcionario_responsavel = valeu
    },

    setDataAbertura (value) {
      this.data_abertura = value
    },

    setDataFechamento (value) {
      this.data_fechamento = value
    },

    setDataMovimentacaoDeTemporario (value) {
      this.data_movimentacao_de_temporario = value
    },

    setDataMovimentacaoAteTemporario (value) {
      this.data_movimentacao_ate_temporario = value
    },

    // executaFiltroRapido () {
    //   // TODO: Adicionar os states de filtro Rapido
    // },

    executaFiltroAvancado () {
      let alunoId = this.aluno ? this.aluno.id : null
      let tipoOcorrenciaId = this.tipo_ocorrencia ? this.tipo_ocorrencia.id : null
      // let usuarioId = this.funcionario ? this.funcionario.id : null
      let funcionarioResponsavelId = this.funcionario_responsavel ? this.funcionario_responsavel.id : null
      let situacao = this.situacaoSelecionada ? this.situacaoSelecionada : null
      let dataAbertura = this.data_abertura ? beginOfDay(this.data_abertura) : null
      let dataFechamento = this.data_fechamento ? endOfDay(this.data_fechamento) : null
      let dataMovimentacoaDe = this.data_movimentacao_de_temporario ? beginOfDay(this.data_movimentacao_de_temporario) : null
      let dataMovimentacoaAte = this.data_movimentacao_ate_temporario ? endOfDay(this.data_movimentacao_ate_temporario) : null

      this.SET_FILTRO_ALUNO(alunoId)
      this.SET_FILTRO_TIPO_OCORRENCIA(tipoOcorrenciaId)
      // this.SET_FILTRO_USUARIO(usuarioId)
      this.SET_FILTRO_FUNCIONARIO_RESPONSAVEL(funcionarioResponsavelId)
      this.SET_FILTRO_SITUACAO(situacao)
      this.SET_FILTRO_DATA_ABERTURA(dataAbertura)
      this.SET_FILTRO_DATA_FECHAMENTO(dataFechamento)
      this.SET_FILTRO_DATA_MOVIMENTACAO_DE(dataMovimentacoaDe)
      this.SET_FILTRO_DATA_MOVIMENTACAO_ATE(dataMovimentacoaAte)

    },

    dataFiltroInvalida (dataIni, dataFim) {
      return dateToCompare(dataIni) > dateToCompare(dataFim) && dataFim !== ''
    },

    adicionarNovo () {
      this.readOnly = false
      this.$refs.modalFormularioOcorrenciaAcademica.SET_ITEM_SELECIONADO_ID(null)
      this.$refs.modalFormularioOcorrenciaAcademica.limparCamposFormulario()
      this.$refs.modalFormularioOcorrenciaAcademica.visibleFormularioOcorrenciaAcademica = true
    },

    cancelarModal () {
      this.LIMPAR_ITEM_SELECIONADO()
      // this.$refs.modalFormularioOcorrenciaAcademica.hide()
      this.$refs.modalFormularioOcorrenciaAcademica.visibleFormularioOcorrenciaAcademica = false
      this.filtrar()
      this.$refs.modalFormularioOcorrenciaAcademica.isEdit = false
    },

    filtrar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.limparStateAnterior()
      this.SET_LISTA([])
      // if (this.filtroSelecionado === 1) {
      //   this.executaFiltroRapido()
      // }
      // else {
      this.executaFiltroAvancado()
      // }
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    limparFiltros () {
      this.aluno = null
      this.tipo_ocorrencia = null
      this.funcionario = null
      this.funcionario_responsavel = null
      this.situacaoSelecionada = null
      this.data_abertura = ''
      this.data_fechamento = ''
      this.data_movimentacao_de_temporario = ''
      this.data_movimentacao_ate_temporario = ''
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_LISTA([])
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          this.readOnly = true
        } else {
          this.readOnly = false
        }

        this.$refs.modalFormularioOcorrenciaAcademica.SET_ITEM_SELECIONADO_ID(item.id)
        this.$refs.modalFormularioOcorrenciaAcademica.visibleFormularioOcorrenciaAcademica = true
        this.$refs.modalFormularioOcorrenciaAcademica.buscarDadosFormulario()
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
</style>
