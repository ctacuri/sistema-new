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
      <li class="breadcrumb-item active" aria-current="page">Productos</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-tag"></i> Productos
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_PRODUCTOS']">
              <button
                type="button"
                @click="abrirModal('articulo','registrar')"
                class="btn btn-link"
              >
                <i class="icon-plus"></i>&nbsp;Agregar producto
              </button>
            </template>
            <template v-if="$parent.granted['IMPORTAR_PRODUCTOS']">
              <button type="button" @click="abrirModal('articulo','importar')" class="btn btn-link">
                <i class="icon-cloud-upload"></i>&nbsp;Importar productos
              </button>
            </template>
            <template v-if="$parent.granted['AGREGAR_CATEGORIAS']">
              <button
                type="button"
                @click="abrirModal('categoria','registrar')"
                class="btn btn-link"
              >
                <i class="icon-plus"></i>&nbsp;Agregar categoría
              </button>
            </template>
            <template v-if="$parent.granted['GENERAR_REPORTE_PRODUCTOS']">
              <button type="button" @click="cargarPdf()" class="btn btn-link">
                <i class="icon-notebook"></i>&nbsp;Generar reporte
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
                  @keyup.enter="listarArticulo(1,buscar,criterio)"
                  v-model="buscar"
                >
                <div class="input-group-append">
                  <button
                    class="btn btn-primary"
                    type="submit"
                    @click="listarArticulo(1,buscar,criterio)"
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
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio Venta</th>
                <!--<th>Stock</th>-->
                <th>Descripción</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_PRODUCTOS']">
                      <button
                        type="button"
                        @click="abrirModal('articulo','actualizar',articulo)"
                        class="btn btn-link"
                      >
                        <i class="icon-pencil"></i>
                      </button> &nbsp;
                      <template v-if="articulo.condicion">
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="desactivarArticulo(articulo.id)"
                        >
                          <i class="icon-trash"></i>
                        </button>
                      </template>
                      <template v-else>
                        <button
                          type="button"
                          class="btn btn-link"
                          @click="activarArticulo(articulo.id)"
                        >
                          <i class="icon-check"></i>
                        </button>
                      </template>
                    </template>
                  </div>
                  <!-- / End Row Options -->
                </td>
                <td v-text="articulo.codigo"></td>
                <td v-text="articulo.nombre"></td>
                <td v-text="articulo.nombre_categoria"></td>
                <td v-text="articulo.precio_venta" class="text-right"></td>
                <!--<td v-text="articulo.stock" class="text-right"></td>-->
                <td v-text="articulo.descripcion"></td>
                <td>
                  <div v-if="articulo.condicion">
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
                  <span class="input-group-text">Categoría</span>
                </div>
                <select
                  class="custom-select"
                  required="required"
                  v-model="idcategoria"
                  v-bind:class="{'placeholder': !(idcategoria || false) }"
                >
                  <option disabled value="0">Categoría</option>
                  <option
                    v-for="categoria in arrayCategoria"
                      :key="categoria.id"
                      :value="categoria.id"
                      v-text="categoria.nombre">
                  </option>
                </select>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Código</span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Código de producto"
                  v-model="codigo"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"></span>
                </div>
                <div class="barcode-wrapper">
                  <barcode
                    :value="codigo"
                    :options="{ format: 'EAN-13' }"
                  >
                    <em><i class="icon icon-tag"></i> Digite arriba para generar el código de barras.</em>
                  </barcode>
                </div>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombre</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Nombre del producto"
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
                  placeholder="Descripción del producto"
                  v-model="descripcion"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Precio de venta</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Precio del producto"
                  v-model="precio_venta"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Precio alt. 02</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Precio alternativo 02"
                  v-model="precio_02"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Precio alt. 03</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Precio alternativo 03"
                  v-model="precio_03"
                >
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Precio alt. 04</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Precio alternativo 04"
                  v-model="precio_04"
                >
              </div>
              <!--
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Stock</span>
                </div>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  placeholder="Stock"
                  v-model="stock"
                >
              </div>
              -->
              <div v-show="errorArticulo" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjArticulo"
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
              @click="registrarArticulo()"
            >Guardar</button>
            <button
              type="button"
              v-if="tipoAccion==2"
              class="btn btn-primary"
              @click="actualizarArticulo()"
            >Actualizar</button>
          </div>
          <!-- / End Modal Footer -->
        </div>
      </div>
    </div>
    <!-- / End Create-Update Modal -->
    <!-- Import Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'show-modal' : modalImportar}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" v-html="tituloModalImportar"></h4>
            <button
              type="button"
              v-if="!procesandoImportacion"
              class="close"
              @click="cerrarModalImportar()"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <!-- / End Modal Header -->
          <!-- Modal Body -->
          <div class="modal-body">
            <form action method="post" enctype="multipart/form-data" class="vf-form">
              <div class="info-block">
                <p>
                  <a class="emphasis-color"
                    href="formatos/FORMATO-PRODUCTOS.csv"
                    target="_blank"
                  ><i class="icon-arrow-down-circle"></i> Descargue el formato</a>, llénelo con la información actualizada de sus productos y cárguelo nuevamente mediante el siguente formulario.
                </p>
                <p>El archivo debe pesar máximo 2MB.</p>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Archivo CSV</span>
                </div>
                <input
                    type="file"
                    id="file"
                    ref="file"
                    class="form-control"
                    v-on:change="validarArchivo()"
                />
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Delimitador</span>
                </div>
                <select  class="custom-select"
                  required="required" 
                  v-model="delimitador"
                  v-bind:class="{'placeholder': !(delimitador || false) }"
                  >
                  <option value=",">coma ( , )</option>
                  <option value=";">punto y coma ( ; )</option>
                </select>
              </div>
              <div v-show="errorImportar" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjImportar"
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
              v-bind:disabled="procesandoImportacion"
              class="btn btn-link"
              @click="cerrarModalImportar()"
            >Cancelar</button>
            <button
              type="button"
              v-bind:disabled="procesandoImportacion"
              class="btn btn-primary"
              v-on:click="procesarImportar()"
            >
              <span v-if="!procesandoImportacion">Procesar</span>
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
    <!-- / End Import Modal -->
    <!-- Create Category Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{'show-modal' : modalCategoria}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" v-html="tituloModalCategoria"></h4>
            <button type="button" class="close" @click="cerrarModalCategoria()" aria-label="Close">
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
                  v-model="nombreCategoria"
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
                  v-model="descripcionCategoria"
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
            <button type="button" class="btn btn-link" @click="cerrarModalCategoria()">Cancelar</button>
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
    <!-- / End Create Category Modal -->
  </main>
