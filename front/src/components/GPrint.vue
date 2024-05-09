<template>
    <button
        id="print-button"
        title="Imprimir"
        @click="imprimir()"
        :disabled="disabled"
        class="btn btn-cinza btn-block text-uppercase no-print">
        <font-awesome-icon icon="print" />
        Imprimir
    </button>
</template>
  
<script>  
  export default {
    name: 'GPrint',
    props: {
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        }
    },

    methods: {
        imprimir() {
            window.print();
        },
   
    }
}
</script>
<style>
</style>
