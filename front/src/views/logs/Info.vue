<template>
  <div class="animated fadeIn">
    <!-- <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div> -->

    <div class="body-sector info-view">
      <div class="p-3">

        <div class="row">
          <div class="col-md-6">
            <div class="form-group row mb-0">
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">Data</label>
                <span class="col-md-8">{{ objLog.data || '--' | formatarData }}</span>
              </div>
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">Usuário</label>
                <span class="col-md-8">{{ objLog.usuario ? objLog.usuario.nome || '--' : '--' }}</span>
              </div>
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">IP de Origem</label>
                <span class="col-md-2">{{ objLog.ip_origem || '--' }}</span>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group row mb-0">
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">Instância</label>
                <span class="col-md-8">{{ objLog.id_franqueada || '--' }}</span>
              </div>
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">Tipo de Evento</label>
                <span class="col-md-8">{{ objLog.tipo_evento || '--' | tipoEvento }}</span>
              </div>
              <div class="info-row col-md-12">
                <label class="col-form-label col-md-4">Informações</label>
                <pre class="col-md-8">{{ objLog.info_evento || '--' | infoEvento }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-8">
        <router-link to="/configuracoes/log" class="btn btn-link">Voltar</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'InfoLog',
  computed: mapState('logs', ['listaLogs', 'objLog', 'estaCarregando']),
  created () {
    this.setLogSelecionado(this.$route.params.id)
    this.getLog()
  },
  methods: {
    ...mapActions('logs', ['getLog']),
    ...mapMutations('logs', ['setLog', 'setLogSelecionado'])
  }
}
</script>
<style scoped>
pre {
  padding-top: calc(0.375rem + 1px);
  padding-bottom: calc(0.375rem + 1px);
  margin-bottom: 0;
}
</style>
