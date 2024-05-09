<template>
  <router-link :to="url" tag="li" class="nav-item nav-dropdown" disabled>
    <div class="nav-link nav-dropdown-toggle" @click="handleClick">
      <template v-if="icon">
        <!-- <font-awesome-icon  icon="star" class="favorito " /> --> {{ name }}
      </template>
      <template v-else>
        <!-- <i :class="icon"></i> --> {{ name }}
      </template>
    </div>
    <ul class="nav-dropdown-items">
      <slot></slot>
    </ul>
  </router-link>
</template>

<script>
export default {
  props: {
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
    }
  },

  methods: {
    handleClick (e) {
      e.preventDefault()
      const target = e.target.parentElement.classList
      if (target.contains('open')) {
        target.remove('open')
        return
      }
      let modulosAbertos = document.querySelectorAll('li.open')
      modulosAbertos.forEach((moduloPai) => {
        moduloPai.classList.toggle('open')
      })

      target.toggle('open')
    }
  }
}
</script>
