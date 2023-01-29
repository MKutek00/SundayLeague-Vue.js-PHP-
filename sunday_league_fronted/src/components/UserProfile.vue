<template>
  <v-card class="pa-5 mx-auto my-15" width="40%" align="center" elevation="10">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-card-title class="justify-center">Ustawienia konta: {{ user.userData?.login }}</v-card-title>
    <v-card-text class="my-5">
      <v-row>
        <v-spacer />
        <v-img
          :src="require(`@/assets/${user.userData?.avatar}`)"
          style="border-radius: 50%"
          aspect-ratio="1"
          width="150"
        ></v-img>
        <v-spacer />
      </v-row>
      <v-row class="mt-5">
        <v-spacer />
        <v-btn color="success" @click="changeAvatarOptions = true" v-if="!changeAvatarOptions">Zmień avatar</v-btn>
        <v-col cols="8" class="pa-0" v-else>
          <v-file-input
            v-model="newAvatar"
            class="ml-n8"
            accept="image/*"
            label="Avatar"
            append-icon="mdi-content-save"
            @click:append="changeAvatar"
          ></v-file-input>
        </v-col>
        <v-spacer />
      </v-row>
      <v-row>
        <v-spacer />

        <v-icon class="mr-2">mdi-account</v-icon>
        <v-col cols="8" class="pa-0">
          <v-text-field :value="user.userData?.login" disabled></v-text-field>
        </v-col>
        <v-spacer />
      </v-row>
      <v-row>
        <v-spacer />

        <v-icon class="mr-2">mdi-form-textbox-password</v-icon>
        <v-col cols="8" class="pa-0">
          <v-text-field v-model="newPassword" type="password" label="Nowe hasło"></v-text-field>
        </v-col>
        <v-spacer />
      </v-row>
      <v-row>
        <v-spacer />

        <v-icon class="mr-2">mdi-form-textbox-password</v-icon>
        <v-col cols="8" class="pa-0">
          <v-text-field v-model="oldPassword" type="password" label="Stare hasło"></v-text-field>
        </v-col>
        <v-spacer />
      </v-row>
    </v-card-text>
    <v-card-actions class="justify-center">
      <v-btn color="success" class="pa-5" @click="changePassword"> Zapisz </v-btn>
    </v-card-actions>
    <v-btn v-if="admin" primary class="mx-5" @click="scrapTeamsAndLeagues">Pobierz Drużyny</v-btn>
    <v-btn v-if="admin" primary class="mx-5" @click="scrapSchedule">Pobierz Terminarz</v-btn>
  </v-card>
</template>

<script lang="ts">
import { Vue, Component } from 'vue-property-decorator';
import { get, post, PrintError, PrintSuccess } from '@/lib/fetch';
import { getModule } from 'vuex-module-decorators';
import User from '@/store/modules/User';
import Swal from 'sweetalert2';

const user = getModule(User);
@Component({
  name: 'UserProfile',
})
export default class extends Vue {
  public changeAvatarOptions = false;
  public oldPassword: string = '';
  public newPassword: string = '';
  public newAvatar: File | null = null;
  public loading = false;

  get user() {
    return user;
  }

  get admin() {
    return user.userData?.role.role_name == 'Admin';
  }

  public async changeAvatar(): Promise<void> {
    if (this.newAvatar?.name == null) return;
    try {
      await post('http://localhost:8080/updateAvatar', {
        id: this.user.userData?.id_user,
        newAvatar: this.newAvatar?.name,
      });
      if (user.userData != null) user.userData.avatar = this.newAvatar?.name;

      PrintSuccess('Zmieniono avatar');
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async changePassword(): Promise<void> {
    if (this.newPassword == null) return;
    try {
      // await post('http://localhost:8080/updatePassword', {
      //   id: this.user.userData?.id_user,
      //   oldPassword: this.oldPassword,
      //   newPassword: this.newPassword,
      // });
      PrintSuccess();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
  public async scrapTeamsAndLeagues(): Promise<void> {
    Swal.fire({
      icon: 'warning',
      title: 'Scrap Teams! Jesteś pewien? (Zapytanie wprowadza istotne zmiany i może trwać znaczną chwilę)',
      showDenyButton: true,
      confirmButtonText: 'Tak',
      confirmButtonColor: 'rgb(76, 175, 80)',
      denyButtonText: 'Nie',
    }).then((result) => {
      if (result.isConfirmed) {
        try {
          this.loading = true;
          setTimeout(() => {
            console.log('doing');
          }, 1000);
          // get('http://localhost:8080/scrapTeamsAndLeagues');
          this.loading = false;
          PrintSuccess('Pobrano drużyny');

          // eslint-disable-next-line @typescript-eslint/no-explicit-any
        } catch (error: any) {
          PrintError(error);
          this.loading = false;
        }
      }
    });
  }
  public async scrapSchedule(): Promise<void> {
    Swal.fire({
      icon: 'warning',
      title: 'Scrap Schedule! Jesteś pewien? (Zapytanie wprowadza istotne zmiany i może trwać znaczną chwilę)',
      showDenyButton: true,
      confirmButtonText: 'Tak',
      confirmButtonColor: 'rgb(76, 175, 80)',
      denyButtonText: 'Nie',
    }).then((result) => {
      if (result.isConfirmed) {
        try {
          this.loading = true;
          setTimeout(() => {
            console.log('doing');
          }, 1000);
          // get('http://localhost:8080/scrapSchedule');
          this.loading = false;
          PrintSuccess('Pobrano terminarz');
          // eslint-disable-next-line @typescript-eslint/no-explicit-any
        } catch (error: any) {
          PrintError(error);
          this.loading = false;
        }
      }
    });
  }
}
</script>
