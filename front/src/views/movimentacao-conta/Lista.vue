<template>
  <div class="animated fadeIn">
    <filtros @troca-classe-tabela="trocaClasseTabela" @filtrar="filtrar()" />

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable" class="selectAll">
        <thead class="text-dark" style="position: sticky;z-index: 1;background: white;">
          <tr>
            <th data-label="Selecionar todos" class="coluna-checkbox">
              <b-form-checkbox :disabled="!lista.length" v-model="checkAll" :indeterminate="indeterminate" class="m-0 p-0" aria-describedby="selected" aria-controls="selected" @change="toggleAll"/>
            </th>
            <th class="coluna-numero-parcela text-center">#</th>
            <th class="text-left">Título</th>
            <th data-column="ch.numero" class="coluna-tipo-operacao">Tipo</th>
            <th v-b-tooltip data-column="movimento.data_deposito" class="coluna-data d-block text-truncate" title="Data Movimentação">Data Movimentação</th>
            <th>Origem</th>
            <!-- <th data-column="movimento.observacao">Categoria</th> -->
            <!-- <th v-b-tooltip data-column="tipoMovimentoConta.descricao" title="Tipo de Movimentação" class="d-block text-truncate">Tipo de Movimentação</th> -->
            <th v-b-tooltip data-column="formaPagamento.descricao" title="Forma de Pagamento" class="d-block text-truncate">Forma de Pagamento</th>
            <!-- <th v-b-tooltip data-column="movimento.numero_documento" title="Nº Cheque/Cartão" class="d-block text-truncate">Nº Cheque/Cartão</th> -->
            <!-- <th data-column="movimento.conciliado">Conciliado</th> -->
            <!-- <th data-column="movimento.titulo">Título</th> -->
            <th data-column="movimento.valor_lancamento" class="coluna-valor">Valor</th>
            <th class="coluna-icones">Ações</th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-else-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in lista" :key="item.id" :lista="list = lista" @click="checkRow($event, item)">
              <td data-label="Selecionar" class="coluna-checkbox">
                <b-form-checkbox v-if="item.conciliado === 'N'" v-model="selected" :value="item" class="m-0 p-0" />
              </td>

              <td class="coluna-numero-parcela text-center" data-label="#">{{ item.id }}</td>

              <td class="text-left" data-label="titulo">{{ item.titulo_receber?.id }}</td>

              <td data-label="Tipo" class="coluna-tipo-operacao">
                <span :class="item.operacao === 'C' ? 'date-success' : 'date-danger'" class="badge align-middle rounded">
                  {{ item.operacao === 'D' ? 'S' : 'E' }}
                </span>
              </td>

              <td data-label="Data Movimentação" class="coluna-data">{{ item.data_deposito | formatarData }}</td>

              <td :title="nomePessoaDisplay(item)" data-label="Origem">{{ nomePessoaDisplay(item) }}</td>

              <!-- <td v-b-tooltip :title="item.observacao" class="d-block text-truncate" data-label="Categoria">{{ item.observacao }}</td> -->
<!-- 
              <td v-b-tooltip :title="item.tipo_movimento_conta.descricao" class="d-block text-truncate" data-label="Tipo de Movimentação">{{ item.tipo_movimento_conta.descricao }}</td> -->

              <td v-b-tooltip :title="item.forma_pagamento.descricao" class="d-block text-truncate" data-label="Forma de Pagamento">{{ item.forma_pagamento.descricao }}</td>
