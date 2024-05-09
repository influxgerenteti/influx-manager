<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" aria-controls="filtros-rapidos" aria-expanded="true" class="btn filtro-selecionado" @click="filtroRapido = !filtroRapido, className = filtroRapido ? 'filtro-open' : null, filtroSelecionado = 1">
            Filtros
          </div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true, buscaAvancada = false, filtrar()">
          <div class="form-group row mb-0">
            <div class="col-md-4">
              <label for="busca_empresa" class="col-form-label">Nome fantasia</label>
              <typeahead id="busca_empresa" :item-hit="setEmpresaId" source-path="/api/pessoa/buscar/empresa" key-name="nome_fantasia" />
            </div>
            <!-- <div class="col-md-4">
              <label for="busca_cnpj" class="col-form-label">CNPJ</label>
              <input v-mask="'##.###.###/####-##'" id="busca_cnpj" v-model="cnpjFiltro" type="text" class="form-control">
            </div> -->
            <div class="col-md-4">
              <label for="busca_unidade_responsavel" class="col-form-label">Unidade responsável</label>
              <input id="busca_unidade_responsavel" v-model="filtros.unidade_responsavel" type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="busca_razao_social" class="col-form-label">Razão social</label>
              <input id="busca_razao_social" v-model="filtros.razao_social" type="text" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="busca_segmento_empresa" class="col-form-label">Segmento</label>
              <g-select id="busca_segmento_empresa"
                        :select="setSegmento"
                        :value="segmento_empresa_temp"
                        :options="listaSegmento"
                        label="descricao"
                        track-by="id"
              />
            </div>

            <div class="col-md-4">
              <label v-help-hint="'filtroRapido-busca_data_de_cadastro_de'" class="col-form-label" for="busca_data_de_cadastro_de">Data de cadastro</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                      <v-date-picker
                        v-model="filtros.data_de_cadastro_de"
                        :input-props="{ id: 'busca_data_de_cadastro_de', class: 'form-control', placeholder: 'Data', autocomplete: 'off' }"
                        :popover="{ visibility: 'click' }"
                        :attributes="attributes"
                      />
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Até</div>
                      <v-date-picker
                        v-model="filtros.data_de_cadastro_ate"
                        :input-props="{ id: 'busca_data_de_cadastro_ate', class: 'form-control', placeholder: 'Data', autocomplete: 'off' }"
                        :popover="{ visibility: 'click' }"
                        :attributes="attributes"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <!-- <div v-if="dataFiltroInvalida(data_agendamento_de,data_agendamento_ate)" class="floating-message bg-danger">
                Data inicial deve ser menor que a data final!
              </div> -->
            </div>

            <!-- <div class="col-md-4">
              <label for="busca_data_de_cadastro" class="col-form-label">Data de cadastro</label>
              <v-date-picker
                v-model="filtros.data_de_cadastro"
                :input-props="{ id: 'busca_data_de_cadastro', class: 'form-control', placeholder: 'Data', required: true, autocomplete: 'off' }"
                :popover="{ visibility: 'click' }"
                :attributes="attributes"
              />
            </div> -->

            <div class="col-md-4">
              <label for="situacao" class="col-form-label">Situação</label>
              <g-select id="situacao"
                        :select="setSituacao"
                        :value="filtroSituacao"
                        :options="situacao"
                        label="text"
                        track-by="id"
              />
            </div>

            <div class="col-md-4">
              <label for="etapas_convenio" class="col-form-label">Status do convênio</label>
              <g-select id="etapas_convenio"
                        :select="setFiltroEtapasConvenios"
                        :value="filtroEtapasConvenio"
                        :options="listaEtapasConvenio"
                        label="descricao"
                        track-by="id"
              />
            </div>

            <div class="col-md-4">
              <label for="cidade" class="col-form-label">Cidade/Município</label>
              <g-select id="cidade"
                        :select="setCidade"
                        :value="filtroCidadeMunicipio"
                        :options="listaDeCidadeMunicipio"
                        label="nome"
                        track-by="id"
              />
            </div>

            <div class="col-md-12">
              <b-form-group label="Abrangência">
                <b-form-radio-group id="abrangencia_filtro" v-model="filtroAbrangencia" name="abrangencia_filtro">
                  <b-form-radio :value="0">Todos</b-form-radio>
                  <b-form-radio :value="2">Nacionais</b-form-radio>
                  <b-form-radio :value="3">Estaduais</b-form-radio>
                </b-form-radio-group>
              </b-form-group>
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase mt-3" @click="filtroRapido=false">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="p.nome_contato" class="size-205">Nome Fantasia</th>
            <th data-column="" class="size-205">Unidade responsável</th>
            <th data-column="conv.est" class="size-205">Estado</th>
            <th data-column="conv.abrangencia_nacional" class="size-205">Abrangência</th>
            <th data-column="conv.beneficiario_colaboradores, conv.beneficiario_dependentes, conv.beneficiario_associados, conv.beneficiario_terceiros, conv.beneficiario_estagiarios, conv.beneficiario_alunos" class="size-205">Beneficiários</th>
            <th data-column="conv.data_cadastro" class="size-205">Data de Cadastro</th>
            <th data-column="" class="size-205">Status</th>
            <th data-column="" class="coluna-situacao size-205">Situação</th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in listaItems" :key="item.id" >
              <td v-b-tooltip.hover.bottom="item.pessoa.nome_fantasia" :title="item.pessoa.nome_fantasia" data-label="Empresa" class="size-205">
                <span>{{ item.pessoa.nome_fantasia }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="item.franqueada ? item.franqueada.nome : ''" data-label="Unidade responsável" class="size-205">
                <span>{{ item.franqueada ? item.franqueada.nome : '' }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="item.pessoa.estado ? item.pessoa.estado.nome : 'Não cadastrada.'" :title="item.pessoa.estado ? item.pessoa.estado.nome : 'Não cadastrada.'" data-label="Estado" class="size-205">
                <span>{{ item.pessoa.cidade ? item.pessoa.estado.nome : 'Não cadastrada.' }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="retornaAbrangenciaDescricao(item)" :title="retornaAbrangenciaDescricao(item)" data-label="Abrangência" class="size-205">
                <span>{{ retornaAbrangenciaDescricao(item) }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="retornaBeneficiariosDescricao(item)" :title="retornaBeneficiariosDescricao(item)" data-label="Beneficiários" class="size-205">
                <span>{{ retornaBeneficiariosDescricao(item) }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="$options.filters.formatarData(item.data_cadastro)" :title="$options.filters.formatarData(item.data_cadastro)" data-label="Data de Cadastro" class="size-205">
                <span>{{ item.data_cadastro | formatarData }}</span>
              </td>
              <td v-b-tooltip.hover.bottom="''" :title="''" data-label="Status" class="size-205">
                <span>
                  {{ getStatus(item) }}
                </span>
              </td>
              <td class="coluna-situacao size-205">
                <PillSituation 
                    :situation="getSituacao(item.situacao)"
                    :situationClass="item.situacao.toLowerCase()" 
                    :textTooltip="getSituacao(item.situacao)"
                >
                </PillSituation>
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
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {formatarCNPJ} from '../../utils/format'
import PillSituation from '../../components/PillSituation.vue';
export default {
  name: 'ListaConveniosNacionais',
  components: {
    PillSituation
  },
  data () {
    return {
      pessoaContato: '',
      cnpjFiltro: '',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: false,
      segmento_empresa_temp: {id: null, descricao: 'Selecione'},
      filtroSelecionado: 1,
      filtroSituacao: null,
      filtroAbrangencia: 0,
      filtroEtapasConvenio: null,
      filtroCidadeMunicipio: null,
      situacao: [
        {id: null, text: 'Selecione', value: null},
        {id: 1, text: 'Ativo', value: 'ATI'},
        {id: 2, text: 'Inativo', value: 'I'}
      ],
      empresaId: null,
      attributes: [
        {
          highlight: { class: 'today-mark' },
          dates: new Date()
        }
      ]
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('root', ['usuarioLogado']),
    ...mapState('convenio', {listaItems: 'lista', estaCarregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados', totalItens: 'totalItens', filtros: 'filtrosNacionais'}),
    ...mapState('segmentoEmpresaConvenio', {listaSegmentoRequisicao: 'lista'}),
    ...mapState('negociacaoParceriaWorkflow', {listaNegociacaoParceriaWorkflow: 'lista'}),
    ...mapState('etapasConvenio', {listaEtapasConvenioRequisicao: 'lista'}),
    ...mapState('cidade', {listaDeCidadeMunicipioRequisicao: 'lista'}),
    ...mapState('franqueadas', {objFranqueada: 'objFranqueada', estaCarregandoFranqueada: 'estaCarregando'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItems.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaSegmento: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaSegmentoRequisicao]
      }
    },

    listaEtapasConvenio: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaEtapasConvenioRequisicao]
      }
    },

    listaDeCidadeMunicipio: {
      get () {
        return [{id: null, nome: 'Selecione'}, ...this.listaDeCidadeMunicipioRequisicao]
      }
    }
  },
  mounted () {
    this.listarCamposSelectFiltro()
    this.filtrar()
  },
  methods: {
    ...mapActions('convenio', {listarItems: 'listarConveniosNacionais', atualizar: 'atualizar'}),
    ...mapMutations('convenio', ['SET_PAGINA_ATUAL', 'SET_FILTRO_NACIONAL_EMPRESA_ID', 'SET_FILTRO_NACIONAL_CNPJ', 'SET_FILTRO_NACIONAL_TIPO_ABRANGENCIA', 'SET_PAGINA_ATUAL', 'SET_LISTA', 'SET_ORDER_BY']),
    formatarCNPJ: formatarCNPJ,

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.filtrar()
    },

    setSegmento (value) {
      this.segmento_empresa_temp = value
      this.filtros.segmento_empresa = value.id
    },

    setEmpresaId (value) {
      if (value) {
        this.empresaId = value.id
      } else {
        this.empresaId = null
      }

      this.filtros.pessoa = this.empresaId
    },

    setSituacao (value) {
      this.filtroSituacao = value
      this.filtros.situacao = value.value
    },

    setFiltroEtapasConvenios (value) {
      this.filtroEtapasConvenio = value
      this.filtros.etapas_convenio = value.id
    },

    setCidade (value) {
      this.filtroCidadeMunicipio = value
      this.filtros.cidade = value.id
    },

    getStatus (item) {
      let status = item.etapas_convenio ? item.etapas_convenio : {descricao: ''}
      return status.descricao
    },

    getSituacao (situacao) {
      const objSituacao = this.situacao.find(item => item.value === situacao)
      return objSituacao.text ? objSituacao.text : ''
    },

    carregarMais () {
      this.listarItems()
    },

    listarCamposSelectFiltro () {
      this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
      this.$store.commit('franqueadas/setFranqueadaSelecionada', this.$store.state.root.usuarioLogado.franqueadaSelecionada)
      this.$store.dispatch('franqueadas/getFranqueada').then(() => {
        this.$store.commit('cidade/SET_ESTADO_FILTRO_ID', this.objFranqueada.estado.id)
        this.$store.dispatch('cidade/listar')
      })

      this.$store.commit('segmentoEmpresaConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.commit('segmentoEmpresaConvenio/SET_LISTA', [])
      this.$store.dispatch('segmentoEmpresaConvenio/listar')

      this.$store.commit('negociacaoParceriaWorkflow/SET_PAGINA_ATUAL', 1)
      this.$store.commit('negociacaoParceriaWorkflow/SET_LISTA', [])
      this.$store.dispatch('negociacaoParceriaWorkflow/listar')

      this.$store.commit('etapasConvenio/SET_PAGINA_ATUAL', 1)
      this.$store.commit('etapasConvenio/SET_LISTA', [])
      this.$store.dispatch('etapasConvenio/buscarTodos')
    },

    retornaAbrangenciaDescricao (item) {
      if (item.abrangencia_nacional === true) {
        return 'Nacional'
      } else {
        return 'Estadual'
      }
    },

    retornaBeneficiariosDescricao (item) {
      let tiposBeneficiariosSelecionados = []
      if (item.beneficiario_colaboradores === true) {
        tiposBeneficiariosSelecionados.push('Colaboradores')
      }
      if (item.beneficiario_dependentes === true) {
        tiposBeneficiariosSelecionados.push('Denpendentes')
      }
      if (item.beneficiario_associados === true) {
        tiposBeneficiariosSelecionados.push('Associados')
      }
      if (item.beneficiario_estagiarios === true) {
        tiposBeneficiariosSelecionados.push('Estagiários')
      }
      if (item.beneficiario_terceiros === true) {
        tiposBeneficiariosSelecionados.push('Terceiros')
      }
      if (item.beneficiario_alunos === true) {
        tiposBeneficiariosSelecionados.push('Alunos')
      }
      if (tiposBeneficiariosSelecionados.length === 0) {
        tiposBeneficiariosSelecionados.push('Nenhum tipo de beneficiário encontrado!')
      }
      return tiposBeneficiariosSelecionados.join(', ')
    },

    limparStateAnterior () {
      this.SET_FILTRO_NACIONAL_TIPO_ABRANGENCIA(null)
      // this.SET_FILTRO_NACIONAL_EMPRESA_ID(null)
    },

    executaFiltroRapido () {
      this.SET_FILTRO_NACIONAL_TIPO_ABRANGENCIA(this.filtroAbrangencia)
      // this.SET_FILTRO_NACIONAL_EMPRESA_ID(this.empresaId)
    },

    filtrar () {
      this.limparStateAnterior()
      this.executaFiltroRapido()
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    }
  }
}
</script>
<style scoped>
.hint-container {
  position: relative;
  width: max-content;
  padding-right: 1rem;
  display: inline-block;
}
</style>
