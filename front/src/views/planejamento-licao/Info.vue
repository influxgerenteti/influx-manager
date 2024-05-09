<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">
      <div class="list-group-accent">
        <div v-if="itemSelecionado.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">
        <div class="info-row col-md-12">
          <label class="col-form-label col-md-2">Descrição</label>
          <span class="col-md-10">{{ itemSelecionado.descricao || '--' }}</span>
        </div>

      </div>

      <div class="content-sector sector-azul">
        <div class="collapse-toggle" @click="isOpenLicao=!isOpenLicao">
          <div v-b-toggle.licao-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Lições
            <div :class="isOpenLicao ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="licao-toggle" class="col-md-12" visible>
          <div class="row p-3">
            <table class="table-scroll table b-table table-borderless">
              <thead>
                <tr>
                  <th class="number">Número</th>
                  <th>Descrição</th>
                  <th>Observação</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in itemSelecionado.licaos" :key="item.id">
                  <td class="number"><span>{{ item.numero }}</span></td>
                  <td>
                    <span>{{ item.descricao || '--' }}</span>
                  </td>
                  <td>
                    <span>{{ item.observacao || '--' }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </b-collapse>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/planejamento-licao/alterar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <router-link to="/configuracoes/planejamento-licao" class="btn btn-link">Voltar</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  data () {
    return {
      isOpenLicao: true
    }
  },
  computed: {
    ...mapState('planejamentoLicao', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando'])
  },
  created () {
    this.SET_ITEM_SELECIONADO_ID(this.$route.params.id)
    this.buscar()
  },
  methods: {
    ...mapActions('planejamentoLicao', ['buscar']),
    ...mapMutations('planejamentoLicao', ['SET_ITEM_SELECIONADO_ID'])
  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 360px);
  height: -webkit-calc(100vh - 360px);
  height: -moz-calc(100vh - 360px);
  margin-bottom: 0;
}

.number {
  max-width: 100px;
}

.options-licao div {
  width: 1.25em;
  margin: auto;
}

.invalid-feedback:not(.planejamento-licao) {
  position: relative;
}
</style>
