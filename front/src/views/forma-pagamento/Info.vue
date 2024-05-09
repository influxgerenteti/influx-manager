<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">

      <div class="list-group-accent">
        <div v-if="item.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Descrição</label>
              <span class="col-md-8">{{ item.descricao }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Descrição Abreviada</label>
              <span class="col-md-8">{{ item.descricao_abreviada }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/forma-pagamento/alterar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <router-link to="/configuracoes/forma-pagamento" class="btn btn-link">Voltar</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'InfoFormaPagamento',
  data () {
    return {
      isOpen: true
    }
  },
  computed: {
    ...mapState('formaPagamento', ['item', 'estaCarregando'])
  },
  created () {
    this.SET_SELECIONADO(this.$route.params.id)
    this.getItem()
  },
  methods: {
    ...mapActions('formaPagamento', ['getItem']),
    ...mapMutations('formaPagamento', ['SET_ITEM', 'SET_SELECIONADO'])
  }
}
</script>
