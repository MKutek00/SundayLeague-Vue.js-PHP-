<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-row>
      <v-col cols="1">
        <v-icon size="48" @click="returnView">mdi-keyboard-return</v-icon>
      </v-col>
      <v-col>
        <v-card-subtitle class="text-h4 font-weight-bold">Mecze blisko Ciebie</v-card-subtitle>
      </v-col>
    </v-row>
    <v-card class="pa-5 my-5" max-width="70%" v-for="game in closeGames" :key="game.id">
      <v-card-text>
        <v-row>
          <v-col cols="1">
            <a :href="`http://maps.google.com/?q=${game.lat_a},${game.long_a}`" style="text-decoration: none">
              <v-icon size="72">mdi-map-search</v-icon>
            </a>
          </v-col>
          <v-col>
            <!-- Górny wiersz -->
            <v-row class="text-subtitle-1">
              <v-col class="pa-0" cols="4">
                <p class="ma-0">{{ game.adrdress_a }}</p>
                <p class="ma-0">{{ `${Math.round(game.distance * 100) / 100}km` }}</p>
              </v-col>
              <v-col cols="4"
                ><p>{{ game.league_name.replace('_', ' ') }}</p></v-col
              >
              <v-spacer />
              <v-col cols="3">{{ game.date }}</v-col>
            </v-row>
            <!-- Dolny wiersz -->
            <v-row>
              <v-spacer />
              <v-col class="text-h5 font-weight-medium" cols="11">{{
                `${game.team_a} ${game.goals_a != undefined ? game.goals_a : ''} - ${
                  game.goals_b != undefined ? game.goals_b : ''
                } ${game.team_b}`
              }}</v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-card>
</template>

<script lang="ts">
import { Vue, Component, Prop, Emit } from 'vue-property-decorator';
import { CloseGame } from '@/lib/types';
import { PrintError, post } from '@/lib/fetch';
@Component({
  name: 'ShowGame',
})
export default class extends Vue {
  @Prop() readonly location!: { address: string; radius: number; date: string };
  public closeGames: CloseGame[] = [];

  public returnView() {
    this.return();
  }

  @Emit()
  return() {
    return true;
  }

  public async getCloseGames(): Promise<void> {
    try {
      const response = await post<CloseGame[]>('http://localhost:8080/getCloseGames', { location: this.location });
      this.closeGames = response;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
  mounted() {
    this.getCloseGames();
  }
}
</script>
