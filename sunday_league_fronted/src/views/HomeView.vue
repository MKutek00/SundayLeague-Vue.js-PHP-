<template>
  <news />
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator';
import News from '../components/News.vue';

@Component({
  name: 'HomeView',
  components: { News },
})
export default class extends Vue {}
</script>
