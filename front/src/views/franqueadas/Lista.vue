<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="$route.path+'/adicionar'" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="fran.nome">Nome</th>
            <th data-column="fran.cnpj">CNPJ</th>
            <th data-column="fran.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="franqueada in listaFranqueada" :key="franqueada.id" @dblclick="alterarFranqueada(franqueada)">
              <td data-label="Nome">{{ franqueada.nome }}</td>
              <td data-label="CNPJ">{{ franqueada.cnpj | formatarCNPJ }}</td>

              <td data-label="Situação">
                <div @click.prevent="desativar(franqueada)">
                  <span v-b-tooltip.viewport.left.hover v-if="franqueada.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex justify-content-between coluna-icones">
                <!-- <a href="javascript:void(0)" title="Informações" class="icone-link" @click.prevent="mostrarFranqueada(franqueada)">
                  <font-awesome-icon icon="eye" />
                </a> -->
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="franqueada.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterarFranqueada(franqueada)">
                  <font-awesome-icon icon="pen" />
                </a>

                <!-- <a v-if="franqueada.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="desativar(franqueada)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Desativar" class="icone-link text-success" @click.prevent="desativar(franqueada)">
                  <font-awesome-icon icon="check-circle" />
                </a> -->
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaFranqueada.length && !estaCarregando" class="busca-vazia">
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
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaFranqueada',
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('franqueadas', ['listaFranqueada', 'objFranqueada', 'estaCarregando', 'todosItensCarregados', 'totalItens']),
    permitirCarregarMais: {
      get () {
        return !!this.listaFranqueada.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    if (this.estaCarregando === false) {
      this.listar()
    }
    console.log()
  },
  methods: {
    ...mapActions('franqueadas', {listar: 'getListaFranqueada', atualizar: 'atualizarFranqueada'}),
    ...mapMutations('franqueadas', ['setFranqueada', 'setFranqueadaSelecionada', 'setSituacao', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    mostrarFranqueada (franqueada) {
      this.setFranqueada(franqueada)
      this.$router.push(`${this.$route.path}/info/${franqueada.id}`)
    },

    alterarFranqueada (franqueada) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (franqueada.situacao !== 'A') {
          return
        }
        this.setFranqueada(franqueada)
        this.$router.push(`${this.$route.path}/atualizar/${franqueada.id}`)
      }
    },

    desativar (itItem) {
      const mensagem = itItem.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          let item = Object.assign({}, itItem)
          this.setFranqueada(item)
          this.setFranqueadaSelecionada(item.id)
          this.setSituacao(item.situacao === 'A' ? 'I' : 'A')
          this.atualizar()
            .then(() => {
              itItem.situacao = item.situacao
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${itItem.nome}?`)
    }
  }
}
</script>
