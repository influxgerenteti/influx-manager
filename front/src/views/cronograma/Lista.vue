<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, filtroRapido = false, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 2">Avançado</div>
        </div>
      </div>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-2" @submit.prevent="buscaAvancada=true, filtrar()">
          <div class="form-group row">
            <div class="col-md-3">
              <label for="filtroRapidos-cronograma-dia" class="col-form-label">Dia da semana</label>
              <g-select
                id="filtroRapidos-cronograma-dia"
                :select="setDiaDaSemana"
                :value="descricaoTemp"
                :options="listaDeDiasDaSemana"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
              />
            </div>
            <div class="col-md-3">
              <label for="filtroRapidos-cronograma-instrutor" class="col-form-label">Instrutor</label>
              <g-select
                id="filtroRapidos-cronograma-instrutor"
                :select="setInstruto"
                :value="instrutorTemp"
                :options="listaDeInstrutor"
                class="multiselect-truncate"
                label="apelido"
                track-by="id"
              />
            </div>
            <div class="col-md-3">
              <label for="filtroRapidos-cronograma-livro" class="col-form-label">Livro</label>
              <g-select
                id="filtroRapidos-cronograma-livro"
                :select="setLivro"
                :value="livroTemp"
                :options="listaDeLivro"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
              />
            </div>
            <div class="col-md-3">
              <label for="filtroRapidos-cronograma-situacao" class="col-form-label">Situação</label>
              <g-select
                id="filtroRapidos-cronograma-situacao"
                :select="setSituacao"
                :value="situacaoTemp"
                :options="listaDesituacao"
                class="multiselect-truncate"
                label="text"
                track-by="id"
              />
            </div>
          </div>
          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = true, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="" class="coluna-checkbox">
              <b-form-checkbox :disabled="!listaItens.length" v-model="checkAll" :indeterminate="indeterminate" class="m-0 p-0" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/>
            </th>
            <th data-column="t.descricao">Turma</th>
            <th data-column="func.apelido">Instrutor</th>
            <th data-column="l.descricao">Livro</th>
            <th data-column="t.sala_franqueada">Sala</th>
            <th data-column=""><!-- Situação --></th>
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

            <tr v-for="item in listaItens" :key="item.turmaId" @dblclick="editar(item.turmaId)">
              <td data-label="Selecione" class="coluna-checkbox">
                <b-form-checkbox v-model="listaDeTurma" :value="item"/>
              </td>
              <td data-label="Turma">{{ item.turmaDescricao || '--' }}</td>
              <td data-label="Instrutor">{{ item.funcionarioApelido || '--' }}</td>
              <td data-label="Livro">{{ item.livroDescricao || '--' }}</td>
              <td data-label="Sala">{{ item.salaDescricao || '--' }}</td>
              <td data-label="" >
                <!-- Situação -->
                <span v-b-tooltip.viewport.left.hover :title="listaDesituacao.find(situcao => item.situacaoTurma === situcao.value).text" :class="'circle-badge-' + item.situacaoTurma.toLowerCase()" class="circle-badge"></span>
              </td>
              <td class="d-flex coluna-icones">
                <!-- Ações -->

              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <div>
          <b-form-group>
            <b-form-radio-group v-model="opcaoDeRelatorio" required>
              <b-form-radio value="D">Diário de Aulas</b-form-radio>
              <b-form-radio value="C">Cronograma de Aulas</b-form-radio>
            </b-form-radio-group>
          </b-form-group>
        </div>

        <div class="info-btn">
          <b-btn :disabled="!listaDeTurma.length || listaDeTurma.length < 1" type="button" variant="verde" @click="imprimirRelatorio(opcaoDeRelatorio)">Imprimir</b-btn>
        </div>
        <!-- <b-btn  class="btn btn-verde" target="_blank" @click="limparSelects()" >
          Imprimir
        </b-btn> -->
      </div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>

        <div>
          <small>
            <template v-if="listaDeTurma.length >= 1">{{ listaDeTurma.length }} ite{{ listaDeTurma.length > 1 ? 'ns' : 'm' }} selecionado{{ listaDeTurma.length > 1 ? 's' : '' }}</template>
            <template v-else>Nenhum item selecionado</template>
          </small>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import open from '../../utils/open'

