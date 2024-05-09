<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="animated fadeIn mb-2">
        <div class="form-group row">
          <div class="col-md-6">
            <label v-help-hint="'form-horario_descricao'" for="descricao" class="col-form-label">Descrição</label>
            <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="50" readonly="readonly">
            <div class="invalid-feedback descricao">Preencha a descrição!</div>
          </div>
        </div>

        <div class="table-responsive-sm mb-3">
          <div class="content-sector-extra p-2 box-scroll">
            <h5 class="title-module mb-2 px-2">Horários *</h5>

            <b-row class="header-card-list mx-2 mb-0">
              <b-col md="4">
                <label class="col-form-label">Dia da semana *</label>
              </b-col>
              <b-col md="4">
                <label class="col-form-label">Início</label>
              </b-col>
              <b-col md="4"/>
            </b-row>

            <div class="row data-scroll">
              <perfect-scrollbar class="scroller col-12">
                <b-row v-for="(item, index) in lista" :key="index" class="body-card-list mx-2">
                  <b-col md="4" data-header="Dia da semana *">
                    <select v-model="item.dia_semana" class="custom-select form-control" required @change="alterarNome()">
                      <option value>Selecione</option>
                      <option value="DOM">Domingo</option>
                      <option value="SEG">Segunda</option>
                      <option value="TER">Terça</option>
                      <option value="QUA">Quarta</option>
                      <option value="QUI">Quinta</option>
                      <option value="SEX">Sexta</option>
                      <option value="SAB">Sábado</option>
                    </select>
                    <div class="invalid-feedback">Selecione o dia da semana!</div>
                  </b-col>

                  <b-col md="4" data-header="Início">
                    <input v-mask="'##:##'" v-model="item.horario_inicio" type="text" placeholder="HH:MM" class="form-control" maxlength="5" required @change="alterarNome()">
                    <div class="invalid-feedback">Informe o horário!</div>
                  </b-col>
                  <b-col md="4">

                    <b-btn v-if="lista.length > 1 && !item.id" variant="light" class="btn-40" @click.prevent="excluir(index)">
                      <font-awesome-icon icon="minus" />
                    </b-btn>

                    <b-btn v-if="index === (lista.length - 1)" variant="azul" class="btn-40" @click.prevent="listaAdd()">
                      <font-awesome-icon icon="plus" />
                    </b-btn>

                  </b-col>
                </b-row>
              </perfect-scrollbar>
            </div>
          </div>
        </div>

      </div>

      <div class="form-group pt-2">
        <b-btn :disabled="isSalvando" type="submit" variant="verde">{{ isSalvando ? 'Salvando...' : 'Salvar' }}</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import {converteHorarioParaBanco, converteHorarioBancoParaInputText} from '../../utils/date'

