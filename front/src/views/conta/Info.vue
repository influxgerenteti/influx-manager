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
              <span class="col-md-8">{{ item.descricao || '--' }}</span>
            </div>
            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Banco</label>
              <span class="col-md-8">{{ item.banco ? item.banco.descricao || '--' : '--' }}</span>
            </div>

            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Agência</label>
              <span class="col-md-8">{{ item.numero_agencia || '--' }}{{ item.digito_agencia ? ' - ' + item.digito_agencia : '' }}</span>
            </div>

            <div class="info-row col-md-12">
              <label class="col-form-label col-md-4">Conta Corrente</label>
              <span class="col-md-8">{{ item.conta_corrente || '--' }}{{ item.digito_conta_corrente ? ' - ' + item.digito_conta_corrente : '' }}</span>
            </div>
          </div>

        </div>

        <div class="col-md-6">
          <div class="info-row col-md-12">
            <label class="col-form-label col-md-4">Observações</label>
            <span class="col-md-8">{{ item.observacao || '--' }}</span>
          </div>
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

          <div class="row p-3">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group row mb-0">
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Código da Empresa no Banco</label>
                    <span class="col-md-8">{{ item.empresa_no_banco || '--' }}</span>
                  </div>
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Variação em Carteira</label>
                    <span class="col-md-8">{{ item.variacao_carteira || '--' }}</span>
                  </div>
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Dias Floating</label>
                    <span class="col-md-8">{{ item.numero_dias_floating || '--' }}</span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row mb-0">
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Fluxo de Caixa</label>
                    <span class="col-md-8">{{ item.considera_fluxo_caixa ? 'Sim' : 'Não' }}</span>
                  </div>
                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Telefone</label>
                    <span class="col-md-8">{{ item.telefone_contato || '--' | formatarTelefone }}</span>
                  </div>

                  <div class="info-row col-md-12">
                    <label class="col-form-label col-md-4">Saldo</label>
                    <span class="col-md-8">{{ item.valor_saldo || '--' | formatarMoeda }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="content-sector-extra p-3 mt-3">
              <h5 class="d-flex justify-content-between title-module">Boleto
                <div v-if="!item.banco_emite_boleto" class="d-flex justify-content-center text-uppercase align-self-center item-disabled p-1 text-uppercase">desabilitado</div>
              </h5>

              <div v-if="item.banco_emite_boleto" class="row">
                <div class="col-md-6">
                  <div class="form-group row mb-0">

                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Instrução</label>
                      <span class="col-md-8">{{ item.primeira_instrucao || '--' }}{{ item.segunda_instrucao ? ' - ' + item.segunda_instrucao : '' }}</span>
                    </div>

                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Arquivo de Cobrança</label>
                      <span class="col-md-8">{{ item.numero_sequencia_arquivo_cobranca ? ' - ' + item.numero_sequencia_arquivo_cobranca : '' }}</span>
                    </div>

                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Dias para Devolução</label>
                      <span class="col-md-8">{{ item.numero_dias_devolucao || '--' }}</span>
                    </div>
                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Dias para Protesto</label>
                      <span class="col-md-8">{{ item.numero_dias_protesto || '--' }}</span>
                    </div>

                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Carteira</label>
                      <span class="col-md-8">{{ item.carteira || '--' }}</span>
                    </div>
                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Multa</label>
                      <span class="col-md-8">{{ item.percentual_multa + '%' || '--' }}</span>
                    </div>

                  </div>

                </div>

                <div class="col-md-6">
                  <div class="form-group row mb-0">
                    <div class="info-row col-md-12">
                      <label class="col-form-label col-md-4">Observações</label>
                      <span class="col-md-8">{{ item.observacao_boleto || '--' }}</span>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </b-collapse>
      </div>

      <div class="content-sector sector-secondary">
        <div class="collapse-toggle" @click="isOpenComplementares=!isOpenComplementares">
          <div v-b-toggle.complementares-toggle class="d-flex justify-content-between list-group-item head-content-sector border-0">Informações Complementares
            <div :class="isOpenComplementares ? 'collapse-toggle-opened' : 'collapse-toggle-closed'" class="collapse-toggle-state">
              <font-awesome-icon icon="sort-down" />
            </div>
          </div>
        </div>

        <b-collapse id="complementares-toggle" class="col-md-12" visible>
          <div class="row p-3">
            <div class="col-md-6">
              <div class="form-group row mb-0">
                <div class="info-row col-md-12">
                  <label class="col-form-label col-md-4">Modalidade</label>
                  <span class="col-md-8">{{ item.modalidade || '--' }}</span>
                </div>
              </div>
            </div>
          </div>
        </b-collapse>
      </div>
    </div>

    <div class="form-group row mt-3">
      <div class="col-md-12">
        <button type="button" class="btn btn-roxo" @click="$router.push(`/configuracoes/conta/atualizar/${$route.params.id}`)">
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
      isOpenComplementares: true
    }
  },
  computed: mapState('conta', ['item', 'estaCarregando']),
  created () {
    // this.LIMPAR_ITEM()

    const id = this.$route.params.id
    if (id) {
      this.SET_SELECIONADO(id)
      this.getItem()
    }
  },
  methods: {
    ...mapActions('conta', ['getItem']),
    ...mapMutations('conta', ['SET_SELECIONADO', 'LIMPAR_ITEM']),

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/conta')
    }
  }
}
</script>
<style scoped>
.observacao {
  overflow: auto;
  height: calc(100% - 35px);
  height: -webkit-calc(100% - 35px);
  height: -moz-calc(100% - 35px);
}
p.text-justify {
  word-wrap: break-word;
}
.content-sector-extra {
  flex-grow: 1;
}
</style>
