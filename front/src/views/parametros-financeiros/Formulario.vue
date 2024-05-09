<template>
  <div class="animated fadeIn">
    <form @submit.prevent="salvar()">
      <div class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="table-responsive-sm">
        <h5 class="title-module mb-2">Movimentações</h5>

        <div class="form-group row">
          <div class="input-group col-md-3">
            <label v-help-hint="'form-franqueada_percentual_juro_dia'" class="col-form-label" for="percentual_juro_dia">Percentual de Juros por dia</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">%</span></div>
              <vue-numeric id="percentual_juro_dia" :precision="4" :empty-value="null" v-model="percentual_juro_dia" :max="99.9999" separator="." class="form-control"/>
            </div>
          </div>

          <div class="input-group col-md-3">
            <label v-help-hint="'form-franqueada_percentual_multa'" class="col-form-label" for="percentual_multa">Percentual de Multa aplicado</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend pre-icon"><span id="pre-icon-desconto" class="input-group-text">%</span></div>
              <input v-money="moeda" id="percentual_multa" v-model="percentual_multa" type="text" class="form-control" maxlength="6">
            </div>
          </div>

          <!-- <div class="col-md-3">
            <label v-help-hint="'form-franqueada_tipo_movimento_conta_receber'" for="tipo_movimento_conta_receber" class="col-form-label">Contas a Receber</label>
            <select id="tipo_movimento_conta_receber" v-model="tipo_movimento_conta_receber" class="custom-select form-control">
              <option value>Nenhum</option>
              <template v-for="(conta_receber, index) in listaTipoMovimento">
                <option v-if="conta_receber.tipo_operacao == 'C'" :key="index" :value="conta_receber.id">{{ conta_receber.descricao }}</option>
              </template>
            </select>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'form-franqueada_tipo_movimento_conta_pagar'" for="tipo_movimento_conta_pagar" class="col-form-label">Contas a Pagar</label>
            <select id="tipo_movimento_conta_pagar" v-model="tipo_movimento_conta_pagar" class="custom-select form-control">
              <option value>Nenhum</option>
              <template v-for="(conta_pagar, index) in listaTipoMovimento">
                <option v-if="conta_pagar.tipo_operacao == 'D'" :key="index" :value="conta_pagar.id">{{ conta_pagar.descricao }}</option>
              </template>
            </select>
          </div> -->
          <!-- </div>

        <div class="row form-group"> -->
          <div class="col-md-3">
            <label v-help-hint="'form-parametros-franqueadora_limite_dias_alteracao_documento'" for="limite_dias_alteracao_documento" class="col-form-label">Limite para alteração de documento (dias)</label>
            <input v-mask="'#########'" id="limite_dias_alteracao_documento" v-model="limite_dias_alteracao_documento" type="text" class="form-control">
          </div>

          <div class="col-md-3">
            <label v-help-hint="'form-franqueada_percentual_desconto_a_vista'" for="percentual_desconto_a_vista" class="col-form-label">Percentual Desconto a Vista</label>
            <select id="percentual_desconto_a_vista" v-model="percentual_desconto_a_vista" class="custom-select form-control">
              <template v-for="(desconto, index) in listaDescontos">
                <option :key="index" :value="index">{{ desconto }}%</option>
              </template>
            </select>
          </div>
        </div>

        <h5 class="title-module mt-3 mb-2">Dias subsequentes</h5>

        <div class="form-group">
          <g-table :autoheight="true">
            <thead>
              <tr>
                <th>Dia Subsequente</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <template v-if="carregouDiasFranqueada">
                <perfect-scrollbar>
                  <tr v-for="(item, index) in listaDias" :key="index">
                    <td data-label="Dia">{{ item.descricao }}</td>
                    <td data-label="Selecione">
                      <b-form-checkbox :id="`diaSelecionado_${index}`" :checked="item.selecionado" class="m-0 pl-3" @change="mudarDiaSelecionado(item)"/>
                    </td>
                  </tr>
                  <div v-if="!listaDias.length && !estaCarregandoDias" class="busca-vazia">
                    <p>Nenhum resultado encontrado.</p>
                  </div>
                </perfect-scrollbar>
              </template>
            </tbody>
          </g-table>
        </div>
      </div>

      <div>
        <button type="submit" class="btn btn-verde">Salvar</button>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'FormularioParametrosFinanceiros',
  data () {
    return {
      listaDescontos: [0, 1, 2, 3, 4, 5],
      percentual_desconto_a_vista: null,
      percentual_multa: null,
      percentual_juro_dia: null,
      tipo_movimento_conta_pagar: null,
      tipo_movimento_conta_receber: null,
      limite_dias_alteracao_documento: '',
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      },
      carregouDiasFranqueada: false,
      diasSelecionados: []
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('franqueadas', ['objFranqueada', 'estaCarregando']),
    ...mapState('tipoMovimentoConta', {listaTipoMovimento: 'lista'}),
    ...mapState('diasSubsequentes', { listaDias: 'listaDias', listaDiasDaFranqueada: 'listaDiasDaFranqueada', estaCarregandoDias: 'estaCarregando' })
  },

  watch: {
    objFranqueada (value) {
      this.percentual_desconto_a_vista = value.percentual_desconto_a_vista
      this.percentual_multa = value.percentual_multa
      this.percentual_juro_dia = value.percentual_juro_dia
      this.tipo_movimento_conta_receber = value.tipo_movimento_conta_receber ? value.tipo_movimento_conta_receber.id : null
      this.tipo_movimento_conta_pagar = value.tipo_movimento_conta_pagar ? value.tipo_movimento_conta_pagar.id : null
      this.limite_dias_alteracao_documento = value.limite_dias_alteracao_documento
    },

    percentual_desconto_a_vista (value) {
      this.setPercentualDescontoAVista(value)
    },

    percentual_multa (value) {
      this.setPercentualMulta(value)
    },

    percentual_juro_dia (value) {
      this.setPercentualJuroDia(value)
    },

    tipo_movimento_conta_pagar (value) {
      this.setMovimentoContasPagar(value)
    },

    tipo_movimento_conta_receber (value) {
      this.setMovimentoContasReceber(value)
    },

    limite_dias_alteracao_documento (value) {
      this.SET_LIMITE_DIAS_ALTERACAO_DOCUMENTO(value)
    }
  },

  mounted () {
    this.$store.dispatch('tipoMovimentoConta/getLista')

    this.limparFranqueada()
    this.setFranqueadaSelecionada(this.$store.state.root.usuarioLogado.franqueadaSelecionada)
    this.getFranqueada()

    this.diasSelecionados = []
    this.filtrarDias()
  },

  methods: {
    ...mapMutations('franqueadas', ['limparFranqueada', 'setFranqueadaSelecionada', 'setPercentualMulta', 'setPercentualJuroDia', 'setMovimentoContasPagar', 'setMovimentoContasReceber', 'setDescontoSuperAmigosAtivo', 'setDescontoSuperAmigosTurbinadoAtivo', 'SET_LIMITE_DIAS_ALTERACAO_DOCUMENTO', 'setPercentualDescontoAVista']),
    ...mapActions('franqueadas', ['getFranqueada', 'atualizarFranqueada']),
    ...mapActions('diasSubsequentes', ['listarTodos', 'buscarPorFranqueadaAtual', 'salvarDiasSubsequentesFranqueadaAtual']),

    salvar () {
      this.salvarDiasSubsequentesFranqueadaAtual(this.diasSelecionados)
        .then(() => {
          this.atualizarFranqueada()
        })
    },

    filtrarDias () {
      this.listarTodos()
        .then(response => {
          this.buscarPorFranqueadaAtual()
            .then(response => {
              this.carregouDiasFranqueada = true
              this.listaDias.forEach(item => {
                let resultado = this.listaDiasDaFranqueada.find(obj => {
                  return item.id === obj.id
                })
                if (resultado !== undefined) {
                  item.selecionado = true
                  this.diasSelecionados.push(item.id)
                }
              })
            })
            .catch(response => {
              this.carregouDiasFranqueada = true
            })
        })
    },

    mudarDiaSelecionado (item) {
      item.selecionado = !item.selecionado
      if (item.selecionado === false) {
        let indexRemovido = this.diasSelecionados.indexOf(item.id)
        this.diasSelecionados.splice(indexRemovido, 1)
      } else {
        this.diasSelecionados.push(item.id)
      }
    }
  }
}
</script>
