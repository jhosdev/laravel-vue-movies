
<template>
    <div>
        <div class="card">
            <pv-toolbar class="mb-4">
                <template #start>
                    <pv-toast />
                    <h2>Peliculas</h2>
                </template>

                <template #end>
                    <pv-button label="Nueva Pelicula" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <pv-button label="Borrar Pelicula(s)" icon="pi pi-trash" severity="danger"
                        @click="confirmDeleteSelected" :disabled="!selectedMovies || !selectedMovies.length" />
                </template>
            </pv-toolbar>

            <pv-data-table ref="dt" :value="movies" v-model:selection="selectedMovies" dataKey="id" :paginator="true"
                :rows="10" :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Mostrando peliculas {first} al {last} de las {totalRecords} peliculas totales">
                <template #header>
                    <div class="flex flex-wrap gap-2 align-items-center justify-content-between">
                        <h4 class="m-0">Administrar Peliculas</h4>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <pv-input-text v-model="filters['global'].value" placeholder="Buscar..." />
                        </span>
                    </div>
                </template>

                <pv-column selectionMode="multiple" style="width: 3rem" :exportable="false"></pv-column>
                <pv-column field="id" header="Id" sortable style="min-width:6rem"></pv-column>
                <pv-column field="name" header="Nombre" sortable style="min-width:10rem"></pv-column>

                <pv-column field="date" header="F. Publicacion" sortable style="min-width:8rem">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.date) }}
                    </template>
                </pv-column>

                <pv-column field=active header="Estado" sortable style="min-width:10rem"></pv-column>

                <pv-column header="Image">
                    <template #body="slotProps">
                        <img v-if="slotProps.data.imgPath" :src="`slotProps.data.imgPath`"
                            :alt="slotProps.data.imgPath" class="shadow-2 border-round" style="width: 64px" />
                        <span v-else>No image available</span>
                    </template>
                </pv-column>

                <pv-column :exportable="false" style="min-width:8rem">
                    <template #body="slotProps">
                        <pv-button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editMovie(slotProps.data)" />
                        <pv-button icon="pi pi-align-justify" outlined rounded class="mr-2" @click="goToShifts(slotProps.data.id)" />
                        <pv-button v-if="slotProps.data.status" icon="pi pi-lock-open" outlined rounded class="mr-2" disabled />
                        <pv-button v-else icon="pi pi-lock" outlined rounded class="mr-2" disabled />
                        <pv-button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteMovie(slotProps.data)" />
                    </template>
                </pv-column>
            </pv-data-table>
        </div>

        <pv-dialog v-model:visible="movieDialog" :style="{ width: '450px' }" header="Detalles de la pelicula:" :modal="true"
            class="p-fluid">
            <img v-if="movie.image" :src="`https://primefaces.org/cdn/primevue/images/product/${movie.image}`"
                :alt="movie.image" class="block m-auto pb-3" />

            <div class="field">
                <label for="name">Nombres</label>
                <pv-input-text id="name" v-model.trim="movie.name" required="true" autofocus
                    :class="{ 'p-invalid': submitted && !movie.name }" />
                <small class="p-error" v-if="submitted && !movie.name">El nombre es obligatorio!</small>
            </div>
            <div class="field">
                <label for="date">F. Publicacion</label>
                <pv-calendar id="date" v-model="movie.date" showIcon required="true" autofocus
                    :class="{ 'p-invalid': submitted && !movie.date }" />
                <small class="p-error" v-if="submitted && !movie.date">La fecha es obligatoria!</small>
            </div>

            <div class="field">
                <p>Imagen</p>
                <pv-file-upload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" chooseLabel="Import"
                    class="mr-2 inline-block" />
            </div>

            <template #footer>
                <pv-button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <pv-button label="Guardar" icon="pi pi-check" text @click="saveMovie" />
            </template>
        </pv-dialog>

        <pv-dialog v-model:visible="deleteMovieDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="movie">Estas seguro de que quieres eliminar la pelicula: <b>{{ movie.name }}</b>?</span>
            </div>
            <template #footer>
                <pv-button label="No" icon="pi pi-times" text @click="deleteMovieDialog = false" />
                <pv-button label="Si" icon="pi pi-check" text @click="deleteMovie" />
            </template>
        </pv-dialog>

        <pv-dialog v-model:visible="deleteMoviesDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="movie">Estas seguro de que quieres eliminar las peliculas seleccionadas?</span>
            </div>
            <template #footer>
                <pv-button label="No" icon="pi pi-times" text @click="deleteMoviesDialog = false" />
                <pv-button label="Si" icon="pi pi-check" text @click="deleteSelectedMovies" />
            </template>
        </pv-dialog>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { ref, onBeforeMount } from 'vue';
