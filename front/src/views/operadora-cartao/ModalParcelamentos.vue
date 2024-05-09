<template>
  <div >
    <form :class="{ 'was-validated': !$v.$invalid }" class="needs-validation" novalidate @submit.prevent="adicionarNovoParcelamentoOperadoraCartao(), $emit('hide')">
      <h5 class="title-module mb-2">Parcelamento</h5>
      <div class="form-group row">
        <div class="col-md-12 mb-3">
          <label class="col-form-label" for="plano-contas">Plano de Contas *</label>
          <g-treeselect
            id="plano-contas"

            :value="planoContaSelecionado"
            :select="setPlanoContaCategoria"
            :options="planoContas"

            :invalid="$v.planoContaSelecionado.$invalid"
            required
          />
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-12 d-flex justify-content-between">
          <h5 class="title-module">Parcelas</h5>

          <b-btn variant="azul" title="Adicionar nova parcela" @click="adicionarParcela()">
            <font-awesome-icon icon="plus" />
          </b-btn>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-12">
          <b-row v-show="Object.keys(parcelaParcelamentoCartaoLista).length" class="header-card-list">
            <b-col md="3">
              <label class="col-form-label">Nº Parcela</label>
            </b-col>
            <b-col md="4">
              <label class="col-form-label">Dias de Repasse*</label>
            </b-col>
            <b-col md="3">
              <label class="col-form-label">% Taxa*</label>
            </b-col>
            <b-col md="2"/>
          </b-row>

          <b-row v-for="(item, index) in parcelaParcelamentoCartaoLista" :key="item.numero_parcela" class="body-card-list">
            <b-col :num="item.numero_parcela = index + 1" md="3" data-header="Nº Parcela">{{ index + 1 }}</b-col>
            <b-col md="4" data-header="Dias de Repasse*">
              <vue-numeric :empty-value="1" v-model="item.dias_repasse" :max="999" :class="{'is-invalid' : item.dias_repasse != '' && !$v.parcelaParcelamentoCartaoLista.$each[index].dias_repasse}" class="form-control" maxlength="3" required />

              <div v-if="item.dias_repasse != '' && !$v.parcelaParcelamentoCartaoLista.$each[index].dias_repasse" class="input-invalid">
                O valor precisa ser maior que os anteriores!
              </div>

            </b-col>
            <b-col md="4" data-header="% Taxa*">
              <vue-numeric :empty-value="null" v-model="item.taxa" :max="100" :class="{'is-invalid' : item.dias_repasse != '' && !$v.parcelaParcelamentoCartaoLista.$each[index].dias_repasse}" :precision="2" class="form-control" maxlength="6" separator="." required />
              <div class="invalid-feedback">Campo não pode ser vazio!</div>
            </b-col>
            <b-col md="1">
              <b-btn v-if="parcelaParcelamentoCartaoLista.length > 1" type="button" variant="clear" class="btn-minus" @click="removerParcela(index, item)">
                <font-awesome-icon icon="minus" />
              </b-btn>
            </b-col>
          </b-row>
        </div>
      </div>

      <div class="form-group list-group-accent">
        <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> Após confirmação é necessário salvar o formulário para efetivar as alterações.
        </div>
      </div>

      <div class="p-3 d-flex justify-content-center">
        <b-btn :disabled="verificaCamposObrigatoriosNaoPreenchidos()" type="submit" variant="verde">Confirmar</b-btn>
        <b-btn type="button" variant="link" @click="resetaDados(), cancelarDados()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>
<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

const verificaCampoDiasRepasse = (valor, vm) => {
  let lista = vm.filter(item => {
    if (valor.numero_parcela > item.numero_parcela) {
      return (valor.dias_repasse * 1) <= (item.dias_repasse * 1)
    }
  })

  if (lista.length > 0) {
    return false
  }

  return true
}

