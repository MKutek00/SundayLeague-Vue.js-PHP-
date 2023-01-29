<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-img class="ma-2" src="@/../public/find_match.svg" max-width="400"></v-img>
    <v-card class="px-5 py-10 ma-5" max-width="50%">
      <v-text-field
        class="text-h5 font-weight-medium"
        v-model="findGame.address"
        placeholder="Podaj adres"
        append-outer-icon="mdi-crosshairs-gps"
        filled
        outlined
        style="border-radius: 15px"
        @click:append-outer="getNavigatorLocation"
      ></v-text-field>
      <v-row>
        <v-col cols="5">
          <v-text-field
            class="text-h6 font-weight-medium mr-auto"
            v-model="findGame.radius"
            label="Promień poszukiwań"
            outlined
            filled
            style="border-radius: 15px"
          ></v-text-field>
        </v-col>
        <v-col cols="5">
          <v-menu
            v-model="menu2"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="findGame.date"
                label="Wybierz date"
                prepend-icon="mdi-calendar"
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker v-model="findGame.date" @input="menu2 = false" color="green lighten-1"></v-date-picker>
          </v-menu>
        </v-col>
      </v-row>
      <v-btn
        color="success"
        rounded
        class="px-15 py-5"
        @click="searchGamesButton"
        type="number"
        :disabled="disabledButton"
        >Znajdź mecz</v-btn
      >
    </v-card>
  </v-card>
</template>

<script lang="ts">
import { Vue, Component, Emit } from 'vue-property-decorator';

@Component({
  name: 'FillForm',
})
export default class extends Vue {
  public findGame = {
    address: '',
    radius: 0,
    date: new Date().toJSON().slice(0, 10),
  };
  public menu2 = false;

  get disabledButton() {
    return this.findGame.address.trim().length == 0 || this.findGame.radius == 0;
    // return false;
  }

  @Emit()
  close() {
    return this.findGame;
  }

  public getNavigatorLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition((position) => {
        this.geocodeLatLng(position.coords.latitude, position.coords.longitude);
      });
    }
  }

  public geocodeLatLng(lat: number, lng: number) {
    // eslint-disable-next-line no-undef
    const geocoder = new google.maps.Geocoder();
    const latlng = { lat: lat, lng: lng };
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    geocoder.geocode({ location: latlng }).then((response: any) => {
      this.findGame.address = response.results[0].formatted_address;
    });
  }

  public searchGamesButton(): void {
    this.close();
  }
}
</script>
