<template>
  <form @submit.prevent="salvar()">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div class="row">
      <div class="col-lg-6">
        <label v-help-hint="'form-parametros-franqueadora_dias_variacao_vencimento'" for="dias_variacao_vencimento" class="col-form-label">Variação de vencimento (dias)</label>
        <input v-mask="'#########'" id="dias_variacao_vencimento" v-model="dias_variacao_vencimento" type="text" class="form-control">
      </div>
      <div class="col-lg-6">
        <label v-help-hint="'form-parametros-franqueadora_percentual_variacao_valores'" for="percentual_variacao_valores" class="col-form-label">Variação de valores (%)</label>
        <money id="percentual_variacao_valores" v-bind="moeda" v-model="percentual_variacao_valores" type="text" class="form-control" maxlength="6" />
      </div>
      <div class="col-lg-6">
        <label v-help-hint="'form-parametros-franqueadora_numero_maximo_parcelas'" for="numero_maximo_parcelas" class="col-form-label">Número máximo de parcelas</label>
        <input v-mask="'#########'" id="numero_maximo_parcelas" v-model="numero_maximo_parcelas" type="text" class="form-control">
      </div>
      <div class="col-lg-6">
        <label v-help-hint="'form-parametros-franqueadora_percentual_variacao_valores'" for="percentual_variacao_valores" class="col-form-label">Variação de valores (%)</label>
        <money id="percentual_variacao_valores" v-bind="moeda" v-model="percentual_variacao_valores" type="text" class="form-control" maxlength="6" />
      </div>
    </div>
    <div class="row bootom-btn-salve">
      <div class="col-12">
        <button v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true) && permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" type="submit" class="btn btn-verde">Salvar</button>
      </div>
    </div>
  </form>

</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {

  name: 'FormularioParametrosFranqueadora',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      dias_variacao_vencimento: '',
      percentual_variacao_valores: '',
      dias_reativacao_interessado: '',
      numero_maximo_parcelas: '',
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: false
      }
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('parametrosFranqueadora', ['lista', 'item', 'estaCarregando'])
  },
  watch: {
    item (value) {
      this.dias_variacao_vencimento = value.dias_variacao_vencimento
      this.percentual_variacao_valores = value.percentual_variacao_valores
      this.numero_maximo_parcelas = value.numero_maximo_parcelas
      this.dias_reativacao_interessado = value.dias_reativacao_interessado
      this.SET_ESTA_CARREGANDO(false)
    },

    dias_variacao_vencimento (value) {
      this.SET_DIAS_VARIACAO_VENCIMENTO(value)
    },

    percentual_variacao_valores (value) {
      this.SET_PERCENTUAL(value)
    },

    numero_maximo_parcelas (value) {
      this.SET_NUMERO_MAXIMO_PARCELAS(value)
    },

    dias_reativacao_interessado (value) {
      this.SET_DIAS_REATIVACAO_INTERESSADO(value)
    }

  },
  created () {
    this.getItem()
  },
  methods: {
    ...mapActions('parametrosFranqueadora', ['getItem', 'atualizar']),
    ...mapMutations('parametrosFranqueadora', ['SET_ITEM', 'SET_DIAS_VARIACAO_VENCIMENTO', 'SET_PERCENTUAL', 'SET_LIMITE_DIAS_ALTERACAO_DOCUMENTO', 'SET_NUMERO_MAXIMO_PARCELAS', 'SET_DIAS_REATIVACAO_INTERESSADO', 'SET_ESTA_CARREGANDO']),

    salvar () {
      this.atualizar()
    }
  }
}
</script>
