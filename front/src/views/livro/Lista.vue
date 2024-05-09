<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div class="d-flex justify-content-between filtro-header head-content-sector">
        <div>
          <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-avancados" aria-expanded="true" @click="filtroAvancado = !filtroAvancado, className = filtroAvancado ? 'filtro-open' : null, filtroSelecionado = 1">Filtros</div>
        </div>

        <router-link v-if="permissoes['CRIAR'] && (permissoes['CRIAR'].possui_permissao === true)" :to="`${$route.path}/adicionar`" class="btn btn-azul">
          <font-awesome-icon icon="plus" /> Adicionar
        </router-link>
      </div>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form class="p-3" @submit.prevent="buscaRapida = false, buscaAvancada = true, filtrar()">
          <div class="form-group row">
            <div class="col-md-6">
              <label v-help-hint="'filtro-livro_descricao'" for="descricaoAvancado" class="col-form-label">Descrição</label>
              <input id="descricaoAvancado" v-model="descricaoAvancado" class="form-control">
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-livro_cursoAvancado'" for="cursoAvancado" class="col-form-label">Curso</label>
              <g-select :select="setCursoAvancado"
                        :value="cursoAvancado"
                        :options="listaCursos"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>

            <div class="col-md-3">
              <label v-help-hint="'filtro-livro_idiomaAvancado'" for="idiomaAvancado" class="col-form-label">Idioma</label>
              <g-select :select="setIdiomaAvancado"
                        :value="idiomaAvancado"
                        :options="listaIdiomas"
                        class="multiselect-truncate"
                        label="descricao"
                        track-by="id"
              />
            </div>
          </div>

          <button type="submit" class="btn btn-cinza btn-block text-uppercase" @click="filtroAvancado = false, className = null">Buscar</button>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table :class="className" :sort="sortTable">
        <thead>
          <tr>
            <th data-column="li.descricao">Descrição</th>
            <th data-column="cu.descricao">Curso</th>
            <th data-column="pli.descricao">Programação das Lições</th>
            <th data-column="li.situacao">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody>
          <perfect-scrollbar @ps-y-reach-end="permitirCarregarMais && carregarMais()">
            <div v-if="estaCarregando" class="d-flex h-100">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr v-for="item in lista" :key="item.id" @dblclick="editar(item)">
              <td>{{ item.descricao }}</td>
              <td>{{ listaDescricoes[item.id] }}</td>
              <td>{{ item.planejamento_licao.descricao }}</td>
              <td data-label="Situação">
                <div @click.prevent="desativar(item)">
                  <span v-b-tooltip.viewport.left.hover :class="`text-${situacoes[item.situacao].cor}`" :title="situacoes[item.situacao].titulo" class="align-middle">
                    <font-awesome-icon :icon="situacoes[item.situacao].icone" />
                  </span>
                </div>
              </td>

              <td class="d-flex coluna-icones">
                <!-- <router-link :to="`${$route.path}/visualizar/${item.id}`" class="icone-link" title="Visualizar"> -->
                <!--   <font-awesome-icon icon="eye" /> -->
                <!-- </router-link> -->

                <router-link v-if="permissoes['EDITAR'] && (permissoes['EDITAR'].possui_permissao === true)" :to="`${$route.path}/atualizar/${item.id}`" class="icone-link" title="Atualizar">
                  <font-awesome-icon icon="pen" />
                </router-link>

                <!--
                <a v-if="item.situacao === 'A'" href="javascript:void(0)" title="Desativar" class="icone-link icon-danger" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="ban" />
                </a>
                <a v-else href="javascript:void(0)" title="Ativar" class="icone-link text-success" @click.prevent="inativar(item)">
                  <font-awesome-icon icon="check-circle" />
                </a>
                -->
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
import EventBus from '../../utils/event-bus'

