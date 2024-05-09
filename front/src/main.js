import 'babel-polyfill'
import Vue from 'vue'
import VueResource from 'vue-resource'
import Vuelidate from 'vuelidate'
import BootstrapVue from 'bootstrap-vue'
import Loading from 'vue-full-loading'
import Multiselect from 'vue-multiselect'
import VueMoney from 'v-money'
import VueTheMask from 'vue-the-mask'
import VueNumeric from 'vue-numeric'
import VueScrollTo from 'vue-scrollto'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import PerfectScrollbar from 'vue2-perfect-scrollbar'
import VueClipboard from 'vue-clipboard2'
import draggable from 'vuedraggable'
import VDatePicker from 'v-calendar'
import Chartkick from 'vue-chartkick'
import Chart from 'chart.js'

import Treeselect from '@riophae/vue-treeselect'

import App from './App'
import './filters'
import './directives'
import router from './router'
import store from './store'

import {LoadPlaceholder, GDatepicker, GSelect, GNumeric, GFormEndereco, Typeahead, GTable, IconCashback, SaveButton, EditorTexto, Calendar, GTransferenciaTurma, GPersonal, NotasAvaliacao, GModal, GTreeSelect, GPrint, GSelectLicao, GSelectIdioma, GSelectSemestre, GSelectConsultor, GSelectTurma, GSelectLivro, GSelectAtendente, GSelectCurso, GSelectInstrutor, GData, GAgendaPersonal} from './components'

import 'vue-multiselect/dist/vue-multiselect.min.css'
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
// import 'bootstrap/dist/css/bootstrap.css'
// import 'bootstrap-vue/dist/bootstrap-vue.css'

import '@riophae/vue-treeselect/dist/vue-treeselect.css'
import {library} from '@fortawesome/fontawesome-svg-core'
import JsonExcel from 'vue-json-excel'
import {faInfo, faInfoCircle, faCheck, faTimes, faExclamation, faLightbulb, faEye, faPen,
  faBan, faCircle, faUser, faLock, faStar, faBell, faPlus, faBars, faCaretDown,
  faBuilding, faSpinner, faArrowAltCircleUp, faSortDown, faGlasses, faPlusCircle,
  faBookmark, faBookOpen, faChild, faFlag, faHeart, faTrashAlt, faCheckCircle,
  faChevronDown, faChevronUp, faExchangeAlt, faMinus, faAngleLeft, faAngleRight, faSignOutAlt, faSignInAlt, faSyncAlt, faTimesCircle, faCloudUploadAlt,
  faReceipt, faDollarSign, faSearch, faSortUp, faBackspace, faSquare, faCheckSquare, faHandPaper, faMinusSquare, faQuestionCircle, faArrowDown, faList, faListAlt, faExclamationTriangle, faLaughBeam, faThumbsUp, faThumbsDown, faClipboardList,
  faBold, faItalic, faStrikethrough, faUnderline, faCode, faParagraph, faListUl, faListOl, faQuoteRight, faUndo, faRedo, faFileCode, faPrint, faCopy, faFile, faCaretUp, faTasks, faAddressBook, faCog, faPhoneSlash, faTrash, faUserFriends, faHandScissors, faHistory, faTruck, faUpload } from '@fortawesome/free-solid-svg-icons'

