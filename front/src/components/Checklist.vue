<template>
  <b-modal id="checklist" v-model="checklistModal" size="lg" centered no-close-on-backdrop hide-header hide-footer>
    <!-- USAR v-b-modal.checklist PARA CHAMAR MODAL -->
    <div class="d-flex justify-content-between mb-3">
      <h5 class="title-module">Lista de atividades</h5>

      <b-form-checkbox v-model="isVisible" @input="toggleCheck()">Ocultar itens concluídos</b-form-checkbox>
    </div>

    <div class="table-responsive-sm">
      <div class="checklist box-scroll">
        <div class="row data-scroll mb-3">
          <perfect-scrollbar class="scroller col-12">
            <div v-if="true" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!checklist.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <b-row v-for="(item, index) in checklist" :key="index">

              <h6 class="title-module mb-2 px-2">{{ item.descricao }}</h6>

              <b-col v-if="item.atividades['D'].length" md="12">
                <div class="d-flex flex-column"><span>Atividades diárias</span></div>

                <b-progress :max="100" class="mb-3" variant="azul" show-progress>
                  <b-progress-bar :item-val="item.value = ((100/item.atividades['D'].length) * (item.atividades['D'].filter(check => { return check.checked === true })).length)" :value="item.value" :label="`${(item.value).toFixed(0)}%`"/>
                </b-progress>

                <b-row v-for="(atividade, aIndex) in item.atividades['D']" :key="aIndex">
                  <b-col md="12">
                    <b-form-checkbox :id="`D-${aIndex}-${item.id}`" :ref="`D-${aIndex}-${item.id}`" :checked="atividade.checked" @change="setCheck(atividade, item.id, `D-${aIndex}-${item.id}`)">{{ atividade.descricao }}</b-form-checkbox>
                  </b-col>
                </b-row>
              </b-col>

              <b-col v-if="item.atividades['S'].length" md="12">
                <div class="d-flex flex-column"><span>Atividades semanais</span></div>

                <b-progress :max="100" class="mb-3" variant="azul" show-progress>
                  <b-progress-bar :item-val="item.value = ((100/item.atividades['S'].length) * (item.atividades['S'].filter(check => { return check.checked === true })).length)" :value="item.value" :label="`${(item.value).toFixed(0)}%`"/>
                </b-progress>

                <b-row v-for="(atividade, aIndex) in item.atividades['S']" :key="aIndex">
                  <b-col md="12">
                    <b-form-checkbox :id="`S-${aIndex}-${item.id}`" :ref="`S-${aIndex}-${item.id}`" :checked="atividade.checked" @change="setCheck(atividade, item.id, `S-${aIndex}-${item.id}`)">{{ atividade.descricao }}</b-form-checkbox>
                  </b-col>
                </b-row>
              </b-col>

              <b-col v-if="item.atividades['M'].length" md="12">
                <div class="d-flex flex-column"><span>Atividades mensais</span></div>

                <b-progress :max="100" class="mb-3" variant="azul" show-progress>
                  <b-progress-bar :item-val="item.value = ((100/item.atividades['M'].length) * (item.atividades['M'].filter(check => { return check.checked === true })).length)" :value="item.value" :label="`${(item.value).toFixed(0)}%`"/>
                </b-progress>

                <b-row v-for="(atividade, aIndex) in item.atividades['M']" :key="aIndex">
                  <b-col md="12">
                    <b-form-checkbox :id="`M-${aIndex}-${item.id}`" :ref="`M-${aIndex}-${item.id}`" :checked="atividade.checked" @change="setCheck(atividade, item.id, `M-${aIndex}-${item.id}`)">{{ atividade.descricao }}</b-form-checkbox>
                  </b-col>
                </b-row>
              </b-col>

              <b-col v-if="item.atividades['A'].length" md="12">
                <div class="d-flex flex-column"><span>Atividades atemporais</span></div>

                <b-progress :max="100" class="mb-3" variant="azul" show-progress>
                  <b-progress-bar :item-val="item.value = ((100/item.atividades['A'].length) * (item.atividades['A'].filter(check => { return check.checked === true })).length)" :value="item.value" :label="`${(item.value).toFixed(0)}%`"/>
                </b-progress>

                <b-row v-for="(atividade, aIndex) in item.atividades['A']" :key="aIndex">
                  <b-col md="12">
                    <b-form-checkbox :id="`A-${aIndex}-${item.id}`" :ref="`A-${aIndex}-${item.id}`" :checked="atividade.checked" @change="setCheck(atividade, item.id, `A-${aIndex}-${item.id}`)">{{ atividade.descricao }}</b-form-checkbox>
                  </b-col>
                </b-row>
              </b-col>

            </b-row>
          </perfect-scrollbar>
        </div>

        <b-btn type="button" variant="link" @click="checklistModal = false">Fechar</b-btn>
      </div>
    </div>
  </b-modal>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'Checklist',
  data () {
    return {
      checklistModal: false,
      isVisible: false,
      checklist: []
    }
  },

  computed: {
    ...mapState('root', ['usuarioLogado']),
    ...mapState('checklist', ['lista']),
    ...mapState('checklistAtividadeRealizada', ['estaCarregando'])
  },

  mounted () {
    this.SET_ESTA_CARREGANDO(true)
    this.SET_USUARIO_ID(this.usuarioLogado.id)

    this.buscarPorUsuarioLogado().then(lista => {
      lista.map(item => {
        let checkItem = {}
        checkItem.descricao = item.descricao
        checkItem.id = item.id

        let atividades = {'D': [], 'S': [], 'M': [], 'A': []}
        let checadas = {'D': [], 'S': [], 'M': [], 'A': []}

        item.checklist_atividade.map(atividade => {
          checadas[atividade.tipo_atividade].push(atividade.id)
          atividade.checkedId = null
          atividade.checked = false
          atividades[atividade.tipo_atividade].push(atividade)
        })

        checkItem.atividades = atividades

        this.SET_ATIVIDADES_DIARIAS(checadas['D'])
        this.SET_ATIVIDADES_SEMANAIS(checadas['S'])
        this.SET_ATIVIDADES_MENSAIS(checadas['M'])
        this.SET_ATIVIDADES_ATEMPORAIS(checadas['A'])

        this.buscarAtividadeRealizada().then(realizadas => {
          realizadas.map(check => {
            checkItem.atividades[check.checklist_atividade.tipo_atividade].map(atividade => {
              if ((check.checklist_atividade.id === atividade.id) && (check.checklist.id === checkItem.id)) {
                atividade.checkedId = check.id
                atividade.checked = true
              }
            })
          })
        })

        this.checklist.push(checkItem)
      })
    })
  },

  methods: {
    ...mapActions('checklist', ['buscarPorUsuarioLogado']),
    ...mapActions('checklistAtividadeRealizada', ['buscarAtividadeRealizada', 'checarAtividade', 'removerChecado']),
    ...mapMutations('checklistAtividadeRealizada', ['SET_USUARIO_ID', 'SET_CHECKED_ID', 'SET_CHECKLIST_ID', 'SET_CHECKLIST_ATIVIDADE', 'RESET_LISTAS_ATIVIDADES', 'SET_ATIVIDADES_DIARIAS', 'SET_ATIVIDADES_SEMANAIS', 'SET_ATIVIDADES_MENSAIS', 'SET_ATIVIDADES_ATEMPORAIS', 'SET_ESTA_CARREGANDO']),

    setCheck (item, checklistId, ref) {
      if (this.isVisible) {
        this.$refs[ref][0].$el.classList.toggle('hide-check')
      }

      if (item.checkedId) {
        this.SET_CHECKED_ID(item.checkedId)
        this.removerChecado().then(id => {
          item.checkedId = null
          item.checked = false
        })
        return
      }

      this.SET_CHECKLIST_ID(checklistId)

      this.SET_CHECKLIST_ATIVIDADE(item.id)
      this.checarAtividade().then(id => {
        item.checkedId = id
        item.checked = true
      })
    },

    toggleCheck () {
      const lista = Array.from(document.querySelectorAll('.checklist input[type=checkbox]:checked'))

      lista.map(item => {
        item.parentElement.classList.toggle('hide-check')
      })
    }
  }
}
</script>
<style scoped>
.main .container-fluid .animated #checlist .table-responsive-sm, .main .container-fluid form .table-responsive-sm {
  height: calc(100vh - 150px);
  height: -webkit-calc(100vh - 150px);
  height: -moz-calc(100vh - 150px);
}

.scroller > .row:not(:last-child) {
  margin-bottom: 1rem;
}
.scroller > .row div span {
  margin: 0 auto;
  color: #717171;
}

.checklist h6 {
  color: #444444;
}

.progress {
  border-radius: 25px;
}
.bg-azul {
  background-color: #4a69c5 !important;
}
.hide-check {
  display: none;
}
.form-loading {
  background-color: rgba(255, 255, 255, 0.5);
}
</style>