export default {
  name: 'LivroLista',
  data () {
    return {
      buscaRapida: false,
      buscaAvancada: false,
      descricaoAvancado: null,
      cursoAvancado: null,
      idiomaAvancado: null,
      className: 'filtro-open',
      filtroAvancado: true,
      filtroSelecionado: 1,
      listaDescricoes: [],
      situacoes: {
        A: {descricao: 'Ativo', cor: 'success', icone: 'check-square', titulo: 'Desativar'},
        I: {descricao: 'Inativo', cor: 'danger', icone: 'square', titulo: 'Ativar'}
      }
    }
  },

  computed: {
    ...mapState('modulos', ['permissoes']),
    ...mapState('livro', ['lista', 'estaCarregando', 'todosItensCarregados', 'totalItens']),

    listaCursos: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.curso.lista)
      }
    },
    listaIdiomas: {
      get () {
        return [{id: null, descricao: 'Selecione'}].concat(this.$store.state.idioma.lista)
      }
    },
    permitirCarregarMais: {
      get () {
        return !!this.lista.length && !this.estaCarregando && !this.todosItensCarregados
      }
    }
  },
  watch: {
    lista (value) {
      if (this.lista.length > 0) {
        this.lista.forEach(livro => {
          this.listaDescricoes[livro.id] = 'Não possui curso relacionado.'
          if (livro.curso !== undefined) {
            this.listaDescricoes[livro.id] = ''
            for (let i = 0; i < livro.curso.length; i++) {
              if (livro.curso[i + 1] === undefined) {
                this.listaDescricoes[livro.id] += livro.curso[i].descricao + ''
              } else {
                this.listaDescricoes[livro.id] += livro.curso[i].descricao + ', '
              }
            }
          }
        })
      }
    }
  },
  mounted () {
    this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
    this.$store.commit('idioma/SET_PAGINA_ATUAL', 1)

    this.$store.dispatch('curso/listar')
    this.$store.dispatch('idioma/listar')

    this.SET_PAGINA_ATUAL(1)
    this.SET_LISTA([])
    if (this.estaCarregando === false) {
      this.listar()
    }
  },

  methods: {
    ...mapActions('livro', ['listar', 'atualizar']),
    ...mapMutations('livro', ['SET_PAGINA_ATUAL', 'SET_ESTA_CARREGANDO', 'SET_ITEM_SELECIONADO', 'SET_ITEM_SELECIONADO_ID', 'SET_ORDER_BY', 'SET_LISTA']),

    carregarMais () {
      this.listar()
    },

    sortTable (response) {
      this.SET_ORDER_BY(response.detail)
      this.SET_PAGINA_ATUAL(1)
      this.SET_LISTA([])
      this.listar()
    },

    filtrar () {
      this.$store.commit('livro/SET_FILTROS_DESCRICAO', this.descricaoAvancado)
      this.$store.commit('livro/SET_FILTROS_CURSO', this.cursoAvancado ? this.cursoAvancado.id : null)
      this.$store.commit('livro/SET_FILTROS_IDIOMA', this.idiomaAvancado ? this.idiomaAvancado.id : null)

      this.SET_PAGINA_ATUAL(1)
      this.listar()
    },

    setCursoAvancado (value) {
      this.cursoAvancado = value
    },

    setIdiomaAvancado (value) {
      this.idiomaAvancado = value
    },

    editar (item) {
      if (this.permissoes['EDITAR'] && (this.permissoes['EDITAR'].possui_permissao === true)) {
        if (item.situacao !== 'A') {
          return
        }
        this.$router.push(`/configuracoes/livro/atualizar/${item.id}`)
      }
    },

    desativar (item) {
      const data = Object.assign({}, item)
      const mensagem = data.situacao === 'A' ? 'desativar' : 'ativar'
      EventBus.$emit('chamarModal', {
        resolve: success => {
          data.situacao = data.situacao === 'A' ? 'I' : 'A'
          delete data.curso
          this.$store.commit('livro/SET_ITEM_SELECIONADO_ID', data.id)
          this.$store.commit('livro/SET_ITEM_SELECIONADO', data)

          this.atualizar()
            .then(() => {
              item.situacao = data.situacao
            })
            .catch(error => {
              console.error(error)
            })
        }
      }, `Deseja ${mensagem} ${data.descricao}?`)
    }

  }
}
</script>

<style scoped>
#filtros-rapidos,
#filtros-avancados {
  transition: all .1s;
}

.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151B1E;
  background-color: #fff;
}
</style>
