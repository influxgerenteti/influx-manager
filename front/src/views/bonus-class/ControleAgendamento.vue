<template>
  <div>
      <form :class="{ 'was-validated': !isValid }" class="needs-validation" novalidate @submit.prevent="salvar()">
        <div v-if="isEdit || estaCarregando" class="form-loading">
          <load-placeholder :loading="estaCarregando" />
        </div>

        <div class="form-group row">
          <h5>Controle de Bônus Class</h5>
        </div>

        <div class="form-group row">
          <b-col md="4">
            <label for="formulario_controle_agendamento_data" class="col-form-label">Data</label>
            <input id="formularo_controle_agendamento_data" v-model="data" type="text" class="form-control" readonly>
            <!-- <v-date-picker
              v-model=""
              :input-props="{ id: 'formulario_controle_agendamento_data', class: 'form-control', placeholder: 'Data', readonly: true, autocomplete: 'off' }"
            /> -->
          </b-col>

          <b-col md="4">
            <label for="formularo_controle_agendamento_instrutor" class="col-form-label">Instrutor</label>
            <input id="formularo_controle_agendamento_instrutor" v-model="nomeInstrutor" type="text" class="form-control" readonly>
          </b-col>

          <b-col md="4">
            <label for="formularo_controle_agendamento_sala" class="col-form-label">Sala</label>
            <input id="formularo_controle_agendamento_sala" v-model="nomeSala" type="text" class="form-control" readonly>
          </b-col>
        </div>

        <div class="form-group row p-2 min-heigth-400">
          <template v-if="reading">
            <div class="table-responsive-sm">
              <g-table>
                <thead class="text-dark">
                  <tr>
                    <th data-column="" class="size-75">Horário</th>
                    <th data-column="">Nome aluno</th>
                    <th data-column="">Conteúdo</th>
                    <th data-column="" class="">Frequência</th>
                  </tr>
                </thead>

                <tbody ref="scroll-wrap">
                  <perfect-scrollbar>
                    <div v-if="!listaDeAlunos.length" class="busca-vazia">
                      <p>Nenhum resultado encontrado.</p>
                    </div>

                    <tr v-for="item in listaDeAlunos" :key="item.id">
                      <template v-if="item.selecionado">
                        <td data-label="Horário" class="size-75">{{ item.horario_aula ? item.horario_aula : '' | formatarHoraDB }}</td>
                        <td data-label="Nome aluno">{{ item.aluno ? item.aluno.pessoa.nome_contato : '' }}</td>
                        <td data-label="Conteúdo">{{ item.conteudo ? item.conteudo : '' }}</td>
                        <td data-label="Presença" class="">{{ item.presenca }}</td>
                      </template>
                      <template v-else>
                        <td data-label="Horário" class="">{{ item.horario_aula ? item.horario_aula : '' | formatarHoraDB }} Lista de espera</td>
                        <td data-label="Nome aluno">{{ item.aluno ? item.aluno.pessoa.nome_contato : '' }}</td>
                        <td data-label="Conteúdo">{{ item.conteudo ? item.conteudo : '' }}</td>
                      </template>
                      <!-- <template>
                        <template v-if="item.selecionado">
                          <td data-label="Horário" class="size-75">{{ item.horario_aula ? item.horario_aula : '' | formatarHoraDB }}</td>
                        </template>
                        <template v-else>
                          <td data-label="Horário" class="size-75">{{ item.horario_aula ? 'Lista de espera' : ''}}</td>
                        </template>
                      </template>
                      <td data-label="Nome aluno">{{ item.aluno ? item.aluno.pessoa.nome_contato : '' }}</td>
                      <td data-label="Conteúdo">{{ item.conteudo ? item.conteudo : '' }}</td>
                      <template v-if="item.selecionado">
                        <td data-label="Presença" class="">{{ item.presenca }}</td>
                      </template>
                      <template v-else>
                        -
                      </template> -->
                    </tr>
                  </perfect-scrollbar>
                </tbody>
              </g-table>
            </div>
          </template>
          <template v-else>
            <perfect-scrollbar class="scroller h-size-400 overflow-hidden vw-100">
              <b-row v-for="(item, index) in listaDeHoras" :key="index" class="mx-2 p-1">
                <template v-if="item.selecionado">
                  <div data-header="Horário" class="">{{ item.horario }}</div>
                  <template v-if="item.aluno">
                    <div data-header="Nome do Aluno" class="col-md-4">
                      <input :id="`nome_aluno_${index}`" v-model="item.nome_aluno" type="text" class="form-control" readonly>
                    </div>
                  </template>
                  <template v-else>
                    <div data-header="Nome do Aluno" class="col-md-4">
                      <typeahead :id="`nome_aluno_${index}`" :ref="`typeahead_nome_aluno_${index}`" :item-hit="setNomeAluno" :extra-param="[index]" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato"/>
                    </div>
                  </template>
                  <div data-header="Conteúdo" class="col-md-4">
                    <input :id="`descricao_${index}`" v-model="item.descricao" :readonly="!item.selecionado" type="text" class="form-control" placeholder="Conteúdo">
                  </div>
                  <div data-header="Presença">
                    <b-form-radio-group :id="`presenca-aluno-${index}`" v-model="item.presenca" :name="`presenca_${index}`" class="check-presenca">
                      <b-form-radio value="P">P</b-form-radio>
                      <b-form-radio value="F">F</b-form-radio>
                    </b-form-radio-group>
                  </div>
                  <div class="d-flex coluna-icones">
                    <a v-b-tooltip.viewport.left.hover v-if="(!removendo && (item.id || item.deletarEmTela))" class="icone-link ml-2" title="Remover" @click.stop="removerAlunoDaLista(item, index)">
                      <font-awesome-icon icon="trash" />
                    </a>
                  </div>
                  <template v-if="item.aluno">
                    <b-btn variant="azul" class="btn-40 ml-2" @click="adicionarAlunoListaDeEspera(item, index)">
                      <font-awesome-icon icon="plus" />
                    </b-btn>
                  </template>
                </template>
                <template v-if="!item.selecionado" class="aluno-espera">
                  <div data-header="Horário" class="">{{ item.horario }} - Lista de espera</div>
                  <template v-if="item.aluno">
                    <div data-header="Nome do Aluno" class="col-md-4">
                      <input :id="`nome_aluno_${index}`" v-model="item.nome_aluno" type="text" class="form-control" readonly>
                    </div>
                  </template>
                  <template v-else>
                    <div data-header="Nome do Aluno" class="col-md-4">
                      <typeahead :id="`nome_aluno_${index}`" :ref="`typeahead_nome_aluno_${index}`" :item-hit="setNomeAluno" :extra-param="[index]" source-path="/api/aluno/buscar-nome" key-name="pessoa.nome_contato"/>
                    </div>
                  </template>
                  <div data-header="Conteúdo" class="col-md-4">
                    <input :id="`descricao_${index}`" v-model="item.descricao" type="text" class="form-control" placeholder="Conteúdo">
                  </div>
                  <div class="d-flex coluna-icones ml-2">
                    <a v-b-tooltip.viewport.left.hover v-if="(!removendo && (item.id || item.deletarEmTela))" class="icone-link ml-2" title="Remover" @click.stop="removerAlunoDaLista(item, index)">
                      <font-awesome-icon icon="trash" />
                    </a>
                  </div>
                  <template v-if="item.aluno">
                    <b-btn variant="light" class="btn-40 ml-2" @click="adicionarAlunoOuHorario(item, index)">
                      <font-awesome-icon icon="chevron-up" />
                    </b-btn>
                  </template>
                </template>
              </b-row>
            </perfect-scrollbar>
          </template>

        </div>
        <div class="form-group pt-2">
          <b-btn v-if="!reading" type="submit" variant="verde">Salvar</b-btn>
          <b-btn v-if="!reading" type="button" variant="verde" @click="salvar(true)">Salvar e sair</b-btn>
          <!-- <b-btn v-if="!reading" type="button" variant="azul" @click="abrirMondalConcluir()">Concluir</b-btn> -->
          <b-btn variant="link" @click="voltar()">Sair</b-btn>
        </div>
      </form>


    <!-- Modal de concluir -->
    <b-modal id="modalConcluir" ref="modalConcluir" v-model="visibilidadeModalConcluir" size="sm" title="Concluir de bônus class" hide-footer no-close-on-backdrop>
      <div class="d-block text-center">
        <p>Bônus class foi realmente concluído?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn type="button" variant="primary" @click="visibilidadeModalConcluir = false, concluido = true, salvar(true)">Concluir</b-btn>
        <button type="button" class="btn btn-link" @click="visibilidadeModalConcluir = false,visibilidade_controle_agendamento = true">Cancelar</button>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {mapState, mapMutations, mapActions} from 'vuex'
