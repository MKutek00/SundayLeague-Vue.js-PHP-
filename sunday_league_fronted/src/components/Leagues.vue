<template>
  <v-card class="pa-5" fluid align="center">
    <v-img src="@/../public/lower_leauges.svg" max-width="300"></v-img>
    <v-card color="white" class="py-2 px-5" max-width="60%" elevation="1">
      <v-card-title class="text-h4 font-weight-bold" style="word-break: break-word"
        >Ligi regionalne 2022/2022 - Małopolski ZPN</v-card-title
      >
    </v-card>
    <v-row class="justify-center">
      <v-col cols="5" v-for="(league, keyName) in leagues" :key="keyName">
        <v-card class="ma-5 py-5 px-5">
          <v-card-text>
            <v-list-item v-for="row in league" :key="row.id_league" :to="`/table/${row.league_name}`">
              <v-list-item-content>
                <v-list-item-title class="text-h6 font-weight-medium">
                  <v-icon>mdi-arrow-right</v-icon>
                  {{ `Keeza ${row.league_name}` }}</v-list-item-title
                >
              </v-list-item-content>
            </v-list-item>
          </v-card-text>
        </v-card>
      </v-col>
      <v-spacer />
    </v-row>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { PrintError, get } from '@/lib/fetch/';
import { League } from '@/lib/types';

@Component({
  name: 'Leagues',
})
export default class extends Vue {
  public leagues: { [key: string]: League[] } | null = null;

  public async getLeagues(): Promise<void> {
    try {
      const response = await get<{ [key: string]: League[] }>('http://localhost:8080/getLeagues');
      this.leagues = response;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  mounted() {
    this.getLeagues();
  }
}
</script>
