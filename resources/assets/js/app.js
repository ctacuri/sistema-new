
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.$ = window.jQuery = require('jquery');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('categoria', require('./components/Categoria.vue'));
Vue.component('articulo', require('./components/Articulo.vue'));
Vue.component('cliente', require('./components/Cliente.vue'));
Vue.component('proveedor', require('./components/Proveedor.vue'));
Vue.component('rol', require('./components/Rol.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('ingreso', require('./components/Ingreso.vue'));
Vue.component('venta', require('./components/Venta.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));
Vue.component('consultaingreso', require('./components/ConsultaIngreso.vue'));
Vue.component('consultaventa', require('./components/ConsultaVenta.vue'));
Vue.component('notification', require('./components/Notification.vue'));
Vue.component('empresa', require('./components/Empresa.vue'));

Vue.filter('commaformat', function (value) {
    return (value||'').toString().trim().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
});

const app = new Vue({
    el: '#app',
    data :{
        menu : 0,
        notifications: [],
        user: { },
        granted: [],
        opened: []
    },
    created(){
        let me = this;
        axios.post('/notification/get').then(function(response){
            //console.log(response.data);
            me.notifications = response.data;
        }).catch(function(error){
            console.log(error);
        });

        axios.get('/logged/userinfo').then(function(response){
            me.user = response.data;
        }).catch(function(error){
            console.log(error);
        });

        axios.get('/logged/granted').then(function(response){
            console.log('granted', response.data);
            me.granted = response.data;
        }).catch(function(error){
            console.log(error);
        });

        var userId = $('meta[name="userId"]').attr('content');      

        Echo.private('App.User.' + userId).notification((notification)=>{
            //console.log(notification);
            me.notifications.unshift(notification);
        });
    },
    methods: {
        isActive: function(idArray){
            var active = false;
            idArray = idArray || [];
            if(idArray.indexOf(this.menu) >= 0){
                active = true;
            }
            return active;
        },
        setActive: function(id){
            this.menu = id;
        },
        isOpen: function(id){
            var open = false;
            if(this.opened.indexOf(id) >= 0) {
                open = true;
            }
            return open;
        },
        toogleOpenClass: function(id){
            if(this.opened.indexOf(id) >= 0) {
                this.opened.splice(this.opened.indexOf(id), 1);
            }
            else {
                this.opened.push(id);
            }
        }
    }
});

const appFooter = new Vue({
    el: '#appFooter',
    data :{
        anio_actual: (new Date).getFullYear()
    }
});