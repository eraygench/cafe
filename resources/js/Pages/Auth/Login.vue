<template>
    <section class="text-gray-600 body-font">
        <Head title="Login" />
        <div class="container px-5 py-24 mx-auto flex flex-wrap items-center">
            <div class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:mx-auto w-full mt-10 md:mt-0">
                <h2 class="text-gray-900 text-lg font-medium title-font mb-5 text-center">Login</h2>
                <form @submit.prevent="form.post(route('login'))">
                    <div class="relative mb-4">
                        <label for="email" class="leading-7 text-sm text-gray-600">Email</label>
                        <input type="email" id="email" v-model="form.email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        <p v-if="form.errors.email" class="text-xs text-red-500 mt-3">{{ form.errors.email }}</p>
                    </div>
                    <div class="relative mb-4">
                        <label for="pass" class="leading-7 text-sm text-gray-600">Password</label>
                        <input type="password" id="pass" v-model="form.password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        <p v-if="form.errors.password" class="text-xs text-red-500 mt-3">{{ form.errors.password }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg w-full">Login</button>
                </form>
                <Link :href="route('register')" as="button" class="text-white bg-purple-500 border-0 py-2 px-8 mt-2 focus:outline-none hover:bg-purple-600 rounded text-lg w-full">Register</Link>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    layout: null,
    data() {
        return {
            form: this.$inertia.form({
                email: null,
                password: null
            })
        }
    },
}
</script>
