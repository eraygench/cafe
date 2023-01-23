<template>
    <div>
        <div class="mb-4 flex justify-between items-center">
            <div class="flex-1 pr-4">
                <div class="relative md:w-1/3">
                    <input type="search"
                           v-model="search"
                           class="w-full pl-10 pr-4 py-2 rounded-lg shadow focus:outline-none focus:shadow-outline text-gray-600 font-medium"
                           placeholder="Search...">
                    <div class="absolute top-0 left-0 inline-flex items-center p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                            <circle cx="10" cy="10" r="7" />
                            <line x1="21" y1="21" x2="15" y2="15" />
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                <div class="shadow rounded-lg flex">
                    <div class="relative">
                        <Link v-if="routes && routes.create" :href="routes.create"
                              as="button"
                              class="border border-green-500 bg-green-500 text-base text-white rounded-md px-4 py-2 transition duration-500 ease select-none hover:bg-green-600 focus:outline-none focus:shadow-outline"
                        >
                            New
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead>
                <tr class="text-left">
                    <th v-for="heading in headings" class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-2 text-gray-600 font-bold tracking-wider uppercase text-xs"
                        v-text="heading.value"></th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200"></th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data.data" :key="item.id">
                        <td v-for="heading in headings" class="border-dashed border-t border-gray-200">
                            <span class="text-gray-700 px-6 py-2 flex items-center" v-text="item[heading.key]"></span>
                        </td>
                        <td class="border-dashed border-t border-gray-200 whitespace-nowrap w-1">
                            <div class="flex gap-x-2 p-2">
                                <Link
                                    v-if="item.active !== undefined && item.routes && item.routes.activate"
                                    :href="item.routes.activate"
                                    method="PUT"
                                    :data="{ active: !item.active }"
                                    as="button"
                                    :class="{ 'border-cyan-500 bg-cyan-500 hover:bg-cyan-600': !item.active, 'border-gray-400 bg-gray-400 hover:bg-gray-500': item.active }"
                                    class="border  text-white rounded-md px-4 py-2 transition duration-500 ease select-none focus:outline-none focus:shadow-outline"
                                >
                                    {{ item.active ? 'Deactive' : 'Active' }}
                                </Link>
                                <Link
                                    v-if="buttons.show && item.routes && item.routes.show"
                                    :href="item.routes.show"
                                    as="button"
                                    class="border border-lime-500 bg-lime-500 text-white rounded-md px-4 py-2 transition duration-500 ease select-none hover:bg-lime-600 focus:outline-none focus:shadow-outline"
                                >
                                    Show
                                </Link>
                                <Link
                                    v-if="buttons.edit && item.routes && item.routes.edit"
                                    :href="item.routes.edit"
                                    as="button"
                                    class="border border-indigo-500 bg-indigo-500 text-white rounded-md px-4 py-2 transition duration-500 ease select-none hover:bg-indigo-600 focus:outline-none focus:shadow-outline"
                                >
                                    Edit
                                </Link>
                                <button
                                    v-if="buttons.delete && item.routes && item.routes.delete"
                                    @click="deleteItem(item.routes.delete)"
                                    type="button"
                                    class="border border-red-500 bg-red-500 text-white rounded-md px-4 py-2 transition duration-500 ease select-none hover:bg-red-600 focus:outline-none focus:shadow-outline"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination class="font-sans flex justify-end space-x-1 mt-8 select-none" :links="data.links" />
    </div>
</template>

<script>
import { defineAsyncComponent } from "vue"
import { router } from "@inertiajs/vue2";
import debounce from "lodash/debounce";

let Pagination = defineAsyncComponent(() => import("@/Shared/Pagination"));

export default {
    components: {Pagination},
    props: {
        data: Object,
        filter: String,
        can: Object,
        columns: Object,
        actions: Object|Boolean,
        routes: Object
    },
    data() {
        return {
            search: this.filter,
            buttons: {
                edit: ((typeof this.actions === 'object' && !Array.isArray(this.actions) ? (this.actions.edit ?? true) : this.actions) ?? true),
                delete: ((typeof this.actions === 'object' && !Array.isArray(this.actions) ? (this.actions.delete ?? true) : this.actions) ?? true),
                show: ((typeof this.actions === 'object' && !Array.isArray(this.actions) ? (this.actions.show ?? false) : this.actions) ?? false),
            },
            headings: Object.keys(this.columns).map(key => ({"key": key, "value": this.columns[key]})),
        }
    },
    methods: {
        async deleteItem(route) {
            this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(({isConfirmed}) => {
                if(isConfirmed)
                    router.delete(route, {}, {preserveState: true, replace: true, preserveScroll: true});
            })
        }
    },
    watch: {
        search: {
            handler: debounce(function (value) {
                router.get(this.routes.search, {search: value}, {preserveState: true, replace: true, preserveScroll: true});
            }, 300),
            deep: true
        }
    }
}
</script>
