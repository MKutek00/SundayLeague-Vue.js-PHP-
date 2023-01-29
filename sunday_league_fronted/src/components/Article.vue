<template>
  <v-card fluid>
    <v-card color="green" class="ma-5 px-5 pt-5">
      <v-card-title
        class="text-h3 font-weight-bold mx-auto"
        style="color: white; max-width: 60%; word-break: break-word"
        >{{ article?.title }}</v-card-title
      >
      <v-card-subtitle class="mt-8">
        <v-row :align="'center'">
          <v-col cols="3"></v-col>
          <v-avatar>
            <img v-if="article != null" :src="require(`@/assets/${article?.author?.avatar}`)" />
          </v-avatar>
          <v-col cols="3">
            <p class="ma-0 subtitle-1 font-weight-medium">{{ article?.date }}</p>
            <p class="ma-0 subtitle-1 font-weight-medium">{{ article?.author?.login }}</p>
          </v-col>
          <v-col cols="3" :align="'end'">
            <p class="subtitle-1 font-weight-medium">
              <v-icon>mdi-comment</v-icon>{{ `Komentarzy: ${article?.comments.length}` }}
            </p>
          </v-col>
          <v-spacer />
        </v-row>
      </v-card-subtitle>
    </v-card>
    <v-card class="pa-5 ma-5">
      <v-btn v-if="canModify && disable" color="warning" @click="disable = !disable">Modyfikuj</v-btn>
      <v-btn v-if="canModify && disable" color="error" class="mx-2" @click="deleteArticleConfirm">Usuń</v-btn>
      <v-btn v-if="!disable" color="warning" @click="disable = !disable">Zamknij</v-btn>
      <v-btn v-if="!disable" color="success" @click="saveChangesArticle">Zapisz</v-btn>

      <v-card-text v-if="article != null">
        <v-row>
          <v-col cols="2"></v-col>
          <v-col class="pa-5">
            <v-textarea
              v-model="article.subtitle"
              auto-grow
              :readonly="disable"
              class="text-h5 font-weight-medium"
            ></v-textarea>
            <v-img class="my-10" :src="require(`@/assets/${article.photo}`)" max-width="960"></v-img>
            <v-textarea v-model="article.text" class="text-h6" auto-grow :disabled="disable"></v-textarea>

            <!-- DODAJ KOMENTARZ -->
            <v-card class="pa-5 mb-5 mt-10" v-if="user.isLoggedIn">
              <v-card-text>
                <v-row>
                  <v-col cols="1">
                    <v-avatar>
                      <img :src="require(`@/assets/${user?.userData?.avatar}`)" />
                    </v-avatar>
                  </v-col>
                  <v-col>
                    <v-row>
                      <p>{{ user.userData?.login + '       ' + currDate }}</p>
                    </v-row>
                    <v-textarea
                      v-model="newComment"
                      no-resize
                      placeholder="Bardzo prosimy o kulturalne wyrażanie opinii"
                      auto-grow
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-card-actions>
                <v-btn class="mb-10" @click="addComment" :disabled="newComment.length < 5">Dodaj komentarz</v-btn>
              </v-card-actions>
            </v-card>
            <!-- END ODADJ KOMENETARZ -->
            <v-row class="mt-10">
              <p class="text-h6">{{ `Komentarzy: ${article?.comments.length}` }}</p>
              <v-spacer />
            </v-row>
            <v-divider />
            <!-- COMMENT -->
            <v-card class="pa-5 ma-5" v-for="c in article?.comments" :key="c.id">
              <v-card-text>
                <v-row>
                  <v-col cols="1">
                    <v-avatar>
                      <img :src="require(`@/assets/${c.user_avatar}`)" />
                    </v-avatar>
                  </v-col>
                  <v-col>
                    <v-row>
                      <p>{{ c.user_name + '       ' + c.date }}</p>
                      <v-spacer />
                      <v-icon v-if="permissions" color="red" @click="deleteComment(c)">mdi-close</v-icon>
                    </v-row>
                    <v-textarea readonly no-resize :value="`${c.comment_text}`" height="100px"></v-textarea>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="2"></v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { PrintError, PrintSuccess, post } from '@/lib/fetch/';
import { Article, Komentarz } from '@/lib/types/';
import User from '@/store/modules/User';
import { getModule } from 'vuex-module-decorators';
import Swal from 'sweetalert2';

const user = getModule(User);

@Component({
  name: 'Article',
})
export default class extends Vue {
  public article: Article | null = null;
  public newComment: string = '';
  public articleId = this.$route.params.articleId;
  public disable = true;

  get user() {
    return user;
  }

  get permissions() {
    if (user.userData?.role == null) return false;
    return ['Admin', 'Redaktor'].includes(user.userData?.role.role_name);
  }

  get currDate() {
    const date = new Date();
    return `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`;
  }
  get canModify() {
    return user.userData && (user.userData.role.id_role == 1 || user.userData.role.id_role == 2);
  }

  public deleteArticleConfirm() {
    Swal.fire({
      title: 'Czy na pewno chcesz usunąć artykuł?',
      showDenyButton: true,
      confirmButtonText: '<v-btn color="red">Tak</v-btn>',
      denyButtonText: 'Anuluj',
    }).then((response) => {
      if (response.isConfirmed) {
        this.deleteArticle();
      } else {
        Swal.fire('Anulowano', '', 'info');
      }
    });
  }
  public async deleteArticle(): Promise<void> {
    try {
      await post('http://localhost:8080/deleteArticle', { article_id: this.article?.id_article });
      this.$router.push('/');

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
  public async getArticle(): Promise<void> {
    try {
      const response = await post<Article>('http://localhost:8080/getArticle', { article: this.articleId });
      this.article = response;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async addComment() {
    try {
      const newComment = await post<Komentarz>('http://localhost:8080/addComment', {
        article: this.article?.id_article,
        comment: this.newComment,
        user: user.userData?.id_user,
      });
      this.article?.comments.push(newComment);
      PrintSuccess();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async deleteComment(c: Komentarz) {
    try {
      await post('http://localhost:8080/deleteComment', { commentId: c.id });
      this.article?.comments.splice(this.article?.comments.indexOf(c), 1);
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }

  public async saveChangesArticle() {
    try {
      await post('http://localhost:8080/saveChangesArticle', { article: this.article });
      this.disable = !this.disable;
      PrintSuccess();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
  mounted() {
    this.getArticle();
  }
}
</script>
