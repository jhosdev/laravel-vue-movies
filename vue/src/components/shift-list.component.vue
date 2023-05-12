<template>
    <div>
        <div class="card">
            <pv-toolbar class="mb-4">
                <template #start>
                    <pv-toast />
                    <h2>Turnos</h2>
                </template>

                <template #end>
                    <pv-button label="Nuevo Turno" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <pv-button label="Borrar Turno(s)" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected"
                        :disabled="!selectedShifts || !selectedShifts.length" />
                </template>
            </pv-toolbar>

            <pv-data-table ref="dt" :value="shifts" v-model:selection="selectedShifts" dataKey="id" :paginator="true"
                :rows="10" :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Mostrando turnos del {first} al {last} de los {totalRecords} turnos">
                <template #header>
                    <div class="flex flex-wrap gap-2 align-items-center justify-content-between">
                        <h4 class="m-0">Administrar Turnos</h4>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <pv-input-text v-model="filters['global'].value" placeholder="Buscar..." />
                        </span>
                    </div>
                </template>

                <pv-column selectionMode="multiple" style="width: 3rem" :exportable="false"></pv-column>
                <pv-column field="id" header="Id" sortable style="min-width:6rem"></pv-column>
                <pv-column field="date" header="Turno" sortable style="min-width:8rem">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.date) }}
                    </template>
                </pv-column>
                <pv-column field=active header="Estado" sortable style="min-width:10rem"></pv-column>

                <pv-column :exportable="false" style="min-width:8rem">
                    <template #body="slotProps">
                        <pv-button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editShift(slotProps.data)" />
                        <pv-button v-if="slotProps.data.status" icon="pi pi-lock-open" outlined rounded class="mr-2"
                            disabled />
                        <pv-button v-else icon="pi pi-lock" outlined rounded class="mr-2" disabled />
                        <pv-button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteShift(slotProps.data)" />
                    </template>
                </pv-column>
            </pv-data-table>
        </div>

        <pv-dialog v-model:visible="shiftDialog" :style="{ width: '450px' }" header="Detalles de la pelicula:" :modal="true"
            class="p-fluid">

            <div class="field">
                <label for="date">Turno</label>
                <pv-calendar id="date" v-model="shift.date" showIcon required="true" autofocus timeOnly
                    :class="{ 'p-invalid': submitted && !shift.date }" />
                <small class="p-error" v-if="submitted && !shift.date">La fecha es obligatoria!</small>
            </div>

            <div class="field flex">
                <pv-checkbox v-model="shift.status" inputId="stat" name="true" :value="true" />
                <label for="stat" class="ml-2"> Activo? </label>
            </div>

            <template #footer>
                <pv-button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <pv-button label="Guardar" icon="pi pi-check" text @click="saveShift" />
            </template>
        </pv-dialog>

        <pv-dialog v-model:visible="deleteShiftDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="shift">Estas seguro de que quieres eliminar el turno de: <b>{{ shift.date }}</b>?</span>
            </div>
            <template #footer>
                <pv-button label="No" icon="pi pi-times" text @click="deleteShiftDialog = false" />
                <pv-button label="Si" icon="pi pi-check" text @click="deleteShift" />
            </template>
        </pv-dialog>

        <pv-dialog v-model:visible="deleteShiftsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="shift">Estas seguro de que quieres eliminar los turnos seleccionados?</span>
            </div>
            <template #footer>
                <pv-button label="No" icon="pi pi-times" text @click="deleteShiftsDialog = false" />
                <pv-button label="Si" icon="pi pi-check" text @click="deleteSelectedShifts" />
            </template>
        </pv-dialog>

    </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue';
import { FilterMatchMode } from 'primevue/api';
import { useToast } from 'primevue/usetoast';
import { ShiftsApiService } from '../service/ShiftService';

const props = defineProps({
    movie_id: Number,
})

let shiftsService = new ShiftsApiService();