import {stringToISODate, converteHorarioParaBanco, dateToString} from '../../utils/date'
import EventBus from '../../utils/event-bus'
import moment from 'moment'

export default {
  name: 'ControleAgendamento',
  data () {
    return {
      isValid: true,
      isEdit: false,
      reading: false,
      removendo: false,
      salvando: false,

      data: '',

      listaDeHoras: [],
      visibilidade_controle_agendamento: false,
      visibilidadeModalConcluir: false,
      concluido: false,

      listaDeAlunos: []

    }
  },
  computed: {
    ...mapState('bonusClass', {listaDeBonus: 'lista', itemSelecionado: 'itemSelecionado', itemSelecionadoID: 'itemSelecionadoID', estaCarregando: 'estaCarregando'}),

    nomeInstrutor: {
      get () {
        return this.itemSelecionado.funcionario ? this.itemSelecionado.funcionario.apelido : ''
      }
    },

    nomeSala: {
      get () {
        return this.itemSelecionado.sala_franqueada ? this.itemSelecionado.sala_franqueada.descricao : ''
      }
    }
  },
  mounted () {
    this.LIMPAR_ITEM_SELECIONADO()
    this.$emit('modalControleAgendamentoReady')
    console.log("call modalControleAgendamentoReady")
  },

  methods: {
    ...mapMutations('bonusClass', ['SET_ITEM_SELECIONADO_ID', 'LIMPAR_ITEM_SELECIONADO', 'SET_ESTA_CARREGANDO', 'SET_CONCLUIDO']),
    ...mapActions('bonusClass', ['buscar', 'atualizarDados']),
    ...mapActions('alunosBonusClass', {excluirAlunoBonusClass: 'excluir'}),

    stringToISODate: stringToISODate,
    dateToString: dateToString,

    abrirModalConcluir () {
      this.visibilidadeModalConcluir = true
    },

    montarListaDeHoras (listaAlunosBonusClasses) {
      this.listaDeHoras = []
      this.listaDeAlunos = this.itemSelecionado.alunosBonusClasses ? this.itemSelecionado.alunosBonusClasses : []
      if (listaAlunosBonusClasses) {
        this.listaDeAlunos = listaAlunosBonusClasses
      }

      this.listaDeAlunos.sort((a, b) => a.id - b.id)

      const horarioInicio = 7
      const horarioFinal = 22

      const primeiroHorarioSalvo = converteHorarioParaBanco(this.itemSelecionado.horario_inicio, undefined, true)
      const primeiraHoraPossivelAgendamento = parseInt(moment(primeiroHorarioSalvo).format('HH'))
      const primeiroMinutoPossivelAgendamento = parseInt(moment(primeiroHorarioSalvo).format('mm'))

      const ultimoHorarioSalvo = converteHorarioParaBanco(this.itemSelecionado.horario_termino, undefined, true)
      const ultimaHoraPossivelAgendamento = moment(ultimoHorarioSalvo).subtract(30, 'minutes').format('HH:mm')

      for (let hora = primeiraHoraPossivelAgendamento; hora <= horarioFinal; hora++) {
        let horario = hora >= 10 ? '' : '0'
        horario += hora

        if (hora >= horarioInicio) {
          let horarioAgendamento = horario + ':00'
          if (hora !== primeiraHoraPossivelAgendamento || primeiroMinutoPossivelAgendamento === 0) {
            this.montarLinhaDeHorario(horarioAgendamento)
            if (horarioAgendamento === ultimaHoraPossivelAgendamento) {
              break
            }
          }
        }

        if (hora !== horarioFinal) {
          let horarioAgendamento = horario + ':30'
          this.montarLinhaDeHorario(horarioAgendamento)
          if (horarioAgendamento === ultimaHoraPossivelAgendamento) {
            break
          }
        }
      }

      return this.listaDeHoras
    },

    montarLinhaDeHorario (horario) {
      let listaDeAlunosEListaDeEspera = this.buscarAlunosEListaDeEsperaPorHorario(horario)
      listaDeAlunosEListaDeEspera.sort((a, b) => {
        if ((a.selecionado && !b.selecionado) && a.id < b.id) {
          return 1
        }

        if ((a.selecionado && !b.selecionado) && a.id > b.id) {
          return -1
        }

        return 0
      })
      listaDeAlunosEListaDeEspera.forEach((aluno) => {
        const linhaDeHorarioAgendamento = this.factoryObjectLine(aluno.selecionado === true ? 1 : 0)
        linhaDeHorarioAgendamento.horario = horario
        linhaDeHorarioAgendamento.disponivel = true

        this.transformaEmObjetoAgendado(linhaDeHorarioAgendamento, aluno)
        this.listaDeHoras.push(linhaDeHorarioAgendamento)
      })
    },

    voltar () {
      this.LIMPAR_ITEM_SELECIONADO()
      this.resetData()
      this.$emit('cancelar')
    },

    resetData () {
      this.reading = false
      this.isEdit = false
      this.removendo = false
      this.listaDeHoras = []
      this.visibilidadeModalConcluir = false
      this.concluido = false
    },

    openEdit (id) {
      if (id) {
        this.isEdit = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar().then(() => {
          this.data = moment(this.itemSelecionado.data_aula).format('DD/MM/YYYY')
          this.montarListaDeHoras()
        })
      }
    },

    openView (id) {
      if (id) {
        this.reading = true
        this.SET_ITEM_SELECIONADO_ID(id)
        this.buscar().then(() => {
          this.listaDeAlunos = this.montarListaDeHoras()
          this.listaDeAlunos = this.listaDeAlunos.filter(aluno => aluno.id)
          this.data = moment(this.itemSelecionado.data_aula).format('DD/MM/YYYY')
        })
      }
    },

    setNomeAluno (value, index) {
      let alunoObj = this.factoryObjectStudent(value)
      let id = alunoObj ? alunoObj.id : null

      if (id) {
        this.setAluno(index, alunoObj)
        this.setLivro(index, alunoObj.livro)
        this.setInstrutor(index, alunoObj.funcionario)
      }
    },

    deletar (item, index) {
      const id = item.id
      const deletarEmTela = item.deletarEmTela

      if (id) {
        this.SET_ESTA_CARREGANDO(true)
        this.excluirAlunoBonusClass(id).then(() => {
          this.buscar().then(() => {
            this.montarListaDeHoras()
          })
        }).catch(() => {

        })
      } else if (deletarEmTela) {
        this.listaDeHoras[index].aluno = null
        this.listaDeHoras[index].nome_aluno = ''
        this.listaDeHoras[index].descricao = ''
        this.listaDeHoras[index].presenca = 'P'

        this.listaDeHoras[index].deletarEmTela = false
      } else {
        EventBus.$emit('criarAlerta', {
          tipo: 'A',
          mensagem: 'Não existe dado para ser removido'
        })
      }
    },

    removerAlunoDaLista (item, index) {
      this.deletar(item, index)
    },

    verificarSeAlunoJaExiste (id) {
      const obj = this.listaDeHoras.find((objLinha) => {
        if (objLinha.aluno) {
          if (objLinha.aluno.id) {
            return objLinha.aluno.id === id
          }
        }
      })

      if (obj) {
        return true
      } else {
        return false
      }
    },

    verificarHorarioInstrutorDisponivel (horarioCorrente, hInicio, hTermino) {
      return ((horarioCorrente >= hInicio) && (horarioCorrente <= hTermino))
    },

    factoryObjectLine (selecionado = 1) {
      const obj = {
        id: null,
        aluno: null,
        nome_aluno: null,
        descricao: null,
        presenca: 'P',
        horario_aula: '',
        horario: '',
        livro: null,
        funcionario: null,
        disponivel: false,
        selecionado: selecionado
      }
      return obj
    },

    setAluno (index, aluno) {
      this.listaDeHoras[index].deletarEmTela = true

      this.listaDeHoras[index].aluno = aluno
      this.listaDeHoras[index].nome_aluno = aluno.nome
    },
    setLivro (index, livro) {
      this.listaDeHoras[index].livro = livro
    },

    setInstrutor (index, funcionario) {
      this.listaDeHoras[index].funcionario = funcionario
    },

    factoryObjectStudent (value) {
      let obj = null
      if (value) {
        obj = {}
        obj.id = value.id
        obj.nome = value.pessoa.nome_contato
      }

      return obj
    },

    transformaEmObjetoAgendado (objLinha, alunoBonusClass = null) {
      alunoBonusClass = alunoBonusClass === null ? this.buscarAlunoBonusClass(objLinha.horario) : alunoBonusClass

      objLinha.id = alunoBonusClass.id || null
      objLinha.horario_aula = alunoBonusClass.horario_aula

      objLinha.aluno = alunoBonusClass.aluno ? alunoBonusClass.aluno : null
      objLinha.descricao = alunoBonusClass.conteudo ? alunoBonusClass.conteudo : null
      objLinha.presenca = alunoBonusClass.presenca ? alunoBonusClass.presenca : null
      objLinha.selecionado = alunoBonusClass.selecionado ? alunoBonusClass.selecionado : null

      objLinha.nome_aluno = objLinha.aluno ? objLinha.aluno.pessoa ? objLinha.aluno.pessoa.nome_contato : null : null
    },

    salvar (bSalvarESair) {
      this.SET_CONCLUIDO(this.concluido)
      let retorno = this.retornaDadosLinhasBackEnd()
      this.itemSelecionado.data_aula = moment(this.itemSelecionado.data_aula).format('DD/MM/YYYY')

      this.atualizarDados(retorno)
        .then(() => {
          if (bSalvarESair || this.concluido) {
            this.voltar()
          } else {
            this.buscar().then(() => {
              this.montarListaDeHoras()
            })
          }
        })
    },

    retornaDadosLinhasBackEnd () {
      let retorno = []
      this.listaDeHoras.forEach((linhaDado) => {
        if (linhaDado.aluno) {
          let objeto = {
            bonus_class: this.itemSelecionadoID,
            aluno: linhaDado.aluno.id,
            horario_aula: this.stringToISODate(this.data, true).split('T')[0] + 'T' + linhaDado.horario + ':00.000Z',
            conteudo: linhaDado.descricao ? linhaDado.descricao : null,
            presenca: linhaDado.presenca,
            selecionado: linhaDado.selecionado
          }
          if (linhaDado.id) {
            objeto.id = linhaDado.id
          }
          retorno.push(objeto)
        }
      })

      return retorno
    },

    buscarAlunoBonusClass (horario) {
      return this.listaDeAlunos.find((aluno) => {
        const horarioAgendado = aluno.horario_aula.match(/(\d{2,2}):(\d{2,2})/)[0]
        if ((`${horario}` === `${horarioAgendado}`) && (aluno.selecionado === true)) {
          return true
        }
      })
    },

    /**
     * Buscar o aluno agendado para certo horario ou caso tenha lista de espera
     * @param horario horario da aula
     * @return[Array]
     */
    buscarAlunosEListaDeEsperaPorHorario (horario) {
      let array = []
      array = this.listaDeAlunos.filter((aluno) => {
        const horarioAgendado = aluno.horario_aula.match(/(\d{2,2}):(\d{2,2})/)[0]
        if ((`${horario}` === `${horarioAgendado}`)) {
          return true
        }
      })

      if (array.length === 0) {
        array.push(this.factoryObjectLine())
      }

      return array
    },

    temItem (lista) {
      return lista.length > 0
    },

    adicionarAlunoListaDeEspera (item, index) {
      const tamanhoListaDeEspera = this.buscarHorario(item.horario)
      const newLine = this.factoryObjectLine(0)
      newLine.horario = item.horario
      newLine.descricao = ''
      newLine.disponivel = true
      this.listaDeHoras.splice(index + tamanhoListaDeEspera, 0, newLine)
    },

    adicionarAlunoOuHorario (item, index) {
      let horario = item.horario_aula.match(/(\d{2,2}):(\d{2,2})/) !== null ? item.horario_aula.match(/(\d{2,2}):(\d{2,2})/)[0] : item.horario
      let objetoDoHorario = this.listaDeHoras.find((alunoHorario) => this.buscarAlunoSelecionadoPeloHorario(alunoHorario, horario))
      let indexObjetoDoHorario = this.listaDeHoras.findIndex((alunoHorario) => this.buscarAlunoSelecionadoPeloHorario(alunoHorario, horario))
      item.selecionado = 1

      if (objetoDoHorario && objetoDoHorario.id) {
        this.excluirAlunoBonusClass(objetoDoHorario.id).then(() => {
          this.listaDeHoras.splice(indexObjetoDoHorario, 1, item)
          this.listaDeHoras.splice(index, 1)
          this.salvar()
        })
      } else {
        this.listaDeHoras.splice(indexObjetoDoHorario, 1, item)
        this.listaDeHoras.splice(index, 1)
      }
    },

    buscarHorario (horario) {
      let arr = this.listaDeHoras.filter(item => item.horario === horario)
      return arr.length
    },

    buscarAlunoSelecionadoPeloHorario (item, horario) {
      let itemHoraio = item.horario_aula.match(/(\d{2,2}):(\d{2,2})/) !== null ? item.horario_aula.match(/(\d{2,2}):(\d{2,2})/)[0] : item.horario
      return itemHoraio === horario && (item.selecionado === 1 || item.selecionado === true)
    }
  }
}

</script>
<style scoped>

.h-size-400 {
  max-height: 400px;
}

.overflow-hidden {
  overflow: hidden;
}

.min-size-230px {
  min-width: 230px;
}

.min-size-300 {
  min-width: 300px;
}

.horario-indisponivel {
  pointer-events: none;
  background: #dddddd;
  border-bottom: none;
}
.text-center {
  text-align: center
}

.block-click {
  pointer-events: none;
}
.aluno-espera {
  background: #d8d8d8;
}
.vw-100 {
  width: 100vw;
}

.min-heigth-400 {
  min-height: 400px
}

</style>
