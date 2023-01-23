<template>
    <div>
        <div class="flex flex-col lg:grid grid-cols-3 gap-4">
            <div>
                <div v-if="desks.filter(d => d.sale).length"
                     class="flex flex-col gap-0.5 bg-gray-200 rounded-lg border-2 border-gray-200 text-gray-900 text-sm font-medium">
                    <button v-for="desk in desks.filter(d => d.sale)" :key="desk.id"
                            @click="openDesk(desk)"
                            :disabled="desk.openedBy"
                            class="bg-white px-4 py-2 w-full grid grid-cols-3 gap-4 transition-all disabled:bg-blue-400 disabled:text-white disabled:cursor-not-allowed hover:bg-gray-100 hover:text-blue-700 cursor-pointer first:rounded-t-lg last:rounded-b-lg">
                        <span class="col-span-2 text-start line-clamp-2 break-all">{{ desk.section.name }} {{ desk.name }}</span>
                        <span class="flex items-center justify-between">
                            <span class="w-auto">{{ desk.sale.hour }}</span>
                            <span>{{ desk.sale.total }}$</span>
                        </span>
                    </button>
                </div>
                <div v-if="desks.filter(d => d.sale).length === 0"
                     class="text-center w-full text-amber-500 font-semibold">
                    No active sales found
                </div>
            </div>
            <div class="md:col-span-2">
                <ul class="flex items-center flex-nowrap overflow-x-auto pb-2 snap-x">
                    <li v-for="section in sections" :key="section.id"
                        class="cursor-pointer py-2 px-4 text-gray-500 border-b-4 whitespace-nowrap snap-center"
                        :class="{ 'text-green-500 border-green-500': activeTab === section.id }"
                        @click="activeTab = section.id"
                        v-text="section.name"/>
                </ul>
                <div v-for="section in sections" :key="section.id"
                     class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 grid-flow-row gap-4 mt-2"
                     v-show="activeTab === section.id">
                    <button class="bg-gray-200 h-32 rounded relative bg-emerald-400 text-white transition-all disabled:bg-blue-400 disabled:cursor-not-allowed px-2"
                            :disabled="desk.openedBy"
                            :class="{ 'bg-red-400': !!desk.sale }"
                            @click="openDesk(desk)"
                            v-for="desk in desks.filter(d => d.section.id === section.id)">
                        <span v-if="desk.sale" class="absolute left-1 top-1 text-xs">{{ desk.sale.hour }}</span>
                        <span class="break-all line-clamp-3">{{ section.name }} {{ desk.name }}</span>
                        <span v-if="desk.sale && desk.sale.details"
                              class="absolute left-1 bottom-1 text-xs">{{ desk.sale.total }}$</span>
                        <span v-if="desk.openedBy" class="absolute right-1 bottom-1 text-xs">{{ desk.openedBy }}</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="relative z-10" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <!--
              Background backdrop, show/hide based on slide-over state.

              Entering: "ease-in-out duration-500"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "ease-in-out duration-500"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <Transition mode="in-out"
                        enter-class="ease-in-out opacity-0 duration-500"
                        enter-to-class="opacity-100 duration-500"
                        leave-class="ease-in-out opacity-100 duration-500"
                        leave-to-class="opacity-0 duration-500">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75"
                     v-if="deskDialog"
                     @click="closeDeskModal"/>
            </Transition>

            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute inset-0 overflow-hidden">
                    <div
                        class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 transform transition translate-x-full ease-in-out duration-500 sm:duration-700"
                        :class="{ 'translate-x-0': deskDialog }">
                        <!--
                          Slide-over panel, show/hide based on slide-over state.

                          Entering: "transform transition ease-in-out duration-500 sm:duration-700"
                            From: "translate-x-full"
                            To: "translate-x-0"
                          Leaving: "transform transition ease-in-out duration-500 sm:duration-700"
                            From: "translate-x-0"
                            To: "translate-x-full"
                        -->
                        <Transition mode="in-out"
                                    enter-class="transform transition ease-in-out translate-x-full duration-500 sm:duration-700"
                                    enter-to-class="translate-x-0 duration-500"
                                    leave-class="transform transition ease-in-out translate-x-0 duration-500 sm:duration-700"
                                    leave-to-class="translate-x-full duration-500">
                            <div class="pointer-events-auto w-screen max-w-md"
                                 :class="{ '!pointer-events-none': !deskDialog }">
                                <div v-if="activeDesk" class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                                    <div class="flex items-center justify-between border-b border-gray-200 py-6 px-4 sm:px-6">
                                        <div v-if="addProductTab != null" class="mr-3 flex h-7 items-center">
                                            <button @click="prevAddProductTab" type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500">
                                                <span class="sr-only">Prev</span>
                                                <ArrowLeftIcon/>
                                            </button>
                                        </div>
                                        <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">
                                            {{ activeDesk.section.name }} {{ activeDesk.name }}
                                        </h2>
                                        <Popper
                                            trigger="hover"
                                            :options="{
                                          placement: 'bottom',
                                          modifiers: { offset: { offset: '0,10px' } }
                                        }">
                                            <div class="popper">
                                                <span v-if="activeDesk.sale.access_code">Code: <span v-text="activeDesk.sale.access_code" /></span>
                                                <span v-else>Open One-Time Access</span>
                                            </div>
                                            <button slot="reference" v-if="addProductTab == null"
                                                    class="flex -m-2 p-2 text-gray-400 hover:text-gray-500"
                                                    @click="createSaleAccessCode">
                                                <span class="sr-only">Close panel</span>
                                                <KeyIcon/>
                                            </button>
                                        </Popper>
                                        <!--                                    <div v-if="addProductTab != null" class="ml-3 flex h-7 items-center">
                                                                                <button type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500">
                                                                                    <span class="sr-only">Next</span>
                                                                                    <ArrowRightIcon/>
                                                                                </button>
                                                                            </div>-->
                                        <button class="flex -m-2 p-2 text-gray-400 hover:text-gray-500"
                                                @click="closeDeskModal">
                                            <span class="sr-only">Close panel</span>
                                            <XIcon/>
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-y-auto pt-4 pb-6 px-4 sm:px-6">

                                        <div v-if="addProductTab == null">
                                            <div class="flow-root">

                                                <ul role="list" class="flex flex-col divide-y divide-gray-200 gap-2">
                                                    <li v-for="item in activeDesk.sale.details" :key="item.id"
                                                        class="flex pt-2">
                                                        <div
                                                            class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200"
                                                            v-if="item.product.image">
                                                            <img
                                                                src="https://tailwindui.com/img/ecommerce-images/shopping-cart-page-04-product-01.jpg"
                                                                class="h-full w-full object-cover object-center"/>
                                                        </div>

                                                        <div class="flex flex-1 flex-col">
                                                            <div>
                                                                <div class="flex justify-between text-base font-medium text-gray-900">
                                                                    <h3 class="line-clamp-2 break-all">{{ item.product.name }}</h3>
                                                                    <p class="ml-4">${{ item.quantity * item.price }}</p>
                                                                </div>
                                                                <!--                                                            <p class="mt-1 text-sm text-gray-500">Salmon</p>-->
                                                            </div>
                                                            <div class="flex flex-1 items-center justify-between text-sm">
                                                                <p class="text-gray-500">Qty {{ item.quantity }}</p>

                                                                <div class="flex">
                                                                    <button type="button"
                                                                            class="font-medium text-indigo-600 hover:text-indigo-500 p-1"
                                                                            @click="deleteRow(item)">Remove
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        <div v-else-if="addProductTab === 0" class="select-none">
                                            <ul class="flex items-center flex-nowrap overflow-x-auto pb-2 snap-x">
                                                <li v-for="category in categories" :key="category.id"
                                                    class="cursor-pointer py-2 px-4 text-gray-500 border-b-4 whitespace-nowrap snap-center"
                                                    :class="{ 'text-green-500 border-green-500': addProductCategory === category.id }"
                                                    @click="addProductCategory = category.id"
                                                    v-text="category.name"/>
                                            </ul>
                                            <div class="grid grid-cols-2 gap-4 mt-2">
                                                <button class="bg-gray-400 h-28 rounded relative text-white px-2"
                                                        @click="addProduct(product)"
                                                        :class="{ '!bg-emerald-400': cart.find(item => item.product_id === product.id) }"
                                                        v-for="product in products.filter(d => d.category_id === addProductCategory)">
                                                <span v-if="cart.find(item => item.product_id === product.id)"
                                                      class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 bg-green-500 text-center p-1 rounded-full text-sm leading-none decoration-0">
                                                    {{ cart.find(item => item.product_id === product.id).quantity }}
                                                </span>
                                                    <span class="line-clamp-2 break-all">{{ product.name }}</span>
                                                    <span class="absolute left-1 bottom-1 text-xs">{{product.price }}$</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div v-else>
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
                                    </div>

                                    <div class="border-t border-gray-200 py-6 px-4 sm:px-6"
                                         v-if="activeDesk.sale.id && addProductTab == null">
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <p>Total</p>
                                            <p>${{ activeDesk.sale.total }}</p>
                                        </div>
                                        <div class="mt-6">
                                            <button @click="checkout"
                                                    :disabled="activeDesk.sale.details.length === 0"
                                                    class="flex items-center justify-center w-full rounded-md border border-transparent bg-indigo-600 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:text-gray-100 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">
                                                Checkout
                                            </button>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 py-6 px-4 sm:px-6">
                                        <div>
                                            <button
                                                v-if="addProductTab === 1"
                                                @click="saveDesk"
                                                class="flex items-center justify-center w-full rounded-md border border-transparent bg-emerald-500 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-emerald-600">
                                                Save
                                            </button>
                                            <button
                                                v-else
                                                @click="addProductTab = addProductTab === 0 ? 1 : 0"
                                                :disabled="addProductTab === 0 && cart.length === 0"
                                                class="flex items-center justify-center w-full rounded-md border border-transparent bg-emerald-500 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:text-gray-100 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-emerald-600">
                                                <span v-if="addProductTab === 0">Open Cart</span>
                                                <span v-else-if="addProductTab == null">Add Product</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue2";
