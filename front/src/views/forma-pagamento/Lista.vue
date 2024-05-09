<template>
  <div class="animated fadeIn">
    <!-- <div class="d-flex justify-content-end mb-4">
      <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" to="/configuracoes/forma-pagamento/adicionar" class="btn btn-azul">
        <font-awesome-icon icon="plus" /> Adicionar
      </router-link>
      foi removido por questões tecnicas. caso necessario incluir os registros diretamente no banco.
    </div> -->

    <div class="table-responsive-sm">
      <g-table id="listaItem" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="fp.descricao" >Descrição</th>
            <th data-column="fp.descricao_abreviada">Abreviação</th>
            <th data-column="fp.liquidacao_imediata"> Liquidação Imediata</th>
            <th>Forma de pagamento</th>
            <th data-column="fp.situacao"> Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <tr v-for="item in lista" :key="item.id" @dblclick="alterar(item)">
              <td data-label="Descrição">{{ item.descricao }}</td>
              <td data-label="Abreviação">{{ item.descricao_abreviada }}</td>

              <td data-label="Liquidação Imediata">
                <div class="icone-acao" @click.prevent="desativarLiquidacaoImediata(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.liquidacao_imediata === true" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td data-label="Forma de pagamento">
                {{ exibirFormaPagamento(item) }}
              </td>

              <td data-label="Situação">
                <div @click.prevent="desativar(item)">
                  <span v-b-tooltip.viewport.left.hover v-if="item.situacao === 'A'" class="align-middle text-success" title="Desativar"><font-awesome-icon icon="check-square" /></span>
                  <span v-b-tooltip.viewport.left.hover v-else class="align-middle icon-danger" title="Ativar"><font-awesome-icon icon="square" /></span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <!-- <a href="javascript:void(0)" title="Informações" class="icone-link" @click.prevent="mostrar(item)">
                  <font-awesome-icon icon="eye" />
                </a> -->
                <a v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :class="item.situacao === 'A' ? null : 'disable-icon'" href="javascript:void(0)" title="Alterar" class="icone-link" @click.prevent="alterar(item)">
                  <font-awesome-icon icon="pen" />
                </a>
                <!-- <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="desativar(item)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Desativar" class="icone-link text-success" @click.prevent="desativar(item)">
                  <font-awesome-icon icon="check-circle" />
                </a> -->
              </td>
            </tr>
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nunhum resultado encontrado.</p>
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
  data () {
    return {
      formas: {
        forma_boleto: 'Cobrança bancária (boleto)',
        forma_cheque: 'Cheque',
        forma_cartao: 'Cartão de crédito',
        forma_cartao_debito: 'Cartão de débito',
        forma_transferencia: 'Transferência'
      }
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('formaPagamento', ['lista', 'item', 'totalItens', 'estaCarregando', 'todosItensCarregados']),
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
    ...mapActions('formaPagamento', {listar: 'getLista', atualizar: 'atualizar'}),
    ...mapMutations('formaPagamento', ['SET_ITEM', 'SET_SITUACAO', 'SET_LIQUIDACAO_IMEDIATA', 'SET_FRANQUEADA', 'SET_GRUPO', 'SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    exibirFormaPagamento (item) {
      let texto = 'Nenhuma'
      Object.keys(item).map(forma => {
        if (forma.includes('forma') && item[forma] === true) {
          texto = this.formas[forma]
        }
      })
      return texto
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    mostrar (item) {
      this.SET_ITEM(item)
      this.$router.push(`/configuracoes/forma-pagamento/info/${item.id}`)
    },

    alterar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.SET_ITEM(item)
        this.$router.push(`/configuracoes/forma-pagamento/atualizar/${item.id}`)
      }
    },

    desativar (itTem) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const item = Object.assign({}, itTem)
          this.SET_ITEM(item)
          this.SET_SITUACAO(item.situacao === 'A' ? 'I' : 'A')
          this.atualizar()
            .then(() => {
              itTem.situacao = item.situacao
            })
            .catch(error => {
              console.error(error)
              // this.SET_PAGINA_ATUAL(1)
              // this.listar()
            })
        }
      })
    },

    desativarLiquidacaoImediata (itTem) {
      EventBus.$emit('chamarModal', {
        resolve: success => {
          const item = Object.assign({}, itTem)
          this.SET_ITEM(item)
          this.SET_LIQUIDACAO_IMEDIATA(!item.liquidacao_imediata)
          this.atualizar()
            .then(() => {
              itTem.liquidacao_imediata = !!item.liquidacao_imediata
            })
            .catch(error => {
              console.error(error)
              // this.SET_PAGINA_ATUAL(1)
              // this.listar()
            })
        }
      })
    }

  }
}
</script>
