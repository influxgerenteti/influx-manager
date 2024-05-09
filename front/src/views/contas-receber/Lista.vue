<template>
  <div class="animated fadeIn fill-width">
    <filtros :situacoes="situacoes" :troca-classe-tabela="trocaClasseTabela" :pessoas-buscar="pessoasBuscar" @filtrar="filtrar()" />

    <div class="table-responsive-sm table-container">
      <g-table :class="[className, 'table-sm']" :sort="sortTable" class="selectAll">
        <thead class="text-dark" style="z-index: 1; position: sticky;">
          <tr>
            <th data-column="" class="coluna-checkbox">
              <b-form-checkbox :disabled="!lista.length" v-model="checkAll" :indeterminate="indeterminate" 
                class="m-0" aria-describedby="selected" aria-controls="selected" 
                @change="toggleAll"/>
            </th>
            <th class="collumn-size-4" style="cursor: pointer;" @click="ordenarListagem('id')">
              <template>
                <div style="margin: 2px 6px; display: flex; flex-direction: column; transform: scale(0.9);">
                  <font-awesome-icon v-if="(orderBy == 'id' && !orderDesc) || orderBy != 'id'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'id' && orderDesc) || orderBy != 'id'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Título
            </th>
            <th  class="collumn-size-8"  data-column="" style="cursor: pointer;" @click="ordenarListagem('cliente_nome')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'cliente_nome' && !orderDesc) || orderBy != 'cliente_nome'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'cliente_nome' && orderDesc) || orderBy != 'cliente_nome'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Aluno (Sacado)
            </th>
            <th class="collumn-size-5 data-column=" style="cursor: pointer;" @click="ordenarListagem('data_vencimento')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'data_vencimento' && !orderDesc) || orderBy != 'data_vencimento'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'data_vencimento' && orderDesc) || orderBy != 'data_vencimento'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Vencimento
            </th>
            <th class="collumn-size-4" >Situação</th>
            <th v-b-tooltip data-column="" title="Categoria" style="cursor: pointer;" @click="ordenarListagem('observacao')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'observacao' && !orderDesc) || orderBy != 'observacao'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'observacao' && orderDesc) || orderBy != 'observacao'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Categoria
            </th>
            <th data-column="" style="cursor: pointer;" @click="ordenarListagem('metodo')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'metodo' && !orderDesc) || orderBy != 'metodo'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'metodo' && orderDesc) || orderBy != 'metodo'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Método
            </th>
            <th data-column="" style="cursor: pointer;" @click="ordenarListagem('valor_original')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'valor_original' && !orderDesc) || orderBy != 'valor_original'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'valor_original' && orderDesc) || orderBy != 'valor_original'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Valor
            </th>
            <th data-column="" style="cursor: pointer;" @click="ordenarListagem('valor_saldo_devedor')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'valor_saldo_devedor' && !orderDesc) || orderBy != 'valor_saldo_devedor'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'valor_saldo_devedor' && orderDesc) || orderBy != 'valor_saldo_devedor'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Saldo
            </th>
            <!-- <th data-column="" style="cursor: pointer;" @click="ordenarListagem('valor_conciliado')">
              <template>
                <div style="margin: 2px 4px; display: flex; flex-direction: column; transform: scale(0.8);">
                  <font-awesome-icon v-if="(orderBy == 'valor_conciliado' && !orderDesc) || orderBy != 'valor_conciliado'" icon="caret-up" style="margin: -3px;"/>
                  <font-awesome-icon v-if="(orderBy == 'valor_conciliado' && orderDesc) || orderBy != 'valor_conciliado'" icon="caret-down" style="margin: -3px;"/>
                </div>
              </template>
              Conciliado
            </th> -->
            
            <th>Ver</th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-if="lista.length > 0 && !estaCarregando" v-for="item in lista" :key="item.id" :lista="list = lista" class="align-items-center" @click="checkRow($event, item)">
             
              <td data-label="Selecione" class="coluna-checkbox">
                <b-form-checkbox v-model="selected" :value="item" :id="`titulo-receber-${item.id}`" />
              </td> 
              <td class="collumn-size-4"  data-label="#">{{ item.id }}</td>
              <td class="collumn-size-8" data-label="Nome">
                <span  v-if="item.aluno_nome " v-b-tooltip.hover.bottom=" item.aluno_nome  + ', Responsável: ' + item.cliente_nome " >{{ item.aluno_nome  }}</span>
                <span   v-if="!item.aluno_nome"  v-b-tooltip.hover.bottom="'Responsável: ' + item.cliente_nome " >R: {{ item.cliente_nome  }}</span>
              </td>

              <td class="collumn-size-5" data-label="Vencimento">
                <template v-if="hoje < getDateFromISO(getDataVencimento(item))">
                  <span class="badge date-success align-middle rounded">{{ getDateFromISO(getDataVencimento(item)) | formatarData }}</span>
                </template>
                <template v-else-if="hoje === getDateFromISO(getDataVencimento(item))">
                  <span class="badge date-warning align-middle rounded">{{ getDateFromISO(getDataVencimento(item)) | formatarData }}</span>
                </template>
                <template v-else-if="hoje > getDateFromISO(getDataVencimento(item))">
                  <span class="badge date-danger align-middle rounded">{{getDateFromISO(getDataVencimento(item)) | formatarData }}</span>
                </template>
              </td>

              <td class="collumn-size-4" data-label="Situação">
                <span v-if="item.situacao === 'LIQ-PEN' && item.vencido == 0" v-b-tooltip.viewport.left.hover :class="`badge-blue`" :title="''" class="situacao-badge">ABERTO</span>
                <span v-if="item.situacao === 'REC' " v-b-tooltip.viewport.left.hover :class="`badge-blue`" :title="''" class="situacao-badge">RECEBER</span>
                  <span v-if="item.situacao === 'ABE'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge">ABERTO</span>
                  <span v-if="item.situacao === 'PEN'" v-b-tooltip.viewport.left.hover :class="`badge-yellow`" :title="''" class="situacao-badge">?</span>
                  <span v-if="item.situacao === 'LIQ' " v-b-tooltip.viewport.left.hover :class="`badge-green`" :title="''" class="situacao-badge">RECEBIDO</span>
                  <span v-if="item.situacao === 'CAN' " v-b-tooltip.viewport.left.hover :class="`badge-black`" :title="''" class="situacao-badge">CANCELADO</span>
                  <span v-if="item.situacao === 'SUB' " v-b-tooltip.viewport.left.hover :class="`badge-black`" :title="''" class="situacao-badge">CANCELADO</span>
                  <span v-if="item.situacao == 'VEN' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="'Título em atrazo'" class="situacao-badge">VENCIDO</span>
                  <span v-if="item.situacao == 'INV' " v-b-tooltip.viewport.left.hover :class="`badge-red`" :title="'Pagamento CANLEDADO, EXTORNADO OU DEVOLVIDO'" class="situacao-badge">INVÁLIDO</span>
              </td>


              <td v-b-tooltip.hover.bottom="item.observacao" :title="item.observacao" data-label="Categoria">
                <div class="text-truncate">{{ item.observacao }}</div>
              </td>

              <td data-label="Método">
                <div class="text-truncate">{{ item.metodo ? item.metodo : '' }}</div>
              </td>

              <td data-label="Valor">{{ getValorItem(item)  | formatarMoeda }}</td>
              <td data-label="Saldo">{{ item.valor_saldo_devedor | formatarMoeda }}</td>
              <!-- <td data-label="Conciliado">{{ item.valor_conciliado | formatarMoeda }}</td> -->
              <!-- item.valor_original - item.valor_saldo_devedor -->
              <!-- <td data-label="Valor Pago" class="coluna-valor">{{ getMontante(item) | formatarMoeda }}</td> -->
              <!-- <td data-label="Saldo" class="coluna-valor">{{ item.valor_saldo_devedor | formatarMoeda }}</td> -->

             
              <td class="d-flex" style="gap: 10%;">
                  <template>
                  <a v-b-tooltip.hover.left="'Ver'" href="#" class="icone-link" @click.prevent="abrirDetalhesTituloReceber(item)">
                    <font-awesome-icon icon="eye" />
                  </a>
                </template>

                <!-- <template v-if="item.situacao === 'LIQ' ">
                  <a   v-b-tooltip.hover.left="'Recibo'" href="#" class="icone-link" @click.prevent="abrirVer(item)">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                  </a>
                </template> -->

                <template v-if="item.situacao === 'ABE' || item.situacao === 'VEN' || item.situacao === 'INV' || item.situacao === 'PEN' || item.situacao === 'REC'">
                  <a   v-b-tooltip.hover.left="'Cancelar'" href="#" class="icone-link" @click.prevent="cancelarTitulo(item)">
                    <i class="fa fa-ban" aria-hidden="true"></i>
                  </a>
                </template>
                <template v-if=" podeQuitarTituloReceber(item)">
                  <a v-b-tooltip.hover.left="'Receber'" href="#" class="icone-link" @click.prevent="abrirQuitar(item)">
                    <i class="fa fa-usd" aria-hidden="true"></i>
                  </a>
                </template>
                
              </td>
            </tr>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center px-0">
      <div class="col-12 row m-0 p-0">
        <b-col sm="" md="auto" class="pr-0">
          <div class="info-btn">
            <!-- <b-btn :disabled="!podeQuitarTodosTitulosSelecionados()" type="button" variant="azul" @click="quitarSelecionados()">Receber</b-btn> -->
            <!-- <b-btn :disabled="!podeRenegociarTitulosSelecionados()" type="button" variant="azul" @click="renegociarParcelas()">Renegociar</b-btn> -->
          </div>
        </b-col>

        <b-col sm="" md="auto" class="pr-0">
          <div class="info-btn mt-2 mt-sm-0">
            <b-btn :disabled="!selected.length || selected.some(item => item.situacao == 'CAN') " type="button" variant="verde" @click="imprimirRecibos()">Imprimir Recibos</b-btn> 
            <!-- <b-btn :disabled="!selected.length || selected.some(item => item.situacao == 'CAN')" type="button" variant="verde" @click="imprimirBoletos()">Imprimir Boletos</b-btn> -->
           </div>
          </b-col>
          <div class="col-md-auto" v-if="lista.length">
            <button class="btn btn-primary" @click="exportarParaExcel">Exportar para Excel</button>

               </div>

        <!-- <b-col sm="" md="auto" class="pr-0">
          <div class="info-btn mt-2 mt-sm-0">
           
            <b-btn :disabled="!list.length " type="button" variant="verde" @click="imprimirRelatorio('PDF')">Exportar PDF</b-btn>  -->
            <!-- <button v-input-locker="lockButton()" :disabled="!possuiPermissaoCancelar || !podeCancelarTitulosSelecionados" class="btn btn-vermelho" @click="cancelarTitulosSelecionados()">Cancelar</button> -->
          <!-- </div>
        </b-col> -->
        <b-col sm="" md="auto" class="pr-0">
          <div class="info-btn mt-2 mt-sm-0">
            <!-- <button v-b-modal.vendaAvulsa type="button" class="btn btn-primary">Venda Avulsa</button> -->
          </div>
        </b-col>

        <b-col sm="" md="auto" class="ml-auto mt-2 mt-sm-0">
          <div class="d-flex info-label">
            <div>
              <!-- <div>Recebido:</div>
              <div>A Receber:</div>
              <div>Total selecionado:</div> -->
              
              <div>Recebido:</div> 
              <!-- <div>Extrato:</div> 
              <div>Recebido Não Conciliado:</div>  -->
              <div>Pendente:</div> 
              <div>Vencido:</div> 
              <!-- <div>Total Selecionado:</div>  -->
            </div>

            <div class="text-right">
              <!-- <div>{{ totalRecebido | formatarMoeda }}</div>
              <div>{{ valoresContas.receber | formatarMoeda }}</div> -->
              <!-- <div>{{ totalSelecionado | formatarMoeda }}</div> -->
              <div>{{ totalRecebido | formatarMoeda }}</div>
              <!-- <div>{{ totalFaturado | formatarMoeda }}</div>
              <div>{{ totalRecebidoNaoConciliado | formatarMoeda }}</div>-->
              <div>{{ totalReceber | formatarMoeda }}</div> 
              <div>{{ totalVencido | formatarMoeda }}</div>
              <!-- <div>{{ totalSelecionado | formatarMoeda }}</div> -->
            </div>
          </div>
        </b-col>

      </div>
    </div>

    <modal-titulo-receber-boletos ref="modalTituloReceberBoletos"></modal-titulo-receber-boletos>

    <!-- <modal-titulo-receber-boletos ref="modalTituloReceberBoletos"></modal-titulo-receber-boletos> -->

    <b-modal ref="quitarModal" :no-enforce-focus="true" title="Receber Título" size="xl" centered no-close-on-backdrop hide-footer>
      <quitar-form :titulos="titulosQuitar" :titulo="tituloSelecionado" @cancelar="fecharModalQuitar()" @reload-list="filtrar()" />
    </b-modal>

    <b-modal ref="verModal" :no-enforce-focus="true" title="Ver Conta a Receber" size="xl" centered no-close-on-backdrop hide-footer>
      <ver-modal :titulos="titulosQuitar" @cancelar="fecharModalVer()" />
    </b-modal>

    <!-- VENDA AVULSA -->
    <b-modal :no-enforce-focus="true" id="vendaAvulsa" ref="vendaAvulsa" v-model="vendaAvulsa" size="xl" centered no-close-on-backdrop no-close-on-esc hide-header hide-footer @show="abrirVendaAvulsa()">
      <transition-group>
        <formulario-pessoa v-show="modalAberta === 'pessoa'" key="form-pessoa" :is-modal="true" @resolve="selecionarPessoaVendaAvulsa" @reject="modalAberta = ''" />
        <venda-avulsa v-show="modalAberta !== 'pessoa'" key="form-venda-avulsa" @abrir-modal-pessoa="modalAberta = 'pessoa'" @modal-venda-avulsa-fechar="fecharModalVendaAvulsa" />
      </transition-group>
    </b-modal>
  </div>