export default {
  name: 'ModalParcelamentos',
  props: {
    indiceParcelaOperadoraCartao: {
      required: true,
      type: Number
    },
    parcelamentoOpradoraCartaoObjeto: {
      required: false,
      type: Object,
      default: null
    },
    parcelaParcelamentoCartaoLista: {
      required: false,
      type: Array,
      default: null
    },
    atualizaDadosListagem: {
      required: false,
      type: Function,
      default: null
    },
    cancelarDados: {
      required: true,
      type: Function,
      default: null
    }
  },
  data () {
    return {
      isValid: true,
      novoRegistro: false,
      objetoOriginal: {},
      arrayItensDeletados: [],
      planoContaSelecionado: null
    }
  },
  computed: {
    ...mapState('planoConta', {listaPlanoConta: 'arvoreItens'}),
    planoContas: {
      get () {
        return this.listaPlanoConta.filter(item => item.tipo_movimento_nota === 'E')
      }
    }
  },
  validations: {
    planoContaSelecionado: {required},
    parcelaParcelamentoCartaoLista: {
      $each: {
        $trackBy: 'numero_parcela',
        dias_repasse: verificaCampoDiasRepasse,
        taxa: {
          required
        }
      }
    }
  },
  methods: {
    ...mapMutations('planoConta', {SET_PAGINA_PLANO_CONTA: 'SET_PAGINA_ATUAL'}),
    ...mapActions('planoConta', {listarPlanoConta: 'listar'}),

    listaCamposDinamicos () {
      this.SET_PAGINA_PLANO_CONTA(1)
      this.listarPlanoConta()
    },

    removerParcela (index, item) {
      if (item.id && item.id !== null) {
        let objetoNovo = Object.assign({}, item)
        objetoNovo.deletado = 1
        this.arrayItensDeletados.push(objetoNovo)
      }
      this.parcelaParcelamentoCartaoLista.splice(index, 1)
    },

    verificaCamposObrigatoriosNaoPreenchidos () {
      let possuiParcelasCadastradas = this.parcelaParcelamentoCartaoLista.length > 0
      if ((possuiParcelasCadastradas === false) || (this.planoContaSelecionado === null) || (this.planoContaSelecionado.id && this.planoContaSelecionado.id === null) || this.$v.$invalid) {
        return true
      }
      return false
    },

    adicionarParcela () {
      this.parcelaParcelamentoCartaoLista.push({
        numero_parcela: null,
        dias_repasse: '',
        taxa: ''
      })
    },

    setPlanoContaCategoria (value) {
      this.planoContaSelecionado = value
    },

    resetaDados () {
      if (this.parcelamentoOpradoraCartaoObjeto === null) {
        this.parcelaParcelamentoCartaoLista.push({
          dias_repasse: '',
          taxa: ''
        })
      }
    },

    adicionarNovoParcelamentoOperadoraCartao () {
      if (this.verificaCamposObrigatoriosNaoPreenchidos() === true) {
        this.isValid = false
        return
      }

      let novoArray = this.parcelaParcelamentoCartaoLista.concat(this.arrayItensDeletados)
      let descricaoNova = this.montaDescricao(this.parcelaParcelamentoCartaoLista)

      let novoObjeto = {
        id: this.parcelamentoOpradoraCartaoObjeto.id,
        descricao: descricaoNova,
        plano_conta: this.planoContaSelecionado,
        parcela_parcelamentos: novoArray
      }

      this.atualizaDadosListagem(this.indiceParcelaOperadoraCartao, novoObjeto, this.novoRegistro)
    },

    montaDiasParaDescricao () {
      let diasDescricao = []
      this.parcelaParcelamentoCartaoLista.forEach(item => {
        diasDescricao.push(item.dias_repasse)
      })
      diasDescricao.sort((a, b) => {
        return a - b
      })
      return diasDescricao.join(', ')
    },

    montaDescricao (arrayNovo) {
      let descricao = arrayNovo.length + 'x em ' + this.montaDiasParaDescricao() + ' dia(s)'
      return descricao
    }
  }
}
</script>
