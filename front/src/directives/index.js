import Vue from 'vue'
import maskFilter from './mask-filter'
import helpHint from './help-hint'
import wipe from './wipe'
import number from './number'
import inputLocker from './input-locker'

Vue.directive('mask-filter', maskFilter)
Vue.directive('help-hint', helpHint)
Vue.directive('wipe', wipe)
Vue.directive('number', number)
Vue.directive('input-locker', inputLocker)
