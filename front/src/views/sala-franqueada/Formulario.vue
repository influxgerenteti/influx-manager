<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !estaValido }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="estaEditando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="body-sector p-3">
        <b-row class="align-items-center">
          <b-col md="4">
            <label v-help-hint="'form-sala-franqueada_sala'" for="sala" class="col-form-label">Sala *</label>
            <template v-if="!estaEditando">
              <g-select
                id="sala"
                :class="!estaValido && !itemSelecionado.sala ? 'invalid-input' : 'valid-input'"
                :value="itemSelecionado.sala"
                :select="setSala"
                :options="opcoesSalas"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
              <div v-if="!estaValido && !itemSelecionado.sala" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </template>
            <span v-else-if="itemSelecionado.sala" class="d-block pt-2">{{ itemSelecionado.sala.descricao }}</span>
            <span v-else class="d-block pt-2">Nenhuma</span>
          </b-col>

          <b-col md="4">
            <label v-help-hint="'form-sala-lotacao_maxima'" for="lotacao_maxima" class="col-form-label">Máximo de alunos *</label>
            <input id="lotacao_maxima" v-model.number="itemSelecionado.lotacao_maxima" type="text" class="form-control" maxlength="9" required>
          </b-col>

          <b-col md="4">
            <label v-help-hint="'form-sala-lotacao_personal'" class="col-form-label d-block">&nbsp;</label>
            <b-form-checkbox v-model="itemSelecionado.personal" :value="true" :unchecked-value="false">
              Personal
            </b-form-checkbox>
          </b-col>
        </b-row>

      </div>

      <div>
        <b-btn :disabled="estaSalvando" type="submit" variant="verde">{{ estaSalvando ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn type="button" variant="link" @click="cancelar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'FormularioSala',

  props: {
    isModal: {
      type: Boolean,
      required: false,
      default: false
    },

    id: {
      type: Number,
      required: false,
      default: null
    },

    salasFranqueada: {
      type: Array,
      required: false,
      default: null
    }
  },

  data () {
    return {
      estaSalvando: false,
      estaValido: true,
      estaEditando: false
    }
  },

  computed: {
    ...mapState('salaFranqueada', ['itemSelecionado', 'estaCarregando']),
    ...mapState('sala', {listaSalas: 'lista', estaCarregandoSalas: 'estaCarregando'}),

    salasFranqueadaIDs: {
      get () {
        return this.salasFranqueada ? this.salasFranqueada.map(item => item.sala_id * 1) : []
      }
    },

    opcoesSalas: {
      get () {
        return this.listaSalas.filter(sala => !this.salasFranqueadaIDs.includes(sala.id))
      }
    }
  },

  watch: {
    id () {
      this.init()
    }
  },

  validations: {
    itemSelecionado: {
      sala: {required},
      lotacao_maxima: {required}
    }
  },

  mounted () {
    this.init()
  },

  methods: {
    ...mapActions('salaFranqueada', ['buscar', 'criar', 'atualizar']),

    init () {
      this.$store.commit('salaFranqueada/LIMPAR_ITEM_SELECIONADO')

      if (!this.listaSalas.length && !this.estaCarregandoSalas) {
        this.$store.commit('sala/SET_PAGINA_ATUAL', 1)
        this.$store.dispatch('sala/listar')
      }

      if (this.id) {
        this.$store.commit('salaFranqueada/SET_ITEM_SELECIONADO_ID', this.id)
        this.$store.dispatch('salaFranqueada/buscar', this.id)
      }
    },

    setSala (value) {
      this.itemSelecionado.sala = value
    },

    validate () {
      return !!this.itemSelecionado.sala &&
        !!this.itemSelecionado.lotacao_maxima
    },

    submit () {
      if (!this.validate()) {
        this.estaValido = false
        return
      }

      this.estaSalvando = true

      let request = null
      if (this.id) {
        request = this.atualizar()
      } else {
        request = this.criar()
      }

      request
        .then(id => {
          this.$emit('resolve', id)
        })
        .catch(console.error)
        .finally(() => {
          this.estaSalvando = false
        })
    },

    cancelar () {
      this.$emit('reject')
    }
  }
}
</script>
