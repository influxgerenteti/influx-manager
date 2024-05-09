<template>
  <div class="animated fadeIn">
    <div class="table-responsive-sm bg-white">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="descricao">Descrição</th>
            <th data-column="nivel_instrutor">Nível Instrutor</th>
            <th data-column="tipo">Tipo</th>
            <th>Tipo Pagamento</th>
            <th data-column="valor">Valor</th>
            <th>Valor Extra</th>
            <!-- <th>Valor Bônus</th> -->
            <th data-column="situacao">Situação</th>
          </tr>
        </thead>

        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="(item, index) in lista" :key="index">
              <td data-label="Descrição">{{ item ? item.descricao : null }}</td>
              <td data-label="Nível Instrutor">{{ item.nivel_instrutor ? item.nivel_instrutor.descricao : null }}</td>
              <td data-label="Tipo">{{ tipos[item.tipo] }}</td>
              <td data-label="Tipo Pagamento">{{ tipos_pagamento[item.tipo_pagamento] }}</td>

              <td data-label="Valor">
                <div class="col-md-8 p-0">
                  <vue-numeric :disabled="!permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === false) || !permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)" :id="`valor_{index}`" :precision="2" :empty-value="null" v-model="item.valor_hora.valor" :max="9999999.99" separator="." class="form-control" @blur="salvar(item.valor_hora)" />
                </div>
              </td>

              <td data-label="Valor Extra">
                <div class="col-md-8 p-0">
                  <vue-numeric :disabled="!permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === false) || !permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)" :id="`valor_extra_{index}`" :precision="2" :empty-value="null" v-model="item.valor_hora.valor_extra" :max="9999999.99" separator="." class="form-control" @blur="salvar(item.valor_hora)" />
                </div>
              </td>

              <!-- <td data-label="Valor Bônus">
                <div class="col-md-8 p-0">
                  <vue-numeric :disabled="!permissoes['CRIAR'] || !permissoes['EDITAR']" :id="`valor_bonus_${index}`" :precision="2" :empty-value="null" v-model="item.valor_hora.valor_bonus" :max="9999999.99" separator="." class="form-control" @blur="salvar(item.valor_hora)" />
                </div>
              </td> -->

              <td :class="{ 'not-pointer' : !permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === false) || !permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)}" data-label="Situação">
                <div @click.prevent="inativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.valor_hora.situacao === 'A'" :title="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === false) || permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false) ? 'Desativar' : ''" class="align-middle text-success">
                    <font-awesome-icon icon="check-square" />
                  </span>
                  <span v-b-tooltip.viewport.left.hover v-else :title="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === false) || permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false) ? 'Ativar' : ''" class="align-middle icon-danger">
                    <font-awesome-icon icon="square" />
                  </span>
                </div>
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
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'
import {currencyToNumber} from '../../utils/number'

