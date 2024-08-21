import { createRouter, createWebHistory } from "vue-router";

import InvoiceIndex from "../components/Invoice/index.vue";
import NotFound from "../components/NotFound.vue";
import CreateInvoice from "../components/Invoice/create.vue";


const routes = [
    {
        path: "/",
        component: InvoiceIndex
    },
    {
        path: '/:pathMatch(.*)*',
        component: NotFound
    },
    {
        path: "/invoice/create",
        component: CreateInvoice
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;