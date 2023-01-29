<template>
  <v-card class="pa-5 ma-5" align="center" v-if="articles != null">
    <v-img class="my-5" src="@/../public/news.svg" max-width="300"></v-img>
    <v-row>
      <v-col cols="4">
        <router-link v-if="articleSide1 != undefined" :to="`/article/${articleSide1.id_article}`">
          <v-card class="pa-2 ma-5" height="50%">
            <v-card-text class="pa-0" style="word-break: break-word"
              ><v-img :src="require('../assets/' + articleSide1.photo)" contain></v-img
            ></v-card-text>
            <v-card-title class="font-weight-bold" style="word-break: break-word">{{
              articleSide1.title
            }}</v-card-title>
            <v-card-subtitle>{{ articleSide1.subtitle.slice(0, 150) + '...' }}</v-card-subtitle>
          </v-card>
        </router-link>
        <router-link v-if="articleSide2 != undefined" :to="`/article/${articleSide2.id_article}`">
          <v-card class="pa-2 ma-5" height="50%">
            <v-card-text class="pa-0" style="word-break: break-word"
              ><v-img :src="require('../assets/' + articleSide2.photo)"></v-img
            ></v-card-text>
            <v-card-title class="font-weight-bold" style="word-break: break-word">{{
              articleSide2.title
            }}</v-card-title>
            <v-card-subtitle class="">{{ articleSide2.subtitle.slice(0, 150) + '...' }}</v-card-subtitle>
          </v-card>
        </router-link>
      </v-col>
      <v-col cols="8">
        <router-link v-if="articleBig != undefined" :to="`/article/${articleBig.id_article}`">
          <v-card class="pa-2 ma-5" height="100%">
            <v-card-text class="pa-0" style="word-break: break-word"
              ><v-img :src="require('../assets/' + articleBig.photo)" max-height="480px" contain></v-img
            ></v-card-text>
            <v-card-title class="text-h4 font-weight-bold justify-center mb-5" style="word-break: break-word">{{
              articleBig.title
            }}</v-card-title>
            <v-card-subtitle class="text-h6">{{ articleBig.subtitle }}</v-card-subtitle>
          </v-card>
        </router-link>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="4" v-for="num in 3" :key="num">
        <router-link
          v-if="articles[page * 6 - 4 + num] != undefined"
          :to="`/articles/${articles[page * 6 - 4 + num].id_article}`"
        >
          <v-card class="pa-2 ma-5" v-if="articles[page * 6 - 4 + num] != undefined">
            <v-card-text class="pa-0" style="word-break: break-word"
              ><v-img :src="require('../assets/' + articles[page * 6 - 4 + num].photo)"></v-img
            ></v-card-text>
            <v-card-title>{{ articles[page * 6 - 4 + num].title }}</v-card-title>
            <v-card-subtitle>{{ articles[page * 6 - 4 + num].subtitle.slice(0, 150) + '...' }}</v-card-subtitle>
          </v-card>
        </router-link>
      </v-col>
    </v-row>
    <v-pagination :length="Math.ceil(articles.length / 6)" v-model="page"></v-pagination>
  </v-card>
</template>
<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { get, PrintError } from '@/lib/fetch/';
import Register from './Register.vue';
import { Article } from '@/lib/types';

@Component({
  name: 'News',
  components: { Register },
})
export default class extends Vue {
  public articles: Article[] = [];

  public page = 1;

  get articleSide1() {
    return this.articles[this.page * 6 - 6];
  }
  get articleSide2() {
    return this.articles[this.page * 6 - 5];
  }
  get articleBig() {
    return this.articles[this.page * 6 - 4];
  }

  public async getArticles(): Promise<void> {
    try {
      const response = await get<Article[]>('http://localhost:8080/getArticles');
      this.articles = response;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
    } catch (error: any) {
      PrintError(error);
    }
  }
  mounted() {
    this.getArticles();
  }
}
</script>
<style scoped>
a {
  text-decoration: none;
}
</style>
