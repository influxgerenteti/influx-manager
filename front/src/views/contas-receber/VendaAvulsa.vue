<template>
  <div>
    <h5 class="title-module mb-2">Venda Avulsa</h5>

    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="incluirVendaAvulsa()">
      <div class="form-group row">
        <div class="col-md-6">
          <label for="sacado_pessoa" class="form-label">Sacado *</label>
          <template v-if="!cliente">
            <typeahead
              id="sacado_pessoa"
              :actions="[{text: 'Adicionar pessoa', icon: 'plus', action: () => $emit('abrir-modal-pessoa')}]"
              :item-hit="setCliente"
              required
              key-name="pessoa.nome_contato"
              source-path="/api/pessoa/buscar_nome_contato" />
          </template>
          <div v-else class="d-flex">
            <span class="form-control form-control-disabled flex-grow-1">{{ cliente.nome_contato }}</span>
            <b-btn variant="link" @click="setCliente(null)">Limpar</b-btn>
          </div>
        </div>

        <div class="col-md-6">
          <label for="vendedor_funcionario" class="form-label">Vendedor *</label>
          <g-select id="vendedor_funcionario"
                    v-model="vendedor_funcionario"
                    :options="listaFuncionarios"
                    :class="!isValid && !vendedor_funcionario ? 'invalid-input' : null"
                    label="apelido"
                    track-by="id"
          />
        </div>
      </div>

      <div class="form-group row d-flex align-items-end">
        <div class="col-md-2">
          <label for="codigo_descricao" class="form-label">Código/Descrição *</label>
          <typeahead
            id="codigo_descricao"
            ref="codigo_descricao"
            :item-hit="setCodesc"
            :invalid="!isValid && $v.codigo_descricao.$invalid"
            :additional-data="{name: 'tipo_item_tipo', data: ['P']}"
            key-name="item.descricao"
            source-path="/api/item/buscar_descricao"
          />
        </div>

        <b-col md="1">
          <label for="estoque" class="col-form-label">Estoque</label>
          <vue-numeric id="estoque" :empty-value="0" v-model="estoque" :max="9999999" class="form-control" disabled />
        </b-col>

        <b-col md="2">
          <label for="quantidade" class="col-form-label">Quantidade *</label>
          <vue-numeric id="quantidade" :class="quantidade < 1 || (codigo_descricao && item_entregue && quantidade > estoque) ? 'invalid-input' : 'valid-input'" :empty-value="1" v-model="quantidade" :max="9999999" class="form-control" @blur="quantidade = quantidade < 1 ? 1 : quantidade" />
          <div v-if="quantidade < 1" class="multiselect-invalid">
            Valor insuficiente.
          </div>
          <div v-if="codigo_descricao && quantidade > estoque && item_entregue" class="multiselect-invalid">
            Estoque insuficiente.
          </div>
        </b-col>

        <b-col md="auto">
          <label for="item_entregue" class="col-form-label">Entregue?</label>
          <div>
            <b-form-checkbox id="item_entregue" v-model="item_entregue" :value="true" class="m-0 p-0" />
          </div>
        </b-col>

        <b-col md="2">
          <label for="valor_unitario" class="col-form-label">Valor unitário</label>
          <vue-numeric id="valor_unitario" :precision="2" :empty-value="0" v-model="valor_unitario" :max="9999999.99" separator="." class="form-control" />
        </b-col>

        <b-col md="2">
          <label for="valor_total" class="col-form-label">Total</label>
          <vue-numeric id="valor_total" :precision="2" :empty-value="null" v-model="valor_total" :calc="valor_total = quantidade * valor_unitario" :max="9999999.99" separator="." class="form-control" disabled />
        </b-col>

        <b-col md="auto" class="mt-2 mt-sm-0">
          <button :disabled="!codigo_descricao || quantidade < 1 || (quantidade > estoque && item_entregue)" type="submit" class="btn btn-roxo btn-block text-uppercase">Incluir</button>
        </b-col>

      </div>
    </form>

    <div class="table-responsive-sm mt-3">
      <g-table v-if="listaVendaAvulsa.length">
        <thead>
          <tr>
            <th data-column="" class="size-27">#</th>
            <th data-column="">Produto</th>
            <th data-column="" class="size-150">Quantidade</th>
            <th data-column="" class="size-150">Valor unitário</th>
            <th data-column="" class="size-150">Total</th>
            <th data-column="" class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar>
            <tr v-for="(item, index) in listaVendaAvulsa" :key="index">
              <td data-label="#" class="size-27"><span>{{ index + 1 }}</span></td>
              <td data-label="Produto"><span>{{ item.produto }}</span></td>
              <td data-label="Quantidade" class="size-150"><span>{{ item.quantidade }}</span></td>
              <td data-label="Valor unitário" class="size-150"><span>{{ item.valor_unitario | formatarMoeda }}</span></td>
              <td data-label="Total" class="size-150"><span>{{ item.valor_total | formatarMoeda }}</span></td>
              <td data-label="Total" class="d-flex coluna-icones">
                <b-btn variant="light" class="btn-40" @click="excluirVendaItem(index, item)">
                  <font-awesome-icon icon="minus" />
                </b-btn>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>

      <div v-else class="form-group list-group-accent">
        <div class="mensagem-info list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> Inclua produtos para iniciar a venda.
        </div>
      </div>
    </div>

    <div v-if="listaVendaAvulsa.length" class="cfooter d-flex justify-content-end align-items-center">
      <div><span>Subtotal</span></div>
      <div></div>
      <div><span>R$</span></div>
      <div>
        {{ subtotal_pagar | formatarMoeda(true) }}
      </div>
    </div>

    <div>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="pagamentoVendaAvulsa()">
        <div class="content-sector sector-vinho-p p-2">
          <h5 class="title-module mb-2">Pagamento</h5>

          <b-row class="form-group">
            <b-col md="2">
              <label for="objVendaAvulsa.forma_cobranca" class="col-form-label">Forma de cobrança *</label>
              <g-select
                id="objVendaAvulsa.forma_cobranca"
                :value="objVendaAvulsa.forma_cobranca"
                :select="setFormaCobranca"
                :options="listaFormasPagamento"
                :class="!isValid && !objVendaAvulsa.forma_cobranca ? 'invalid-input' : 'valid-input'"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
              <div v-if="!isValid && !objVendaAvulsa.forma_cobranca" class="multiselect-invalid">
                Selecione uma opção
              </div>
            </b-col>
            <b-col md="2">
              <label for="numero_parcelas" class="col-form-label">Número de parcelas *</label>
              <vue-numeric id="numero_parcelas" :class="objVendaAvulsa.numero_parcelas < 1 ? 'invalid-input' : 'valid-input'" :empty-value="1" v-model="objVendaAvulsa.numero_parcelas" :max="9999999" class="form-control" @blur="objVendaAvulsa.numero_parcelas = objVendaAvulsa.numero_parcelas < 1 ? 1 : objVendaAvulsa.numero_parcelas" />
              <div v-if="objVendaAvulsa.numero_parcelas < 1" class="multiselect-invalid">
                Deve haver ao menos uma parcela.
              </div>
            </b-col>

            <b-col md="2">
              <label for="objVendaAvulsa.valor_parcela" class="col-form-label">Valor da parcela *</label>
              <vue-numeric id="objVendaAvulsa.valor_parcela" :precision="2" :empty-value="null" v-model="objVendaAvulsa.valor_parcela" :parcela="objVendaAvulsa.valor_parcela = subtotal_pagar/objVendaAvulsa.numero_parcelas" :max="9999999.99" separator="." class="form-control" disabled />
            </b-col>

            <b-col md="2">
              <label class="col-form-label" for="objVendaAvulsa.data_vencimento">Vencimento *</label>
              <calendar :element-id="'objVendaAvulsa.data_vencimento'" v-model="objVendaAvulsa.data_vencimento" />
              <div v-if="!isValid && !objVendaAvulsa.data_vencimento" class="multiselect-invalid">
                Selecione uma data
              </div>
            </b-col>

            <b-col md="2">
              <label class="col-form-label" for="objVendaAvulsa.dias_subsequentes">Dias subsequentes {{ objVendaAvulsa.numero_parcelas > 1 ? '*' : '' }}</label>
              <g-select
                id="objVendaAvulsa.dias_subsequentes"
                :value="objVendaAvulsa.dias_subsequentes"
                :select="setDiasSubsequentes"
                :options="listaDiasSubsequentes"
                :class="!isValid && objVendaAvulsa.numero_parcelas > 1 && (!objVendaAvulsa.dias_subsequentes || !objVendaAvulsa.dias_subsequentes.id) ? 'invalid-input' : 'valid-input'"
                label="descricao"
                class="multiselect-truncate" />
              <div v-if="!isValid && objVendaAvulsa.numero_parcelas > 1 && (!objVendaAvulsa.dias_subsequentes || !objVendaAvulsa.dias_subsequentes.id)" class="multiselect-invalid">
                Selecione uma opção
              </div>
            </b-col>
          </b-row>
        </div>

        <titulos-conta-receber :esta-valido="isValid" :btn-imprimir-boletos="false" @gerar-parcelas="prepararParametros" />

        <div class="form-group pt-2">
          <b-btn :disabled="!listaVendaAvulsa.length || !titulosReceber.length || salvando" type="button" class="btn btn-verde" variant="verde" @click="pagamentoVendaAvulsa()">{{ salvando ? 'Salvando...' : 'Concluir' }}</b-btn>
          <b-btn variant="link" @click="cancelarVendaAvulsa()">Cancelar</b-btn>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import TitulosContaReceber from './TitulosContaReceber'
