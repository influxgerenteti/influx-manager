<template>
  <div>
    <b-modal id="modalHorarioSala" ref="modalHorarioSala" v-model="visible" size="xl" title="Horários do Personal" no-close-on-backdrop hide-footer>
      <div v-if="estaCarregando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>

      <div class="content-sector-extra p-3 position-relative mt-3">
        <h5 class="title-module mb-2">
          Disponibilidade
          {{ ' - ' + itemSelecionado.descricao }}
        </h5>

        <div v-if="disponibilidades && disponibilidades.length > 0" class="row data-scroll">
          <perfect-scrollbar class="scroller col-12">
            <div v-for="(item, index) in disponibilidades" :key="index" class="body-card-list mx-2 mb-2">

              <b-row>
                <b-col md="2" data-header="Data">
                  <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Dia</label>
                  <select v-model="disponibilidades[index].dia_semana" class="custom-select form-control">
                    <option :value="null">Selecione</option>
                    <option value="DOM">Domingo</option>
                    <option value="SEG">Segunda</option>
                    <option value="TER">Terça</option>
                    <option value="QUA">Quarta</option>
                    <option value="QUI">Quinta</option>
                    <option value="SEX">Sexta</option>
                    <option value="SAB">Sábado</option>
                  </select>
                </b-col>
  
                <b-col md="2" data-header="Início">
                  <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Início {{ disponibilidades[index].dia_semana ? "*": '' }}</label>
                  <input v-mask="'##:##'" id="form-funcionario_horario_inicio" v-model="disponibilidades[index].hora_inicial" :required="disponibilidades[index].dia_semana" type="text" class="form-control" maxlength="5" placeholder="hh:mm" @keyup="horaAlterada(index)">
                  <div class="invalid-feedback">
                    <template v-if="disponibilidades[index].msgHorarioInvalido">
                      {{ disponibilidades[index].msgHorarioInvalido }}
                    </template>
                  </div>
                </b-col>
  
                <b-col md="2" data-header="Término">
                  <label v-help-hint="'form-funcionario_dia_disponibilidades'" for="dia_disponibilidades" class="col-form-label">Término {{ disponibilidades[index].dia_semana ? "*": '' }}</label>
                  <input v-mask="'##:##'" id="form-funcionario_horario_final" v-model="disponibilidades[index].hora_final" :required="disponibilidades[index].dia_semana" type="text" class="form-control" maxlength="5" placeholder="hh:mm" @keyup="horaAlterada(index)">
                  <div class="invalid-feedback">
                    <template v-if="disponibilidades[index].msgHorarioInvalido">
                      {{ disponibilidades[index].msgHorarioInvalido }}
                    </template>
                  </div>
                </b-col>
  
                <b-col md="4">
                  <b-btn variant="light" class="btn-40 mt-4" @click.prevent="excluir(index)">
                    <font-awesome-icon icon="minus" />
                  </b-btn>
  
                  <b-btn variant="azul" class="btn-40 mt-4" @click.prevent="listaAdd()">
                    <font-awesome-icon icon="plus" />
                  </b-btn>
                </b-col>
              </b-row>
              <b-row v-if="item.erro">
                <span class="text-danger">{{ item.erro }}</span>
              </b-row>

            </div>
          </perfect-scrollbar>
        </div>
        <div v-if="!disponibilidades || disponibilidades.length < 1">
          <h6>Nenhum registro de disponibilidade registrado.</h6>
          <b-btn variant="azul" class="btn-40 mt-4" @click.prevent="listaAdd()">
            <span>Adicionar </span>
            <font-awesome-icon icon="plus" />
          </b-btn>
        </div>
      </div>

      <hr>
      
      <b-row class="m-0 d-flex justify-content-between">
        <button @click="closeModal" class="btn btn-azul">
          Voltar
        </button>
        <button class="btn btn-verde" @click="formSubmit">
          Confirmar
        </button>
      </b-row>
    </b-modal>
  </div>
</template>
<script>

import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'modalHorarioSala',
  props: {
  },

  data () {
    return {
      visible: false,
      sala: {},
      form: {
        pristine: false
      }
    }
  },
  computed: {
    ...mapState('salaFranqueada', ['estaCarregando', 'itemSelecionado', 'disponibilidades']),
  },
  watch: {
    sala: {
      handler() {
        this.carregarHorarios()
      },
      deep: true
    }
  },
  mounted() {
  },

  methods: {
    ...mapActions('salaFranqueada', ['atualizar', 'listarDisponibilidade', 'atualizarDisponibilidade']),
    ...mapMutations('salaFranqueada', ['SET_ITEM_SELECIONADO']),

    carregarHorarios() {
      this.SET_ITEM_SELECIONADO(this.sala)
      this.listarDisponibilidade()
    },
    formSubmit() {
      if(!this.form.pristine) {
        return
      }
      this.atualizarDisponibilidade(this.disponibilidades).then(data => {
        this.listarDisponibilidade()
      })
    },
    closeModal() {
      this.visible = false
    },
    horaAlterada(index = null) {
      if(index) {
        delete this.disponibilidades[index].erro;
        if(this.disponibilidades[index].hora_inicial >= this.disponibilidades[index].hora_final){
          this.disponibilidades[index].erro = 'Favor verifique a ordem dos horários.'
        }
        this.disponibilidades[index].hora_inicial = this.checarHorario(this.disponibilidades[index].hora_inicial)
        this.disponibilidades[index].hora_final = this.checarHorario(this.disponibilidades[index].hora_final)
        this.$forceUpdate()
      }
      this.form.pristine = true;
    },
    checarHorario(horario) {
      if(horario.length < 5) {
        return horario
      }
      let horarioArr = horario.toString().split(':')
      horarioArr[0] = parseInt(horarioArr[0]) >= 24 ? '24' : (parseInt(horarioArr[0]) < 0 ? '00' : horarioArr[0].toString())
      horarioArr[1] = parseInt(horarioArr[1]) >= 30 ? '30' : parseInt(horarioArr[1]) >= 0 ? '00' : parseInt(horarioArr[1]) < 0 ? '00' : horarioArr[1].toString()
      return horarioArr[0].toString() + ':' + horarioArr[1].toString()
    },
    listaAdd () {
      this.horaAlterada()
      this.disponibilidades.push({id: null, dia_semana: null, hora_inicial: '', hora_final: ''})
      this.focar()
    },
    excluir (index) {
      this.horaAlterada()
      this.disponibilidades.splice(index, 1)
      this.focar()
    },
    focar () {
      setTimeout(() => {
        document.getElementsByTagName('select')[this.disponibilidades.length - 1].focus()
      }, 100)
    }
  }
}
</script>

<style scoped>
</style>
