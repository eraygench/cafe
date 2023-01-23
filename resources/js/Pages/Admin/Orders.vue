<template>
    <div>
        <div class="flex flex-wrap text-gray-900 text-sm font-medium first-child:ml-2">
            <div v-for="(order, index) in orders" :key="index"
                 class="bg-white px-4 py-2 w-1/4 h-full cursor-pointer shadow-md rounded-lg">
                <span class="col-span-2 text-start line-clamp-2 break-all">{{ order.desk.section.name }} {{ order.desk.name }}</span>
                <div class="flex flex-col">
                    <div v-for="detail in order.details" class="flex items-center justify-between">
                        <span>{{ detail.product_name }}</span>
                        <span>{{ detail.quantity }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="orders.length === 0"
             class="text-center w-full text-amber-500 font-semibold">
            No active orders found
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return ({
            orders: [{
                "desk": {
                    "id": 16,
                    "organization_id": 1,
                    "section_id": 1,
                    "name": "16",
                    "active": true,
                    "created_at": "2022-11-28T07:17:58.000000Z",
                    "updated_at": "2022-11-28T07:17:58.000000Z",
                    "section": {
                        "id": 1,
                        "organization_id": 1,
                        "name": "salon",
                        "active": true,
                        "created_at": "2022-11-28T07:14:00.000000Z",
                        "updated_at": "2022-11-28T14:26:39.000000Z"
                    }
                },
                "details": [
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    }
                ]
            },{
                "desk": {
                    "id": 16,
                    "organization_id": 1,
                    "section_id": 1,
                    "name": "16",
                    "active": true,
                    "created_at": "2022-11-28T07:17:58.000000Z",
                    "updated_at": "2022-11-28T07:17:58.000000Z",
                    "section": {
                        "id": 1,
                        "organization_id": 1,
                        "name": "salon",
                        "active": true,
                        "created_at": "2022-11-28T07:14:00.000000Z",
                        "updated_at": "2022-11-28T14:26:39.000000Z"
                    }
                },
                "details": [
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    }
                ]
            },{
                "desk": {
                    "id": 16,
                    "organization_id": 1,
                    "section_id": 1,
                    "name": "16",
                    "active": true,
                    "created_at": "2022-11-28T07:17:58.000000Z",
                    "updated_at": "2022-11-28T07:17:58.000000Z",
                    "section": {
                        "id": 1,
                        "organization_id": 1,
                        "name": "salon",
                        "active": true,
                        "created_at": "2022-11-28T07:14:00.000000Z",
                        "updated_at": "2022-11-28T14:26:39.000000Z"
                    }
                },
                "details": [
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    }
                ]
            },{
                "desk": {
                    "id": 16,
                    "organization_id": 1,
                    "section_id": 1,
                    "name": "16",
                    "active": true,
                    "created_at": "2022-11-28T07:17:58.000000Z",
                    "updated_at": "2022-11-28T07:17:58.000000Z",
                    "section": {
                        "id": 1,
                        "organization_id": 1,
                        "name": "salon",
                        "active": true,
                        "created_at": "2022-11-28T07:14:00.000000Z",
                        "updated_at": "2022-11-28T14:26:39.000000Z"
                    }
                },
                "details": [
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    },
                    {
                        "id": "e27e3d95-337c-4bd7-9d0f-96395457cb67",
                        "product_id": 1,
                        "product_name": "1",
                        "price": 1,
                        "quantity": 1
                    }
                ]
            }]
        });
    },
    methods: {
        uuidv4() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }
    },
    mounted() {
        Echo.channel(`OrderChannel.${this.$page.props.auth.user.organization_id}`)
            .listen('NewOrder', function (event) {
                if (!!event.desk) {
                    console.log(JSON.stringify(event))
                    this.orders.push(event)
                }
            }.bind(this))
    }
}
</script>
