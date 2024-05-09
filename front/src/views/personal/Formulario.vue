<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <div class="form-loading">
        <load-placeholder :loading="isLoading" />
      </div>
      <!-- CABEÇALHO TURMA -->
      <div class="d-flex justify-content-between align-items-end body-sector mb-2 p-2">
        <div class="col-12 row m-0 p-0">
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_turma'" for="diario-personal-agendamentos" class="col-form-label">Agendamentos</label>
            <input id="diario-personal-agendamentos" :value="descricaoAgendamentos" type="text" class="form-control" readonly>
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_livro'" for="diario-personal-livro" class="col-form-label">Livro</label>
            <g-select
              id="diario-personal-livro"
              :options="listaLivros"
              :value="diarioPersonal.livro"
              :select="setLivro"
              class="multiselect-truncate"
              label="descricao"
              track-by="id"
              style="max-width: 100%" />
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_sala'" for="diario-personal-sala" class="col-form-label">Sala</label>
            <input id="diario-personal-sala" v-model="diarioPersonal.sala_franqueada.sala.descricao" type="text" class="form-control" readonly>
          </b-col>
          <b-col sm="6" md="">
            <label v-help-hint="'form-diario-classe_instrutor'" for="diario-personal-instrutor" class="col-form-label">Instrutor</label>
            <input id="diario-personal-instrutor" v-model="diarioPersonal.funcionario.apelido" type="text" class="form-control" readonly>
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

       <template v-if="isEdit && todasAulasDadas">
        <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> Todas as lições foram aplicadas. Consulte o histórico para alterar ou verificar alguma lição aplicada.
        </div>
      </template>
      <template v-else> 
        <div id="diario-personal-frequencia" class="content-flex-column p-3">
          <div class="form-group row">
             <b-col md="2">

              <label v-help-hint="'form-diario-personal-data_aula'" for="data-aula" class="col-form-label">Data da aula</label>
              <g-datepicker :value="data_aula" :selected="setDataAula" element-id="data_aula" maxlength="10" required/>

            </b-col> 
            <b-col md="4">
              <label v-help-hint="'form-diario-personal-livro'" for="licao-planejada" class="col-form-label">Lição anterior</label>
              <g-select
                id="licao-planejada"
                :value="licaoAnterior"
                :select="setLicaoAnterior"
                :options="listaLicoesAnteriores"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />

            </b-col> 

          </div>

          <div class="form-group row">
             <b-col md="12">
              <label v-help-hint="'form-diario-personal-licao_aplicada'" for="licao-aplicada" class="col-form-label">Lição aplicada *</label>
              <g-select
                id="licao-aplicada"
                :multi-tag="true"
                :value="tagsLicoesAplicadas"
                :select="setTagLicoesAplicadas"
                :options="listaLicoesAplicadas"
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

            <div class="form-group row">
               <b-col md="">
                <label v-help-hint="'form-diario-personal-aluno'" for="diario-personal-aluno" class="col-form-label">Aluno</label>
                <input id="diario-personal-turma" v-model="diarioPersonal.aluno" type="text" class="form-control" readonly>
              </b-col> 

              <b-col md="auto">
                <label v-help-hint="'form-diario-personal-presenca'" for="diario-personal-presenca" class="col-form-label">Frequência</label>
                <b-form-checkbox-group id="diario_presenca" v-model="diarioPersonal.presenca" name="diario_presenca" class="check-presenca">
                  <b-form-checkbox :disabled="diarioPersonal.presenca === 'P'" value="P">P</b-form-checkbox>
                  <b-form-checkbox :disabled="diarioPersonal.presenca === 'F'" value="F">F</b-form-checkbox>
                </b-form-checkbox-group>
              </b-col> 

              <b-col md="auto">
                <label v-help-hint="'form-diario-personal-homework'" for="diario-personal-homework" class="col-form-label">Homework</label>
                <div id="ct-legenda">
                  <div class="py-1">
                    <div data-ct="A">
                      <div v-b-tooltip title="Entregue" data-interesse="E"></div>
                      <div v-b-tooltip title="Não entregue" data-interesse="NE"></div>
                      <div v-b-tooltip title="Entregue com atraso" data-interesse="EA"></div>
                    </div>
                  </div>
                </div> 

                 <div class="d-flex">
                  <span class="check-homework">
                    CA
                    <b-form-checkbox-group id="diario_atividade_ca" v-model="diarioPersonal.atividade_ca" name="diario_atividade_ca">
                      <b-form-checkbox value="E"/>
                      <b-form-checkbox value="NE"/>
                      <b-form-checkbox value="EA"/>
                    </b-form-checkbox-group>
                  </span>
                  <span class="check-homework">
                    CE
                    <b-form-checkbox-group id="diario_atividade_ce" v-model="diarioPersonal.atividade_ce" name="diario_atividade_ce">
                      <b-form-checkbox value="E"/>
                      <b-form-checkbox value="NE"/>
                      <b-form-checkbox value="EA"/>
                    </b-form-checkbox-group>
                  </span>
                </div> 
              </b-col>

            </div>

          </div>

          <div class="form-group row">
            <b-col md="4">
              <label v-help-hint="'form-diario-personal-instrutor_substituto'" for="diario-personal-instrutor-substituto" class="col-form-label">Instrutor substituto</label>
              <g-select
                id="diario-personal-instrutor-substituto"
                :options="listaInstrutores"
                v-model="instrutorSubstituto"
                :disabled="isEdit"
                class="multiselect-truncate"
                label="apelido"
                track-by="id" />
            </b-col>
          </div>

          <div class="form-group row flex-grow-1">
            <b-col md="12">
              <label v-help-hint="'form-diario-personal-observacoes'" for="observacoes" class="col-form-label">Observações</label>
              <textarea id="observacoes" v-model="diarioPersonal.observacao" class="form-control full-textarea" placeholder="" maxLength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - (diarioPersonal.observacao ? diarioPersonal.observacao.length : 0) }}</span>
            </b-col>
          </div>
        </div>

       <div class="form-group pt-2 mb-0">
          <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
          <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

          <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
        </div> 
      </template>

    </form>

    <!-- HISTÓRICO -->
    <b-modal id="historico" v-model="historicoModal" size="xl" stacking centered no-close-on-backdrop hide-header hide-footer>
      <historico-aulas ref="componenteHistoricoAulas" :is-personal="true" @fecharModal="fecharHistorico" @carregarAulaCallback="carregarAula" />
    </b-modal>

    <!-- AVALIAÇÕES -->
    <b-modal id="avaliacoes" v-model="avaliacoesModal" size="xl" stacking centered no-close-on-backdrop hide-header hide-footer>
      <avaliacoes-alunos ref="componenteAvaliacoesAlunos" :is-personal="true" @fecharModal="fecharAvaliacoesAlunos"/>
    </b-modal>

    <!-- InfluxDolar -->
    <b-modal id="influxDollar" v-model="atividadesInfluxDollarModal" size="xl" modal-class="teste-modal" centered no-close-on-backdrop hide-header hide-footer>
      <atividades-dollar-influx ref="componenteAtividadeInfluxDollar" :is-personal="true" :contrato-id="contratoSelecionadoID" @fecharModal="fecharAtividadeInfluxDollar"/>
    </b-modal>

  </div>
