<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado">
      <!-- <div class="form-group row">
        <div class="col-md-6">
          <label v-help-hint="'filtroRapido-aluno_nome_aluno'" for="nome_aluno" class="col-form-label">Aluno</label>
          <typeahead id="nome_aluno" :item-hit="setAluno" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato" />
        </div>
      </div> -->

      <div class="form-group row">

        <!-- <div class="col-md-3">
          <label v-help-hint="'filtro-turma_curso'" for="curso" class="col-form-label">Curso</label>
          <g-select
            id="curso"
            v-model="filtros.curso"
            :options="listaCursos"
            class="multiselect-truncate"
            label="descricao"
            track-by="id" />
        </div> -->

        <!-- <div class="col-md-3">
          <label v-help-hint="'filtro-turma_modalidade_turma'" for="modalidade_turma" class="col-form-label">Modalidade</label>
          <g-select
            id="modalidade_turma"
            v-model="filtros.modalidade_turma"
            :select="onChangeModalidadeTurma"
            :options="modalidadesTurmaSelect"
            class="multiselect-truncate"
            label="descricao"
            track-by="id" />
        </div> -->

        <!-- <b-col md="4">
          <label v-help-hint="'form-contrato_turma'" for="turma" class="col-form-label">Turma</label>
          <div class="d-flex">
            <g-select
              id="turma"
              v-model="filtros.turma"
              :options="listaTurmas"
              class="flex-grow-1"
              label="turmaDescricao"
              track-by="turmaId"/>
          </div>
        </b-col> -->

        <div class="col-md-auto">
          <label for="situacao_rapido" class="col-form-label d-block">Situação do alunos</label>
          <b-form-checkbox-group id="situacao_rapido" v-model="filtros.situacao" :options="situacoes" buttons button-variant="cinza" name="situacao_rapido" class="checkbtn-line fill-width"/>
        </div>


      </div>
   

      <b-btn class="btn btn-cinza btn-block text-uppercase" @click="abrirRelatorio()">Gerar relatório</b-btn>
    </div>
  </div>
</template>

<script>

import {mapState
// , mapActions
} from 'vuex'
import open from '../../utils/open'

export default {
  name: 'ListaRelatorioAlunosSituacao',

  data () {
    return {
      optionsChecked: [
        { item: 'notas', name: 'Notas' },
        { item: 'presencas', name: 'Presenças' }
      ],
      alunos: [
          { value: null, text: 'Please select an option' },
          { value: 'a', text: 'This is First option' },
          { value: 'b', text: 'Selected Option' },
          { value: { C: '3PO' }, text: 'This is an option with object value' },
          { value: 'd', text: 'This one is disabled', disabled: true }

      ],
      situacoes: [
        {text: 'Ativo', value: 'ATI'},
        {text: 'Inativo', value: 'INA'},
        {text: 'Trancado', value: 'TRA'}
      ]
    }
  },

  computed: {
    ...mapState('relatorioAlunos', ['filtros']),
    ...mapState('curso', {listaCursos: 'lista'}),
    ...mapState('modalidadeTurma', {listaModalidadesTurma: 'lista'}),
    ...mapState('turma', {listaTurmas: 'lista'}),

    modalidadesTurmaSelect: {
      get () {
        const modalidades = this.listaModalidadesTurma ? this.listaModalidadesTurma : []
        modalidades.unshift({descricao: 'Selecione', id: null})
        return modalidades
      }
    }
  },

  mounted () {
    // this.listarTodosCursos(true)
    // this.listarModalidadesTurma()
    // this.listarTodasTurmas()
  },

  methods: {
    // ...mapActions('curso', {listarTodosCursos: 'listar'}),
    // ...mapActions('modalidadeTurma', {listarModalidadesTurma: 'listar'}),
    // ...mapActions('turma', {listarTodasTurmas: 'listarTodos'}),

    // setAluno (value) {
    //   this.filtros.aluno = value
    // },

    // onChangeModalidadeTurma (value) {
    //   const data = value.id ? {modalidade_turma: value.id} : {}
    //   this.listarTodasTurmas(data)
    // },

    abrirRelatorio () {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const rota = this.$route.matched[0].path
      const filtrosRelatorio = this.converterDadosParaLink()
      const urlRelatorio = `/api/relatorios/situacao_aluno?franqueada=${franqueada}&Authorization=${auth}&URLModulo=${rota}${filtrosRelatorio}`
      open(urlRelatorio, '_blank')
    },

    converterDadosParaLink () {
      const form = {...this.filtros}

      const dados = {
        // aluno: form.aluno ? form.aluno.id : null,
        situacao: form.situacao.length > 0 ? form.situacao : null
        // modalidade_turma: form.modalidade_turma ? form.modalidade_turma.id : null,
        // curso: form.curso ? form.curso.id : null,
        // turma: form.turma ? form.turma.turmaId : null
      }

      let dadosArray = []
      for (let key in dados) {
        if (dados[key] !== null) {
          if (dados[key] instanceof Array) {
            dados[key].forEach(element => {
              dadosArray.push(`${key}[]=${element}`)
            })
          } else {
            dadosArray.push(`${key}=${dados[key]}`)
          }
        }
      }
      let retorno = dadosArray.length > 0 ? '&' : ''
      retorno += dadosArray.join('&')
      return retorno
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

.filtro-avancado .input-group-text {
  border: 0;
  background-color: #E5E5E5;
}
</style>
