<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <div class="form-loading">
        <load-placeholder :loading="diarioCarregando || turmaAulaCarregando" />
      </div>

      <!-- CABEÇALHO TURMA -->
      <div class="d-flex justify-content-between align-items-end body-sector mb-2 p-2">
        <div class="col-12 row m-0 p-0">
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_turma'" for="turma-aula-turma" class="col-form-label">Turma</label>
            <input id="turma-aula-turma" v-model="turmaSelecionada.descricao" type="text" class="form-control" readonly>
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_livro'" for="turma-aula-livro" class="col-form-label">Livro</label>
            <input id="turma-aula-livro" v-model="turmaSelecionada.livro.descricao" type="text" class="form-control" readonly>
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_sala'" for="turma-aula-sala" class="col-form-label">Sala</label>
            <input id="turma-aula-sala" :value="descricaoSala" type="text" class="form-control" readonly>
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_instrutor'" for="turma-aula-instrutor" class="col-form-label">Instrutor</label>
            <input id="turma-aula-instrutor" :value="apelidoInstrutor" type="text" class="form-control" readonly>
          </b-col>

          <b-col sm="4" md="">
            <b-btn variant="azul" class="mt-4 btn-block" @click="abrirHistorico()">Histórico de aulas</b-btn>
          </b-col>
          <b-col sm="4" md="">
            <b-btn variant="roxo" class="mt-4 btn-block" @click="abrirAvaliacoes()">Avaliações</b-btn>
          </b-col>
          <b-col sm="4" md="">
            <b-btn variant="roxo" class="mt-4 btn-block" @click="abrirInfluxDollar()">Influx Dollar</b-btn>
          </b-col>
        </div>
      </div>

      <div class="content-sector sector-primary content-flex-column p-3">
        <div class="form-group row">
          <b-col md="2">
            <label v-help-hint="'form-diario-classe_data_aula'" for="data-aula" class="col-form-label">Data da aula</label>
            <g-datepicker :value="turmaAulaSelecionada.data_aula" :selected="setDataAula" element-id="data_aula" maxlength="10" required/>
          </b-col>

          <b-col md="4">
            <label v-help-hint="'form-diario-classe_livro'" for="licao-planejada" class="col-form-label">Lição planejada</label>

            <g-select
              id="turma-aula-funcionario"
              :value="licaoPlanejada"
              :select="setLicaoPlanejada"
              :options="listaLicoesPlanejadas"
              class="multiselect-truncate"
              label="descricao"
              track-by="id" />

          </b-col>

        </div>

        <div class="form-group row">
          <b-col md="12">
            <label v-help-hint="'form-lancamento-licao_aplicada'" for="licao-aplicada" class="col-form-label">Lição aplicada *</label>
            <g-select
              id="licao-aplicada"
              :multi-tag="true"
              :value="tagsLicoesAplicadas"
              :select="setTagLicoesAplicadas"
              :options="listaLicoesPlanejadas"
              :class="!isValid && $v.tagsLicoesAplicadas.$invalid ? 'invalid-input' : 'valid-input'"
              class="multiselect-truncate g-multiselect-tag"
              label="descricao"
              track-by="id" />
            <div v-if="!isValid && $v.tagsLicoesAplicadas.$invalid" class="multiselect-invalid">
              Selecione uma opção!
            </div>
          </b-col>
        </div>

        <!-- FREQUENCIA -->
        <div class="content-sector-extra p-2 mb-2">

          <div class="table-responsive-sm">
            <g-table id="listagem-alunos">
              <thead>
                <tr>
                  <th data-column="">Aluno</th>
                  <th data-column="" class="size-150">Frequência</th>
                  <th data-column="" class="size-210">
                    Homework
                    <div id="ct-legenda">
                      <div class="py-1">
                        <div data-ct="A">
                          <div v-b-tooltip title="Entregue" data-interesse="E"></div>
                          <div v-b-tooltip title="Não entregue" data-interesse="NE"></div>
                          <div v-b-tooltip title="Entregue com atraso" data-interesse="EA"></div>
                        </div>
                      </div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <perfect-scrollbar>
                  <div v-if="turmaAulaCarregando" class="d-flex h-100">
                    <load-placeholder :loading="turmaAulaCarregando" />
                  </div>
                  <div v-if="!listaAlunoPresenca.length && !turmaAulaCarregando" class="busca-vazia">
                    <p>Nenhum resultado encontrado.</p>
                  </div>

                  <tr v-for="(item, index) in listaAlunoPresenca" :key="index">

                    <td data-label="Aluno"><span>{{ item.nome }}</span></td>
                    <td data-label="Frequência" class="size-150">
                      <b-form-checkbox-group :id="`diario_presenca-${index}`" v-model="item.presenca" name="diario_presenca" class="check-presenca">
                        <b-form-checkbox :disabled="item.presenca === 'P' || item.reposicao_aula" value='P'>P</b-form-checkbox>
                        <b-form-checkbox :disabled="item.presenca === 'F' || item.reposicao_aula" value='F'>F</b-form-checkbox>
                      </b-form-checkbox-group>
                      <div class="check-presenca">
                         <b-form-checkbox :disabled="true" v-model="item.reposicao_aula" >R</b-form-checkbox>
                      </div>
                      </b-form-checkbox-group>
                                      
                    </td>
                    <td data-label="Homework" class="size-210">
                      <span class="check-homework">
                        CA
                        <b-form-checkbox-group :id="`diario_atividade_ca-${index}`" v-model="item.atividade_ca" name="diario_atividade_ca">
                          <b-form-checkbox value="E"/>
                          <b-form-checkbox value="NE"/>
                          <b-form-checkbox value="EA"/>
                        </b-form-checkbox-group>
                      </span>
                      <span class="check-homework">
                        CE
                        <b-form-checkbox-group :id="`diario_atividade_ce-${index}`" v-model="item.atividade_ce" name="diario_atividade_ce">
                          <b-form-checkbox value="E"/>
                          <b-form-checkbox value="NE"/>
                          <b-form-checkbox value="EA"/>
                        </b-form-checkbox-group>
                      </span>
                    </td>
                  </tr>
                </perfect-scrollbar>
              </tbody>
            </g-table>
          </div>
        </div>

        <div class="form-group row">
          <b-col md="4">
            <label v-help-hint="'form-diario-classe_instrutor_substituto'" for="turma-aula-instrutor-substituto" class="col-form-label">Instrutor substituto</label>
            <g-select
              id="turma-aula-instrutor-substituto"
              :value="instrutorSubstituto"
              :select="setInstrutorSubstituto"
              :options="listaInstrutores"
              class="multiselect-truncate"
              label="apelido"
              track-by="id" />
          </b-col>
        </div>

        <div class="form-group row flex-grow-1">
          <b-col md="12">
            <label v-help-hint="'form-diario-classe_observacoes'" for="observacaoes" class="col-form-label">Observações</label>
            <textarea id="observacaoes" v-model="turmaAulaSelecionada.observacao" class="form-control full-textarea" placeholder="" maxLength="5000"></textarea>
            <span class="text-secondary">Limite de caracteres: {{ 5000 - (turmaAulaSelecionada.observacao ? turmaAulaSelecionada.observacao.length : 0) }}</span>
          </b-col>
        </div>
      </div>

      <div class="form-group pt-2 mb-0">
        <b-btn :disabled="isEnviando" variant="verde" @click="isEdit = true, salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
        <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

        <b-btn variant="link" @click="voltar()">Voltar</b-btn>
      </div>
    </form>

    <!-- HISTÓRICO -->
    <b-modal id="historico" v-model="historicoModal" size="xl" stacking centered no-close-on-backdrop hide-header hide-footer>
      <historico-aulas ref="componenteHistoricoAulas" :is-personal="false" @fecharModal="fecharHistorico" @carregarAulaCallback="carregarAula" />
    </b-modal>

    <!-- AVALIAÇÕES -->
    <b-modal id="avaliacoes" v-model="avaliacoesModal" size="xl" stacking centered no-close-on-backdrop hide-header hide-footer>
      <avaliacoes-alunos ref="componenteAvaliacoesAlunos" :is-personal="false" @fecharModal="fecharAvaliacoesAlunos"/>
    </b-modal>

    <!-- InfluxDolar -->
    <b-modal id="influxDollar" v-model="atividadesInfluxDollarModal" size="xl" modal-class="teste-modal" centered no-close-on-backdrop hide-header hide-footer>
      <atividades-dollar-influx ref="componenteAtividadeInfluxDollar" :is-personal="false" :turma-id="turmaSelecionada.id" @fecharModal="fecharAtividadeInfluxDollar"/>
    </b-modal>
  </div>

