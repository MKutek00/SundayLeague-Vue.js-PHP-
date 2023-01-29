<template>
  <v-card fluid align="center" class="pa-5" min-height="97vh">
    <v-card-title class="text-h4 font-weight-bold justify-center">Dodaj artykuł</v-card-title>
    <v-card-text>
      <v-form v-model="valid">
        <v-card class="pa-10">
          <v-card-text>
            <v-row>
              <v-col cols="6"> <v-text-field v-model="newArticle.title" outlined label="Tytuł"></v-text-field> </v-col
            ></v-row>
            <v-row>
              <v-col cols="6">
                <v-textarea v-model="newArticle.subtitle" outlined label="Podtytuł"></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="6">
                <v-textarea v-model="newArticle.text" outlined label="Treść"></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="6">
                <v-file-input v-model="newPhoto" show-size accept="image/*" label="Photo"></v-file-input>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-btn class="pa-5" color="success" :disabled="false" @click="addArticle">Dodaj artykuł</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import User from '@/store/modules/User';
import { getModule } from 'vuex-module-decorators';
import { post, PrintError, PrintSuccess } from '@/lib/fetch';

const user = getModule(User);
@Component({
  name: 'AddArticle',
})
export default class extends Vue {
  public valid = false;
  public newPhoto: File | null = null;
  public newArticle = {
    title: '',
    subtitle: '',
    text: '',
    photo: '',
  };
  // Brzydko to wygląda

  get disabledButton() {
    return (
      this.newArticle.title.trim().length < 5 ||
      this.newArticle.title.trim().length > 50 ||
      this.newArticle.subtitle.trim().length > 80 ||
      this.newArticle.subtitle.trim().length < 20 ||
      this.newArticle.text.trim().length < 100
    );
  }

  public async addArticle(): Promise<void> {
    if (this.newPhoto == null) return;
    this.newArticle.photo = this.newPhoto?.name ? this.newPhoto.name : '';

    try {
      await post('http://localhost:8080/addArticle', { article: this.newArticle, user_id: user.userData?.id_user });

      this.newArticle = {
        title: '',
        subtitle: '',
        text: '',
        photo: '',
      };
      PrintSuccess();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
}
</script>