export default {
  props: {
    isModal: {
      type: Boolean,
      default: false,
      required: false
    }
  },

  data () {
    return {
      isValid: true,
      isEdit: false,
      isSalvando: false,
      lista: [],
      diasOrdem: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
      listSize: 0
    }
  },

  computed: {
    ...mapState('horario', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        this.SET_DESCRICAO(value)
      }
    }
  },

  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id && this.$route.name === 'atualizar-horario') {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar().then(() => {
        this.lista = this.itemSelecionado.horarioAulas.map((time) => {
          time.horario_inicio = converteHorarioBancoParaInputText(time.horario_inicio)
          return {
            id: time.id,
            dia_semana: time.dia_semana,
            horario_inicio: time.horario_inicio
          }
        })
      })
    } else {
      this.lista.push({
        dia_semana: '',
        horario_inicio: ''
      })
    }
  },

  validations: {
    descricao: {required},
    item: {
      dia_semana: {required},
      horario_inicio: {required}
    }
  },

  methods: {
    ...mapMutations('horario', ['SET_DESCRICAO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),
    ...mapActions('horario', ['criar', 'atualizarHorarioAula', 'buscar', 'excluir']),

    voltar (id) {
      this.isSalvando = false
      this.LIMPAR_ITEM_SELECIONADO()
      if (this.isModal === true) {
        this.$emit('resolve', id)
      } else {
        this.$router.push('/configuracoes/horario')
      }
    },

    arrumarNome (string) {
      string = string.toLowerCase().split('')
      string[0] = string[0].charAt(0).toUpperCase()
      return string.join('')
    },

    alterarNome () {
      let nomeLista = []

      this.lista.map(item => {
        if (item.dia_semana !== '' && item.horario_inicio !== '') {
          if (nomeLista.length < 1) {
            const hora = item.horario_inicio
            nomeLista.push({ dia: [this.arrumarNome(item.dia_semana)], hora: hora })
          } else {
            for (let i = 0; i < nomeLista.length; i++) {
              if (nomeLista[i].hora === item.horario_inicio) {
                nomeLista[i].dia.push(this.arrumarNome(item.dia_semana))

                nomeLista[i].dia.sort((a, b) => {
                  return this.diasOrdem.indexOf(a) - this.diasOrdem.indexOf(b)
                })

                break
              } else if (i + 1 === nomeLista.length) {
                nomeLista.push({ dia: [this.arrumarNome(item.dia_semana)], hora: item.horario_inicio })
                break
              }
            }
          }
        }
      })

      let nome = ''

      nomeLista.map(item => {
        if (item.hora !== '') {
          const regex = new RegExp(/^\d+/g)
          item.hora = item.hora + '/' + item.hora.replace(regex, Number(item.hora.match(regex)[0]) + 1)
          nome += item.dia.join('&') + ' (' + item.hora + ') '
        }
      })

      this.descricao = nome.trim()
    },

    listaAdd () {
      this.lista.push({dia_semana: '', horario_inicio: ''})
      setTimeout(() => {
        document.getElementsByTagName('select')[this.lista.length - 1].focus()
      }, 100)
    },

    limpar (item) {
      item.dia_semana = ''
      item.horario_inicio = ''

      this.alterarNome()
    },

    excluir (index) {
      this.lista.splice(index, 1)
      this.alterarNome()
    },

    salvar () {
      this.isSalvando = true
      let listaAlterada = [...this.lista]
      const isValid = listaAlterada.filter(item => item.dia_semana === '' || item.horario_inicio === '')

      if (this.$v.descricao.$invalid || isValid.length > 0) {
        this.isValid = false
        this.isSalvando = false
        return
      }

      if (listaAlterada.length > 0) {
        listaAlterada = listaAlterada.map((item) => {
          let objClone = Object.assign({}, item)
          objClone.horario_inicio = converteHorarioParaBanco(objClone.horario_inicio)
          return objClone
        })
      }

      this.itemSelecionado.horarios_aula = listaAlterada

      if (this.itemSelecionadoID) {
        this.atualizarHorarioAula().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(() => {
          this.isSalvando = false
        })
      }
    }
  }
}
</script>
<style scoped>
.main .container-fluid .animated .table-responsive-sm, .main .container-fluid form .table-responsive-sm {
  min-height: 270px !important;
}

.number {
  max-width: 100px;
}

.options-licao div {
  width: 1.25em;
  margin: auto;
}

.invalid-feedback:not(.descricao) {
  position: relative;
}

@media (max-width: 768px) {
  .number {
    max-width: 100%;
  }
  .table.mobile-cards td:not(:last-child) > div {
    max-width: 60%;
    padding-right: 0;
  }
  .options-licao div {
    width: auto;
  }
  .table.mobile-cards tr:hover td {
    border-color: #EBECF0;
  }
}

.content-sector {
  height: calc(100vh - 295px);
  height: -webkit-calc(100vh - 295px);
  height: -moz-calc(100vh - 295px);
}
.table-scroll {
  height: calc(100vh - 375px);
  height: -webkit-calc(100vh - 375px);
  height: -moz-calc(100vh - 375px);
  margin-bottom: 0;
}
</style>
