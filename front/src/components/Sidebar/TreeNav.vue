<template>
  <div>
    <template v-if="item.filhos && item.filhos.length">
      <!-- First level dropdown  -->
      <SidebarNavDropdown :name="item.nome" :url="item.url" :icon="item.icon" :key="item.id" class="sidebar-form">
        <template v-for="child in item.filhos">
          <tree-nav v-if="(!child.apenas_franqueadora) || (child.apenas_franqueadora && usuarioLogado.franqueadaSelecionada == 1)" :item="child" :key="child.id" />
        </template>
      </SidebarNavDropdown>
    </template>

    <!-- Fixado em dashboard para não aparecerem agrupadores (configurações, cadastros) sem filhos -->
    <template v-else-if="item.url === '/dashboard' || item.url === '/ocorrencia-academica' || item.modulo_pai_id !== undefined">
      <SidebarNavItem :classes="item.class" :key="item.id" class="sidebar-form">
        <template v-if="item.apenas_franqueadora && usuarioLogado && usuarioLogado.pertenceFranqueadora">
          <SidebarNavLink :id="item.id" :name="item.nome" :url="item.url" :icon="item.icone" :is-favorito="!!item.favorito_id" :favorito-id="item.favorito_id" :badge="item.badge" :variant="item.variant"/>
        </template>
        <template v-else-if="!item.apenas_franqueadora">
          <SidebarNavLink :id="item.id" :name="item.nome" :url="item.url" :icon="item.icone" :is-favorito="!!item.favorito_id" :favorito-id="item.favorito_id" :badge="item.badge" :variant="item.variant"/>
        </template>
      </SidebarNavItem>
    </template>
  </div>
</template>

<script>
import SidebarNavDropdown from './SidebarNavDropdown'
import SidebarNavLink from './SidebarNavLink'
import SidebarNavItem from './SidebarNavItem'
import { mapState } from 'vuex'

export default {
  name: 'TreeNav',

  components: {
    SidebarNavDropdown,
    SidebarNavLink,
    SidebarNavItem
  },
  props: {
    item: {
      required: false,
      type: Object,
      default: null
    }
  },
  computed: {
    ...mapState('root', ['usuarioLogado'])
  }
}
</script>
