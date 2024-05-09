<template>
  <div class="content-sector sector-secondary p-3">
    <h5 class="title-module mb-2">Endereço</h5>
    <template v-if="permiteUsarEnderecoAluno">
      <div class="form-group row">
        <div class="col-md-12">
          <b-form-checkbox v-model="bloquearUtilizandoAluno" @change="setUsarEnderecoAluno">
            Utilizar endereço do aluno?
            (não será permitido a edição do endereço, caso checado)
          </b-form-checkbox>
        </div>
      </div>
    </template>
    <a href="http://www.buscacep.correios.com.br/sistemas/buscacep/buscaCepEndereco.cfm" target="_blank">Consultar CEP nos correios</a>
    <div class="form-group row">
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_cep_endereco'" for="cep_endereco" class="col-form-label">CEP</label>
        <input v-mask="'#####-###'" id="cep_endereco" :disabled="bloquearUtilizandoAluno" v-model="cep_endereco" type="text" class="form-control" @blur="validaCep()">
      </div>
      <div class="col-md-6">
        <label v-help-hint="'form-endereco_endereco'" for="endereco" class="col-form-label">{{ labelEndereco }}</label>
        <input id="endereco" :disabled="bloquearUtilizandoAluno" :class="campoInvalido('endereco') ? 'invalid-input' : 'valid-input'" v-model="cepData.endereco" type="text" class="form-control" maxlength="80" @change="exportEndereco">
      </div>
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_numero_endereco'" for="numero_endereco" class="col-form-label">Número</label>
        <input v-mask="'#########'" id="numero_endereco" :disabled="bloquearUtilizandoAluno" v-model="cepData.numero_endereco" type="text" class="form-control" maxlength="9" @change="exportNumeroEndereco">
      </div>
    </div>

    <div class="form-group row">
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_complemento_endereco'" for="complemento_endereco" class="col-form-label">Complemento</label>
        <input id="complemento_endereco" :disabled="bloquearUtilizandoAluno" v-model="cepData.complemento_endereco" type="text" class="form-control" maxlength="255" @change="exportComplementoEndereco">
      </div>
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_bairro_endereco'" for="bairro_endereco" class="col-form-label">Bairro</label>
        <input id="bairro_endereco" :disabled="bloquearUtilizandoAluno" v-model="cepData.bairro_endereco" type="text" class="form-control" maxlength="50" @change="exportBairroEndereco">
      </div>
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_estado'" for="estado" class="col-form-label">Estado</label>
        <g-select
          id="estado"
          :value="cepData.estado"
          :select="setEstadoId"
          :options="listaEstados"
          :disabled="bloquearUtilizandoAluno"
          label="nome"
          track-by="id" />
      </div>
      <div class="col-md-3">
        <label v-help-hint="'form-endereco_cidade'" for="cidade" class="col-form-label">Cidade</label>
        <g-select
          id="cidade"
          :value="cepData.cidade"
          :select="setCidadeId"
          :options="listaCidades"
          :disabled="listaCidades.length === 0 || bloquearUtilizandoAluno"
          label="nome"
          track-by="id" />
      </div>
    </div>
  </div>
</template>
<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../utils/event-bus'

