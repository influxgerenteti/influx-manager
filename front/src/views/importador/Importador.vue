<template>
  <div class="animated fadeIn">
    <!-- <div class="d-flex justify-content-between mb-4">
      <h4 class="text-dark title-module">Importador de dados</h4>
    </div> -->

    <div class="importador container list-group-accent list-group-item list-group-item-accent-light list-group-item-light border-0">
      <form @reset="resetForm()" @submit.prevent="submit()">
        <step-0 v-show="step === 0">0</step-0>
        <step-1 v-show="step === 1">1</step-1>
        <step-2 v-show="step === 2">2</step-2>

        <b-row v-if="step === 2" class="mt-3 nav-footer-importador">
          <b-col>
            <b-btn variant="link" size="sm">Log de execução</b-btn>
            <b-btn variant="link" size="sm">Inconsistências</b-btn>
          </b-col>
          <b-col>
            <b-btn variant="verde" @click.prevent="mostrarUsuarios = true">Efetivar em validação de usuário</b-btn>
          </b-col>
        </b-row>

        <b-card v-if="mostrarUsuarios" class="mt-3 mx-auto w-50 card-usuarios">
          <div class="form-group">
            <label for="selectUsuarios">Usuário para validação</label>
            <select id="selectUsuarios" class="form-control">
              <option value="">Selecione</option>
              <option value="1">Usuário 01</option>
              <option value="2">Usuário 02</option>
            </select>
          </div>

          <div class="text-right">
            <b-btn variant="success" type="submit">Confirmar</b-btn>
          </div>
        </b-card>
      </form>
    </div>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import FirstStep from './steps/step-0'
import SecondStep from './steps/step-1'
import ThirdStep from './steps/step-2'

export default {
  name: 'Importador',
  components: {
    'step-0': FirstStep,
    'step-1': SecondStep,
    'step-2': ThirdStep
  },
  data () {
    return {
      mostrarUsuarios: false
    }
  },
  computed: mapState('importador', ['arquivoSelecionado', 'nomeArquivoSelecionado', 'readingFile', 'sendingFile', 'step']),
  validations: {
    arquivo: {required}
  },
  methods: {
    ...mapMutations('importador', ['setArquivoSelecionado', 'setSendingFile', 'setStep']),

    submit () {
      console.log('efet')
    },

    resetForm () {
      this.setArquivoSelecionado(null)
      this.setSendingFile(true)
      this.setStep(0)
    }
  }
}
</script>

<style scoped>
.importador.container {
  margin-bottom: 1.5rem;
  /* height: calc(100vh - 100px);
  height: -webkit-calc(100vh - 100px);
  height: -moz-calc(100vh - 100px); */
}
/* .importador.container .table-scroll {
  height: calc(100vh - 270px);
  height: -webkit-calc(100vh - 270px);
  height: -moz-calc(100vh - 270px);
  margin: 0;
} */
.card-header * {
  border: 0 !important;
}

.card-usuarios {
  /* border: 1px solid #ddd; */
  background: #eee;
}

.list-group-item-light {
  background-color: #EBECF0;
}

/* form > div {
  display: flex;
  align-items: center;
  height: calc(100vh - 100px);
  height: -webkit-calc(100vh - 100px);
  height: -moz-calc(100vh - 100px);
} */
</style>
