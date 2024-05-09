<script>
import { Line } from 'vue-chartjs'

// const brandPrimary = '#20a8d8'
const brandSuccess = '#4dbd74'
const brandInfo = '#63c2de'
const brandDanger = '#f86c6b'
const brandDark = '#29363d'

export default {
  extends: Line,
  props: {
    height: {
      type: String,
      default: ''
    }
  },
  mounted () {
    var data1 = [4, 10, 13, 13, 16, 48]
    var data2 = [2, 4, 4, 7, 9, 13]
    var data3 = [1, 2, 2, 4, 4, 5]
    var data4 = [1, 5, 8, 14, 23, 24]

    this.renderChart({
      labels: ['Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro', 'Janeiro'],
      datasets: [
        {
          label: 'Matrículas',
          backgroundColor: 'transparent',
          borderColor: brandSuccess,
          pointHoverBackgroundColor: brandSuccess,
          borderWidth: 2,
          data: data1
        },
        {
          label: 'Rematrículas',
          backgroundColor: 'transparent',
          pointHoverBackgroundColor: brandInfo,
          borderColor: brandInfo,
          borderWidth: 1,
          data: data2
        },
        {
          label: 'Rescisões',
          backgroundColor: 'transparent',
          borderColor: brandDanger,
          pointHoverBackgroundColor: brandDanger,
          borderWidth: 1,
          data: data3
        },
        {
          label: 'Encerramentos',
          backgroundColor: 'transparent',
          borderColor: brandDark,
          pointHoverBackgroundColor: brandDark,
          borderWidth: 1,
          data: data4
        }
      ]
    }, {
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines: {
            drawOnChartArea: true
          }
        }],
        yAxes: [{
          ticks: {
            beginAtZero: true,
            maxTicksLimit: 5,
            stepSize: Math.ceil(50 / 5),
            max: 50
          },
          gridLines: {
            display: true
          }
        }]
      },
      elements: {
        point: {
          radius: 0,
          hitRadius: 10,
          hoverRadius: 4,
          hoverBorderWidth: 3
        }
      }
    })
  }
}
</script>
