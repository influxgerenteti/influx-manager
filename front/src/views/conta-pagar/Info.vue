<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div v-if="itemSelecionado && itemSelecionado.id" class="body-sector info-view">
      <div class="row p-3">
        <div class="col-md-12">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-2">Destino</label>
              <span class="col-md-10">
                {{ itemSelecionado.fornecedor_pessoa.razao_social || itemSelecionado.fornecedor_pessoa.nome_contato }} -
                <template v-if="itemSelecionado.fornecedor_pessoa.tipo_pessoa === 'F'">
                  {{ itemSelecionado.fornecedor_pessoa.cnpj_cpf | formatarCPF }}
                </template>
                <template v-else>
                  {{ itemSelecionado.fornecedor_pessoa.cnpj_cpf | formatarCNPJ }}
                </template>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="content-sector sector-roxo-c collapse-toggle">
        <div id="secao-cobranca" class="collapse-toggle" @click="isOpenCobranca=!isOpenCobranca">
          <div v-b-toggle.cobranca-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Cobrança
            <div :class="isOpenCobranca ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="cobranca-toggle" class="col-md-12" visible>
          <div class="row p-3">
            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Forma de cobrança</label>
                  <span class="col-md-8">{{ itemSelecionado.forma_cobranca ? itemSelecionado.forma_cobranca.descricao : '--' }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Conta</label>
                  <span class="col-md-8">{{ itemSelecionado.conta ? itemSelecionado.conta.descricao : '--' }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Data de vencimento</label>
                  <span class="col-md-8">{{ itemSelecionado.data_vencimento ? itemSelecionado.data_vencimento : '--' }}</span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label for="valor_parcela" class="col-form-label col-md-4">Valor da Parcela</label>
                  <span class="col-md-8">{{ itemSelecionado.valor_parcela | formatarNumero }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Número de Parcelas</label>
                  <span class="col-md-8">{{ itemSelecionado.numero_parcelas }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4"><b>Valor Total</b></label>
                  <span class="col-md-8">{{ itemSelecionado.valor_total | formatarNumero }}</span>
                </div>
              </div>
            </div>
          </div>
        </b-collapse>
      </div>

      <div class="content-sector sector-azul collapse-toggle">
        <div id="secao-plano-contas" class="collapse-toggle" @click="isOpenPlano=!isOpenPlano">
          <div v-b-toggle.plano-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Plano de Contas
            <div :class="isOpenPlano ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="plano-toggle" class="col-md-12" visible>
          <b-row v-for="(item, index) in itemSelecionado.plano_conta" :key="index" class="p-3">
            <b-col md="4">
              <label :for="`plano_conta[${index}][categoria]`" class="col-form-label">Categoria</label>
              <span v-if="item && item.plano_conta" class="d-block">{{ item.plano_conta.descricao }}</span>
            </b-col>

            <b-col md="4">
              <label :for="`plano_conta[${index}][complemento]`" class="col-form-label">Complemento</label>
              <span class="d-block">{{ item.complemento }}</span>
            </b-col>

            <b-col md="4">
              <label :for="`plano_conta[${index}][valor]`" class="col-form-label">Valor</label>
              <span class="d-block">{{ item.valor | formatarNumero }}</span>
            </b-col>
          </b-row>
        </b-collapse>
      </div>

      <div class="separator">
        <div v-for="(parcela, index) in itemSelecionado.parcelas" :key="index" class="p-3 list-separator">
          <b-row class="align-items-center">
            <b-col md="1" class="text-center">
              <label class="col-form-label">#</label>
              <span class="d-block"><b>{{ parcela.numero_parcela_documento }}</b></span>
            </b-col>

            <b-col md="3">
              <label :for="`parcela[${index}][forma_cobranca]`" class="col-form-label">Forma de Cobrança</label>
              <span v-if="parcela" class="d-block">{{ parcela.forma_cobranca.descricao }}</span>
            </b-col>

            <b-col md="2">
              <label for="parcela.data_vencimento" class="col-form-label">Vencimento</label>
              <span class="d-block">{{ parcela.data_vencimento }}</span>
            </b-col>

            <b-col md="2">
              <template v-if="parcela.movimento_conta && parcela.movimento_conta.length && parcela.movimento_conta[parcela.movimento_conta.length - 1].data_movimento">
                <label for="parcela.data_pagamento" class="col-form-label">Último pagam.</label>
                <span class="d-block badge date-payment align-middle rounded">{{ parcela.movimento_conta[parcela.movimento_conta.length - 1].data_movimento | formatarData }}</span>
              </template>
            </b-col>

            <b-col md="3">
              <label :for="`parcela[${index}][valor]`" class="col-form-label">Valor Parcela</label>
              <span class="d-block">{{ parcela.valor_documento | formatarNumero }}</span>
            </b-col>

            <b-col v-if="parcela.situacao" md="1" class="text-center">
              <span :class="`circle-badge-${parcela.situacao.toLowerCase()}`" :title="situacoes[parcela.situacao]" class="circle-badge"></span>
              <a v-if="parcela.movimento_conta && parcela.movimento_conta.length" :title="parcela.verDetalhes ? 'Esconder detalhes' : 'Ver detalhes'" href="javascript:void(0)" class="icone-link" @click="parcela.verDetalhes = !parcela.verDetalhes">
                <font-awesome-icon :icon="parcela.verDetalhes ? 'minus' : 'plus'" />
              </a>
            </b-col>
          </b-row>

          <template v-if="parcela.verDetalhes">
            <b-row v-for="movimento in parcela.movimento_conta" :key="`${parcela.id}!!${movimento.id}`" class="linha-movimento-conta">
              <b-col md="1"/>

              <b-col md="3">
                <label class="col-form-label">Forma de Pagamento</label>
                <span class="d-block">{{ movimento.forma_pagamento.descricao }}</span>
              </b-col>

              <b-col md="2">
                <label class="col-form-label">Pagamento</label>
                <span class="d-block">{{ movimento.data_movimento | formatarData }}</span>
              </b-col>

              <b-col md="3" style="border-left: 1px dashed #eee;">
                <div class="d-flex justify-content-between">
                  <label class="col-form-label">Valor Pago:</label>
                  <span class="pt-2">{{ movimento.valor_lancamento | formatarNumero }}</span>
                </div>

                <div class="d-flex justify-content-between">
                  <label class="col-form-label">Diferença:</label>
                  <span class="pt-2">{{ movimento.valor_diferenca_baixa | formatarNumero }}</span>
                </div>
              </b-col>

              <b-col md="3">
                <div class="d-flex justify-content-between">
                  <label class="col-form-label">Juros:</label>
                  <span class="pt-2">{{ movimento.valor_juros | formatarNumero }}</span>
                </div>

                <div class="d-flex justify-content-between">
                  <label class="col-form-label">Desconto:</label>
                  <span class="pt-2">{{ movimento.valor_desconto | formatarNumero }}</span>
                </div>
              </b-col>
            </b-row>
          </template>
        </div>
      </div>

      <div class="p-3">
        <b-row class="align-items-center">
          <b-col md="8"/>

          <b-col md="4">
            <label class="col-form-label d-block">Total das parcelas</label>
            <big>{{ itemSelecionado.valor_total | formatarMoeda }}</big>
          </b-col>
        </b-row>
      </div>

      <div class="p-3">
        <div class="info-row col-md-12">
          <label class="col-form-label col-md-2">Observação</label>
          <span class="col-md-10">{{ itemSelecionado.observacao }}</span>
        </div>
      </div>
    </div>

    <div class="mt-3">
      <b-btn variant="roxo" @click="editar()"><font-awesome-icon icon="pen" /> Atualizar</b-btn>
      <b-btn variant="link" @click="voltar()">Voltar</b-btn>
    </div>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
export default {
  props: {
    id: {
      type: Number,
      required: false,
      default: null
    },

    isModal: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      mostrar_mais: false,
      isOpenCobranca: true,
      isOpenPlano: true,
      situacoes: {
        'PEN': 'Pendente',
        'LIQ': 'Liquidado',
        'CAN': 'Cancelado',
        'BAI': 'Baixado',
        'SUB': 'Substituído'
      }
    }
  },

  computed: mapState('contaPagar', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

  watch: {
    id () {
      this.init()
    }
  },

  mounted () {
    this.init()
  },

  methods: {
    ...mapMutations('contaPagar', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('contaPagar', ['buscar']),

    init () {
      if (this.id) {
        this.SET_ITEM_SELECIONADO_ID(this.id)
        this.buscar()
      }
    },

    voltar () {
      this.$emit('cancel')
    },

    editar () {
      this.$emit('resolve', this.id)
    }
  }
}
</script>

<style scoped>
.table td {
  vertical-align: middle;
}

.item-second-row td {
  border-top: 0;
}

.col-min {
  padding-left: 6px;
  padding-right: 6px;
}

.col-1-2 {
  flex: 0 0 12%;
  max-width: 12%;
}

.col-form-label + .d-block {
  margin-bottom: calc(0.375rem + 1px);
}

.linha-movimento-conta {
  background-color: #fafafa;
}

.linha-movimento-conta + .linha-movimento-conta {
  border-top: 1px dashed #eee;
}
</style>
