<template>
  <div class="animated fadeIn">
    <div class="form-loading">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div v-if="itemSelecionado" class="body-sector p-3">
      <b-row class="form-group">
        <b-col md="3">
          <label>Descrição</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Item</label>
          <span v-if="itemSelecionado.item" class="d-block pt-2 text-muted">{{ itemSelecionado.item.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Curso</label>
          <span v-if="itemSelecionado.curso" class="d-block pt-2 text-muted">{{ itemSelecionado.curso.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Sistema de Avaliação</label>
          <span v-if="itemSelecionado.sistema_avaliacao" class="d-block pt-2 text-muted">{{ itemSelecionado.sistema_avaliacao.descricao }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Planejamento de Lição</label>
          <span v-if="itemSelecionado.planejamento_licao" class="d-block pt-2 text-muted">{{ itemSelecionado.planejamento_licao.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Próximo Livro</label>
          <span v-if="itemSelecionado.proximo_livro" class="d-block pt-2 text-muted">{{ itemSelecionado.proximo_livro.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Idade Mínima</label>
          <span v-if="itemSelecionado.idade_minima" class="d-block pt-2 text-muted">{{ itemSelecionado.idade_minima }}</span>
        </b-col>

        <b-col md="3">
          <label>Máximo de Alunos</label>
          <span v-if="itemSelecionado.maximo_alunos" class="d-block pt-2 text-muted">{{ itemSelecionado.maximo_alunos }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Número de Aulas</label>
          <span v-if="itemSelecionado.numero_aulas" class="d-block pt-2 text-muted">{{ itemSelecionado.numero_aulas }}</span>
        </b-col>

        <b-col md="3">
          <label>Situação</label>
          <div class="d-block">
            <span v-if="situacoes[itemSelecionado.situacao]" :class="`text-${situacoes[itemSelecionado.situacao].cor}`" class="badge border-badge align-middle rounded">{{ situacoes[itemSelecionado.situacao].descricao }}</span>
          </div>
        </b-col>
      </b-row>

      <div>
        <router-link :to="`/configuracoes/livro/atualizar/${itemSelecionadoID}`" class="btn btn-roxo">
          <font-awesome-icon icon="pen" /> Atualizar
        </router-link>
        <router-link to="/configuracoes/livro" class="btn btn-link">Voltar</router-link>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'

export default {
  name: 'LivroInfo',

  data () {
    return {
      situacoes: {
        A: {descricao: 'Ativo', cor: 'success', icone: 'check-square', titulo: 'Desativar'},
        I: {descricao: 'Inativo', cor: 'danger', icone: 'square', titulo: 'Ativar'}
      }
    }
  },

  computed: mapState('livro', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando']),

  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },

  methods: {
    ...mapMutations('livro', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('livro', ['buscar'])
  }
}
</script>
