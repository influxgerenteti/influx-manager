<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <label v-help-hint="'formulario_atividade_tipo_atividade'" for="formulario_atividade_tipo_atividade" class="col-form-label">Atividade *</label>
        <g-select
          id="formulario_atividade_tipo_atividade"
          :select="setTipoAtividade"
          :value="retornoObjetos.item"
          :options="listaDeItem"
          :required="true"
          :disabled="edit || readOnly"
          :class="$v.retornoObjetos.item.$invalid ? 'invalid-input' : 'valid-input'"
          class="multiselect-truncate"
          label="descricao"
          track-by="id"
        />
      </div>
      <div class="col-md-2">
        <label v-help-hint="'formulario_atividade_tipo_data'" for="formulario_atividade_tipo_data" class="col-form-label">Data *</label>
        <template>
          <template v-if="readOnly">
            <input id="formulario_atividade_tipo_data" v-model="retornoObjetos.data" :disabled="readOnly" type="text" class="form-control">
          </template>
          <template v-else>
            <g-datepicker
              :element-id="'formulario_atividade_tipo_data'"
              :value="retornoObjetos.data"
              :required="true"
              :selected="setDataObjetoRetorno"
            />
          </template>
        </template>
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-md-6">
            <label v-help-hint="'formulario_atividade_tipo_hora_inicio'" for="formulario_atividade_horario_inicio" class="col-form-label">Início *</label>
            <input v-mask="'##:##'" id="formulario_atividade_horario_inicio" v-model="retornoObjetos.horario_de_inicio" :class="!$v.retornoObjetos.horario_de_inicio.validateHour || !$v.retornoObjetos.horario_de_termino.comparaHora ? 'is-invalid' : null" :disabled="readOnly" type="text" class="form-control" maxlength="5" required @change="setHorarioInicioObjeto">
            <div v-if="$v.retornoObjetos.horario_de_termino.comparaHora" class="invalid-feedback">
              {{ (!$v.retornoObjetos.horario_de_inicio.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
            </div>
          </div>
          <div class="col-md-6">
            <label v-help-hint="'formulario_atividade_tipo_hora_termino'" for="formulario_atividade_tipo_hora_termino" class="col-form-label">Término *</label>
            <input v-mask="'##:##'" id="formulario_atividade_tipo_hora_termino" v-model="retornoObjetos.horario_de_termino" :class="!$v.retornoObjetos.horario_de_termino.validateHour || !$v.retornoObjetos.horario_de_termino.comparaHora ? 'is-invalid' : null" :disabled="readOnly" type="text" class="form-control" maxlength="5" required @change="setHorarioTerminoObjeto">
            <div v-if="$v.retornoObjetos.horario_de_termino.comparaHora" class="invalid-feedback">
              {{ (!$v.retornoObjetos.horario_de_termino.validateHour) ? 'Horário inválido' : 'Campo obrigatório' }}
            </div>
          </div>
        </div>
        <div v-if="!$v.retornoObjetos.horario_de_termino.comparaHora" class="input-invalid">
          Horário de término deve ser maior que horário de início.
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label v-help-hint="'formulario_atividade_sala'" for="formulario_atividade_sala" class="col-form-label">Sala *</label>
        <g-select
          id="formulario_atividade_sala"
          :select="setSala"
          :value="retornoObjetos.sala"
          :options="listaDeSala"
          :required="true"
          :disabled="readOnly"
          :class="$v.retornoObjetos.sala.$invalid ? 'invalid-input' : 'valid-input'"
          class="multiselect-truncate"
          label="descricao"
          track-by="id"
        />
      </div>
      <div v-if="formulario === 'atividade_extra'" class="col-md-3">
        <label v-help-hint="'formulario_atividade_max_aluno'" for="formulario_atividade_max_aluno" class="col-form-label">Máximo de Alunos </label>
        <input v-mask="'#####'" id="formulario_atividade_max_aluno" :disabled="readOnly" v-model="retornoObjetos.max_aluno" type="text" class="form-control" maxlength="9999999" @input="verificarLista">
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label v-help-hint="'formulario_atividade_usuario'" for="formulario_atividade_usuario" class="col-form-label">{{ formulario === 'atividade_extra' ? 'Usuário *' : 'Responsável pela criação do nivelamento' }}</label>
        <input id="formulario_atividade_usuario" :value="retornoObjetos.usuario" disabled type="text" class="form-control" required>
      </div>

      <template>
        <div v-if="formulario === 'atividade_extra'" class="col-md-6">
          <label v-help-hint="'formulario_atividade_responsavel'" for="formulario_atividade_responsavel" class="col-form-label">Responsável pela execução *</label>
          <g-select
            id="formulario_atividade_responsavel"
            :multi-tag="true"
            :value="retornoObjetos.responsavel"
            :select="setResponsavelPelaExecucao"
            :options="listaDeFuncionario"
            :class="$v.retornoObjetos.responsavel.$invalid ? 'invalid-input' : 'valid-input'"
            :disabled="readOnly"
            :required="true"
            label="apelido"
            track-by="id" />
        </div>
        <div v-else class="col-md-6">
          <label v-help-hint="'formulario_atividade_responsavel'" for="formulario_atividade_responsavel" class="col-form-label">Responsável pela execução *</label>
          <g-select
            id="formulario_atividade_responsavel"
            :value="retornoObjetos.responsavel"
            :select="setResponsavel"
            :options="listaDeFuncionario"
            :disabled="readOnly"
            :class="$v.retornoObjetos.responsavel.$invalid ? 'invalid-input' : 'valid-input'"
            :required="true"
            label="apelido"
            track-by="id" />
        <!-- <div v-if="$v.retornoObjetos.responsavel.$invalid" class="multiselect-invalid">
            Selecione uma opção!
          </div> -->
        </div>
      </template>
    </div>

    <div>
      <label v-help-hint="'formulario_atividade_responsavel'" for="formulario_atividade_responsavel" class="col-form-label">Descrição da atividade </label>
      <b-form-textarea
        id="formulario_atividade_descricao "
        v-model="retornoObjetos.descricao"
        :disabled="readOnly"
        class="full-textarea"
        rows="2"
      />
    </div>
  </div>
</template>

<script>
import {mapState} from 'vuex'
import { required } from 'vuelidate/lib/validators'
import { validateHour } from '../../utils/validators'
const comparaHora = (value, vm) => {
  if (vm.horario_de_termino !== '' && vm.horario_de_inicio !== '') {
    return vm.horario_de_inicio < vm.horario_de_termino
  }

  return true
}

export default {
  name: 'FormularioAtividade',
  props: {
    formulario: {
      type: [String],
      required: true,
      default: ''
    },
    retornoObjetos: {
      type: [Object],
      required: false,
      default: null
    },
    edit: {
      type: [Boolean],
      required: true,
      default: false
    },
    readOnly: {
      type: [Boolean],
      required: false,
      default: false
    }
  },
  data () {
    return {
      isValid: true,
      tipo_atividade: null,
      sala: null,
      usuario: null,
      responsavel: null,
      data: null,
      horario_de_inicio: null,
      horario_de_termino: null,
      max_alunos: null,
      descricao_atividade: null
    }
  },
  computed: {
    ...mapState('cadastroServico', {listaDeItemRequisicao: 'lista'}),
    ...mapState('funcionario', {listaDeFuncionarioRequisicao: 'lista'}),
    ...mapState('sala', {listaDeSalaRequisicao: 'lista'}),
    ...mapState('root', {usuarioLogado: 'usuarioLogado'}),

    listaDeItem: {
      get () {
        if (this.formulario === 'atividade_extra') {
          return [{id: null, descricao: 'Selecione'}, ...this.listaDeItemRequisicao.filter(item => item.tipo_item.tipo === 'AE')]
        }
        if (this.formulario === 'nivelamento') {
          return [{id: null, descricao: 'Selecione'}, ...this.listaDeItemRequisicao.filter(item => item.tipo_item.tipo === 'SN')]
        }
      }
    },

    listaDeFuncionario: {
      get () {
        return this.listaDeFuncionarioRequisicao
      }
    },

    listaDeSala: {
      get () {
        return [{id: null, descricao: 'Selecione'}, ...this.listaDeSalaRequisicao]
      }
    }
  },
  mounted () {
    this.listarCamposDosSelects()
  },
  validations: {
    retornoObjetos: {
      item: {required},
      data: {required},
      horario_de_inicio: {validateHour},
      horario_de_termino: {validateHour, comparaHora},
      sala: {required},
      max_aluno: {required},
      responsavel: {required}
    }
  },
  methods: {
    listarCamposDosSelects () {
      this.$store.commit('sala/SET_PAGINA_ATUAL', 1)
      this.$store.commit('sala/SET_LISTA', [])
      this.$store.dispatch('sala/listar', { sala_franqueada: true, apenas_sala_ativa: true })

      if (this.formulario === 'nivelamento') {
        this.$store.commit('cadastroServico/SET_PAGINA_ATUAL', 1)
        this.$store.commit('cadastroServico/SET_LISTA', [])
        this.$store.dispatch('cadastroServico/listar')
          .then((lista) => {
            this.setTipoAtividade(this.listaDeItem.find((item) => item.tipo_item.tipo === 'SN'))
          })
      }
    },

    setTipoAtividade (value) {
      this.retornoObjetos.item = value.id == null ? null : value
      if (this.formulario === 'atividade_extra') {
        const valorInvalido = value.id == null || value.itemFranqueadas[0].id == null || value.itemFranqueadas[0].valor_venda == null
        this.retornoObjetos.valor = valorInvalido ? 0 : value.itemFranqueadas[0].valor_venda * 1
      }
    },

    setResponsavelPelaExecucao (value) {
      const index = this.retornoObjetos.responsavel.indexOf(value)
      if (index === -1) {
        this.retornoObjetos.responsavel.push(value)
        return
      }

      this.retornoObjetos.responsavel.splice(index, 1)
    },

    setResponsavel (value) {
      this.retornoObjetos.responsavel = value.id == null ? null : value
    },

    setSala (value) {
      this.retornoObjetos.sala = value.id == null ? null : value
    },

    setDataObjetoRetorno (value) {
      this.retornoObjetos.data = value
    },

    setHorarioInicioObjeto () {
      // this.retornoObjetos.horario_de_inicio = this.horario_de_inicio
    },

    setHorarioTerminoObjeto () {
      // this.retornoObjetos.horario_de_termino = this.horario_de_termino
    },

    verificarLista () {
      this.$emit('verificarLista')
    }
  }
}
</script>