export default {
  data () {
    return {
      podePrepararDados: 0,
      valoresHoraExistentes: {},
      lista: [],
      direcao: '',
      colunaOrdena: '',
      tipos: {
        TUR: 'Turmas',
        VIP: 'VIP',
        BON: 'Bonus Class',
        PER: 'Personal',
        ATI: 'Atividade Extra'
      },
      tipos_pagamento: {
        H: 'Autônomo',
        M: 'Registrado'
      }
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('valorHoraLinhas', {listaValoresHoraLinhas: 'lista', estaCarregando: 'estaCarregando', todosItensCarregados: 'todosItensCarregados'}),
    ...mapState('nivelInstrutor', {listaNiveis: 'lista'}),
    ...mapState('valorHora', {listaValores: 'lista', itemSelecionadoID: 'itemSelecionadoID'}),
    permitirCarregarMais: {
      get () {
        return !!this.listaValoresHoraLinhas.length && !!this.listaNiveis.length && !!this.listaValores.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  mounted () {
    this.executaRequisicoes()
  },

  methods: {
    ...mapActions('valorHoraLinhas', ['listar']),
    ...mapActions('nivelInstrutor', {listarNivelInstrutor: 'listar'}),
    ...mapActions('valorHora', {listarValorHora: 'listar', atualizar: 'atualizar', criar: 'criar'}),
    ...mapMutations('valorHoraLinhas', ['SET_ORDER_BY', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID']),

    carregarMais () {
      this.listar()
    },

    executaRequisicoes () {
      this.$store.commit('valorHoraLinhas/SET_PAGINA_ATUAL', 1)
      this.$store.commit('valorHoraLinhas/SET_LISTA', [])
      this.listar().then(this.prepararDados).catch(console.error)

      this.$store.commit('nivelInstrutor/SET_PAGINA_ATUAL', 1)
      this.$store.commit('nivelInstrutor/SET_LISTA', [])
      this.listarNivelInstrutor().then(this.prepararDados).catch(console.error)

      this.$store.commit('valorHora/SET_PAGINA_ATUAL', 1)
      this.$store.commit('valorHora/SET_FILTROS', {})
      this.$store.commit('valorHora/SET_LISTA', [])
      this.listarValorHora().then(this.prepararDados).catch(console.error)
    },

    sortTable (response) {
      this.direcao = response.detail.direcao
      this.colunaOrdena = response.detail.order
      this.lista.sort(this.compararAsc)// sempre ordena antes de qualquer chamada

      if (this.direcao === 'ASC') {
        this.lista.sort(this.compararAsc)
      } else if (this.direcao === 'DESC') {
        this.lista.sort(this.compararDesc)
      }
    },

    montarValorPadrao (valorHoraLinhas, nivelInstrutor) {
      return {
        id: null,
        valor_hora_linhas: {id: valorHoraLinhas.id},
        nivel_instrutor: {id: nivelInstrutor.id},
        valor: 0,
        valor_extra: 0,
        // valor_bonus: 0,
        situacao: 'I'
      }
    },

    prepararDados () {
      this.podePrepararDados++

      if (this.podePrepararDados < 3) {
        return
      }

      this.listaValores.map(item => {
        this.valoresHoraExistentes[`${item.valor_hora_linhas.id},${item.nivel_instrutor.id}`] = {
          id: item.id,
          valor_hora_linhas: {id: item.valor_hora_linhas.id},
          nivel_instrutor: {id: item.nivel_instrutor.id},
          valor: currencyToNumber(item.valor),
          valor_extra: currencyToNumber(item.valor_extra),
          // valor_bonus: currencyToNumber(item.valor_bonus),
          situacao: item.situacao
        }
      })

      const lista = []
      this.listaValoresHoraLinhas.map(item => {
        this.listaNiveis.map(nivel => {
          const itemCopy = Object.assign({}, item)
          itemCopy.valor_hora = this.valoresHoraExistentes[`${item.id},${nivel.id}`] || this.montarValorPadrao(item, nivel)
          itemCopy.nivel_instrutor = nivel
          lista.push(itemCopy)
        })
      })

      this.lista = lista
    },

    verificarColuna (a, b) {
      let lista = []
      let _a = ''
      let _b = ''
      if (this.colunaOrdena === 'descricao') {
        _a = a.descricao
        _b = b.descricao
      } else if (this.colunaOrdena === 'nivel_instrutor') {
        _a = a.nivel_instrutor.descricao
        _b = b.nivel_instrutor.descricao
      } else if (this.colunaOrdena === 'valor') {
        _a = a.valor_hora.valor
        _b = b.valor_hora.valor
      } else if (this.colunaOrdena === 'situacao') {
        _a = a.valor_hora.situacao
        _b = b.valor_hora.situacao
      } else {
        _a = a.descricao
        _b = b.descricao
      }
      lista.push(_a)
      lista.push(_b)
      return lista
    },

    compararAsc (a, b) {
      let lista = this.verificarColuna(a, b)
      let _a = lista[0]
      let _b = lista[1]

      if (_a < _b) {
        return -1
      }
      if (_a > _b) {
        return 1
      }
      return 0
    },

    compararDesc (a, b) {
      let lista = this.verificarColuna(a, b)
      let _a = lista[0]
      let _b = lista[1]

      if (_a > _b) {
        return -1
      }
      if (_a < _b) {
        return 1
      }
      return 0
    },

    inativar (item) {
      if ((this.permissoes['CRIAR'] && (this.permissoes['CRIAR'].possui_permissao === true)) || (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true))) {
        const valorHora = item.valor_hora
        const mensagem = valorHora === 'A' ? 'desativar' : 'ativar'
        EventBus.$emit('chamarModal', {
          resolve: success => {
            valorHora.situacao = valorHora.situacao === 'A' ? 'I' : 'A'
            this.salvar(valorHora)
          }
        }, `Deseja ${mensagem} este valor hora?`)
      }
    },

    salvar (item) {
      this.$store.commit('valorHora/SET_ITEM_SELECIONADO_ID', item.id)
      this.$store.commit('valorHora/SET_ITEM_SELECIONADO', item)

      if (this.itemSelecionadoID) {
        this.atualizar()
      } else {
        this.criar().then(res => { item.id = res.body.corpo.objetoORM })
      }
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
