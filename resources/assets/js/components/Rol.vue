<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">Acceso</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Roles</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-user-following"></i> Roles
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_ROLES']">
              <button type="button" @click="abrirModal('rol','registrar')" class="btn btn-link">
                <i class="icon-plus"></i>&nbsp;Agregar rol
              </button>
            </template>
          </div>
          <!-- / End Header Options -->
        </div>
        <div class="card-body">
          <!-- Filter Form -->
          <div class="form-group row">
            <div class="col-md-5">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Filtro</span>
                  <select class="custom-select" v-model="criterio">
                    <option value="nombre" selected>Por nombre</option>
                    <option value="descripcion">Por descripción</option>
                  </select>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Texto a buscar..."
                  @keyup.enter="listarRol(1,buscar,criterio)"
                  v-model="buscar"
                >
                <div class="input-group-append">
                  <button
                    class="btn btn-primary"
                    type="submit"
                    @click="listarRol(1,buscar,criterio)"
                  >
                    <i class="fa fa-search"></i> Buscar
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- / End Filter Form -->
          <!-- Table Records -->
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rol in arrayRol" :key="rol.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_ROLES']">
                      <template v-if="rol.idempresa != 0">
                        <button
                          type="button"
                          @click="abrirModal('rol','actualizar',rol)"
                          class="btn btn-link"
                        >
                          <i class="icon-pencil"></i>
                        </button> &nbsp;
                        <button
                          type="button"
                          @click="abrirModal('rol','permisos',rol)"
                          class="btn btn-link"
                        >
                          <i class="icon-user-following"></i>
                        </button>
                      </template>
                      <template v-else>
                        <span class="banned">
                          <i class="icon-lock"></i> <span>Rol de sistema</span>
                        </span>
                      </template>
                    </template>
                  </div>
                  <!-- / End Row Options -->
                </td>
                <td v-text="rol.nombre"></td>
                <td v-text="rol.descripcion"></td>
                <td>
                  <div v-if="rol.condicion">
                    <span class="badge badge-success">Activo</span>
                  </div>
                  <div v-else>
                    <span class="badge badge-danger">Desactivado</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- / End Table Records -->
          <!-- Pagination -->
          <nav>
            <ul class="pagination" v-if="pagesNumber.length > 1">
              <li class="page-item" v-bind:class="{'disabled': pagination.current_page == 1}">
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)"
                >
                  <i class="icon-arrow-left"></i>
                </a>
              </li>
              <li
                class="page-item"
                v-for="page in pagesNumber"
                :key="page"
                :class="[page == isActived ? 'active' : '']"
              >
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="cambiarPagina(page,buscar,criterio)"
                  v-text="page"
                ></a>
              </li>
              <li
                class="page-item"
                v-bind:class="{'disabled': pagination.current_page == pagination.last_page}"
              >
                <a
                  class="page-link"
                  href="#"
                  @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)"
                >
                  <i class="icon-arrow-right"></i>
                </a>
              </li>
            </ul>
          </nav>
          <!-- / End Pagination -->
        </div>
      </div>
    </div>
    <!-- 
      ** MODALS **
    -->
    <!-- Create / Update Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'show-modal' : modal}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" v-html="tituloModal"></h4>
            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <!-- / End Modal Header -->
          <!-- Modal Body -->
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="vf-form">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombre</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Nombre del rol"
                  v-model="nombre"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Descripción</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Descripción del rol"
                  v-model="descripcion"
                >
              </div>

              <div v-show="errorRol" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjRol"
                  :key="error"
                  class="alert alert-warning"
                  role="alert"
                >
                  <i class="icon-ban"></i>
                  <em v-text="error"></em>
                </div>
              </div>
            </form>
          </div>
          <!-- / End Modal Body -->
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-link" @click="cerrarModal()">Cancelar</button>
            <button
              type="button"
              v-if="tipoAccion==1"
              class="btn btn-primary"
              @click="registrarRol()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarRol()"
            >Actualizar</button>
          </div>
          <!-- / End Modal Footer -->
        </div>
      </div>
    </div>
    <!-- / End Update Modal -->
    
    <!-- Permissions Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'show-modal' : modalPermisos}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" v-html="tituloModalPermisos"></h4>
            <button type="button" class="close" @click="cerrarModalPermisos()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <!-- / End Modal Header -->
          <!-- Modal Body -->
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="vf-form">
              <div class="row">
                <div class="col-sm-12">
                  <template v-if="permisos && permisos.length == 0">
                    <div>
                      <em>Cargando permisos</em>
                      <span class="spinner blue">
                        <span class="bounce1"></span>
                        <span class="bounce2"></span>
                        <span class="bounce3"></span>
                      </span>
                    </div>
                  </template>
                  <template v-else-if="permisos && permisos.length > 0">
                    <div class="info-block">
                      <p>Active los permisos que desea asignarle al rol.</p>
                    </div>
                    <div class="checkbox-list-wrapper">
                      <ul class="checkbox-list">
                        <li v-for="permiso in permisos" v-bind:key="permiso.id">
                          <label v-bind:for="permiso.id">
                            <input
                              type="checkbox"
                              v-bind:id="permiso.id"
                              v-bind:value="permiso.id"
                              v-model="checkedPermisos"
                            >
                            <span v-text="permiso.descripcion"></span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </template>
                  <template v-else-if="permisos == false">
                    <div class="text-center text-error">
                      <p>No existen permisos</p>
                    </div>
                  </template>
                </div>
              </div>
              <div v-show="errorPermisos" class="form-group row div-error">
                <div class="text-center text-error">
                  <div v-for="error in errorMostrarMsjPermisos" :key="error" v-text="error"></div>
                </div>
              </div>
            </form>
          </div>
          <!-- / End Modal Body -->
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button
              type="button"
              v-bind:disabled="procesandoPermisos"
              class="btn btn-secondary"
              @click="cerrarModalPermisos()"
            >Cancelar</button>
            <button
              v-if="permisos && permisos.length > 0"
              type="button"
              v-bind:disabled="procesandoPermisos"
              class="btn btn-primary"
              @click="actualizarPermisos()"
            >
              <span v-if="!procesandoPermisos">Guardar</span>
              <span v-else class="spinner">
                <span class="bounce1"></span>
                <span class="bounce2"></span>
                <span class="bounce3"></span>
              </span>
            </button>
          </div>
          <!-- / End Modal Footer -->
        </div>
      </div>
    </div>
    <!-- / End Permissions Modal -->
  </main>
