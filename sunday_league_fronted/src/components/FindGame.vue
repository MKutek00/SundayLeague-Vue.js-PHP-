<template>
  <div>
    <keep-alive v-if="fillLocation">
      <fill-form @close="findGames"></fill-form>
    </keep-alive>
    <show-games v-else :location="location" @return="returnView"></show-games>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import FillForm from './FillForm.vue';
import ShowGames from './ShowGames.vue';

@Component({
  name: 'FindGame',
  components: { FillForm, ShowGames },
})
export default class extends Vue {
  public fillLocation = true;
  public location: { address: string; radius: number; date: string } | null = null;

  public findGames(findGame: { address: string; radius: number; date: string }): void {
    this.fillLocation = false;
    this.location = findGame;
  }

  public returnView(value: boolean): void {
    this.fillLocation = value;
  }
}
</script>
