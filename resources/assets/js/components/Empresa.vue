<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Empresa</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-home"></i> Empresa
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options"></div>
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
                    <option value="ruc" selected>Por RUC</option>
                    <option value="nombre">Por nombre</option>
                  </select>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Texto a buscar..."
                  @keyup.enter="listarEmpresa(1,buscar,criterio)"
                  v-model="buscar"
                >
                <div class="input-group-append">
                  <button
                    class="btn btn-primary"
                    type="submit"
                    @click="listarEmpresa(1,buscar,criterio)"
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
                <th>RUC</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="empresa in arrayEmpresa" :key="empresa.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_EMPRESAS']">
                      <button
                        type="button"
                        @click="abrirModal('empresa','actualizar',empresa)"
                        class="btn btn-link"
                      >
                        <i class="icon-pencil"></i>
                      </button> &nbsp;
                    </template>
                  </div>
                  <!-- / End Row Options -->
                </td>
                <td v-text="empresa.ruc"></td>
                <td v-text="empresa.nombre"></td>
                <td v-text="empresa.direccion"></td>
                <td>
                  <div v-if="empresa.condicion">
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
    <!-- Create-Update Modal -->
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
                  <span class="input-group-text">RUC</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="RUC de la empresa"
                  v-model="ruc"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombre</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Nombre de la empresa"
                  v-model="nombre"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Departamento</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="departamento"
                  v-bind:class="{'placeholder': !(departamento || false) }"
                >
                  <option disabled value="0">Departamento</option>
                  <option
                    v-for="optDep in fillDepartamentos"
                    v-bind:key="optDep.id"
                    v-bind:value="optDep.id" 
                    v-text="optDep.descripcion"></option>
                </select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Provincia</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="provincia"
                  v-bind:class="{'placeholder': !(provincia || false) }"
                >
                  <option disabled value="0">Provincia</option>
                  <option
                    v-for="optProv in fillProvincias"
                    v-bind:key="optProv.iddepartamento + optProv.id"
                    v-bind:value="optProv.id" 
                    v-text="optProv.descripcion"></option>
                </select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Distrito</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="distrito"
                  v-bind:class="{'placeholder': !(distrito || false) }"
                >
                  <option disabled value="0">Distrito</option>
                  <option
                    v-for="optDis in fillDistritos"
                    v-bind:key="optDis.iddepartamento + optDis.idprovincia + optDis.id"
                    v-bind:value="optDis.id" 
                    v-text="optDis.descripcion"></option>
                </select>
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
                  <span class="input-group-text">E-mail</span>
                </div>
                <input
                  type="text"
                  class="form-control text-lowercase"
                  placeholder="Correo electrónico"
                  v-model="email"
                >
              </div>
              <div v-show="errorEmpresa" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjEmpresa"
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
              @click="registrarEmpresa()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarEmpresa()"
            >Actualizar</button>
          </div>
          <!-- / End Modal Footer -->
        </div>
      </div>
    </div>
    <!-- / End Create-Update Modal -->
  </main>