export default {
  name: 'GFormEndereco',
  props: {
    cepData: {
      type: Object,
      required: true
    },
    camposObrigatorios: {
      type: Object,
      required: false,
      default: function () {
        return {}
      }
    },
    camposInvalidos: {
      type: Object,
      required: false,
      default: function () {
        return {}
      }
    },
    callbackCepData: {
      type: Function,
      required: true
    },
    permiteUsarEnderecoAluno: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      cep_endereco: '',
      bloquearUtilizandoAluno: false
    }
  },

  computed: {
    ...mapState('cep', ['objCep', 'numero_cep']),
    ...mapState('estado', {listaEstados: 'lista'}),
    ...mapState('cidade', {listaCidades: 'lista'}),
    labelEndereco () {
      let label = 'Endereço'
      if (this.camposObrigatorios.endereco && this.camposObrigatorios.endereco === true) {
        label += ' *'
      }
      return label
    }
  },

  watch: {
    cepData (value) {
      this.cep_endereco = value.cep_endereco
      if (value.estado !== undefined) {
        if (value.cidade !== undefined) {
          this.cepData.cidade = value.cidade.id
        }
        this.setEstadoId(value.estado)
      }
    },

    cep_endereco (value) {
      if (value) {
        value = value.replace(/\D/g, '')
      }
      this.SET_CEP_NUMERO(value)
      this.cepData.cep_endereco = value
    },

    listaCidades (value) {
      if (this.listaCidades.length > 0) {
        if (typeof this.cidade === 'number') {
          this.cepData.cidade = this.listaCidades.filter((item) => item.id === this.cepData.cidade)[0]
        }
      }
    }
  },

  mounted () {
    this.listaEstadosRequisicao()
  },

  methods: {
    // Actions
    ...mapActions('cep', {buscarCep: 'buscar'}),
    ...mapActions('estado', {listaEstadosRequisicao: 'listar'}),
    ...mapActions('cidade', {listaCidadesRequisicao: 'listar'}),
    // Mutations
    ...mapMutations('cep', ['SET_CEP_SELECIONADO', 'SET_CEP_NUMERO']),
    ...mapMutations('estado', ['SET_ESTADO_SELECIONADO']),
    ...mapMutations('cidade', ['SET_ESTADO_FILTRO_ID']),
    // Methods do componente
    validaCep () {
      if (this.cep_endereco.length === 9) {
        this.callbackCepData(this.cepData)
        this.SET_CEP_NUMERO(this.cepData.cep_endereco)
        this.buscarCep()
          .then((dados) => {
            if (dados.error) {
              EventBus.$emit('criarAlerta', {
                tipo: 'A',
                mensagem: 'CEP não encontrado.'
              })
            } else {
              this.cepData.endereco = dados.rua
              this.cepData.numero_endereco = dados.numero
              this.cepData.bairro_endereco = dados.bairro
              this.cepData.estado = this.listaEstados.filter((item) => item.id === dados.estado)[0]
              this.cepData.cidade = dados.cidade
              this.setEstadoId(this.cepData.estado)
            }
          })
      }
    },

    campoInvalido (campo) {
      return this.camposInvalidos[campo] && this.camposInvalidos[campo] === true
    },

    setUsarEnderecoAluno (value) {
      this.$emit('setEnderecoResponsavel', value)
    },

    setEstadoId (estado) {
      if (!estado) {
        return
      }
      this.cepData.estado = Object.assign({}, estado)
      this.callbackCepData(this.cepData)

      let cidadeId = null
      if (typeof this.cepData.cidade === 'number') {
        cidadeId = this.cepData.cidade
      }

      this.cepData.cidade = null
      this.SET_ESTADO_FILTRO_ID(estado.id)
      this.listaCidadesRequisicao()
        .then(() => {
          const cidadeFilter = this.listaCidades.filter((item) => item.id === cidadeId)[0]
          const cidade = Object.assign({}, cidadeFilter)
          this.cepData.cidade = cidade
          this.setCidadeId(cidade)
        })
    },

    setCidadeId (cidade) {
      this.cepData.cidade = cidade
      this.callbackCepData(this.cepData)
    },

    // Verificar possibilidade de refatoracao para não utilizar o EventBus
    exportCepEndereco () {
      this.callbackCepData(this.cepData)
    },
    exportEndereco () {
      this.callbackCepData(this.cepData)
    },
    exportNumeroEndereco () {
      this.callbackCepData(this.cepData)
    },
    exportComplementoEndereco () {
      this.callbackCepData(this.cepData)
    },
    exportBairroEndereco () {
      this.callbackCepData(this.cepData)
    },
    exportEstadoSelecionado () {
      this.callbackCepData(this.cepData)
    },
    exportCidadeSelecionada () {
      this.callbackCepData(this.cepData)
    }
  }
}
</script>
