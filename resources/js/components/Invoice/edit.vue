
<script setup>

import { onMounted, ref, inject } from 'vue';
import { useRouter } from 'vue-router';

let props = defineProps({
    id: {
        type: String,
        default: ""
    }
})

const router = useRouter();
const form = ref([]);
const customers = ref([]);
const products = ref([]);
const listCart = ref([]);
const isOpen = ref(false);
let isLoading = inject("isLoading");
const errorMessages = ref();

const indexForm = async () => {
    const response = await axios.get(`/api/invoice/${props.id}/get`);
    form.value = response.data;
    const products = response.data.products.map(product => {
        return {...product, quantity: product.pivot.quantity ?? 0}
    })
    listCart.value = products;
    console.log(form.value);
}
let totalSum = ref();

const getCustomers = async () => {
    const response = await axios.get("/api/customers");
    customers.value = response.data;
}

const getProducts = async () => {
    const response = await axios.get("/api/products");
    products.value = response.data.products;
    console.log(response.data);
}
onMounted(async () => {
    await indexForm();
    await getCustomers();
    await getProducts();
    isLoading.value = false;
});

const addToCart = item => {
    const itemcart = {
        id: item.id,
        item_code: item.item_code,
        description: item.description,
        unit_price: item.unit_price,
        quantity: item?.quantity ?? 1
    }

    listCart.value.push(itemcart);
}

const removeFromCart = inCartItem => {
    listCart.value = listCart.value.filter(item => item.id !== inCartItem.id);
}

const subTotal = () => {
    let total = 0;
    listCart.value.map(data => {
        total += data.unit_price * data.quantity;
    })
    return total;
}

const submitForm = async () => {
    let subtotal = 0;
    let total = 0;
    if(listCart.value.length) {
        subtotal = subTotal();
        total = subtotal - form.value.discount;
    }

    const formData = new FormData();
    formData.append("subTotal", subtotal);
    formData.append("total", total);
    formData.append("invoice_items", JSON.stringify(listCart.value));
    formData.append("form", JSON.stringify(form.value));

    let response = "";
    try {
        response = await axios.post(`/api/invoice/${props.id}/edit`, formData);
    } catch(e) {
        if(e.response.status === 422) {
            errorMessages.value = e.response.data.errors;
        }
        console.log(e);
    }
    if(response && response.data) {
        router.push("/")
        // console.log(response.data);
    }
}


function is_error(field) {
    return errorMessages.value && errorMessages.value[field];
}

function get_message(field) {
    return errorMessages.value[field][0];
}

</script>

