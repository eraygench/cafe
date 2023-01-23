<template>
    <main class="min-h-full">
        <div class="flex justify-center min-h-screen w-full relative">
            <div class="max-w-xl mx-auto w-full md:max-w-2xl">
                <div class="overflow-auto max-h-screen pb-16 p-2 select-none">
                    <div class="text-2xl">{{ organization.name }}</div>
                    <hr class="my-2"/>
                    <ul class="flex items-center flex-nowrap overflow-x-auto pb-2 snap-x">
                        <li v-for="category in categories" :key="category.id"
                            class="cursor-pointer py-2 px-4 text-gray-500 border-b-4 whitespace-nowrap snap-center"
                            :class="{ 'text-green-500 border-green-500': activeCategory === category.id }"
                            @click="activeCategory = category.id"
                            v-text="category.name"/>
                    </ul>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <button class="bg-gray-400 h-28 rounded relative text-white px-2"
                                @click="addProduct(product)"
                                :class="{ '!bg-emerald-400': cart.find(item => item.product_id === product.id) }"
                                v-for="product in products.filter(d => d.category_id === activeCategory)">
                            <span v-if="cart.find(item => item.product_id === product.id)"
                                  class="absolute top-1 right-1 bg-white text-center text-emerald-400 p-1 rounded-full text-sm leading-none decoration-0">
                                {{ cart.find(item => item.product_id === product.id).quantity }}
                            </span>
                            <span class="line-clamp-2 break-all">{{ product.name }}</span>
                            <span class="absolute left-1 bottom-1 text-xs">{{product.price }}$</span>
                        </button>
                    </div>
                </div>
                <div>
                    <Transition mode="in-out"
                                enter-class="ease-in-out opacity-0 duration-500"
                                enter-to-class="opacity-100 duration-500"
                                leave-class="ease-in-out opacity-100 duration-500"
                                leave-to-class="opacity-0 duration-500">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                             v-if="bottomBar"
                             @click="bottomBar = !bottomBar"/>
                    </Transition>
                    <div :class="{ 'top-10 bottom-0': bottomBar }"
                         class="fixed bottom-0 flex flex-col select-none bg-gray-200 border w-full max-w-xl md:max-w-2xl rounded-t-xl select-none">
                        <div v-if="!bottomBar"
                             class="w-full p-4 bg-gray-300 rounded-t-xl"
                             @click="bottomBar = !bottomBar">
                            <div v-if="!sale">
                                New Order
                            </div>
                            <div v-else>
                                Cart
                            </div>
                        </div>
                        <div v-if="bottomBar" class="p-4 h-full">
                            <div class="p-4 bg-gray-300 flex items-center justify-between -m-4 mb-4 rounded-t-xl group"
                                 @click="bottomBar = !bottomBar">
                                <span>Close</span>
                                <button class="flex -m-2 p-2 text-gray-400 group-hover:text-gray-500">
                                    <span class="sr-only">Close panel</span>
                                    <XIcon/>
                                </button>
                            </div>
                            <div v-if="sale">
                                <div><button @click="save" :disabled="cart.length === 0">Save</button></div>
                                <div class="flex flex-col select-none gap-2">
                                    <div v-for="item in cart" class="flex items-center justify-between gap-2">
                                        <span class="flex flex-1 line-clamp-2 break-all">{{ item.product_name }}</span>
                                        <span class="border border-gray-400 rounded flex gap-1">
                                        <button v-if="item.quantity > 1" class="py-2 px-2 text-red-400" @click="setItemQuantity(item.id, 0)"><TrashIcon /></button>
                                        <button class="py-2 px-2" @click="setItemQuantity(item.id, '-')" :class="{ 'text-red-400': item.quantity === 1 }"><Component :is="item.quantity === 1 ? 'TrashIcon' : 'MinusSmIcon'" /></button>
                                        <span class="py-2 px-2 text-sm flex items-center justify-center w-10">{{ item.quantity }}</span>
                                        <button class="py-2 px-2" @click="setItemQuantity(item.id, '+')"><PlusSmIcon /></button>
                                    </span>
                                        <span class="w-14 text-right">{{ item.quantity * item.price }}$</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <input type="text" placeholder="Code" v-model="code">
                                <button @click="requestDesk">Find</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import { router } from "@inertiajs/vue2";
import {XIcon, TrashIcon, MinusSmIcon, PlusSmIcon} from '@vue-hero-icons/outline'
export default {
    layout: null,
    components: {
        XIcon,
        TrashIcon,
        MinusSmIcon,
        PlusSmIcon
    },
    props: {
        organization: Object,
        categories: Array,
        products: Array,
        sale: String
    },
    data() {
        return {
            cart: [],
            activeCategory: null,
            bottomBar: false,
            code: null
        }
    },
    methods: {
        async requestDesk() {
            if (!this.code) {
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 3000,
                    timerProgressBar: true,
                    title: "Please fill the code",
                    icon: "warning"
                })
                return;
            }
            await router.get(this.route('menu', this.organization.uuid), {ac: this.code}, {
                preserveState: true, replace: true, preserveScroll: true
            })
            console.log(this.sale)
            if (this.sale)
                this.bottomBar = false
        },
        save() {
            // TODO
            router.put(this.route('menu', this.organization.uuid), {
                sale: this.sale,
                details: this.cart
            }, {preserveState: true, replace: false, preserveScroll: true})
        },
        addProduct(product) {
            if (!this.sale) return
            const detail = this.cart.find(item => item.product_id === product.id)
            if (detail)
                detail.quantity++
            else
                this.cart.push({
                    id: this.uuidv4(),
                    product_id: product.id,
                    product_name: product.name,
                    price: product.price,
                    quantity: 1
                })
        },
        setItemQuantity(id, operation) {
            if (operation === "-")
                if (this.cart.find(item => item.id === id).quantity > 1)
                    this.cart.find(item => item.id === id).quantity--
                else {
                    this.cart.splice(this.cart.findIndex(item => item.id === id), 1)
                    if (this.cart.length === 0)
                        this.bottomBar = !this.bottomBar
                }
            else if (operation === "+")
                this.cart.find(item => item.id === id).quantity++
            else if (operation === 0) {
                this.cart.splice(this.cart.findIndex(item => item.id === id), 1)
                if (this.cart.length === 0)
                    this.bottomBar = !this.bottomBar
            }
        },
        uuidv4() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }
    },
    updated() {
        if(this.$page.props.flash.message) {
            this.$swal({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showCloseButton: true,
                timer: 3000,
                timerProgressBar: true,
                title: this.$page.props.flash.message,
                icon: this.$page.props.flash.icon,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', this.$swal.stopTimer)
                    toast.addEventListener('mouseleave', this.$swal.resumeTimer)
                }
            })
            // TODO
            this.$page.props.flash = []
        }
    }
}
</script>
