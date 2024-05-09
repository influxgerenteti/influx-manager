<template>
  <form v-if="mostrarCampos" :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="salvar()">
    <div v-if="estaCarregando" class="form-loading">
      <load-placeholder :loading="true" />
    </div>

    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-incluir_data_lancamento'" for="incluir_data_movimento" class="col-form-label">Data de lançamento *</label>
        <g-datepicker :class-name="!estaValido && !itemSelecionado.data_movimento ? 'invalid-input' : 'valid-input'" :element-id="'incluir_data_movimento'" :value="itemSelecionado.data_movimento" :selected="setDataMovimento" required />
        <div v-if="!estaValido && !itemSelecionado.data_movimento" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>

      <b-col md="6">
        <label v-help-hint="'form-incluir_data_deposito'" for="incluir_data_deposito" class="col-form-label">Data de movimento *</label>
        <g-datepicker :class="!estaValido && !itemSelecionado.data_deposito ? 'invalid-input' : 'valid-input'" :element-id="'incluir_data_deposito'" :value="itemSelecionado.data_deposito" :selected="setDataDeposito" required />
        <div v-if="!estaValido && !itemSelecionado.data_deposito" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>
    </b-row>

    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-incluir_operacao'" for="incluir_operacao" class="col-form-label">Operação *</label>
        <b-form-radio-group id="incluir_operacao" v-model="itemSelecionado.operacao" name="incluir_operacao" required @change="limparCategoria()">
          <b-form-radio value="C" name="incluir_operacao">Entrada</b-form-radio>
          <b-form-radio value="D" name="incluir_operacao">Saída</b-form-radio>
        </b-form-radio-group>
        <span v-if="!estaValido && !itemSelecionado.operacao" class="multiselect-invalid">Selecione uma opção</span>
      </b-col>
      <b-col md="6">
        <label v-help-hint="'form-incluir_tipo_movimentacao'" for="incluir_forma_pagamento" class="col-form-label">Tipo de movimentação *</label>
        <g-select
          id="incluir_forma_pagamento"
          :class="!estaValido && !itemSelecionado.forma_pagamento ? 'invalid-input' : 'valid-input'"
          :value="itemSelecionado.forma_pagamento"
          :select="setFormaPagamento"
          :options="listaFormasPagamento"
          class="multiselect-truncate"
          label="descricao"
        />
        <div v-if="!estaValido && !itemSelecionado.forma_pagamento" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>
    </b-row>

    <b-row class="form-group">

      <b-col md="6">
        <label v-help-hint="'form-incluir_conta'" for="incluir_conta" class="col-form-label">Conta *</label>
        <g-select
          id="incluir_conta"
          key="id"
          :class="!estaValido && !itemSelecionado.conta ? 'invalid-input' : 'valid-input'"
          :value="itemSelecionado.conta"
          :select="setConta"
          :options="listaContas"
          class="multiselect-truncate"
          label="descricao"
        />
        <div v-if="!estaValido && !itemSelecionado.forma_pagamento" class="multiselect-invalid">Campo obrigatório</div>
      </b-col>

      <b-col md="6">
        <label v-help-hint="'form-conta-pagar_categoria'" for="plano_conta_categoria" class="col-form-label">Categoria *</label>
        <g-treeselect
          id="plano_conta_categoria"
          :value="itemSelecionado.plano_conta"
          :input="setPlanoConta"
          :options="listaPlanosConta"
          required
        />
      </b-col>

      <b-col v-if="itemSelecionado.forma_pagamento && itemSelecionado.forma_pagamento.forma_cheque === true" md="12">
        <label v-help-hint="'form-incluir_conciliado'" for="incluir_conciliado" class="col-form-label">Conciliado *</label>
        <b-form-radio-group id="incluir_conciliado" v-model="itemSelecionado.conciliado" name="incluir_conciliado" required>
          <b-form-radio value="S" name="incluir_conciliado">Sim</b-form-radio>
          <b-form-radio value="N" name="incluir_conciliado">Não</b-form-radio>
        </b-form-radio-group>
        <span v-if="!estaValido && !itemSelecionado.conciliado" class="multiselect-invalid">Selecione uma opção</span>
      </b-col>
    </b-row>

    <b-row class="form-group">
      <b-col md="6">
        <label v-help-hint="'form-incluir_numero_documento'" for="incluir_numero_documento" class="col-form-label">Nº documento</label>
        <input v-model="itemSelecionado.numero_documento" type="text" class="form-control">
      </b-col>
      <b-col md="6">
        <label v-help-hint="'form-incluir_valor'" for="incluir_valor_lancamento" class="col-form-label">Valor *</label>
        <vue-numeric id="incluir_valor_lancamento" :class="!estaValido && !itemSelecionado.valor_lancamento ? 'invalid-input' : 'valid-input'" :precision="2" :empty-value="null" v-model="itemSelecionado.valor_lancamento" :max="9999999.99" separator="." class="form-control" required />
        <span v-if="!estaValido && !itemSelecionado.valor_lancamento" class="multiselect-invalid">Campo obrigatório</span>
      </b-col>
    </b-row>

    <b-row class="form-group">
      <b-col md="12">
        <label v-help-hint="'form-incluir_observacao'" for="incluir_observacao" class="col-form-label">Observação</label>
        <textarea id="incluir_observacao" v-model="itemSelecionado.observacao" class="form-control" placeholder="Detalhes da movimentação" rows="3" maxlength="150"></textarea>
      </b-col>
    </b-row>

    <div>
      <b-btn :disabled="salvando" type="submit" variant="verde">{{ salvando ? 'Salvando...' : 'Salvar' }}</b-btn>
      <b-btn type="button" variant="link" @click="fechar">Cancelar</b-btn>
    </div>
  </form>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {dateToString} from '../../utils/date'
