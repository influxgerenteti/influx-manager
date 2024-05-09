<template>
  <div class="animated fadeIn">
    <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="submit()">
      <div v-if="estaEditando" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div v-if="!estaEditando" class="form-loading">
        <load-placeholder :loading="verificaCarregamento(loadCount,3)" />
      </div>

      <div class="body-sector">
        <div class="p-3">
          <b-row class="form-group">
            <b-col md="12">
              <label v-help-hint="'form-livro_descricao'" for="descricao" class="col-form-label">Descrição *</label>
              <input id="descricao" v-model="itemSelecionado.descricao" type="text" class="form-control" maxlength="50" required>
            </b-col>
          </b-row>

          <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'form-livro_item'" for="item" class="col-form-label">Item *</label>
              <g-select
                id="item"
                :class="!isValid && !itemSelecionado.item ? 'invalid-input' : 'valid-input'"
                :value="itemSelecionado.item"
                :select="setItem"
                :options="listaItens"
                class="multiselect-truncate"
                label="descricao"
                required
                track-by="id" />
              <div v-if="!isValid && !itemSelecionado.item" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <!-- <b-col md="3">
              <label v-help-hint="'form-livro_sistema_avaliacao'" for="sistema_avaliacao" class="col-form-label">Sistema de Avaliação *</label>
              <g-select
                id="sistema_avaliacao"1 782 919 629
                :class="!isValid && !itemSelecionado.sistema_avaliacao ? 'invalid-input' : 'valid-input'"
                :value="itemSelecionado.sistema_avaliacao"
                :select="setSistemaAvaliacao"
                :options="listaSistemasAvaliacao"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
              <div v-if="!isValid && !itemSelecionado.sistema_avaliacao" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col> -->

            <b-col md="3">
              <label v-help-hint="'form-livro_planejamento_licao'" required for="planejamento_licao" class="col-form-label">Programação das Lições *</label>
              <g-select
                id="planejamento_licao"
                :class="!isValid && !itemSelecionado.planejamento_licao ? 'invalid-input' : 'valid-input'"
                :value="itemSelecionado.planejamento_licao"
                :select="setPlanejamentoLicao"
                :options="listaPlanejamentosLicaos"
                class="multiselect-truncate"
                label="descricao"
                required
                track-by="id" />
              <div v-if="!isValid && !itemSelecionado.planejamento_licao" class="multiselect-invalid">
                Selecione uma opção!
              </div>
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-livro_numero_aulas'" for="numero_aulas" class="col-form-label">Número de Aulas</label>
              <input id="numero_aulas" :value="numeroAulas" type="text" class="form-control" disabled >
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-livro_proximo_livro'" for="proximo_livro" class="col-form-label">Próximo Livro</label>
              <g-select
                id="proximo_livro"
                :value="itemSelecionado.proximo_livro"
                :select="setProximoLivro"
                :options="listaProximoLivro"
                class="multiselect-truncate"
                label="descricao"
                track-by="id" />
            </b-col>
          </b-row>

          <!-- <b-row class="form-group">
            <b-col md="3">
              <label v-help-hint="'form-livro_idade_minima'" for="idade_minima" class="col-form-label">Idade Mínima *</label>
              <input id="idade_minima" v-model.number="itemSelecionado.idade_minima" type="text" class="form-control" maxlength="9" required @input="validaNumero('idade_minima')">
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-livro_maximo_alunos'" for="maximo_alunos" class="col-form-label">Máximo de Alunos *</label>
              <input id="maximo_alunos" v-model.number="itemSelecionado.maximo_alunos" type="text" class="form-control" maxlength="9" required @input="validaNumero('maximo_alunos')">
            </b-col>

            <b-col md="3">
              <label v-help-hint="'form-livro_numero_aulas'" for="numero_aulas" class="col-form-label">Número de Aulas *</label>
              <input id="numero_aulas" v-model.number="itemSelecionado.numero_aulas" type="text" class="form-control" maxlength="9" required @input="validaNumero('numero_aulas')">
            </b-col>
          </b-row> -->
        </div>

        <div class="content-sector sector-secondary p-3">
          <div class="row">
            <div class="col-md-4">
              <h5 v-help-hint="'form-livro_cursos'" class="title-module mb-2">Cursos</h5>
              <b-form-group label="Selecione os cursos aos quais este livro pertence *">
                <b-form-checkbox-group v-model="cursosSelecionados" stacked name="cursos" required>
                  <template v-for="curso in listaCursos">
                    <b-form-checkbox :key="curso.id" :value="curso.id">{{ curso.descricao }}</b-form-checkbox>
                  </template>
                </b-form-checkbox-group>
              </b-form-group>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-md-12">
          <button :disabled="estaSalvando" type="submit" class="btn btn-verde">{{ estaSalvando ? 'Salvando...' : 'Salvar' }}</button>
          <router-link to="/configuracoes/livro" class="btn btn-link">Cancelar</router-link>
        </div>
      </div>

    </form>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {required} from 'vuelidate/lib/validators'

