<template>
    <div>
        <Component :is="link.url ? 'Link' : 'span'"
                   v-for="link in links"
                   :href="link.url"
                   v-html="link.label"
                   v-bind:key="link.label"
                   class="px-4 py-2 text-gray-700 rounded-md hover:bg-teal-400 hover:text-white"
                   :class="{'text-gray-500 bg-gray-100': !link.url, '!text-white !bg-teal-400': link.active}"></Component>
    </div>
</template>

<script>
export default {
    props: {
        links: Array,
    }
}
</script>
