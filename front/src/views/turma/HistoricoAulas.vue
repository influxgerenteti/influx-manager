<template>
  <div class="animated fadeIn">
    <h5 class="title-module mb-3">Histórico de aulas</h5>

    <div class="table-responsive-sm ">
      <g-table id="listagem-alunos">
        <thead>
          <tr>
            <th class="size-85">Data</th>
            <th>Lição</th>
            <th>Livro</th>
            <th class="size-85">Sala</th>
            <th>Instrutor</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar class="h-100">
            <div v-if="historicoCarregando" class="d-flex form-loading">
              <load-placeholder :loading="historicoCarregando" />
            </div>
            <div v-if="!listaHistoricoAulas.length && !historicoCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
            <tr v-for="item in listaHistoricoAulas" :key="item.id" @dblclick="carregarAula(retornaLicaoObjAgendamentoPersonalId(item), retornaIdContratoNull(item))">
              <td class="size-85"><span>{{ displayData(item) }}</span></td>
              <td :title="displayLicao(item)"><span>{{ displayLicao(item) }}</span></td>
              <td><span>{{ displayLivro(item) }}</span></td>
              <td class="size-85"><span>{{ displaySala(item) }}</span></td>
              <td><span>{{ displayInstrutor(item) }}</span></td>

              <td class="d-flex coluna-icones">
                <font-awesome-icon icon="pen" class="icone-link" title="Atualizar" @click="carregarAula(retornaLicaoObjAgendamentoPersonalId(item), retornaIdContratoNull(item))" />
              </td>
            </tr>

          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>
    <div class="form-group pt-2 mb-0">
      <b-btn variant="link" @click="$emit('fecharModal')">Fechar</b-btn>
    </div>
  </div>
</template>
<script>
import {mapState, mapActions} from 'vuex'
import {dateToString} from '../../utils/date'

export default{
  name: 'HistoricoAulas',

  props: {
    isPersonal: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      historicoCarregando: false,
      listaHistoricoAulas: []
    }
  },

  computed: {
    ...mapState('turma', {turmaId: 'itemSelecionadoID'})
  },
  methods: {
    ...mapActions('diarioPersonal', ['buscarHistoricoPorContrato']),
    ...mapActions('turmaAula', ['buscarHistoricoAulasPorTurma']),

    dateToString: dateToString,

    retornaIdContratoNull (item) {
      if (this.isPersonal && item.contrato) {
        return item.contrato.id
      }
      return null
    },

    retornaLicaoObjAgendamentoPersonalId (item) {
      if (this.isPersonal) {
        return item.id
      }
      return {id: item.licaoId, descricao: item.licao_descricao}
    },

    carregarHistoricoAulas () {
      if (this.isPersonal) {
        this.historicoCarregando = true
        this.buscarHistoricoPorContrato()
          .then(lista => {
            this.listaHistoricoAulas = lista
            this.historicoCarregando = false
          })
      } else {
        this.historicoCarregando = true
        this.buscarHistoricoAulasPorTurma(this.turmaId)
          .then(lista => {
            this.listaHistoricoAulas = lista
            this.historicoCarregando = false
          })
      }
    },

    limparDados () {
      this.historicoCarregando = false
      this.listaHistoricoAulas = []
    },

    carregarAula (licaoAgendamentoPersonalId, contratoIdNull) { 
        if (this.isPersonal) {
          this.$emit('carregarAulaCallback', licaoAgendamentoPersonalId, contratoIdNull)
        } else {
          this.$emit('carregarAulaCallback', licaoAgendamentoPersonalId)
        }
    },

    displayData (aula) {
      const dataAula = this.isPersonal ? aula.alunoDiarioPersonal.data_aula : aula.turma_aula_data_aula
      return dateToString(new Date(dataAula))
    },

    displayLicao (aula) {
      if (this.isPersonal) {
        return aula.alunoDiarioPersonal.aluno_diario_personal_licao.map(item => (item.descricao)).join(', ')
      } else {
        if (aula.licoes_aplicadas && aula.licoes_aplicadas.length) {
          let descricaoLicoesAplicadas = ''
          aula.licoes_aplicadas.forEach(licaoAplicada => {
            if (descricaoLicoesAplicadas !== '') {
              descricaoLicoesAplicadas += ' & '
            }
            descricaoLicoesAplicadas += licaoAplicada.descricao
          })
          return descricaoLicoesAplicadas
        }
        return aula.licao_descricao
      }
    },

    displayLivro (aula) {
      if (this.isPersonal) {
        return aula.alunoDiarioPersonal.livro.descricao
      } else {
        return aula.livro_descricao
      }
    },

    displaySala (aula) {
      if (this.isPersonal) {
        return aula.alunoDiarioPersonal.sala_franqueada && aula.alunoDiarioPersonal.sala_franqueada.sala ? aula.alunoDiarioPersonal.sala_franqueada.sala.descricao : ''
      } else {
        return aula.sala_descricao
      }
    },

    displayInstrutor (aula) {
      if (this.isPersonal) {
        return aula.alunoDiarioPersonal.funcionario ? aula.alunoDiarioPersonal.funcionario.apelido : ''
      } else {
        return aula.funcionario_apelido
      }
    }
  }
}
</script>
<style scoped>
#listagem-alunos {
  position: relative;
  min-height: 200px;
}
</style>
