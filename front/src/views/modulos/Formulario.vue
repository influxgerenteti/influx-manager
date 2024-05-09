<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="listaModulo.length === 0" class="form-loading">
        <load-placeholder :loading="listaModulo.length === 0" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-modulo_nome'" for="nome" class="col-form-label">Nome *</label>
          <input id="nome" v-model="nome" type="text" class="form-control" placeholder="Nome" required maxlength="80">
          <div class="invalid-feedback">Preencha o nome!</div>
        </div>

        <div class="col-md-6">
          <label v-help-hint="'form-modulo_moduloPai'" class="col-form-label" for="moduloPai">Módulo Pai</label>
          <g-select
            id="moduloPai"
            :value="modulo_pai"
            :select="setModuloPai"
            :options="listaModulo"
            class="multiselect-truncate"
            label="nome"
            track-by="id" />
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-modulo_url'" for="url" class="col-form-label">URL *</label>
          <input id="url" v-model="url" type="text" class="form-control" placeholder="URL" required maxlength="255">
          <div class="invalid-feedback">Informe a URL!</div>
        </div>

        <div class="col-md-2">
          <label v-help-hint="'form-modulo_isAtivo'" class="col-form-label">Situação</label>
          <div class="custom-control custom-checkbox">
            <input id="isAtivo" v-model="situacao" type="checkbox" class="custom-control-input">
            <label class="custom-control-label" for="isAtivo">Ativo</label>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <button type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/configuracoes/modulo" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {

  name: 'FormularioModulo',
  data () {
    return {
      isValid: true,
      errorMsg: '',
      nome: '',
      situacao: '',
      url: '',
      modulo_pai: '',
      isEdit: false
    }
  },
  validations: {
    nome: {
      required
    },
    situacao: {},
    url: {
      required
    }
  },
  computed: {
    ...mapState('modulos', ['listaModulo', 'listaModulosPais', 'objModulo', 'estaCarregando'])
  },
  watch: {
    objModulo (value) {
      this.nome = value.nome
      this.url = value.url
      this.situacao = value.situacao === 'A'
    },

    nome (value) {
      this.setNome(value)
    },

    url (value) {
      if (value) {
        value = value.replace(/[^a-zA-Z0-9/-]+/g, '')
        this.url = value
      }
      this.setURL(value)
    },

    situacao (value) {
      this.setSituacao(value === true ? 'A' : 'I')
    }
  },
  created () {
    this.limparModulo()
    this.getListaModulo()
    this.buscarModulosPais()
    if (this.$route.params.id) {
      this.isEdit = true
      this.setModuloSelecionado(this.$route.params.id)
      this.getModulo().then(res => {
        if (this.objModulo.modulo_pai) {
          this.setModuloPai(this.objModulo.modulo_pai)
        }
      })
    }
  },
  methods: {
    ...mapActions('modulos', ['getListaModulo', 'getModulo', 'criarModulo', 'buscarModulosPais', 'atualizarModulo']),
    ...mapMutations('modulos', ['setNome', 'setURL', 'setSituacao', 'setModuloPai', 'setModuloSelecionado', 'limparModulo', 'SET_ESTA_CARREGANDO']),

    voltar () {
      this.setModuloSelecionado(null)
      this.limparModulo()
      this.$router.push('/configuracoes/modulo')
    },

    setModuloPai (value) {
      this.modulo_pai = value
      this.objModulo.modulo_pai_id = value.id
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.isEdit) {
        this.atualizarModulo()
          .then(() => {
            this.voltar()
          })
      } else {
        this.criarModulo()
          .then(() => {
            this.voltar()
          })
      }
    }
  }
}
</script>