</template>

<script>
export default {
  data() {
    return {
      rol_id: 0,

      nombre: "",
      descripcion: "",
      arrayRol: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorRol: 0,
      errorMostrarMsjRol: [],

      procesandoPermisos: false,
      permisos: [],
      checkedPermisos: [],
      modalPermisos: 0,
      tituloModalPermisos: "",
      errorPermisos: 0,
      errorMostrarMsjPermisos: [],

      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
      offset: 3,
      criterio: "nombre",
      buscar: ""
    };
  },
  computed: {
    isActived: function() {
      return this.pagination.current_page;
    },
    //Calcula los elementos de la paginación
    pagesNumber: function() {
      if (!this.pagination.to) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    }
  },
  methods: {
    listarRol(page, buscar, criterio) {
      let me = this;
      var url =
        "/rol?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayRol = respuesta.roles.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarRol(page, buscar, criterio);
    },
    registrarRol() {
      if (this.validarRol()) {
        return;
      }

      let me = this;

      axios
        .post("/rol/registrar", {
          nombre: this.nombre,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarRol(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizarRol() {
      if (this.validarRol()) {
        return;
      }

      let me = this;

      axios
        .put("/rol/actualizar", {
          id: this.rol_id,
          nombre: this.nombre,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarRol(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    validarRol() {
      this.errorRol = 0;
      this.errorMostrarMsjRol = [];

      if (!this.nombre)
        this.errorMostrarMsjRol.push("El nombre del rol no puede estar vacío.");
      if (!this.descripcion)
        this.errorMostrarMsjRol.push("La descripción no puede estar vacía.");

      if (this.errorMostrarMsjRol.length) this.errorRol = 1;

      return this.errorRol;
    },
    actualizarPermisos() {
      let me = this;

      if (me.procesandoPermisos) {
        return;
      }

      me.procesandoPermisos = true;
      axios
        .put("/rol/actualizarPermisosDeRol", {
          id: this.rol_id,
          checked: this.checkedPermisos
        })
        .then(function(response) {
          me.cerrarModalPermisos();
          me.listarRol(1, "", "nombre");

          me.procesandoPermisos = false;
        })
        .catch(function(error) {
          console.log(error);
          me.procesandoPermisos = false;
        });
    },

    cerrarModal() {
      this.modal = 0;
      this.tituloModal = "";
      this.nombre = "";
      this.descripcion = "";
      this.rol_id = 0;
      this.errorRol = 0;
    },
    cerrarModalPermisos() {
      this.modalPermisos = 0;
      this.tituloModalPermisos = "";
      this.permisos = [];
      this.checkedPermisos = [];
      this.rol_id = 0;
      this.errorRolPermisos = 0;
    },
    abrirModal(modelo, accion, data = []) {
      switch (modelo) {
        case "rol": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = '<i class="icon-plus"></i> Registrar Rol';
              this.nombre = "";
              this.descripcion = "";
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              //console.log(data);
              this.modal = 1;
              this.tituloModal = '<i class="icon-pencil"></i> Actualizar Rol';
              this.rol_id = data["id"];
              this.nombre = data["nombre"];
              this.descripcion = data["descripcion"];
              this.tipoAccion = 2;
              break;
            }
            case "permisos": {
              //console.log(data);
              this.modalPermisos = 1;
              this.tituloModalPermisos =
                '<i class="icon-user-following"></i> Permisos del rol "' + data["nombre"] + '"';
              this.rol_id = data["id"];

              let me = this;
              axios
                .get("/rol/obtenerPermisosDeRol", {
                  params: {
                    id: me.rol_id
                  }
                })
                .then(function(response) {
                  console.log(response.data);
                  me.permisos = response.data.permisos;
                })
                .catch(function(error) {
                  console.log(error);
                })
                .then(function() {
                  if (me.permisos && me.permisos.length > 0) {
                    var initialChecked = me.permisos.filter(function(o) {
                      return o.valor == 1;
                    });
                    me.checkedPermisos = initialChecked.map(function(o) {
                      return o.id;
                    });
                    console.log(me.checkedPermisos);
                  } else {
                    me.permisos = false;
                  }
                });
              break;
            }
          }
        }
      }
    }
  },
  mounted() {
    this.listarRol(1, this.buscar, this.criterio);
  }
};
</script>
<style>
</style>
