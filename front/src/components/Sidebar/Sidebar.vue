<template>
  <div class="sidebar no-print">
    <div :class="{'d-none': escondeLoading}" class="menu-loading">
      <load-placeholder :loading="estaCarregando" :color-a="'#fff'" :color-b="'#fff'" />
    </div>
    <SidebarHeader/>
    <SidebarForm/>
    <nav class="sidebar-nav">

      <ul class="nav">
        <!-- First level dropdown -->
        <transition name="fade">
          <li v-show="selected === 'favoritos'">
            <template v-for="(childL1) in navFavoritos">
              <SidebarNavItem :classes="'is-favorito'" :key="childL1.id" class="sidebar-form">
                <SidebarNavLink :name="childL1.nome" :url="childL1.url" :icon="childL1.icon" :id="childL1.id" :favorito-id="childL1.favorito_id" />
              </SidebarNavItem>
            </template>
          </li>
        </transition>

        <transition name="fade">
          <li v-show="selected === 'principal'" class="menu-principal">
            <template v-for="(item, index) in navItems">

              <template v-if="item.exibir_no_menu">
                <template v-if="item.apenas_franqueadora && usuarioLogado && usuarioLogado.pertenceFranqueadora ">
                  <template v-if="item.titulo">
                    <SidebarNavTitle :name="item.nome" :classes="item.class" :wrapper="item.wrapper" :key="item.id"/>
                  </template>
                  <template v-else-if="item.divisor">
                    <SidebarNavDivider :classes="item.class" :key="index"/>
                  </template>
                  <template v-else-if="item.label">
                    <SidebarNavLabel :name="item.nome" :url="item.url" :icon="item.icon" :label="item.label" :classes="item.class" :key="index"/>
                  </template>
                  <template v-else>
                    <tree-nav :key="item.id" :item="item" />
                  </template>
                </template>

                <template v-else-if="!item.apenas_franqueadora">
                  <template v-if="item.titulo">
                    <SidebarNavTitle :name="item.nome" :classes="item.class" :wrapper="item.wrapper" :key="item.id"/>
                  </template>
                  <template v-else-if="item.divisor">
                    <SidebarNavDivider :classes="item.class" :key="index"/>
                  </template>
                  <template v-else-if="item.label">
                    <SidebarNavLabel :name="item.nome" :url="item.url" :icon="item.icon" :label="item.label" :classes="item.class" :key="index"/>
                  </template>
                  <template v-else>
                    <!-- {{ item }} -->
                    <tree-nav :key="item.id" :item="item" />
                  </template>
                </template>

              </template>
            </template>
          </li>
        </transition>

      </ul>
      <slot></slot>
    </nav>
    <!-- <SidebarFooter/> -->
    <SidebarNavigator/>
    <SidebarMinimizer/>
  </div>
</template>
<script>
// import SidebarFooter from './SidebarFooter'
import SidebarForm from './SidebarForm'
import SidebarHeader from './SidebarHeader'
import SidebarMinimizer from './SidebarMinimizer'
import SidebarNavDivider from './SidebarNavDivider'
import SidebarNavDropdown from './SidebarNavDropdown'
import SidebarNavLink from './SidebarNavLink'
import SidebarNavTitle from './SidebarNavTitle'
import SidebarNavItem from './SidebarNavItem'
import SidebarNavLabel from './SidebarNavLabel'
import SidebarNavigator from './SidebarNavigator'
import TreeNav from './TreeNav'
import { mapState } from 'vuex'

export default {
  name: 'Sidebar',
  components: {
    // SidebarFooter,
    SidebarForm,
    SidebarHeader,
    SidebarMinimizer,
    SidebarNavDivider,
    SidebarNavDropdown,
    SidebarNavLink,
    SidebarNavTitle,
    SidebarNavItem,
    SidebarNavLabel,
    SidebarNavigator,
    TreeNav
  },

  props: {
    navItems: {
      type: Array,
      required: true,
      default: () => []
    },
    navFavoritos: {
      type: Array,
      required: false,
      default: () => []
    }
  },

  data () {
    return {
      selected: 'principal',
      escondeLoading: false
    }
  },

  computed: {
    ...mapState('root', ['estaCarregando', 'usuarioLogado'])
  },

  mounted () {
    if (!this.estaCarregando) {
      setTimeout(() => {
        this.escondeLoading = true
      }, 150)
    }
  },

  methods: {
    handleClick (e) {
      e.preventDefault()
      e.target.parentElement.classList.toggle('open')
    },
    moduloSelected (selected) {
      this.selected = selected
    }
  }
}
</script>

<style lang="css">
  .nav-link {
    cursor:pointer;
  }

  .menu-principal {
    position: absolute;
  }

  .fade-enter-active {
    transition: all .3s ease;
  }
  .fade-enter, fade-leave-to {
    transform: translateX(10px);
    opacity: 0;
  }

  .sidebar .menu-loading {
    background-color: #21336d;
  }
</style>
