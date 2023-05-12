import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/movies',
    },
    {
      path: '/movies',
      name: 'movies',
      component: () => import('../views/movie-view.vue')
    },
    {
      path: '/movies/:id/shifts',
      name: 'shifts',
      component: () => import('../views/shift-view.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'notFound',
      component: () => import ('../views/not-found-view.vue') //lazy loading
    }
  ]
})

export default router
