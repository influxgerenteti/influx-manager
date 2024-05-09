<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div v-b-toggle.filtros-rapidos :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="acaoBotaoFiltroRapido()">Filtro Rápido</div>
          <div v-b-toggle.filtros-avancados :class="{'filtro-selecionado': filtroSelecionado === 2}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="acaoBotaoFiltroAvancado()">Avançado</div>
        </div>

        <div class="d-flex justify-content-end">
          <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="`${$route.path}/adicionar`" class="btn btn-azul">
            <font-awesome-icon icon="plus" /> Adicionar
          </router-link>
        </div>
      </div>

      <b-collapse id="filtros-rapidos" accordion="filtros">
        <form class="p-2">
          <div class="row">
            <b-col md="4">
              <label v-help-hint="'filtroRapido-busca_funcionario'" for="busca_funcionario" class="form-label">Funcionário</label>
              <typeahead
                id="busca_funcionario"
                :item-hit="setFuncionario"
                key-name="pessoa.nome_contato"
                source-path="/api/funcionario/buscar_nome_contato"
              />
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-apelido'" for="apelido" class="form-label">Apelido</label>
              <input id="apelido" v-model="filtros.apelido" type="text" class="form-control" @change="filtrar()" >
            </b-col>

            <b-col md="4">
              <label v-help-hint="'filtroRapido-cpf'" for="cpf" class="form-label">CPF</label>
              <input v-mask="['###.###.###-##']" id="cpf" v-model="filtros.cnpj_cpf" type="text" class="form-control" @change="filtrar()" >
            </b-col>
          </div>
        </form>
      </b-collapse>

      <b-collapse id="filtros-avancados" accordion="filtros">
        <form class="p-2" @submit.prevent="filtrar()">
          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'filtroAvancado-busca_funcionario_avancado'" for="busca_funcionario_avancado" class="form-label">Funcionário</label>
              <typeahead
                id="busca_funcionario_avancado"
                :item-hit="setFuncionarioAvancado"
                key-name="pessoa.nome_contato"
                source-path="/api/funcionario/buscar_nome_contato"
              />
            </b-col>

            <b-col md="9">
              <b-row>
                <b-col md="2">
                  <label v-help-hint="'filtroAvancado-cpf_avancado'" for="cpf_avancado" class="form-label">CPF</label>
                  <input v-mask="['###.###.###-##']" id="cpf_avancado" v-model="filtros.cnpj_cpf" type="text" class="form-control" >
                </b-col>

                <b-col md="2">
                  <label v-help-hint="'filtroAvancado-data_admissao'" for="data_admissao" class="form-label">Admissão</label>
                  <g-datepicker :element-id="'data_inicial_vencimento'" :value="filtros.data_admissao" @selected="setDataAdmissao" />
                </b-col>

                <b-col md="2">
                  <label v-help-hint="'filtroAvancado-data_demissao'" for="data_demissao" class="form-label">Demissão</label>
                  <g-datepicker :element-id="'data_demissao'" :value="filtros.data_demissao" @selected="setDataDemissao" />
                </b-col>

                <b-col md="3">
                  <label v-help-hint="'filtroAvancado-cargo_funcionario'" for="cargo_funcionario" class="form-label">Cargo</label>
                  <g-select
                    id="cargo_funcionario"
                    :value="filtros.cargo"
                    :select="setCargo"
                    :options="listaCargo"
                    class="multiselect-truncate"
                    label="descricao"
                    track-by="id"
                  />
                </b-col>

                <b-col md="3">
                  <label v-help-hint="'filtroAvancado-email_usuario'" for="email_usuario" class="form-label">E-mail de Usuário</label>
                  <input id="email_usuario" v-model="filtros.email_usuario" type="email" class="form-control" >
                </b-col>
              </b-row>
            </b-col>
          </b-row>

          <b-row>
            <b-col md="auto">
              <label v-help-hint="'filtroAvancado-funcionario.tipo_pagamento'" for="tipo_pagamento" class="form-label">Tipo de Pagamento</label>
              <b-form-checkbox-group id="tipos_pagamento" v-model="filtros.tipo_pagamento" :options="tiposPagamento" buttons button-variant="cinza" name="tipo_pagamento" class="checkbtn-line" />
            </b-col>

            <b-col md="auto">
              <label class="form-label">&nbsp;</label>
              <b-form-checkbox id="check-consultor" v-model="filtros.consultor" :value="true" :unchecked-value="null" name="check-consultor">
                Consultor
              </b-form-checkbox>

              <b-form-checkbox id="check-gestor_comercial" v-model="filtros.gestor_comercial" :value="true" :unchecked-value="null" name="check-gestor_comercial">
                Gestor Comercial
              </b-form-checkbox>

              <b-form-checkbox id="check-coordenador_pedagogico" v-model="filtros.coordenador_pedagogico" :value="true" :unchecked-value="null" name="check-coordenador_pedagogico">
                Coordenador Pedagógico
              </b-form-checkbox>

              <b-form-checkbox id="check-atendente" v-model="filtros.atendente" :value="true" :unchecked-value="null" name="check-atendente">
                Atendente
              </b-form-checkbox>

              <b-form-checkbox id="check-instrutor" v-model="filtros.instrutor" :value="true" :unchecked-value="null" name="check-instrutor">
                Instrutor
              </b-form-checkbox>

              <b-form-checkbox id="check-instrutor_personal" v-model="filtros.instrutor_personal" :value="true" :unchecked-value="null" name="check-instrutor_personal">
                Instrutor Personal
              </b-form-checkbox>
            </b-col>
          </b-row>

          <button v-b-toggle.filtros-avancados type="submit" class="btn btn-cinza btn-block text-uppercase mt-3">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :sort="sortTable">
        <thead>
          <tr>
            <th data-column="pes.nome_contato">Nome</th>
            <th data-column="pes.cnpj_cpf">CPF</th>
            <th data-column="pes.telefone_preferencial">Telefone</th>
            <th data-column="func.cargo">Cargo</th>
            <th data-column="func.data_admissao" class="d-block text-truncate coluna-data">Admissão</th>
            <th data-column="func.data_demissao" class="d-block text-truncate coluna-data">Demissão</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>

        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>

            <div v-else-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="(item, index) in lista" v-else :key="index" @dblclick="editar(item)">
              <td v-b-tooltip :title="item.pessoa.nome_contato" data-label="Nome" class="d-block text-truncate">{{ item.pessoa.nome_contato }}</td>

              <td data-label="CPF">{{ item.pessoa.cnpj_cpf | formatarCPF }}</td>

              <td data-label="Telefone">{{ item.pessoa.telefone_preferencial | formatarTelefone }}</td>

              <td data-label="Cargo">
                <template v-if="item.cargo">
                  {{ item.cargo.descricao }}
                </template>
              </td>

              <td data-label="Admissão" class="coluna-data">{{ item.data_admissao | formatarData }}</td>

              <td data-label="Demissão" class="coluna-data">{{ item.data_demissao | formatarData }}</td>

              <td class="d-flex coluna-icones">
                <router-link v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.id}`" class="icone-link" title="Atualizar">
                  <font-awesome-icon icon="pen" />
                </router-link>
              </td>
            </tr>
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

export default {
  data () {
    return {
      filtroSelecionado: null,
      tiposPagamento: {
        'H': 'Horista',
        'M': 'Mensalista'
      }
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('funcionario', ['estaCarregando', 'todosItensCarregados', 'lista', 'totalItens', 'filtros']),

    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    },

    listaCargo: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.cargo.lista)
      }
    }
  },

  mounted () {
    this.filtros.apenas_funcionarios_ativos = false
    this.filtros.consultor_ou_gestor_comercial = false
    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])

    this.$store.commit('cargo/SET_PAGINA_ATUAL', 1)
    this.$store.dispatch('cargo/listar')

    if (!this.estaCarregando) {
      this.listar()
    }
  },

  methods: {
    ...mapMutations('funcionario', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),
    ...mapActions('funcionario', ['listar']),

    carregarMais () {
      this.listar()
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        this.$router.push(`${this.$route.path}/atualizar/${item.id}`)
      }
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    acaoBotaoFiltroRapido () {
      this.limparFiltros()
      if (this.filtroSelecionado === 2) {
        this.filtrar()
      }

      this.filtroSelecionado = 1
    },

    acaoBotaoFiltroAvancado () {
      if (this.filtroSelecionado !== 2) {
        this.limparFiltros()
      }

      this.filtroSelecionado = 2
    },

    limparFiltros () {
      if (this.filtroSelecionado === 2) {
        this.filtros.data_admissao = ''
        this.filtros.data_demissao = ''
        this.filtros.cargo = null
        this.filtros.tipo_pagamento = []
        this.filtros.consultor = null
        this.filtros.coordenador_pedagogico = null
        this.filtros.instrutor = null
        this.filtros.instrutor_personal = null
        this.filtros.atendente = null
        this.filtros.gestor_comercial = null
        this.filtros.email_usuario = null
      } else {
        this.filtros.apelido = null
      }
    },

    filtrar () {
      this.filtros.apenas_funcionarios_ativos = false
      this.filtros.consultor_ou_gestor_comercial = false
      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    setCargo (value) {
      this.filtros.cargo = value
    },

    setFuncionario (value) {
      this.filtros.funcionario = value
      this.filtrar()
    },

    setFuncionarioAvancado (value) {
      this.filtros.funcionario = value
    },

    setDataAdmissao ({value}) {
      this.filtros.data_admissao = value
    },

    setDataDemissao ({value}) {
      this.filtros.data_demissao = value
    }
  }
}
</script>
