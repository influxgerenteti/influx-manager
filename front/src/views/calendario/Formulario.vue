<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'form-calendario_descricao'" for="descricao" class="col-form-label">Descrição *</label>
          <input id="descricao" v-model="descricao" type="text" class="form-control" required maxlength="50">
          <div class="invalid-feedback calendario">Preencha a descrição!</div>
        </div>
        <!-- <div class="col-md-6">
          <label for="descricao" class="col-form-label">Ano</label>
          <g-select
                          v-model="itemSelecionado.fornecedor_pessoa"
                          :options="listaPessoas.filter(item => tipoFornecedor ? tipoFornecedor.includes(item.categoria.id) : true)"
                          :custom-label="selectOptionText"
                          label="razao_social"
                          track-by="id"
                />
        </div> -->
      </div>
      <div class="form-group row content-calendario mb-0">

        <div v-for="mes in meses" :key="mes.num" class="col-md-3 mb-3">
          <div class="d-flex justify-content-between">
            <label class="col-form-label">{{ mes.nome }}</label>
            <a v-b-modal.mes-calendario href="javascript:void(0)" title="Atualizar" @click="mesLista = dataLista.filter(item => new Date(item.data_inicial).getMonth() === mes.num), title = mes.nome">
              <font-awesome-icon icon="pen" />
            </a>
          </div>

          <!-- <button v-b-modal.mes-calendario type="button" class="btn btn-azul" @click="mesLista = dataLista.filter(item => new Date(item.data).getMonth() === mes.num)">Atualizar</button> -->

          <div class="table-calendario">
            <perfect-scrollbar>
              <ul v-for="item in dataLista.filter(item => new Date(item.data_inicial).getMonth() === mes.num)" :key="item.id">
                <li><span class="badge align-middle rounded">{{ new Date(item.data_inicial).getDate() }}</span>{{ dias[new Date(item.data_inicial).getDay()] }}</li>
                <li>{{ item.descricao }}</li>
              </ul>
            </perfect-scrollbar>
          </div>
        </div>

      </div>
      <!-- <div class="content-sector sector-azul p-3">
        <div class="d-flex justify-content-between">
          <h5 class="title-module">Lições</h5>

          <a href="javascript:void(0)" title="Adicionar" class="btn btn-azul mb-3" @click.prevent="listaAdd()">
            <font-awesome-icon icon="plus" />
          </a>
        </div>

        <div class="form-group row mb-0">
          <div class="col-md-12 table-responsive-sm">
            <table class="table-scroll mobile-cards table b-table table-borderless">
              <thead>
                <tr>
                  <th class="number">Número</th>
                  <th>Descrição *</th>
                  <th>Observação</th>
                  <th class="coluna-icones"></th>
                </tr>
              </thead>
              <tbody>
                <perfect-scrollbar>
                  <tr v-for="(item, index) in lista" :key="index">
                    <td :num="item.numero = index + 1" :plan="!itemSelecionadoID ? null : item.planejamento_licao = itemSelecionadoID" data-label="Número" class="number">{{ index + 1 }}</td>
                    <td data-label="Descrição *">
                      <div class="col-md-12 pl-0">
                        <input v-model="item.descricao" type="text" class="form-control" maxlength="50" required>
                        <div class="invalid-feedback">Preencha a descrição!</div>
                      </div>
                    </td>
                    <td data-label="Observação">
                      <div class="col-md-12 pl-0">
                        <textarea v-model="item.observacao" class="form-control" rows="3" maxlength="5000"></textarea>
                        <span class="text-secondary">Limite de caracteres: {{ 5000 - (item.observacao || '').length }}</span>
                      </div>
                    </td>
                    <td class="d-flex coluna-icones options-licao">
                      <div>
                        <a v-if="!item.id && item.descricao !== '' && isEdit" href="javascript:void(0)" title="Salvar" class="icone-link text-success" @click.prevent="saveLine(item)">
                          <font-awesome-icon icon="check" />
                        </a>
                      </div>

                      <a href="javascript:void(0)" title="Limpar" class="icone-link" @click.prevent="limpar(item)">
                        <font-awesome-icon icon="backspace" />
                      </a>

                      <div>
                        <a v-if="lista.length > 1 && descricao !== ''" href="javascript:void(0)" title="Excluir" class="icone-link text-muted" @click.prevent="excluir(index, item)">
                          <font-awesome-icon icon="trash-alt" />
                        </a>
                      </div>

                    </td>
                  </tr>
                  <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
                    <p>Nenhum resultado encontrado.</p>
                  </div>
                </perfect-scrollbar>
              </tbody>
            </table>
          </div>
        </div>
      </div> -->

      <!-- <div class="mb-3">
        <div v-if="isEdit && lista.filter(item => item.id === undefined).length > 0" class="list-group-item list-group-item-warning border-0">
          Existem novos itens na lista de Lições que devem ser salvos!
        </div>
        <div v-else style="height: 45px;"></div>
      </div> -->

      <div class="form-group pt-2">
        <b-btn type="submit" variant="verde">Salvar</b-btn>
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>

    <b-modal id="mes-calendario" v-model="visible" size="lg" centered no-close-on-backdrop hide-header hide-footer modal-class="mes-calendario">
      <div class="d-flex justify-content-between">
        <h5 class="title-module">{{ title }}</h5>

        <a href="javascript:void(0)" title="Adicionar" class="btn btn-roxo mb-3" @click.prevent="listaAdd()">
          <font-awesome-icon icon="plus" />
        </a>
      </div>

      <form @submit.prevent="salvarMes">
        <table class="table-scroll mobile-cards table b-table table-borderless">
          <!-- <table class="mobile-cards table b-table table-hover table-borderless"> -->
          <thead>
            <tr>
              <th class="number">Dia/Período</th>
              <th>Descrição</th>
              <th class="coluna-icones"></th>
            </tr>
          </thead>
          <tbody>
            <perfect-scrollbar>
              <div v-if="!mesLista.length" class="busca-vazia">
                <p>Nenhuma data cadastrada.</p>
              </div>
              <tr v-for="(item, index) in mesLista" :key="index">
                <div v-b-toggle="`opt-toggle-${index}`" class="tr-head">
                  <td data-label="Dia/Período" class="number"><div><span class="badge align-middle rounded">{{ new Date(item.data_inicial).getDate() }}</span>{{ dias[new Date(item.data_inicial).getDay()] }}</div></td>
                  <td data-label="Descrição">{{ item.descricao }}</td>

                  <!-- <td data-label="Situação">
                    <div @click.prevent="inativar(item)">
                      <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                      <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                    </div>
                  </td> -->

                  <td class="d-flex coluna-icones">
                    <!-- <a href="javascript:void(0)" class="icone-link" title="Visualizar" @click.prevent="mostrar(item)">
                      <font-awesome-icon icon="eye" />
                    </a> -->
                    <!-- <a :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" class="icone-link" title="Atualizar" @click.prevent="alterar(item)">
                      <font-awesome-icon icon="pen" />
                    </a> -->

                    <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
                      <font-awesome-icon icon="ban" />
                    </a>
                    <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item)">
                      <font-awesome-icon icon="check-circle" />
                    </a> -->
                  </td>
                </div>

                <b-collapse :id="`opt-toggle-${index}`" accordion="datas" class="opt-mes">
                  <div class="p-3 col-md-12">
                    <div class="form-group row">
                      <div class="col-md-4">
                        <label :for="`data_inicial_${index}`" class="col-form-label">Dia *</label>
                        <g-datepicker :value="data_inicial" :selected="selectDataInicial" :element-id="`data_inicial_${index}`" maxlength="10" class="form-control" />

                        <!-- <label for="sigla" class="col-form-label">Sigla *</label>
                        <input id="sigla" v-model="sigla" type="text" class="form-control" required maxlength="20">
                        <div class="invalid-feedback">Preencha a sigla!</div> -->
                      </div>

                    </div>

                  </div>
                </b-collapse>
              </tr>
            </perfect-scrollbar>
          </tbody>
          <label class="table-count">{{ mesLista.length }} itens na lista.</label>
        </table>

        <button type="submit" class="btn btn-azul">Salvar</button>
        <button type="button" class="btn btn-link" @click="visible = false">Cancelar</button>
      </form>
    </b-modal>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'