import {stringToISODate, dateToString} from '../../utils/date'

export default {
  components: { TitulosContaReceber },
  data () {
    return {
      isValid: true,
      salvando: false,
      vendedor_funcionario: null,
      cliente: null,
      listaVendaAvulsa: [],
      codigo_descricao: '',
      quantidade: 1,

      item: null,
      produto: null,
      estoque: 0,
      item_entregue: true,
      valor_unitario: 0,
      valor_total: 0,
      id_produto: null,
      subtotal_pagar: 0,

      objVendaAvulsa: {
        forma_cobranca: null,
        numero_parcelas: 1,
        data_vencimento: dateToString(new Date()),
        dias_subsequentes: null,
        valor_total: 0
      }
    }
  },

  computed: {
    ...mapState('formaPagamento', {listaFormasPagamento: 'lista'}),
    ...mapState('diasSubsequentes', ['listaDiasDaFranqueada']),
    ...mapState('funcionario', {listaFuncionarios: 'lista'}),
    ...mapState('contrato', ['titulosReceber']),

    listaDiasSubsequentes: {
      get () {
        return [{descricao: 'Selecione', id: null}].concat(this.listaDiasDaFranqueada)
      }
    }
  },

  mounted () {
    this.objVendaAvulsa.data_vencimento = dateToString(new Date())
    this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('funcionario/listar')
    this.$store.commit('contrato/SET_VALOR_TOTAL_ITENS', 0)
    this.$store.commit('contrato/SET_TITULOS_RECEBER', [])
    this.$store.commit('contrato/SET_VALOR_TOTAL_ITENS', 0)
    this.$store.commit('contrato/SET_VALOR_TOTAL_PARCELAS', 0)

    EventBus.$on('venda-avulsa-selecionar-pessoa', (pessoa) => {
      this.setCliente(pessoa)
    })
  },

  validations: {
    codigo_descricao: {required},
    quantidade: {required}
  },

  methods: {
    prepararParametros (callback) {
      this.isValid = false
      callback(this.objVendaAvulsa)
    },

    limparVendaItem () {
      this.setCodesc(null)
      this.$refs.codigo_descricao.reset()
    },

    setCliente (value) {
      this.cliente = value
    },

    setCodesc (value) {
      this.codigo_descricao = value
      this.quantidade = 1
      this.item_entregue = true

      if (value) {
        this.id_produto = value.id

        this.produto = value.descricao
        this.item = value
        this.valor_unitario = value.itemFranqueadas[0].valor_venda * 1

        let remover = 0
        if (this.listaVendaAvulsa.length) {
          this.listaVendaAvulsa.map(item => {
            if (item.id_produto === value.id) {
              remover += item.quantidade
            }
          })
        }

        this.estoque = (value.itemFranqueadas[0].saldo_estoque * 1) - remover
      } else {
        this.id_produto = null
        this.estoque = 0
        this.produto = null
        this.item = null
        this.valor_unitario = 0
      }
    },

    calcularValor (value) {
      console.log('QUANTIDADE', value)
    },

    mountItemDescription () {
      this.objVendaAvulsa.observacao = this.listaVendaAvulsa.map(item => item.produto).join(', ')
    },

    incluirVendaAvulsa () {
      const item = {
        id_produto: this.id_produto,
        item: this.id_produto,
        plano_conta: this.item.plano_conta ? this.item.plano_conta.id : null,
        produto: this.produto,
        estoque: this.estoque,
        quantidade: this.quantidade,
        valor_unitario: this.valor_unitario,
        valor: this.valor_total,
        valor_total: this.valor_total,
        numero_sequencia: this.listaVendaAvulsa.length + 1,
        item_entregue: this.item_entregue
      }

      this.listaVendaAvulsa.push(item)

      this.subtotal_pagar += item.valor_total
      this.objVendaAvulsa.valor_total += item.valor_total
      this.mountItemDescription()

      this.limparVendaItem()
    },

    excluirVendaItem (index, item) {
      this.listaVendaAvulsa.splice(index, 1)
      this.subtotal_pagar = this.subtotal_pagar - item.valor_total
      this.objVendaAvulsa.valor_total = this.objVendaAvulsa.valor_total - item.valor_total
      this.mountItemDescription()
    },

    cancelarVendaAvulsa () {
      this.isValid = true
      this.listaVendaAvulsa = []
      this.subtotal_pagar = 0
      this.setCliente(null)
      this.vendedor_funcionario = null
      this.limparVendaItem()
      this.mountItemDescription()
      this.$store.commit('contrato/SET_VALOR_TOTAL_ITENS', 0)
      this.$store.commit('contrato/SET_TITULOS_RECEBER', [])
      this.$store.commit('contrato/SET_VALOR_TOTAL_ITENS', 0)
      this.$store.commit('contrato/SET_VALOR_TOTAL_PARCELAS', 0)

      this.objVendaAvulsa.forma_cobranca = null
      this.objVendaAvulsa.numero_parcelas = 1
      this.objVendaAvulsa.data_vencimento = dateToString(new Date())
      this.objVendaAvulsa.dias_subsequentes = null
      this.objVendaAvulsa.valor_total = 0

      this.$emit('modal-venda-avulsa-fechar')
    },

    pagamentoVendaAvulsa () {
      this.salvando = true
      this.isValid = false

      const titulos = this.$store.state.contrato.titulosReceber.map(item => {
        const titulo = {...item}
        if (titulo.cheque) {
          titulo.cheque = {...titulo.cheque}
          titulo.cheque.valor = titulo.valor
        }

        if (titulo.transacao_cartao) {
          titulo.transacao_cartao = {...titulo.transacao_cartao}

          if (titulo.transacao_cartao.operadora_cartao) {
            titulo.transacao_cartao.operadora_cartao = titulo.transacao_cartao.operadora_cartao.id
          }

          if (titulo.transacao_cartao.parcelamento_operadora_cartao) {
            titulo.transacao_cartao.parcelamento_operadora_cartao = titulo.transacao_cartao.parcelamento_operadora_cartao.id
          }

          titulo.transacao_cartao.valor_liquido = titulo.valor
          titulo.transacao_cartao.data_pagamento = stringToISODate(titulo.data_vencimento, true)
        }

        return titulo
      })

      const data = {
        vendedor_funcionario: this.vendedor_funcionario,
        cliente: this.cliente,
        itens_conta_receber: this.listaVendaAvulsa,
        titulos_receber: titulos,
        venda_avulsa: this.objVendaAvulsa
      }

      this.$store.dispatch('contasReceber/criarVendaAvulsa', data)
        .then(() => {
          this.cancelarVendaAvulsa()
        })
        .finally(() => {
          this.salvando = false
        })
    },

    filtroParcelas (item) {
      let lista = []
      if (item.item === null) {
        return lista
      }

      lista = ['valor_venda', 'valor_venda_2', 'valor_venda_3', 'valor_venda_4', 'valor_venda_5', 'valor_venda_6'].map(i => {
        return item.item[i] && item.item[i] * 1 ? (i === 'valor_venda' ? 1 : i.replace(/.*(\d)/, '$1') * 1) : undefined
      })

      return lista.filter(i => i !== undefined)
    },

    setFormaCobranca (value) {
      this.objVendaAvulsa.forma_cobranca = value
    },

    setDiasSubsequentes (value) {
      this.objVendaAvulsa.dias_subsequentes = value
    }
  }
}
</script>