</template>

<script>
import VueBarcode from "vue-barcode";
export default {
  data() {
    return {
      articulo_id: 0,
      idcategoria: 0,
      nombre_categoria: "",
      codigo: "",
      nombre: "",
      precio_venta: 0,
      precio_02: 0,
      precio_03: 0,
      precio_04: 0,
      stock: 0,
      descripcion: "",
      arrayArticulo: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorArticulo: 0,
      errorMostrarMsjArticulo: [],

      procesandoImportacion: false,
      archivo: null,
      delimitador: ",",
      modalImportar: 0,
      tituloModalImportar: "",
      errorImportar: 0,
      errorMostrarMsjImportar: [],

      modalCategoria: 0,
      tituloModalCategoria: "",
      nombreCategoria: "",
      descripcionCategoria: "",
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
      buscar: "",
      arrayCategoria: []
    };
  },
  components: {
    barcode: VueBarcode
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
    listarArticulo(page, buscar, criterio) {
      let me = this;
      var url =
        "/articulo?page=" +
        page +
        "&buscar=" +
        buscar +
        "&criterio=" +
        criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayArticulo = respuesta.articulos.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cargarPdf() {
      window.open("/articulo/listarPdf", "_blank");
    },
    selectCategoria() {
      let me = this;
      var url = "/categoria/selectCategoria";
      axios
        .get(url)
        .then(function(response) {
          //console.log(response);
          var respuesta = response.data;
          me.arrayCategoria = respuesta.categorias;
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
      me.listarArticulo(page, buscar, criterio);
    },
    registrarArticulo() {
      if (this.validarArticulo()) {
        return;
      }

      let me = this;

      axios
        .post("/articulo/registrar", {
          idcategoria: this.idcategoria,
          codigo: this.codigo,
          nombre: this.nombre,
          stock: this.stock,
          precio_venta: this.precio_venta,
          precio_02: this.precio_02,
          precio_03: this.precio_03,
          precio_04: this.precio_04,
          descripcion: this.descripcion
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarArticulo(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    actualizarArticulo() {
      if (this.validarArticulo()) {
        return;
      }

      let me = this;

      axios
        .put("/articulo/actualizar", {
          idcategoria: this.idcategoria,
          codigo: this.codigo,
          nombre: this.nombre,
          stock: this.stock,
          precio_venta: this.precio_venta,
          precio_02: this.precio_02,
          precio_03: this.precio_03,
          precio_04: this.precio_04,
          descripcion: this.descripcion,
          id: this.articulo_id
        })
        .then(function(response) {
          me.cerrarModal();
          me.listarArticulo(1, "", "nombre");
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    procesarImportar() {
      let me = this;

      if (me.procesandoImportacion) {
        return;
      }

      me.archivo = me.$refs.file.files[0];
      if (me.validarImportar()) {
        return;
      }

      let formData = new FormData();
      formData.append("archivo", me.archivo);
      formData.append("delimitador", me.delimitador);

      me.procesandoImportacion = true;

      axios
        .post("/articulo/importarcsv", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(function(response) {
          if (response.data.success == true) {
            swal({
              title: "Importación finalizada!",
              text: response.data.message,
              type: "success",
              confirmButtonText: "Listo",
              confirmButtonClass: "btn btn-primary",
              buttonsStyling: false
            });
            me.cerrarModalImportar();
            me.listarArticulo(1, "", "nombre");
          } else {
            swal({
              title: "Error en la importación!",
              text: response.data.message,
              type: "error",
              confirmButtonText: "De acuerdo",
              confirmButtonClass: "btn btn-primary",
              buttonsStyling: false
            });
          }
          me.procesandoImportacion = false;
        })
        .catch(function(error) {
          console.log(error);
          me.procesandoImportacion = false;
        });
    },

    registrarCategoria() {
      if (this.validarCategoria()) {
        return;
      }
      let me = this;
      axios
        .post("/categoria/registrar", {
          nombre: this.nombreCategoria,
          descripcion: this.descripcionCategoria
        })
        .then(function(response) {
          me.cerrarModalCategoria();
          me.selectCategoria();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    desactivarArticulo(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de desactivar este producto?",
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
              .put("/articulo/desactivar", {
                id: id
              })
              .then(function(response) {
                me.listarArticulo(1, "", "nombre");
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
    activarArticulo(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de activar este producto?",
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
              .put("/articulo/activar", {
                id: id
              })
              .then(function(response) {
                me.listarArticulo(1, "", "nombre");
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
    validarArticulo() {
      this.errorArticulo = 0;
      this.errorMostrarMsjArticulo = [];

      if (this.idcategoria == 0)
        this.errorMostrarMsjArticulo.push("Seleccione una categoría.");
      if (!this.nombre)
        this.errorMostrarMsjArticulo.push(
          "El nombre del artículo no puede estar vacío."
        );
      //if (!this.stock) this.errorMostrarMsjArticulo.push("El stock del artículo debe ser un número y no puede estar vacío.");
      if (!this.precio_venta)
        this.errorMostrarMsjArticulo.push(
          "El precio venta del artículo debe ser un número y no puede estar vacío."
        );

      if (this.errorMostrarMsjArticulo.length) this.errorArticulo = 1;

      return this.errorArticulo;
    },
    validarArchivo() {
      var inputfile = this.$refs.file.files[0];
      var maxSize = 2000000;
      if (inputfile) {
        console.log(inputfile);
        if (inputfile.name.substr(-4) != ".csv" || inputfile.size > maxSize) {
          swal({
              title: "Archivo no válido!",
              text: 'El archivo debe tener la extensión ".csv" y pesar máximo 2MB.',
              type: "error",
              confirmButtonText: "De acuerdo",
              confirmButtonClass: "btn btn-primary",
              buttonsStyling: false
            });
          this.$refs.file.value = null;
        }
      }
    },
    validarImportar() {
      this.errorImportar = 0;
      this.errorMostrarMsjImportar = [];
      console.log(this.archivo);
      if (this.archivo == null)
        this.errorMostrarMsjImportar.push("Seleccione un archivo.");

      if (this.errorMostrarMsjImportar.length) this.errorImportar = 1;

      return this.errorImportar;
    },
    validarCategoria() {
      this.errorCategoria = 0;
      this.errorMostrarMsjCategoria = [];

      if (!this.nombreCategoria)
        this.errorMostrarMsjCategoria.push(
          "El nombre de la categoría no puede estar vacío."
        );

      if (this.errorMostrarMsjCategoria.length) this.errorCategoria = 1;

      return this.errorCategoria;
    },
    cerrarModal() {
      this.modal = 0;
      this.tituloModal = "";
      this.idcategoria = 0;
      this.nombre_categoria = "";
      this.codigo = "";
      this.nombre = "";
      this.precio_venta = 0;
      this.precio_02 = 0;
      this.precio_03 = 0;
      this.precio_04 = 0;
      this.stock = 0;
      this.descripcion = "";
      this.errorArticulo = 0;
    },
    cerrarModalImportar() {
      this.modalImportar = 0;
      this.tituloModalImportar = "";
      this.archivo = null;
    },
    cerrarModalCategoria() {
      this.modalCategoria = 0;
      this.tituloModalCategoria = "";
      this.nombreCategoria = "";
      this.descripcionCategoria = "";
    },
    abrirModal(modelo, accion, data = []) {
      switch (modelo) {
        case "articulo": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = '<i class="icon-plus"></i> Agregar Producto';
              this.idcategoria = 0;
              this.nombre_categoria = "";
              this.codigo = "";
              this.nombre = "";
              this.precio_venta = 0;
              this.precio_02 = 0;
              this.precio_03 = 0;
              this.precio_04 = 0;
              this.stock = 0;
              this.descripcion = "";
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              //console.log(data);
              this.modal = 1;
              this.tituloModal = '<i class="icon-pencil"></i> Actualizar Producto';
              this.tipoAccion = 2;
              this.articulo_id = data["id"];
              this.idcategoria = data["idcategoria"];
              this.codigo = data["codigo"];
              this.nombre = data["nombre"];
              this.stock = data["stock"];
              this.precio_venta = data["precio_venta"];
              this.precio_02 = data["precio_02"];
              this.precio_03 = data["precio_03"];
              this.precio_04 = data["precio_04"];
              this.descripcion = data["descripcion"];
              break;
            }
            case "importar": {
              //console.log(data);
              this.modalImportar = 1;
              this.tituloModalImportar = '<i class="icon-cloud-upload"></i> Importar Productos';
              this.tipoAccion = 1;
              this.archivo = null;
              break;
            }
          }
          break;
        }

        case "categoria": {
          switch (accion) {
            case "registrar": {
              this.modalCategoria = 1;
              this.tituloModalCategoria = '<i class="icon-plus"></i> Agregar Categoría';
              this.nombreCategoria = "";
              this.descripcionCategoria = "";
              this.tipoAccion = 1;
              break;
            }
          }
          break;
        }
      }
      this.selectCategoria();
    }
  },
  mounted() {
    this.listarArticulo(1, this.buscar, this.criterio);
  }
};
</script>

