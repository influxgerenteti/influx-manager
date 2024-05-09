<template>
  <div v-if="isExternalLink">
    <a :href="url" :class="classList">
      <i :class="icon"></i> {{ name }}
      <b-badge v-if="badge && badge.text" :variant="badge.variant">{{ badge.text }}</b-badge>
    </a>
  </div>
  <div v-else>
    <router-link v-b-tooltip.viewport.right.hover :to="url" :class="classList" :id="id" :title="name" :disabled="(name.length < 25)" class="d-flex align-items-center justify-content-between nav-modulo">
      <span class="nav-link-title">{{ name }}</span>
      <font-awesome-icon :class="{favoritado: isFavorito}" icon="star" class="favorito" @click.stop.prevent="chamarFavorito(id || favoritoId, favoritoId)" />
      <!-- <i :class="icon"></i> {{ name }} -->
      <b-badge v-if="badge && badge.text" :variant="badge.variant">{{ badge.text }}</b-badge>
    </router-link>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'SidebarNavLink',
  props: {
    id: {
      type: [String, Number],
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    url: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: ''
    },
    badge: {
      type: Object,
      default: () => {}
    },
    variant: {
      type: String,
      default: ''
    },
    classes: {
      type: String,
      default: ''
    },
    favoritoId: {
      type: [String, Number],
      default: ''
    },
    isFavorito: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapState('root', ['listaModulo', 'objModulo', 'favoritoSelecionadoId']),
    classList () {
      return [
        'nav-link',
        this.linkVariant,
        ...this.itemClasses
      ]
    },
    linkVariant () {
      return this.variant ? `nav-link-${this.variant}` : ''
    },
    itemClasses () {
      return this.classes ? this.classes.split(' ') : []
    },
    isExternalLink () {
      return this.url.substring(0, 4) === 'http'
    }
  },
  methods: {
    ...mapActions('root', ['criarFavorito', 'listarMenus', 'removerFavorito']),
    ...mapMutations('root', ['setFavorito', 'setFavoritoSelecionado']),
    chamarFavorito (id, favoritoId) {
      this.setFavoritoSelecionado(favoritoId)

      if (!!favoritoId || this.isFavorito) {
        this.removerFavorito()
          .then(() => {
            this.listarMenus()
          })
      } else {
        this.setFavorito(id)
        this.criarFavorito()
          .then(() => {
            this.listarMenus()
          })
      }
    }
  }
}
</script>

<style scoped>
.favorito {
  opacity: 0;
  transition: opacity .2s, color .2s;
}

.nav-modulo:hover .favorito:not(.favoritado),
.nav-item.is-favorito:hover .favorito:hover,
.nav-modulo:hover .favorito.favoritado:hover {
  opacity: .6;
  color: #fff;
  /* color: #29363d; */
}

.nav-modulo:hover .favorito:hover,
.nav-item.is-favorito:hover .favorito,
.nav-modulo:hover .favorito.favoritado {
  opacity: 1;
  /* color: #fff; */
  color: #ffdf50;
}

/* .is-favorito:hover {
  opacity: .6;
  color: #ffdf50;
  -webkit-filter:drop-shadow(0 0px 4px #1985ac);
          filter:drop-shadow(0 0px 4px #1985ac);
} */

</style>
