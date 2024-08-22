import { createRouter, createWebHistory } from "vue-router";

import InvoiceIndex from "../components/Invoice/index.vue";
import NotFound from "../components/NotFound.vue";
import CreateInvoice from "../components/Invoice/create.vue";
import showInvoice from "../components/Invoice/show..vue";
import EditInvoice from "../components/Invoice/edit.vue";


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
    },
    {
        path: "/invoice/:id/edit",
        component: EditInvoice,
        props: true
    },
    {
        path: "/invoice/:id/show",
        component: showInvoice,
        props: true
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
