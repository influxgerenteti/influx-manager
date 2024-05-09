<template>
  <div class="animated fadeIn d-flex">

    <div class="form-group row flex-grow-1 mb-3">

      <div class="col-md-6">
        <div class="row">
          <div class="col">
            <div class="mb-3">
              <h4 id="traffic" class="card-title mb-0">Escolas em cada meta</h4>
              <div class="small text-muted">{{ date.mes }} de {{ date.ano }}</div>
            </div>
          </div>
        </div>

        <!-- TABELA -->
        <div class="table-responsive-sm">

          <g-table class="grafico-tabela">
            <!-- <thead class="text-dark">
              <tr>
                <th data-column="it.nome">Nome</th>
                <th data-column="it.telefone_contato, it.telefone_secundario" class="size-205">Telefone</th>
                <th data-column="cu.descricao" class="size-85">Curso</th>
                <th class="size-85">Idioma</th>
                <th data-column="it.tipo_lead" class="size-150">Tipo de Contato</th>
                <th data-column="cf.apelido, crf.apelido" class="size-150">Consultor</th>
                <th v-b-tooltip.top title="Próximo contato" class="d-block text-truncate size-125">Próximo contato</th>
                <th class="size-100">Estado atual</th>
                <th data-column="it.situacao" class="size-75">Situação</th>
                <th class="coluna-icones"></th>
              </tr>
            </thead> -->
            <tbody>
              <perfect-scrollbar>
                <!-- <div v-if="estaCarregando" class="form-loading">
                  <load-placeholder :loading="estaCarregando" />
                </div> -->
                <!-- <div v-if="!listaItems.length && !estaCarregando" class="busca-vazia">
                  <p>Nenhum resultado encontrado.</p>
                </div> -->

                <tr v-for="(item, index) in lista" :key="index" :data-bg="item.bg" :style="`background-color: ${item.bg}`">
                  <td data-label="Nome">{{ item.descricao }}</td>
                  <td data-label="Telefone" class="size-95">{{ item.value }}</td>
                </tr>
              </perfect-scrollbar>
            </tbody>
          </g-table>

        </div>
        <!-- TABELA -->
      </div>

      <div class="col-md-6">
        <!-- GRÁFICO LINHAS -->
        <line-chart :data="matriculas"
                    :curve="false"
                    :download="{background: '#fff', filename: 'Evolução de matrículas do mês'}"
                    ytitle="Matrículas convertidas"
                    xtitle="Dia do mês"
                    width="100%"
                    height="100%"
        />
        <!-- :refresh="60" -->
      </div>
    </div>

    <div class="form-group row flex-grow-1">
      <div class="col-md-12">
        <!-- GRÁFICO COLUNAS -->
        <column-chart :data="contatos"
                      :data-label="cLabel"
                      :download="{background: '#fff', filename: 'Evolução de contatos do mês'}"
                      width="100%"
                      height="100%"
        />
        <!-- :refresh="60" -->
      </div>
    </div>

    <!-- :min="-1" :max="1" -->
    <!-- :colors="['#ea7124', '#2d4899']" -->

    <!-- <b-card>

      <b-row v-if="user">
        <b-col sm="12">
          Bem-vindo {{ user.nome }}
        </b-col>
      </b-row>

      <b-row>
        <b-col sm="5">
          <h4 id="traffic" class="card-title mb-0">Movimentação de alunos</h4>
          <div class="small text-muted">Agosto de 2017 a Janeiro de 2018</div>
        </b-col>
        <b-col sm="7" class="d-none d-md-block">
          <b-button type="button" variant="primary" class="float-right"><i class="icon-cloud-download"></i></b-button>
        </b-col>
      </b-row>
      <main-chart-example class="chart-wrapper" style="height:300px;margin-top:40px;" height="300"/>
      <div slot="footer">
        <ul>
          <li>
            <div class="text-muted">Matrículas</div>
            <b-progress :precision="1" :value="100" height="{}" class="progress-xs mt-2" variant="success"/>
          </li>
          <li class="d-none d-md-table-cell">
            <div class="text-muted">Rematrículas</div>
            <b-progress :precision="1" :value="100" height="{}" class="progress-xs mt-2" variant="info"/>
          </li>
          <li class="d-none d-md-table-cell">
            <div class="text-muted">Rescisões</div>
            <b-progress :precision="1" :value="100" height="{}" class="progress-xs mt-2" variant="danger"/>
          </li>
          <li class="d-none d-md-table-cell">
            <div class="text-muted">Encerramentos</div>
            <b-progress :precision="1" :value="100" height="{}" class="progress-xs mt-2" variant="dark" />
          </li>
        </ul>
      </div>
    </b-card> -->

  </div>
</template>

<script>
import {mapState} from 'vuex'
import MainChartExample from './dashboard/MainChartExample'
import moment from 'moment'

