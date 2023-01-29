<template>
  <v-dialog v-model="dialogLogin" persistent width="580">
    <v-card>
      <v-toolbar class="text-center" color="success">
        <v-toolbar-title class="text-h4 font-weight-bold" style="width: 90%; color: #ffffff">Logowanie</v-toolbar-title>
        <v-icon @click="zamknijOkno">mdi-window-close</v-icon>
      </v-toolbar>
      <v-card-text class="px-10 pt-10">
        <v-text-field
          v-model="loginUser.login"
          class="text-h6"
          outlined
          placeholder="Podaj login"
          label="Login"
          :rules="[rules.required]"
        ></v-text-field>
        <v-text-field
          v-model="loginUser.password"
          class="text-h6"
          outlined
          placeholder="Podaj hasło"
          label="Hasło"
          :rules="[rules.required]"
          type="password"
        ></v-text-field>
        <v-btn color="success" @click="registerDialog"
          >Nie posiadasz konta? Utwórz je! <v-icon class="ml-2">mdi-account</v-icon></v-btn
        >
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="justify-center">
        <v-btn class="pa-8" color="success" @click="login">Zaloguj się <v-icon class="ml-2">mdi-login</v-icon></v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import { Vue, Component, Emit } from 'vue-property-decorator';
import { getModule } from 'vuex-module-decorators';
import User from '@/store/modules/User';

const user = getModule(User);

@Component({
  name: 'Login',
})
export default class extends Vue {
  public dialogLogin = true;
  public rules = {
    required: (value: string) => !!value || 'Wymagane.',
  };
  public loginUser: { login: string; password: string } = { login: '', password: '' };

  @Emit('close')
  close() {
    this.dialogLogin = false;
  }

  @Emit('showRegister')
  showRegister() {
    this.dialogLogin = false;
  }

  public registerDialog() {
    this.showRegister();
  }

  public zamknijOkno() {
    this.close();
  }

  public async login(): Promise<void> {
    await user.loginUser(this.loginUser);
    if (user.isLoggedIn) this.zamknijOkno();
    console.log(user.userData);
    console.log(user.isLoggedIn);
  }
}
</script>
