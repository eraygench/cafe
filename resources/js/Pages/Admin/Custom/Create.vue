<template>
    <form @submit.prevent="id ? form.put(routes.update) : form.post(routes.create)" class="mx-auto w-full max-w-[550px] flex flex-col gap-y-4">
        <Head :title="form.name ? form.name : 'New'" />

        <h1 v-if="header" class="text-3xl py-4 text-center" v-text="header"/>
        <div v-for="field in fields">
            <label
                :for="field.name"
                v-text="field.label"
                class="mb-1 block text-base font-medium text-[#07074D]"
            />
            <input
                v-if="!['radio','select'].includes(field.type)"
                :type="field.type ? field.type : 'text'"
                :id="field.name"
                v-model="form[field.name]"
                :placeholder="field.placeholder"
                :class="{'w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md': !['radio','checkbox'].includes(field.type), 'h-5 w-5': ['radio','checkbox'].includes(field.type)}"
            />
            <select
                v-if="field.type === 'select'"
                :id="field.name"
                v-model="form[field.name]"
                class="w-full appearance-none rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md"
            >
                <option v-if="field.optional" value="">None</option>
                <option v-for="itemKey in Object.keys(field.items)" :value="itemKey" :key="itemKey" v-text="field.items[itemKey]" />
            </select>
            <div v-if="field.type === 'radio'" class="flex items-center space-x-6">
                <div v-for="itemKey in Object.keys(field.items)" class="flex items-center">
                    <input
                        type="radio"
                        :id="field.name + '_' + itemKey"
                        v-model="form[field.name]"
                        :value="field.items[itemKey]"
                        class="h-5 w-5"
                    />
                    <label
                        :for="field.name + '_' + itemKey"
                        v-text="field.items[itemKey]"
                        class="pl-3 text-base font-medium text-[#07074D]"
                    />
                </div>
            </div>
            <p v-if="form.errors[field.name]" class="text-xs text-red-500 mt-3" v-text="form.errors[field.name]" />
        </div>

        <div class="flex gap-x-4">
            <button
                type="submit" :disabled="form.processing"
                class="hover:shadow-form rounded-md bg-[#6A64F1] py-2 px-6 text-center text-base font-semibold text-white outline-none"
            >
                Save
            </button>
            <Link
                :href="routes.cancel" as="button"
                class="hover:shadow-form rounded-md bg-gray-400 py-2 px-6 text-center text-base font-semibold text-white outline-none"
            >
                Cancel
            </Link>
        </div>
    </form>
</template>

<script>
export default {
    props: {
        header: String,
        fields: Array,
        routes: Object,
        id: Number,
        items: Object
    },
    data() {
        return {
            form: this.$inertia.form(this.fields.reduce((obj, field) => ({...obj, [field.name]: field.value ?? ''}), {})),
        }
    }
}
</script>
