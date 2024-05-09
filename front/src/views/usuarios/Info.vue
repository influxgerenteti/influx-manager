<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">
      <div class="list-group-accent">
        <div v-if="objUsuario.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Nome</label>
              <span class="col-md-8">{{ objUsuario.nome || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">CPF</label>
              <span class="col-md-8">{{ objUsuario.cpf || '--' | formatarCPF }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">E-mail</label>
              <span class="col-md-8">{{ objUsuario.email || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Franqueada Padrão</label>
              <span class="col-md-8">{{ objUsuario.franqueada_padrao ? objUsuario.franqueada_padrao.nome : '--' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="content-sector sector-azul">
        <div class="collapse-toggle" @click="isOpenFranqueada=!isOpenFranqueada">
          <div v-b-toggle.licao-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Franqueadas
            <div :class="isOpenFranqueada ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="licao-toggle" class="col-md-12" visible>
          <div class="row p-3">
            <table class="table-scroll table b-table table-borderless">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Telefone</th>
                  <th>CNPJ</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in objUsuario.franqueadas" :key="item.id">
                  <td><span>{{ item.nome }}</span></td>
                  <td>
                    <span>{{ item.telefone || '--' | formatarTelefone }}</span>
                  </td>
                  <td>
                    <span>{{ item.cnpj || '--' | formatarCNPJ }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </b-collapse>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-8">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/usuario/alterar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <router-link to="/configuracoes/usuario" class="btn btn-link">Voltar</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  data () {
    return {
      isOpenFranqueada: true
    }
  },
  computed: {
    ...mapState('usuarios', ['listaUsuarios', 'objUsuario', 'estaCarregando'])
  },
  created () {
    this.setUsuarioSelecionado(this.$route.params.id)
    this.getUsuario()
  },
  methods: {
    ...mapActions('usuarios', ['getListaUsuarios', 'getUsuario']),
    ...mapMutations('usuarios', ['setUsuario', 'setUsuarioSelecionado'])
  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 392px);
  height: -webkit-calc(100vh - 392px);
  height: -moz-calc(100vh - 392px);
  margin-bottom: 0;
}
</style>
