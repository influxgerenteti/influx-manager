<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-2">
      <router-link v-if="podeAdicionar" :to="`${$route.path}/adicionar`" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
      <!-- <div class="filtro-avancado body-sector">
        <div class="d-flex justify-content-between filtro-header head-content-sector">
          <div>
            <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros(), filtrar()">Filtro Rápido</div>
            <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
          </div>

          <router-link :to="`${$route.path}/adicionar`" class="btn btn-azul">
            <font-awesome-icon icon="plus" /> Adicionar
          </router-link>
        </div>

        <b-collapse id="filtros-rapidos" v-model="filtroRapido">
          <form class="p-3" @submit.prevent="buscaRapida=true, buscaAvancada=false">
            <div class="form-group row mb-0">
              <div class="col-md-2">
                <label v-help-hint="'filtroRapido-modelo_contrato_situacao'" for="situacao_filtro_rapido" class="col-form-label">Situação</label>
                <div class="d-block">
                  <b-form-checkbox-group
                    id="situacao_filtro_rapido"
                    v-model="selected"
                    :options="situacao"
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
        </b-collapse>
        <b-collapse id="filtros-avancados" v-model="filtroAvancado">
          <form class="p-3" @submit.prevent="buscaRapida=false, buscaAvancada=true, filtrar()">
            <div class="form-group row">
              <div class="col-md-2">
                <label v-help-hint="'filtroAvancado-modelo_contrato_situacao'" for="situacao_filtro_avancado" class="col-form-label">Situação</label>
                <div class="d-block">
                  <b-form-checkbox-group
                    id="situacao_filtro_avancado"
                    v-model="selectedFiltroAvancado"
                    :options="situacao"
                    buttons
                    button-variant="cinza"
                    class="checkbtn-line"
                    name="situacao_filtro_avancado"
                    @change="setSituacaoAvancado"
                  />
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
          </form>
        </b-collapse>
      </div> -->
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable" :class="className" class="table-scroll mobile-cards table b-table table-hover table-borderless">
        <thead class="text-dark">
          <tr>
            <th data-column="mt.descricao">Descrição</th>
            <th data-column="mt.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in listaItems" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Descrição">{{ item.descricao }}</td>

              <td data-label="Situação">
                <div @click.prevent="toggleSituacao(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="modeloEstaAtivo(item)" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>
              <td class="d-flex coluna-icones">
                <a v-if="podeEditar" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <a v-else href="javascript:void(0)" class="icone-link" title="Visualizar" @click.prevent="visualizar(item)">
                  <font-awesome-icon icon="file" />
                </a>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label">
        <div class="text-right">
          <small>
            {{ totalItens === 0 ? 'Nenhum' : totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaModeloContrato',
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selected: [],
      situacao: [
        {text: 'Ativo', value: 'A'},
        {text: 'Inativo', value: 'I'}
      ],
      selectedFiltroAvancado: []
    }
  },
  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('modulos', ['permissoes']),
    ...mapState('modeloTemplate', {listaItems: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens'}),
    usuarioLogadoFranqueadora: {
      get () {
        return this.$store.state.root.usuarioLogado.logadoFranqueadora
      }
    },
    podeEditar: {
      get () {
        return this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true) && this.usuarioLogadoFranqueadora
      }
    },
    podeAdicionar: {
      get () {
        return this.permissoes['CRIAR'] && (this.permissoes['CRIAR'].possui_permissao === true) && this.usuarioLogadoFranqueadora
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.filtrar()
    this.SET_LISTA([])
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('modeloTemplate', {listarItems: 'listar', atualizar: 'atualizar', alterarSituacao: 'alterarSituacao'}),
    ...mapMutations('modeloTemplate', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_FILTRO_SITUACAO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY']),

    modeloEstaAtivo (modelo) {
      const situacaoAtiva = modelo && modelo.situacao && modelo.situacao === 'A'
      const modeloAtivoNaFranqueada = modelo.modelo_template_franqueadas && modelo.modelo_template_franqueadas.length > 0
      return situacaoAtiva && (modeloAtivoNaFranqueada || this.usuarioLogadoFranqueadora)
    },

    alterarSituacaoState (modeloTemplate, situacao, modeloTemplateFranqueadaId) {
      if (this.usuarioLogadoFranqueadora) {
        modeloTemplate.situacao = situacao
      } else {
        if (situacao === 'A') {
          modeloTemplate.modelo_template_franqueadas.push({id: modeloTemplateFranqueadaId})
        } else {
          modeloTemplate.modelo_template_franqueadas = []
        }
      }
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    },

    listarCamposSelects () {
      // TODO: Se necessario filtro de Select Preencher
    },

    setSituacao (value) {
      this.selected = value
      this.filtrar()
    },

    setSituacaoAvancado (value) {
      this.selectedFiltroAvancado = value
    },

    toggleSituacao (item) {
      const mensagem = this.modeloEstaAtivo(item) ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const situacao = this.modeloEstaAtivo(item) ? 'I' : 'A'
          const data = {
            id: item.id,
            situacao: situacao
          }
          if (item.modelo_template_franqueadas && item.modelo_template_franqueadas.length) {
            data.modelo_template_franqueada = item.modelo_template_franqueadas[0].id
          }

          this.alterarSituacao(data)
            .then((res) => {
              this.alterarSituacaoState(item, situacao, res)
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${item.descricao}?`)
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.$router.push(`/configuracoes/modelo-template/atualizar/${item.id}`)
      }
    },

    visualizar (item) {
      this.$router.push(`/configuracoes/modelo-template/visualizar/${item.id}`)
    },

    limparStateAnterior () {
      this.SET_FILTRO_SITUACAO(null)
    },

    executaFiltroRapido () {
      let situacao = this.selected ? this.selected : null
      this.SET_FILTRO_SITUACAO(situacao)
    },

    executaFiltroAvancado () {
      let situacao = this.selectedFiltroAvancado ? this.selectedFiltroAvancado : null
      this.SET_FILTRO_SITUACAO(situacao)
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else if (this.filtroSelecionado === 2) {
        this.executaFiltroAvancado()
      }

      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listarItems()
    },

    limparFiltros () {
      this.selected = []
      this.selectedFiltroAvancado = []
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
</style>
