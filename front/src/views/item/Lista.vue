<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/item/adicionar" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-rapidos" v-model="filtroRapido">
        <form class="p-2" @submit.prevent="buscaRapida = true, filtrar()">
          <div class="form-group row mb-0">
            <!-- <b-col md="3"> -->
            <!--   <label v-help-hint="'filtroRapido-franqueada'" for="franqueada" class="col-form-label">Franquia</label> -->
            <!--   <g-select id="franqueada" -->
            <!--             :value="franqueada" -->
            <!--             :options="listaFranquias" -->
            <!--             :select="setFranqueada" -->
            <!--             class="multiselect-truncate" -->
            <!--             label="nome" -->
            <!--             track-by="id" /> -->
            <!-- </b-col> -->

            <div class="col-md-6">
              <label v-help-hint="'filtro-item_descricao'" for="descricao" class="col-form-label">Descrição</label>
              <div class="d-flex">
                <input id="descricao" v-model="descricao_temporaria" type="text" class="form-control" maxlength="255">
                <button type="submit" class="btn btn-azul">
                  <font-awesome-icon icon="search" />
                </button>
              </div>
            </div>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">
          <div class="row">
            <!-- <b-col md="3"> -->
            <!--   <label v-help-hint="'filtroRapido-franqueada'" for="franqueada" class="col-form-label">Franquia</label> -->
            <!--   <g-select id="franqueada" -->
            <!--             :value="franqueada" -->
            <!--             :options="listaFranquias" -->
            <!--             :select="setFranqueadaAvancado" -->
            <!--             class="multiselect-truncate" -->
            <!--             label="nome" -->
            <!--             track-by="id" /> -->
            <!-- </b-col> -->

            <div class="col-md-3">
              <label v-help-hint="'filtro-item_descricao'" for="descricao" class="col-form-label">Descrição</label>
              <input id="descricao" v-model="descricao_temporaria" type="text" class="form-control" maxlength="255">
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-item_unidade_medida_temporaria'" for="unidade_medida_temporaria" class="col-form-label">Unidade de Medida</label>
              <input id="unidade_medida_temporaria" v-model="unidade_medida_temporaria" type="text" class="form-control" maxlength="3">
            </div>
            <div class="col-md-3">
              <label v-help-hint="'filtro-item_situacao'" for="situacao" class="col-form-label">Situação</label>
              <div class="d-block">
                <b-form-checkbox-group id="situacao" v-model="selectedAvancados" :options="situacao" buttons button-variant="cinza" name="situacao" class="checkbtn-line" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label v-help-hint="'filtro-item_valor_venda_inicial_temporaria'" class="col-form-label">Valor de venda</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input v-money="moeda" id="valor_venda_inicial_temporaria" v-model="valor_venda_inicial_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input v-money="moeda" id="valor_venda_final_temporaria" v-model="valor_venda_final_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label v-help-hint="'filtro-item_valor_compra_inicial_temporaria'" class="col-form-label">Valor de compra</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input v-money="moeda" id="valor_compra_inicial_temporaria" v-model="valor_compra_inicial_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input v-money="moeda" id="valor_compra_final_temporaria" v-model="valor_compra_final_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'filtro-item_saldo_estoque_inicial_temporaria'" class="col-form-label">Saldo em estoque</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input v-money="moeda" id="saldo_estoque_inicial_temporaria" v-model="saldo_estoque_inicial_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input v-money="moeda" id="saldo_estoque_final_temporaria" v-model="saldo_estoque_final_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label v-help-hint="'filtro-item_estoque_inicial_temporaria'" class="col-form-label">Estoque minimo</label>
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input v-money="moeda" id="estoque_minimo_inicial_temporaria" v-model="estoque_minimo_inicial_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input v-money="moeda" id="estoque_minimo_final_temporaria" v-model="estoque_minimo_final_temporaria" type="text" class="form-control" maxlength="9">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaItem" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="it.descricao">Descrição</th>
            <th data-column="it.unidade_medida">Unidade</th>
            <th >Saldo Estoque</th>
            <th >Valor Compra</th>
            <th >Valor Venda</th>
            <th >Estoque mínimo</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="item in lista" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Descrição">{{ item.descricao }}</td>
              <td data-label="Unidade">{{ item.unidade_medida }}</td>

              <template v-if="item.itemFranqueadas.length">
                <td data-label="Saldo Estoque">
                  <template v-if="item.unidade_medida === 'UN'">
                    {{ item.itemFranqueadas[0].saldo_estoque*1 | formatarNumero(0) }}
                  </template>
                  <template v-else>
                    {{ item.itemFranqueadas[0].saldo_estoque ? item.itemFranqueadas[0].saldo_estoque*1 : '--' }}
                  </template>
                </td>
                <td data-label="Valor Compra">{{ item.itemFranqueadas[0].valor_compra | formatarNumero }}</td>
                <td data-label="Valor Venda">{{ item.itemFranqueadas[0].valor_venda | formatarNumero }}</td>
                <td data-label="Estoque mínimo">
                  <template v-if="item.unidade_medida === 'UN'">
                    {{ item.itemFranqueadas[0].estoque_minimo*1 | formatarNumero(0) }}
                  </template>
                  <template v-else>
                    {{ item.itemFranqueadas[0].estoque_minimo ? item.itemFranqueadas[0].estoque_minimo*1 : '--' }}
                  </template>
                </td>
              </template>

              <template v-else>
                <td data-label="Saldo Estoque">
                  --
                </td>
                <td data-label="Valor Compra">{{ 0 | formatarNumero }}</td>
                <td data-label="Valor Venda">{{ 0 | formatarNumero }}</td>
                <td data-label="Estoque mínimo">
                  --
                </td>
              </template>

              <td class="d-flex coluna-icones">
                <!-- <a href="javascript:void(0)" title="Informações" class="icone-link" @click.prevent="mostrar(item)">
                  <font-awesome-icon icon="eye" />
                </a> -->
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="desativar(item)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Desativar" class="icone-link text-success" @click.prevent="desativar(item)">
                  <font-awesome-icon icon="check-circle" />
                </a> -->
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
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
import {currencyToNumber} from '../../utils/number'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaItem',
  data () {
    return {
      className: 'rapido-open',
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 1,
      selectedAvancados: [],
      situacao: [
        {text: 'Ativo', value: 'A'},
        {text: 'Inativo', value: 'I'}
      ],
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      },
      number: {
        '#': {
          pattern: /\d/
        }
      },
      descricao_temporaria: '',
      unidade_medida_temporaria: '',
      saldo_estoque_inicial_temporaria: null,
      saldo_estoque_final_temporaria: null,
      estoque_minimo_inicial_temporaria: null,
      estoque_minimo_final_temporaria: null,
      valor_compra_inicial_temporaria: null,
      valor_compra_final_temporaria: null,
      valor_venda_inicial_temporaria: null,
      valor_venda_final_temporaria: null,
      franqueada: null
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('franqueadas', { listaFranquias: 'listaFranqueada' }),
    ...mapState('item', ['lista', 'item', 'totalItens', 'estaCarregando', 'todosItensCarregados']),

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])

    this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('franqueadas/getListaFranqueada')
      .then(() => {
        this.setFranqueada(this.listaFranquias.find(f => f.id === this.$store.state.root.usuarioLogado.franqueadaSelecionada))
      })
  },

  methods: {
    ...mapActions('item', {listar: 'getLista', atualizar: 'atualizar'}),
    ...mapMutations('item', ['SET_ITEM', 'SET_SITUACAO', 'SET_FRANQUEADA', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    setFranqueada (value) {
      this.franqueada = value
      this.filtrar()
    },

    setFranqueadaAvancado (value) {
      this.franqueada = value
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.SET_ITEM(item)
        this.$router.push(`/configuracoes/item/atualizar/${item.id}`)
      }
    },

    desativar (item) {
      const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.SET_ITEM(item)
          this.SET_FRANQUEADA(item.franqueada.id)
          this.SET_SITUACAO(item.situacao === 'A' ? 'I' : 'A')
          this.atualizar()
            .then(() => {
              this.filtrar()
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${item.descricao}?`)
    },

    limparStateAnterior () {
      this.$store.commit('item/SET_FILTRO_DESCRICAO', '')
      this.$store.commit('item/SET_FILTRO_FRANQUEADA', null)
      this.$store.commit('item/SET_FILTRO_UNIDADE_MEDIDA', '')
      this.$store.commit('item/SET_FILTRO_SALDO_ESTOQUE_INICIAL', null)
      this.$store.commit('item/SET_FILTRO_SALDO_ESTOQUE_FINAL', null)
      this.$store.commit('item/SET_FILTRO_ESTOQUE_MINIMO_INICIAL', null)
      this.$store.commit('item/SET_FILTRO_ESTOQUE_MINIMO_FINAL', null)
      this.$store.commit('item/SET_FILTRO_VALOR_COMPRA_INICIAL', null)
      this.$store.commit('item/SET_FILTRO_VALOR_COMPRA_FINAL', null)
      this.$store.commit('item/SET_FILTRO_VALOR_VENDA_INICIAL', null)
      this.$store.commit('item/SET_FILTRO_VALOR_VENDA_FINAL', null)
      this.$store.commit('item/SET_FILTRO_SITUACAO', [])
    },

    executaFiltroRapido () {
      this.$store.commit('item/SET_FILTRO_DESCRICAO', this.descricao_temporaria)
      this.$store.commit('item/SET_FILTRO_FRANQUEADA', this.franqueada ? this.franqueada.id : null)
    },

    executaFiltroAvancado () {
      const saldoInicial = currencyToNumber(this.saldo_estoque_inicial_temporaria)
      const saldoFinal = currencyToNumber(this.saldo_estoque_final_temporaria)
      const estoqueInicial = currencyToNumber(this.estoque_minimo_inicial_temporaria)
      const estoqueFinal = currencyToNumber(this.estoque_minimo_final_temporaria)
      const valorCompraInicial = currencyToNumber(this.valor_compra_inicial_temporaria)
      const valorCompraFinal = currencyToNumber(this.valor_compra_final_temporaria)
      const valorVendaInicial = currencyToNumber(this.valor_venda_inicial_temporaria)
      const valorVendaFinal = currencyToNumber(this.valor_venda_final_temporaria)
      this.$store.commit('item/SET_FILTRO_DESCRICAO', this.descricao_temporaria)
      this.$store.commit('item/SET_FILTRO_FRANQUEADA', this.franqueada ? this.franqueada.id : null)
      this.$store.commit('item/SET_FILTRO_UNIDADE_MEDIDA', this.unidade_medida_temporaria)
      this.$store.commit('item/SET_FILTRO_SITUACAO', this.selectedAvancados)
      this.$store.commit('item/SET_FILTRO_SALDO_ESTOQUE_INICIAL', saldoInicial || null)
      this.$store.commit('item/SET_FILTRO_SALDO_ESTOQUE_FINAL', saldoFinal || null)
      this.$store.commit('item/SET_FILTRO_ESTOQUE_MINIMO_INICIAL', estoqueInicial || null)
      this.$store.commit('item/SET_FILTRO_ESTOQUE_MINIMO_FINAL', estoqueFinal || null)
      this.$store.commit('item/SET_FILTRO_VALOR_COMPRA_INICIAL', valorCompraInicial || null)
      this.$store.commit('item/SET_FILTRO_VALOR_COMPRA_FINAL', valorCompraFinal || null)
      this.$store.commit('item/SET_FILTRO_VALOR_VENDA_INICIAL', valorVendaInicial || null)
      this.$store.commit('item/SET_FILTRO_VALOR_VENDA_FINAL', valorVendaFinal || null)
    },

    filtrar () {
      this.limparStateAnterior()
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    limparFiltros () {
      this.unidade_medida_temporaria = ''
      this.saldo_estoque_inicial_temporaria = null
      this.saldo_estoque_final_temporaria = null
      this.estoque_minimo_inicial_temporaria = null
      this.estoque_minimo_final_temporaria = null
      this.valor_compra_inicial_temporaria = null
      this.valor_compra_final_temporaria = null
      this.valor_venda_inicial_temporaria = null
      this.valor_venda_final_temporaria = null
      this.selectedAvancados = []
    }

  }
}
</script>
