<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div class="body-sector p-3">
        <div v-if="isEdit" class="form-loading">
          <load-placeholder :loading="estaCarregando" />
        </div>

        <div class="form-group row">
          <div class="col-md-6">
            <label v-help-hint="'filtro-cheques-pagar-receber_titular_cheque'" for="titular_cheque" class="col-form-label">Titular *</label>
            <input id="titular_cheque" v-model="objCheque.titular" type="text" class="form-control" maxlength="50" required >
            <div class="invalid-feedback">Informe o titular!</div>
          </div>
          <div class="col-md-6">
            <label v-help-hint="'filtro-cheques-pagar-receber_numero_cheque'" for="numero_cheque" class="col-form-label">Número do cheque *</label>
            <input id="numero_cheque" v-model="objCheque.numero" type="text" class="form-control" required maxlength="9" @input="validaNumero('numero')">
            <div class="invalid-feedback">Informe o número do cheque!</div>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_banco_cheque'" for="banco_cheque" class="col-form-label">Banco *</label>
            <input id="banco_cheque" v-model="objCheque.banco" type="text" class="form-control" required maxlength="50">
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_agencia_cheque'" for="agencia_cheque" class="col-form-label">Agência *</label>
            <input id="agencia_cheque" v-model="objCheque.agencia" type="text" class="form-control" required maxlength="9">
            <div class="invalid-feedback">Informe a Agência!</div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_conta_cheque'" for="conta_cheque" class="col-form-label">Conta *</label>
            <input id="conta_cheque" v-model="objCheque.conta" type="text" class="form-control" maxlength="20" required>
            <div class="invalid-feedback">Informe a Conta!</div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_cheque'" for="complemento_cheque" class="col-form-label">Complemento</label>
            <input id="complemento_cheque" v-model="objCheque.complemento" type="text" class="form-control" maxlength="250">
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_data_entrada_cheque'" for="data_entrada_cheque" class="col-form-label">Data de Entrada</label>
            <span id="data_entrada_cheque" class="form-control form-control-disabled">{{ objCheque.data_entrada | formatarData }}</span>
          </div>
          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_data_bom'" for="data_bom_para_selecionado" class="col-form-label">Bom para</label>
            <div class="row">
              <div class="col">
                <div class="input-group">
                  <g-datepicker :element-id="'data_bom_para_selecionado'" :value="data_bom_para_selecionado" :selected="setDataBomPara"/>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_data_bom'" for="data_baixa_cheque" class="col-form-label">Data de baixa</label>
            <input id="data_baixa_cheque" :value="objCheque.data_baixa ? dateToString(new Date(objCheque.data_baixa)) : null" type="text" class="form-control" readonly>
          </div>

          <div class="col-md-3">
            <label v-help-hint="'filtro-cheques-pagar-receber_valor_cheque'" for="valor_cheque" class="col-form-label">Valor</label>
            <vue-numeric id="valor_cheque" :precision="2" :empty-value="null" v-model="objCheque.valor" :max="9999999.99" separator="." class="form-control" required />
          </div>
        </div>

        <div class="row">

          <div v-if="objCheque.data_devolucao" :class="objCheque.motivo_devolucao_cheque ? 'col-md-3' : 'col-md-6'">
            <label v-help-hint="'filtro-cheques-pagar-receber_data_devolucao_cheque'" for="data_devolucao_cheque" class="col-form-label">Data de devolução</label>
            <span class="d-block form-control form-control-disabled">{{ ISOToString(objCheque.data_devolucao) }}</span>
          </div>

          <div v-if="objCheque.motivo_devolucao_cheque" :class="objCheque.data_devolucao ? 'col-md-3' : 'col-md-6'">
            <label v-help-hint="'filtro-cheques-pagar-receber_motivo_devolucao_cheque'" for="motivo_devolucao_cheque" class="col-form-label">Motivo de devolução</label>
            <span class="d-block form-control form-control-disabled">{{ objCheque.motivo_devolucao_cheque.descricao }}</span>
          </div>
          <div class="col-md-6">
            <label v-help-hint="'filtro-cheques-pagar-receber_observacao_cheque'" for="observacao_cheque" class="col-form-label">Observação</label>
            <textarea id="observacao_cheque" v-model="objCheque.observacao" class="form-control" placeholder="Observação" rows="6" maxlength="5000"></textarea>
            <span class="text-secondary">Limite de caracteres: {{ 5000 - objCheque.observacao.length }}</span>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <button :disabled="salvando" type="submit" class="btn btn-verde">Salvar</button>
          <router-link to="/financeiro/cheques-pagar-receber" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>

    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {dateToString, stringToISODate, ISOToString} from '../../utils/date'

export default {
  name: 'ChequesPagarReceberFormulario',
  data () {
    return {
      data_bom_para_selecionado: '',
      errorMsg: '',
      isValid: true,
      isEdit: false,
      salvando: false,
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: true
      }
    }
  },

  validations: {
    objCheque: {
      titular: {required},
      conta: {required},
      agencia: {required},
      numero: {required}
    }
  },

  computed: {
    ...mapState('chequesPagarReceber', ['objCheque', 'estaCarregando'])
  },

  mounted () {
    this.LIMPAR_CHEQUE()
    const id = this.$route.params.id

    if (id) {
      this.isEdit = true
      this.SET_CHEQUE_SELECIONADO(id)
      this.getChequesPagarReceber()
        .then(() => {
          this.data_entrada_cheque = dateToString(new Date(this.objCheque.data_entrada))
          this.data_bom_para_selecionado = (this.objCheque.data_bom_para ? dateToString(new Date(this.objCheque.data_bom_para)) : '')
        })
    } else {
      this.objCheque.data_entrada = new Date()
      this.objCheque.situacao = 'P'
    }
  },

  methods: {
    ...mapActions('chequesPagarReceber', ['getChequesPagarReceber', 'criarChequePagarReceber', 'atualizarChequePagarReceber']),
    ...mapMutations('chequesPagarReceber', ['SET_CHEQUE', 'LIMPAR_CHEQUE', 'SET_CHEQUE_SELECIONADO']),

    dateToString: dateToString,
    ISOToString: ISOToString,

    validaNumero (campo) {
      this.objCheque[campo] = this.objCheque[campo].replace(/[^\d.]/g, '')
    },

    setDataBomPara (value) {
      this.data_bom_para_selecionado = value
      this.objCheque.data_bom_para = stringToISODate(value, true)
    },

    voltar () {
      this.SET_CHEQUE_SELECIONADO(null)
      this.LIMPAR_CHEQUE()
      this.$router.push('/financeiro/cheques-pagar-receber')
    },

    salvar () {
      if (this.$v.$invalid) {
        this.isValid = false
        return
      }

      this.salvando = true

      if (this.isEdit) {
        this.objCheque.atendente_usuario = this.objCheque.atendente_usuario.id
        this.atualizarChequePagarReceber()
          .then(() => {
            this.voltar()
            this.salvando = false
          })
      } else {
        this.criarChequePagarReceber()
          .then(() => {
            this.voltar()
            this.salvando = false
          })
      }
    }
  }
}
</script>
