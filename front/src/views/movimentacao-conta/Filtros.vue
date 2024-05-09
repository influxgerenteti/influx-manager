<template>
  <div class="filtro-avancado body-sector">
    <div class="d-flex justify-content-between filtro-header head-content-sector">
      <div>
        <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtros.avancado = false, filtroRapido = !filtroRapido, filtroAvancado = false, trocaClasse(filtroRapido ? 'rapido-open' : null), filtroSelecionado = 1">Filtro Rápido</div>
        <div :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtros.avancado = true, filtroAvancado = !filtroAvancado, filtroRapido = false, trocaClasse(filtroAvancado ? 'filtro-open' : null), filtroSelecionado = 2">Avançado</div>
      </div>
    </div>

    <b-collapse id="filtros-rapidos" v-model="filtroRapido">
      <form class="p-2">
        <div class="row">
          <b-col md="2">
            <label v-help-hint="'filtroRapido-movimentacao-conta_conta'" for="conta_rapido" class="col-form-label">Conta</label>
            <g-select
              v-input-locker="{permissao: permissoes['CONTA']}"
              id="conta_rapido"
              :value="filtros.conta"
              :select="setConta"
              :extra-param="true"
              :options="listaContasRapido"
              class="multiselect-truncate"
              label="descricao"
            />
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroRapido-movimentacao-conta_mes'" for="mes_rapido" class="col-form-label">Mês</label>
            <g-select
              id="mes_rapido"
              :value="filtros.mes"
              :select="setMes"
              :options="meses"
              class="multiselect-truncate"
              label="text"
            />
          </b-col>

          <b-col md="auto">
            <label v-help-hint="'filtroRapido-movimentacao-conta_ano'" for="ano_rapido" class="col-form-label d-block">Ano</label>
            <b-form-radio-group id="ano_rapido" v-model="filtros.ano" :options="anos" buttons button-variant="cinza" name="ano_rapido" class="checkbtn-line" @change="setAno"/>
          </b-col>

          <b-col md="auto">
            <label v-help-hint="'filtroRapido-movimentacao-conta_tipo'" for="tipo_rapido" class="col-form-label d-block">Tipo</label>
            <b-form-checkbox-group id="tipo_rapido" v-model="filtros.tipo" :options="tipos" buttons button-variant="cinza" name="tipo_rapido" class="checkbtn-line" @change="setTipo" />
          </b-col>

          <!-- <b-col md="auto">
            <label v-help-hint="'filtroRapido-movimentacao-conta_conciliado'" for="conciliado_rapido" class="col-form-label d-block">Conciliado</label>
            <b-form-checkbox-group id="conciliado_rapido" v-model="filtros.conciliado" :options="opcoesConciliado" buttons button-variant="cinza" name="conciliado_rapido" class="checkbtn-line" @change="setConciliado" />
          </b-col> -->

          <!-- <b-col md="2">
            <label v-help-hint="'filtroRapido-movimentacao-conta_origem'" for="origem" class="col-form-label d-block">Origem</label>
            <typeahead id="buscaOrigemRapido" :item-hit="setOrigem" ref="buscaOrigemRapido" :additional-data="additionalDataOrigem" source-path="/api/movimento_conta/buscar_aluno_fornecedor_com_movimento" key-name="nome_contato" />
          </b-col> -->
        </div>
      </form>
    </b-collapse>

    <b-collapse id="filtros-avancados" v-model="filtroAvancado">
      <form class="p-2" @submit.prevent="buscaAvancada = true, filtrar()">

        <b-row class="form-group">
          <div class="col-md-4">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_lancamento'" class="col-form-label">Movimentos entre</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">De</div>
                  </div>
                  <g-datepicker :element-id="'data_lancamento_inicio'" :value="filtros.data_lancamento_inicio" :selected="setDataLancamentoInicio"/>
                </div>
              </div>
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">à</div>
                  </div>
                  <g-datepicker :element-id="'data_lancamento_fim'" :value="filtros.data_lancamento_fim" :selected="setDataLancamentoFim"/>
                </div>
              </div>
            </div>
            <div v-if="dateToCompare(filtros.data_lancamento_inicio) > dateToCompare(filtros.data_lancamento_fim) && filtros.data_lancamento_fim !== ''" class="floating-message bg-danger">
              Data inicial deve ser menor que a data final!
            </div>
          </div>

          <div class="col-md-4">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_valor_entre'" class="col-form-label">Valor entre</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Min</div>
                  </div>
                  <vue-numeric id="valor_lancamento_de" :precision="2" :empty-value="null" v-model="filtros.valor_lancamento_de" :max="9999999.99" separator="." class="form-control" />
                </div>
              </div>

              <div class="col">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Max</div>
                  </div>
                  <vue-numeric id="valor_lancamento_ate" :precision="2" :empty-value="null" v-model="filtros.valor_lancamento_ate" :max="9999999.99" separator="." class="form-control" />
                </div>
              </div>
            </div>

            <div v-if="filtros.valor_lancamento_de > filtros.valor_lancamento_ate && !!filtros.valor_lancamento_ate" class="floating-message bg-danger">
              Valor mínimo deve ser menor que o máximo!
            </div>
          </div>

          <!-- <b-col md="2">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_usuario'" for="usuario" class="col-form-label d-block">Usuário</label>
            <typeahead id="buscaUsuario" :item-hit="setUsuario" source-path="/api/usuario/buscar_nome" key-name="nome" />
          </b-col> -->

          <b-col md="auto">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_tipo'" for="tipo_avancado" class="col-form-label d-block">Tipo</label>
            <b-form-checkbox-group id="tipo_avancado" v-model="filtros.tipo" :options="tipos" buttons button-variant="cinza" name="tipo_avancado" class="checkbtn-line" />
          </b-col>

          <!-- <b-col md="auto">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_conciliado'" for="conciliado_avancado" class="col-form-label d-block">Conciliado</label>
            <b-form-checkbox-group id="conciliado_avancado" v-model="filtros.conciliado" :options="opcoesConciliado" buttons button-variant="cinza" name="conciliado_avancado" class="checkbtn-line" />
          </b-col> -->
        </b-row>

        <b-row class="form-group">
          <b-col md="6">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_conta'" for="conta_avancado" class="col-form-label">Conta</label>
            <g-select
              id="conta_avancado"
              :value="filtros.conta"
              :select="setConta"
              :options="listaContasAvancado"
              class="multiselect-truncate"
              label="descricao"
            />
          </b-col>

          <b-col md="6">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_forma_pagamento'" for="buscaFormaCobranca" class="col-form-label">Forma de Pagamento</label>
            <g-select
              id="buscaFormaCobranca"
              :value="filtros.forma_cobranca"
              :select="setFormaCobranca"
              :options="listaFormasCobranca"
              class="multiselect-truncate"
              label="descricao"
            />
          </b-col>

          <!-- <b-col md="2">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_numero_lancamento'" for="filtroNumeroLancamento" class="col-form-label">Nº Lançamento</label>
            <input id="filtroNumeroLancamento" v-model="filtros.numero_lancamento" type="text" name="filtroNumeroLancamento" class="form-control">
          </b-col> -->

          <!-- <b-col md="2">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_numero_cartao_ou_cheque'" for="filtroNumeroChequeCartao" class="col-form-label">Nº do Cheque/Transação Cartão</label>
            <input id="filtroNumeroChequeCartao" v-model="filtros.numero_cheque_cartao" type="text" name="filtroNumeroChequeCartao" class="form-control">
          </b-col> -->

          <!-- <b-col md="2">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_numero_categoria'" for="filtroCategoria" class="col-form-label">Categoria</label>
            <input id="filtroCategoria" v-model="filtros.categoria" type="text" name="filtroCategoria" class="form-control">
          </b-col>

          <b-col md="2">
            <label v-help-hint="'filtroAvancado-movimentacao-conta_numero_origem'" for="origem" class="col-form-label d-block">Origem</label>
            <typeahead id="buscaOrigemAvancado" :item-hit="setOrigem" ref="buscaOrigemRapido" :additional-data="additionalDataOrigem" source-path="/api/movimento_conta/buscar_aluno_fornecedor_com_movimento" key-name="nome_contato" />
          </b-col> -->
        </b-row>

        <button type="submit" class="btn btn-cinza btn-block text-uppercase">Buscar</button>
      </form>
    </b-collapse>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'
