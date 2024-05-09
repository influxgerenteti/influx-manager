<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="body-sector info-view">
      <div class="list-group-accent">
        <div v-if="objFranqueada.situacao === 'A'" class="list-group-item-success p-3">Ativo</div>
        <div v-else class="list-group-item-danger p-3">Inativo</div>
      </div>

      <div class="row p-3">
        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Nome</label>
              <span class="col-md-8">{{ objFranqueada.nome || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">CNPJ</label>
              <span class="col-md-8">{{ objFranqueada.cnpj | formatarCNPJ }}</span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group row mb-0">
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Razão Social</label>
              <span class="col-md-8">{{ objFranqueada.razao_social || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Inscrição Estadual</label>
              <span class="col-md-8">{{ objFranqueada.inscricao_estadual || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Endereço</label>
              <span class="col-md-8">{{ objFranqueada.endereco || '--' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="content-sector sector-azul">
        <div class="collapse-toggle" @click="isOpenContatos=!isOpenContatos">
          <div v-b-toggle.contatos-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Contatos
            <div :class="isOpenContatos ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="contatos-toggle" class="col-md-12" visible>
          <div class="row p-3">
            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">E-mail Direção</label>
                  <span class="col-md-8">{{ objFranqueada.email_direcao || '--' }}</span>
                </div>

                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">E-mail Franqueada</label>
                  <span class="col-md-8">{{ objFranqueada.email || '--' }}</span>
                </div>

                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">E-mail Comercial</label>
                  <span class="col-md-8">{{ objFranqueada.email_comercial || '--' }}</span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Telefone</label>
                  <span class="col-md-8">{{ objFranqueada.telefone || '--' | formatarTelefone }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Telefone Secundário</label>
                  <span class="col-md-8">{{ objFranqueada.telefone_secundario || '--' | formatarTelefone }}</span>
                </div>
              </div>
            </div>
          </div>
        </b-collapse>
      </div>

      <div class="content-sector sector-secondary">
        <div class="collapse-toggle" @click="isOpenMovimentacoes=!isOpenMovimentacoes">
          <div v-b-toggle.movimentacao-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Movimentações
            <div :class="isOpenMovimentacoes ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="movimentacao-toggle" class="col-md-12" visible>

          <div class="row p-3">
            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Dias em aberto</label>
                  <span class="col-md-8">{{ objFranqueada.dias_em_abertos_movimentos }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Contas a Receber</label>
                  <span class="col-md-8">{{ objFranqueada.tipo_movimento_conta_receber ? objFranqueada.tipo_movimento_conta_receber.descricao : '--' }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Contas a Pagar</label>
                  <span class="col-md-8">{{ objFranqueada.tipo_movimento_conta_pagar ? objFranqueada.tipo_movimento_conta_pagar.descricao : '--' }}</span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Desconto Super Amigos</label>
                  <span class="col-md-8">{{ objFranqueada.desconto_super_amigos | formatarMoeda }}</span>
                </div>
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Sábado</label>
                  <span class="col-md-8">{{ objFranqueada.sabado_dia_util === 1 ? 'Dia útil' : 'Dia não útil' }}</span>
                </div>
              </div>
            </div>
          </div>
        </b-collapse>
      </div>

    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/franqueada/alterar/${$route.params.id}`)">
          <font-awesome-icon icon="pen" /> Atualizar
        </button>
        <router-link to="/configuracoes/franqueada" class="btn btn-link">Voltar</router-link>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'InfoFranqueada',
  data () {
    return {
      isOpenContatos: true,
      isOpenMovimentacoes: true
    }
  },
  computed: {
    ...mapState('franqueadas', ['listaFranqueada', 'objFranqueada', 'estaCarregando'])
  },
  created () {
    this.setFranqueadaSelecionada(this.$route.params.id)
    this.getFranqueada()
  },
  methods: {
    ...mapActions('franqueadas', ['getListaFranqueada', 'getFranqueada']),
    ...mapMutations('franqueadas', ['setFranqueada', 'setFranqueadaSelecionada'])
  }
}
</script>