<style lang="scss">
/* Table Card */
.table-card, .cheader, .cbody ul, .cfooter, .cbase {
  display: flex;
}

.cheader {
  color: #4a4a4a;
  background-color: #fff;
}

.cheader div,
.cbody li,
.cfooter div,
.cbase div {
  flex: 1 1 0;
  padding: .75rem;
}

.cbody {
  overflow-y: overlay;
  height: calc(100vh - 250px);
  height: -webkit-calc(100vh - 250px);
  height: -moz-calc(100vh - 250px);
  color: #4A4A4A;
}
.cbody ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  background-color: #F8F9FA;
  box-shadow: inset 0px 3px 0px 0px rgba(74, 74, 74, 0.05);
}
.cbody li {
  display: grid;
  position: relative;
}
.cbody ul li:first-child {
  text-align: center;
  box-shadow: inset 0px 3px 0px 0px rgba(74, 74, 74, 0.05);
}
.cbody ul li:first-child div {
  font-size: medium;
  display: flex;
  align-self: end;
  margin: 0 auto;
  padding-bottom: 0.4rem;
}

.table-card {
  width: 100%;
  max-width: 100%;
  background-color: #EBECF0;
  flex-direction: column;
  height: calc(100vh - 310px);
  height: -webkit-calc(100vh - 310px);
  height: -moz-calc(100vh - 310px);
}
.table-card thead {
  background-color: #F8F9FA;
}
.table-card tbody td:last-child {
  background-color: #F3F3F3;
  border-color: #F3F3F3;
}

