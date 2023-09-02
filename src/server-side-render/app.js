import { createPinia } from 'pinia'
import { createSSRApp } from 'vue'
import Application from './Application.vue'
import { createRouter } from './router'

export function createApp(props = {}) {

    const app = createSSRApp(Application)
    const pinia = createPinia();
    const router = createRouter(props);
    app.use(pinia)
    app.use(router)

    return { app, router }
}
