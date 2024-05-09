<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/modulo/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaModulo" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="m.nome">Nome</th>
            <th data-column="m.modulo_pai">Módulo Pai</th>
            <th data-column="m.url">URL</th>
            <th data-column="m.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="modulo in listaModulo" :key="modulo.id" @dblclick="editar(modulo)">
              <td data-label="Nome">{{ modulo.nome }}</td>
              <td data-label="Módulo Pai">{{ modulo.modulo_pai ? modulo.modulo_pai.nome : '--' }}</td>
              <td data-label="URL">{{ modulo.url || '--' }}</td>

              <td data-label="Situação">
                <div @click.prevent="inativar(modulo)">
                  <span v-b-tooltip.viewport.left.hover v-if="modulo.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <!-- <a href="javascript:void(0)" title="Informações" class="icone-link" @click.prevent="mostrarModulo(modulo)">
                  <font-awesome-icon icon="eye" />
                </a> -->
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="editar(modulo)">
                  <font-awesome-icon icon="pen" />
                </a>
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!listaModulo.length && !estaCarregando" class="busca-vazia">
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
  name: 'ListaModulo',
  computed: {
    ...mapState('modulos', ['permissoes', 'listaModulo', 'objModulo', 'totalItens', 'estaCarregando', 'todosItensCarregados']),
    permitirCarregarMais: {
      get () {
        return !!this.listaModulo.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  mounted () {
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    this.listar()
  },
  methods: {
    ...mapActions('modulos', {listar: 'getListaModulo', atualizar: 'atualizarModulo', filtrar: 'getListaModulo'}),
    ...mapMutations('modulos', ['setModulo', 'setModuloSelecionado', 'setListaModulo', 'setModuloPai', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    mostrarModulo (modulo) {
      this.setModulo(modulo)
      this.$router.push(`/configuracoes/modulo/info/${modulo.id}`)
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    inativar (modulo) {
      const mensagem = modulo.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          modulo.situacao = modulo.situacao === 'A' ? 'I' : 'A'
          this.setModulo(modulo)
          this.atualizar()
            .then(() => {
              this.filtrar()
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${modulo.nome}?`)
    },

    editar (modulo) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (modulo.situacao !== 'A') {
          return
        }
        if (modulo.modulo_pai) {
          this.setModulo(modulo.modulo_pai.id)
        }
        this.$router.push(`/configuracoes/modulo/atualizar/${modulo.id}`)
      }
    }

  }
}
</script>
