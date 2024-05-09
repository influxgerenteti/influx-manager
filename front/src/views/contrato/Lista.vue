<!-- eslint-disable vue/no-unused-components -->
<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 1">Filtros</div>
        </div>

        <!-- <router-link :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link> -->
      </div>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaRapida = false, filtrar()">
          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'filtro-contrato_filtroAluno'" for="filtroAluno" class="col-form-label">Aluno</label>
              <typeahead id="filtroAluno" :item-hit="setFiltroAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
            </b-col>

            <b-col md="2">
              <label v-help-hint="'filtro-contrato_filtroNumeroContrato'" for="filtroNumeroContrato" class="col-form-label">Número do contrato</label>
              <input id="filtroNumeroContrato" v-model="filtros.numero_contrato" type="text" class="form-control">
            </b-col>

            <b-col md="6">
              <label v-help-hint="'filtro-contrato_situacao_filtro'" for="situacao_filtro" class="col-form-label">Situação do contrato</label>
              <div>
                <b-form-checkbox-group id="situacao_filtro" v-model="filtros.situacao" :options="situacaoFiltro" buttons button-variant="cinza" name="situacao_filtro" class="checkbtn-line"/>
              </div>
            </b-col>
          </b-row>

          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'filtro-contrato_data_inicio_contrato_inicio'" for="data_inicio_contrato_inicio" class="col-form-label">Data início de</label>
              <g-datepicker :value="filtros.data_inicio_contrato_inicio" :selected="setFiltroDataInicioContratoInicio" element-id="data_inicio_contrato_inicio" maxlength="10" />
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtro-contrato_data_inicio_contrato_fim'" for="data_inicio_contrato_fim" class="col-form-label">Data início até</label>
              <g-datepicker :value="filtros.data_inicio_contrato_fim" :selected="setFiltroDataInicioContratoFim" element-id="data_inicio_contrato_fim" maxlength="10" />
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtro-contrato_data_termino_contrato_inicio'" for="data_termino_contrato_inicio" class="col-form-label">Data término de</label>
              <g-datepicker :value="filtros.data_termino_contrato_inicio" :selected="setFiltroDataTerminoContratoInicio" element-id="data_termino_contrato_inicio" maxlength="10" />
            </b-col>

            <b-col md="3">
              <label v-help-hint="'filtro-contrato_termino_contrato_fim'" for="data_termino_contrato_fim" class="col-form-label">Data término até</label>
              <g-datepicker :value="filtros.data_termino_contrato_fim" :selected="setFiltroDataTerminoContratoFim" element-id="data_termino_contrato_fim" maxlength="10" />
            </b-col>
          </b-row>

          <b-row class="form-group">
            <b-col md="4">
              <label v-help-hint="'filtro-contrato_filtroResponsavelCarteira'" for="filtroResponsavelCarteira" class="col-form-label">Responsável pela carteira</label>
              <g-select
                id="filtroResponsavelCarteira"
                :value="filtros.responsavel_carteira_funcionario"
                :select="setFiltroResponsavelCarteiraFuncionario"
                :options="listaFuncionarios"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtro-contrato_filtroResponsavelVenda'" for="filtroResponsavelVenda" class="col-form-label">Responsável pela venda</label>
              <g-select
                id="filtroResponsavelVenda"
                :value="filtros.responsavel_venda_funcionario"
                :select="setFiltroResponsavelVendaFuncionario"
                :options="listaFuncionarios"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
            </b-col>
          </b-row>

          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="al.id" class="size-85">Contrato</th>
            <th data-column="ct.data_inicio_contrato" class="coluna-data">Início</th>
            <th data-column="ct.data_termino_contrato" class="coluna-data">Término</th>
            <th data-column="pess.nome_contato">Aluno</th>
            <th data-column="cur.descricao" class="size-85">Curso</th>
            <th data-column="tm.descricao">Turma</th>
            <th data-column="lv.descricao" class="size-85">Livro</th>
            <th class="size-85 text">Informações</th>
            <th class="coluna-situacao size-85">Situação</th>
            <th class="coluna-icones">Atualizar</th>
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

            <tr v-for="item in lista" :key="item.id" @dblclick="editar(item.id)">
              <td class="size-85">{{ `${item.aluno.id}/${item.sequencia_contrato}` }}</td>
              <td class="coluna-data">{{ item.data_inicio_contrato | formatarData }}</td>
              <td class="coluna-data">{{ item.data_termino_contrato | formatarData }}</td>
              <td v-b-tooltip :title="item.aluno.pessoa.nome_contato" class="d-block text-truncate">{{ item.aluno.pessoa.nome_contato }}</td>
              <td v-b-tooltip :title="item.curso ? item.curso.descricao : '' " class="size-85 d-block text-truncate">{{ item.curso ? item.curso.descricao : '' }}</td>
              <td v-b-tooltip :title="retornaTurmaDescricao(item)" class="d-block text-truncate">{{ retornaTurmaDescricao(item) }}</td>
              <td class="size-85">{{ item.livro.descricao }}</td>
              <td class="coluna-situacao">
                <a
                  href="#"
                  class="icone-link"
                  @click.prevent="openModal( item)"
                >
                <font-awesome-icon  style="margin-right: 4px;" icon="file"/>
                </a>             
              </td>
              <td class="size-85">
                <PillSituation 
                    :situation="situacoes[item.situacao] + (item.situacao === 'T' && item.data_cancelamento ? ' - ' +' Desde: '+ formatarDataTool(item.data_cancelamento) : '')"
                    :situationClass="item.situacao.toLowerCase()" 
                    :textTooltip="situacoes[item.situacao] + (item.situacao === 'T' && item.data_cancelamento ? ' - ' +' Desde: '+ formatarDataTool(item.data_cancelamento) : '')"
                  >
                  </PillSituation>
              </td>

              <td class="d-flex coluna-icones">
                <router-link v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.id}`" class="icone-link" title="Atualizar">
                  <font-awesome-icon icon="pen" />
                </router-link>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="info-btn">
      </div>

      <div class="info-label">
        <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
        <small v-else>Nenhum item encontrado</small>
      </div>
    </div>
    <b-modal
      id="assinaturaModal"
      ref="assinaturaModal"
      size="sm"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
    >
      <form-assinatura-contrato ref="formAssinaturaContrato" />
    </b-modal>
  </div>
  

</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import Typeahead from '../../components/Typeahead.vue'
import PillSituation from '../../components/PillSituation.vue'
import FormAssinaturaContrato from "./FormAssinaturaContrato.vue";
import item from '@/store/item';


export default {
  components: {
    Typeahead,
    // eslint-disable-next-line vue/no-unused-components
    FormAssinaturaContrato,
    PillSituation
  },

  data () {
    return {
      className: 'filtro-open',
      filtroAvancado: false,
      filtroSelecionado: null,
      situacoes: {
        V: 'Vigente',
        E: 'Encerrado',
        R: 'Rescindido',
        C: 'Cancelado',
        T: 'Trancado'
      },
      situacaoFiltro: [
        {value: 'V', text: 'Vigente'},
        {value: 'E', text: 'Encerrado'},
        {value: 'R', text: 'Rescindido'},
        {value: 'C', text: 'Cancelado'},
        {value: 'T', text: 'Trancado'}
      ]
    }
  },

  computed: {
    ...mapState('contrato', ['lista', 'filtros', 'estaCarregando', 'totalItens', 'todosItensCarregados']),
    ...mapState('modulos', ['permissoes']),

    listaFuncionarios: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.$store.state.funcionario.lista]
      }
    },

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  mounted () {
    this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('funcionario/listar')
    this.filtrar()
  },

  methods: {
    ...mapActions('contrato', ['listar', 'listarContratos','atualizar']),
    ...mapMutations('contrato', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listarContratos()
    },
    formatarDataTool(data) {
      const options = { day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
      return new Date(data).toLocaleString('pt-BR', options);
    },

    retornaTurmaDescricao (item) {
      if (item.turma) {
        return item.turma.descricao
      } else {
        return 'Personal'
      }
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarContratos()
    },

    editar (id) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push(`${this.$route.path}/atualizar/${id}`)
      }
    },

    filtrar () {
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarContratos()
    },

    setFiltroAluno (value) {
      this.filtros.aluno = value
    },

    setFiltroDataInicioContratoInicio (value) {
      this.filtros.data_inicio_contrato_inicio = value
    },

    setFiltroDataInicioContratoFim (value) {
      this.filtros.data_inicio_contrato_fim = value
    },

    setFiltroDataTerminoContratoInicio (value) {
      this.filtros.data_termino_contrato_inicio = value
    },

    setFiltroDataTerminoContratoFim (value) {
      this.filtros.data_termino_contrato_fim = value
    },

    setFiltroResponsavelCarteiraFuncionario (value) {
      this.filtros.responsavel_carteira_funcionario = value
    },

    setFiltroResponsavelVendaFuncionario (value) {
      this.filtros.responsavel_venda_funcionario = value
    },
    openModal(item) {
      console.log(item);
      
      
      this.$refs.assinaturaModal.show('assinaturaModal');
      
      //espera a tela carregar, se der problema implementar um callback
      setTimeout(() => {
        this.$refs.formAssinaturaContrato.item = item;        
        this.$refs.formAssinaturaContrato.onClose = this.onCloseModalAssinatura;
      }, 500);
    },
    onCloseModalAssinatura(){
      this.$refs.assinaturaModal.hide('assinaturaModal');
    }
  }
}
</script>

<style scoped>
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
.icon-assinado
  {
    max-width: 50px;
    max-height: 50px;
    background-image: url("data:image/svg+xml,%3Csvg%20version%3D%221.1%22%20id%3D%22fi_684831%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%20x%3D%220px%22%20y%3D%220px%22%20viewBox%3D%220%200%20512%20512%22%20style%3D%22enable-background%3Anew%200%200%20512%20512%3B%22%20xml%3Aspace%3D%22preserve%22%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M498.791%2C161.127c-17.545-17.546-46.094-17.545-63.645%2C0.004c-5.398%2C5.403-39.863%2C39.896-45.128%2C45.166V87.426%09%09%09c0-12.02-4.681-23.32-13.181-31.819L334.412%2C13.18C325.913%2C4.68%2C314.612%2C0%2C302.592%2C0H45.018c-24.813%2C0-45%2C20.187-45%2C45v422%09%09%09c0%2C24.813%2C20.187%2C45%2C45%2C45h300c24.813%2C0%2C45-20.187%2C45-45V333.631L498.79%2C224.767C516.377%2C207.181%2C516.381%2C178.715%2C498.791%2C161.127%09%09%09z%20M300.019%2C30c2.834%2C0%2C8.295-0.491%2C13.18%2C4.393l42.426%2C42.427c4.76%2C4.761%2C4.394%2C9.978%2C4.394%2C13.18h-60V30z%20M360.018%2C467%09%09%09c0%2C8.271-6.728%2C15-15%2C15h-300c-8.271%2C0-15-6.729-15-15V45c0-8.271%2C6.729-15%2C15-15h225v75c0%2C8.284%2C6.716%2C15%2C15%2C15h75v116.323%09%09%09c0%2C0-44.254%2C44.292-44.256%2C44.293l-21.203%2C21.204c-1.646%2C1.646-2.888%2C3.654-3.624%2C5.863l-21.214%2C63.64%09%09%09c-1.797%2C5.39-0.394%2C11.333%2C3.624%2C15.35c4.023%2C4.023%2C9.968%2C5.419%2C15.35%2C3.624l63.64-21.213c2.209-0.736%2C4.217-1.977%2C5.863-3.624%09%09%09l1.82-1.82V467z%20M326.378%2C312.427l21.213%2C21.213l-8.103%2C8.103l-31.819%2C10.606l10.606-31.82L326.378%2C312.427z%20M368.8%2C312.422%09%09%09l-21.213-21.213c11.296-11.305%2C61.465-61.517%2C72.105-72.166l21.213%2C21.213L368.8%2C312.422z%20M477.573%2C203.558l-15.463%2C15.476%09%09%09l-21.213-21.213l15.468-15.481c5.852-5.849%2C15.366-5.848%2C21.214%2C0C483.426%2C188.19%2C483.457%2C197.673%2C477.573%2C203.558z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M285.018%2C150h-210c-8.284%2C0-15%2C6.716-15%2C15s6.716%2C15%2C15%2C15h210c8.284%2C0%2C15-6.716%2C15-15S293.302%2C150%2C285.018%2C150z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M225.018%2C210h-150c-8.284%2C0-15%2C6.716-15%2C15s6.716%2C15%2C15%2C15h150c8.284%2C0%2C15-6.716%2C15-15S233.302%2C210%2C225.018%2C210z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M225.018%2C270h-150c-8.284%2C0-15%2C6.716-15%2C15s6.716%2C15%2C15%2C15h150c8.284%2C0%2C15-6.716%2C15-15S233.302%2C270%2C225.018%2C270z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M225.018%2C330h-150c-8.284%2C0-15%2C6.716-15%2C15s6.716%2C15%2C15%2C15h150c8.284%2C0%2C15-6.716%2C15-15S233.302%2C330%2C225.018%2C330z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%09%3Cg%3E%09%09%3Cpath%20d%3D%22M285.018%2C422h-90c-8.284%2C0-15%2C6.716-15%2C15s6.716%2C15%2C15%2C15h90c8.284%2C0%2C15-6.716%2C15-15S293.302%2C422%2C285.018%2C422z%22%3E%3C%2Fpath%3E%09%3C%2Fg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3Cg%3E%3C%2Fg%3E%3C%2Fsvg%3E");
    background-size:cover;
    background-repeat: no-repeat;
  }

  .login-container {
    border: 0;
    background-color: transparent;
    background-image: url("../../assets/img/icons/icon_novo.png");
    background-size: 7em;
    height: 125px;
    transition: all .2s ease;
}
@media (min-width: 769px){
  .table-sm .coluna-icones {
      min-width: 60px;
      max-width: 120px;
  }
}

</style>