import EventBus from '../../utils/event-bus'
import { setTimeout } from 'timers'

export default {
  data () {
    return {
      isValid: true,
      isEdit: false,
      editado: false,
      visible: false,
      dataLista: [],
      mesLista: [],
      title: '',
      meses: [
        {num: 0, nome: 'Janeiro'},
        {num: 1, nome: 'Fevereiro'},
        {num: 2, nome: 'Março'},
        {num: 3, nome: 'Abril'},
        {num: 4, nome: 'Maio'},
        {num: 5, nome: 'Junho'},
        {num: 6, nome: 'Julho'},
        {num: 7, nome: 'Agosto'},
        {num: 8, nome: 'Setembro'},
        {num: 9, nome: 'Outubro'},
        {num: 10, nome: 'Novembro'},
        {num: 11, nome: 'Dezembro'}
      ],
      dias: [
        'Domingo',
        'Segunda',
        'Terça',
        'Quarta',
        'Quinta',
        'Sexta',
        'Sábado'
      ],
      data_inicial: ''
    }
  },
  computed: {
    ...mapState('calendario', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

    descricao: {
      get () {
        return this.itemSelecionado.descricao
      },
      set (value) {
        if (this.isEdit) {
          this.editado = true
        }
        this.SET_DESCRICAO(value)
      }
    },

    lista: {
      get () {
        return this.itemSelecionado.licaos
      },
      set (value) {
        this.SET_LICAO(value)
      }
    }

  },
  created () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.isEdit = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar().then(() => {
        this.dataLista = this.itemSelecionado.data_calendarios
        console.log('DATAS...', this.dataLista)
      })
    } else {
      /* this.lista.push({
        descricao: '',
        observacao: ''
      }) */
    }
  },
  validations: {
    descricao: {required}
  },
  methods: {
    ...mapActions('calendario', ['buscar', 'criar', 'atualizar']),
    ...mapMutations('calendario', ['SET_DESCRICAO', 'SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO']),

    listaAdd () {
      console.log(this.mesLista)
      /* this.lista.push({descricao: '', observacao: ''})
      setTimeout(() => {
        document.getElementsByTagName('input')[this.lista.length].focus()
      }, 100) */
    },

    selectDataInicial (data) {
      this.data_inicial = data
    },

    selectDataFinal (data) {
      this.data_final = data
    },

    salvarMes () {
      console.log('SALVANDO...')
    },

    limpar (item) {
      item.descricao = ''
      item.observacao = ''

      this.isValid = !this.isEdit
    },

    excluir (index, item) {
      if (this.isEdit && !!item.id) {
        EventBus.$emit('chamarModal', {
          resolve: success => {
            this.licaoSelecionadoID(item.id)
            this.licaoExcluir().then(() => {
              this.lista.splice(index, 1)
              if (!this.editado) {
                delete this.itemSelecionado.descricao
              }
              setTimeout(() => {
                this.atualizar()
              }, 100)
              // this.SET_PAGINA_ATUAL(1)
              // this.listar()
            })
          }
        }, `Lição "${item.descricao}" será excluída permanetemente. Deseja prosseguir?`)
      } else {
        this.lista.splice(index, 1)
      }
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/calendario')
    },

    saveLine (item) {
      this.licaoSelecionado(item)
      this.licaoCriar().then((id) => {
        item.id = id
        this.lista.splice(this.lista.map(item => item.numero).indexOf(item.numero), 1, item)
      })
    },

    salvar () {
      if (this.$v.$invalid || !this.isValid) {
        this.isValid = false
        return
      }

      if (this.itemSelecionadoID) {
        if (!this.editado) {
          delete this.itemSelecionado.descricao
        }
        this.atualizar().then(this.voltar).catch(console.error)
      } else {
        this.criar().then(this.voltar).catch(console.error)
      }
    }

  }
}
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 360px);
  height: -webkit-calc(100vh - 360px);
  height: -moz-calc(100vh - 360px);
  margin-bottom: 0;
}