</template>
<script>
export default {
  data() {
    return {
      empresa_id: 0,
      ruc: "",
      nombre: "",
      direccion: "",
      telefono: "",
      email: "",
      departamento: 0,
      provincia: 0,
      distrito: 0,
      arrayEmpresa: [],
      fillDepartamentos: [],
      fillProvincias: [],
      fillDistritos: [],
      ubigeo: {
        departamentos: [],
        provincias: [],
        distritos: [],
        allLoaded: false
      },
      modal: 0,
      loadingModal: false,
      tituloModal: "",
      tipoAccion: 0,
      errorEmpresa: 0,
      errorMostrarMsjEmpresa: [],
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
      offset: 3,
      criterio: "ruc",
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
    listarEmpresa(page, buscar, criterio) {
      let me = this;
      var url =
        "/empresa?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayEmpresa = respuesta.empresas.data;

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
      me.listarEmpresa(page, buscar, criterio);
    },
    actualizarEmpresa() {
      if (this.validarEmpresa()) {
        return;
      }

      let me = this;
      axios
        .put("/empresa/actualizar", {
          ruc: this.ruc,
          nombre: this.nombre,
          direccion: this.direccion,
          iddepartamento: this.departamento,
          idprovincia: this.provincia,
          iddistrito: this.distrito,
          telefono: this.telefono,
          email: this.email,
          id: this.empresa_id
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarEmpresa(1, "", "nombre");
        })
        .catch(function(error) {
          /*console.log(error.response);*/
          if (error.response.status == 403) {
            me.errorEmpresa = 1;
            me.errorMostrarMsjEmpresa.push(
              error.response.data.message || error.response.statusText
            );
          }
        });
    },
    validarEmpresa() {
      this.errorEmpresa = 0;
      this.errorMostrarMsjEmpresa = [];

      if (!this.nombre)
        this.errorMostrarMsjEmpresa.push(
          "El nombre de la empresa no puede estar vacío."
        );

      if (this.errorMostrarMsjEmpresa.length) this.errorEmpresa = 1;

      return this.errorEmpresa;
    },
    obtenerUbigeo() {
      let me = this;

      axios
        .get("/departamento/all", {})
        .then(function(response) {
          me.ubigeo.departamentos = response.data.departamentos || [];

          axios
            .get("/provincia/all", {})
            .then(function(response) {
              me.ubigeo.provincias = response.data.provincias || [];

              axios
                .get("/distrito/all", {})
                .then(function(response) {
                  me.ubigeo.distritos = response.data.distritos || [];

                  me.ubigeo.allLoaded = true;
                  me.fillDepartamentos = me.ubigeo.departamentos;
                })
                .catch(function(error) {
                  console.log(error);
                });
            })
            .catch(function(error) {
              console.log(error);
            });
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cerrarModal() {
      let me = this;

      me.modal = 0;
      me.tituloModal = "";
      me.ruc = "";
      me.nombre = "";
      me.direccion = "";
      me.departamento = 0;
      me.provincia = 0;
      me.distrito = 0;
      me.telefono = "";
      me.email = "";
    },
    abrirModal(modelo, accion, data = []) {
      let me = this;

      if (!me.ubigeo.allLoaded) {
        return;
      }

      switch (modelo) {
        case "empresa": {
          me.loadingModal = true;
          switch (accion) {
            case "actualizar": {
              //console.log(data);
              me.modal = 1;
              me.tituloModal = '<i class="icon-pencil"></i> Actualizar empresa';
              me.tipoAccion = 2;
              me.empresa_id = data["id"];
              me.ruc = data["ruc"];
              me.nombre = data["nombre"];
              me.direccion = data["direccion"];
              me.departamento = data["iddepartamento"];
              me.provincia = data["idprovincia"];
              me.distrito = data["iddistrito"];
              me.telefono = data["telefono"];
              me.email = data["email"];
              break;
            }
          }
          setTimeout(
            function() {
              me.loadingModal = false;
            }.bind(me),
            500
          );
        }
      }
    }
  },
  watch: {
    departamento: function() {
      let me = this;

      if (me.loadingModal == false) {
        me.provincia = 0;
        me.distrito = 0;
      }

      me.fillProvincias = [];
      me.fillDistritos = [];

      if (me.departamento != 0) {
        me.ubigeo.provincias.map(function(obj, ind) {
          if (me.departamento == obj.iddepartamento) {
            me.fillProvincias.push(obj);
          }
        });
      }
    },
    provincia: function() {
      let me = this;

      if (me.loadingModal == false) {
        me.distrito = 0;
      }

      me.fillDistritos = [];

      if (me.provincia != 0) {
        me.ubigeo.distritos.map(function(obj, ind) {
          if (
            me.departamento == obj.iddepartamento &&
            me.provincia == obj.idprovincia
          ) {
            me.fillDistritos.push(obj);
          }
        });
      }
    }
  },
  mounted() {
    this.obtenerUbigeo();
    this.listarEmpresa(1, this.buscar, this.criterio);
  }
};
</script>
