<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-3">
          <label v-help-hint="'form-classificacao_aluno_icone'" for="icone" class="col-form-label">Ícone *</label>
          <div :class="!isValid && $v.icone.$invalid ? 'is-invalid' : !isValid && !$v.icone.$invalid ? 'is-valid' : ''"
               class="d-flex component-icone-select justify-content-between align-items-center form-control" required>
            <font-awesome-icon v-if="icone" :icon="icone" v-model="icone" />
            <button id="icone" :class="!isValid && $v.icone.$invalid ? 'btn-danger' : !isValid && !$v.icone.$invalid ? 'btn-success' : ''" type="button"
                    class="btn btn-icone-select el-icon-arrow-down" @blur="isOpen=false" @click="isOpen=!isOpen">
              <font-awesome-icon icon="sort-down"/>
            </button>
            <div :class="isOpen ? 'open' : ''" class="d-flex flex-wrap modal-icone-select">
              <div v-for="icone_classificacao_aluno in listaIcone" :d-selected="icone === icone_classificacao_aluno.nome"
                   :key="icone_classificacao_aluno.id"
                   class="d-flex flex-fill justify-content-center align-items-center icone-select" @mousedown.prevent="icone=icone_classificacao_aluno.nome, isOpen=!isOpen">
                <font-awesome-icon :icon="icone_classificacao_aluno.nome"/>
              </div>
            </div>
          </div>
          <div class="invalid-feedback">Escolha um ícone de classificação!</div>
        </div>

        <div class="col-md-9">
          <label v-help-hint="'form-classificacao_aluno_nome'" for="nome" class="col-form-label">Nome *</label>
          <input id="nome" v-model="nome" type="text" class="form-control" required maxlength="30">
          <div class="invalid-feedback">Informe um nome para sua classificação!</div>
        </div>
      </div>

      <div class="form-group pt-2">
        <button type="submit" class="btn btn-verde">Salvar</button>
        <router-link to="/configuracoes/classificacao-aluno" class="btn btn-link">Cancelar</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  name: 'FormularioClassificacaoAluno',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      isOpen: false,
      isEdit: false,
      icone: '',
      nome: '',
      listaIcone: [
        { nome: 'star' },
        { nome: 'glasses' },
        { nome: 'plus-circle' },
        { nome: 'bookmark' },
        { nome: 'book-open' },
        { nome: 'child' },
        { nome: 'flag' },
        { nome: 'heart' }
      ]
    }
  },
  validations: {
    icone: {required},
    nome: {required}
  },
  computed: {
    ...mapState('classificacaoAlunos', ['listaClassificacaoAluno', 'objClassificacaoAluno', 'estaCarregando'])
  },
  watch: {

    objClassificacaoAluno (value) {
      this.icone = value.icone
      this.nome = value.nome
    },

    icone (value) {
      this.SET_ICONE(value)
    },

    nome (value) {
      this.SET_NOME(value)
    }

  },
  created () {
    if (this.$route.params.id) {
      this.isEdit = true
      this.SET_CLASSIFICACAO_ALUNO_SELECIONADA(this.$route.params.id)
      this.getClassificacaoAluno()
    }
  },
  methods: {
    ...mapActions('classificacaoAlunos', ['getListaClassificacaoAluno', 'getClassificacaoAluno', 'criarClassificacaoAluno', 'atualizarClassificacaoAluno']),
    ...mapMutations('classificacaoAlunos', ['SET_CLASSIFICACAO_ALUNO', 'SET_ICONE', 'SET_NOME', 'SET_CLASSIFICACAO_ALUNO_SELECIONADA', 'LIMPAR_CLASSIFICACAO_ALUNO', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.SET_CLASSIFICACAO_ALUNO_SELECIONADA(null)
      this.LIMPAR_CLASSIFICACAO_ALUNO()
      this.$router.push('/configuracoes/classificacao-aluno')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
      }

      if (this.isEdit) {
        this.atualizarClassificacaoAluno()
          .then(() => {
            this.voltar()
          })
      } else {
        this.criarClassificacaoAluno()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
