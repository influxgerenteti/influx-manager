<template>
  <div class="animated fadeIn">
    <div>
      <b-modal id="modalAjusteEstoque" v-model="bVerModal" size="lg" title="Ajuste de estoque" hide-footer no-close-on-backdrop
      >
        <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvarAjuste()">
          <div class="form-loading">
            <load-placeholder :loading="requisicaoCarregada" />
          </div>

          <div class="form-group row">
            <div class="col-md-4">
              <label v-help-hint="'form-item_descricao'" for="descricao" class="col-form-label">
                Descrição
              </label>
              <input id="descricao" v-model="itemSelecionado.descricao" type="text" class="form-control" placeholder="" maxlength="80" disabled>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'form-item_saldo_estoque'" for="saldo_estoque" class="col-form-label">
                Quantidade Atual
              </label>
              <vue-numeric id="saldo_estoque" :empty-value="null" v-model="itemSelecionado.saldo_estoque" :max="9999999" disabled class="form-control"/>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'form-item_ajuste_estoque'" for="ajuste_estoque" class="col-form-label">
                Quantidade para Ajuste *
              </label>
              <vue-numeric id="ajuste_estoque" :empty-value="1" v-model="quantidadeAjustada" :max="9999999" :class="(quantidadeAjustada === 0 || $v.quantidadeAjustada.$invalid) ? 'invalid-input' : 'valid-input'" separator="." class="form-control" required/>
              <div v-if="!isValid && $v.quantidadeAjustada.$invalid" class="multiselect-invalid">
                A quantidade informada, não pode exceder a quantidade atual!
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <b-form-group label="Tipo de Ajuste *">
                <b-form-radio-group
                  v-model="tipoAjusteSelecionado"
                  :options="listaTipoDeAjuste"
                  name="radio-validation"
                  value-field="id"
                  text-field="descricao"
                  required
                  @change="trocaDeAjuste" />
              </b-form-group>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-md-12">
              <label v-help-hint="'form-item_justificativa'" for="justificativa" class="col-form-label">
                Justificativa *
              </label>
              <textarea id="justificativaTexto" v-model="justificativaTexto" class="form-control full-textarea" placeholder="Justificativa da alteração do estoque" rows="6" maxlength="5000" required></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - justificativaTexto.length }}</span>
            </div>
          </div>
          <div>
            <b-btn :disabled="isSalvando" type="submit" variant="verde">{{ isSalvando ? 'Salvando...': 'Salvar' }}</b-btn>
            <b-btn variant="link" @click="bVerModal = false, $emit('hide')">Cancelar</b-btn>
          </div>
        </form>
      </b-modal>
    </div>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {requiredIf} from 'vuelidate/lib/validators'

const validaRequiredQuantidade = (value, vm) => {
  if ((typeof value === 'number') && (typeof vm.itemSelecionado.saldo_estoque === 'string')) {
    let saldo = Number(vm.itemSelecionado.saldo_estoque.replace(',', '.'))
    if ((vm.tipoEntradaSaida === 'AS') && (value > saldo)) {
      return false
    }
  }
  return true
}

export default {
  name: 'ModalAjusteEstoque',
  data () {
    return {
      bVerModal: false,
      isSalvando: false,
      justificativaTexto: '',
      tipoEntradaSaida: '',
      tipoAjusteSelecionado: null,
      isValid: true,
      quantidadeAjustada: 0,
      itemSelecionado: {
        id: null,
        descricao: null,
        saldo_estoque: '0'
      }
    }
  },
  validations: {
    justificativaTexto: {
      required: requiredIf(function () {
        return this.justificativaTexto.trim().length === 0
      })
    },
    quantidadeAjustada: {validaRequiredQuantidade},
    tipoAjusteSelecionado: {
      required: requiredIf(function () {
        return this.tipoAjusteSelecionado === null
      })
    }
  },
  computed: {
    ...mapState('tipoMovimentoEstoque', {listaTipoDeAjusteRequisicao: 'lista', requisicaoCarregada: 'estaCarregando'}),

    listaTipoDeAjuste: {
      get () {
        return this.listaTipoDeAjusteRequisicao.filter(item => item.tipo_movimento === 'AE' || item.tipo_movimento === 'AS')
      }
    }
  },
  methods: {
    ...mapActions('tipoMovimentoEstoque', ['getLista']),
    ...mapActions('movimentacaoEstoque', {criarEstoque: 'criar'}),
    ...mapMutations('tipoMovimentoEstoque', {setPaginaAtual: 'SET_PAGINA_ATUAL'}),
    ...mapMutations('movimentacaoEstoque', {setParametrosMovimentoEstoque: 'SET_PARAMETROS'}),

    trocaDeAjuste (value) {
      this.tipoEntradaSaida = this.listaTipoDeAjuste.filter(item => item.id === value)[0].tipo_movimento
    },

    resetModalAjusteEstoque () {
      this.justificativaTexto = ''
      this.tipoEntradaSaida = ''
      this.tipoAjusteSelecionado = null
      this.isValid = true
      this.isSalvando = false
      this.quantidadeAjustada = 0
      this.itemSelecionado = {
        id: null,
        descricao: null,
        saldo_estoque: '0'
      }
      this.setParametrosMovimentoEstoque({})
    },

    salvarAjuste () {
      this.isSalvando = true
      if (this.$v.$invalid) {
        this.isValid = false
        this.isSalvando = false
        return
      }
      let parametros = {
        item: this.itemSelecionado.id,
        tipo_movimento_estoque: this.tipoAjusteSelecionado,
        saldo_estoque: this.quantidadeAjustada,
        observacao: this.justificativaTexto,
        tipo: this.tipoEntradaSaida
      }
      this.setParametrosMovimentoEstoque(parametros)
      this.criarEstoque()
        .then(() => {
          this.bVerModal = false
          this.$emit('rcdados')
          this.$emit('hide')
        })
        .catch((error) => {
          this.isSalvando = false
          console.error(error)
        })
    },

    carregarRequisicoes () {
      this.setPaginaAtual(1)
      this.getLista()
    },

    setarItemExibicao (item) {
      this.itemSelecionado.id = item.id
      this.itemSelecionado.descricao = item.descricao
      const saldo = item.itemFranqueadas[0] ? item.itemFranqueadas[0].saldo_estoque : 0
      this.itemSelecionado.saldo_estoque = String(saldo).replace(/[.]/, ',')
    }
  }
}
</script>