import Typeahead from '../../components/Typeahead.vue'
import {dateToCompare} from '../../utils/date'
import EventBus from '../../utils/event-bus'

export default {
  components: {
    Typeahead
  },

  data () {
    return {
      filtroSelecionado: 1,
      filtroRapido: true,
      buscaRapida: true,
      filtroAvancado: false,
      buscaAvancada: false,
      anos: [],
      tipos: [
        {text: 'Entrada', value: 'C'},
        {text: 'Saída', value: 'D'},
        {text: 'Todos', value: null}
      ],
      opcoesConciliado: [
        {text: 'Sim', value: 'S'},
        {text: 'Não', value: 'N'}
      ]
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('root', ['usuarioLogado']),
    ...mapState('franqueadas', ['listaFranqueada']),
    ...mapState('conta', {listaContasRapido: 'lista'}),
    ...mapState('movimentacaoConta', ['filtros', 'meses']),

    listaContasAvancado: {
      get () {
        return [
          {
            descricao: 'Todas (apenas ultimo mês)',
            id: null
          },
          ...this.listaContasRapido]
      }
    },

    listaFormasCobranca: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.$store.state.formaPagamento.lista)
      }
    },

    additionalDataOrigem: {
      get () {
        const data = this.filtros && this.filtros.conta && this.filtros.conta.id ? this.filtros.conta.id : null
        return {name: 'conta', data: data}
      }
    }
  },

  mounted () {
    this.$emit('troca-classe-tabela', 'rapido-open')

    const thisYear = (new Date()).getFullYear()
    for (let year = thisYear - 1, endYear = thisYear + 1; year <= endYear; year++) {
      this.anos.push(year)
    }

    this.$store.commit('conta/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('conta/getLista')
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('formaPagamento/getLista')

    this.$store.commit('franqueadas/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('franqueadas/getListaFranqueada')
      .then(() => {
        const franqueada = this.listaFranqueada.filter(i => i.id === this.usuarioLogado.franqueadaSelecionada)[0]
        const conta = franqueada.conta_padrao || this.listaContasRapido[0]
        this.setConta(conta)
      })

    EventBus.$on('form-filtros:buscar-contas', () => {
      this.$store.commit('conta/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('conta/getLista')
        .then(() => {
          this.setConta(this.listaContasRapido.filter(conta => conta.id === this.filtros.conta.id)[0], false)
        })
    })
  },

  methods: {

    ...mapMutations('movimentacaoConta', ['SET_ORIGENS']),

    dateToCompare: dateToCompare,

    setConta (value, rapido = true) {
      this.filtros.conta = value
      if (rapido === true) {
        this.filtrar()
      }
    },

    setMes (value, naoFiltrar = null) {
      this.filtros.mes = value
      if (naoFiltrar === null) {
        this.filtrar()
      }
    },

    setAno (value) {
      this.filtros.ano = value
      this.filtrar()
    },

    setOrigem (value, naoFiltrar = null) {
     this.SET_ORIGENS(value)
      if (naoFiltrar === null) {
        this.filtrar()
      }
    },

    setConciliado (value) {
      this.filtros.conciliado = value
      this.filtrar()
    },

    setTipo (value) {
      this.filtros.tipo = value
      this.filtrar()
    },

    setFormaCobranca (value) {
      this.filtros.forma_cobranca = value
    },

    setDataLancamentoInicio (value) {
      console.log(value)
      this.filtros.data_lancamento_inicio = value
    },

    setDataLancamentoFim (value) {
      this.filtros.data_lancamento_fim = value
    },

    setUsuario (value) {
      this.filtros.usuario = value
    },

    trocaClasse (classe = null) {
      this.setOrigem(null)
      this.$emit('troca-classe-tabela', classe)
     },

    filtrar () {
      this.$emit('filtrar')
    }
  }
}
</script>
