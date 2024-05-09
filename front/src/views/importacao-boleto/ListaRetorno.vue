<template>
  <div class="animated fadeIn">
    <div>
      <div class="form-group row">
        <div class="d-flex">
          <div class="m-2">
            <b-btn :disabled="estaCarregando" type="button" variant="azul" @click="uploadFile()">
              <font-awesome-icon icon="upload"/>
              Buscar arquivo
            </b-btn>
          </div>

          <div class="m-2">
            <input v-model="arquivo.name" type="text" disabled placeholder="Buscar um arquivo" class="form-control">
          </div>
          <div class="m-2">
            <b-btn :disabled="estaCarregando" type="button" variant="link" @click="limpar()">Limpar</b-btn>
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className">
        <thead class="text-dark">
          <tr>
            <th data-column="" class="size-75">Contrato</th>
            <th data-column="">Sacado</th>
            <th data-column="" class="size-95">Data vencimento</th>
            <th data-column="" class="size-210">Categoria</th>
            <th data-column="">Valor título</th>
            <th data-column="">Valor pago</th>
            <th data-column="">Situação </th>
            <!--<th class="coluna-icones"></th>-->
          </tr>
        </thead>

        <tbody ref="scroll-wrap">
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in listaItens" :key="item.id">
              <td data-label="Contrato" class="size-75">{{ contrato(item) }}</td>
              <td data-label="Sacado">{{ sacado(item) }}</td>
              <td data-label="Data vencimento" class="size-95">{{ dataVencimento(item) }}</td>
              <td data-label="Categoria" class="size-210">{{ categoria(item) }}</td>
              <td data-label="Valor título">R$ {{ valorTitulo(item) }}</td>
              <td data-label="Valor pago">R$ {{ valorPago(item) }}</td>
              <td data-label="Situação">{{ situacao(item) }}</td>
            <!-- <td class="d-flex coluna-icones">Ações</td>-->
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div class="d-flex">
        <!---->
        <div class="info-btn">
          <b-btn type="button" variant="azul" @click="verBoletosComErro = !verBoletosComErro">{{ verBoletosComErro ? 'Voltar para lista de boletos' : 'Ver boletos com erro' }}</b-btn>
        </div>
      </div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small>
            <template>{{ textoItensEncontrados }}</template>
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
export default {
  name: 'ListaImportacaoBoletoRetorno',
  filters: {

    getSituation (situation) {
      const listOfSituation = [
        {id: 1, text: 'Pendente de Envio', value: 'PEN'},
        {id: 2, text: 'Enviado', value: 'ENV'},
        {id: 3, text: 'Confirmado', value: 'CON'},
        {id: 4, text: 'Rejeitado', value: 'REJ'},
        {id: 5, text: 'Recebimento', value: 'REC'},
        {id: 6, text: 'Devolvido', value: 'DEV'},
        {id: 7, text: 'Cancelado', value: 'CAN'}
      ]

      return listOfSituation.find(s => s.value === situation)['text']
    },

    amountPaid (item) {
      const calc = (a, b) => a * 1 - b * 1
      return calc(item.titulo_receber.valor_original, item.titulo_receber.valor_saldo_devedor)
    }
  },
  data () {
    return {
      className: 'rapido-open',
      filtroImportador: true,
      arquivo: {name: ''},
      verBoletosComErro: false
    }
  },
  computed: {
    ...mapState('importacaoBoleto', {lista: 'lista',
      listaDeBoletos: 'listaDeBoletos',
      listaDeBoletosNE: 'listaDeBoletosNE',
      estaCarregando: 'estaCarregando'}),

    listaItens: {
      get () {
        return this.verBoletosComErro ? this.listaDeBoletosNE : this.listaDeBoletos.concat(this.listaDeBoletosNE)
      }
    },

    totalItens: {
      get () {
        return this.listaItens.length
      }
    },

    textoItensEncontrados: {
      get () {
        if (this.totalItens < 1) {
          return 'Nenhum item encontrado'
        }
        return `${this.totalItens} ite${this.totalItens > 1 ? 'ns' : 'm'} encontrado${this.totalItens > 1 ? 's' : ''}`
      }
    }
  },
  mounted () {
    this.limpar()
    this.limparStateAnterior()
  },
  methods: {
    ...mapActions('importacaoBoleto', {listar: 'listarAll', importarArquivo: 'importar'}),
    ...mapMutations('importacaoBoleto', ['LIMPAR_ITEM_SELECIONADO', 'SET_ARQUIVO']),

    limpar () {
      this.arquivo = {nome: ''}
    },

    importar () {
      this.SET_ARQUIVO(this.arquivo)
      this.importarArquivo()
    },

    uploadFile () {
      var input = document.createElement('input')
      input.type = 'file'
      input.accept = '.RET,.RST,.CRT'

      // Extensões .R[num] são os arquivos de retorno, que vão de 01 até a quantidade processada no dia
      const max = 20
      for (let i = 1; i <= max; i++) {
        input.accept += ',.R' + i.toString().padStart(2, '0')
      }

      input.onchange = e => {
        var file = e.target.files[0]
        var reader = new FileReader()
        reader.readAsText(file, 'UTF-8')

        reader.onload = readerEvent => {
          this.arquivo = file
          this.importar()
        }
      }

      input.click()
    },

    limparStateAnterior () {
      this.$store.commit('importacaoBoleto/LIMPAR_ITEM_SELECIONADO')
      this.$store.commit('importacaoBoleto/SET_LISTA_DE_BOLETOS', [])
      this.$store.commit('importacaoBoleto/SET_LISTA_DE_BOLETOS_NE', [])
    },

    contrato (item) {
      if (!item.foiProcessado) {
        return item.contrato
      }

      if (item.titulo_receber) {
        if (item.titulo_receber.conta_receber) {
          if (item.titulo_receber.conta_receber.contrato) {
            return item.titulo_receber.conta_receber.contrato.id
          }
        }
      }

      return ''
    },

    sacado (item) {
      if (!item.foiProcessado) {
        return item.nome_sacado
      }
      return item.titulo_receber.sacado_pessoa.nome_contato
    },

    dataVencimento (item) {
      if (!item.foiProcessado) {
        const dia = item.data_vencimento.substr(0, 2)
        const mes = item.data_vencimento.substr(2, 2)
        const ano = item.data_vencimento.substr(4, 4)
        return `${dia}/${mes}/${ano}`
      }
      return this.$options.filters.formatarData(item.data_vencimento)
    },

    categoria (item) {
      if (!item.foiProcessado) {
        return item.categoria
      }
      return item.titulo_receber.observacao
    },

    transformarNumeroEmCurrency (valor) {
      valor = parseFloat(valor)
      valor = valor.toFixed(2).replace('.', ',')
      return valor
    },

    valorTitulo (item) {
      if (!item.foiProcessado) {
        return this.transformarNumeroEmCurrency(item.valor_titulo)
      }
      return this.transformarNumeroEmCurrency(this.$options.filters.formatarNumero(item.valor))
    },

    valorPago (item) {
      if (!item.foiProcessado) {
        return this.transformarNumeroEmCurrency(item.valor_pago)
      }
      return this.transformarNumeroEmCurrency(this.$options.filters.formatarNumero(this.$options.filters.amountPaid(item)))
    },

    situacao (item) {
      if (!item.foiProcessado) {
        return item.situacao
      }
      return this.$options.filters.getSituation(item.situacao_cobranca)
    }
  }
}
</script>
<style scoped>
span.badge {
  font-size: 95%;
}

#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}

.filtro-header {
  color: #4a4a4a;
}

.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}

.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: .5rem;
  padding-bottom: .5rem;
  color: #3e515b;
  border-top: 1px solid #EBECF0;
}
</style>
