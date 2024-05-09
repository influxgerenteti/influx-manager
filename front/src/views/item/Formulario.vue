<template>
  <div class="animated fadeIn">
    <div>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
        <div v-if="isEdit" class="form-loading">
          <load-placeholder :loading="estaCarregando" />
        </div>

        <div class="animated fadeIn mb-2">
          <div class="form-group row">
            <div class="col-md-8">
              <label v-help-hint="'form-item_descricao'" for="descricao" class="col-form-label">Descrição *</label>
              <input id="descricao-item" v-model="item.descricao" type="text" class="form-control" placeholder="" maxlength="80" required>
              <div class="invalid-feedback">Informe a descrição!</div>
            </div>
            <div class="col-md-4">
              <label v-help-hint="'form-item_unidade_medida'" for="unidade_medida" class="col-form-label">Unidade de Medida</label>
              <input id="unidade_medida" v-model="item.unidade_medida" type="text" class="form-control" placeholder="ex.: UN" maxlength="3">
            </div>
          </div>

          <!-- <div class="table-responsive-sm mb-3"> -->
          <div class="content-sector-extra p-2 box-scroll">
            <h5 class="title-module mb-2 px-2">Preço de venda *</h5>

            <b-row class="header-card-list mx-2 mb-0">
              <b-col md="3">
                <label class="col-form-label">Condição</label>
              </b-col>
              <b-col md="5">
                <label class="col-form-label">Valor *</label>
              </b-col>
              <b-col md="2"/>
            </b-row>

            <div class="row data-scroll">
              <perfect-scrollbar class="scroller col-12">
                <b-row v-for="(item, index) in lista" :key="index" class="body-card-list mx-2">
                  <b-col md="3" data-header="Condição">{{ index === 0 ? 'À vista' : `${index+1}x` }}</b-col>
                  <b-col md="5" data-header="Valor *">
                    <div class="input-group">
                      <div class="input-group-prepend p-0"><span id="pre-icon-saldo" class="input-group-text border-0">R$</span></div>
                      <vue-numeric :id="`valor_venda_${index}`" :precision="2" :empty-value="null" v-model="item.valor_venda" :max="9999999.99" :class="{ 'invalid-list' : !isValid && $v.lista.$each[index].$invalid }" separator="." class="form-control" required/>
                    </div>
                  </b-col>

                  <b-col md="2">
                    <b-btn v-if="lista.length > 1 && lista !== ''" variant="light" class="btn-40" @click="excluir(index, item)">
                      <font-awesome-icon icon="minus" />
                    </b-btn>

                    <b-btn v-if="index === (lista.length - 1) " variant="azul" class="btn-40" @click="lista.push({valor_venda: ''})">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </b-col>
                </b-row>
              </perfect-scrollbar>
            </div>
          </div>
          <!-- </div> -->

          <div class="form-group row">
            <div class="col-md-12">
              <label v-help-hint="'form-item_narrativa'" for="narrativa" class="col-form-label">Narrativa</label>
              <textarea id="narrativa" v-model="item.narrativa" class="form-control" placeholder="Descreva detalhes do produto" rows="6" maxlength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - item.narrativa.length }}</span>
            </div>
          </div>

          <div class="content-sector-extra p-3">
            <h5 class="title-module mb-2">Estoque</h5>
            <div class="form-group row mb-0">
              <div class="col-md-4">
                <label v-help-hint="'form-item_estoque_minimo'" for="estoque_minimo" class="col-form-label">Mínimo</label>
                <vue-numeric id="estoque_minimo" :precision="0" :empty-value="null" v-model="item.itemFranqueadas[0].estoque_minimo" :max="9999999" separator="." class="form-control" />
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-item_saldo_estoque'" for="saldo_estoque-item" class="col-form-label">Saldo</label>
                <vue-numeric id="saldo_estoque-item" :precision="0" :empty-value="null" v-model="item.itemFranqueadas[0].saldo_estoque" :max="9999999" :disabled="isEdit" separator="." class="form-control"/>
              </div>

              <div class="col-md-4">
                <label v-help-hint="'form-item_valor_compra'" for="valor_compra" class="col-form-label">Valor da compra</label>
                <vue-numeric id="valor_compra" :precision="2" :empty-value="null" v-model="item.itemFranqueadas[0].valor_compra" :max="9999999.99" :disabled="isEdit" separator="." class="form-control"/>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <button type="submit" class="btn btn-verde">Salvar</button>
            <b-btn v-b-modal.modalAjusteEstoque v-if="isEdit" variant="roxo" class="btn-40" @click="carregarDados()">Ajustar estoque</b-btn>
            <router-link to="/configuracoes/item" class="btn btn-link">Cancelar</router-link>
          </div>
        </div>
      </form>

      <!-- Modal de Ajuste -->
      <modal-ajuste-estoque ref="ajusteEstoqueModal" @rcdados="recarregarDados" @hide="resetModalAjusteEstoque()" />

    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import ModalAjusteEstoque from './ModalAjusteEstoque.vue'

