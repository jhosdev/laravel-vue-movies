<template>
    <main>
        <movieListComponentVue></movieListComponentVue>
    </main>
</template>

<style scoped></style>

<script>
import movieListComponentVue from '../components/movie-list.component.vue'

export default {
    name: "MovieView",
    components: { movieListComponentVue }
}
</script>