import { createRouter, createWebHistory } from 'vue-router';
import MainPage from '../components/MainPage.vue';
import AddPost from '../components/AddPost.vue';
import MinePosts from '../components/MinePosts.vue';
import Login from '../components/Login.vue';

const routes = [
  {
    path: '/',
    component: MainPage,
    beforeEnter() {
      if(localStorage.getItem('user_id') == null)
      return router.push('/login')
    },
  },
  {
    path: '/add-post',
    component: AddPost,
    beforeEnter() {
      if(localStorage.getItem('user_id') == null)
      return router.push('/login')
    },
  },
  {
    path: '/mine-posts',
    component: MinePosts,
    beforeEnter() {
      if(localStorage.getItem('user_id') == null)
      return router.push('/login')
    },
  },
  {
    path: '/login',
    component: Login,
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