.cfooter {
  background-color: #E0E0E0;
  font-size: large;
  margin-bottom: 1rem;
}
.cbody input {
  display: block;
  color: #3e515b;
  background-clip: padding-box;
  border: 0;
  border-radius: 0;
  width: 100%;
  padding: 0;
  line-height: 1;
  /* background-color: transparent; */
  transition: all .2s;
  font-size: large;
}
/* .cbody label:after {
  font-family: 'FontAwesome', 'Comfortaa' ;
  content: '\f14b';
  display: inline-block;
  padding-right: 3px;
  vertical-align: middle;
  font-size: x-large;
} */
.cbody input:focus {
  padding-left: 0.5rem;
  background-color: #E9E9E9;
}
.cbody ul li:last-child {
  color: #151B1E;
  font-size: large;
  align-items: flex-end;
  padding-bottom: 0.4rem;
}
.cfooter div,
.cbase div {
  display: grid;
  text-align: right;
  align-items: center;
  padding: 0.2rem 0.75rem;
}
.cfooter div:last-child {
  border-top: 1px dashed #C4C4C4;
  background-color: #E9E9E9;
  color: #3e515b;
  text-align: left;
}
.cbody ul li:last-child {
  background-color: #EBECF0;
}

.cheader div:last-child,
.cbody ul li:last-child,
.cfooter div:last-child,
.cbase div:last-child {
  padding-right: 1rem;
}

.cbase {
  font-size: large;
  margin-bottom: 1rem;
}
.cbase div:nth-child(4) {
  text-align: left;
}

.datepicker {
  padding: 0;
}

.floating-message {
  position: absolute;
  z-index: 1;
  margin-top: 4px;
  padding: 3px 5px;
  font-size: 0.7rem;
  width: 145px;
}
.floating-message::before {
  content: '';
  position: absolute;
  top: -16px;
  border: 8px solid #FF3860;
  border-top-color: transparent;
  border-left-color: transparent;
  border-right-color: transparent;
}

.cbody ul li:last-child .floating-message {
  margin-top: 74px;
  left: 12px;
}

.mensagem-info {
  padding: 0.75rem 1.25rem;
}
</style>
