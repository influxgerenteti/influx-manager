<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-plano-conta_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="100">
          <div class="invalid-feedback">Preencha a descrição</div>
        </div>

        <div class="col-md-6">
          <div class="col-md-4">
            <label v-help-hint="'form-plano-conta_tipo_movimento_nota'" class="col-form-label">Tipo de movimento *</label>
          </div>
          <b-form-radio-group v-model="tipo_movimento_nota" name="tipo_movimento_nota" required @change="pai=null">
            <b-form-radio value="S">Entrada</b-form-radio>
            <b-form-radio value="E">Saída</b-form-radio>

            <div v-if="!isValid && !tipo_movimento_nota" class="multiselect-invalid">Selecione uma opção</div>
          </b-form-radio-group>
        </div>

      </div>

      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-plano_pai'" for="pai" class="col-form-label">Pai</label>
          <select id="pai" v-model="pai" :disabled="tipo_movimento_nota === null" class="form-control">
            <option :value="null">Nenhum</option>
            <option v-for="(item, index) in listaNova" :key="index" :value="item.id">
              <template v-if="!(itemSelecionado && itemSelecionado.id === item.id)">{{ item.descricao }}</template>
            </option>
          </select>
        </div>
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
      isEdit: true,
      isValid: true
    }
  },
  computed: {
    ...mapState('planoConta', ['lista', 'estaCarregando', 'itemSelecionado', 'itemSelecionadoID']),

    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        this.SET_DESCRICAO(value)
      }
    },
    pai: {
      get () {
        return this.itemSelecionado.pai
      },
      set (value) {
        this.SET_PAI(value)
      }
    },
    tipo_movimento_nota: {
      get () {
        return this.itemSelecionado.tipo_movimento_nota
      },
      set (value) {
        this.SET_TIPO_MOVIMENTO_NOTA(value)
      }
    },
    listaNova: {
      get () {
        return this.lista.filter(item => (item.tipo_movimento_nota === this.tipo_movimento_nota) && (item.id !== (this.itemSelecionadoID * 1)))
      }
    }

  },
  created () {
    this.$store.commit('planoConta/SET_PAGINA_ATUAL', 1)

    this.listar()
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar({transformID: true})
        .then(() => {
          if (this.itemSelecionado.franqueada !== undefined) {
            if (this.itemSelecionado.franqueada.id !== this.$store.state.root.usuarioLogado.franqueadaSelecionada) {
              this.$router.push('/configuracoes/plano-conta')
            }
          } else {
            this.$router.push('/configuracoes/plano-conta')
          }
        })
      this.isEdit = true
    }
  },
  validations: {
    descricao: {required},
    tipo_movimento_nota: {required}
  },
  methods: {
    ...mapMutations('planoConta', [
      'SET_DESCRICAO',
      'SET_TIPO_MOVIMENTO_NOTA',
      'SET_ITEM_SELECIONADO_ID',
      'SET_PAI',
      'LIMPAR_ITEM_SELECIONADO'
    ]),
    ...mapActions('planoConta', ['listar', 'buscar', 'criar', 'atualizar']),

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/plano-conta')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    }
  }
}
</script>
