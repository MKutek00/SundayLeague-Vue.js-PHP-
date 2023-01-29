<template>
  <v-dialog v-model="dialogRegister" persistent width="580">
    <v-card>
      <v-toolbar class="text-center" color="success">
        <v-toolbar-title class="text-h4 font-weight-bold" style="width: 90%; color: #ffffff"
          >Rejestracja</v-toolbar-title
        >
        <v-icon @click="close">mdi-window-close</v-icon>
      </v-toolbar>
      <v-form v-model="isValid">
        <v-card-text class="px-10 pt-10">
          <v-text-field
            v-model="newUser.login"
            class="text-h6"
            outlined
            placeholder="Podaj login"
            label="Login"
            :rules="[rules.required]"
          ></v-text-field>
          <v-text-field
            v-model="newUser.email"
            class="text-h6"
            outlined
            placeholder="Podaj email"
            label="E-mail"
            :rules="[rules.required, rules.email]"
          ></v-text-field>
          <v-text-field
            v-model="newUser.password"
            type="password"
            class="text-h6"
            outlined
            placeholder="Podaj hasło"
            label="Hasło"
            :rules="[rules.required]"
          ></v-text-field>
          <v-text-field
            v-model="repeatPassword"
            type="password"
            class="text-h6"
            outlined
            placeholder="Powtórz hasło"
            label="Powtórz hasło"
            :rules="[rules.required, rules.identical]"
          ></v-text-field>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="justify-center">
          <v-btn class="pa-8" color="success" :disabled="!isValid" @click="registerUser"
            >Utwórz konto <v-icon class="ml-2">mdi-login</v-icon></v-btn
          >
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import { Component, Vue, Emit } from 'vue-property-decorator';
import { post, PrintError } from '@/lib/fetch/';
import Swal from 'sweetalert2';
import User from '@/store/modules/User';
import { getModule } from 'vuex-module-decorators';

const user = getModule(User);
@Component({
  name: 'Register',
})
export default class extends Vue {
  public dialogRegister = true;
  public newUser: {
    login: string;
    email: string;
    password: string;
  } = {
    login: '',
    email: '',
    password: '',
  };
  public repeatPassword: string = '';
  public isValid: boolean = false;

  public rules = {
    required: (value: string) => !!value || 'Wymagane.',
    email: (value: string) => {
      const pattern =
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return pattern.test(value) || 'Błędny email';
    },
    identical: () => this.checkPass(),
  };
  public checkPass() {
    if (this.newUser.password === this.repeatPassword) return true;
    else return 'Hasła nie są identyczne';
  }

  @Emit('close')
  close() {
    this.dialogRegister = false;
  }

  public async registerUser(): Promise<void> {
    try {
      await post('http://localhost:8080/registerUser', { user: this.newUser });

      await user.loginUser({ login: this.newUser.login, password: this.newUser.password });
      Swal.fire({
        icon: 'success',
        title: 'Utworzono twoje konto',
        timer: 3000,
        showConfirmButton: false,
      });

      this.close();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
}
</script>
