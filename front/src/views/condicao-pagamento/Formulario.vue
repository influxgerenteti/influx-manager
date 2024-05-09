<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div class="form-group row">
        <div class="col-md-6">
          <label for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" maxlength="30" required>
          <div class="invalid-feedback">Preencha a descrição</div>
        </div>
        <div class="col-md-6">
          <label for="quantidade_parcelas" class="col-form-label">Quantidade de parcelas *</label>
          <input v-mask="'###'" id="quantidade_parcelas" v-model="quantidade_parcelas" type="text" class="form-control" required>
          <div class="invalid-feedback">Preencha a quantidade de parcelas.</div>
        </div>
      </div>

      <div class="body-sector">
        <template v-if="quantidade_parcelas >= 1 && itemSelecionado.parcelas.length > 0">
          <table :class="vencimentosInvalidos || percentuaisInvalidos ? 'invalid-state' : null" class="table-scroll table b-table table-borderless">
            <thead>
              <tr>
                <th></th>
                <th>Vencimento (dias)</th>
                <th>Percentual</th>
                <th></th>
              </tr>
            </thead>
            <tbody ref="scroll-wrap">
              <tr v-for="(parcela, index) in itemSelecionado.parcelas" :key="index">
                <td><span>{{ parcela.numero_parcela }}</span></td>
                <td><input v-mask="'####'" v-model="parcela.dias_vencimento" type="text" class="form-control text-center" required @change="validarVencimentos(index)"></td>
                <td><input v-model="parcela.percentual_parcela" type="text" class="form-control text-center" required @keydown="parcelaKeydown($event)" @input="calcularTotal()"></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </template>
        <template v-else>
          <div>
            <p>Informe a quantidade de parcelas.</p>
          </div>
        </template>

        <b-alert :show="(vencimentosInvalidos || percentuaisInvalidos) && (quantidade_parcelas >= 1 && itemSelecionado.parcelas.length > 0)" variant="danger">
          <template v-if="vencimentosInvalidos">Dias de vencimento inseridos estão inválidos.</template>
          <template v-if="vencimentosInvalidos && percentuaisInvalidos"><br></template>
          <template v-if="percentuaisInvalidos">Percentuais inseridos estão inválidos.</template>
        </b-alert>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <b-btn :disabled="vencimentosInvalidos || percentuaisInvalidos" type="submit" variant="verde">Salvar</b-btn>
          <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {round, toNumber} from '../../utils/number'

const reducer = (acc, parcela) => round(acc + toNumber(parcela.percentual_parcela || 0))