const vazio = (value, vm) => {
  return true
}

export default {
  name: 'FormularioItem',
  components: {
    ModalAjusteEstoque
  },
  data () {
    return {
      isValid: true,
      errorMsg: '',
      isEdit: false,
      lista: [{}]
    }
  },

  validations: {
    item: {
      descricao: {required}
    },
    lista: {
      $each: {
        valor_venda: {vazio}
      }
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('item', ['item', 'estaCarregando']),
    ...mapState('root', ['franqueadaSelecionada']),
    ...mapState('parametrosFranqueadora', {parametrosFranqueadora: 'item'}),

    listaDeTipoItem: {
      get () {
        return [{descricao: 'Selecione', id: null, tipo: null}].concat(this.listaRequisicaoTipoItem)
      }
    },

    maximoParcelas: {
      get () {
        return this.parametrosFranqueadora.numero_maximo_parcelas
      }
    }
  },

  mounted () {
    this.recarregarDados()
  },

  methods: {
    ...mapActions('item', ['getLista', 'getItem', 'criar', 'atualizar']),
    ...mapMutations('item', ['LIMPAR_ITEM', 'SET_SELECIONADO', 'SET_FRANQUEADA', 'SET_TIPO_ITEM_ID']),

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/item')
    },

    resetModalAjusteEstoque () {
      this.$refs.ajusteEstoqueModal.resetModalAjusteEstoque()
    },

    recarregarDados () {
      this.LIMPAR_ITEM()

      if (this.$route.params.id) {
        this.isEdit = true
        this.SET_SELECIONADO(this.$route.params.id)
        this.getItem().then(() => {
          const obj = {
            valor_venda: 0,
            valor_venda_2: 0,
            valor_venda_3: 0,
            valor_venda_4: 0,
            valor_venda_5: 0,
            valor_venda_6: 0,
            valor_compra: 0,
            estoque_minimo: 0,
            saldo_estoque: 0
          }

          if (!this.item.itemFranqueadas.length) {
            this.item.itemFranqueadas.push(obj)
          }

          this.criarLista()
        })
      }
    },

    carregarDados () {
      this.$refs.ajusteEstoqueModal.setarItemExibicao(this.item)
      this.$refs.ajusteEstoqueModal.carregarRequisicoes()
    },

    criarLista () {
      let lista = []
       // Verifique se valor_venda está definido antes de manipulá-lo
      if (this.item.itemFranqueadas[0] && this.item.itemFranqueadas[0].valor_venda) {
        lista.push({valor_venda: parseFloat(this.item.itemFranqueadas[0].valor_venda.replace(',', '.'))})
      } else {
        lista.push({valor_venda: parseFloat(this.item.itemFranqueadas[0].valor_venda)});
      }

      for (let i = 2; i <= 6; i++) {
        // Verifique se valor_venda_${i} está definido antes de manipulá-lo
        let valorVenda = 0;
        if (this.item.itemFranqueadas[0] && this.item.itemFranqueadas[0][`valor_venda_${i}`]) {
           valorVenda = parseFloat(this.item.itemFranqueadas[0][`valor_venda_${i}`].replace(',', '.'))
        } else {
           valorVenda = parseFloat(this.item.itemFranqueadas[0][`valor_venda_${i}`])
        }
        if (valorVenda > 0) {
          lista.push({valor_venda: valorVenda})
        }
      }
      this.lista = lista
    },

    salvarLista () {
      const lista = this.lista
      this.item.itemFranqueadas[0].valor_venda = lista[0].valor_venda

      if (lista.length > 1) {
        for (let i = 1; i < lista.length; i++) {
          this.item.itemFranqueadas[0][`valor_venda_${i + 1}`] = lista[i].valor_venda
        }
      }
    },

    excluir (index, item) {
      this.item.itemFranqueadas[0].valor_venda = 0
      this.item.itemFranqueadas[0].valor_venda_2 = 0
      this.item.itemFranqueadas[0].valor_venda_3 = 0
      this.item.itemFranqueadas[0].valor_venda_4 = 0
      this.item.itemFranqueadas[0].valor_venda_5 = 0
      this.item.itemFranqueadas[0].valor_venda_6 = 0
      this.lista.splice(index, 1)
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.SET_TIPO_ITEM_ID(1)
      this.salvarLista()

      if (this.isEdit) {
        this.atualizar()
          .then(() => {
            this.voltar()
          })
      } else {
        this.SET_FRANQUEADA(this.franqueadaSelecionada)
        this.criar()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
<style scoped>
@media (max-width: 768px) {
  .table-responsive-sm {
    min-height: 300px;
  }
}
</style>
