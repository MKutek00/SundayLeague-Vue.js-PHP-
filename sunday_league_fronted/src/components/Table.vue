<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-btn class="ma-5 py-5 px-10" :color="path.includes('schedule') ? 'success' : ''" :to="`/schedule/${leagueName}`"
      >Terminarz</v-btn
    >
    <v-btn class="ma-5 py-5 px-10" :color="path.includes('table') ? 'success' : ''" :to="`/table/${leagueName}`"
      >Tabela</v-btn
    >
    <v-card class="pa-5 ma-5" width="70%">
      <v-card-title class="text-h5 font-weight-bold justify-center">{{ leagueName.replace('_', ' ') }}</v-card-title>
      <v-data-table
        :headers="headers"
        :items="teams"
        sort-by="punkty"
        sort-desc
        hide-default-footer
        class="elevation-1"
        disable-pagination
      ></v-data-table>
    </v-card>
  </v-card>
</template>

<script lang="ts">
import { post, PrintError } from '@/lib/fetch';
import { TeamTable } from '@/lib/types';
import { Component, Vue } from 'vue-property-decorator';

@Component({
  name: 'Table',
})
export default class extends Vue {
  public headers = [
    { text: 'Nazwa drużyny', value: 'team_name' },
    { text: 'Mecze', value: 'games' },
    { text: 'Punkty', value: 'points' },
    { text: 'Wygrane', value: 'wins' },
    { text: 'Remisy', value: 'draws' },
    { text: 'Porażki', value: 'loses' },
    { text: 'Bramki +', value: 'goal_plus' },
    { text: 'Bramki -', value: 'goal_minus' },
    { text: 'Bramki +/-', value: 'goal_plus_minus' },
  ];
  public leagueName = this.$route.params.leagueName;
  public teams: TeamTable[] = [];

  public path = location.pathname;

  public async getTeamTable(): Promise<void> {
    try {
      const response = await post<TeamTable[]>('http://localhost:8080/getTeamTable', { league_name: this.leagueName });
      this.teams = response;
      console.log(response);
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  mounted() {
    this.getTeamTable();
  }
}
</script>
