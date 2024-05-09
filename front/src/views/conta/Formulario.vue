<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar(true)">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="!isEdit" class="form-loading">
        <load-placeholder :loading="verificaCarregamento(loadCount,1)" />
      </div>
      <div class="body-sector">
        <div class="animated fadeIn p-3">
          <div class="form-group row row-flex row-flex-wrap">
            <div class="col-md-6">
              <div class="form-group row">
                <div class="col-md-6">
                  <label v-help-hint="'form-conta_descricao'" for="descricao" class="col-form-label">Descrição *</label>
                  <input id="descricao" v-model="descricao" type="text" class="form-control" placeholder="" maxLength="20" required>
                  <div class="invalid-feedback">Informe a descrição!</div>
                </div>

                <div class="col-md-6">
                  <label v-help-hint="'form-conta_banco'" for="banco" class="col-form-label">Banco *</label>
                  <select id="banco" v-model="banco" class="custom-select form-control" required>
                    <option value>Nenhum</option>
                    <template v-for="(item, index) in lista">
                      <option :key="index" :value="item.id">{{ item.descricao }}</option>
                    </template>
                  </select>
                  <div class="invalid-feedback">Selecione um banco!</div>
                </div>
              </div>
              <div class="form-group row content-sector sector-roxo-c mb-0 pb-1">
                <div class="col-md-9">
                  <label v-help-hint="'form-conta_numero_agencia'" for="numero_agencia" class="col-form-label">Agência</label>
                  <input v-mask="'##########'" id="numero_agencia" :tokens="number" v-model="numero_agencia" type="text" class="form-control" placeholder="Número" maxLength="10">
                </div>

                <div class="col-md-3 mt-1 d-flex align-items-end">
                  <input v-mask="'XXX'" id="digito_agencia" :tokens="character" v-model="digito_agencia" type="text" class="form-control" placeholder="Dígito" maxLength="3">
                </div>
              </div>

              <div class="form-group row content-sector sector-verde-c">
                <div class="col-md-9">
                  <label v-help-hint="'form-conta_corrente'" for="conta_corrente" class="col-form-label">Conta Corrente</label>
                  <input v-mask="'###############'" id="conta_corrente" :tokens="number" v-model="conta_corrente" type="text" class="form-control" placeholder="Número" maxLength="15">
                </div>

                <div class="col-md-3 mt-1 d-flex align-items-end">
                  <input v-mask="'XX'" id="digito_conta_corrente" :tokens="character" v-model="digito_conta_corrente" type="text" class="form-control" placeholder="Dígito" maxLength="2">
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label v-help-hint="'form-conta_observacao'" for="observacao" class="col-form-label">Observações</label>
              <textarea id="observacao" v-model="observacao" class="form-control full-textarea" placeholder="" rows="6" maxLength="5000"></textarea>
              <span class="text-secondary">Limite de caracteres: {{ 5000 - observacao.length }}</span>
            </div>
          </div>
        </div>

        <div class="content-sector sector-azul p-3">
          <h5 class="title-module mb-2">Definição</h5>

          <div class="content-sector-extra p-3">
            <div class="col-sm-2">
              <h5 class="title-module mb-2">Boleto</h5>
              <div class="custom-control custom-checkbox">
                <input v-b-toggle.boleto-toggle id="banco_emite_boleto" v-model="banco_emite_boleto" type="checkbox" class="custom-control-input">
                <label v-help-hint="'form-conta_banco_emite_boleto'" class="custom-control-label" for="banco_emite_boleto">Emitir</label>
              </div>
            </div>
            <b-collapse id="boleto-toggle" :visible="banco_emite_boleto">
              <div class="form-group row mb-0">

                <div class="col-md-12">

                  <div class="form-group row">

                    <div class="col-md-3">
                      <label v-help-hint="'form-conta_dias_floating'" for="numero_dias_floating" class="col-form-label">Dias para Compensação</label>
                      <input v-mask="'#########'" id="numero_dias_floating" :tokens="number" v-model="numero_dias_floating" type="text" class="form-control mt-auto" placeholder="" maxLength="11">
                    </div>

                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_empresa_no_banco'" for="empresa_no_banco" class="col-form-label">Código da empresa no banco</label>
                      <input id="empresa_no_banco" v-model="empresa_no_banco" type="text" class="form-control mt-auto" placeholder="" maxLength="20">
                    </div>

                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_numero_sequencia_arquivo_cobranca'" for="numero_sequencia_arquivo_cobranca" class="col-form-label">Número sequencial de remessa</label>
                      <input v-mask="'#########'" id="numero_sequencia_arquivo_cobranca" :tokens="number" v-model="numero_sequencia_arquivo_cobranca" type="text" class="form-control" placeholder="" disabled>
                    </div>

                    <div class="col-md-2">
                      <label v-help-hint="'form-conta_carteira'" for="carteira" class="col-form-label">Carteira</label>
                      <input v-mask="'###'" id="carteira" :tokens="number" v-model="carteira" type="text" class="form-control mt-auto" placeholder="" maxLength="3">
                    </div>

                    <div class="col-md-2">
                      <label v-help-hint="'form-conta_variacao_carteira'" for="variacao_carteira" class="col-form-label">Variação carteira</label>
                      <input id="variacao_carteira" v-model="variacao_carteira" type="text" class="form-control mt-auto" placeholder="" maxLength="3">
                    </div>

                  </div>

                  <div class="form-group row">
                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_numero_dias_desconto_antecipado'" for="numero_dias_desconto_antecipado" class="col-form-label">Quantidade de dias para desconto</label>
                      <input v-mask="'#########'" id="numero_dias_desconto_antecipado" :tokens="number" v-model="numero_dias_desconto_antecipado" type="text" class="form-control" placeholder="">
                    </div>

                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_percentual_desconto_antecipado'" for="percentual_desconto_antecipado" class="col-form-label">Desconto antecipação (%)</label>
                      <input v-mask="['#,##', '##,##', '###,##']" id="percentual_desconto_antecipado" :tokens="regexp" v-model="percentual_desconto_antecipado" type="text" class="form-control mt-auto" placeholder="">
                    </div>

                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_numero_dias_max_pagamento_apos_vencimento'" for="numero_dias_max_pagamento_apos_vencimento" class="col-form-label">Máx. dias para pagamento após vencimento</label>
                      <input v-mask="'#########'" id="numero_dias_max_pagamento_apos_vencimento" :tokens="number" v-model="numero_dias_max_pagamento_apos_vencimento" type="text" class="form-control" placeholder="">
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-md-5">
                      <label v-help-hint="'form-conta_texto_mora_diaria'" for="texto_mora_diaria" class="col-form-label">Texto que antecede o valor da mora diária</label>
                      <input id="texto_mora_diaria" v-model="texto_mora_diaria" type="text" class="form-control mt-auto" placeholder="" maxLength="255">
                    </div>

                    <div class="col-md-3">
                      <label v-help-hint="'form-conta_taxa_juro_dia'" for="taxa_juro_dia" class="col-form-label">Mora diária (%)</label>
                      <input v-mask="['#,##', '##,##', '###,##']" id="taxa_juro_dia" :tokens="regexp" v-model="taxa_juro_dia" type="text" class="form-control mt-auto" placeholder="">
                    </div>

                    <div class="col-md-2">
                      <label v-help-hint="'form-conta_primeira_instrucao'" for="primeira_instrucao" class="col-form-label">Primeira instrução</label>
                      <input id="primeira_instrucao" v-model="primeira_instrucao" type="text" class="form-control mt-auto" placeholder="" maxLength="2">
                    </div>

                    <div class="col-md-2">
                      <label for="segunda_instrucao" class="col-form-label">Segunda instrução</label>
                      <input id="segunda_instrucao" v-model="segunda_instrucao" type="text" class="form-control" placeholder="" maxLength="2">
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-md-5">
                      <label v-help-hint="'form-conta_texto_multa_atraso'" for="texto_multa_atraso" class="col-form-label">Texto que antecede a multa por atraso</label>
                      <input id="texto_multa_atraso" v-model="texto_multa_atraso" type="text" class="form-control mt-auto" placeholder="" maxLength="255">
                    </div>

                    <div class="col-md-3">
                      <label v-help-hint="'form-conta_taxa_multa'" for="taxa_multa" class="col-form-label">Multa (%)</label>
                      <input v-mask="['#,##', '##,##', '###,##']" id="taxa_multa" :tokens="regexp" v-model="taxa_multa" type="text" class="form-control mt-auto" placeholder="">
                    </div>

                    <div class="col-md-4">
                      <label v-help-hint="'form-conta_ultimo_nosso_numero'" for="ultimo_nosso_numero" class="col-form-label">Último nosso número</label>
                      <input id="ultimo_nosso_numero" v-model="ultimo_nosso_numero" type="text" class="form-control mt-auto" placeholder="" maxLength="255" disabled>
                    </div>
                  </div>

                  <div class="form-group row">
                    <div class="col-md-12">
                      <label v-help-hint="'form-conta_observacao_boleto'" for="observacao_boleto" class="col-form-label">Observações</label>
                      <textarea id="observacao_boleto" v-model="observacao_boleto" class="form-control full-textarea" placeholder="" rows="6" maxLength="5000"></textarea>
                      <span class="text-secondary">Limite de caracteres: {{ 5000 - observacao_boleto.length }}</span>
                    </div>
                  </div>

                </div>

              </div>
            </b-collapse>

          </div>

        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <b-btn :disabled="isEnviando" variant="verde" @click="salvar(false)">{{ isEnviando ? 'Salvando...': 'Salvar' }}</b-btn>
          <b-btn :disabled="isEnviando" type="submit" variant="verde">{{ isEnviando ? 'Salvando...': 'Salvar e sair' }}</b-btn>
          <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {

  name: 'FormularioConta',
  data () {
    return {
      loadCount: 0,
      isValid: true,
      isEdit: false,
      isEnviando: false,
      errorMsg: '',
      descricao: '',
      banco: '',
      numero_agencia: '',
      digito_agencia: '',
      conta_corrente: '',
      digito_conta_corrente: '',
      observacao: '',
      empresa_no_banco: '',
      banco_emite_boleto: false,
      primeira_instrucao: '',
      segunda_instrucao: '',
      texto_multa_atraso: '',
      texto_mora_diaria: '',
      // numero_dias_protesto: '',
      observacao_boleto: '',
      variacao_carteira: '',
      // numero_dias_devolucao: '',
      modalidade: '',
      numero_dias_floating: '',
      carteira: '',
      telefone_contato: '',
      valor_saldo: 0.00,
      taxa_multa: 0.00,
      taxa_juro_dia: 0.00,
      percentual_desconto_antecipado: 0.00,
      ultimo_nosso_numero: '',
      numero_sequencia_arquivo_cobranca: '',
      numero_dias_max_pagamento_apos_vencimento: '',
      numero_dias_desconto_antecipado: '',
      situacao: 'A',
      character: {
        'X': {
          pattern: /\w/
        }
      },
      number: {
        '#': {
          pattern: /\d/
        }
      },
      regexp: {
        '#': {
          pattern: /(\d{1,5})[.,]\d{1,2}/
        }
      },
      moeda: {
        decimal: ',',
        thousands: '.',
        precision: 2,
        masked: false
      }
    }
  },
  validations: {
    descricao: {required},
    banco: {required}
  },
  computed: {
    ...mapState('conta', ['lista', 'item', 'estaCarregando']),
    ...mapState('banco', ['lista']),
    ...mapState('root', ['franqueadaSelecionada'])
  },
  watch: {
    item (value) {
      this.descricao = value.descricao
      this.banco = value.banco ? value.banco.id : null
      this.numero_agencia = value.numero_agencia
      this.digito_agencia = value.digito_agencia
      this.conta_corrente = value.conta_corrente
      this.digito_conta_corrente = value.digito_conta_corrente
      this.observacao = value.observacao || ''
      this.empresa_no_banco = value.empresa_no_banco
      this.banco_emite_boleto = !!value.banco_emite_boleto
      this.primeira_instrucao = value.primeira_instrucao
      this.segunda_instrucao = value.segunda_instrucao
      // this.numero_dias_protesto = value.numero_dias_protesto
      this.numero_dias_max_pagamento_apos_vencimento = value.numero_dias_max_pagamento_apos_vencimento
      this.numero_dias_desconto_antecipado = value.numero_dias_desconto_antecipado
      this.observacao_boleto = value.observacao_boleto || ''
      this.variacao_carteira = value.variacao_carteira
      // this.numero_dias_devolucao = value.numero_dias_devolucao
      this.modalidade = value.modalidade
      this.numero_dias_floating = value.numero_dias_floating
      this.carteira = value.carteira
      this.telefone_contato = value.telefone_contato
      this.valor_saldo = value.valor_saldo
      this.taxa_multa = value.taxa_multa ? value.taxa_multa : null
      this.taxa_juro_dia = value.taxa_juro_dia ? value.taxa_juro_dia : null
      this.percentual_desconto_antecipado = value.percentual_desconto_antecipado ? value.percentual_desconto_antecipado : null
      this.ultimo_nosso_numero = value.ultimo_nosso_numero
      this.numero_sequencia_arquivo_cobranca = value.numero_sequencia_arquivo_cobranca
      this.texto_mora_diaria = value.texto_mora_diaria
      this.texto_multa_atraso = value.texto_multa_atraso
      this.franqueada = value.franqueada ? value.franqueada.id : null
    },

    descricao (value) {
      this.SET_DESCRICAO(value)
    },

    banco (value) {
      this.SET_BANCO(value)
    },

    numero_agencia (value) {
      this.SET_NUMERO_AGENCIA(value)
    },

    digito_agencia (value) {
      this.SET_DIGITO_AGENCIA(value)
    },

    conta_corrente (value) {
      this.SET_CONTA_CORRENTE(value)
    },

    digito_conta_corrente (value) {
      this.SET_DIGITO_CONTA_CORRENTE(value)
    },

    observacao (value) {
      this.SET_OBSERVACAO(value)
    },

    empresa_no_banco (value) {
      this.SET_EMPRESA_NO_BANCO(value)
    },

    banco_emite_boleto (value) {
      this.SET_BANCO_EMITE_BOLETO(value === true ? 1 : 0)
    },

    primeira_instrucao (value) {
      this.SET_PRIMEIRA_INSTRUCAO(value)
    },

    segunda_instrucao (value) {
      this.SET_SEGUNDA_INSTRUCAO(value)
    },

    // numero_dias_protesto (value) {
    //   this.SET_NUMERO_DIAS_PROTESTO(value)
    // },

    observacao_boleto (value) {
      this.SET_OBSERVACAO_BOLETO(value)
    },

    variacao_carteira (value) {
      this.SET_VARIACAO_CARTEIRA(value)
    },

    // numero_dias_devolucao (value) {
    //   this.SET_NUMERO_DIAS_DEVOLUCAO(value)
    // },

    modalidade (value) {
      this.SET_MODALIDADE(value)
    },

    numero_dias_floating (value) {
      this.SET_NUMERO_DIAS_FLOATING(value)
    },

    carteira (value) {
      this.SET_CARTEIRA(value)
    },

    telefone_contato (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.SET_TELEFONE_CONTATO(value)
    },

    valor_saldo (value) {
      if (value) {
        value = Number(value.toString().replace(/[.]/, '').replace(/[,]/, '.'))
      }
      this.SET_VALOR_SALDO(value)
    },

    taxa_multa (value) {
      if (value) {
        value = Number(value.toString().replace(/[.]/, '').replace(/[,]/, '.'))
      }
      this.SET_TAXA_MULTA(value)
    },

    taxa_juro_dia (value) {
      if (value) {
        value = Number(value.toString().replace(/[.]/, '').replace(/[,]/, '.'))
      }
      this.SET_TAXA_JURO_DIA(value)
    },

    percentual_desconto_antecipado (value) {
      if (value) {
        value = Number(value.toString().replace(/[.]/, '').replace(/[,]/, '.'))
      }
      this.SET_PERCENTUAL_DESCONTO_ANTECIPADO(value)
    },

    ultimo_nosso_numero (value) {
      this.SET_ULTIMO_NOSSO_NUMERO(value)
    },

    numero_sequencia_arquivo_cobranca (value) {
      this.SET_NUMERO_SEQUENCIA_ARQUIVO_COBRANCA(value)
    },

    texto_mora_diaria (value) {
      this.SET_TEXTO_MORA_DIARIA(value)
    },

    texto_multa_atraso (value) {
      this.SET_TEXTO_MULTA_ATRASO(value)
    },

    numero_dias_desconto_antecipado (value) {
      this.SET_NUMERO_DIAS_DESCONTO_ANTECIPADO(value)
    },

    numero_dias_max_pagamento_apos_vencimento (value) {
      this.SET_NUMERO_DIAS_MAX_PAGAMENTO_APOS_VENCIMENTO(value)
    }

  },
  created () {
    this.LIMPAR_ITEM()
    this.listar().then(this.countCarregamento)

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_SELECIONADO(id)
      this.getItem()
    } else {
      this.SET_BANCO_EMITE_BOLETO(0)
    }
  },
  methods: {
    ...mapActions('conta', ['getLista', 'getItem', 'criar', 'atualizar']),
    ...mapActions('banco', ['listar']),
    ...mapMutations('conta', ['SET_ITEM', 'LIMPAR_ITEM', 'SET_SELECIONADO', 'SET_DESCRICAO', 'SET_FRANQUEADA', 'SET_BANCO', 'SET_NUMERO_AGENCIA', 'SET_DIGITO_AGENCIA', 'SET_CONTA_CORRENTE', 'SET_DIGITO_CONTA_CORRENTE', 'SET_OBSERVACAO', 'SET_CONSIDERA_FLUXO_CAIXA', 'SET_EMPRESA_NO_BANCO', 'SET_BANCO_EMITE_BOLETO', 'SET_PRIMEIRA_INSTRUCAO', 'SET_SEGUNDA_INSTRUCAO', 'SET_OBSERVACAO_BOLETO', 'SET_VARIACAO_CARTEIRA', 'SET_MODALIDADE', 'SET_NUMERO_DIAS_FLOATING', 'SET_CARTEIRA', 'SET_TELEFONE_CONTATO', 'SET_VALOR_SALDO', 'SET_TAXA_MULTA', 'SET_TAXA_JURO_DIA', 'SET_PERCENTUAL_DESCONTO_ANTECIPADO', 'SET_ESTA_CARREGANDO', 'SET_ULTIMO_NOSSO_NUMERO', 'SET_NUMERO_SEQUENCIA_ARQUIVO_COBRANCA', 'SET_TEXTO_MORA_DIARIA', 'SET_TEXTO_MULTA_ATRASO', 'SET_NUMERO_DIAS_DESCONTO_ANTECIPADO', 'SET_NUMERO_DIAS_MAX_PAGAMENTO_APOS_VENCIMENTO']),
    ...mapMutations('franqueadas', ['setFranqueadaSelecionada']),
    // 'SET_NUMERO_DIAS_PROTESTO', 'SET_NUMERO_DIAS_DEVOLUCAO',

    voltar () {
      this.LIMPAR_ITEM()
      this.$router.push('/configuracoes/conta')
    },

    salvar (bSalvarESair) {
      this.isEnviando = true

      if (this.$v.$invalid) {
        this.isValid = false
        this.isEnviando = false
        return
      }

      if (this.isEdit) {
        this.atualizar()
          .then(() => {
            if (bSalvarESair === true) {
              this.voltar()
            } else {
              this.$router.go()
            }
          })
      } else {
        this.SET_FRANQUEADA(this.franqueadaSelecionada)
        this.criar()
          .then(() => {
            if (bSalvarESair === true) {
              this.voltar()
            } else {
              this.$router.go()
            }
          })
      }
    },

    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    }
  }
}
</script>

<style scoped>
/* #observacao,
#observacao_boleto {
  height: calc(100% - 60px);
  height: -webkit-calc(100% - 60px);
  height: -moz-calc(100% - 60px);
} */
.input-group-text {
  background-color: #e2e4e4;
}
</style>
