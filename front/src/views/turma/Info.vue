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
          <label>Modalidade</label>
          <span v-if="itemSelecionado.modalidade_turma" class="d-block pt-2 text-muted">{{ itemSelecionado.modalidade_turma.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Curso</label>
          <span v-if="itemSelecionado.curso" class="d-block pt-2 text-muted">{{ itemSelecionado.curso.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Intensidade</label>
          <span class="d-block pt-2 text-muted">{{ intensidades[itemSelecionado.intensidade] }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Livro</label>
          <span v-if="itemSelecionado.livro" class="d-block pt-2 text-muted">{{ itemSelecionado.livro.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Sala</label>
          <span v-if="itemSelecionado.sala_franqueada" class="d-block pt-2 text-muted">{{ itemSelecionado.sala_franqueada.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Instrutor</label>
          <span v-if="itemSelecionado.funcionario" class="d-block pt-2 text-muted">{{ itemSelecionado.funcionario.apelido }}</span>
        </b-col>

        <b-col md="3">
          <label>Tipo de Pagamento</label>
          <span v-if="itemSelecionado.valor_hora_linhas" class="d-block pt-2 text-muted">{{ itemSelecionado.valor_hora_linhas.descricao }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Calendário</label>
          <span v-if="itemSelecionado.calendario" class="d-block pt-2 text-muted">{{ itemSelecionado.calendario.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Horário</label>
          <span v-if="itemSelecionado.horario" class="d-block pt-2 text-muted">{{ itemSelecionado.horario.descricao }}</span>
        </b-col>

        <b-col md="3">
          <label>Máximo de Alunos</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.maximo_alunos }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Data de início</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.data_inicio }}</span>
        </b-col>

        <b-col md="3">
          <label>Data de término</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.data_fim }}</span>
        </b-col>

        <b-col md="3">
          <label>Semestre</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.semestre }}</span>
        </b-col>
      </b-row>

      <b-row class="form-group">
        <b-col md="3">
          <label>Situação</label>
          <div class="d-block">
            <span v-if="itemSelecionado.situacao" :class="`text-${situacaoItemSelecionado.cor}`" class="badge border-badge align-middle rounded">{{ situacaoItemSelecionado.descricao }}</span>
          </div>
        </b-col>

        <b-col md="9">
          <label>Observação</label>
          <span class="d-block pt-2 text-muted">{{ itemSelecionado.observacao }}</span>
        </b-col>
      </b-row>

      <div>
        <router-link :to="`/configuracoes/turma/atualizar/${itemSelecionadoID}`" class="btn btn-roxo">
          <font-awesome-icon icon="pen" /> Atualizar
        </router-link>
        <router-link to="/configuracoes/turma" class="btn btn-link">Voltar</router-link>
      </div>
    </div>

  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'

export default {
  name: 'TurmaInfo',

  data () {
    return {
      intensidades: {
        R: 'Regular',
        S: 'Semi-intensivo',
        I: 'Intensivo'
      }
    }
  },

  computed: {
    ...mapState('turma', ['itemSelecionado', 'itemSelecionadoID', 'estaCarregando', 'situacoes']),
    situacaoItemSelecionado: {
      get () {
        return this.situacoes.find(sit => {
          return sit.valor === this.itemSelecionado.situacao
        })
      }
    }
  },

  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()

    const id = this.$route.params.id
    if (id) {
      this.SET_ITEM_SELECIONADO_ID(id)
      this.buscar()
    }
  },

  methods: {
    ...mapMutations('turma', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO']),
    ...mapActions('turma', ['buscar'])
  }
}
</script>