<template>
    <div class="container">
        <div class="invoices">

        <div class="card__header">
            <div>
                <h2 class="invoice__title">New Invoice</h2>
            </div>
            <div>

            </div>
        </div>

        <div class="card__content">
            <div class="card__content--header">
                <div>
                    <p class="my-1">Customer</p>
                    <p style="font-size: 13px; color: red;" v-if="is_error('customer_id')">{{ get_message('customer_id') }}</p>
                    <p style="font-size: 13px;" v-else>&nbsp;</p>

                    <select name="" id="" class="input" :class="{ 'error': is_error('customer_id') }" v-model="form.customer_id">
                        <option disabled>Select Customers</option>
                        <option :value="customer.id" v-for="customer in customers.customers" :key="customer.id">{{ customer.firstname }}</option>
                    </select>
                </div>
                <div>
                    <p class="my-1">Date</p>
                    <p style="font-size: 13px; color: red;" v-if="is_error('date')">{{ get_message('date') }}</p>
                    <p style="font-size: 13px;" v-else>&nbsp;</p>
                    <input id="date" placeholder="dd-mm-yyyy" :class="{ 'error': is_error('date') }" v-model="form.date" type="date" class="input"> <!---->
                    <p class="my-1">Due Date</p>
                    <p style="font-size: 13px; color: red;" v-if="is_error('due_date')">{{ get_message('due_date') }}</p>
                    <p style="font-size: 13px;" v-else>&nbsp;</p>
                    <input id="due_date" v-model="form.due_date" :class="{ 'error': is_error('due_date') }" type="date" class="input">
                </div>
                <div>
                    <p class="my-1">Numero</p>
                    <p style="font-size: 13px; color: red;" v-if="is_error('number')">{{ get_message('number') }}</p>
                    <p style="font-size: 13px;" v-else>&nbsp;</p>
                    <input type="text" class="input" :class="{ 'error': is_error('number') }" v-model="form.number">
                    <p class="my-1">Reference(Optional)</p>
                    <p style="font-size: 13px;" >&nbsp;</p>

                    <input type="text" class="input" v-model="form.reference">
                </div>
            </div>
            <br><br>
            <div class="table">

                <div class="table--heading2">
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                    <p></p>
                </div>

                <!-- item 1 -->
                <div class="table--items2">
                    <p style="color: red;" v-if="is_error('invoice_items')">{{ get_message('invoice_items') }}</p>
                </div>
                <div class="table--items2" v-for="itemcart in listCart" v-if="listCart.length" :key="itemcart.id">
                    <p>#{{ itemcart.item_code }} {{ itemcart.description }}</p>
                    <p>
                        <input type="text" class="input" v-model="itemcart.unit_price">
                    </p>
                    <p>
                        <input type="text" class="input" v-model="itemcart.quantity">
                    </p>
                    <p>
                        $ {{ itemcart.unit_price * (itemcart?.quantity ?? 0) }}
                    </p>
                    <p @click="removeFromCart(itemcart)" style="color: red; font-size: 24px;cursor: pointer;">
                        &times;
                    </p>
                </div>
                <div class="table--items2" v-else>
                    <p>No Product in item</p>
                </div>
                <div style="padding: 10px 30px !important;">
                    <button class="btn btn-sm btn__open--modal" @click="isOpen = true">Add New Line</button>
                </div>
            </div>

            <div class="table__footer">
                <div class="document-footer" >
                    <p>Terms and Conditions</p>
                    <p style="font-size: 13px; color: red; margin-top: 10px;" v-if="is_error('terms_and_conditions')">{{ get_message('terms_and_conditions') }}</p>
                    <textarea cols="50" rows="7" :class="{ 'error': is_error('terms_and_conditions') }" class="textarea" v-model="form.terms_and_conditions"></textarea>
                </div>
                <div>
                    <div class="table__footer--subtotal">
                        <p>Sub Total</p>
                        <span>$ {{ isNaN(subTotal()) ? 0 : subTotal() }}</span>
                    </div>
                    <div class="table__footer--discount">
                        <p>Discount</p>
                        <p style="font-size: 13px; color: red; margin-top: 10px;" v-if="is_error('discount')">{{ get_message("discount") }}</p>
                        <input type="text" v-model="form.discount" class="input">
                    </div>
                    <div class="table__footer--total">
                        <p>Grand Total</p>
                        <span>$ {{ isNaN(subTotal() - form.discount) ? "Invalid" : subTotal() - form.discount }}</span>
                    </div>
                </div>
            </div>


        </div>
        <div class="card__header" style="margin-top: 40px;">
            <div>

            </div>
            <div @click="submitForm()">
                <a class="btn btn-secondary">
                    Save
                </a>
            </div>
        </div>

    </div>
    <!--==================== add modal items ====================-->
    <div class="modal main__modal " :class="{ show: isOpen }">
        <div class="modal__content">
            <span class="modal__close btn__close--modal">×</span>
            <h3 class="modal__title">Add Item</h3>
            <hr><br>
            <div class="modal__items">
                <ul>
                    <li v-for="(item, i) in products" :key="item.id" style="display: grid; grid-template-columns: 30px 350px 15px; align-items: center; padding-bottom: 5px; ">
                        <p>{{ i+1 }}</p>
                        <a href="#">{{ item.item_code }} {{ item.description }}</a>
                        <button @click="addToCart(item)">+</button>
                    </li>
                </ul>
            </div>
            <br><hr>
            <div class="model__footer">
                <button class="btn btn-light mr-2 btn__close--modal" @click="isOpen = false">
                    Cancel
                </button>
                <button class="btn btn-light btn__close--modal " @click="isOpen = false">Save</button>
            </div>
        </div>
    </div>

    <br><br><br>
    </div>
</template>


<style scoped>
.error {
    border: 1px solid red;
}
</style>
