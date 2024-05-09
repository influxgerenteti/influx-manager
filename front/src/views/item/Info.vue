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
        <div class="info-row col-md-12">
          <label class="col-form-label col-md-2">Descrição</label>
          <span class="col-md-10">{{ item.descricao || '--' }}</span>
        </div>
      </div>

      <div class="content-sector sector-azul">
        <div class="collapse-toggle" @click="isOpenDefinicao=!isOpenDefinicao">
          <div v-b-toggle.definicao-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Definição
            <div :class="isOpenDefinicao ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="definicao-toggle" class="col-md-12" visible>

          <div class="definicao p-3">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group row mb-0">
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Unidade de Medida</label>
                    <span class="col-md-8">{{ item.unidade_medida || '--' }}</span>
                  </div>
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Classificação Fiscal</label>
                    <span class="col-md-8">{{ item.classificacao_fiscal || '--' }}</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Narrativa</label>
                  <span class="col-md-8">{{ item.narrativa || '--' }}</span>
                </div>
              </div>
            </div>

            <div class="content-sector-extra p-3 mt-3">
              <h5 class="title-module">Estoque</h5>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group row mb-0">

                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Mínimo</label>
                      <span class="col-md-8">{{ item.estoque_minimo || '--' }}</span>
                    </div>

                  </div>
                </div>
              </div>

            </div>

          </div>

        </b-collapse>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/item/atualizar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <b-btn variant="link" @click="voltar()">Voltar</b-btn>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
export default {
  data () {
    return {
      isOpenDefinicao: true,
      isOpenEstoque: true
    }
  },
  computed: mapState('item', ['item', 'estaCarregando']),
  created () {
    this.SET_SELECIONADO(this.$route.params.id)
    this.getItem()
  },
  methods: {
    ...mapMutations('item', ['SET_SELECIONADO', 'LIMPAR_ITEM']),
    ...mapActions('item', ['getItem']),

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/item')
    }
  }
}
</script>
<style scoped>
.definicao {
  margin-right: -15px;
  margin-left: -15px;
}
</style>