import EventBus from '../../utils/event-bus'

export default {
  data () {
    return {
      estaValido: true,
      salvando: false,
      mostrarCampos: false
    }
  },

  validations: {
    itemSelecionado: {
      data_movimento: {required},
      data_deposito: {required},
      valor_lancamento: {required},
      plano_conta: {required},
      forma_pagamento: {required},
      conciliado: {required},
      operacao: {required}
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['itemSelecionado']),
    ...mapState('planoConta', ['selectReceitas', 'selectDespesas', 'estaCarregando']),
    ...mapState('conta', {listaContas: 'lista'}),
    ...mapState('formaPagamento', {listaFormasPagamento: 'lista'}),

    listaPlanosConta: {
      get () {
        return this.itemSelecionado.operacao === 'C' ? this.selectReceitas : this.selectDespesas
      }
    }
  },

  mounted () {
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('planoConta/listar')

    EventBus.$on('form-incluir:abrir', () => {
      this.estaValido = true
      this.setDataMovimento(dateToString(new Date()))
      this.setDataDeposito(dateToString(new Date()))
      this.mostrarCampos = true
    })
  },

  methods: {
    ...mapActions('movimentacaoConta', ['criar']),

    fechar () {
      this.$emit('fechar')
      this.mostrarCampos = false
    },

    salvar () {
      this.estaValido = false

      if (this.$v.$invalid || !this.itemSelecionado.valor_lancamento) {
        return
      }

      this.salvando = true

      this.criar()
        .then(() => {
          this.$emit('filtrar')
          this.fechar()
          EventBus.$emit('form-filtros:buscar-contas')
        })
        .finally(() => {
          this.salvando = false
        })
    },

    limparCategoria () {
      this.itemSelecionado.plano_conta = null
      this.itemSelecionado.observacao = null
    },

    setDataMovimento (value) {
      this.itemSelecionado.data_movimento = value
      this.$forceUpdate()
    },

    setDataDeposito (value) {
      this.itemSelecionado.data_deposito = value
      this.$forceUpdate()
    },

    setConta (value) {
      this.itemSelecionado.conta = value
      this.$forceUpdate()
    },

    setPlanoConta (value) {
      this.itemSelecionado.plano_conta = value
      if (value) {
        this.itemSelecionado.observacao = value.descricao.replace(/^[_\s]+/, '')
      }
    },

    setFormaPagamento (value) {
      this.itemSelecionado.forma_pagamento = value
    }
  }
}
</script>
