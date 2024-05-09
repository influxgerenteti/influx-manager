<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/tipo-movimento-estoque/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table id="listaTipoMovimentoEstoque" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="tpe.descricao">Descrição</th>
            <th data-column="tpe.tipo_movimento">Tipo de Movimento</th>
            <th data-column="tpe.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar>
            <tr v-for="item in lista" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Descrição">{{ item.descricao }}</td>

              <td data-label="Tipo de Movimento">
                <div v-if="item.tipo_movimento === 'E' || item.tipo_movimento === 'AE'" class="text-primary">
                  <font-awesome-icon icon="arrow-alt-circle-up" /> Entrada
                </div>
                <div v-else class="text-secondary">
                  <font-awesome-icon icon="arrow-alt-circle-up" flip="vertical" /> Saída
                </div>
              </td>

              <td data-label="Situação">
                <div @click.prevent="inativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="check-circle" />
                </a> -->
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
  </div>
</template>

<script>
import {mapState, mapActions, mapMutations} from 'vuex'
import EventBus from '../../utils/event-bus'

export default {
  name: 'ListaTipoMovimentoEstoque',
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('tipoMovimentoEstoque', ['lista', 'item', 'totalItens', 'estaCarregando', 'todosItensCarregados']),
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
    ...mapActions('tipoMovimentoEstoque', {listar: 'getLista', atualizar: 'atualizar', atualizarSituacao: 'atualizarSituacao'}),
    ...mapMutations('tipoMovimentoEstoque', ['SET_ITEM', 'SET_SITUACAO', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.SET_ITEM(item)
        this.$router.push(`/configuracoes/tipo-movimento-estoque/atualizar/${item.id}`)
      }
    },

    inativar (item) {
      const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const data = Object.assign({}, item)
          this.SET_ITEM(data)
          this.SET_SITUACAO(data.situacao === 'A' ? 'I' : 'A')
          this.atualizarSituacao(data)
            .then(() => {
              item.situacao = data.situacao
            })
        }
      }, `Deseja ${mensagem} "${item.descricao}" ?`)
    }

  }
}
</script>
