<template>
  <v-navigation-drawer app color="green">
    <modal-login v-if="showDialog" @close="hideModal"></modal-login>
    <v-img class="ma-2" src="@/../public/logo.svg"></v-img>
    <v-divider />
    <v-list nav dense class="my-10">
      <v-list-item-group>
        <v-list-item class="my-3" v-for="(item, i) in navBarOptions" :key="i" :to="item.path" color="white">
          <v-list-item-icon>
            <v-icon large v-text="item.icon"></v-icon>
          </v-list-item-icon>
          <v-list-item-content class="py-3">
            <v-list-item-title class="py-1 text-h6 font-weight-bold" v-text="item.text"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>

    <template v-slot:append>
      <v-container text-center>
        <v-divider />
        <v-list v-if="isLoggedIn">
          <v-list-item link :to="`/userProfile/${person?.id_user}`">
            <v-list-item-avatar>
              <v-img :src="require(`@/assets/${person?.avatar}`)"></v-img>
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title class="text-h6"> {{ person?.login }} </v-list-item-title>
              <v-list-item-subtitle>{{ person?.email }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-btn @click="logout">Wyloguj się</v-btn>
        </v-list>

        <v-btn v-else class="my-10" @click="showModal">Zaloguj się</v-btn>
      </v-container>
    </template>
  </v-navigation-drawer>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import ModalLogin from './ModalLogin.vue';
import { getModule } from 'vuex-module-decorators';
import User from '@/store/modules/User';

const user = getModule(User);

@Component({
  name: 'NavBar',
  components: { ModalLogin },
})
export default class extends Vue {
  public showDialog = false;
  public selectedIte = 0;
  public items = [
    { text: 'Strona główna', icon: 'mdi-home', path: '/', permission: [1, 2, 3, 4] },
    { text: 'Znajdź Mecz', icon: 'mdi-map-search', path: '/findGame', permission: [1, 2, 3, 4] },
    { text: 'Niższe Ligi', icon: 'mdi-soccer', path: '/leagues', permission: [1, 2, 3, 4] },
    { text: 'Dodaj Artykuł', icon: 'mdi-file-document-plus', path: '/addArticle', permission: [1, 2] },
    { text: 'Użytkownicy', icon: 'mdi-account-group', path: '/allUsers', permission: [1, 2] },
  ];
  public renderItems: { text: string; icon: string; path: string; permission: number[] }[] = [];
  mounted() {
    this.renderItems = this.items.filter((i) => i.permission.includes(4));
  }

  get isLoggedIn() {
    return user.isLoggedIn;
  }
  get person() {
    return user.userData;
  }

  get navBarOptions() {
    const userRole = user.userData?.role.id_role;
    if (userRole != null) {
      return this.items.filter((i) => i.permission.includes(Number(userRole)));
    } else {
      return this.items.filter((i) => i.permission.includes(4));
    }
  }

  public logout() {
    user.logout();
  }

  public showModal() {
    this.showDialog = true;
  }
  public hideModal() {
    this.showDialog = false;
  }
}
</script>