const toast = useToast();
const dt = ref();
const shifts = ref();
const shiftDialog = ref(false);
const deleteShiftDialog = ref(false);
const deleteShiftsDialog = ref(false);
const shift = ref({});
const selectedShifts = ref();
const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

onBeforeMount(() => {
    getShifts();
})

/** Obtener los turnos por pelicula del backend */
async function getShifts() {
    try {
        const { data } = await shiftsService.getShiftsByMovie(props.movie_id);

        shifts.value = data.map((mov) => ({
            ...mov,
            active: mov.status ? "Activo" : "Inactivo",
        }));

        console.log(shifts.value)
    }
    catch (err) {
        console.log(err)
    }
}

async function createShift(body) {
    try {
        body.movie_id=props.movie_id;
        (body.status) ? body.status=body.status[0] : body.status=0
        body.date=formatDate(body.date);
        const { data } = await shiftsService.createShift(props.movie_id,body);
        console.log(data)
        shifts.value.push({ ...data, active: data.status ? "Activo" : "Inactivo", });
    }
    catch (err) {
        console.log(err)
    }
}

async function updateShiftService(id, body) {
    try {
        let st;
        console.log(shift.value)
        console.log(body)


        (body.active === "Activo") ? (body.status = true, st = 'Activo') : (body.status = false, st = 'Inactivo');
        delete body.active;

        const { data } = await shiftsService.updateShift(id, body);
        shifts.value = shifts.value.map(mov => (mov.id === data.id) ? {
            ...data,
            status: st
        } : mov)
    }
    catch (err) {
        console.log(err)
    }
}

async function deleteShiftService(id) {
    try {
        const { data } = await shiftsService.deleteShift(id);
        shifts.value = shifts.value.filter(shift => shift.id !== id);
        if (data) return true;
    }
    catch (err) {
        console.log(err)
        return true;
    }
}

const formatDate = (value) => {
    if (value) {
        const options = { hour: '2-digit', minute: '2-digit' };
        let date = new Date(value);
        let formattedDate = date.toLocaleDateString('en-GB', options);
        const datetimeArray = formattedDate.split(',');
        return datetimeArray[1];
    }
    return;
};

const openNew = () => {
    shift.value = {};
    submitted.value = false;
    shiftDialog.value = true;
};

const hideDialog = () => {
    shiftDialog.value = false;
    submitted.value = false;
};

const saveShift = () => {
    submitted.value = true;

    if (shift.value.date) {
        if (shift.value.id) {
            updateShiftService(shift.value.id, shift.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Turno Actualizado', life: 3000 });
        }
        else {
            //El id se genera automaticamente en la base de datos
            createShift(shift.value);
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Turno Creado', life: 3000 });
        }

        shiftDialog.value = false;
        shift.value = {};
    }
};

const editShift = (prod) => {
    shift.value = { ...prod };
    shiftDialog.value = true;
};

const confirmDeleteShift = (mov) => {
    shift.value = mov;
    deleteShiftDialog.value = true;
};

const deleteShift = () => {
    deleteShiftService(shift.value.id); //delete en el servicio

    deleteShiftDialog.value = false;
    toast.add({ severity: 'success', summary: 'Successful', detail: 'Pelicula Eliminada', life: 3000 });
};

const confirmDeleteSelected = () => {
    deleteShiftsDialog.value = true;
};

const deleteSelectedShifts = async () => {
    const promises = selectedShifts.value.map((mov) => deleteShiftService(mov.id));
    const results = await Promise.all(promises);
    const numDeleted = results.filter((res) => res === true).length;
    shifts.value = shifts.value.filter((val) => !selectedShifts.value.includes(val));

    selectedShifts.value = null;
    deleteShiftsDialog.value = false;

    if (numDeleted > 0) {
        toast.add({ severity: 'success', summary: 'Successful', detail: `${numDeleted} turnos eliminados`, life: 3000 });
    } else {
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'No se eliminó ningun turno', life: 3000 });
    }
};

</script>

<style></style>