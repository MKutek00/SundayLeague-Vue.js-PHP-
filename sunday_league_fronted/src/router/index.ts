import Vue from 'vue';
import VueRouter, { RouteConfig } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import { getModule } from 'vuex-module-decorators';
import User from '@/store/modules/User';

Vue.use(VueRouter);

const user = getModule(User);
const ADMIN = 1;
const REDAKTOR = 2;

const routes: Array<RouteConfig> = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/about',
    name: 'about',
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue'),
  },
  {
    path: '/article/:articleId',
    name: 'article',
    component: () => import('@/components/Article.vue'),
  },
  {
    path: '/leagues',
    name: 'leagues',
    component: () => import(/* webpackChunkName: "article" */ '@/components/Leagues.vue'),
  },
  {
    path: '/table/:leagueName',
    name: 'table',
    component: () => import(/* webpackChunkName: "article" */ '@/components/Table.vue'),
  },
  {
    path: '/schedule/:leagueName',
    name: 'schedule',
    component: () => import(/* webpackChunkName: "article" */ '@/components/Schedule.vue'),
  },
  {
    path: '/findGame',
    name: 'findGame',
    component: () => import(/* webpackChunkName: "article" */ '@/components/FindGame.vue'),
  },
  {
    path: '/addArticle',
    name: 'addArticle',
    component: () => import(/* webpackChunkName: "article" */ '@/components/AddArticle.vue'),
    beforeEnter: (to, from, next) => {
      if (user.userData?.role.id_role == ADMIN || user.userData?.role.id_role == REDAKTOR) {
        next();
      } else {
        next('/');
      }
    },
  },
  {
    path: '/allUsers',
    name: 'allUsers',
    component: () => import('@/components/AllUsers.vue'),
    beforeEnter: (to, from, next) => {
      if (user.userData?.role.id_role == ADMIN) {
        next();
      } else {
        next('/');
      }
    },
  },
  {
    path: '/userProfile/:userId',
    name: 'userProfile',
    component: () => import('@/components/UserProfile.vue'),
    beforeEnter: (to, from, next) => {
      if (user.isLoggedIn) {
        next();
      } else {
        next('/');
      }
    },
  },
];
//TODO ew. zmiana na history
const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

export default router;