export default {
  name: 'Dashboard',
  components: {
    MainChartExample
  },
  data: function () {
    return {
      selected: 'Month',
      tableItems: [
        {
          avatar: { url: 'static/img/avatars/1.jpg', status: 'success' },
          user: { name: 'Yiorgos Avraamu', new: true, registered: 'Jan 1, 2015' },
          country: { name: 'USA', flag: 'us' },
          usage: { value: 50, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'Mastercard', icon: 'fa fa-cc-mastercard' },
          activity: '10 sec ago'
        },
        {
          avatar: { url: 'static/img/avatars/2.jpg', status: 'danger' },
          user: { name: 'Avram Tarasios', new: false, registered: 'Jan 1, 2015' },
          country: { name: 'Brazil', flag: 'br' },
          usage: { value: 22, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'Visa', icon: 'fa fa-cc-visa' },
          activity: '5 minutes ago'
        },
        {
          avatar: { url: 'static/img/avatars/3.jpg', status: 'warning' },
          user: { name: 'Quintin Ed', new: true, registered: 'Jan 1, 2015' },
          country: { name: 'India', flag: 'in' },
          usage: { value: 74, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'Stripe', icon: 'fa fa-cc-stripe' },
          activity: '1 hour ago'
        },
        {
          avatar: { url: 'static/img/avatars/4.jpg', status: '' },
          user: { name: 'Enéas Kwadwo', new: true, registered: 'Jan 1, 2015' },
          country: { name: 'France', flag: 'fr' },
          usage: { value: 98, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'PayPal', icon: 'fa fa-paypal' },
          activity: 'Last month'
        },
        {
          avatar: { url: 'static/img/avatars/5.jpg', status: 'success' },
          user: { name: 'Agapetus Tadeáš', new: true, registered: 'Jan 1, 2015' },
          country: { name: 'Spain', flag: 'es' },
          usage: { value: 22, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'Google Wallet', icon: 'fa fa-google-wallet' },
          activity: 'Last week'
        },
        {
          avatar: { url: 'static/img/avatars/6.jpg', status: 'danger' },
          user: { name: 'Friderik Dávid', new: true, registered: 'Jan 1, 2015' },
          country: { name: 'Poland', flag: 'pl' },
          usage: { value: 43, period: 'Jun 11, 2015 - Jul 10, 2015' },
          payment: { name: 'Amex', icon: 'fa fa-cc-amex' },
          activity: 'Last week'
        }
      ],

      contatos: [],
      cLabel: '',

      matriculas: [],

      lista: [
        {descricao: 'Total de escolas', value: '163'},
        {descricao: 'Bateram meta', value: '79'},

        {descricao: 'Primeira meta', value: '22', bg: '#79caf9'},
        {descricao: 'Segunda meta', value: '17', bg: '#00D1B2'}, // #bbf86b
        {descricao: 'Terceira meta', value: '40', bg: '#FFDD57'},
        {descricao: 'Não bateram meta', value: '46', bg: '#FF3860'},

        {descricao: 'Matrículas no mês', value: '1399'},
        {descricao: 'Ticket médio', value: '10.76'},
        {descricao: 'Previsão', value: '1398.8'},
        {descricao: 'Escolas que enviaram resultado (%)', value: '79.75%'},
        {descricao: 'Bateram meta (%)', value: '60.77%'}
      ],

      date: {
        mes: '',
        ano: ''
      }

    }
  },
  computed: mapState(['user']),
  mounted () {
    this.cLabel = `Evolução de contatos do mês: ${moment().locale('pt').format('MMMM de YYYY')}`

    const m = moment().locale('pt')
    this.date.mes = m.format('MMMM')
    this.date.ano = m.format('YYYY')

    this.buildCharts()
  },
  methods: {

    buildCharts () {
      this.contatos = [
        {name: 'Cont. Ativos', data: {}, color: '#ea7124', tipo: 'A'},
        {name: 'Cont. Receptivos', data: {}, color: '#2d4899', tipo: 'R'}
      ]

      this.matriculas = [
        {name: 'Matrículas Ativas', data: {}, color: '#ea7124', tipo: 'A'},
        {name: 'Matrículas Receptivas', data: {}, color: '#2d4899', tipo: 'R'},
        {name: 'Matrículas Totais', data: {}, color: '#00D1B2', tipo: 'T'}
      ]

      const d = moment().get('date')
      const data = this.buildRandomDataInfo(d)

      this.contatos.map(item => {
        this.buildDataCharts(item, data, d)
      })
      this.matriculas.map(item => {
        this.buildDataCharts(item, data, d)
      })
    },

    buildDataCharts (item, data, endDay) {
      for (let i = 1; i <= endDay; i++) {
        const dia = moment().set('date', i).format('DD/MM/YYYY')

        if (item.tipo === 'A') {
          item.data[dia] = data[i].va
        }
        if (item.tipo === 'R') {
          item.data[dia] = data[i].vr
        }
        if (item.tipo === 'T') {
          item.data[dia] = data[i].total
        }
      }
    },

    buildRandomDataInfo (endDay) {
      let dataChart = [null]
      for (let i = 1; i <= endDay; i++) {
        const values = {
          va: this.getRandomInt(200),
          vr: this.getRandomInt(200)
        }
        values.total = values.va + values.vr

        dataChart.push(values)
      }
      return dataChart
    },

    getRandomInt (max) {
      return Math.floor(Math.random() * Math.floor(max))
    }

  }
}
</script>
<style scoped>
.table-borderless.table-hover tbody tr[data-bg] {
  color: #ffffff;
}
.table-borderless.table-hover tbody tr[data-bg="#FFDD57"] {
  color: #4A4A4A;
}

.main .container-fluid .animated .table-responsive-sm, .main .container-fluid form .table-responsive-sm {
  flex-grow: 1;
  position: relative;
  min-height: 100%;
}

</style>