export default {
  name: 'ListaCronograma',
  data () {
    return {
      className: 'rapido-open',
      imprimir: false,
      indeterminate: false,
      buscaAvancada: false,
      buscaRapida: false,
      filtroAvancado: false,
      filtroRapido: true,
      filtroSelecionado: 2,
      checkAll: false,
      listaDeTurma: [],
      listaDesituacao: [
        {id: 0, text: 'Selecione', value: null},
        {id: 1, text: 'Aberta', value: 'ABE'},
        {id: 2, text: 'Em formação', value: 'FOR'},
        {id: 3, text: 'Encerrada', value: 'ENC'}
      ],
      listaDeDiasDaSemana: [
        {id: 0, descricao: 'Selecionar', value: null},
        {id: 1, descricao: 'Segunda', value: 'Seg'},
        {id: 2, descricao: 'Terça', value: 'Ter'},
        {id: 3, descricao: 'Quarta', value: 'Qua'},
        {id: 4, descricao: 'Quinta', value: 'Qui'},
        {id: 5, descricao: 'Sexta', value: 'Sex'},
        {id: 6, descricao: 'Sábado', value: 'Sab'}
      ],
      descricaoTemp: null,
      instrutorTemp: null,
      livroTemp: null,
      situacaoTemp: null,
      diarioDeAulas: null,
      cronograma: null,
      opcaoDeRelatorio: 'D'
    }
  },
  computed: {
    ...mapState('cronograma', {listaItens: 'lista', estaCarregando: 'estaCarregando', totalItens: 'totalItens', todosItensCarregados: 'todosItensCarregados', filtros: 'filtros'}),
    ...mapState('livro', {listaDeLivrosState: 'lista'}),
    ...mapState('funcionario', {listaDeFuncionarioState: 'lista'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaDeLivro: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeLivrosState]
      }
    },
    listaDeInstrutor: {
      get () {
        return [{id: null, apelido: 'Selecione'}, ...this.listaDeFuncionarioState.filter(funcionario => funcionario.instrutor === true || funcionario.instrutor_personal === true)]
      }
    }

  },
  watch: {
    listaDeTurma (value, oldVal) {
      if (value.length === 0) {
        this.indeterminate = false
        this.checkAll = false
      }
      if (value.length === this.listaItens.length) {
        this.indeterminate = false
        this.checkAll = true
      } else {
        this.indeterminate = true
        this.checkAll = false
      }

      // console.log("VaLUE::",value)
      // console.log("olvval",oldVal)
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.filtrar()
    this.listarCamposSelects()
  },
  methods: {
    ...mapActions('cronograma', {listarItens: 'listar'}),
    ...mapMutations('cronograma', ['SET_LISTA', 'SET_PAGINA_ATUAL', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ORDER_BY']),

    carregarMais () {
      this.listarItens()
    },

    listarCamposSelects () {
      this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('livro/listar')

      this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('funcionario/listar')
    },

    setSituacao (value) {
      this.situacaoTemp = value
    },

    setLivro (value) {
      this.livroTemp = value
    },

    setDiaDaSemana (value) {
        this.descricaoTemp = value
    },

    setInstruto (value) {
      this.instrutorTemp = value
    },

    executaFiltroAvancado () {
      const descricao = this.descricaoTemp ? this.descricaoTemp.value : null
      const funcionario = this.instrutorTemp ? this.instrutorTemp.id : null
      const livro = this.livroTemp ? this.livroTemp.id : null
      const situacao = this.situacaoTemp ? this.situacaoTemp.value : null

      this.filtros.descricao = descricao
      this.filtros.funcionario = funcionario
      this.filtros.livro = livro
      this.filtros.situacao = situacao
    },

    filtrar () {
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido()
      } else {
        this.executaFiltroAvancado()
      }
      this.SET_PAGINA_ATUAL(1)
      this.listarItens().then(() => {
        this.checkAll = false
        this.listaDeTurma = []
      })
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.listarItens()
    },

    imprimirRelatorio (tipoRelatorio) {
      this.imprimir = true
      let api
      if (tipoRelatorio === 'C') {
        api = 'class_record_cronograma/imprimir'
      } else {
        api = 'class_record_diario/imprimir'
      }
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()

      open(`/api/relatorios/${api}?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&${filtrosRelatorio}`, '_blank')
    },

    converterDadosParaLink () {
      const ids = this.listaDeTurma.map(turma => turma.turmaId)
      let dados = []

      for (let index in ids) {
        dados.push(`turma[]=${ids[index]}`)
      }

      return dados.join('&')
    },

    limparSelects () {
      this.listaDeTurma.splice()
    },

    toggleAll (checked) {
      if (checked) {
        this.listaDeTurma = this.listaItens
        return
      }

      this.listaDeTurma = []
    }
  }
}
</script>
<style scoped>
.coluna-checkbox .custom-control.custom-checkbox {
  padding-left: 0;
}

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