</template>

<script>
import {required} from 'vuelidate/lib/validators'
import {mapState, mapMutations, mapActions} from 'vuex'
import {stringToISODate, dateToString} from '../../utils/date'
import HistoricoAulas from './HistoricoAulas.vue'
import AvaliacoesAlunos from './AvaliacoesAlunos.vue'
import AtividadesDollarInflux from './AtividadesDollarInflux.vue'

export default {
  name: 'FormularioDiarioClasse',
  components: {
    HistoricoAulas,
    AvaliacoesAlunos,
    'atividades-dollar-influx': AtividadesDollarInflux
  },
  data () {
    return {
      showPopOverColumns: false,
      showPopOverSubtitle: false,
      isEnviando: false,
      isEdit: false,
      isValid: true,
      estaCarregando: false,
      reposicao_aula: false,
      observacaoes: '',
      tagsLicoesAplicadas: [],
      listaFrequencia: [],
      aluno_frequencia: '',
      diarioCarregando: true,
      listaDeAlunos: [],
      turmaSelecionada: {
        descricao: {},
        livro: {},
        sala_franqueada: {sala: {}},
        funcionario: {},
        turmaAulas: {}
      },

      turmaAulaSelecionada: {
        id: null,
        data_aula: null,
        instrutor: null,
        observacao: ''
      },

      licaoAplicadaHomework: null,
      listaLicoesAplicadasHomework: [],

      data_aula: null,

      licaoPlanejada: null,
      listaLicoesPlanejadas: [],

      listaAlunoPresenca: [],

      instrutor: null,
      instrutorSubstituto: {id: null, apelido: 'Selecione'},

      historicoModal: false,
      avaliacoesModal: false,
      atividadesInfluxDollarModal: false
    }
  },
  computed: {
    ...mapState('turmaAula', {turmaAulaCarregando: 'estaCarregando'}),
    ...mapState('turma', {turma: 'itemSelecionado'}),
    ...mapState('funcionario', ['lista']),

    listaInstrutores: {
      get () {
        const empty = [{id: null, apelido: 'Selecione'}]
        return empty.concat(this.$store.state.funcionario.lista.filter(item => item.instrutor && (!this.turmaSelecionada.funcionario || this.turmaSelecionada.funcionario.id !== item.id)))
      }
    },

    descricaoSala: {
      get () {
        return this.turmaSelecionada.sala_franqueada && this.turmaSelecionada.sala_franqueada.sala ? this.turmaSelecionada.sala_franqueada.sala.descricao : ''
      }
    },

    apelidoInstrutor: {
      get () {
        return this.turmaSelecionada.funcionario ? this.turmaSelecionada.funcionario.apelido : ''
      }
    }
  },
  mounted () {
    this.buildDiario()
  },
  validations: {
    tagsLicoesAplicadas: {required}
  },
  methods: {
    ...mapMutations('turma', {SET_TURMA_ID: 'SET_ITEM_SELECIONADO_ID', LIMPAR_TURMA: 'LIMPAR_ITEM_SELECIONADO'}),
    ...mapMutations('licao', ['SET_LIVRO_SELECIONADO_ID']),
    ...mapActions('alunoDiario', {lancarAtualizarNotas: 'lancarAtualizarNotas', lancarAtualizarFrequencias: 'lancarAtualizarFrequencias'}),
    ...mapActions('turmaAula', {buscarLicoesRealizadas: 'buscarLicoesRealizadas'}),
    ...mapActions('turma', {buscarDiario: 'buscarDiario'}),
    ...mapMutations('lancamentoFrequencia', ['LIMPAR_ITEM_SELECIONADO']),

    fecharAvaliacoesAlunos () {
      this.avaliacoesModal = false
    },

    fecharAtividadeInfluxDollar () {
      this.atividadesInfluxDollarModal = false
      this.$refs.componenteAtividadeInfluxDollar.limparState()
    },

    montaLicaoAplicadas (turma) {
      this.listaDeAlunos = Object.assign([], turma.turmaAulas)
      this.listaLicoesPlanejadas = turma.turmaAulas.map(aula => {
        if (!this.licaoPlanejada && !aula.finalizada) {
          this.setLicaoPlanejada(aula.licao)
        }

        return aula.licao
      })

      this.listaAlunoPresenca = []
      this.setAlunoDiario(turma)
      this.instrutor = this.turma.funcionario
      this.setInstrutor()
    },

    recarregarDiario (fCallback) {
      this.buscarDiario().then(turma => {
        turma.turmaAulas = turma.turmaAulas.filter(aula => {
          return aula.licao.modalidade === 'V'
        })
        this.turmaSelecionada = turma
        if (fCallback) {
          fCallback(turma)
        }
        this.diarioCarregando = false
      })
    },

    buildDiario () {
      this.diarioCarregando = true
      this.isValid = true

      this.resetDiario()

      if (!this.$store.state.funcionario.estaCarregando) {
        this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
        this.$store.commit('funcionario/SET_LISTA', [])
        this.$store.dispatch('funcionario/listar')
      }

      const id = this.$route.params.id

      this.SET_TURMA_ID(id)

      this.licaoPlanejada = null
      this.recarregarDiario(this.montaLicaoAplicadas)
    },

    dateToString: dateToString,

    setInstrutorSubstituto (value) {
      this.instrutorSubstituto = value
      this.setInstrutor()
    },

    setAlunoDiario (turma) {
      this.listaAlunoPresenca = []
      let contratosValidos = turma.contratos.filter(contrato => contrato.situacao === 'V' || contrato.situacao === 'E' || (contrato.situacao === 'E' && turma.situacao === 'ENC'))
      if (this.tagsLicoesAplicadas.length > 0){
        let contratosTrancados = turma.contratos.filter(contrato => contrato.situacao === 'T' || (contrato.situacao === 'E' && turma.situacao === 'ENC'))
          contratosValidos = contratosValidos.concat(contratosTrancados);
      }
      contratosValidos.map(contrato => {
        let aluno = contrato.aluno
        const alunoDiarioAtual = {
          aluno: aluno.id,
          contrato: contrato.id,
          nome: aluno.pessoa.nome_contato,
          presenca: 'P',
          atividade_ca: 'false',
          atividade_ce: 'false',
          reposicao_aula: false,
          curso: turma.curso.id,
          sala_franqueada: !!turma.sala_franqueada && !!turma.sala_franqueada.id ? turma.sala_franqueada.id : null,
          livro: turma.livro.id
        }

        let alunoDiarioTurmaAulaSelecionada = aluno.alunoDiarios.find(item => item.turma_aula.id === this.turmaAulaSelecionada.id)
 
        if (alunoDiarioTurmaAulaSelecionada) {
          alunoDiarioAtual.id = alunoDiarioTurmaAulaSelecionada.id
          alunoDiarioAtual.presenca = alunoDiarioTurmaAulaSelecionada.presenca
          alunoDiarioAtual.atividade_ca = alunoDiarioTurmaAulaSelecionada.atividade_ca
          alunoDiarioAtual.atividade_ce = alunoDiarioTurmaAulaSelecionada.atividade_ce
          alunoDiarioAtual.reposicao_aula = alunoDiarioTurmaAulaSelecionada.reposicao_aula
        }

        this.listaAlunoPresenca.push(alunoDiarioAtual)
      })
      this.listaAlunoPresenca.sort((a, b) => {
        let fa = a.nome.toLowerCase()
        let fb = b.nome.toLowerCase()
        if (fa < fb) return -1
        if (fa > fb) return 1
        return 0
      })
    },

    setInstrutor () {
      this.turmaAulaSelecionada.instrutor = this.instrutorSubstituto.id ? this.instrutorSubstituto : this.instrutor
    },

    setDataAula (value) {
      this.turmaAulaSelecionada.data_aula = value
    },

    setTagLicoesAplicadas (value) {
      let possuiaLicao = false
      for (let i = 0; i < this.tagsLicoesAplicadas.length; i++) {
        if (this.tagsLicoesAplicadas[i].id === value.id) {
          this.tagsLicoesAplicadas.splice(i, 1)
          possuiaLicao = true
        }
      }
      if (!possuiaLicao) {
        this.tagsLicoesAplicadas.push(value)
      }
    },

    setLicaoPlanejada (value) {
      console.log(value)
      this.licaoPlanejada = null
      this.isValid = true
      this.turma.turma_aula = null
      const turmaAula = this.listaDeAlunos.find(aula => aula.licao.id === value.id)
      this.licaoPlanejada = value

      this.turmaAulaSelecionada.id = turmaAula.id
      this.turmaAulaSelecionada.data_aula = turmaAula.data_aula
      this.turmaAulaSelecionada.observacao = turmaAula.observacao

      this.setInstrutorSubstituto({id: null, apelido: 'Selecione'})

      if (turmaAula.funcionario && (!this.turmaSelecionada.funcionario || turmaAula.funcionario.id !== this.turmaSelecionada.funcionario.id)) {
        this.setInstrutorSubstituto(turmaAula.funcionario)
      }

      this.setAlunoDiario(this.turmaSelecionada)

      const data = {
        turma: this.turmaSelecionada.id,
        turma_aula: turmaAula.id
      }

      if (turmaAula.finalizada) {
        this.turma.turma_aula = data.turma_aula
      } 
      //   this.recarregarDiario(this.setAlunoDiario)
      this.buscarLicoesRealizadas(data).then(listaLicao => {
        this.tagsLicoesAplicadas = []

        if (!listaLicao.length) {
          this.isEdit = false
          return
        }

        this.isEdit = true
        listaLicao.map(licao => {
          this.setTagLicoesAplicadas(licao)
        })
      })
    },

    convertData (value) {
      return value.replace(/([A-D])?([-+])?.*/, '$1$2')
    },

    getLicoesAplicadas () {
      return this.tagsLicoesAplicadas.map(licao => licao.id)
    },

    salvarTurmaAula () {
      this.setInstrutor()

      let dadosAlunos = [...this.listaAlunoPresenca]
      dadosAlunos = dadosAlunos.map(aluno => {
        if (aluno.atividade_ca === 'false' || !aluno.atividade_ca) {
          aluno.atividade_ca = ''
        }
        if (aluno.atividade_ce === 'false' || !aluno.atividade_ce) {
          aluno.atividade_ce = ''
        }
        if (!aluno.sala_franqueada) {
          aluno.sala_franqueada = null
        }
        return aluno
      })

      const parametrosTurmaAula = {
        turma: this.turma.id,
        dados_alunos: dadosAlunos,
        turma_aula: this.turmaAulaSelecionada.id,
        licaos: this.getLicoesAplicadas(),
        observacao: this.turmaAulaSelecionada.observacao,
        funcionario: this.turmaAulaSelecionada && this.turmaAulaSelecionada.instrutor ? this.turmaAulaSelecionada.instrutor.id : null,
        data_aula: stringToISODate(this.turmaAulaSelecionada.data_aula, true)
      }

      return parametrosTurmaAula
    },

    voltar () {
      this.isEdit = false
      this.$store.commit('turmaAula/LIMPAR_ITEM_SELECIONADO')
      this.resetDiario()

      this.$router.push('/academico/turma')
    },

    salvar (saveClose) {
      this.isEnviando = true

      if (this.$v.$invalid) {
        this.isEdit = false
        this.isValid = false
        this.isEnviando = false
        return
      }

      this.lancarAtualizarFrequencias(this.salvarTurmaAula()).then(() => {
        if (saveClose) {
          this.voltar()
          return
        }

        this.buildDiario()

        this.isEnviando = false
      })
        .catch((error) => {
          console.info(error)
          this.isEnviando = false
        })
    },

    resetDiario () {
      this.$store.commit('alunoDiario/LIMPAR_CAMPOS')
      this.LIMPAR_TURMA()
      this.licaoAplicadaHomework = null
      this.tagsLicoesAplicadas = []
      this.listaLicoesAplicadasHomework = []
    },

    abrirHistorico () {
      this.licaoAplicadaHomework = null
      this.tagsLicoesAplicadas = []
      this.listaLicoesAplicadasHomework = []
      this.historicoModal = true
      setTimeout(() => {
        this.$refs.componenteHistoricoAulas.carregarHistoricoAulas()
      }, 1)
    },

    fecharHistorico () {
      this.historicoModal = false
      this.$refs.componenteHistoricoAulas.limparDados()
    },

    // Função passada como callback para HistoricoAulas
    carregarAula (licao) {
      this.licaoAplicadaHomework = null
      this.tagsLicoesAplicadas = []
      this.listaLicoesAplicadasHomework = []
      
      console.log('carregarAula') 
      setTimeout(() => {  
        this.setLicaoPlanejada(licao)
        this.historicoModal = false
      }, 600)
    },

    abrirAvaliacoes () {
      this.avaliacoesModal = true
      this.SET_LIVRO_SELECIONADO_ID(this.turmaSelecionada.livro.id)
      setTimeout(() => {
        this.$refs.componenteAvaliacoesAlunos.carregarAvaliacoes()
      }, 1)
    },

    abrirInfluxDollar () {
      this.atividadesInfluxDollarModal = true
      setTimeout(() => {
        this.$refs.componenteAtividadeInfluxDollar.carregarCamposDinamicos()
      }, 1)
    }
  }
}
</script>
<style scoped>
.check-homework {
  display: flex;
  align-items: center;
  line-height: 1;
  margin-right: .5rem;
}
.check-homework > div {
  padding-left: .5rem;
}

#ct-legenda > div {
  display: flex;
  justify-content: space-between;
}
#ct-legenda > div > div {
  display: flex;
  justify-content: flex-end;
}
#ct-legenda > div > div div {
  width: 12px;
  height: 12px;
  margin-left: 5px;
}
#ct-legenda div[data-ct='A'] div {
  background-color: #ea7124;
}
.ct-ativo.ct-card[data-interesse='E'],
#ct-legenda div[data-ct='A'] div[data-interesse='E'] {
  background-color: #00D1B2;
}
.ct-ativo.ct-card[data-interesse='NE'],
#ct-legenda div[data-ct='A'] div[data-interesse='NE'] {
  background-color: #FF3860;
}
.ct-ativo.ct-card[data-interesse='EA'],
#ct-legenda div[data-ct='A'] div[data-interesse='EA'] {
  background-color: #FFDD57;
}
.teste-modal {
  background-color: yellow;
}

#listagem-alunos {
  position: relative;
  min-height: 200px;
}

</style>
