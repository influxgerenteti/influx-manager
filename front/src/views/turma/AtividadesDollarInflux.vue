<template>
  <div>
    <div class="form-loading">
      <load-placeholder :loading="carregando" />
    </div>

    <g-table :class="className">
      <thead class="text-dark">
        <tr>
          <th>Aluno</th>
          <th v-b-tooltip.top v-for="atividade in listaAtividadesDollar" :title="atividade.descricao" :key="atividade.id" class="size-160">
            <span class="truncate">
              {{ atividade.descricao }}
            </span>
          </th>
          <th class="size-100">Total</th>
        </tr>
      </thead>

      <tbody ref="scroll-wrap">
        <tr v-for="alunoDolarFlux in listaDeAlunosComDollar" :key="alunoDolarFlux.id">
          <td data-label="Aluno" class="truncate">
            <span v-b-tooltip.top :title="alunoDolarFlux.nomeAluno">
              {{ alunoDolarFlux.nomeAluno }}
            </span>
          </td>
          <td v-for="atividade in listaAtividadesDollar" :data-label="atividade.descricao" :key="atividade.id" class="size-160">
            <vue-numeric :id="`atividade_${alunoDolarFlux.aluno}_${alunoDolarFlux.contrato}_${atividade.id}`"
                         :precision="2"
                         :empty-value="0"
                         :value="mostrarValor(alunoDolarFlux.aluno, alunoDolarFlux.contrato, atividade.id)"
                         :max="9999999.99"
                         separator="."
                         class="form-control"
                         @input="alterarInfluxDollar(alunoDolarFlux.aluno, alunoDolarFlux.contrato, atividade.id, $event)"
            />
          </td>
          <td data-label="Total" class="size-100">
            {{ somarCredito(alunoDolarFlux.aluno, alunoDolarFlux.contrato) | formatarMoeda(false, true) }}
          </td>
        </tr>
      </tbody>
    </g-table>
    <div class="form-group pt-2 mb-0">
      <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
      <b-btn :disabled="isEnviando" variant="verde" @click="salvar(true)">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>

      <b-btn variant="link" @click="$emit('fecharModal')">Cancelar</b-btn>
    </div>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default{
  name: 'AtividadesDollarInflux',

  props: {
    isPersonal: {
      type: Boolean,
      required: false,
      default: false
    },
    turmaId: {
      type: [String, Number],
      required: false,
      default: null
    },
    contratoId: {
      type: [String, Number],
      required: false,
      default: null
    }
  },

  data () {
    return {
      carregando: false,
      isEnviando: false,
      className: 'rapido-open',
      listaDeAlunosComDollar: []
    }
  },

  computed: {
    ...mapState('atividadeDollarInflux', {listaAtividadesDollar: 'lista'})
  },
  methods: {
    ...mapActions('atividadeDollarInflux', {listarAtividadesDollar: 'listar'}),
    ...mapActions('movimentoDollarInflux', {lancarAtualizarInfluxDollar: 'lancarAtualizar'}),
    ...mapActions('contrato', {listarContratosAtivosComDollarPorTurma: 'buscarContratosComDollarAtivosPorTurma', buscarContratoPorId: 'buscar'}),
    ...mapMutations('atividadeDollarInflux', {SET_PAGINA_ATUAL_ATIVIDADE_DOLLAR: 'SET_PAGINA_ATUAL', SET_LISTA_ATIVIDADE_DOLLAR: 'SET_LISTA'}),

    carregarCamposDinamicos () {
      this.carregando = true
      this.SET_PAGINA_ATUAL_ATIVIDADE_DOLLAR(1)
      this.SET_LISTA_ATIVIDADE_DOLLAR([])
      this.listarAtividadesDollar()
        .then(() => {
          this.carregarAlunos()
        })
    },

    montaListaDollar (contratoORM) {
      let objAluno = {
        contrato: contratoORM.id,
        aluno: contratoORM.aluno.id,
        atividadesDollar: [],
        nomeAluno: contratoORM.aluno.pessoa.nome_contato
      }
      this.listaAtividadesDollar.map(atividadeDollarInfluxORM => {
        let atividadeData = {
          atividade: atividadeDollarInfluxORM.id,
          valor: 0
        }
        contratoORM.movimentoDollars.map(movimentoDollarMetaData => {
          if (movimentoDollarMetaData.atividade_dollar.id === atividadeDollarInfluxORM.id) {
            atividadeData.movimento_dollar = movimentoDollarMetaData.id
            atividadeData.valor = movimentoDollarMetaData.valor.replace('.', ',')
          }
        })
        objAluno.atividadesDollar.push(atividadeData)
      })
      this.listaDeAlunosComDollar.push(objAluno)
    },

    carregarAlunos () {
      if (this.isPersonal) {
        this.buscarContratoPorId(this.contratoId)
          .then(contratoORM => {
            this.montaListaDollar(contratoORM)
            this.carregando = false
          })
      } else {
        this.listarContratosAtivosComDollarPorTurma(this.turmaId)
          .then(lista => {
            lista.map(contratoORM => {
              this.montaListaDollar(contratoORM)
            })
            this.carregando = false
          })
      }
    },

    alterarInfluxDollar (alunoId, contratoId, atividadeId, value) {
      let valorAlterado = value
      let indexAluno = this.listaDeAlunosComDollar.findIndex(item => {
        return (item.aluno === alunoId) && (item.contrato === contratoId)
      })
      let indexAtividade = this.listaDeAlunosComDollar[indexAluno].atividadesDollar.findIndex(atvDollar => {
        return atvDollar.atividade === atividadeId
      })
      this.listaDeAlunosComDollar[indexAluno].atividadesDollar[indexAtividade].valor = valorAlterado
    },

    mostrarValor (alunoId, contratoId, atividadeId) {
      let indexAluno = this.listaDeAlunosComDollar.findIndex(item => {
        return (item.aluno === alunoId) && (item.contrato === contratoId)
      })
      let indexAtividade = this.listaDeAlunosComDollar[indexAluno].atividadesDollar.findIndex(atvDollar => {
        return atvDollar.atividade === atividadeId
      })
      return this.listaDeAlunosComDollar[indexAluno].atividadesDollar[indexAtividade].valor
    },

    somarCredito (alunoId, contratoId) {
      let resultado = 0
      let indexAluno = this.listaDeAlunosComDollar.findIndex(item => {
        return (item.aluno === alunoId) && (item.contrato === contratoId)
      })
      this.listaDeAlunosComDollar[indexAluno].atividadesDollar.map(atividadesDollar => {
        let valorDolar = atividadesDollar.valor
        if (valorDolar) {
          resultado += (valorDolar * 1)
        }
      })
      return resultado
    },

    limparState () {
      this.isEnviando = false
      this.listaDeAlunosComDollar = []
    },

    montaParametros () {
      let retorno = []
      this.listaDeAlunosComDollar.map(item => {
        item.atividadesDollar.map(atividadeDollar => {
          let metaDataMovimentoDollar = {
            aluno: item.aluno,
            contrato: item.contrato,
            atividade_dollar: atividadeDollar.atividade,
            tipo_operacao: 'C',
            valor: atividadeDollar.valor
          }
          if (atividadeDollar.movimento_dollar !== undefined && atividadeDollar.movimento_dollar !== null) {
            metaDataMovimentoDollar.id = atividadeDollar.movimento_dollar
          }
          retorno.push(metaDataMovimentoDollar)
        })
      })
      return retorno
    },

    salvar (bSalvarESair) {
      this.isEnviando = true
      let parametros = this.montaParametros()
      this.lancarAtualizarInfluxDollar(parametros)
        .then(resp => {
          if (bSalvarESair) {
            this.$emit('fecharModal')
          } else {
            this.limparState()
            this.carregarCamposDinamicos()
          }
        })
        .catch(error => {
          console.error(error)
          this.isEnviando = false
        })
    }
  }
}
</script>
<style scoped>
.rapido-open td input {
  max-width: 100px;
}
</style>
