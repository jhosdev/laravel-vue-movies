import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import PrimeVue from "primevue/config";
import "primeflex/primeflex.css";
import "primevue/resources/themes/lara-light-teal/theme.css"; //theme
import "primevue/resources/primevue.min.css"; //core
import "primeicons/primeicons.css"; //icons

import Toolbar from 'primevue/toolbar';
import Button from "primevue/button"
import Card from 'primevue/card';
import Calendar from 'primevue/calendar';
import Checkbox from 'primevue/checkbox';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import FileUpload from 'primevue/fileupload';
import Rating from 'primevue/rating';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import Toast from "primevue/toast";
import ToastService from 'primevue/toastservice';
import DialogService from 'primevue/dialogservice'
import Textarea from "primevue/textarea";
import Divider from "primevue/divider";
import Avatar from "primevue/avatar";
import TabMenu from "primevue/tabmenu";

const app = createApp(App)

app.component('pv-button', Button);
app.component('pv-toolbar', Toolbar);
app.component('pv-card', Card);
app.component('pv-calendar', Calendar);
app.component('pv-checkbox', Checkbox);
app.component('pv-data-table', DataTable);
app.component('pv-column', Column);
app.component('pv-input-text', InputText);
app.component('pv-radio-button', RadioButton);
app.component('pv-file-upload', FileUpload);
app.component('pv-rating', Rating);
app.component('pv-tag', Tag);
app.component('pv-dialog', Dialog);
app.component('pv-dropdown', Dropdown);
app.component('pv-input-number', InputNumber);
app.component('pv-toast', Toast);
app.component('pv-text-area', Textarea);
app.component('pv-divider', Divider);
app.component('pv-avatar', Avatar);
app.component('pv-tab-menu', TabMenu);
app.use(ToastService);
app.use(DialogService);

app.use(router)
app.use(PrimeVue, {ripple: true});

app.mount('#app')