<!-- 
              <td data-label="Nº Cheque/Cartão">{{ item.numero_documento }}</td> -->

              <!-- <td data-label="Conciliado">{{ item.conciliado === 'S' ? 'Sim' : 'Não' }}</td> -->
              <!-- <td data-label="titulo">
                <span v-if=" item.estornado === true && item.titulo_receber"  v-b-tooltip :title="'ESTORNADO'">
                  R:{{ item.titulo_receber.id }}
                </span>
                <span v-if=" item.estornado === true && item.titulo_pagar" v-b-tooltip :title="'ESTORNADO'">
                  R:{{ item.titulo_pagar.id }}
                </span>
                <span v-if=" item.estornado === false && item.titulo_receber">
                  R:{{ item.titulo_receber.id }}
                </span>
                <span v-if=" item.estornado === false && item.titulo_pagar">
                  R:{{ item.titulo_pagar.id }}
                </span>
              </td> -->
             <!-- <td v-if=" item.estornado === true && item.titulo_receber" v-b-tooltip :title="'ESTORNADO'" class = "estornado coluna-titulo" >
                R:{{ item.titulo_receber.id }}
             </td> -->
               <!-- <td v-if="item.estornado === false && item.titulo_receber"  class = " coluna-titulo" >
                 R:{{ item.titulo_receber.id }}
             </td>
              <td v-if=" item.estornado === true && item.titulo_pagar" v-b-tooltip :title="'ESTORNADO'" class = "estornado coluna-titulo" >
                 P:{{ item.titulo_pagar.id }}
             </td>
               <td v-if="item.estornado === false && item.titulo_pagar"  class = " coluna-titulo" >
                 P:{{ item.titulo_pagar.id }}
             </td> -->
             
              
              <td class="coluna-valor" data-label="Valor">{{ item.valor_lancamento | formatarNumero }}</td>
             
              <td class="coluna-icones d-flex">
                <!-- Transferir -->
                <a v-b-tooltip.left v-if="item.estornado === false && item.movimento_estorno === false" href="javascript: void(0)" class="icone-link" title="Transferir para outra conta" @click="transferirExistente(item)">
                  <font-awesome-icon icon="exchange-alt" />
                </a>

                <a v-b-tooltip.left v-else href="javascript: void(0)" class="icone-link disable-icon" title="Este lançamento não pode ser transferido">
                  <font-awesome-icon icon="exchange-alt" />
                </a>

                <!-- Editar -->
                <a v-b-tooltip.left href="javascript: void(0)" class="icone-link" title="Editar" @click="editar(item)">
                  <font-awesome-icon icon="pen" />
                </a>

                <!-- Conciliar -->
                <!-- <a v-b-tooltip.left v-if="item.conciliado === 'S'" title="Lançamento já conciliado" href="javascript: void(0)" class="icone-link disable-icon">
                  <font-awesome-icon icon="check" />
                </a> -->

                <!-- <a v-b-tooltip.left v-else title="Conciliar" href="javascript: void(0)" class="icone-link" @click="conciliar(item)">
                  <font-awesome-icon icon="check" />
                </a> -->

                <!-- Estornar -->
                <a v-b-tooltip.left v-if="item.estornado === false && item.movimento_estorno === false" class="icone-link" title="Estornar" @click="estornar(item)">
                  <icon-cashback />
                </a>

                <a v-b-tooltip.left v-else-if="item.movimento_estorno === true" class="icone-link disable-icon" href="javascript: void(0)" title="Este lançamento é um estorno">
                  <icon-cashback />
                </a>

                <a v-b-tooltip.left v-else class="icone-link disable-icon" href="javascript: void(0)" title="Lançamento já estornado">
                  <icon-cashback />
                </a>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <div class="d-flex flex-column info-btn">
          <b-btn type="button" variant="azul" @click="incluir()">Incluir</b-btn>
          <b-btn class="mt-1" type="button" variant="roxo" @click="transferir()">Transferência</b-btn>
        </div>

        <div class="col-md-auto" v-if="lista.length">
            <button class="btn btn-primary" @click="exportarParaExcel">Exportar para Excel</button>
        </div>

        <!-- <div class="d-flex flex-column info-btn ml-2">
          <b-btn :disabled="selected.length === 0" type="button" variant="azul" @click="conciliarVarios()">Conciliar</b-btn>
        </div> -->
      </div>

      <div class="d-flex flex-row info-label">
        <div>
          <!-- <div>Saldo Inícial:</div> -->
          <div>Entradas:</div>
          <div>Saídas:</div>
          <!-- <div>Saldo Provisório:</div>
          <div>Saldo Conciliado:</div> -->
        </div>

        <div class="ml-1 text-right">
          <!-- <div>{{ saldoInicial | formatarMoeda }}</div> -->
          <div>{{ totalEntradas | formatarMoeda }}</div>
          <div>{{ totalSaidas | formatarMoeda }}</div>
          <!-- <div>{{ ((totalEntradas || 0) * 1) - ((totalSaidas || 0) * 1) | formatarMoeda }}</div>
          <div>{{ ((saldoInicial || 0) * 1) + ((totalEntradas || 0) * 1) - ((totalSaidas || 0) * 1)| formatarMoeda }}</div> -->
        </div>
      </div>
    </div>

    <b-modal id="formIncluir" ref="formIncluir" title="Incluir" hide-footer @hide="limparFormIncluir()">
      <formulario-incluir @filtrar="filtrar()" @fechar="$refs.formIncluir.hide()" />
    </b-modal>

    <b-modal id="formEditar" ref="formEditar" title="Editar" hide-footer @hide="limparFormEditar()">
      <formulario-editar @filtrar="filtrar()" @fechar="$refs.formEditar.hide()" />
    </b-modal>

    <b-modal id="formTransferir" ref="formTransferir" title="Transferir" hide-footer @hide="limparFormTransferir()">
      <formulario-transferir @filtrar="filtrar()" @fechar="$refs.formTransferir.hide()" />
    </b-modal>

    <b-modal id="formTransferirExistente" ref="formTransferirExistente" title="Transferir para outra conta" hide-footer @hide="limparFormTransferirExistente()">
      <formulario-transferir-existente @filtrar="filtrar()" @fechar="$refs.formTransferirExistente.hide()" />
    </b-modal>

    <b-modal id="formEstornar" ref="formEstornar" title="Estorno de lançamento" hide-footer @hide="limparFormEstornar()">
      <formulario-estornar @filtrar="filtrar()" @fechar="$refs.formEstornar.hide()" />
    </b-modal>

    <b-modal id="formConciliarVarios" ref="formConciliarVarios" title="Conciliar" hide-footer @hide="limparFormConciliarVarios()">
      <formulario-conciliar @filtrar="filtrar()" @fechar="$refs.formConciliarVarios.hide()" />
    </b-modal>
  </div>
