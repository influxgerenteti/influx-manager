<template>
  <div class="animated fadeIn">
    <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/tipo-movimento-conta/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead class="text-dark">
          <tr>
            <th data-column="tpmc.descricao">Descrição</th>
            <th data-column="tpmc.tipo_operacao">Operação</th>
            <th data-column="tpmc.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar>
            <tr v-for="item in lista" :key="item.id">
              <td data-label="Descrição">{{ item.descricao }}</td>

              <td data-label="Operação" class="text-muted">
                <div>
                  <template v-if="item.tipo_operacao === 'C'">
                    <font-awesome-icon icon="plus" class="text-success" /> Crédito
                  </template>
                  <template v-else-if="item.tipo_operacao === 'D'">
                    <font-awesome-icon icon="minus" class="icon-danger" /> Débito
                  </template>
                  <template v-else>
                    <font-awesome-icon icon="exchange-alt" class="text-info" /> Transferência
                  </template>
                </div>
              </td>

              <td data-label="Situação">
                <template v-if="!item.reservado">
                  <div @click.prevent="inativar(item)">
                    <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                    <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                  </div>
                </template>
                <template v-else>
                  <div class="not-pointer">
                    <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-secondary" title="Ativo"><font-awesome-icon icon="check-square" /></span>
                    <span v-else class="align-middle icon-danger" title="Inativo"><font-awesome-icon icon="square" /></span>
                  </div>
                </template>
              </td>

              <td :class="!item.reservado ? null : 'invisible-options'" class="d-flex coluna-icones">
                <template v-if="!item.reservado">
                  <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterar(item)">
                    <font-awesome-icon icon="pen" />
                  </a>
                  <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
                    <font-awesome-icon icon="ban" />
                  </a>
                  <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item)">
                    <font-awesome-icon icon="check-circle" />
                  </a> -->
                </template>
              </td>
            </tr>
            <div v-if="estaCarregando" class="form-loading">
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
  name: 'ListaTipoMovimentoConta',
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('tipoMovimentoConta', ['lista', 'item', 'totalItens', 'estaCarregando', 'todosItensCarregados']),
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
    ...mapActions('tipoMovimentoConta', {listar: 'getLista', atualizar: 'atualizar', atualizarSituacao: 'atualizarSituacao'}),
    ...mapMutations('tipoMovimentoConta', ['SET_ITEM', 'SET_SITUACAO', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

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
        this.SET_ITEM(item)
        this.$router.push(`/configuracoes/tipo-movimento-conta/atualizar/${item.id}`)
      }
    },

    inativar (item) {
      const mensagem = item.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const data = Object.assign({}, item)
          this.SET_ITEM(data)
          this.SET_SITUACAO(data.situacao === 'A' ? 'I' : 'A')
          this.atualizar(data)
            .then(() => {
              item.situacao = data.situacao
            })
        }
      }, `Deseja ${mensagem} "${item.descricao}" ?`)
    }

  }
}
</script>
