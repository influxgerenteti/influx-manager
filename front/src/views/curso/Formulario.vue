<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="true" class="form-loading">
        <load-placeholder :loading="verificaCarregamento(loadCount,1)" />
      </div>
      <div class="form-group row">
        <div class="col-md-4">
          <label v-help-hint="'form-curso_sigla'" for="sigla" class="col-form-label">Sigla *</label>
          <input id="sigla" v-model="sigla" type="text" class="form-control" required maxlength="20">
          <div class="invalid-feedback">Preencha a sigla!</div>
        </div>

        <div class="col-md-4">
          <label v-help-hint="'form-curso_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="100">
          <div class="invalid-feedback">Preencha a descrição!</div>
        </div>

        <div class="col-md-4">
          <label v-help-hint="'form-curso_idioma'" for="idioma" class="col-form-label">Idioma *</label>
          <g-select id="idioma" :class="!isValid && !itemSelecionado.idioma ? 'invalid-input' : 'valid-input'"
                    :select="SET_IDIOMA"
                    :value="itemSelecionado.idioma"
                    :options="lista"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"
          />
          <div v-if="!isValid && !itemSelecionado.idioma" class="multiselect-invalid">
            Selecione um idioma!
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-4">
          <label for="idade_minima" class="col-form-label">Idade mínima</label>
          <input v-mask="'###'" id="idade_minima" v-model="itemSelecionado.idade_minima" type="text" class="form-control">
          <!-- <vue-numeric v-mask="'###'" id="idade_minima" :empty-value="1" :value="itemSelecionado.idade_minima" :max="999" class="form-control" /> -->
        </div>

        <div class="col-md-4">
          <label for="idade_maxima" class="col-form-label">Idade máxima</label>
          <input v-mask="'###'" id="idade_maxima" v-model="itemSelecionado.idade_maxima" type="text" class="form-control">
          <!-- <vue-numeric v-mask="'###'" id="idade_maxima" :empty-value="1" :value="itemSelecionado.idade_maxima" :max="999" class="form-control" /> -->
        </div>

        <div class="col-md-4">
          <label for="servico" class="col-form-label">Serviço *</label>
          <g-select id="servico" :class="!isValid && !itemSelecionado.servico ? 'invalid-input' : 'valid-input'"
                    :select="SET_SERVICO"
                    :value="itemSelecionado.servico"
                    :options="listaServico"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"
          />
          <div v-if="!isValid && !itemSelecionado.servico" class="multiselect-invalid">
            Selecione um serviço!
          </div>
        </div>
        <div class="col-md-4">
          <label for="modalidadeTurma" class="col-form-label">Modalidade *</label>
          <g-select id="modalidadeTurma" :class="!isValid && !itemSelecionado.modalidade_turma ? 'invalid-input' : 'valid-input'"
                    :select="SET_MODALIDADE_TURMA"
                    :value="itemSelecionado.modalidade_turma"
                    :options="listamodalidade"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"
          />
          <div v-if="!isValid && !itemSelecionado.modalidade_turma" class="multiselect-invalid">
            Selecione uma Modalidade!
          </div>
        </div>
      </div>

      <div class="form-group row">
        <b-col md="12">
          <label v-help-hint="'form-turma_intensidade'" for="livro" class="col-form-label">Intensidade *</label>
          <b-form-radio-group id="intensidade" v-model="intensidadeSelecionada" required name="intensidade">
            <b-form-radio value="R">Regular</b-form-radio>
            <b-form-radio value="S">Semi-intensivo</b-form-radio>
            <b-form-radio value="I">Intensivo</b-form-radio>
          </b-form-radio-group>
          <div v-if="!isValid && !intensidadeSelecionada" class="multiselect-invalid">
            Selecione uma opção!
          </div>
          <!-- <label v-help-hint="'form-turma_intensidade'" for="livro" class="col-form-label">Intensidade</label>
          <b-form-checkbox-group id="intensidade"  name="intensidade">
            <b-form-checkbox v-model="itemSelecionado.intensidade_regular" value="R">Regular</b-form-radio>
            <b-form-checkbox v-model="itemSelecionado.intensidade_semi_intensivo" value="S">Semi-intensivo</b-form-radio>
            <b-form-checkbox v-model="itemSelecionado.intensidade_intensivo" value="I">Intensivo</b-form-radio>
          </b-form-checkbox-group> -->
        </b-col>
      </div>

      <div class="form-group pt-2">
        <b-btn type="submit" variant="verde">Salvar</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {
  data () {
    return {
      loadCount: 0,
      intensidadeSelecionada: '',
      isValid: true,
      isEdit: false
    }
  },
  computed: {
    ...mapState('curso', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),
    ...mapState('cadastroServico', {listaServico: 'lista'}),
    ...mapState('idioma', ['lista']),
    ...mapState('modalidadeTurma', {listamodalidade: 'lista'}),

    sigla: {
      get () {
        return this.itemSelecionado.sigla
      },
      set (value) {
        this.SET_SIGLA(value)
      }
    },
    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        this.SET_DESCRICAO(value)
      }
    },
    idioma: {
      get () {
        return this.itemSelecionado.idioma
      },
    },
    modalidadeTurma: {
      get () {
        return this.itemSelecionado.modalidade_turma
      }
    }
  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()
    this.listar().then(this.countCarregamento)

    this.$store.commit('cadastroServico/SET_PAGINA_ATUAL', 1)
    this.$store.commit('cadastroServico/SET_LISTA', [])
    this.$store.commit('cadastroServico/SET_FILTRO_FRANQUEADA', [this.$store.state.root.usuarioLogado.franqueadaSelecionada])
    this.$store.dispatch('cadastroServico/listar')
    this.$store.commit('modalidadeTurma/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('modalidadeTurma/listar')
 

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(() => {
          if (this.itemSelecionado.intensidade_regular === true) {
            this.intensidadeSelecionada = 'R'
          }
          if (this.itemSelecionado.intensidade_semi_intensivo === true) {
            this.intensidadeSelecionada = 'S'
          }
          if (this.itemSelecionado.intensidade_intensivo === true) {
            this.intensidadeSelecionada = 'I'
          }
        })
    }
  },
  validations: {
    sigla: {required},
    descricao: {required},
    idioma: {required},
    intensidadeSelecionada: {required},
    itemSelecionado: {
      servico: {required},
      modalidade_turma: {required}
    }
  },
  methods: {
    ...mapMutations('curso', ['SET_SIGLA', 'SET_MODALIDADE_TURMA', 'SET_DESCRICAO', 'SET_IDIOMA', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO', 'SET_SERVICO']),
    ...mapActions('curso', ['criar', 'atualizar', 'buscar']),
    ...mapActions('idioma', ['listar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/cadastros/curso')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }
      this.itemSelecionado.intensidadeSelecionada = this.intensidadeSelecionada
      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    },
    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    }
  }
}
</script>
