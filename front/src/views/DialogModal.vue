<template>
    <div class="dialog-modal-backdrop"
      v-show="isOpen"
      :class="{open: isOpen}"
      @click="$emit('onClose')">
  
      <div class="dialog-modal" :class="{open: isOpen}" @click.stop>
        <div class="dialog-modal-title" v-if="title">{{ title }}</div>
        <div class="dialog-modal-body">
          <slot />
        </div>
      </div>
  
    </div>
  </template>
  
  <script>
  export default {
    props: {
      isOpen: Boolean,
      title: String
    }
  }
  </script>
  
  <style scoped>
  .dialog-modal-backdrop{
    /* background: rgba(250, 250, 250, 0.8); */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    z-index: 99999;
  }
  
  .dialog-modal{
    z-index: 99999;
    width: 30rem;
    background: rgb(255, 255, 255);
    padding: 1.5rem 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-radius: 0.3rem;
  }
  
  .dialog-modal-title {
    font-weight: bold;
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: rgb(100, 100, 100);
  }
  
  .dialog-modal-body {
    color: rgb(180, 180, 180);
  }
  </style>