.number {
  max-width: 100px;
}

.options-licao div {
  width: 1.25em;
  margin: auto;
}

.invalid-feedback:not(.calendario) {
  position: relative;
}

/* a.icone-link.text-muted {
  font-size: x-large;
} */

.table .number {
  max-width: 120px;
}

@media (min-width: 768px) and (max-width: 1530px) {
  .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
  }
/*   .number {
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
  } */
}

.content-sector {
  height: calc(100vh - 355px);
  height: -webkit-calc(100vh - 355px);
  height: -moz-calc(100vh - 355px);
}
.table-scroll {
  height: calc(100vh - 420px);
  height: -webkit-calc(100vh - 420px);
  height: -moz-calc(100vh - 420px);
}

.table-calendario {
  border: 1px solid #EBECF0;
  width: 100%;
  height: 187px;
  overflow: hidden;
}
.table-calendario .ps {
  max-height: 100%;
}
.ps__rail-y {
  opacity: 1;
}
.table-calendario ul {
  list-style-type: none;
  display: flex;
  padding: 0;
  margin: 0;
  cursor: default;
}
.table-calendario ul:hover {
  background-color: #EBECF0;
}
.table-calendario ul li {
  padding: .5rem;
  flex: 1 1 0;
}
.table-calendario ul li:first-child {
  max-width: 115px;
}
.table-calendario ul span,
.table span{
  min-width: 25px;
  font-size: 95%;
  color: #fff;
  margin-right: .5rem;
  background-color: #ec8644;
}

.content-calendario a {
  color: #4a4a4a !important;
}

.nacional {
  background-color: #85d017;
}
.data {
  background-color: #4a69c5;
}
.periodo {
  background-color: #ec8644;
}

.tr-head {
  cursor: pointer;
  display: flex;
}
.opt-mes {
  /* background-color: #EBECF0; */
  box-shadow: inset 0 1px 3px #dbdbdb;
  background-color: #fff;
}
.table-scroll tbody tr {
  flex-direction: column;
}

/* .table-scroll tbody tr:last-child .opt-mes {
  border-bottom: 1px solid #EBECF0;
} */
/* .table-scroll tbody tr:last-child {
  background-color: black;
} */

tr.collapsed:hover {
  background-color: #EBECF0;
}

@media (max-width: 768px) {
  .table .number {
    max-width: 100%;
  }
  .tr-head {
    display: block;
  }
}
</style>
