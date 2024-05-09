<template>
    <b-modal id="modalTituloReceberBoletos" ref="modalTituloReceberBoletos" v-model="visible" size="xl" title="Detalhes do título" no-close-on-backdrop hide-footer>
      <main v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </main>
      <main v-if="!estaCarregando && Object.keys(tituloDetalhes).length > 0">
        <section><div >
            <span class="font-lg">Responsavel/Aluno</span>
          </div>
          <div class="main-header">
            <h3>{{ buscaResponsavelAluno()}}</h3>
            <div>
              <!-- <h4>Valor Original: R$ {{ numberToCurrency(tituloDetalhes.valor_original) }}</h4>
              <span >Valor Saldo: R$ {{ numberToCurrency(tituloDetalhes.valor_saldo_devedor) }}</span> -->

              <!-- <span v-if="tituloDetalhes.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge">ABERTO</span>
              <span v-if="tituloDetalhes.situacao === 'LIQ' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge">RECEBIDO</span>
              <span v-if="tituloDetalhes.situacao === 'CAN' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge">CANCELADO</span>
              <span v-if="tituloDetalhes.situacao === 'SUB' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge">CANCELADO</span> -->
            </div>
          </div>
          <hr>
          <div class="main-header">
            <div class="flex-grow-1 text-center">
              <span>Vencimento: </span>
              <p class="font-lg">{{ formatarData(tituloDetalhes.data_vencimento) }}</p>
            </div>
            <div class="flex-grow-1 text-center" v-if="verificaTituloSituacao()">
              <button class="btn btn-info" @click="imprimirRecibos()">
                <i class="fa fa-print mr-2"></i>
                <span>Imprimir Recibo</span>
              </button>
            </div>
          </div>

          <div v-if="tituloDetalhes.observacao">
            <span class="font-lg">Observação: </span>
            <span class="text-center">{{ tituloDetalhes.observacao }}</span>
          </div>
          <br>
          <g-table class="table-area table-bordered">
            <thead>
              <tr>
                <td class="w-100 text-center"> 
                  <span class="font-lg">
                    Valores
                  </span>
                </td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span>Valor sem desconto</span></td>
                <td class="w-100"><span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(outrosDetalhes.valor_parcela_sem_desconto) || '0.00' }}</span></td>
              </tr>
              <tr>
                <td><span>Desconto antecipação</span></td>
                <td class="w-100"><span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(outrosDetalhes.desconto_antecipacao) || '0.00' }}</span></td>
              </tr>
              <tr>
                <td><span>Valor com desconto</span></td>
                <td class="w-100"><span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(outrosDetalhes.valor_original) || '0.00' }}</span></td>
              </tr>


              <tr>
                <td><span>Saldo (considera apenas valores movimentados)</span></td>
                <td class="w-100"><span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(outrosDetalhes.valor_saldo_devedor) || '0.00' }}</span></td>
              </tr>
            </tbody>
          </g-table>
        </section>
        <br>
        <section v-if="tituloDetalhes.boletos.length > 0">
          <h3>Boletos</h3>
          <g-table class="table-area table-bordered table-checkbox">
            <thead>
              <tr>
                <th><span>
                  Selec.
                </span></th>
                <th><span class="d-block m-auto">
                  Situação
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Valor
                </span></th>
                <th><span class="d-block m-auto">
                  Vencimento
                </span></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="boleto in tituloDetalhes.boletos" :key="boleto.id" class="pointer" @click="selecionarBoleto(boleto)">
                <td class="d-flex justify-content-center">
                  <input type="checkbox" :name="'checkbox-'+boleto.id" :id="'checkbox-'+boleto.id">
                </td>
                <td>
                  <span v-if="boleto.situacao_cobranca === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                  <span v-if="boleto.situacao_cobranca === 'REC' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                  <span v-if="boleto.situacao_cobranca === 'ENV' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                  <span v-if="boleto.situacao_cobranca === 'CAN' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                </td>
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(boleto.valor) }}</span>
                </td>
                <td>
                  <span class="d-block m-auto">{{ formatarData(boleto.data_vencimento)}}</span>
                </td>
              </tr>
            </tbody>
          </g-table>
          <br>
          <button class="btn btn-primary" @click="imprimirBoletos()">Imprimir Boletos</button>
        </section>
        <section v-if="tituloDetalhes.transacoes_cartao.length > 0">
          <h3>Transações de Cartões</h3>
          <g-table class="table-area table-bordered">
            <thead>
              <tr>
                <th><span class="d-block mr-auto">
                  Situação
                </span></th>
                <th><span class="d-block mr-auto">
                  Tipo
                </span></th>
                <th><span class="d-block ml-0 mr-auto">
                  Identificador
                </span></th>
                <th><span class="d-block m-auto">
                  Data
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Taxa
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Desconto
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Valor
                </span></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transacao in tituloDetalhes.transacoes_cartao" :key="transacao.id">
                <td>
                  <span v-if="transacao.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                  <span v-if="transacao.situacao === 'CRE' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                  <span v-if="transacao.situacao === 'EST' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                  <span v-if="transacao.situacao === 'EXC' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                </td>
                <td>
                  <span v-if="transacao.tipo_transacao === 'C'" class="d-block mr-auto">Crédito</span>
                  <span v-if="transacao.tipo_transacao === 'D'" class="d-block mr-auto">Débito</span>
                </td>
                <td>
                  <span class="d-block mr-auto ml-0">{{ transacao.identificador }}</span>
                </td>
                <td>
                  <span class="d-block m-auto">{{ formatarData(transacao.data_pagamento)}}</span>
                </td>
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(transacao.taxa)}}</span>
                </td>
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(transacao.valor_desconto)}}</span>
                </td>
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(transacao.valor_liquido)}}</span>
                </td>
              </tr>
            </tbody>
          </g-table>
        </section>
        <!-- <section v-if="tituloMovimentos.length > 0">
          <h3>Movimentações</h3>
          <g-table class="table-area table-bordered">
            <thead>
              <tr>
                <th><span class="d-block mr-auto">
                  Situação
                </span></th>
                <th><span class="d-block mr-auto">
                  Tipo
                </span></th>
                <th><span class="d-block ml-0 mr-auto">
                  Conta
                </span></th>
                <th><span class="d-block m-auto">
                  Data
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Valor Título
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Valor Lançamento
                </span></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="movimento in tituloMovimentos" :key="movimento.id">
                <td>
                  <span v-if="movimento.estornado == 1" v-b-tooltip.viewport.left.hover :class="`badge-black`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                  <span v-if="movimento.estornado == 0 && movimento.conciliado != 'S'" v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">PENDENTE</span>
                  <span v-if="movimento.estornado == 0 && movimento.conciliado === 'S' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">CONCILIADO</span>
                </td>
                <td>
                  <span v-if="movimento.operacao === 'C'" class="d-block mr-auto">Crédito</span>
                  <span v-if="movimento.operacao === 'D'" class="d-block mr-auto">Débito</span>
                </td>
                <td>
                  <span class="d-block mr-auto ml-0">{{ movimento.conta }}</span>
                </td>
                <td>
                  <span class="d-block m-auto">{{ formatarData(movimento.data_contabil)}}</span>
                </td>
               
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor_titulo)}}</span>
                </td>
                <td>
                  <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor_lancamento)}}</span>
                </td>
              </tr>
            </tbody>
          </g-table>

        </section> -->
        <section v-if="tituloPagamentos.length > 0">
          <h3>Movimentações</h3>
          <g-table class="table-area table-bordered">
            <thead>
              <tr>
                <th><span class="d-block ml-0 mr-auto">
                  Tipo
                </span></th>
                <th><span class="d-block m-auto">
                  Situação
                </span></th>
                <th><span class="d-block mr-auto">
                  Operação
                </span></th>
                <th><span class="d-block ml-0 mr-auto">
                  Conta
                </span></th>
                <th><span class="d-block m-auto">
                  Data
                </span></th>
                <th><span class="d-block mr-0 ml-auto">
                  Valor
                </span></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="movimento in tituloPagamentos" :key="movimento.id">
                <template v-if="movimento.tipo === 'TR'">
                  <td>
                    <span class="d-block mr-auto">Transferência</span>
                  </td>
                  <td>
                    <span v-if="movimento.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                    <span v-if="movimento.situacao === 'CRE' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                    <span v-if="movimento.situacao === 'EST' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                    <span v-if="movimento.situacao === 'EXC' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                  </td>
                  <td>
                    <span v-if="movimento.operacao === 'C'" class="d-block mr-auto">Crédito</span>
                    <span v-if="movimento.operacao === 'D'" class="d-block mr-auto">Débito</span>
                  </td>
                  <td>
                    <span class="d-block mr-auto ml-0">{{ movimento.conta }}</span>
                  </td>
                  <td>
                    <span class="d-block m-auto">{{ formatarData(movimento.data_contabil)}}</span>
                  </td>
                  <td>
                    <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor)}}</span>
                  </td>
                </template>
                <template v-else-if="movimento.tipo === 'BOLETO'">
                  <td>
                    <span class="d-block mr-auto">Boleto</span>
                  </td>
                  <td>
                    <span v-if="movimento.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                    <span v-if="movimento.situacao === 'CRE' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                    <span v-if="movimento.situacao === 'REC' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                    <span v-if="movimento.situacao === 'EST' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                    <span v-if="movimento.situacao === 'EXC' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                  </td>
                  <td>
                    <span v-if="movimento.operacao === 'C'" class="d-block mr-auto">Crédito</span>
                    <span v-if="movimento.operacao === 'D'" class="d-block mr-auto">Débito</span>
                  </td>
                  <td>
                    <span class="d-block mr-auto ml-0">{{ movimento.conta }}</span>
                  </td>
                  <td>
                    <span class="d-block m-auto">{{ formatarData(movimento.data_contabil)}}</span>
                  </td>
                  <td>
                    <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor)}}</span>
                  </td>
                </template>
                <template v-else-if="movimento.tipo === 'CHEQUE'">
                  <td>
                    <span class="d-block mr-auto">Cheque</span>
                  </td>
                  <td>
                    <span v-if="movimento.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                    <span v-if="movimento.situacao === 'CRE' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                    <span v-if="movimento.situacao === 'EST' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                    <span v-if="movimento.situacao === 'EXC' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                  </td>
                  <td>
                    <span v-if="movimento.operacao === 'C'" class="d-block mr-auto">Crédito</span>
                    <span v-if="movimento.operacao === 'D'" class="d-block mr-auto">Débito</span>
                  </td>
                  <td>
                    <span class="d-block mr-auto ml-0">{{ movimento.conta }}</span>
                  </td>
                  <td>
                    <span class="d-block m-auto">{{ formatarData(movimento.data_contabil)}}</span>
                  </td>
                  <td>
                    <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor)}}</span>
                  </td>
                </template>
                <template v-else-if="movimento.tipo === 'CARTAO'">
                  <td>
                    <span class="d-block mr-auto">Cartão</span>
                  </td>
                  <td>
                    <span v-if="movimento.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ABERTO</span>
                    <span v-if="movimento.situacao === 'CRE' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge d-block m-auto">RECEBIDO</span>
                    <span v-if="movimento.situacao === 'EST' " v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge d-block m-auto">ESTORNADO</span>
                    <span v-if="movimento.situacao === 'EXC' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="''" class="situacao-badge d-block m-auto">CANCELADO</span>
                  </td>
                  <td>
                    <span v-if="movimento.operacao === 'C'" class="d-block mr-auto">Crédito</span>
                    <span v-if="movimento.operacao === 'D'" class="d-block mr-auto">Débito</span>
                  </td>
                  <td>
                    <span class="d-block mr-auto ml-0">{{ movimento.conta }}</span>
                  </td>
                  <td>
                    <span class="d-block m-auto">{{ formatarData(movimento.data_contabil)}}</span>
                  </td>
                  <td>
                    <span class="d-block mr-0 ml-auto">R$ {{ numberToCurrency(movimento.valor)}}</span>
                  </td>
                </template>
              </tr>
            </tbody>
          </g-table>

        </section>
      </main>
      <main v-if="!estaCarregando && !tituloDetalhes">
        <p>Não foi possível encontrar os detalhes do título.</p>
      </main>
    </b-modal>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {converterDataDoBancoParaString} from '../../utils/date'
