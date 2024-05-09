<template>
  <div class="animated fadeIn">

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="sal.descricao">Descrição</th>
            <th data-column="slf.lotacao_maxima">Lotação Máxima</th>
            <th data-column="slf.personal">Personal</th>
            <th data-column="slf.situacao">Situação</th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="(item, index) in lista" :key="index">
              <td data-label="Descrição">{{ item.descricao }}</td>

              <td data-label="Lotação Máxima">
                <div class="col-md-8 p-0">
                  <input v-if = "item.situacao === 'A'" v-mask="'#########'" :disabled="!permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao" :id="`lotacao_maxima_${index}`" v-model="item.lotacao_maxima" type="text" class="form-control pt-0 pb-0" maxlength="9" @focus="item.oldVal = item.lotacao_maxima" @blur="checkValue(item)">
                  <input v-else v-mask="'#########'" :disabled="!permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao" :id="`lotacao_maxima_${index}`" v-model="item.lotacao_maxima" type="text" class="form-control pt-0 pb-0" maxlength="9" @focus="item.oldVal = item.lotacao_maxima" @blur="checkValue(item)" readonly>
                </div>
              </td>

              <td data-label="Personal">
                <div class="d-flex justify-content-between w-100">
                  <b-form-checkbox :disabled="!permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao" :id="`personal_${index}`" :checked="item.personal" class="m-0 " @change="mudarPersonal(item)"/>
                  <button v-if="item.personal && permissoes['EDITAR']" class="btn btn-primary" @click="abrirModal(item)">Editar Horários</button>
                </div>
              </td>

              <td :class="{ 'not-pointer' : !permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao}" data-label="Situação">
                <div @click.prevent="inativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" :title="!permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao ? 'Desativar' : ''" class="align-middle text-success"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else :title="!permissoes['CRIAR'] || !permissoes['EDITAR'] || !permissoes['CRIAR'].possui_permissao || !permissoes['EDITAR'].possui_permissao ? 'Ativar' : ''" class="align-middle icon-danger"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div id="total-container" class="d-flex justify-content-between align-items-center">
      <div></div>

      <div class="info-label d-flex flex-column align-items-end">
        <div>
          <small v-if="totalItens >= 1">{{ totalItens }} ite{{ totalItens > 1 ? 'ns' : 'm' }} encontrado{{ totalItens > 1 ? 's' : '' }}</small>
          <small v-else>Nenhum item encontrado</small>
        </div>
      </div>
    </div>
    <modal-formulario ref="modalHorarioSala"/>
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'
import ModalFormulario from './ModalFormulario.vue'

export default {
  data () {
    return {
      oldVal: null
    }
  },
  components: {
    ModalFormulario
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('salaFranqueada', ['estaCarregando', 'todosItensCarregados', 'lista', 'itemSelecionadoID', 'totalItens']),
    ...mapState('sala', { salaCarregando: 'estaCarregando', salaCarregado: 'todosItensCarregados', salaLista: 'lista' }),
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },
  methods: {
    ...mapActions('salaFranqueada', ['listar', 'atualizar', 'criar']),
    ...mapMutations('salaFranqueada', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    checkValue (item) {
      if (item.lotacao_maxima === '') {
        item.lotacao_maxima = item.oldVal
      }

      this.salvar(item)
    },

    inativar (item) {
      if (this.permissoes['CRIAR'] || this.permissoes['EDITAR']) {
        const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
        EventBus.$emit('chamarModal', {
          resolve: success => {
            const data = item
            data.situacao = item.situacao === 'A' ? 'I' : 'A'
            this.salvar(data)
          }
        }, `Deseja ${mensagem} a sala "${item.descricao}" ?`)
      }
    },

    reloadLista () {
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    mudarPersonal (item) {
      item.personal = !item.personal
      this.salvar(item)
    },

    salvar (item) {
      this.SET_ITEM_SELECIONADO_ID(item.salaFranqueadaId || null)
      this.SET_ITEM_SELECIONADO(item)

      if (this.itemSelecionadoID) {
        this.atualizar().then((r) => {
          // console.log(r)
          // this.lista = r
          this.SET_PAGINA_ATUAL(1)
          // this.SET_LISTA([])
          this.listar(false)
        })
      } else {
        this.criar().then((res) => {
          // item.id = res.body.corpo.objetoORM
          this.SET_PAGINA_ATUAL(1)
          // this.SET_LISTA([])
          this.listar(false)
        })
      }
    },
    abrirModal(item) {
      this.$refs.modalHorarioSala.sala = item;
      this.$refs.modalHorarioSala.visible = true;
    }
  }
}
</script>
<style scoped>
.custom-checkbox {
  min-height: 1rem !important;
}
.col-md-8 {
  max-width: 50%;
}



.table-borderless.table-hover tbody tr:hover .form-control {
  background-color: #fff;
}
</style>
