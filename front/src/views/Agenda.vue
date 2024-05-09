<template>
  <div class="animated fadeIn">
    <!--
    <b-card-group class="mb-4">

      <b-card v-for="(weekday, key) in days" :key="'day-'+ key">
        <div class="text-muted text-uppercase text-center font-weight-bold">{{ weekday }}</div>

        <draggable v-model="tasks[key]" :options="{group: 'tasks'}" style="padding: 5px 0; min-height: 30px; border: 1px dashed #333;" @start="drag=true">
          <div v-for="element in tasks[key]" :key="element.id" :class="[element.bg, 'card-list-item']">{{ element.name }}</div>
        </draggable>
      </b-card>

    </b-card-group>
    -->

    <div class="grid-container">
      <div class="grid-item grid-header">Horários</div>
      <div class="grid-item grid-header">Segunda</div>
      <div class="grid-item grid-header">Terça</div>
      <div class="grid-item grid-header">Quarta</div>
      <div class="grid-item grid-header">Quinta</div>
      <div class="grid-item grid-header">Sexta</div>
      <div class="grid-item grid-header">Sábado</div>

      <template v-for="hour in hours">
        <div :key="hour" class="grid-item">{{ hour }}</div>
        <div :key="hour + '1'" class="grid-item">Segunda</div>
        <div :key="hour + '2'" class="grid-item">Terça</div>
        <div :key="hour + '3'" class="grid-item">Quarta</div>
        <div :key="hour + '4'" class="grid-item">Quinta</div>
        <div :key="hour + '5'" class="grid-item">Sexta</div>
        <div :key="hour + '6'" class="grid-item">Sábado</div>
      </template>
    </div>
  </div>
</template>

<script>
// import {mapState} from 'vuex'
import Draggable from 'vuedraggable'

export default {
  name: 'Agenda',
  components: {
    Draggable
  },
  data: function () {
    return {
      hours: ['08:00', '08:15', '08:30'],
      days: {
        monday: 'Segunda-feira',
        tuesday: 'Terça-feira',
        wednesday: 'Quarta-feira',
        thursday: 'Quinta-feira',
        friday: 'Sexta-feira',
        saturday: 'Sábado'
      },
      tasks: {
        monday: [
          {id: 1, name: 'Turma 01', bg: 'bg-primary'},
          {id: 2, name: 'Turma 02', bg: 'bg-danger'}
        ],
        tuesday: [
          {id: 3, name: 'Turma 03', bg: 'bg-success'}
        ],
        wednesday: [
          {id: 4, name: 'Turma 04', bg: 'bg-info'}
        ],
        thursday: [
          {id: 5, name: 'Turma 05', bg: 'bg-primary'},
          {id: 6, name: 'Turma 06', bg: 'bg-danger'}
        ],
        friday: [
          {id: 7, name: 'Turma 07', bg: 'bg-success'},
          {id: 8, name: 'Turma 08', bg: 'bg-warning'}
        ],
        saturday: [
          {id: 9, name: 'Turma 09', bg: 'bg-primary'},
          {id: 10, name: 'Turma 10', bg: 'bg-danger'}
        ]
      }
    }
  },
  beforeMount () {
    document.body.classList.add('sidebar-minimized')
  },
  destroyed () {
    document.body.classList.remove('sidebar-minimized')
  }
}
</script>
