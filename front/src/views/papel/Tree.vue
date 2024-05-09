<template>
  <div class="p-2 tree-item">
    <div class="d-flex align-items-center checkbox-wrapper">
      <div>
        <b-form-checkbox :disabled="!permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)" :checked="item.modulo_papel_acao && item.modulo_papel_acao.length > 0" :indeterminate="isIndeterminate(item)" class="indeterminate-verde" @change="changeParent($event, item)" />
      </div>

      <!-- <div v-b-toggle="`item-${item.id}`" v-else> -->
      <!--   <font-awesome-icon v-if="item.acaoSistemas && item.acaoSistemas.length" icon="plus" /> -->
      <!-- </div> -->

      <div v-b-toggle="`item-${item.id}`" :class="{'text-decoration-underline': (item.filhos && item.filhos.length) || (item.acaoSistemas && item.acaoSistemas.length)}" class="flex-grow">
        {{ item.nome }}
      </div>
    </div>

    <b-collapse :id="`item-${item.id}`">
      <div v-if="item.filhos && item.filhos.length" class="pl-3 tree-item-content">
        <tree v-for="filho in item.filhos" :key="filho.id" :papel-id="papelId" :item="filho" />
      </div>

      <div v-else-if="item.acaoSistemas && item.acaoSistemas.length" class="tree-item-actions">
        <div v-for="acao in item.acaoSistemas" :key="acao.id">
          <b-form-checkbox :disabled="!permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === false)" :checked="item.modulo_papel_acao.find(mpa => mpa.acao_sistema_id === acao.id) !== undefined" @change="change($event, item, acao)">{{ acao.permissao_descricao }}</b-form-checkbox>
        </div>
      </div>
    </b-collapse>
  </div>
</template>

<script>
import {mapState} from 'vuex'

export default {
  name: 'Tree',
  props: {
    item: {
      type: Object,
      required: true
    },

    papelId: {
      type: Number,
      required: true
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes'])
  },

  methods: {
    change (checked, modulo, acao) {
      this.$store.dispatch('permissao/alterarAcaoModulo', {checked, moduloID: modulo.id, papelID: this.papelId, acaoID: acao.id})
      this.$store.dispatch('permissao/remontarArvore')
    },

    changeParent (checked, modulo) {
      if (modulo.filhos.length) {
        modulo.filhos.forEach(filho => {
          this.changeParent(checked, filho)
        })
      }

      modulo.acaoSistemas.map(acao => {
        if (checked === true) {
          if (modulo.modulo_papel_acao.findIndex(m => m.acao_sistema_id === acao.id) === -1) {
            this.$store.dispatch('permissao/alterarAcaoModulo', {checked, moduloID: modulo.id, papelID: this.papelId, acaoID: acao.id})
          }
        } else {
          this.$store.dispatch('permissao/alterarAcaoModulo', {checked, moduloID: modulo.id, papelID: this.papelId, acaoID: acao.id})
        }
      })

      this.$store.dispatch('permissao/remontarArvore')
    },

    isIndeterminate (modulo, recursivo = false) {
      if (!modulo.filhos.length) {
        const algumDesmarcado = modulo.acaoSistemas.some(acao => modulo.modulo_papel_acao.find(mpa => mpa.acao_sistema_id === acao.id) === undefined)
        const todosDesmarcados = modulo.acaoSistemas.every(acao => modulo.modulo_papel_acao.find(mpa => mpa.acao_sistema_id === acao.id) === undefined)

        return (!recursivo && algumDesmarcado && !todosDesmarcados) ||
          (recursivo && algumDesmarcado && !todosDesmarcados)
      }

      return modulo.filhos.some(moduloFilho => this.isIndeterminate(moduloFilho, true))
    }
  }
}
</script>

<style scoped>
.tree-item-content .tree-item {
  border-left: 1px solid #eee;
}

.tree-item-actions {
  padding: 0.25rem 0.5rem;
  background-color: #eee;
}

.text-decoration-underline {
  cursor: pointer;
}

.text-decoration-underline:hover {
  text-decoration: underline;
}

.checkbox-wrapper {
  min-height: 26px;
}

.checkbox-wrapper > div:first-child {
  width: 24px;
  font-size: 0.9rem;
}

.checkbox-wrapper .custom-control-inline {
  margin-right: 0;
  min-height: 1.3rem;
}

.custom-checkbox .custom-control-input:indeterminate ~ .custom-control-label::before {
  background-color: #85d017;
}
</style>
