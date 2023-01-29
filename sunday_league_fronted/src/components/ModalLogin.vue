<template>
  <login v-if="showLogin" @close="closeDialog" @showRegister="showRegister"></login>
  <register v-else @close="closeDialog"></register>
</template>

<script lang="ts">
import { Vue, Component, Emit } from 'vue-property-decorator';
import Login from './Login.vue';
import Register from './Register.vue';

@Component({
  name: 'ModalLogin',
  components: { Login, Register },
})
export default class extends Vue {
  public showLogin = true;

  @Emit()
  close() {
    this.showLogin = true;
  }

  public closeDialog() {
    this.close();
  }

  public showRegister() {
    this.showLogin = false;
  }
}
</script>
