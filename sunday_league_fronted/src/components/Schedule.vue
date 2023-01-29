<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-btn class="ma-5 py-5 px-10" :color="path.includes('schedule') ? 'success' : ''" :to="`/schedule/${leagueName}`"
      >Terminarz</v-btn
    >
    <v-btn class="ma-5 py-5 px-10" :color="path.includes('table') ? 'success' : ''" :to="`/table/${leagueName}`"
      >Tabela</v-btn
    >
    <v-card class="pa-5 ma-5" width="80%">
      <v-card-title class="text-h4 font-weight-bold justify-center my-5">{{
        leagueName.replace('_', ' ')
      }}</v-card-title>
      <v-card-text class="pa-5" v-for="(games, kolejka) in schedule" :key="kolejka">
        <v-row clas="my-5">
          <v-icon>mdi-arrow-right</v-icon>
          <p class="ma-0 text-h5 font-weight-medium">{{ `Kolejka ${kolejka}` }}</p>
          <v-spacer />
        </v-row>
        <v-divider class="my-5" />
        <v-row class="my-5 text-subtitle-1" v-for="game in games" :key="game.long_a">
          <v-col class="py-0" cols="1"></v-col>
          <v-col class="py-0">{{ game.team_a }}</v-col>
          <v-col class="py-0" cols="1" v-if="game.id != modifyId">{{
            `${game.goals_a == null ? '' : game.goals_a} - ${game.goals_b == null ? '' : game.goals_b}`
          }}</v-col>

          <v-col class="py-0 d-flex" cols="1" v-else>
            <v-text-field v-model="game.goals_a"></v-text-field>
            <v-divider vertical class="mx-2"> </v-divider>
            <v-text-field v-model="game.goals_b"></v-text-field>
          </v-col>

          <v-col class="py-0">{{ game.team_b }}</v-col>
          <v-col class="py-0">
            <v-icon v-if="(modifyId == 0 || modifyId == game.id) && permissions" @click="editSchedule(game)"
              >mdi-text-box-edit-outline</v-icon
            >
            {{ game.date }}</v-col
          >
          <v-col cols="1" class="py-0"
            ><a :href="`http://maps.google.com/?q=${game.lat_a},${game.long_a}`" style="text-decoration: none"
              ><v-icon large>mdi-map-marker</v-icon></a
            ></v-col
          >
        </v-row>
      </v-card-text>
    </v-card>
  </v-card>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator';
import type { Schedule } from '@/lib/types';
import { post, PrintError, PrintSuccess } from '@/lib/fetch';
import User from '@/store/modules/User';
import { getModule } from 'vuex-module-decorators';
const user = getModule(User);

@Component({
  name: 'Schedule',
})
export default class extends Vue {
  public modifyId: number = 0;
  public modyfingGoalsA: number = 0;
  public modyfingGoalsB: number = 0;
  public path: string = location.pathname;
  public leagueName: string = this.$route.params.leagueName;
  public schedule: { [key: number]: Schedule[] } = [];

  get user() {
    return user;
  }

  get permissions() {
    if (user.userData?.role == null) return false;
    return ['Admin', 'Redaktor'].includes(user.userData?.role.role_name);
  }

  public editSchedule(game: Schedule) {
    if (this.modifyId != 0) {
      this.saveSchedule(game);
    } else {
      this.modifyId = game.id;
      this.modyfingGoalsA = game.goals_a;
      this.modyfingGoalsB = game.goals_b;
    }
  }
  public async saveSchedule(game: Schedule) {
    this.modifyId = 0;
    if (this.modyfingGoalsA == game.goals_a && this.modyfingGoalsB == game.goals_b) return;
    try {
      await post('http://localhost:8080/saveSchedule', { schedule: game });
      PrintSuccess('Zapisano zmiany');
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async getSchedule(): Promise<void> {
    try {
      const response = await post<{ [key: number]: Schedule[] }>('http://localhost:8080/getLeagueSchedule', {
        league_name: this.leagueName,
      });
      this.schedule = response;
      console.log(this.schedule);
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  mounted() {
    this.getSchedule();
  }
}
</script>