export default {
  data () {
    return {
      isValid: true,
      vencimentosInvalidos: false,
      percentuaisInvalidos: false,
      percentual_total: 0,
      whiteListKeys: ['ArrowDown', 'ArrowUp', 'ArrowRight', 'ArrowLeft', 'Backspace', 'Tab']
    }
  },
  computed: {
    ...mapState('condicaoPagamento', ['itemSelecionado', 'itemSelecionadoID']),

    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        this.SET_DESCRICAO(value)
      }
    },
    quantidade_parcelas: {
      get () {
        return this.itemSelecionado.quantidade_parcelas
      },

      set (value) {
        value = value === '' ? null : value * 1
        if (value && value > 399) {
          value = 399
        }

        this.SET_QUANTIDADE_PARCELAS(value)
      }
    }
  },

  watch: {
    'itemSelecionado.parcelas' (value) {
      this.validarVencimentos()
      this.calcularTotal()
    }
  },

  created () {
    this.LIMPAR_ITEM_SELECIONADO()
  },

  validations: {
    descricao: {required},
    quantidade_parcelas: {required}
  },

  methods: {
    ...mapMutations('condicaoPagamento', [
      'LIMPAR_ITEM_SELECIONADO',
      'SET_DESCRICAO',
      'SET_QUANTIDADE_PARCELAS',
      'SET_PARCELA_DIAS_VENCIMENTO',
      'SET_PARCELA_PERCENTUAL_PARCELA'
    ]),
    ...mapActions('condicaoPagamento', ['buscar', 'criar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/condicao-pagamento')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.criar().then(this.voltar).catch(console.error)
    },

    parcelaKeydown ($event) {
      const key = $event.key
      const value = $event.target.value

      if (this.whiteListKeys.includes(key)) {
        return true
      }

      if (value.length >= 6) {
        $event.preventDefault()
      }

      if (/[0-9]/.test(key) === true) {
        return true
      }

      if (key === '.') {
        const match = value.match(/\./g)
        if (!match) {
          return true
        }
      }

      $event.preventDefault()
    },

    validarVencimentos (index = null) {
      let ultimaParcela = 0
      let intervaloDias = null
      const parcelas = this.itemSelecionado.parcelas
      const length = parcelas.length

      // No segundo item da lista, apenas.
      if (index === 1) {
        const vencimento0 = parcelas[0].dias_vencimento * 1
        const vencimento1 = parcelas[1].dias_vencimento * 1
        intervaloDias = vencimento1 - vencimento0
      }

      for (let i = 0; i < length; i++) {
        const naoPossuiVencimento = parcelas[i].dias_vencimento === null
        if (index && naoPossuiVencimento && intervaloDias && i > index) {
          parcelas[i].dias_vencimento = (parcelas[i - 1].dias_vencimento * 1) + intervaloDias
        }

        const vencimento = parcelas[i].dias_vencimento * 1
        if (vencimento < ultimaParcela) {
          this.vencimentosInvalidos = true
          return
        }

        this.vencimentosInvalidos = false
        ultimaParcela = vencimento
      }
    },

    calcularTotal () {
      this.percentual_total = this.itemSelecionado.parcelas.reduce(reducer, 0)
      if (this.itemSelecionado.parcelas.length > 0) {
        this.percentuaisInvalidos = this.percentual_total !== 100
      }
    }
  }
}
</script>
<style scoped>
.table td, .table th {
  border: 0;
  display: flex;
  align-items: center;
  flex-grow: 2;
}
.table td:first-child, .table th:first-child,
.table td:last-child, .table th:last-child {
  flex-grow: 1;
}
.body-sector {
  height: calc(100vh - 248px);
  height: -webkit-calc(100vh - 248px);
  height: -moz-calc(100vh - 248px);
  overflow: hidden;
}
.table-scroll {
  height: calc(100vh - 250px);
  height: -webkit-calc(100vh - 250px);
  height: -moz-calc(100vh - 250px);
  margin: 0;
}
.table-scroll.invalid-state {
  height: calc(100vh - 320px);
  height: -webkit-calc(100vh - 320px);
  height: -moz-calc(100vh - 320px);
}
.alert {
  width: 100%;
  height: 100%;
  border-color: transparent;
}

.body-sector div:not(.alert) {
  display: flex;
  align-items: center;
  height: 100%;
}
.body-sector div p {
  margin: auto;
}

.table-borderless.table > tbody > tr > td span {
  margin-left: auto;
}

@media (max-width: 991.98px) {
  .body-sector {
    height: calc(100vh - 303px);
    height: -webkit-calc(100vh - 303px);
    height: -moz-calc(100vh - 303px);
    overflow: hidden;
  }
  .table-scroll {
    height: calc(100vh - 305px);
    height: -webkit-calc(100vh - 305px);
    height: -moz-calc(100vh - 305px);
  }
  .table-scroll.invalid-state {
    height: calc(100vh - 375px);
    height: -webkit-calc(100vh - 375px);
    height: -moz-calc(100vh - 375px);
  }
}
@media (max-width: 767px) {
  .body-sector {
    height: calc(100vh - 372px);
    height: -webkit-calc(100vh - 372px);
    height: -moz-calc(100vh - 372px);
    overflow: hidden;
  }
  .table-scroll {
    height: calc(100vh - 380px);
    height: -webkit-calc(100vh - 380px);
    height: -moz-calc(100vh - 380px);
  }
  .table-scroll.invalid-state {
    height: calc(100vh - 466px);
    height: -webkit-calc(100vh - 466px);
    height: -moz-calc(100vh - 466px);
  }
}
@media (max-width: 559px) {
  .table td:last-child, .table th:last-child {
    flex-grow: 2;
  }
  .table-scroll thead th, .table-scroll tbody td {
    flex: unset;
  }
}
</style>