</template>

<script>
import {required} from 'vuelidate/lib/validators'
import {mapState, mapMutations, mapActions} from 'vuex'
import {dateToString, stringToISODate} from '../../utils/date'
import HistoricoAulas from '../turma/HistoricoAulas.vue'
import AvaliacoesAlunos from '../turma/AvaliacoesAlunos.vue'
import AtividadesDollarInflux from '../turma/AtividadesDollarInflux.vue'

export default {
  name: 'FormularioDiarioPersonal',
  components: {
    HistoricoAulas,
    AvaliacoesAlunos,
    'atividades-dollar-influx': AtividadesDollarInflux
  },
  data () {
    return {
      isLoading: false,

      isValid: true,
      isEdit: false,
      isEnviando: false,
      todasAulasDadas: false,
      atividadesInfluxDollarModal: false,

      idSelecionado: null, // id do contrato

      diarioPersonal: {
        descricao: '',
        livro: '',
        sala_franqueada: { sala: '' },
        funcionario: '',

         presenca: 'P',
        atividade_ca: 'false',
        atividade_ce: 'false',

        observacao: ''
      },

      data_aula: '',

      listaLicoesAnteriores: [],
      listaLicoesAplicadas: [],
      licaoAnterior: {id: null, descricao: 'Atual'},
      tagsLicoesAplicadas: [],
      agendamentoSelecionado: {},

      instrutorSubstituto: {id: null, apelido: 'Selecione'},

      historicoModal: false,

      avaliacoesModal: false,
      avaliacoesCarregando: false,

      aplicandoNotas: false,
      turmaNotas: [],

      avaliacaoConceitual: {
        nota_listening_1: '',
        nota_speaking_1: '',
        nota_writing_1: '',

        nota_listening_2: '',
        nota_speaking_2: '',
        nota_writing_2: ''
      },

      avaliacao: {
        nota_mid_term_oral: {id: null, descricao: ''},
        nota_mid_term_test: 0,
        nota_mid_term_composition: 0,
        nota_mid_term_escrita: 0,
        nota_retake_mid_term_oral: {id: null, descricao: ''},
        nota_retake_mid_term_escrita: 0,

        nota_final_oral: {id: null, descricao: ''},
        nota_final_test: 0,
        nota_final_composition: 0,
        nota_final_escrita: 0,
        nota_retake_final_oral: {id: null, descricao: ''},
        nota_retake_final_escrita: 0
      }
    }
  },
  computed: {
    ...mapState('personal', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('diarioPersonal', ['listaDiario', 'contratoSelecionadoID', 'listaLicoesRealizadas']),
    ...mapState('funcionario', {listaInstrutores: 'lista'}),
    ...mapState('livro', {listaLivros: 'lista'}),

    descricaoAgendamentos: {
      get () {
        if (!this.listaDiario || !this.listaDiario.creditosPersonal) {
             return ''
        }
            return this.listaDiario.creditosPersonal.saldo + '/' + this.listaDiario.creditosPersonal.quantidade + ' Créditos'
      }
    },

    listaInstrutores: {
      get () {
        const empty = [{id: null, apelido: 'Selecione'}]
        const lista = this.$store.state.funcionario.lista
         if (lista.length > 0) {
            return empty.concat(lista.filter(item => this.agendamentoSelecionado.funcionario && item.cargo.tipo !== 'ASG' && item.instrutor_personal && this.agendamentoSelecionado.funcionario.id !== item.id))
        }
  
        return empty
      }
    }
  },
  mounted () {
    this.idSelecionado = this.$route.params.id
    this.carregarDiario(this.idSelecionado)
  },
  validations: {
    tagsLicoesAplicadas: {required}
  },
  methods: {
    ...mapMutations('diarioPersonal', ['SET_CONTRATO_SELECIONADO_ID', 'SET_ESTA_CARREGANDO', 'SET_AGENDAMENTO_PERSONAL', 'LIMPAR_AGENDAMENTO_PERSONAL']),
    ...mapMutations('licao', ['SET_LIVRO_SELECIONADO_ID']),
    ...mapMutations('personal', ['SET_ITEM_SELECIONADO_ID']),
    ...mapMutations('livro', {SET_PAGINA_ATUAL_LIVROS: 'SET_PAGINA_ATUAL'}),
    ...mapActions('diarioPersonal', ['buscarDiarioPorContrato', 'buscarLicoesAplicadasPorContrato', 'lancarAtualizarFrequencias']),
    ...mapActions('licao', ['buscarLicoesPorLivro']),
    ...mapActions('livro', {listarLivros: 'listar'}),

    dateToString: dateToString,

    fecharAtividadeInfluxDollar () {
      this.atividadesInfluxDollarModal = false
      this.$refs.componenteAtividadeInfluxDollar.limparState()
    },

    buscarLicoesLivro (limparLicoesAplicadas) {
      
      this.buscarLicoesPorLivro()
        .then((listaLicoes) => {
          this.listaLicoesAplicadas = listaLicoes

          if (limparLicoesAplicadas) {
            this.tagsLicoesAplicadas = []
          } else if (this.isEdit) {
            this.agendamentoSelecionado.alunoDiarioPersonal.aluno_diario_personal_licao.map(item => {
              this.setTagLicoesAplicadas(item)
            })
          }
          this.isLoading = false
        })
    },

    abrirInfluxDollar () {
      this.atividadesInfluxDollarModal = true
      this.$refs.componenteAtividadeInfluxDollar.carregarCamposDinamicos()
    },

    carregarDiario (id) {
     this.isLoading = true

      this.SET_CONTRATO_SELECIONADO_ID(id)
      this.SET_PAGINA_ATUAL_LIVROS(1)
     this.listarLivros()

      this.buscarDiarioPorContrato()
        .then(() => {
          this.tagsLicoesAplicadas = []
          this.diarioPersonal.atividade_ca = 'false'
          this.diarioPersonal.atividade_ce = 'false'
          this.diarioPersonal.presenca = 'P'
          this.instrutorSubstituto = {id: null, apelido: 'Selecione'}
          this.diarioPersonal.observacao = ''

          this.agendamentoSelecionado = this.listaDiario.agendamentoPersonals.find(item => (item.finalizado === false))
       

          if (this.isEdit) {
          
            this.agendamentoSelecionado = this.listaDiario.agendamentoPersonals.find(item => (item.finalizado === true))
            this.todasAulasDadas = this.agendamentoSelecionado.length > 1

            if (this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca) {
              this.diarioPersonal.atividade_ca = this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca
            }
            if (this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ca) {
              this.diarioPersonal.atividade_ce = this.agendamentoSelecionado.alunoDiarioPersonal.atividade_ce
            }

            this.diarioPersonal.presenca = this.agendamentoSelecionado.alunoDiarioPersonal.presenca
            
            const funcionario = this.agendamentoSelecionado.alunoDiarioPersonal.funcionario
            this.instrutorSubstituto = this.agendamentoSelecionado.funcionario.id !== funcionario.id ? funcionario : {id: null, apelido: 'Selecione'}
            this.diarioPersonal.observacao = this.agendamentoSelecionado.alunoDiarioPersonal.observacao
          }

          if(!this.agendamentoSelecionado){
            this.agendamentoSelecionado = this.listaDiario.agendamentoPersonals.find(item => 
            (item.finalizado === true))
          }

          if (this.agendamentoSelecionado.alunoDiarioPersonal && this.agendamentoSelecionado.alunoDiarioPersonal.data_aula) {
            this.data_aula = this.agendamentoSelecionado.alunoDiarioPersonal.data_aula
          } else {
            let reagendamento = false
            if (this.agendamentoSelecionado.reagendado) {
              reagendamento = this.agendamentoSelecionado.datasReagendamentoPersonals.find(reag => reag.ultimo_reagendamento === true) || false
            }
            this.data_aula = reagendamento === false ? this.agendamentoSelecionado.inicio : reagendamento.data_reagendada
          }

          if (this.isEdit && this.agendamentoSelecionado.alunoDiarioPersonal && this.agendamentoSelecionado.alunoDiarioPersonal.livro) {
            this.listaDiario.livro = this.agendamentoSelecionado.alunoDiarioPersonal.livro
          }
          this.diarioPersonal.livro = this.listaDiario.livro
          this.diarioPersonal.sala_franqueada = this.agendamentoSelecionado.sala_franqueada
          this.diarioPersonal.funcionario = this.agendamentoSelecionado.funcionario
          this.diarioPersonal.aluno = this.listaDiario.aluno.pessoa.nome_contato
     
          this.buscarLicoesAplicadasPorContrato().then(lista => {
        
            this.configuraListaLicoesAnteriores(this.listaLicoesRealizadas)
            if (this.isEdit) {
              this.licaoAnterior = this.listaLicoesRealizadas.find(item => (item.id === this.agendamentoSelecionado.id))
            }
          })
          this.SET_LIVRO_SELECIONADO_ID(this.listaDiario.livro.id)
          this.buscarLicoesLivro(false)

          if (!this.$store.state.funcionario.estaCarregando) {
            this.$store.commit('funcionario/SET_PAGINA_ATUAL', 1)
            this.$store.commit('funcionario/SET_LISTA', [])
            this.$store.dispatch('funcionario/listar')
          }
        })
    },

    setDataAula (value) {
      this.data_aula = value
    },

    setLivro (value) {
      this.diarioPersonal.livro = value
      this.listaDiario.livro = value
      this.SET_LIVRO_SELECIONADO_ID(value.id)
      this.buscarLicoesLivro(true)
    },

    configuraListaLicoesAnteriores (listaLicoesRealizadasAnteriormente) {
      this.listaLicoesAnteriores = [{id: null, descricao: 'Selecione'}]

      if (listaLicoesRealizadasAnteriormente) {
        listaLicoesRealizadasAnteriormente.map(item => {
          const aula = {
            id: item.id,
            descricao: item.descricao,
            contrato: item.contratoId
          }
          this.listaLicoesAnteriores.push(aula)
        })
      }
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

    setLicaoAnterior (value) {
      this.licaoAnterior = value
      this.carregarAula(value.id, value.contrato)
    },

    voltar () {
      this.LIMPAR_AGENDAMENTO_PERSONAL()
      this.$router.push('/academico/personal')
    },

    salvar (saveClose) {
      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      let parametrosBackEnd = {
        aluno: this.listaDiario.aluno.id,
        funcionario: this.instrutorSubstituto.id || this.agendamentoSelecionado.funcionario.id,

        sala_franqueada: this.diarioPersonal.sala_franqueada.id,

        agendamento_personal: this.agendamentoSelecionado.id,

        livro: this.listaDiario.livro.id,
        creditos_personal: this.listaDiario.creditosPersonal.id,
        presenca: this.diarioPersonal.presenca,

        atividade_ca: this.diarioPersonal.atividade_ca, // (E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue
        atividade_ce: this.diarioPersonal.atividade_ce, // (E)ntregue, (EA)ntregue com Atraso, (NE)ão Entregue

        observacao: this.diarioPersonal.observacao,
        licaos: this.tagsLicoesAplicadas.map(item => (item.id)),
        data_aula: stringToISODate(this.data_aula, true)
      }

      if (parametrosBackEnd.atividade_ca === 'false' || !parametrosBackEnd.atividade_ca) {
        parametrosBackEnd.atividade_ca = ''
      }
      if (parametrosBackEnd.atividade_ce === 'false' || !parametrosBackEnd.atividade_ce) {
        parametrosBackEnd.atividade_ce = ''
      }

      if (this.isEdit) {
        parametrosBackEnd.id = this.agendamentoSelecionado.alunoDiarioPersonal.id // Não enviar o campo completo, fará com que ele crie um novo registro
      }

      this.lancarAtualizarFrequencias(parametrosBackEnd).then(() => {
        if (saveClose) {
          this.voltar()
        } else {
          window.location.reload()
        }
      })
    },

    fecharHistorico () {
      this.historicoModal = false
      this.$refs.componenteHistoricoAulas.limparDados()
    },

    abrirHistorico () {
      this.historicoModal = true
    // this.$refs.componenteHistoricoAulas.carregarHistoricoAulas()
      setTimeout(() => {
        this.$refs.componenteHistoricoAulas.carregarHistoricoAulas()
      }, 1)
    },

    fecharAvaliacoesAlunos () {
      this.avaliacoesModal = false
    },

    abrirAvaliacoes () {
      this.avaliacoesModal = true
      setTimeout(() => {
        this.$refs.componenteAvaliacoesAlunos.carregarAvaliacoes()
      }, 1)
    },

    // Função passada como callback para HistoricoAulas
    carregarAula (agendamentoPersonalId, contratoId) {
      this.isEdit = true

      if (!agendamentoPersonalId) {
        this.isEdit = false
        this.todasAulasDadas = false
        contratoId = this.idSelecionado
      }

      this.SET_AGENDAMENTO_PERSONAL(agendamentoPersonalId)
      this.carregarDiario(contratoId)
      this.historicoModal = false
    }
  }
}
</script>
<style scoped>
#diario-personal-frequencia {
  position: relative;
}

.check-homework {
  display: flex;
  align-items: center;
  margin-right: .5rem;
}
.check-homework > div {
  margin-left: .5rem;
}

#ct-legenda {
  display: inline-block;
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
</style>