</template>
<script>
import * as XLSX from 'xlsx';
import {mapState, mapActions, mapMutations} from 'vuex'
import {getDateFromISO} from '../../utils/date'
import {toNumber, currencyToNumber} from '../../utils/number'
import Filtros from './Filtros.vue'
import QuitarForm from './QuitarForm.vue'
import VendaAvulsa from './VendaAvulsa.vue'
import FormularioPessoa from '../pessoas/Formulario.vue'
import Ver from './Ver.vue'
import EventBus from '../../utils/event-bus'
import open from '../../utils/open'
import moment from 'moment'
import ModalTituloReceberBoletos from './ModalTituloReceberBoletos.vue'

export default {
  components: {
    ModalTituloReceberBoletos,
    Filtros,
    QuitarForm,
    VendaAvulsa,
    'ver-modal': Ver,
    'formulario-pessoa': FormularioPessoa
  },

  data () {
    return {
      orderBy: '',
      orderDesc: false,
      situacoesTitle: {
        PEN: 'Em Aberto',
        LIQ: 'Quitado',
        CAN: 'Cancelado',
        REC: 'A Receber',
        VEN: 'Atrazado',        
      },
      situacoes: [
      {text: 'Vencidos', value: 'VEN'},
      {text: 'Abertos', value: 'ABE'},
      {text: 'Receber', value: 'REC'},
      {text: 'Recebidos', value: 'LIQ'},
        // {text: 'Cancelados', value: 'CAN'},
        
      ],
      exportFields:{
        Titulo: 'id',
        'Aluno/Responsavel': 'cliente_nome',
        'Vencimento': 'data_vencimento',
        'Situação': 'situacao',
        'Categoria': 'observacao',
        'Método': 'metodo',
        'Valor': {
          field:'valor_original', 
          callback: (value) => value ? `R$ ${parseFloat(value)}` : 'R$ 0,00'
        },  
        'Saldo': {
          field:'valor_saldo_devedor', 
          callback: (value) => value ? `R$ ${parseFloat(value)}` : 'R$ 0,00'
        },  
        'Conciliado': {
          field:'valor_conciliado', 
          callback: (value) => value ? `R$ ${parseFloat(value)}` : 'R$ 0,00'
        },  
        'valor_total': 'total_faturado'      
      },     
      pessoasBuscar: {
        aluno: true,
        sacado: true
      },
      className: 'rapido-open',
      checkAll: false,
      indeterminate: false,
      selected: [],
      createdSelected: [],
      list: [],
      valoresContas: {
        recebidos: 0,
        receber: 0
      },
      pagando: false,
      podeSalvar: false,
      visible: false,
      totalSelecionado: 0,
      // totalFaturado: 0,
      // totalRecebido: 0,
      // totalReceber: 0,
      // totalCancelado: 0,
      // totalVencido: 0,
      hoje: (new Date()).toISOString().split('T')[0],
      titulosQuitar: [],
      tituloSelecionado: null,
      gerentePermitiuCancelar: false,
      gerenteId: null,
      input_locker_callback: false,

      vendaAvulsa: false,
      modalAberta: ''
    }
  },

  computed: {
    ...mapState('contasReceber', ['lista', 'estaCarregando', 'totalFaturado','totalRecebido','totalRecebidoNaoConciliado','totalReceber', 'totalVencido','totalCancelado','todosItensCarregados', 'paginaAtual','formato_impressao']),
    ...mapState('formaPagamento', {listaFormasPagamento: 'lista'}),
    ...mapState('diasSubsequentes', ['listaDiasDaFranqueada']),
    ...mapState('modulos', ['permissoes']),
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },
    // possuiPermissaoCancelar: {
    //   get () {
    //     const possuiPermissao = this.permissoes['CANCELAR_TITULO_RECEBER'] && this.permissoes['CANCELAR_TITULO_RECEBER'].possui_permissao === true
    //     return possuiPermissao || this.gerentePermitiuCancelar
    //   }
    // },

    // podeCancelarTitulosSelecionados: {
    //   get () {
    //     if (this.selected.length === 0) {
    //       return false
    //     }
    //     let podeCancelar = true
    //     this.selected.forEach(registro => {
    //       if (!this.podeCancelarTitulo(registro.titulo_receber)) {
    //         podeCancelar = false
    //       }
    //     })
    //     return podeCancelar
    //   }
    // }
  },

  watch: {
    selected (value, oldVal) {
      this.totalSelecionado = 0
       // Handle changes in individual checkboxes
      if (value.length === 0) {
        this.indeterminate = false
        this.checkAll = false
      } else if (value.length === this.list.length) {
        this.indeterminate = false
        this.checkAll = true
      } else {
        this.indeterminate = true
        this.checkAll = false
      }

      if (value.length > 0 || oldVal.length > 0) {
        let total = 0
        for (let i = value.length; i--;) {
          total += parseFloat(value[i].valor_original)
        }

        this.totalSelecionado = total
        this.conta = ((total > 0 || this.conta === '') && value[0]) ? value[0].contaa : ''
      }
    },

    list (lista) {
      
      // this.valoresContas = {
      //   recebidos: 0,
      //   receber: 0
      // }

      //const statusPendencia = ['PEN', 'VEN', 'LIQ-PEN']
      // this.totalTela = 0
      // console.log(lista.length)
      // for (let i = lista.length ; i--; i >= 0) {
      //   const titulo = lista[i]
      //   if(titulo.situacao !== 'CAN' && titulo.situacao !== 'SUB'){
      //     this.totalTela += Number(titulo.valor_original)
      //   }
        
      // }

      // for(let i = lista.length; i--; i > 0) {
      //   const titulo = lista[i]
      //   if(statusPendencia.includes(titulo.situacao)) {
      //     this.valoresContas.receber += Number(titulo.valor_saldo_devedor)
      //   } 
        // else 
        // if(titulo.situacao === 'LIQ' && titulo.valor_saldo_devedor >0) {
          //TODO verificar se o titulo foi pago OU TEM SALDO
          // if(titulo.transacoes_cartao ) {
          //   if(titulo.transacoes_cartao[0] && titulo.transacoes_cartao[0].situacao == 'PEN') {
          //     this.valoresContas.receber += Number(titulo.valor_saldo_devedor)
          //   }
          // }
        // }
        
      // }

      this.$store.commit('contasReceber/SET_ESTA_CARREGANDO', false)
    },

    createdSelected (value) {
      if (!value) {
        return
      }

      value.map(item => {
        this.somarTotal(item)
      })

      this.valorTotal()
    },

    input_locker_callback (value) {
      if (value && value.id) {
        this.gerentePermitiuCancelar = true
        this.gerenteId = value.id
        if (this.podeCancelarTitulo) {
          this.cancelarTitulosSelecionados()
        }
      }
    }

  },

  mounted () {
    this.$store.commit('formaPagamento/SET_PAGINA_ATUAL', 1)
    this.$store.commit('operadoraCartao/SET_PAGINA_ATUAL', 1)
    this.$store.commit('conta/SET_PAGINA_ATUAL', 1)
    this.$store.commit('contasReceber/SET_PAGINA_ATUAL', 1)
    this.$store.commit('contasReceber/SET_LISTA', [])
    this.$store.dispatch('formaPagamento/getLista')
    this.$store.dispatch('operadoraCartao/listar')
    this.$store.dispatch('conta/getLista')

    

    this.SET_PAGINA_ATUAL(1)

    this.consultar()

    EventBus.$on('modal:reabrir-modal', () => {
      // this.abrirModalQuitar(false)
    })
  },

  methods: {
    ...mapActions('recibo', ['gerarRecibo']),
    ...mapActions('contasReceber', ['listar', 'gerarRelatorio']),
    ...mapActions('contasReceber', ['consultar']),
    ...mapActions('tituloReceber', ['cancelarTitulos']),
    ...mapMutations('contasReceber', ['SET_LISTA','SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_FORMATO_IMPRESSAO']),
    ...mapMutations('tituloReceber', ['SET_TITULOS_QUITAR','SET_TITULO_QUITAR']),


    getDateFromISO,
    toNumber,

    getDataVencimento(tituloReceber){
      // if(tituloReceber.data_prorrogacao > tituloReceber.data_vencimento){
      //   return tituloReceber.data_prorrogacao
      // }
      // else{
      //   return tituloReceber.data_vencimento
      // }
      return tituloReceber.data_vencimento
    },
    getValorItem(item){
      return item.valor_original
    },
    formatarValor(valor) {
      if((valor === undefined) || (valor === '')) { 
        return '' 
      }
      //  else {
      //   if (typeof valor === 'number') {
      //     return `R$ ${parseFloat(valor).toFixed(2).replace(".",",")}`;         
      //   } else {
      //     const valorNumerico = parseFloat(valor);
      //    return `R$ ${parseFloat(valor).toFixed(2).replace(".",",")}`;
      //   }
      // }
      var retorno = 0;
      if( !(valor === undefined) || (valor === '')) { 
        retorno +=  Number(valor);
      }
      return retorno;
    },
    formatarDataIsoParaBr(dataIso) {
      if (dataIso ) {
        const data = new Date(dataIso);
        const dia = data.getDate().toString().padStart(2, '0');
        const mes = (data.getMonth() + 1).toString().padStart(2, '0'); // Janeiro é 0!
        const ano = data.getFullYear();
        const horas = data.getHours().toString().padStart(2, '0');
        const minutos = data.getMinutes().toString().padStart(2, '0');
        
        return `${dia}/${mes}/${ano} ${horas}:${minutos}:00`;
      }
      return '';
      
    },
    formatarSituacao(valor) {      
      if((valor === 'VEN') ) { 
        return 'Vencido' 
      }
      if((valor === 'LIQ-PEN') || (valor === 'PEN')) { 
        return 'Aberto' 
      }
      if((valor === 'LIQ')) { 
        return 'Recebido' 
      }
      if((valor === 'CAN') || (valor === 'SUB')) { 
        return 'Cancelado' 
      }
      if(valor === 'INV') { 
        return 'Inválido' 
      }   
        return valor
      
    },

    exportarParaExcel() {
      
      //  const total = this.list.reduce((acc, item) => acc + item.valor, 0);
      this.list.push({ id: '', metodo: '', valor_original: '' });
      this.list.push({ id: '', metodo: '', valor_original: '' });
      this.list.push({ id: 'Recebido: ', cliente_nome: this.formatarValor(this.totalRecebido )});
      this.list.push({ id: 'Recebido Não Conciliado: ', cliente_nome: this.formatarValor(this.totalRecebidoNaoConciliado) });
      this.list.push({ id: 'Pendente: ', cliente_nome: this.formatarValor(this.totalReceber) });
      this.list.push({ id: 'Vencidos: ', cliente_nome: this.formatarValor(this.totalVencido) });
   
      const wb = XLSX.utils.book_new();

      const ws = XLSX.utils.json_to_sheet(this.list.map(item => ({
        Titulo: item.id,
        'Aluno(sacado)': item.cliente_nome,
        'Data Vencimento': this.formatarDataIsoParaBr(item.data_vencimento ),
        Situação: this.formatarSituacao(item.situacao),
        Categoria: item.observacao,
        Método: item.metodo,
        'Valor Liquido': this.formatarValor(item.valor_original),
        'Valor Bruto': this.formatarValor(item.valor_parcela_sem_desconto != null ? item.valor_parcela_sem_desconto : item.valor_original),   
        'Valor Lançamento': this.formatarValor(item.valor_lancamento != null ? item.valor_lancamento : ''),   
      })));

      XLSX.utils.book_append_sheet(wb, ws, 'Planilha1');

      XLSX.writeFile(wb, 'relatorio-contas-a-receber.xlsx');

      // Remover a linha de total da lista após exportar
      this.list.pop();
    },

    ordenarListagem(campo) {
      if(this.orderBy == campo) {
        this.orderDesc = !this.orderDesc
      }else {
        this.orderDesc = false
        this.orderBy = campo
      }
      this.ordenar()
    },

    ordenar() {
      if(this.lista.length <= 1 || !this.lista[0].hasOwnProperty(this.orderBy)) {
        return
      } 

      const listaOrdenada = Object.values(this.lista).sort((a,b) => {
        let result = 0
        switch (this.orderBy) {
          case 'valor_original':
          case 'valor_saldo_devedor':
          case 'valor_conciliado':
          case 'id':
              result = parseInt(a[this.orderBy]) - parseInt(b[this.orderBy])
            break;
          case 'metodo':
          case 'observacao':
          case 'cliente_nome':
            if(!a[this.orderBy].attr){
              result = a[this.orderBy].localeCompare(b[this.orderBy])
            } else {
              result = a[this.orderBy].attr.localeCompare(b[this.orderBy].attr)
            }
            break;
          case 'data_vencimento':
              result = Date.parse(moment(a[this.orderBy], "YYYY-MM-DD hh:mm:ss").toISOString()) - Date.parse(moment(b[this.orderBy], "YYYY-MM-DD hh:mm::ss").toISOString())
            break;
          default:
            break;
        }
        return result * (this.orderDesc ? -1 : 1)
      }).map( el => el)
      this.$store.commit('contasReceber/SET_LISTA', listaOrdenada)

    },

    podeQuitarTituloReceber (tituloReceber) {
      const situacaoValida = tituloReceber.situacao !== 'CAN' && tituloReceber.situacao !== 'SUB'
      
      const saldoDevedorValido = tituloReceber.valor_saldo_devedor > 0
      if (!situacaoValida || !saldoDevedorValido) {
        return false
      }
      // if (tituloReceber.cheques && tituloReceber.cheques.length) {
      //   const possuiChequePreenchidoPendente = tituloReceber.cheques.some(cheque => !cheque.excluido && cheque.situacao === 'P')
      //   if (possuiChequePreenchidoPendente) {
      //     return false
      //   }
      // }
      // if (tituloReceber.transacoes_cartao && tituloReceber.transacoes_cartao.length) {
      //   const possuiTransacaoCartaoPendente = tituloReceber.transacoes_cartao.some(transacao => transacao.situacao === 'PEN')
      //   if (possuiTransacaoCartaoPendente) {
      //     return false
      //   }
      // }
      return true
    },

    podeQuitarTodosTitulosSelecionados () {
      if (!this.selected.length) {
        return false
      }
      if (this.selected.some(item => !this.podeQuitarTituloReceber(item))) {
        return false
      }
      return true
    },

    // nomeAlunoSacado (item) {
    //   const nomeSacado = item.sacado_pessoa.razao_social || item.sacado_pessoa.nome_contato
    //   if (item.aluno && item.aluno.pessoa) {
    //     const nomeAluno = item.aluno.pessoa.nome_contato
    //     if (nomeSacado === nomeAluno) {
    //       return nomeAluno
    //     }
    //     return nomeAluno + ' (' + nomeSacado + ')'
    //   }
    //   return nomeSacado
    // },

    getMontante (item) {
      let montante = 0

      item.movimento_conta.forEach((item) => {
        if (item.operacao === 'D') {
          montante -= parseFloat(item.valor_lancamento)
        } else if (item.operacao === 'C') {
          montante += parseFloat(item.valor_lancamento)
        }
      })
      return montante
    },

    checkRow (event, item) {
      const tag = event.target.tagName.toLocaleLowerCase()
      const tags = ['label', 'input', 'path', 'svg', 'a']
      if (tags.includes(tag)) {
        return
      }

      if (item.situacao === 'CAN' || item.situacao === 'LIQ') {
        return
      }

      const index = this.selected.indexOf(item)
      if (index !== -1) {
        this.selected.splice(index, 1)
        return
      }

      this.selected.push(item)
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.consultar()
    },

    fecharModalQuitar () {
      this.selected = []
      this.$refs.quitarModal.hide()
      this.SET_PAGINA_ATUAL(1)
      this.consultar()
    },

    fecharModalVer () {
      this.selected = []
      this.$refs.verModal.hide()
    },

    fecharModalVendaAvulsa () {
      this.vendaAvulsa = false
      this.filtrar()
    },

    toggleAll (checked) {
      if (checked) {
        const list = this.list.slice()
        this.selected = list.filter(item => { return item.situacao !== 'CAN' && item.situacao !== 'LIQ' })
        return
      }

      this.selected = []
    },

    trocaClasseTabela (className) {
      this.className = className
    },

    filtrar () {
      this.orderBy = '';
      this.orderDesc = false;
      this.toggleAll()
      this.SET_PAGINA_ATUAL(1)
      this.consultar()
    },

    abrirVer (item) {
      this.selected = [item]

      this.titulosQuitar = this.selected.map(titulo => ({...titulo}))

      this.SET_TITULOS_QUITAR(this.titulosQuitar)

      this.$refs.verModal.show()
      setTimeout(() => {
        EventBus.$emit('ver-modal:abrir', true)
      }, 100)
    },

    abrirQuitar (item) {
      // this.selected = [item]
      // this.quitarSelecionados()
      // this.titulosQuitar = [item]
      this.tituloSelecionado = item
      //this.SET_TITULOS_QUITAR(this.titulosQuitar)
      this.SET_TITULO_QUITAR(this.tituloSelecionado)
      
      this.$refs.quitarModal.show()
        setTimeout(() => {
          EventBus.$emit('quitar-modal:abrir', true)
        }, 100)
    },

    quitarSelecionados () {
      this.abrirModalQuitar()
    },

    abrirModalQuitar (atualizarTitulos) {
      let podeQuitarTodos = true
      this.titulosQuitar = this.selected.map(titulo => {
        if (!this.podeQuitarTituloReceber(titulo)) {
          podeQuitarTodos = false
        }

        return {...titulo}
      })

      this.SET_TITULOS_QUITAR(this.titulosQuitar)

      if (podeQuitarTodos === true) {
        this.$refs.quitarModal.show()
        setTimeout(() => {
          EventBus.$emit('quitar-modal:abrir', atualizarTitulos)
         
        }, 100)
      } else {
        EventBus.$emit('chamarModal', {}, 'Não pode quitar títulos com saldo zero.')
      }
    },

    cancelarTitulosSelecionados () {
      if (!this.podeCancelarTitulosSelecionados) {
        EventBus.$emit('criarAlerta', {
          tipo: 'a',
          mensagem: 'Ao menos um titulo e somente títulos válidos (pendentes ou vencidos) devem ser selecionados.'
        })
        return false
      }
      let titulosCancelar = []
      this.selected.forEach(registro => {
        if (this.podeCancelarTitulo(registro.titulo_receber)) {
          titulosCancelar.push(parseInt(registro.titulo_receber.id))
        }
      })
      if (titulosCancelar.length === 0) {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'Nenhum dos itens selecionados pode ser cancelado.'
        })
        return false
      }

      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.cancelarTitulos({titulos: titulosCancelar, usuarioID: this.$store.state.root.usuarioLogado.id}).then(() => {
            this.filtrar()
            // TODO: Fazer com que uma vez que foi cancelado uma vez, o gerente tenha que colocar a senha novamente, caso o usuário não possua a permissão
            // this.gerentePermitiuCancelar = false
          }, err => {
            const mensagem = err.mensagem ? err.mensagem : 'Erro ao cancelar titulos.'
            EventBus.$emit('criarAlerta', {
              tipo: 'a',
              mensagem: mensagem
            })
          })
        }
      }, `Tem certeza que deseja cancelar os titulos selecionados?`, 'Sim', 'Não')
    },

    cancelarTitulo (titulo) {
      
      
      let titulosCancelar = []
      titulosCancelar.push(parseInt(titulo.id))

      EventBus.$emit('chamarModal', {
        resolve: success => {
          this.cancelarTitulos({titulos: titulosCancelar, usuarioID: this.$store.state.root.usuarioLogado.id}).then(() => {
            this.filtrar()
            // TODO: Fazer com que uma vez que foi cancelado uma vez, o gerente tenha que colocar a senha novamente, caso o usuário não possua a permissão
            // this.gerentePermitiuCancelar = false
          }, err => {
            const mensagem = err.mensagem ? err.mensagem : 'Erro ao cancelar titulos.'
            EventBus.$emit('criarAlerta', {
              tipo: 'a',
              mensagem: mensagem
            })
          })
        }
      }, `Tem certeza que deseja cancelar o titulo?`, 'Sim', 'Não')
    },

    podeCancelarTitulo (titulo) {
      if (titulo && (titulo.situacao === 'PEN' || titulo.situacao === 'LIQ-PEN' || titulo.situacao === 'VEN')) {
        return true
      }
      return false
    },

    lockButton () {
      if (!this.gerentePermitiuCancelar) {
        return {permissao: this.permissoes['CANCELAR_TITULO_RECEBER'], callBack: this}
      }
    },

    renegociarParcelas () {
      if (!this.podeRenegociarTitulosSelecionados()) {
        return false
      }
      const titulos = this.selected.map(contaReceber => contaReceber.titulo_receber.id)
      this.$router.push({ path: '/m/Renegociacao/adicionar', query: { titulos } })
    },

    podeRenegociarTitulosSelecionados () {
      if (!this.selected.length) {
        return false
      }
      let podeRenegociar = false
      this.selected.forEach(registro => {
        if (this.podeRenegociarTitulo(registro.titulo_receber)) {
          podeRenegociar = true
        }
      })
      return podeRenegociar
    },

    imprimirRelatorio (formato) {
      this.SET_FORMATO_IMPRESSAO(formato)
      this.gerarRelatorio()
      this.SET_FORMATO_IMPRESSAO('')
    },

    podeRenegociarTitulo (titulo) {
      if (titulo && (titulo.situacao === 'PEN' || titulo.situacao === 'LIQ-PEN' || titulo.situacao === 'VEN') && titulo.valor_saldo_devedor > 0) {
        return true
      }
      return false
    },

    somarTotal (item) {
      const montante = currencyToNumber(item.valor_montante)
      const juros = currencyToNumber(item.valor_juros)
      const desconto = currencyToNumber(item.valor_desconto)

      const valor = (montante + juros - desconto)
      item.valor_total = valor
      item.valor_negativo = valor < 0
      this.podeSalvar = item.erro_valor || item.valor_negativo

      item.total_titulo = valor
    },

    valorTotal () {
      const lista = this.createdSelected
      let valor = 0
      for (let i = lista.length; i--;) {
        valor += parseFloat(lista[i].valor_total)
      }
      this.totalReceber = valor
    },

     imprimir () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const rota = this.$route.matched[0].path
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const contratoId = this.contratoId
      const modeloContratoId = this.modeloContratoId
      const url = `/api/contrato/imprimir/${contratoId}?Authorization=${auth}&URLModulo=${rota}&franqueada=${franqueada}&modelo_contrato=${modeloContratoId}`
      // open(url, '_blank')
      var host = process.env.VUE_APP_HOST;
      // window.open(`${host}${url}`, '_blank')
    },
    imprimirRecibos () {
      let movimentoContaId = []
      let tituloId = []
      this.selected.map(item => {

        // if (item !== undefined) {
        //   if (item.movimento_conta.length > 0) {
        //     item.movimento_conta.forEach(movimentoConta => {
        //       if (movimentoConta.estornado == false && movimentoConta.movimento_estorno == false) {
        //         movimentoContaId.push(movimentoConta.id)
        //       }
        //     })
        //   }

        // }
        //if (item.movimento_conta.length == 0 || movimentoContaId.length == 0) {
          if (item.id != null) {
            tituloId.push(item.id)
          }
        //}

      })

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
      routeData.href = routeData.href.replace('/dashboard', '/api/recibo/imprimir')
      // for (let i = 0; i < movimentoContaId.length; i++) {
        //    routeData.href += `&movimentos_conta[${i}]=${movimentoContaId[i]}`
        //  }
 
 //     routeData.href = routeData.href.replace('/dashboard', '/api/recibo/imprimir').replace('/financeiro', '')
         



      for (let i = 0; i < tituloId.length; i++) {
        routeData.href += `&titulos[${i}]=${tituloId[i]}`
      }
      open(routeData.href, '_blank')
    },

    imprimirBoletos () {
          let boletos = []
          this.selected.forEach(itemSelecionado => {
            if (itemSelecionado.titulo_receber.boletos.length > 0) {
              itemSelecionado.titulo_receber.boletos.forEach(boleto => {
                boletos.push(boleto.id)
              })
            }
           })
        
      
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

    abrirVendaAvulsa () {
      this.$store.dispatch('diasSubsequentes/buscarPorFranqueadaAtual')
    },

    selecionarPessoaVendaAvulsa (pessoa) {
      this.$store.commit('pessoas/setPessoaSelecionada', pessoa)
      this.$store.dispatch('pessoas/getPessoa')
        .then((pessoaData) => {
          this.modalAberta = ''
          EventBus.$emit('venda-avulsa-selecionar-pessoa', pessoaData)
        })
    },

    carregarMais () {
      // this.listar()
    },

    abrirDetalhesTituloReceber(titulo) {
      this.$refs.modalTituloReceberBoletos.visible = true;
      this.$refs.modalTituloReceberBoletos.outrosDetalhes = titulo
      this.$refs.modalTituloReceberBoletos.carregarDetalhes(titulo.id)
    }
  }
}
</script>

<style>
div.unlock-input[data-id=CANCELAR_TITULO_RECEBER]{
  margin-top: .5rem!important;
}
</style>

<style lang="scss" scoped>
#aviso-gerando-recibo .loader-box .spinner {
  margin: auto;
}

.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}

.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}

#total-container {
  background-color: #EBECF0;
  font-size: 1rem;
}
#total-container span {
  color: #4a4a4a;
}

.btn-filter {
  color: #fff;
  background-color: #4a69c5;
  transition: all .2s;
}
.btn-filter.active {
  color: #4a69c5;
  background-color: transparent;
}
.btn-filter:not(.active):hover {
  background-color: #415eb5;
}

.table-filter div {
  display: flex;
  align-items: center;
}
.table-filter div label {
  margin: 0;
}
.table-filter div input {
  margin-left: .3rem;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

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

button.btn.btn-vermelho.locked-el{
  padding-top: 0.24rem;
  padding-bottom: 0.24rem;
}

table thead {
    position: relative;
    background-color: #ada8a8;
}
</style>
