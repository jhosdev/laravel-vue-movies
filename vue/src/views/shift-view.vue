<template>
    <main>
        <div class="card mb-2 flex">
            <h1 class="mr-auto" v-if="movieTitle">Pelicula: {{ movieTitle }}</h1>
            <RouterLink to="/">
                <pv-button class="ml-auto">Volver a Peliculas</pv-button>
            </RouterLink>
        </div>
        <shiftListComponentVue movie_id="1"></shiftListComponentVue>
    </main>
</template>

<style scoped></style>

<script>
import shiftListComponentVue from '../components/shift-list.component.vue'
import { MoviesApiService } from '../service/MovieService';
export default {
    name: "ShiftView",
    components: { shiftListComponentVue },
    data() {
        return {
            movieID: undefined,
            movieTitle: undefined,
            service: new MoviesApiService(),
        }
    },
    beforeMount() {
        const id = this.$route.params.id;
        this.movieID = id;
        this.getUser();
    },
    methods: {
        getUser: async function () {
            const { data } = await this.service.getMovieById(this.movieID);
            this.movieTitle = data.name;
        }
    }
}
</script>