</template>

<script>
import * as XLSX from 'xlsx';
import {mapState} from 'vuex'
import EventBus from '../../utils/event-bus'
import Filtros from './Filtros.vue'
import FormularioIncluir from './FormularioIncluir.vue'
import FormularioEditar from './FormularioEditar.vue'
import FormularioTransferir from './FormularioTransferir.vue'
import FormularioTransferirExistente from './FormularioTransferirExistente.vue'
import FormularioEstornar from './FormularioEstornar.vue'
import FormularioConciliar from './FormularioConciliar.vue'
import moment from 'moment';

export default {
  name: 'ListaMovimentacaoConta',
  components: {
    Filtros,
    'formulario-incluir': FormularioIncluir,
    'formulario-editar': FormularioEditar,
    'formulario-transferir': FormularioTransferir,
    'formulario-transferir-existente': FormularioTransferirExistente,
    'formulario-estornar': FormularioEstornar,
    'formulario-conciliar': FormularioConciliar
  },

  data () {
    return {
      selected: [],
      checkAll: false,
      indeterminate: false,
      className: '',

      exportFields:{
        Extrato: 'id',
        'Numero': 'id',
        'Nome_Contato': 'nome_contato',
        'Tipo': 'operacao',
        'Data de Movimento': 'data_movimento',
        'Origem': 'observacao',
        'Forma de Pagamento': 'forma_pagamento.descricao',
        'Valor': {
          field:'valor_titulo', 
          callback: (value) => value ? `R$ ${parseFloat(value)}` : 'R$ 0,00'
        }      
      }, 
    }
  },

  computed: {
    ...mapState('movimentacaoConta', ['lista', 'filtros', 'saldoInicial', 'totalEntradas', 'totalSaidas', 'totalNaoConciliados', 'estaCarregando', 'todosItensCarregados']),

    saldoConta: {
      get () {
        return this.filtros.conta ? this.filtros.conta.valor_saldo : 0
      }
    },

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },

  watch: {
    selected (value, oldVal) {
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
    }
  },

  mounted () {
    this.$store.commit('movimentacaoConta/SET_PAGINA_ATUAL', 1)
    this.$store.commit('movimentacaoConta/SET_LISTA', [])
  },

  methods: {
    checkRow (event, item) {
      if (item.conciliado === 'N') {
        const tag = event.target.tagName.toLocaleLowerCase()
        const tags = ['label', 'input', 'path', 'svg', 'a']
        if (tags.includes(tag)) {
          return
        }
        const index = this.selected.indexOf(item)
        if (index !== -1) {
          this.selected.splice(index, 1)
          return
        }
        this.selected.push(item)
      }
    },

    nomePessoaDisplay (item) {
      let nomePessoa = ''
      if (item.titulo_receber) {
        if (!item.titulo_receber.aluno) {
          nomePessoa += `(${item.titulo_receber.sacado_pessoa.nome_contato})`
        } else {
          nomePessoa = item.titulo_receber.aluno.pessoa.nome_contato
          if (item.titulo_receber.aluno.pessoa.id !== item.titulo_receber.sacado_pessoa.id) {
            nomePessoa += ` (${item.titulo_receber.sacado_pessoa.nome_contato})`
          }
        }
      } else if (item.titulo_pagar) {
        return item.titulo_pagar.favorecido_pessoa.nome_contato + `/ ` + item.observacao
      }
      return nomePessoa + `/ ` + item.observacao
    },

    sortTable (response) {
      // this.$store.commit('movimentacaoConta/SET_ORDER_BY', response.detail)
      // this.filtrar()

      this.sort(response.detail)
    },

    sort(response) {
      console.log(response)
      if(this.lista.length <= 1) {
        return
      }

      const listaOrdenada = Object.values(this.lista).sort((a,b) => {
        let result = 0
        switch (response.order) {
          case 'ch.numero':
              result = (a.operacao == "D" && b.operacao == "C") ? 1 : -1
            break;
          case 'movimento.data_deposito':
              result = Date.parse(moment(a.data_deposito, "YYYY-MM-DD hh:mm:ss").toISOString()) - Date.parse(moment(b.data_deposito, "YYYY-MM-DD hh:mm::ss").toISOString())
            break;
          case 'formaPagamento.descricao':
            if(!a.forma_pagamento.descricao.attr){
                result = a.forma_pagamento.descricao.localeCompare(b.forma_pagamento.descricao)
              } else {
                result = a.forma_pagamento.descricao.attr.localeCompare(b.forma_pagamento.descricao.attr)
              }
            break;
          case 'movimento.valor_lancamento':
              result = (parseFloat(a.valor_lancamento.replace(',','.')) - parseFloat(b.valor_lancamento.replace(',','.')))
            break;
          default:
            break;
        }
        return result * (response.direcao == 'DESC' ? -1 : 1)
      }).map( el => el)
      console.log(listaOrdenada)
      this.$store.commit('movimentacaoConta/SET_PAGINA_ATUAL', 1)
      this.$store.commit('movimentacaoConta/SET_LISTA', listaOrdenada)
      this.$forceUpdate()
    },

    formatarValor(valor) {
      if((valor === undefined) || (valor === '')) { 
        return '' 
      } 
      // else {
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

    exportarParaExcel() {
      //  const total = this.list.reduce((acc, item) => acc + item.valor, 0);
      this.list.push({ id: '', Tipo: '', Origem: '' });
      this.list.push({ id: '', Tipo: '', Origem: '' });
      this.list.push({ id: 'Entradas: ', operacao:this.formatarValor(this.totalEntradas), Origem: ''});
      this.list.push({ id: 'Saida: ', operacao: this.formatarValor(this.totalSaidas), Origem: ''});
      // console.log(this.list);
      const wb = XLSX.utils.book_new();
 
      const ws = XLSX.utils.json_to_sheet(this.lista.map(item => ({
        Extrato: item.id,
        'Tipo': item.operacao,        
        'Titulo': item.titulo_receber?.id ?? '',        
        'Data de Movimento': this.formatarDataIsoParaBr(item.data_movimento),
        Origem:  item.Origem != '' ? this.nomePessoaDisplay(item) : '',
        'Forma de Pagamento':  item.forma_pagamento ? item.forma_pagamento.descricao : '',
        'Valor Título': this.formatarValor(item.valor_titulo),
        'Valor Lançamento': this.formatarValor(item.valor_lancamento != null ? item.valor_lancamento : ''),
         
      })));

      XLSX.utils.book_append_sheet(wb, ws, 'Planilha1');

      XLSX.writeFile(wb, 'Extrato.xlsx');

      // Remover a linha de total da lista após exportar
      this.list.pop();
    },

    filtrar () {
      this.toggleAll(false)

      this.$store.commit('movimentacaoConta/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('movimentacaoConta/listar')
    },

    carregarMais () {
      this.$store.dispatch('movimentacaoConta/listar')
    },

    incluir () {

      this.$refs.formIncluir.show()

      //espera a tela carregar, se der problema implementar um callback
        setTimeout(() => {
            EventBus.$emit('form-incluir:abrir')
      }, 500)
     
    },

    editar (item) {
      this.$refs.formEditar.show()
      EventBus.$emit('form-editar:abrir', item)
    },

    transferir () {
      this.$refs.formTransferir.show()
      EventBus.$emit('form-transferir:abrir')
    },

    conciliar (item) {
      this.selected = [item]
      this.conciliarVarios()
    },

    conciliarVarios () {
      this.$refs.formConciliarVarios.show()
      EventBus.$emit('form-conciliar:abrir', this.selected)
    },

    estornar (item) {
      this.selected = [item]
      this.$refs.formEstornar.show()
      setTimeout(() => {
        EventBus.$emit('form-estornar:abrir', item)
      }, 1)
    },

    transferirExistente (item) {
      this.selected = [item]
      this.$refs.formTransferirExistente.show()
      EventBus.$emit('form-transferir-existente:abrir', item)
    },

    trocaClasseTabela (classe) {
      this.className = classe
    },

    limparFormIncluir () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_ITEM_SELECIONADO')
    },

    limparFormEditar () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_ITEM_SELECIONADO')
    },

    limparFormConciliarVarios () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_CONCILIAR_VARIOS')
    },

    limparFormTransferir () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_TRANSFERIR')
    },

    limparFormEstornar () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_ESTORNAR')
    },

    limparFormTransferirExistente () {
      this.selected = []
      this.$store.commit('movimentacaoConta/LIMPAR_TRANSFERIR_EXISTENTE')
    },

    toggleAll (checked) {
      if (checked) {
        const list = this.list.slice().filter(item => item.conciliado === 'N')
        this.selected = list
        return
      }

      this.selected = []
    }
  }
}
</script>

<style lang="scss" scoped>
.table-sm .coluna-icones {
  max-width: 95px;
}
</style>
