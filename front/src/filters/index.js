import Vue from 'vue'
import tipoEvento from './tipo-evento'
import infoEvento from './info-evento'
import formatarData from './formatar-data'
import formatarDataHora from './formatar-data-hora'
import formatarSituacao from './formatar-situacao'
import formatarCNPJ from './formatar-cnpj'
import formatarCPF from './formatar-cpf'
import formatarRG from './formatar-rg'
import formatarTelefone from './formatar-telefone'
import formatarMoeda from './formatar-moeda'
import formatarCep from './formatar-cep'
import formatarNumero from './formatar-numero'
import formatarDataString from './formatar-data-string'
import formatarHoraDB from './formatar-hora'
import formatarDataDiaSemana from './formatar-data-dia-semana'
import formatarDataHHMM from './formatar-data-HHMM'
import { formatarDataTimeZone } from './formatar-data-timezone'

Vue.filter('tipoEvento', tipoEvento)
Vue.filter('infoEvento', infoEvento)
Vue.filter('formatarData', formatarData)
Vue.filter('formatarDataHora', formatarDataHora)
Vue.filter('formatarSituacao', formatarSituacao)
Vue.filter('formatarCNPJ', formatarCNPJ)
Vue.filter('formatarCPF', formatarCPF)
Vue.filter('formatarRG', formatarRG)
Vue.filter('formatarTelefone', formatarTelefone)
Vue.filter('formatarMoeda', formatarMoeda)
Vue.filter('formatarCep', formatarCep)
Vue.filter('formatarNumero', formatarNumero)
Vue.filter('formatarDataString', formatarDataString)
Vue.filter('formatarHoraDB', formatarHoraDB)
Vue.filter('formatarDataDiaSemana', formatarDataDiaSemana)
Vue.filter('formatarDataHHMM', formatarDataHHMM)
Vue.filter('formatarDataTimeZone', formatarDataTimeZone)

