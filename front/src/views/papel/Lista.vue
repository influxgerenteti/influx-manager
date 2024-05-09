<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado pb-2">
      <b-row>
        <b-col md="3">
          <label for="papel" class="form-label">Papel</label>
          <div class="d-flex">
            <g-select
              id="papel"
              :value="papelSelecionado"
              :select="setPapelId"
              :options="papeis"
              class="multiselect-truncate"
              label="descricao"
              track-by="id" />

            <b-btn v-b-modal.modalPapel v-b-tooltip v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" variant="cinza" class="w-40" title="Atualizar" @click="$refs.formPapel.$emit('modal-papel:abrir', papelSelecionado.id)">
              <font-awesome-icon icon="pen" />
            </b-btn>

            <b-btn v-b-modal.modalPapel v-b-tooltip v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" variant="azul" class="w-40" title="Adicionar" @click="$refs.formPapel.$emit('modal-papel:abrir')">
              <font-awesome-icon icon="plus" />
            </b-btn>
          </div>
        </b-col>

        <b-col md="3">
          <label for="modulos" class="form-label">Módulo</label>
          <g-select
            id="modulos"
            :value="moduloSelecionado"
            :select="setModuloId"
            :options="modulos"
            class="multiselect-truncate"
            label="nome"
            track-by="id" />
        </b-col>
      </b-row>
    </div>

    <div class="table-responsive-sm">
      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div v-else-if="arvoreItens && arvoreItens.length" class="table-scroll w-100">
        <perfect-scrollbar>
          <template v-if="papelSelecionado.id !== undefined">
            <tree v-for="item in arvoreItens" :key="item.id" :papel-id="papelSelecionado.id" :item="item" />
          </template>
        </perfect-scrollbar>
      </div>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center mt-2">
      <div>
        <b-btn :disabled="!permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false) || submiting" variant="verde" @click="submit()">{{ submiting ? 'Salvando...' : 'Salvar' }}</b-btn>
      </div>

      <div></div>
    </div>

    <b-modal v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" id="modalPapel" ref="modalPapel" size="sm" centered no-close-on-backdrop hide-header hide-footer>
      <formulario-papel ref="formPapel" @update="update" @cancel="$refs.modalPapel.hide()" />
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import FormularioPapel from './Formulario'
import Tree from './Tree'

export default {
  name: 'ListaPapel',

  components: {
    FormularioPapel,
    Tree
  },

  data () {
    return {
      submiting: false,
      filtroRapido: true,
      papeis: [],
      modulos: [],
      papelSelecionado: {},
      moduloSelecionado: {}
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('permissao', ['arvoreItens', 'estaCarregando', 'todosItensCarregados'])
  },

  mounted () {
    this.SET_LISTA([])
    this.listarSelects()
  },

  methods: {
    ...mapActions('permissao', ['listar', 'atualizar']),
    ...mapActions('modulos', ['buscaTodosModulosSemPai']),
    ...mapActions('papel', ['buscarPapeis']),
    ...mapMutations('permissao', ['SET_FILTROS', 'SET_LISTA']),

    listarSelects (id = null) {
      this.buscarPapeis()
        .then(papeisRetorno => {
          this.papeis = papeisRetorno

          if (id === null) {
            this.papelSelecionado = this.papeis[0]
          } else {
            this.papelSelecionado = this.papeis.find(papel => papel.id === id)
          }

          this.filtrar()
        })

      this.buscaTodosModulosSemPai()
        .then(modulosRetorno => {
          this.modulos = [{nome: 'Selecione', id: ''}].concat(modulosRetorno)
        })
    },

    filtrar () {
      let parametros = {
        papel: null,
        modulo: null
      }

      if (this.papelSelecionado.id !== undefined) {
        parametros.papel = this.papelSelecionado.id
      }

      if (this.moduloSelecionado.id !== undefined) {
        parametros.modulo = this.moduloSelecionado.id
      }

      this.SET_FILTROS(parametros)
      this.SET_LISTA([])

      this.listar()
    },

    setPapelId (papelObj) {
      this.papelSelecionado = papelObj
      this.filtrar()
    },

    setModuloId (moduloObj) {
      this.moduloSelecionado = moduloObj
      this.filtrar()
    },

    submit () {
      if (!this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === false)) {
        return
      }

      this.submiting = true

      this.atualizar({papelID: this.papelSelecionado.id, moduloID: this.moduloSelecionado.id})
        .finally(() => {
          this.submiting = false
        })
    },

    update (id) {
      this.$refs.modalPapel.hide()
      this.listarSelects(id)
    }
  }
}
</script>
