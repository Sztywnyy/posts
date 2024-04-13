import { createRouter, createWebHistory } from 'vue-router';
import MainPage from '../components/MainPage.vue';
import AddPost from '../components/AddPost.vue';

const routes = [
  {
    path: '/',
    name: 'MainPage',
    component: MainPage
  },
  {
    path: '/add-post',
    name: 'AddPost',
    component: AddPost,
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