export default {
  name: 'LivroFormulario',
  data () {
    return {
      loadCount: 0,
      numeroAulas: 0,
      isValid: true,
      estaEditando: false,
      estaSalvando: false,
      cursosSelecionados: []
    }
  },

  computed: {
    ...mapState('livro', ['lista', 'itemSelecionado', 'estaCarregando']),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('item', {listaItensState: 'lista'}),
    ...mapState('sistemaAvaliacao', {listaSistemasAvaliacao: 'lista'}),
    ...mapState('planejamentoLicao', {listaPlanejamentosLicao: 'lista'}),

    listaItens: {
      get () {
        return [...this.listaItensState.filter(item => item.tipo_item.tipo === 'P')]
      }
    },

    listaProximoLivro: {
      get () {
        const listaProximosLivros = this.lista.filter(livro => {
          // Se já está selecionado como proximo livro, continua aparecendo na lista

          if (this.itemSelecionado && this.itemSelecionado.proximo_livro && parseInt(this.itemSelecionado.proximo_livro.id) === parseInt(livro.id)) {
            return true
          }
          // Se for o livro que está sendo atualizado, não aparece na lista
          const idLivroSendoAtualizado = this.$route.params.id
          if (idLivroSendoAtualizado && parseInt(idLivroSendoAtualizado) === parseInt(livro.id)) {
            return false
          }
          // Se o livro está inativo, não aparece na lista
          if (livro.situacao === 'I') {
            return false
          }
          return true
        })

        return [{id: null, descricao: 'Selecione'}, ...listaProximosLivros]
      }
    },

    listaPlanejamentosLicaos: {
      get () {
        return this.listaPlanejamentosLicao.filter(item => (item.situacao === 'A'))
      }
    }

  },

  watch: {
    itemSelecionado (value) {
      if (value.curso !== null && typeof value.curso === 'object' && value.curso.length) {
        this.cursosSelecionados = value.curso.map(curso => curso.id)
      } else {
        this.cursosSelecionados = []
      }
    }
  },

  mounted () {
    this.$store.commit('item/SET_PAGINA_ATUAL', 1)
    this.$store.commit('sistemaAvaliacao/SET_PAGINA_ATUAL', 1)
    this.$store.commit('planejamentoLicao/SET_PAGINA_ATUAL', 1)

    if (this.$store.state.curso.estaCarregando === false) {
      this.$store.commit('curso/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('curso/listar')
    }

    if (this.$store.state.livro.estaCarregando === false) {
      this.$store.commit('livro/SET_PAGINA_ATUAL', 1)
      this.$store.dispatch('livro/listar')
    }

    this.$store.dispatch('item/getLista').then(this.countCarregamento)
    this.$store.dispatch('sistemaAvaliacao/listar').then(this.countCarregamento)
    this.$store.dispatch('planejamentoLicao/listar').then(this.countCarregamento)

    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.estaEditando = true
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
        .then(response => {
          this.itemSelecionado.numero_aulas = this.itemSelecionado.planejamento_licao.licaos.length
          this.numeroAulas = this.itemSelecionado.numero_aulas
        })
    }
  },

  validations: {
    itemSelecionado: {
      descricao: {required},
      numero_aulas: {required},
      item: {required},
      planejamento_licao: {required}
    },
    cursosSelecionados: {required}
  },

  methods: {
    ...mapMutations('livro', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('livro', ['buscar', 'criar', 'atualizar']),

    validaNumero (campo) {
      if (isNaN(parseFloat(this.itemSelecionado[campo])) === true) {
        this.itemSelecionado[campo] = this.itemSelecionado[campo].replace(/[^\d.]/g, '')
      }
    },

    validate () {
      return !!this.itemSelecionado.descricao &&
        !!this.itemSelecionado.numero_aulas &&
        !!this.itemSelecionado.item &&
        !!this.itemSelecionado.planejamento_licao &&
        !!this.cursosSelecionados.length
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.$router.push('/configuracoes/livro')
    },

    submit () {
      this.estaSalvando = true
      this.isValid = false

      if (!this.validate()) {
        this.estaSalvando = false
        return
      }

      this.itemSelecionado.curso = this.cursosSelecionados

      if (this.estaEditando) {
        this.atualizar()
          .then(() => {
            this.voltar()
          })
          .catch(console.error)
          .finally(() => {
            this.estaSalvando = false
          })
      } else {
        this.criar()
          .then(() => {
            this.voltar()
          })
          .catch(console.error)
          .finally(() => {
            this.estaSalvando = false
          })
      }
    },

    setItem (value) {
      this.itemSelecionado.item = value
    },

    setSistemaAvaliacao (value) {
      this.itemSelecionado.sistema_avaliacao = value
    },

    setPlanejamentoLicao (value) {
      this.itemSelecionado.planejamento_licao = value
      this.itemSelecionado.numero_aulas = value.licaos.length
      this.numeroAulas = this.itemSelecionado.numero_aulas
    },

    setProximoLivro (value) {
      this.itemSelecionado.proximo_livro = value
    },

    countCarregamento () {
      this.loadCount++
    },

    verificaCarregamento (numeroDeRequisicoesFeitas, requisicoes) {
      if (numeroDeRequisicoesFeitas !== requisicoes) {
        return true
      } else {
        return false
      }
    }
  }
}
</script>
