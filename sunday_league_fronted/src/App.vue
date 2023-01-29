<template>
  <v-app>
    <nav-bar></nav-bar>
    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import NavBar from '@/components/NavBar.vue';

import { getModule } from 'vuex-module-decorators';
import User from '@/store/modules/User';

const UserModule = getModule(User);

@Component({
  name: 'App',
  components: { NavBar },
})
export default class extends Vue {
  mounted() {
    UserModule.checkStorage();
  }
}
</script>
