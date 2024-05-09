<template>
  <div class="table-responsive-sm">
    <g-table :sort="sortTable" class="table-followup">
      <thead class="text-dark">
        <tr>
          <th data-column="" class="size-75">Data</th>
          <th data-column="" class="size-90">Próx. contato</th>
          <th data-column="" class="size-175">Contato</th>
          <th data-column="" class="min-size-300">Resumo</th>
          <th data-column="" class="size-160">Etapa funil</th>
          <th data-column="" class="size-175">Tipo contato</th>
          <th data-column="" class="size-175">Forma contato</th>
          <th data-column="" class="size-160">Telefone</th>
          <th data-column=""><!-- Situação --> </th>
          <th class="coluna-icones"></th>
        </tr>
      </thead>

      <tbody ref="scroll-wrap">
        <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
          <div v-if="estaCarregando" class="d-flex h-100">
            <load-placeholder :loading="estaCarregando" />
          </div>

          <div v-if="!listaItens.length && !estaCarregando" class="busca-vazia">
            <p>Nenhum resultado encontrado.</p>
          </div>
          <tr v-for="item in listaItens" :key="item.id" @dblclick="editar(item)">
            <td data-label="Data" class="size-75">{{ item.followupComercials[0] ? item.followupComercials[0].data_registro : '' | formatarData }}</td>
            <td data-label="Próx. Contato" class="size-90">{{ item.agendaComerciais[0] ? item.agendaComerciais[0].data_agendamento : '' | formatarData }}</td>
            <td data-label="Contato" class="size-175"><span class=" truncate">{{ item.nome ? item.nome : '' }}</span></td>
            <td :id="`popOverResumo_${item.id}`" data-label="Resumo" class="min-size-300">{{ item.followupComercials[0] ? mostrarResumo(item.followupComercials, 'tabela') : '' }}</td>
            <td data-label="Etapa funil" class="size-160">{{ item.workflow ? item.workflow.descricao : '' }}</td>
            <td data-label="Tipo contato" class="size-175">{{ tipoContato(item) }}</td>
            <td data-label="Forma contato" class="size-175">{{ formaContato(item) }}</td>
            <td data-label="Telefone" class="size-160">{{ item.telefone_contato ? item.telefone_contato : '' | formatarTelefone }}</td>
            <td data-label="">
              <template v-if="item.agendaComerciais[0]">
                <span v-b-tooltip.viewport.left.hover :title="listaSituacao.find(situacao => situacao.value == item.agendaComerciais[0].situacao).text" :class="'circle-badge-' + item.agendaComerciais[0].situacao.toLowerCase(item.agendaComerciais[0].situacao)" class="circle-badge"></span>
              </template>
            </td>
            <td class="d-flex coluna-icones">
              <!-- Ações -->
              <template v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)">
                <a v-b-tooltip.viewport.left.hover class="icone-link" title="Atualizar" @click="editar(item)">
                  <font-awesome-icon icon="pen"/>
                </a>
              </template>
            </td>

            <!-- PopOver -->
            <b-popover :target="`popOverResumo_${item.id}`" placement="bottom" triggers="hover" class="popover-card">
              <div class="form-group">
                <label class="form-label">Follow-Up:</label>
                <p>{{ mostrarResumo(item.followupComercials, 'tooltip') }}</p>
              </div>
            </b-popover>
            <!-- /PopOver -->

          </tr>
        </perfect-scrollbar>
      </tbody>
    </g-table>
  </div>
</template>
<script>
import { not } from 'vuelidate/lib/validators'
import {mapState, mapActions, mapMutations} from 'vuex'

export default {
  name: 'ListagemFollowupInteressadoComponent',
  props: {
    isFollowupAluno: {
      type: Boolean,
      required: true,
      default: false
    },
    listaSituacao: {
      type: [Object, Array],
      required: true,
      default: () => ([])
    },
    listaItens: {
      type: [Object, Array],
      required: true,
      default: () => ([])
    }
  },
  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('followUp', {estaCarregando: 'estaCarregando', todosItensCarregadosInteressadoAluno: 'todosItensCarregadosInteressadoAluno'}),

    permitirCarregarMais: {
      get () {
        return !!this.listaItens.length && !this.estaCarregando && !this.todosItensCarregadosInteressadoAluno
      }
    }
  },
  methods: {
    ...mapActions('followUp', {listarItens: 'listar'}),
    ...mapMutations('followUp', ['SET_LISTA_INTERESSADO_ALUNO', 'SET_PAGINA_ATUAL', 'SET_ORDER_BY']),

    mostrarResumo (arrayFollowupComercials, fonte) {
      const followUp = arrayFollowupComercials.find((item) => {
        if (item.followup) {
          return item
        }
      })

      if (followUp) {
        let stringMostrar = followUp.followup

        const tamanhoTruncate = fonte === 'tooltip' ? 300 : 65
        if (stringMostrar.length > tamanhoTruncate) {
          let inicio = 0

          if (fonte === 'tabela') {
            const textos = ['Follow-up Receptivo\n', 'Follow-up Ativo\n']
            for (let i = 0; i < textos.length; i++) {
              const indice = stringMostrar.indexOf(textos[i])
              if (indice !== -1) {
                inicio = indice + textos[i].length
                break
              }
            }
          }

          const fim = inicio + tamanhoTruncate
          stringMostrar = stringMostrar.substring(inicio, fim)
          return stringMostrar
        }
        return stringMostrar
      } else {
        return '--'
      }
    },

    tipoContato (item) {
      return item.tipo_lead ? (item.tipo_lead === 'A' ? 'Contato Ativo' : 'Contato Receptivo') : ''
    },

    formaContato (item) {
      if (item.tipo_contato) {
        return item.tipo_contato.nome
      } 
      if (item.tipo_prospeccao) {
        return item.tipo_prospeccao.descricao
      } 
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA_INTERESSADO_ALUNO([])
      this.listarItens()
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push({ path: `/cadastros/interessados/followup/${item.id}`, query: { bVeioListagemFollowup: true } })
      }
    },

    carregarMais () {
      this.listarItens()
    }
  }
}
</script>
