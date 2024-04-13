import { createRouter, createWebHistory } from 'vue-router';
import MainPage from '../components/MainPage.vue';
import AddPost from '../components/AddPost.vue';

const routes = [
  {
    path: '/',
    component: MainPage
  },
  {
    path: '/add-post',
    component: AddPost,
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
