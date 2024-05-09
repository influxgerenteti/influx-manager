<template>
  <div ref="el" :class="loading ? null : 'content-loaded'" class="loader-box">
    <div v-if="animate" class="spinner">
      <div :style="`background-color: ${colorA}`" class="double-bounce1"></div>
      <div :style="`background-color: ${colorB}`" class="double-bounce2"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadPlaceholder',
  props: {
    loading: {
      type: Boolean,
      default: false
    },
    animate: {
      type: Boolean,
      default: true
    },
    colorA: {
      type: String,
      default: '#2d4899'
    },
    colorB: {
      type: String,
      default: '#85d017'
    },
    message: {
      type: String,
      default: 'Carregando...'
    }
  },
  watch: {
    loading (value) {
      const parent = this.$refs.el.parentElement
      parent.removeAttribute('style')
      if (!value) {
        parent.style.opacity = 0
        setTimeout(() => {
          parent.style.display = 'none'
        }, 300)
      }
    }
  }
}
</script>

<style scoped>
.loader-box {
  margin: auto;
}
.spinner {
  width: 40px;
  height: 40px;
  position: relative;
}

.screen-load .loader-box {
  margin-top: 50vh;
}

.double-bounce1, .double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  /* background-color: #333; */
  opacity: 0.6;
  position: absolute;
  top: 0;
  left: 0;
  -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.double-bounce2 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

@-webkit-keyframes sk-bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bounce {
  0%, 100% {
    transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% {
    transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}

/* .loading-items {
  height: 64px;
}

.loading-items div {
  padding: 20px;
  font-size: 16px;
  line-height: 24px;
} */
</style>
