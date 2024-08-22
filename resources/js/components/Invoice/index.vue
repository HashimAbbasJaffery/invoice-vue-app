<script setup>

import { onMounted, ref, watch, reactive, inject } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import objectToQueryString from '../../utiils/objToQuery';

const router = useRouter();
let isLoading = inject("isLoading");

const invoices = ref([]);
// const keyword = ref("");
const links = ref();
// const type = ref(1);
// const is_paid = ref("all");

onMounted(async () => {
    try {
        await getInvoices();
    } catch(e) {
        console.log(e);
    }

    isLoading.value = false;
})

const filters = reactive({
    keyword: "",
    type: 1,
    is_paid: "all"
});

// keyword=${keyword.value}&type=${type.value}&is_paid=${is_paid.value}
const getInvoices = async (link = `/api/invoices?${objectToQueryString(filters)}`) => {
    const response = await axios.get(`${link}&${objectToQueryString(filters)}`);
    console.log(response.data.invoices.data);
    invoices.value = response.data.invoices.data;
    links.value = response.data.invoices.links;
}

const search_invoice = async () => {
    const response = await axios.get(`/api/search_invoice?${objectToQueryString(filters)}`);
    invoices.value = response.data.invoices.data;
    links.value = response.data.invoices.links;
}

const add_invoice = async () => {
    isLoading.value = true;
    router.push("/invoice/create");
}

const showInvoice = id => {
    isLoading.value = true;
    router.push(`/invoice/${id}/show`);
}

watch(filters, function() {
    search_invoice();
})


</script>
<template v-show="isLoading">
    <div class="container">
        <div class="invoices">

        <div class="card__header">
            <div>
                <h2 class="invoice__title">Invoices</h2>
            </div>
            <div>
                <a class="btn btn-secondary" @click="add_invoice">
                    New Invoice
                </a>
            </div>
        </div>

        <div class="table card__content">
            <div class="table--filter">
                <span class="table--filter--collapseBtn ">
                    <i class="fas fa-ellipsis-h"></i>
                </span>
                <div class="table--filter--listWrapper">
                    <ul class="table--filter--list">
                        <li>
                            <p style="cursor: pointer;" class="table--filter--link" @click="filters.is_paid = 'all'" :class="{ 'table--filter--link--active': filters.is_paid === 'all' }">
                                All
                            </p>
                        </li>
                        <li>
                            <p style="cursor: pointer;" class="table--filter--link " @click="filters.is_paid = 'paid'" :class="{ 'table--filter--link--active': filters.is_paid === 'paid'}">
                                Paid
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table--search">
                <div class="table--search--wrapper">
                    <select class="table--search--select" v-model="filters.type" name="" id="">
                        <option value="1">Filter By Customer</option>
                        <option value="2">Filter By Number</option>
                        <option value="3">Filter By Total</option>
                        <option value="4">Filter By Date</option>
                        <option value="5">Filter By Due Date</option>
                    </select>
                </div>
                <div class="relative">
                    <i class="table--search--input--icon fas fa-search "></i>
                    <input class="table--search--input" v-model="filters.keyword" type="text" placeholder="Search invoice">
                </div>
            </div>

            <div class="table--heading">
                <p>ID</p>
                <p>Date</p>
                <p>Number</p>
                <p>Customer</p>
                <p>Due Date</p>
                <p>Total</p>
            </div>

            <!-- item 1 -->
            <div class="table--items" v-if="invoices.length" v-for="item in invoices" :key="invoices.id" >
                <a @click="showInvoice(item.id)" class="table--items--transactionId">#{{ item.id }}</a>
                <p>{{ item.date }}</p>
                <p>#{{ item.number }}</p>
                <p v-if="item.customer">{{ item.customer.firstname }}</p>
                <p v-else>No Customer</p>
                <p>{{ item.due_date }}</p>
                <p> $ {{ item.total }}</p>
            </div>
            <div class="table--items" v-else>
                <p>Invoice Not Found!</p>
            </div>
            <nav aria-label="Page navigation example" v-if="invoices.length" style="display: flex;margin-top: 10px; margin-left: 10px;">
                <button @click="getInvoices(link.url)" :disabled="!link.url" v-for="link in links" class="page-item" :class="{ 'active': link.active }" style="padding: 5px 10px;  border: 1px solid black;"><a disabled class="page-link" style="text-decoration: none;" href="#" v-html="link.label"></a></button>
            </nav>
        </div>

    </div>
    </div>
</template>
<style scoped>
.page-item {
    margin: 0px 2px;
}
.active {
    background: black;
}
.active a {
    color: white;
}
button:disabled {
    background: lightgray;
    color: black;
}
</style>