import { FilterMatchMode } from 'primevue/api';
import { useToast } from 'primevue/usetoast';
import { MoviesApiService } from '../service/MovieService';

let moviesService = new MoviesApiService();
const router = useRouter();

const toast = useToast();
const dt = ref();
const movies = ref();
const movieDialog = ref(false);
const deleteMovieDialog = ref(false);
const deleteMoviesDialog = ref(false);
const movie = ref({});
const selectedMovies = ref();
const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);


onBeforeMount(() => {
    getMovies();
});


/** Obtener las peliculas del backend */
async function getMovies() {
    try {
        const { data } = await moviesService.getMovies();
        
        movies.value = data.map((mov) => ({
            ...mov,
            active: mov.status ? "Activo" : "Inactivo",
        }));
        console.log(movies.value)
    }
    catch (err) {
        console.log(err)
    }
}

async function createMovie(body) {
    try {
        const { data } = await moviesService.createMovie(body);
        movies.value.push({ ...data, active: "Inactivo" });
        console.log(movies.value)
    }
    catch (err) {
        console.log(err)
    }
}

async function updateMovieService(id, body) {
    try {
        let st;

        (body.active === "Activo") ? (body.status = true, st='Activo') : (body.status = false, st='Inactivo');
        delete body.active;

        const { data } = await moviesService.updateMovie(id, body);
        movies.value = movies.value.map(mov => (mov.id === data.id) ? {
            ...data,
            status: st
        } : mov)
    }
    catch (err) {
        console.log(err)
    }
}

async function deleteMovieService(id) {
    try {
        const { data } = await moviesService.deleteMovie(id);
        if (data) {
            console.log(data)
            movies.value = movies.value.filter(movie => movie.id !== id);
            return true;
        }
    }
    catch (err) {
        console.log(err)
        return true;
    }
}



const formatDate = (value) => {
    if (value) {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        let date = new Date(value);
        let formattedDate = date.toLocaleDateString('en-GB', options);
        return formattedDate;
    }
    return;
};

const openNew = () => {
    movie.value = {};
    submitted.value = false;
    movieDialog.value = true;
};

const hideDialog = () => {
    movieDialog.value = false;
    submitted.value = false;
};


const saveMovie = () => {
    submitted.value = true;

    if (movie.value.name.trim() && movie.value.date) {
        if (movie.value.id) {
            updateMovieService(movie.value.id, movie.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Pelicula Actualizada', life: 3000 });
        }
        else {
            //El id se genera automaticamente en la base de datos
            //movie.value.imgPath = '../../upload.php';
            createMovie(movie.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Pelicula Creada', life: 3000 });
        }

        movieDialog.value = false;
        movie.value = {};
    }
};

const editMovie = (prod) => {
    movie.value = { ...prod };
    movieDialog.value = true;
};

const confirmDeleteMovie = (mov) => {
    movie.value = mov;
    deleteMovieDialog.value = true;
};

const deleteMovie = () => {
    deleteMovieService(movie.value.id); //delete en el servicio

    deleteMovieDialog.value = false;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Pelicula Eliminada', life: 3000 });
};

const confirmDeleteSelected = () => {
    deleteMoviesDialog.value = true;
};

const deleteSelectedMovies = async () => {
    const promises = selectedMovies.value.map((mov) => deleteMovieService(mov.id));
    const results = await Promise.all(promises);
    const numDeleted = results.filter((res) => res === true).length;
    movies.value = movies.value.filter((val) => !selectedMovies.value.includes(val));

    selectedMovies.value = null;
    deleteMoviesDialog.value = false;

    if (numDeleted > 0) {
        toast.add({ severity: 'success', summary: 'Successful', detail: `${numDeleted} películas eliminadas`, life: 3000 });
    } else {
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'No se eliminó ninguna película', life: 3000 });
    }
};  

const goToShifts = (id) => {
    router.push(`movies/${id}/shifts`)
}

</script>