library.add(
  faInfo,
  faInfoCircle,
  faCheck,
  faTimes,
  faExclamation,
  faLightbulb,
  faEye,
  faPen,
  faBan,
  faCircle,
  faUser,
  faLock,
  faStar,
  faBell,
  faPlus,
  faBars,
  faCaretDown,
  faBuilding,
  faSpinner,
  faArrowAltCircleUp,
  faSortDown,
  faSortUp,
  faGlasses,
  faPlusCircle,
  faBookmark,
  faBookOpen,
  faChild,
  faFlag,
  faHeart,
  faTrashAlt,
  faCheckCircle,
  faChevronDown,
  faChevronUp,
  faExchangeAlt,
  faMinus,
  faAngleLeft,
  faAngleRight,
  faSignOutAlt,
  faSignInAlt,
  faSyncAlt,
  faTimesCircle,
  faCloudUploadAlt,
  faReceipt,
  faDollarSign,
  faSearch,
  faBackspace,
  faSquare,
  faCheckSquare,
  faMinusSquare,
  faHandPaper,
  faQuestionCircle,
  faArrowDown,
  faList,
  faListAlt,
  faExclamationTriangle,
  faLaughBeam,
  faThumbsUp,
  faThumbsDown,
  faClipboardList,
  faBold,
  faItalic,
  faStrikethrough,
  faUnderline,
  faCode,
  faParagraph,
  faListUl,
  faListOl,
  faQuoteRight,
  faUndo,
  faRedo,
  faFileCode,
  faPrint,
  faCopy,
  faFile,
  faCaretUp,
  faTasks,
  faAddressBook,
  faCog,
  faPhoneSlash,
  faTrash,
  faUserFriends,
  faHandScissors,
  faHistory,
  faTruck,
  faUpload
)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('load-placeholder', LoadPlaceholder)
Vue.component('g-datepicker', GDatepicker)
Vue.component('g-data', GData)
Vue.component('g-select', GSelect)
Vue.component('g-treeselect', GTreeSelect)
Vue.component('g-numeric', GNumeric)
Vue.component('g-form-endereco', GFormEndereco)
Vue.component('g-table', GTable)
Vue.component('g-print', GPrint)
Vue.component('g-select-licao', GSelectLicao)
Vue.component('g-select-idioma', GSelectIdioma)
Vue.component('g-select-livro', GSelectLivro)
Vue.component('g-select-semestre', GSelectSemestre)
Vue.component('g-select-consultor', GSelectConsultor)
Vue.component('g-select-turma', GSelectTurma)
Vue.component('g-select-atendente', GSelectAtendente)
Vue.component('g-select-curso', GSelectCurso)
Vue.component('g-select-instrutor', GSelectInstrutor)
Vue.component('g-data', GData)
Vue.component("g-excel", JsonExcel)
Vue.component('multiselect', Multiselect)
Vue.component('treeselect', Treeselect)
Vue.component('vue-numeric', VueNumeric)
Vue.component('typeahead', Typeahead)
Vue.component('icon-cashback', IconCashback)
Vue.component('save-button', SaveButton)
Vue.component('editor-texto', EditorTexto)
Vue.component('calendar', Calendar)
Vue.component('draggable', draggable)
Vue.component('g-transferencia-turma', GTransferenciaTurma)
Vue.component('g-personal', GPersonal)
Vue.component('g-agenda-personal', GAgendaPersonal)
Vue.component('g-notas-avaliacao', NotasAvaliacao)
Vue.component('g-modal', GModal)
Vue.component('g-data', GData)

Vue.use(BootstrapVue)
Vue.use(VueResource)
Vue.use(Vuelidate)
Vue.use(Loading)
Vue.use(VueTheMask)
Vue.use(VueMoney)
Vue.use(VueScrollTo)
Vue.use(PerfectScrollbar)
Vue.use(VDatePicker)
// Vue.use(Chartkick.use(Chart))

VueClipboard.config.autoSetContainer = true
Vue.use(VueClipboard)

Vue.http.options.emulateJSON = true
Vue.http.interceptors.push((request, next) => {
  // console.log('interceptors');
  // Adiciona o header 'versao' a todas as requisições
  request.headers.set('versao', process.env.VUE_APP_VERSION);
  next();
});

/* eslint-disable no-new */
// new Vue({
//   el: '#app',
//   router,
//   store,
//   components: { App },
//   template: '<App/>'
// })


new Vue({  
  router,
  store,
  render: h => h(App),
}).$mount('#app')

