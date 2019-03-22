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
      <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-user"></i> Usuarios
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_USUARIOS']">
              <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-link">
                <i class="icon-plus"></i>&nbsp;Agregar usuario
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
                    <option value="num_documento">Por documento</option>
                    <option value="email">Por e-mail</option>
                    <option value="telefono">Por teléfono</option>
                  </select>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Texto a buscar..."
                  @keyup.enter="listarPersona(1,buscar,criterio)"
                  v-model="buscar"
                >
                <div class="input-group-append">
                  <button
                    class="btn btn-primary"
                    type="submit"
                    @click="listarPersona(1,buscar,criterio)"
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
                <th>Tipo Documento</th>
                <th>Número</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Usuario</th>
                <th>Rol</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="persona in arrayPersona" :key="persona.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_USUARIOS']">
                      <button
                        type="button"
                        @click="abrirModal('persona','actualizar',persona)"
                        class="btn btn-link"
                      >
                        <i class="icon-pencil"></i>
                      </button> &nbsp;
                      <button
                        type="button"
                        @click="abrirModal('persona','permisos',persona)"
                        class="btn btn-link"
                      >
                        <i class="icon-user-following"></i>
                      </button> &nbsp;
                      <template v-if="persona.condicion">
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="desactivarUsuario(persona.id)"
                        >
                          <i class="icon-trash"></i>
                        </button>
                      </template>
                      <template v-else>
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="activarUsuario(persona.id)"
                        >
                          <i class="icon-check"></i>
                        </button>
                      </template>
                    </template>
                  </div>
                  <!-- / End Row Options -->
                </td>
                <td v-text="persona.nombre"></td>
                <td v-text="persona.tipo_documento"></td>
                <td v-text="persona.num_documento"></td>
                <td v-text="persona.direccion"></td>
                <td v-text="persona.telefono"></td>
                <td v-text="persona.email"></td>
                <td v-text="persona.usuario"></td>
                <td v-text="persona.rol"></td>
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
                  placeholder="Nombre de la persona"
                  v-model="nombre"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Tipo de documento</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="tipo_documento"
                  v-bind:class="{'placeholder': !(tipo_documento || false) }"
                >
                  <option disabled value="">Tipo de documento</option>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CEDULA">CEDULA</option>
                  <option value="PASS">PASS</option>
                </select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Número de documento</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Número de documento"
                  v-model="num_documento"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Dirección</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Dirección"
                  v-model="direccion"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Teléfono</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Teléfono"
                  v-model="telefono"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Email</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Email"
                  v-model="email"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rol</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="idrol"
                  v-bind:class="{'placeholder': !(idrol || false) }"
                >
                  <option disabled value="0">Seleccione un rol</option>
                  <option
                      v-for="rol in arrayRol"
                      :key="rol.id"
                      :value="rol.id"
                      v-text="rol.nombre"
                    ></option>
                </select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Usuario</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Nombre de usuario"
                  v-model="usuario"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Password</span>
                </div>
                <input
                  type="password"
                  class="form-control"
                  placeholder="Password de acceso"
                  v-model="password"
                >
              </div>

              <div v-show="errorPersona" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjPersona"
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
              @click="registrarPersona()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarPersona()"
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
                      <p>Active los permisos que desea asignarle al usuario.</p>
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

              <div v-show="errorPermisos" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjPermisos"
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
      persona_id: 0,

      nombre: "",
      tipo_documento: "",
      num_documento: "",
      direccion: "",
      telefono: "",
      email: "",
      usuario: "",
      password: "",
      idrol: 0,
      arrayPersona: [],
      arrayRol: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorPersona: 0,
      errorMostrarMsjPersona: [],

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
    listarPersona(page, buscar, criterio) {
      let me = this;
      var url =
        "/user?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayPersona = respuesta.personas.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    selectRol() {
      let me = this;
      var url = "/rol/selectRol";
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayRol = respuesta.roles;
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
      me.listarPersona(page, buscar, criterio);
    },
    registrarPersona() {
      if (this.validarPersona()) {
        return;
      }

      let me = this;

      axios
        .post("/user/registrar", {
          nombre: this.nombre,
          tipo_documento: this.tipo_documento,
          num_documento: this.num_documento,
          direccion: this.direccion,
          telefono: this.telefono,
          email: this.email,
          usuario: this.usuario,
          password: this.password,
          idrol: this.idrol
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarPersona(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizarPersona() {
      if (this.validarPersona()) {
        return;
      }

      let me = this;

      axios
        .put("/user/actualizar", {
          nombre: this.nombre,
          tipo_documento: this.tipo_documento,
          num_documento: this.num_documento,
          direccion: this.direccion,
          telefono: this.telefono,
          email: this.email,
          usuario: this.usuario,
          password: this.password,
          idrol: this.idrol,
          id: this.persona_id
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarPersona(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    validarPersona() {
      this.errorPersona = 0;
      this.errorMostrarMsjPersona = [];

      if (!this.nombre)
        this.errorMostrarMsjPersona.push(
          "El nombre de la persona no puede estar vacío."
        );
      if (!this.usuario)
        this.errorMostrarMsjPersona.push(
          "El nombre de usuario no puede estar vacío."
        );
      if (!this.password)
        this.errorMostrarMsjPersona.push("El password no puede estar vacío.");
      if (this.idrol == 0)
        this.errorMostrarMsjPersona.push(
          "Debes seleccionar un rol para el usuario."
        );

      if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

      return this.errorPersona;
    },
    actualizarPermisos() {
      let me = this;

      if (me.procesandoPermisos) {
        return;
      }

      me.procesandoPermisos = true;
      axios
        .put("/user/actualizarPermisosDeUsuario", {
          id: this.persona_id,
          checked: this.checkedPermisos
        })
        .then(function(response) {
          me.cerrarModalPermisos();
          me.listarPersona(1, "", "nombre");

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
      this.tipo_documento = "";
      this.num_documento = "";
      this.direccion = "";
      this.telefono = "";
      this.email = "";
      this.usuario = "";
      this.password = "";
      this.idrol = 0;
      this.errorPersona = 0;
    },
    cerrarModalPermisos() {
      this.modalPermisos = 0;
      this.tituloModalPermisos = "";
      this.permisos = [];
      this.checkedPermisos = [];
      this.persona_id = 0;
      this.errorRolPermisos = 0;
    },
    abrirModal(modelo, accion, data = []) {
      this.selectRol();
      switch (modelo) {
        case "persona": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = '<i class="icon-plus"></i> Registrar Usuario';
              this.nombre = "";
              this.tipo_documento = "";
              this.num_documento = "";
              this.direccion = "";
              this.telefono = "";
              this.email = "";
              this.usuario = "";
              this.password = "";
              this.idrol = 0;
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              //console.log(data);
              this.modal = 1;
              this.tituloModal = '<i class="icon-pencil"></i> Actualizar Usuario';
              this.tipoAccion = 2;
              this.persona_id = data["id"];
              this.nombre = data["nombre"];
              this.tipo_documento = data["tipo_documento"];
              this.num_documento = data["num_documento"];
              this.direccion = data["direccion"];
              this.telefono = data["telefono"];
              this.email = data["email"];
              this.usuario = data["usuario"];
              this.password = data["password"];
              this.idrol = data["idrol"];
              break;
            }
            case "permisos": {
              //console.log(data);
              this.modalPermisos = 1;
              this.tituloModalPermisos =
                '<i class="icon-user-following"></i> Permisos del usuario "' + data["nombre"] + '"';
              this.persona_id = data["id"];

              let me = this;
              axios
                .get("/user/obtenerPermisosDeUsuario", {
                  params: {
                    id: me.persona_id
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
    },
    desactivarUsuario(id) {

      swal.queue([
        {
          title: "",
          text: "¿Está seguro de desactivar este usuario?",
          type: "question",
          showCancelButton: true,
          confirmButtonText: "Sí",
          cancelButtonText: "No",
          confirmButtonClass: "btn btn-primary",
          cancelButtonClass: "btn btn-secondary",
          buttonsStyling: false,
          reverseButtons: false,
          allowOutsideClick: false,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            let me = this;
            return axios
              .put("/user/desactivar", {
                id: id
              })
              .then(function(response) {
                me.listarPersona(1, "", "nombre");
                swal.insertQueueStep({
                  title: "Desactivado!",
                  text: "El registro ha sido desactivado con éxito.",
                  type: "success",
                  confirmButtonText: "Listo",
                  confirmButtonClass: "btn btn-primary",
                  buttonsStyling: false
                });
              })
              .catch(function(error) {
                Swal.insertQueueStep({
                  type: "error",
                  title: "",
                  text: error.response.data.message || error.response.statusText
                });
              });
          }
        }
      ]);
    },
    activarUsuario(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de activar este usuario?",
          type: "question",
          showCancelButton: true,
          confirmButtonText: "Sí",
          cancelButtonText: "No",
          confirmButtonClass: "btn btn-primary",
          cancelButtonClass: "btn btn-secondary",
          buttonsStyling: false,
          reverseButtons: false,
          allowOutsideClick: false,
          showLoaderOnConfirm: true,
          preConfirm: () => {
            let me = this;
            return axios
              .put("/user/activar", {
                id: id
              })
              .then(function(response) {
                me.listarPersona(1, "", "nombre");
                swal.insertQueueStep({
                  title: "Activado!",
                  text: "El registro ha sido activado con éxito.",
                  type: "success",
                  confirmButtonText: "Listo",
                  confirmButtonClass: "btn btn-primary",
                  buttonsStyling: false
                });
              })
              .catch(function(error) {
                console.log(error.response);
                Swal.insertQueueStep({
                  type: "error",
                  title: "",
                  text: error.response.data.message || error.response.statusText
                });
              });
          }
        }
      ]);
    }
  },
  mounted() {
    this.listarPersona(1, this.buscar, this.criterio);
  }
};
</script>
<style>
</style>
