<template>
  <div>
    <div v-if="!sendingFile">
      Arquivo: <b>{{ nomeArquivoSelecionado }}</b>
      <b-button variant="link" type="reset">Escolher outro</b-button>

      <div>
        <!-- <b-table :fields="workbookTableFields" :items="workbookData" class="mt-2 table-scroll table" hover /> -->

        <table class="table-scroll mobile-cards table b-table table-hover">
          <thead>
            <tr>
              <th>Nome da tabela</th>
              <th>Quantidade de registros</th>
            </tr>
          </thead>
          <tbody>
            <perfect-scrollbar>
              <tr v-for="item in workbookData" :key="item.id">
                <td data-label="Nome da tabela">{{ item.nome }}</td>
                <td data-label="Quantidade de registros">{{ item.quantidadeRegistros }}</td>
              </tr>
              <!-- <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
                <p>Nenhum resultado encontrado.</p>
              </div> -->
            </perfect-scrollbar>
          </tbody>
        </table>

        <div class="text-center">
          <b-button type="button" variant="azul" block @click.prevent="sendFile()">Processar tabelas</b-button>
        </div>
      </div>
    </div>
    <!-- <div v-else class="process-spinner">
      <font-awesome-icon icon="spinner" spin /> Processando tabelas...
    </div> -->
    <div v-else class="process-spinner">
      <load-placeholder :loading="sendingFile" :color-a="'#7d7e7f'" :color-b="'#7d7e7f'" />
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  data () {
    return {
      workbookTableFields: {
        nome: {
          label: 'Nome da tabela',
          sortable: true
        },
        quantidadeRegistros: {
          label: 'Quantidade de registros',
          sortable: true,
          tdClass: 'text-right'
        }
      }
    }
  },
  computed: mapState('importador', ['sendingFile', 'nomeArquivoSelecionado', 'workbookData']),
  methods: {
    ...mapActions('importador', ['enviarArquivo']),
    ...mapMutations('importador', ['setStep', 'setSendingFile', 'setResultSet']),

    sendFile () {
      this.setSendingFile(true)

      this.enviarArquivo()
        .then(data => {
          console.log(data)
          this.setStep(2)
          this.setSendingFile(false)
        })
        .catch(err => {
          console.error(err)
          this.setSendingFile(false)
        })
    }
  }
}
</script>
<style scoped>
@media (max-width: 768px){
  .table.mobile-cards tr {
    border: 1px solid #a4b7c1;
  }
  .table.mobile-cards td:first-child {
    border-top: 0;
  }
  .table.mobile-cards tr:hover td {
    border-color: #a4b7c1;
  }
}
</style>
