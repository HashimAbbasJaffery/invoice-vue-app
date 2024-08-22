<script setup>

import { onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
const router = useRouter();

const invoices = ref([]);
const keyword = ref("");
const links = ref();
const type = ref(1);
const is_paid = ref();

onMounted(async () => {
    try {
        getInvoices();
    } catch(e) {
        console.log(e);
    }
})

const getInvoices = async (link = `/api/invoices?keyword=${keyword.value}&type=${type.value}&is_paid=${is_paid.value}`) => {
    const response = await axios.get(`${link}&keyword=${keyword.value}&type=${type.value}&is_paid=${is_paid.value}`);
    console.log(response.data.invoices.data);
    invoices.value = response.data.invoices.data;
    links.value = response.data.invoices.links;
    console.log(links.value)
}

const search_invoice = async () => {
    const response = await axios.get(`/api/search_invoice?keyword=${keyword.value}&type=${type.value}&is_paid=${is_paid.value}`);
    invoices.value = response.data.invoices.data;
    links.value = response.data.invoices.links;
}

const add_invoice = async () => {
    router.push("/invoice/create");
}

const showInvoice = id => {
    router.push(`/invoice/${id}/show`);
}

watch(keyword, function() {
    search_invoice();
})
watch(type, function() {
    search_invoice();
})
watch(is_paid, function() {
    alert(is_paid.value)
    search_invoice();
})


</script>
<template>
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
                            <p style="cursor: pointer;" class="table--filter--link" @click="is_paid = 0" :class="{ 'table--filter--link--active': !is_paid }">
                                All
                            </p>
                        </li>
                        <li>
                            <p style="cursor: pointer;" class="table--filter--link " @click="is_paid = 1" :class="{ 'table--filter--link--active': is_paid }">
                                Paid
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table--search">
                <div class="table--search--wrapper">
                    <select class="table--search--select" v-model="type" name="" id="">
                        <option value="1">Filter By Customer</option>
                        <option value="2">Filter By Number</option>
                        <option value="3">Filter By Total</option>
                        <option value="4">Filter By Date</option>
                        <option value="5">Filter By Due Date</option>
                    </select>
                </div>
                <div class="relative">
                    <i class="table--search--input--icon fas fa-search "></i>
                    <input class="table--search--input" v-model="keyword" type="text" placeholder="Search invoice">
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
            <nav aria-label="Page navigation example" v-if="invoices.length" style="margin-top: 10px; margin-left: 10px;">
                <ul class="pagination" style="display: flex;">
                    <li @click="getInvoices(link.url)" v-for="link in links" class="page-item" :class="{ 'active': link.active }" style="padding: 5px 10px;  border: 1px solid black;"><a class="page-link" style="text-decoration: none;" href="#" v-html="link.label"></a></li>
                </ul>
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
</style>
