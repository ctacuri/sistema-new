<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">Almacén</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Categorías</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-folder"></i> Categorías
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_CATEGORIAS']">
              <button type="button" @click="abrirModal('categoria','registrar')" class="btn btn-link" >
                <i class="icon-plus"></i>&nbsp;Agregar categoría
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
                  @keyup.enter="listarCategoria(1,buscar,criterio)"
                  v-model="buscar"
                >
                <div class="input-group-append">
                  <button
                    class="btn btn-primary"
                    type="submit"
                    @click="listarCategoria(1,buscar,criterio)"
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
              <tr v-for="categoria in arrayCategoria" :key="categoria.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_CATEGORIAS']">
                      <button
                        type="button"
                        @click="abrirModal('categoria','actualizar',categoria)"
                        class="btn btn-link"
                      >
                        <i class="icon-pencil"></i>
                      </button> &nbsp;
                      <template v-if="categoria.condicion">
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="desactivarCategoria(categoria.id)"
                        >
                          <i class="icon-trash"></i>
                        </button>
                      </template>
                      <template v-else>
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="activarCategoria(categoria.id)"
                        >
                          <i class="icon-check"></i>
                        </button>
                      </template>
                    </template>
                  </div>
                  <!-- / End Row Options -->
                </td>
                <td v-text="categoria.nombre"></td>
                <td v-text="categoria.descripcion"></td>
                <td>
                  <div v-if="categoria.condicion">
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
                  <span class="input-group-text">Nombre</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Nombre de la categoría"
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
                  placeholder="Descripción de la categoría"
                  v-model="descripcion"
                >
              </div>
              <div v-show="errorCategoria" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjCategoria"
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
              @click="registrarCategoria()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarCategoria()"
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
      categoria_id: 0,
      nombre: "",
      descripcion: "",
      arrayCategoria: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorCategoria: 0,
      errorMostrarMsjCategoria: [],
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
    listarCategoria(page, buscar, criterio) {
      let me = this;
      var url =
        "/categoria?page=" +
        page +
        "&buscar=" +
        buscar +
        "&criterio=" +
        criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayCategoria = respuesta.categorias.data;
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
      me.listarCategoria(page, buscar, criterio);
    },
    async registrarCategoria() {
      if (await this.validarCategoria()) {
        return;
      }

      let me = this;

      axios
        .post("/categoria/registrar", {
          nombre: this.nombre,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarCategoria(1, "", "nombre");
        })
        .catch(function(error) {
          /*console.log(error.response);*/
          if (error.response.status == 403) {
            me.errorCategoria = 1;
            me.errorMostrarMsjCategoria.push(
              error.response.data.message || error.response.statusText
            );
          }
        });
    },
    async actualizarCategoria() {
      if (await this.validarCategoria()) {
        return;
      }

      let me = this;

      axios
        .put("/categoria/actualizar", {
          nombre: this.nombre,
          descripcion: this.descripcion,
          id: this.categoria_id
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarCategoria(1, "", "nombre");
        })
        .catch(function(error) {
          /*console.log(error.response);*/
          if (error.response.status == 403) {
            me.errorCategoria = 1;
            me.errorMostrarMsjCategoria.push(
              error.response.data.message || error.response.statusText
            );
          }
        });
    },
    desactivarCategoria(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de desactivar esta categoría?",
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
              .put("/categoria/desactivar", {
                id: id
              })
              .then(function(response) {
                me.listarCategoria(1, "", "nombre");
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
    activarCategoria(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de activar esta categoría?",
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
              .put("/categoria/activar", {
                id: id
              })
              .then(function(response) {
                me.listarCategoria(1, "", "nombre");
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
    },
    async validarCategoria() {
      let me = this;

      this.errorCategoria = 0;
      this.errorMostrarMsjCategoria = [];

      if (!this.nombre) {
        this.errorMostrarMsjCategoria.push(
          "El nombre de la categoría no puede estar vacío."
        );
      } else {
        var nombreExiste = false;
        let response = await axios
          .get("/categoria/validarSiExiste", {
            params: {
              nombre: me.nombre,
              id: me.tipoAccion == 1 ? 0 : me.categoria_id
            }
          })
          .then(function(response) {
            nombreExiste = response.data.exists;
          })
          .catch(function(error) {
            console.log(error);
          });

        if (nombreExiste) {
          this.errorMostrarMsjCategoria.push(
            "Ya existe una categoría con este nombre."
          );
        }
      }

      if (this.errorMostrarMsjCategoria.length) this.errorCategoria = 1;

      return this.errorCategoria;
    },
    cerrarModal() {
      this.modal = 0;
      this.tituloModal = "";
      this.nombre = "";
      this.descripcion = "";
    },
    abrirModal(modelo, accion, data = []) {
      switch (modelo) {
        case "categoria": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = '<i class="icon-plus"></i> Agregar Categoría';
              this.nombre = "";
              this.descripcion = "";
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              this.modal = 1;
              this.tituloModal =
                '<i class="icon-pencil"></i> Actualizar Categoría';
              this.tipoAccion = 2;
              this.categoria_id = data["id"];
              this.nombre = data["nombre"];
              this.descripcion = data["descripcion"];
              break;
            }
          }
        }
      }
    }
  },
  mounted() {
    this.listarCategoria(1, this.buscar, this.criterio);
  }
};
</script>
