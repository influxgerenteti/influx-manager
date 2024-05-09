<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div class="filtro-selecionado btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, className = filtroRapido ? 'rapido-open' : null">Filtros</div>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida=true">
          <div class="form-group row mb-0">

            <b-col v-if="mostrandoFranqueadora" md="6">
              <label v-help-hint="'filtro_rapido-estado_meta_franqueada'" for="grupo" class="col-form-label">Grupo</label>
              <g-select
                id="grupo"
                v-model="grupo"
                :options="listaGrupos"
                label="nome"
                track-by="id" />
            </b-col>

            <b-col md="2">
              <label v-help-hint="'filtro_rapido-ano_meta_franqueada'" for="ano" class="col-form-label">Ano</label>
              <g-select id="ano"
                        v-model="ano"
                        :options="listaAnos"
                        class="multiselect-truncate"
                        label="text"
              />
            </b-col>

            <b-col md="2">
              <label v-help-hint="'filtro_rapido-mes_meta_franqueada'" for="mes" class="col-form-label">Mês</label>
              <g-select id="mes"
                        v-model="mes"
                        :options="listaMeses"
                        class="multiselect-truncate"
                        label="text"
              />
            </b-col>

            <b-col sm="4" md="">
              <b-btn variant="roxo" class="mt-4 btn-block text-uppercase" @click="filtrar()">Buscar</b-btn>
            </b-col>
          </div>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="">Franquia</th>
            <th data-column="" class="size-150">Meta 1</th>
            <th data-column="" class="size-150">Meta 2</th>
            <th data-column="" class="size-150">Meta 3</th>
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!metasCarregadas.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <template v-if="mostrandoFranqueadora === true">
              <tr v-for="item in metasCarregadas" :key="item.id">
                <td data-label="Nome">{{ item.nome }}</td>

                <td data-label="Meta 1" class="size-150">
                  <div class="col-md-8 p-0">
                    <input v-mask="'#########'" :id="`meta_franqueadora_1_${item.id}`" v-model="item.meta_franqueadora_1" type="text" class="form-control pt-0 pb-0" maxlength="9">
                  </div>
                </td>

                <td data-label="Meta 2" class="size-150">
                  <div class="col-md-8 p-0">
                    <input v-mask="'#########'" :id="`meta_franqueadora_2_${item.id}`" v-model="item.meta_franqueadora_2" type="text" class="form-control pt-0 pb-0" maxlength="9">
                  </div>
                </td>

                <td data-label="Meta 3" class="size-150">
                  <div class="col-md-8 p-0">
                    <input v-mask="'#########'" :id="`meta_franqueadora_3_${item.id}`" v-model="item.meta_franqueadora_3" type="text" class="form-control pt-0 pb-0" maxlength="9">
                  </div>
                </td>
              </tr>
            </template>

            <template v-else>
              <template v-for="item in metasCarregadas">
                <tr :key="`${item.id}_0`">
                  <td data-label="Nome">Metas da franqueadora</td>

                  <td data-label="Meta 1" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_franqueadora_1_${item.id}`" :value="item.meta_franqueadora_1" type="text" class="form-control pt-0 pb-0" maxlength="9" disabled>
                    </div>
                  </td>

                  <td data-label="Meta 2" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_franqueadora_2_${item.id}`" :value="item.meta_franqueadora_2" type="text" class="form-control pt-0 pb-0" maxlength="9" disabled>
                    </div>
                  </td>

                  <td data-label="Meta 3" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_franqueadora_3_${item.id}`" :value="item.meta_franqueadora_3" type="text" class="form-control pt-0 pb-0" maxlength="9" disabled>
                    </div>
                  </td>
                </tr>

                <tr :key="`${item.id}_1`">
                  <td data-label="Nome">{{ item.nome }}</td>

                  <td data-label="Meta 1" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_1_${item.id}`" v-model="item.meta_1" type="text" class="form-control pt-0 pb-0" maxlength="9">
                    </div>
                  </td>

                  <td data-label="Meta 2" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_2_${item.id}`" v-model="item.meta_2" type="text" class="form-control pt-0 pb-0" maxlength="9">
                    </div>
                  </td>

                  <td data-label="Meta 3" class="size-150">
                    <div class="col-md-8 p-0">
                      <input v-mask="'#########'" :id="`meta_3_${item.id}`" v-model="item.meta_3" type="text" class="form-control pt-0 pb-0" maxlength="9">
                    </div>
                  </td>
                </tr>
              </template>
            </template>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div>
        <b-btn :disabled="submited" variant="roxo" @click="submit()">Salvar</b-btn>
      </div>

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
import moment from 'moment'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      mostrandoFranqueadora: false,

      grupo: {id: null, nome: 'Selecione'},

      ano: {text: 'Selecione', value: null},
      mes: {text: 'Selecione', value: null},
      oldVal: null,
      submited: false
    }
  },
  computed: {
    ...mapState('metaFranqueada', {listaMetas: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('estado', {listaEstados: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaMetas.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    metasCarregadas: {
      get () {
        return this.listaMetas.map(item => {
          const _ano = this.ano.value
          const _mes = this.mes.value

          const franqueada = {
            nome: item.nome,
            ano: _ano,
            mes: _mes
          }

          if (!item.metaFranqueadas.length) {
            item.metaFranqueadas.push({
              meta_1: null,
              meta_2: null,
              meta_3: null,
              meta_franqueadora_1: null,
              meta_franqueadora_2: null,
              meta_franqueadora_3: null
            })
          }

          return { ...franqueada, ...item.metaFranqueadas[0], id: item.id }
        })
      }
    },

    listaGrupos: {
      get () {
        return [{id: null, nome: 'Selecione'}, ...this.listaEstados]
      }
    },

    listaAnos: {
      get () {
        const year = new Date().getFullYear()
        let content = []
        for (let i = 10; i >= 0; i--) {
          const data = year - i
          content.push({text: data, value: data})
        }
        return content
      }
    },

    listaMeses: {
      get () {
        let content = []
        for (let i = 0; i <= 11; i++) {
          content.push({text: moment().month(i).locale('pt').format('MMMM'), value: i + 1})
        }
        return content
      }
    }
  },

  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])

    const franqueada = this.$store.state.root.usuarioLogado.franqueadas.find(f => f.id === this.$store.state.root.usuarioLogado.franqueadaSelecionada)
    this.mostrandoFranqueadora = !!franqueada.franqueadora

    this.init()
  },

  methods: {
    ...mapActions('metaFranqueada', {listarMetas: 'listar', atualizarMeta: 'atualizar'}),
    ...mapActions('estado', {listaEstadosRequisicao: 'listar'}),
    ...mapMutations('metaFranqueada', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY', 'SET_FILTROS']),
    ...mapMutations('estado', ['SET_ESTADO_SELECIONADO']),

    carregarMais () {
      this.filtrar()
    },

    init () {
      if (this.mostrandoFranqueadora) {
        this.listaEstadosRequisicao()
      }

      const m = moment()
      this.ano = this.listaAnos.find(e => e.text === Number(m.format('YYYY')))
      this.mes = this.listaMeses.find(e => e.text === m.locale('pt').format('MMMM'))
    },

    filtrar () {
      const filtros = {
        estado: this.grupo ? this.grupo.id : null,
        ano: this.ano.value,
        mes: this.mes.value
      }

      this.SET_PAGINA_ATUAL(1)
      this.listarMetas(filtros)
    },

    submit () {
      this.submited = true

      Promise.all(this.metasCarregadas.map(item => {
        const data = {
          id: item.id,
          ano: item.ano,
          mes: item.mes
        }

        for (let i = 1; i <= 3; i++) {
          const key = `meta_${i}`
          const keyFranqueadora = `meta_franqueadora_${i}`
          const value = item[key]
          const valueFranqueadora = item[keyFranqueadora]

          if (value) {
            data[key] = Number(value)
          }

          if (valueFranqueadora) {
            data[keyFranqueadora] = Number(valueFranqueadora)
          }
        }

        return this.atualizarMeta(data)
      }))
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Metas atualizadas com sucesso!'
          })
        })
        .catch(console.error)
        .finally(() => {
          this.submited = false
        })
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.filtrar()
    }
  }
}
</script>

<style scoped>
.custom-checkbox {
  min-height: 1rem !important;
}
.col-md-8 {
  max-width: 50%;
}

.table-borderless.table-hover tbody tr:hover .form-control {
  background-color: #fff;
}
</style>
