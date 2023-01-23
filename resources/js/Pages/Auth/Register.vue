<template>
    <section class="text-gray-600 body-font">
        <Head title="Register" />
        <div class="container px-5 py-24 mx-auto flex flex-wrap items-center">
            <div class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:mx-auto w-full mt-10 md:mt-0">
                <h2 class="text-gray-900 text-lg font-medium title-font mb-5 text-center">Register</h2>
                <form @submit.prevent="form.post(route('register'))">
                    <div class="relative mb-4">
                        <label for="name" class="leading-7 text-sm text-gray-600">Name</label>
                        <input type="text" id="name" v-model="form.name" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        <p v-if="form.errors.name" class="text-xs text-red-500 mt-3">{{ form.errors.name }}</p>
                    </div>
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
                    <div class="relative mb-4">
                        <label for="repass" class="leading-7 text-sm text-gray-600">Password Confirmation</label>
                        <input type="password" id="repass" v-model="form.password_confirmation" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        <p v-if="form.errors.password_confirmation" class="text-xs text-red-500 mt-3">{{ form.errors.password_confirmation }}</p>
                    </div>
                    <button type="submit" :disabled="form.processing" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg w-full">Register</button>
                </form>
                <Link :href="route('login')" as="button" class="text-white bg-purple-500 border-0 py-2 px-8 mt-2 focus:outline-none hover:bg-purple-600 rounded text-lg w-full">Login</Link>
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
				name: null,
                email: null,
                password: null,
				password_confirmation: null
            })
        }
    },
}
</script>