import { numberToCurrency } from '@/utils/number'

export default {
  name: 'ModalTituloReceberBoletos',
  props: {
  },

  data () {
    return {
      visible: false,
      outrosDetalhes: {},
      boletosImpressao: []
    }
  },
  computed: {
    ...mapState('salaFranqueada', {listaSalasFranqueada: 'lista'}),
    ...mapState('tituloReceber', ['estaCarregando', 'tituloDetalhes', 'tituloMovimentos', 'tituloPagamentos']),
  },
  validations: {
  },
  mounted() {
  },
  watch: {
  },
  methods: {
    ...mapActions('tituloReceber', ['consultarDetalhesTitulo','consultarMovimentosTitulo']),
    ...mapActions('agendamentoPersonal', {
      getAgendamentos: 'buscarAgendamentos',
      setFiltros: 'setFiltrosAgenda'
    }),

    carregarDetalhes(tituloId) {
      this.consultarDetalhesTitulo(tituloId)
      this.consultarMovimentosTitulo(tituloId)
    },
    buscaResponsavelAluno(){
     if(this.tituloDetalhes.aluno && this.tituloDetalhes.sacado_pessoa.nome_contato !== this.tituloDetalhes.aluno.pessoa.nome_contato) {
        return this.tituloDetalhes.sacado_pessoa.nome_contato + ' / ' + this.tituloDetalhes.aluno.pessoa.nome_contato   
      }
      return this.tituloDetalhes.sacado_pessoa.nome_contato
    },
    verificaTituloSituacao() {
      if(this.tituloDetalhes.situacao === 'LIQ') {
        return true
      }
      if(this.tituloDetalhes.situacao === 'PEN') {
        if((this.tituloDetalhes.forma_recebimento.forma_cartao === true) || 
        (this.tituloDetalhes.forma_recebimento.forma_cheque === true) ||
        (this.tituloDetalhes.forma_recebimento.forma_cartao_debito === true) ||
              (this.tituloDetalhes.forma_recebimento.forma_transferencia === true) ) {
          return true
        }
      }
      return false
    },
    formatarData(data) {
      if(!data || (typeof(data) !== "string")){
        return ''
      }
      return converterDataDoBancoParaString(data)
    },

    numberToCurrency(number) {
      if(!number) {
        return ''
      }
      return numberToCurrency(number)
    },

    selecionarBoleto(boleto){
      let added = false
      Object.values(this.boletosImpressao).forEach(el => {
        if(el.id == boleto.id) {
          added = true
          return
        }
      })
      if(added) {
        this.boletosImpressao = Object.values(this.boletosImpressao).filter(el => el.id !== boleto.id).map(bol => bol)
      } else {
        this.boletosImpressao.push(boleto)
      }
      this.atualizarCheckboxes()
    },

    atualizarCheckboxes() {
      var elements = document.querySelectorAll('[id^=checkbox-]');
      elements.forEach(check => {
        let id = check.id.replace('checkbox-','')
        check.checked = false
        Object.values(this.boletosImpressao).forEach(bol => {
          if(bol.id == id) {
            check.checked = true
          }
        })
      })
    },

    imprimirBoletos () {
      let boletos = this.boletosImpressao.map(bol => bol.id)
  
      if (boletos.length > 0) {
        const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
        const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
        const rota = this.$route.matched[0].path
        const url = `/api/boleto/imprimir-boletos?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}&boletos[]=${[boletos.join('&boletos[]=')].join('')}`
        // open(url, '_blank')
        var host = process.env.VUE_APP_HOST;
        window.open(`${host}${url}`, '_blank')
      }
    },

    imprimirRecibos () {
      let tituloId = this.tituloDetalhes.id

      let data = {
        name: 'Dashboard',
        query: {
          franqueada: this.$store.state.root.usuarioLogado.franqueadaSelecionada,
          usuario: this.$store.state.root.usuarioLogado.id,          
          Authorization : this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso,
          URLModulo:this.$route.matched[0].path
        }
      }
      let routeData = this.$router.resolve(data)
      routeData.href = routeData.href.replace('/dashboard', '/api/recibo/imprimir').replace('/financeiro', '')
      
      routeData.href += `&titulos[0]=${tituloId}`
      const url = routeData.href
        // open(url, '_blank')
        var host = process.env.VUE_APP_HOST;
        window.open(`${host}${url}`, '_blank')      
    },

    closeModal() {
      this.visible = false
    }
  }
}
</script>
<style scoped>

.situacao-badge{
  margin-left: auto !important;
}
.table-area{
  height: min-content;
}
.pointer{
  cursor:pointer;
}
.main-header{
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}
.form-loading {
  position: relative;
}
section {
  margin: 20px 0;
}
</style>
