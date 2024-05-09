<template>
  <li class="tree-item">
    <div :class="{'is-title': item.filhos && item.filhos.length}" class="d-flex justify-content-between p-2">
      <div :class="item.tipo_movimento_nota === 'E' ? 'infx-vinho' : 'infx-verde'" class="tree-item-name">{{ item.descricao }}</div>

      <div>
        <!-- <router-link :to="`/configuracoes/plano-conta/info/${item.id}`" class="icone-link" title="Informações">
          <font-awesome-icon icon="eye" />
        </router-link> -->

        <a v-if="item.franqueada !== undefined && item.franqueada.id === franqueadaSelecionada" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
          <font-awesome-icon icon="pen" />
        </a>

        <!-- <td data-label="Situação"> -->
        <!-- <div @click.prevent="inativar(item)">
          <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" v-show="item.franqueada !== undefined" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
          <span v-b-tooltip.viewport.left.hover v-else v-show="item.franqueada !== undefined" class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
        </div> -->
        <!-- </td> -->

        <a v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" v-show="item.franqueada !== undefined" href="javascript:void(0)" title="Desativar" class="icone-link text-success" @click.prevent="inativar(item)">
          <font-awesome-icon icon="check-square" />
        </a>
        <a v-b-tooltip.viewport.left.hover v-else v-show="item.franqueada !== undefined" href="javascript:void(0)" title="Ativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
          <font-awesome-icon icon="square" />
        </a>

      </div>
    </div>

    <ul v-if="item.filhos && item.filhos.length">
      <tree v-for="filho in item.filhos" :key="filho.id" :item="filho" />
    </ul>
  </li>
</template>

<script>
import {mapActions, mapMutations} from 'vuex'

export default {
  name: 'Tree',
  props: {
    item: {
      required: false,
      type: Object,
      default: null
    }
  },
  computed: {
    franqueadaSelecionada: {
      get () {
        return this.$store.state.root.usuarioLogado.franqueadaSelecionada
      }
    }
  },
  methods: {
    ...mapActions('planoConta', ['listar', 'atualizar']),
    ...mapMutations('planoConta', ['SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID']),

    inativar (item) {
      this.SET_ITEM_SELECIONADO_ID(item.id)

      const data = Object.assign({}, item)
      data.situacao = item.situacao === 'A' ? 'I' : 'A'
      data.pai = item.pai ? item.pai.id : null
      this.SET_ITEM_SELECIONADO(data)

      this.atualizar()
        .then(() => {
          this.listar()
        })
        .catch(error => {
          console.error(error)
        })
    },

    alterar (item) {
      this.$router.push(`/configuracoes/plano-conta/atualizar/${item.id}`)
      // 'configuracoes/plano-conta/adicionar'
    }
  }
}
</script>

<style scoped>
.tree-item .icone-link {
  margin: 0 3px;
  padding: 5px;
}

.is-title {
  background-color: #fafafa;
  font-weight: 600;
}

.tree-item:not(:last-child) {
  border-bottom: 1px solid rgb(235, 236, 240);
}

.tree-item ul {
  border-top: 1px solid rgb(235, 236, 240);
}
</style>
