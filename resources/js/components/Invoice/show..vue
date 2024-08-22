<template>
    <div class="container">
        <div class="invoices">

        <div class="card__header">
            <div>
                <h2 class="invoice__title">Invoice</h2>
            </div>
            <div>

            </div>
        </div>
        <div>
            <div class="card__header--title ">
                <h1 class="mr-2">#{{ invoice.number }}</h1>
                <p>{{ moment(invoice.created_at).format("MMMM D, YYYY [at] h:mm a") }}</p>
            </div>

            <div>
                <ul  class="card__header-list">
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat" @click="print()">
                            <i class="fas fa-print"></i>
                            Print
                        </button>
                        <!-- End Select Btn Option -->
                    </li>
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat" @click="onEdit">
                            <i class=" fas fa-reply"></i>
                            Edit
                        </button>
                        <!-- End Select Btn Option -->
                    </li>
                    <li>
                        <!-- Select Btn Option -->
                        <button class="selectBtnFlat" @click="onDelete">
                            <i class=" fas fa-pencil-alt"></i>
                            Delete
                        </button>
                        <!-- End Select Btn Option -->
                    </li>

                </ul>
            </div>
        </div>

        <div class="table invoice">
            <div class="logo">
                <img src="assets/img/logo.png" alt="" style="width: 200px;">
            </div>
            <div class="invoice__header--title">
                <p></p>
                <p class="invoice__header--title-1">Invoice</p>
                <p></p>
            </div>


            <div class="invoice__header--item">
                <div>
                    <h2>Invoice To:</h2>
                    <p>{{ invoice.customer?.firstname ?? "No customer" }}</p>
                </div>
                <div>
                        <div class="invoice__header--item1">
                            <p>Invoice#</p>
                            <span>#{{ invoice.number }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Date</p>
                            <span>{{ invoice.date }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Due Date</p>
                            <span>{{ invoice.due_date }}</span>
                        </div>
                        <div class="invoice__header--item2">
                            <p>Reference</p>
                            <span>{{ invoice.reference ?? "None" }}</span>
                        </div>

                </div>
            </div>

            <div class="table py1">

                <div class="table--heading3">
                    <p>#</p>
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                </div>

                <!-- item 1 -->
                <div class="table--items3" v-for="product in invoice.products" :key="product.id">
                    <p>{{ product.id }}</p>
                    <p>{{ product.description }}</p>
                    <p>$ {{ product.pivot.unit_price }}</p>
                    <p>{{ product.pivot.quantity }}</p>
                    <p>$ {{ product.pivot.unit_price * product.pivot.quantity }}</p>
                </div>
            </div>

            <div  class="invoice__subtotal">
                <div>
                    <h2>Thank you for your business</h2>
                </div>
                <div>
                    <div class="invoice__subtotal--item1">
                        <p>Sub Total</p>
                        <span> $ {{ invoice.sub_total }}</span>
                    </div>
                    <div class="invoice__subtotal--item2">
                        <p>Discount</p>
                        <span>$ {{ invoice.discount }}</span>
                    </div>

                </div>
            </div>

            <div class="invoice__total">
                <div>
                    <h2>Terms and Conditions</h2>
                    <p>{{ invoice.terms_and_conditions }}</p>
                </div>
                <div>
                    <div class="grand__total" >
                        <div class="grand__total--items">
                            <p>Grand Total</p>
                            <span>$ {{ invoice.total }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card__footer">
            <div>

            </div>
            <div>
                <a class="btn btn-secondary">
                    Save
                </a>
            </div>
        </div>

    </div>

    <br><br><br>
    </div>
</template>
<script setup>

import { onMounted, ref } from 'vue';
import moment from 'moment';
import { useRouter } from 'vue-router';
import confirm from '../../utiils/confirmation';

const router = useRouter();
const invoice = ref([]);
import Swal from 'sweetalert2';

let props = defineProps({
    id: {
        type: String,
        default: ""
    }
})

onMounted( async () => {
    const response = await axios.get(`/api/invoice/${props.id}/get`);
    invoice.value = response.data;
})

const print = () => {
    window.print();
}

const deleteInvoice = async () => {
    const response = await axios.delete(`/api/invoice/${props.id}/delete`);
    if(response.data) {
        router.push("/");
    }
}
const onDelete = () => {
    Swal.fire({
        title: "Do you want to delete it?",
        showCancelButton: true,
        confirmButtonText: "Save",
        denyButtonText: `Don't save`
    }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        deleteInvoice()
    }
    });
}

const onEdit = () => {
    router.push(`/invoice/${props.id}/edit`);
}
</script>
