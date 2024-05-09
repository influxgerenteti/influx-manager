<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="animated fadeIn p-3">
        <div class="form-group row">

          <div class="col-md-6">
            <label v-help-hint="'form-forma-pagamento_descricao'" for="descricao" class="col-form-label">Descrição *</label>
            <input id="descricao" :readonly="item && item.id" v-model="descricao" type="text" class="form-control" placeholder="" maxlength="60" required>
            <div class="invalid-feedback">Informe a descrição!</div>
          </div>

          <div class="col-md-4">
            <label v-help-hint="'form-forma-pagamento_descricao_abreviada'" for="descricao_abreviada" class="col-form-label">Abreviação *</label>
            <input id="descricao_abreviada" v-model="descricao_abreviada" type="text" class="form-control" placeholder="" maxlength="10" required>
            <div class="invalid-feedback">Informe a abreviação!</div>
          </div>

          <div class="col-md-2">
            <label v-help-hint="'form-forma-pagamento_liquidacao_imediata'" for="liquidacao_imediata" class="col-form-label">Liquidação imediata</label>
            <div class="custom-checkbox">
              <b-form-checkbox id="liquidacao_imediata" v-model="status_liquidacao" :value="true" :unchecked-value="false">Sim</b-form-checkbox>
            </div>
          </div>

        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <label class="col-form-label d-block">Forma de pagamento</label>
            <b-form-group>
              <b-form-radio-group
                v-model="tipoPagamento"
                :options="formas"
                name="forma-pagamento"
              />
            </b-form-group>
          </div>

        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/configuracoes/forma-pagamento" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  data () {
    return {
      isValid: true,
      errorMsg: '',
      descricao: '',
      descricao_abreviada: '',
      situacao: 'A',
      isEdit: false,
      inputval: '',
      status_liquidacao: '',
      tipoPagamento: 'forma_nenhum',
      formas: [
        {text: 'Nenhuma', value: 'forma_nenhum'},
        {text: 'Cobrança bancária (boleto)', value: 'forma_boleto'},
        {text: 'Cheque', value: 'forma_cheque'},
        {text: 'Cartão de crédito', value: 'forma_cartao'},
        {text: 'Cartão de débito', value: 'forma_cartao_debito'},
        {text: 'Transferência', value: 'forma_transferencia'}
      ],
      status_boleto: false,
      status_cheque: false,
      status_credito: false,
      status_debito: false,
      status_transferencia: false
    }
  },
  validations: {
    descricao: {required},
    descricao_abreviada: {required}
  },
  computed: {
    ...mapState('formaPagamento', ['lista', 'item', 'estaCarregando'])
  },
  watch: {
    item (value) {
      this.descricao = value.descricao
      this.descricao_abreviada = value.descricao_abreviada
      this.status_liquidacao = value.liquidacao_imediata
      this.status_boleto = value.forma_boleto
      this.status_cheque = value.forma_cheque
      this.status_credito = value.forma_cartao
      this.status_debito = value.forma_cartao_debito
      this.status_transferencia = value.forma_transferencia
    },

    descricao (value) {
      this.SET_DESCRICAO(value)
    },

    descricao_abreviada (value) {
      this.SET_DESCRICAO_ABREVIADA(value)
    },

    status_liquidacao (value) {
      this.SET_LIQUIDACAO_IMEDIATA(value)
    },

    status_boleto (value) {
      this.SET_FORMA_BOLETO(value)
    },

    status_cheque (value) {
      this.SET_FORMA_CHEQUE(value)
    },

    status_credito (value) {
      this.SET_FORMA_CREDITO(value)
    },

    status_debito (value) {
      this.SET_FORMA_DEBITO(value)
    },

    status_transferencia (value) {
      this.SET_FORMA_TRANSFERENCIA(value)
    },

    tipoPagamento (value) {
      this.status_boleto = value === 'forma_boleto'
      this.status_cheque = value === 'forma_cheque'
      this.status_credito = value === 'forma_cartao'
      this.status_debito = value === 'forma_cartao_debito'
      this.status_transferencia = value === 'forma_transferencia'
    }

  },
  created () {
    if (this.$route.params.id) {
      this.isEdit = true
      this.SET_SELECIONADO(this.$route.params.id)
      this.getItem().then(() => {
        Object.keys(this.item).map(forma => {
          if (forma.includes('forma') && this.item[forma] === true) {
            this.tipoPagamento = forma
          }
        })
      })
    } else {
      this.LIMPAR_ITEM()
    }
  },
  methods: {
    ...mapActions('formaPagamento', ['getLista', 'getItem', 'criar', 'atualizar']),
    ...mapMutations('formaPagamento', ['SET_ITEM', 'LIMPAR_ITEM', 'SET_SELECIONADO', 'SET_DESCRICAO', 'SET_DESCRICAO_ABREVIADA', 'SET_ESTA_CARREGANDO', 'SET_LIQUIDACAO_IMEDIATA', 'SET_FORMA_BOLETO', 'SET_FORMA_CHEQUE', 'SET_FORMA_CREDITO', 'SET_FORMA_DEBITO', 'SET_FORMA_TRANSFERENCIA']),

    setTipoPagamento () {
      Object.keys(this.item).map(forma => {
        if (forma.includes('forma') && this.item[forma] === true) {
          this.tipoPagamento = forma
        }
      })
    },

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/forma-pagamento')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isEdit) {
        this.atualizar()
          .then(() => {
            this.voltar()
          })
      } else {
        this.criar()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
