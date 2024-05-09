<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">
      <div class="list-group-accent">
        <div v-if="objModulo.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">
        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Nome</label>
              <span class="col-md-8">{{ objModulo.nome || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Módulo Pai</label>
              <span class="col-md-8">{{ objModulo.modulo_pai ? objModulo.modulo_pai.nome : '--' }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">URL</label>
              <span class="col-md-8">{{ objModulo.url || '--' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/modulo/alterar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <router-link to="/configuracoes/modulo" class="btn btn-link">Voltar</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'InfoModulo',
  computed: {
    ...mapState('modulos', ['listaModulo', 'objModulo', 'estaCarregando'])
  },
  created () {
    this.setModuloSelecionado(this.$route.params.id)
    this.getModulo()
  },
  methods: {
    ...mapActions('modulos', ['getListaModulo', 'getModulo']),
    ...mapMutations('modulos', ['setModulo', 'setModuloSelecionado'])
  }
}
</script>
