<template>
  <div v-if="resultSet && resultSet.resultado" class="step">
    <b-tabs card>
      <b-tab title="Resultados" active>
        <div v-for="(item, key) in resultSet.resultado" :key="key">
          {{ key }}: {{ item }}
        </div>
      </b-tab>

      <b-tab v-for="(tabela, nomeTabela) in resultSet.tabelas" :title="nomeTabela" :key="nomeTabela">
        <table class="table-scroll mobile-cards table table-borderless">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nome</th>
              <th>Sexo</th>
              <th>Data Nasc.</th>
            </tr>
          </thead>
          <tbody>
            <perfect-scrollbar>
              <tr v-for="(item, index) in tabela" :key="item.id">
                <td data-label="Código">{{ item.id }}</td>
                <td data-label="Nome" class="p-1">
                  <div class="col-md-8 p-0">
                    <input :value="item.nome" type="text" class="form-control" placeholder="Nome" @input="changeInputValue($event, nomeTabela, index, 'nome')">
                  </div>
                </td>
                <td data-label="Sexo" class="p-1">
                  <div class="col-md-8 p-0">
                    <input :value="item.sexo" type="text" class="form-control" placeholder="Sexo" @input="changeInputValue($event, nomeTabela, index, 'sexo')">
                  </div>
                </td>
                <td data-label="Data Nasc." class="p-1">
                  <div class="col-md-8 p-0">
                    <input :value="item.data_nascimento" type="text" class="form-control" placeholder="Sexo" @input="changeInputValue($event, nomeTabela, index, 'data_nascimento')">
                  </div>
                </td>
              </tr>
            </perfect-scrollbar>
          </tbody>

          <tfoot>
            <tr>
              <td class="text-center" colspan="4">
                <b-btn variant="azul" @click.prevent="submitEdits()">Salvar alterações</b-btn>
              </td>
            </tr>
          </tfoot>
        </table>
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'

export default {
  data () {
    return {
      localResult: null
    }
  },
  computed: mapState('importador', ['resultSet']),
  watch: {
    resultSet (value) {
      this.localResult = value
    }
  },
  created () {
    const data = {
      resultado: {
        'Inconsistencias': 122,
        'Alunos': 12,
        'Responsáveis': 2
      },
      tabelas: {
        'Alunos': [
          {id: 1, nome: 'Nome do aluno', sexo: 'M', data_nascimento: '1999-02-15'},
          {id: 2, nome: 'Nome da aluna', sexo: 'F', data_nascimento: '1999-02-20'}
        ],
        'Responsáveis': [
          {id: 1, nome: 'Resp do aluno', sexo: 'M', data_nascimento: '1999-02-15'},
          {id: 2, nome: 'Resp da aluna', sexo: 'F', data_nascimento: '1999-02-20'}
        ]
      }
    }

    this.setResultSet(data)
  },
  methods: {
    ...mapMutations('importador', ['setResultSet']),

    changeInputValue ($event, table, tableIndex, field) {
      this.localResult.tabelas[table][tableIndex][field] = $event.target.value
      this.setResultSet(this.localResult)
    },

    submitEdits () {
      console.log('salvando alterações')
    }
  }
}
</script>
<style scoped>
tfoot tr {
  display: flex;
}
tfoot td {
  flex: 1 1 0;
}
.col-md-8 {
  max-width: 100%;
}
@media (max-width: 768px){
  .col-md-8 {
    max-width: 50%;
  }
}
</style>