import Popper from 'vue-popperjs';
import {TrashIcon, XIcon, ArrowLeftIcon, ArrowRightIcon, MinusSmIcon, PlusSmIcon, KeyIcon} from '@vue-hero-icons/outline'
import 'vue-popperjs/dist/vue-popper.css';

export default {
    props: {
        sections: Array,
        _desks: Array,
        categories: Array,
        products: Array,
    },
    components: {
        TrashIcon,
        XIcon,
        ArrowLeftIcon,
        ArrowRightIcon,
        MinusSmIcon,
        PlusSmIcon,
        KeyIcon,
        Popper
    },
    data() {
        return ({
            activeTab: this.sections?.[0].id,
            activeDesk: null,
            deskDialog: false,
            desks: this._desks.map(desk => {
                if (desk.sale)
                    desk.sale.hour = new Date(desk.sale.created_at).toTimeString().substring(0, 5)
                return desk
            }),
            addProductTab: null,
            addProductCategory: this.categories.length ? this.categories[0].id : null,
            cart: [],
            channel: null
        });
    },
    methods: {
        openDesk(desk) {
            this.activeDesk = {
                ...structuredClone(desk),
                sale: structuredClone(desk.sale ?? {
                    desk_id: desk.id,
                    details: []
                })
            }
            this.deskDialog = true
            this.channel?.whisper('DeskOpen', {deskId: this.activeDesk.id, name: this.$page.props.auth.user.name})
        },
        async createSaleAccessCode() {
            if ((await this.$swal({
                title: this.activeDesk.sale.access_code ? "Do you want to turn off one-time access?" : "Do you want to open one-time access?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })).isConfirmed) {
                await router.put(this.route('plan.update', this.activeDesk.id), {
                    ...this.activeDesk.sale,
                    details: [],
                    access_code: this.activeDesk.sale.access_code ? null : true
                }, {preserveState: false, replace: true, preserveScroll: true})
                this.channel?.whisper('DeskClose', {deskId: this.activeDesk.id})
            }
        },
        checkout() {
            router.put(this.route('plan.update', this.activeDesk.id), {
                ...this.activeDesk.sale,
                status: true
            }, {preserveState: false, replace: true, preserveScroll: true})
        },
        async closeDeskModal() {
            if (this.cart.length === 0 || (this.cart.length > 0 && (await this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, close it!'
            })).isConfirmed)) {
                this.channel?.whisper('DeskClose', {deskId: this.activeDesk.id})
                this.addProductTab = null
                this.deskDialog = false
                this.cart = []
                this.addProductCategory = this.categories.length ? this.categories[0].id : null
                setTimeout(() => this.activeDesk = null, 500)
            }
        },
        async saveDesk() {
            await router.put(this.route('plan.update', this.activeDesk.id), {
                ...this.activeDesk.sale,
                details: this.cart
            }, {preserveState: false, replace: false, preserveScroll: true})
            this.channel?.whisper('DeskClose', {deskId: this.activeDesk.id})
        },
        async prevAddProductTab() {
            if (this.addProductTab === 1)
                this.addProductTab = 0
            else {
                if (this.cart.length === 0 || (this.cart.length > 0 && (await this.$swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, back to the desk!'
                })).isConfirmed)) {
                    this.cart = []
                    this.addProductTab = null
                    this.addProductCategory = this.categories.length ? this.categories[0].id : null
                }
            }
        },
        addProduct(product) {
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
                        this.addProductTab = 0
                }
            else if (operation === "+")
                this.cart.find(item => item.id === id).quantity++
            else if (operation === 0) {
                this.cart.splice(this.cart.findIndex(item => item.id === id), 1)
                if (this.cart.length === 0)
                    this.addProductTab = 0
            }
        },
        deleteRow(row) {
            this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(async ({isConfirmed}) => {
                if (isConfirmed) {
                    await router.delete(this.route('plan.delete', [this.activeDesk.id, row.id]), {
                        preserveState: false,
                        replace: true,
                        preserveScroll: true
                    })
                    this.channel?.whisper('DeskClose', {deskId: this.activeDesk.id})
                }
            })
        },
        uuidv4() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }
    },
    mounted() {
        Pusher.logToConsole = true;
        Echo.channel(`SaleChannel.${this.$page.props.auth.user.organization_id}`)
            .listen('NewSale', function (event) {
                if (!!event.deskId) {
                    const desk = this.desks.find(desk => desk.id === event.deskId)
                    if (desk) {
                        desk.sale = event.sale
                        if (desk.sale) {
                            desk.sale.hour = new Date(desk.sale.created_at).toTimeString().substring(0, 5)
                        }
                    }
                }
            }.bind(this))

        this.channel = Echo.private(`SaleChannel.${this.$page.props.auth.user.organization_id}`)
            .listenForWhisper('DeskOpen', function (event) {
                if (!!event.deskId) {
                    const desk = this.desks.find(desk => desk.id === event.deskId)
                    if (desk) {
                        desk.openedBy = event.name
                    }
                }
            }.bind(this))
            .listenForWhisper('DeskClose', function (event) {
                if (!!event.deskId) {
                    const desk = this.desks.find(desk => desk.id === event.deskId)
                    if (desk) {
                        desk.openedBy = null
                    }
                }
            }.bind(this))
    }
}
</script>
