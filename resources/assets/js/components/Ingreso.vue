<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">Compras</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Ingresos</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-present"></i> Ingresos
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_INGRESOS']">
              <button
                type="button"
                @click="prepararNuevoIngreso()"
                class="btn btn-link"
                v-if="listado != 0"
              >
                <i class="icon-plus"></i>&nbsp;Registrar ingreso
              </button>
              <button
                type="button"
                @click="cancelarTodo()"
                class="btn btn-link cancel-color"
                v-else
              >
                <i class="icon-close"></i>&nbsp;Cancelar registro
              </button>
            </template>
          </div>
          <!-- / End Header Options -->
        </div>
        <!-- List -->
        <template v-if="listado==1">
          <div class="card-body">
            <!-- Filter Form -->
            <div class="form-group row">
              <div class="col-md-5">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Filtro</span>
                    <select class="custom-select" v-model="criterio">
                      <option value="tipo_comprobante">Por tipo de comprobante</option>
                      <option value="num_comprobante">Por número de comprobante</option>
                      <option value="fecha_hora">Por fecha-hora</option>
                    </select>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Texto a buscar..."
                    @keyup.enter="listarIngreso(1,buscar,criterio)"
                    v-model="buscar"
                  >
                  <div class="input-group-append">
                    <button
                      class="btn btn-primary"
                      type="submit"
                      @click="listarIngreso(1,buscar,criterio)"
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
                  <th>Usuario</th>
                  <th>Proveedor</th>
                  <th>Tipo Comprobante</th>
                  <th>Serie Comprobante</th>
                  <th>Número Comprobante</th>
                  <th>Fecha Hora</th>
                  <th>Total</th>
                  <th>Impuesto</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ingreso in arrayIngreso" :key="ingreso.id">
                  <td>
                    <!-- Row Options -->
                    <div class="row-options">
                      <button
                        type="button"
                        @click="verDetalleIngreso(ingreso.id)"
                        class="btn btn-link"
                      >
                        <i class="icon-eye"></i>
                      </button> &nbsp;
                      <template v-if="$parent.granted['ACTUALIZAR_INGRESOS']">
                        <template v-if="ingreso.estado=='Registrado'">
                          <button
                            type="button"
                            class="btn btn-link"
                            @click="desactivarIngreso(ingreso.id)"
                          >
                            <i class="icon-trash"></i>
                          </button>
                        </template>
                      </template>
                    </div>
                    <!-- / End Row Options -->
                  </td>
                  <td v-text="ingreso.usuario"></td>
                  <td v-text="ingreso.nombre"></td>
                  <td v-text="ingreso.tipo_comprobante"></td>
                  <td v-text="ingreso.serie_comprobante"></td>
                  <td v-text="ingreso.num_comprobante"></td>
                  <td v-text="ingreso.fecha_hora"></td>
                  <td v-text="ingreso.total" class="text-right"></td>
                  <td v-text="ingreso.impuesto" class="text-right"></td>
                  <td v-text="ingreso.estado"></td>
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
        </template>
        <!-- / End List-->
        <!-- Create Box -->
        <template v-else-if="listado==0">
          <div class="card-body">
            <div class="vf-form">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Proveedor(*)</span>
                    </div>
                    <div class="v-select-wrapper">
                      <v-select
                        id="search-proveedor"
                        :on-search="selectProveedor"
                        label="fullname"
                        :options="arrayProveedor"
                        placeholder="Buscar Proveedores..."
                        :onChange="getDatosProveedor"
                      ></v-select>
                      <input type="hidden" v-model="impuesto">
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Comprobante(*)</span>
                    </div>
                    <select
                      class="custom-select"
                      required="required"
                      v-model="tipo_comprobante"
                      v-bind:class="{'placeholder': (tipo_comprobante == 0) }"
                      @change="validarImpuesto()"
                    >
                      <option value="0" selected disabled="disabled">Seleccione</option>
                      <option value="BOLETA">Boleta</option>
                      <option value="FACTURA">Factura</option>
                      <option value="TICKET">Ticket</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Serie</span>
                    </div>
                    <input
                      type="text"
                      class="form-control text-uppercase"
                      placeholder="000x"
                      v-model="serie_comprobante"
                    >
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Número(*)</span>
                    </div>
                    <input
                      type="text"
                      class="form-control text-uppercase"
                      placeholder="000xx"
                      v-model="num_comprobante"
                    >
                  </div>
                </div>
              </div>

              <div v-show="errorIngreso" class="modal-errors">
                <div
                  v-for="error in errorMostrarMsjIngreso"
                  :key="error"
                  class="alert alert-warning"
                  role="alert"
                >
                  <i class="icon-ban"></i>
                  <em v-text="error"></em>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Buscar prod.</span>
                    </div>
                    <input
                      type="text"
                      class="form-control text-uppercase"
                      placeholder="Digite código de producto"
                      v-model="codigo"
                      @keyup.enter="buscarArticulo()"
                    >
                    <div class="input-group-append">
                      <button @click="abrirModal()" class="btn btn-primary">...</button>
                    </div>
                  </div>
                </div>
                <div class="col-md-8">
                  <!-- Empty -->
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text no-top-border">Producto</span>
                    </div>
                    <input
                      type="text"
                      readonly="readonly"
                      class="form-control text-uppercase no-top-border"
                      placeholder
                      v-model="articulo"
                    >
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text"
                        v-bind:class="{'error-color': (idarticulo!=0 && precio<=0)}"
                      >Precio</span>
                    </div>
                    <input
                      type="number"
                      value="0.00"
                      step="any"
                      class="form-control"
                      placeholder="0.00"
                      v-model="precio"
                    >
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span
                        class="input-group-text"
                        v-bind:class="{'error-color': (idarticulo!=0 && cantidad<=0)}"
                      >Cantidad
                        <span>(*)</span>
                      </span>
                    </div>
                    <input
                      type="number"
                      value="0.00"
                      step="any"
                      class="form-control"
                      placeholder="0.00"
                      v-model="cantidad"
                    >
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group">
                    <button
                      @click="agregarDetalle()"
                      class="btn btn-link"
                      v-bind:disabled="(idarticulo==0 || precio<=0 || cantidad<=0)"
                    >
                      <i class="icon-plus"></i> Agregar
                    </button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <!-- Table Products -->
                  <table class="table table-bordered table-striped table-align-middle">
                    <thead>
                      <tr>
                        <th>Opciones</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody v-if="arrayDetalle.length">
                      <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                        <td>
                          <!-- Row Options -->
                          <div class="row-options">
                            <button
                              @click="eliminarDetalle(index)"
                              type="button"
                              class="btn btn-link"
                            >
                              <i class="icon-close"></i>
                            </button>
                          </div>
                          <!-- / End Row Options -->
                        </td>
                        <td v-text="detalle.articulo"></td>
                        <td class="text-right">
                          <input
                            v-model="detalle.precio"
                            type="number"
                            class="form-control"
                          >
                        </td>
                        <td class="text-right">
                          <input
                            v-model="detalle.cantidad"
                            type="number"
                            class="form-control"
                          >
                        </td>
                        <td
                          class="text-right"
                        >{{ parseFloat(detalle.precio*detalle.cantidad).toFixed(2) | commaformat }}</td>
                      </tr>
                      <tr>
                        <th colspan="4" class="text-right">Total Parcial:</th>
                        <td class="text-right" style="background-color: rgba(80, 168, 216, 0.2);">
                          <!--$-->
                          {{ totalParcial = parseFloat(total - totalImpuesto).toFixed(2) | commaformat }}
                        </td>
                      </tr>
                      <tr>
                        <th colspan="4" class="text-right">Total Impuesto:</th>
                        <td class="text-right" style="background-color: rgba(80, 168, 216, 0.4);">
                          <!--$-->
                          {{ totalImpuesto = parseFloat((total * impuesto) / (1 + impuesto)).toFixed(2) | commaformat }}
                        </td>
                      </tr>
                      <tr>
                        <th colspan="4" class="text-right">Total Neto:</th>
                        <td class="text-right" style="background-color: rgba(80, 168, 216, 0.6);">
                          <!--$-->
                          {{ total = parseFloat(calcularTotal).toFixed(2) | commaformat }}
                        </td>
                      </tr>
                    </tbody>
                    <tbody v-else>
                      <tr>
                        <td colspan="5">No hay productos agregados</td>
                      </tr>
                    </tbody>
                  </table>
                  <!-- / End Table Products -->
                  <!-- Additional Error Block -->
                  <div v-show="errorIngreso" v-if="arrayDetalle.length > 10" class="modal-errors">
                    <div
                      v-for="error in errorMostrarMsjIngreso"
                      :key="error"
                      class="alert alert-warning"
                      role="alert"
                    >
                      <i class="icon-ban"></i>
                      <em v-text="error"></em>
                    </div>
                  </div>
                  <!-- / End Additional Error Block -->
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group">
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click="registrarIngreso()"
                    >Registrar Compra</button>
                    <button type="button" @click="cancelarTodo()" class="btn btn-link">Cancelar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!-- / End Create Box -->
        <!-- View Details -->
        <template v-else-if="listado==2">
          <div class="card-body">
            <div class="invoice-box">
              <div class="invoice-inner">
                <div class="row">
                  <div class="col-md-10">
                    <div class="detail-block">
                      <label>Proveedor</label>
                      <input readonly="readonly" type="text" v-bind:value="proveedor">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="detail-block">
                      <label>Impuesto</label>
                      <input readonly="readonly" type="text" v-bind:value="impuesto">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="detail-block">
                      <label>Tipo Comprobante</label>
                      <input readonly="readonly" type="text" v-bind:value="tipo_comprobante">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="detail-block">
                      <label>Serie Comprobante</label>
                      <input readonly="readonly" type="text" v-bind:value="serie_comprobante">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="detail-block">
                      <label>Número Comprobante</label>
                      <input readonly="readonly" type="text" v-bind:value="num_comprobante">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="detail-block">
                      <label>Fecha</label>
                      <input readonly="readonly" type="text" v-bind:value="fecha_guardada">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="detail-block">
                      <!-- Table Summary -->
                      <table class="table table-bordered table-striped table-align-middle">
                        <thead>
                          <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                          </tr>
                        </thead>
                        <tbody v-if="arrayDetalle.length">
                          <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                            <td v-text="detalle.articulo"></td>
                            <td v-text="detalle.precio" class="text-right"></td>
                            <td v-text="detalle.cantidad" class="text-right"></td>
                            <td
                              class="text-right"
                            >{{ (detalle.precio * detalle.cantidad).toFixed(2) | commaformat }}</td>
                          </tr>
                          <tr>
                            <th colspan="3" class="text-right">Total Parcial:</th>
                            <td
                              class="text-right"
                              style="background-color: rgba(80, 168, 216, 0.2);"
                            >
                              <!--$-->
                              {{ totalParcial = parseFloat(total - totalImpuesto).toFixed(2) | commaformat }}
                            </td>
                          </tr>
                          <tr>
                            <th colspan="3" class="text-right">Total Impuesto:</th>
                            <td
                              class="text-right"
                              style="background-color: rgba(80, 168, 216, 0.4);"
                            >
                              <!--$-->
                              {{ totalImpuesto = parseFloat(total * impuesto).toFixed(2) | commaformat }}
                            </td>
                          </tr>
                          <tr>
                            <th colspan="3" class="text-right">Total Neto:</th>
                            <td
                              class="text-right"
                              style="background-color: rgba(80, 168, 216, 0.6);"
                            >
                              <!--$-->
                              {{ total = parseFloat(total).toFixed(2) | commaformat }}
                            </td>
                          </tr>
                        </tbody>
                        <tbody v-else>
                          <tr>
                            <td colspan="4">No hay productos agregados</td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- / End Table Summary -->
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <button type="button" @click="cancelarTodo()" class="btn btn-link">
                      <i class="icon icon-arrow-left"></i> Volver
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!-- / End View Details -->
      </div>
    </div>
    <!-- 
      ** MODALS *
    -->
    <!-- Select Products Modal -->
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
            <div class="vf-form">
              <!-- Filter Form -->
              <div class="row">
                <div class="col-md-8">
                  <div class="input-group">
                    <div class="input-group-prepend with-select">
                      <span class="input-group-text">Filtro</span>
                      <select class="custom-select" v-model="criterioA">
                        <option value="nombre" selected>Por nombre</option>
                        <option value="descripcion">Por descripción</option>
                        <option value="codigo">Por código</option>
                      </select>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Texto a buscar..."
                      @keyup.enter="listarArticulo(buscarA,criterioA)"
                      v-model="buscarA"
                    >
                    <div class="input-group-append">
                      <button
                        class="btn btn-primary"
                        type="submit"
                        @click="listarArticulo(buscarA,criterioA)"
                      >
                        <i class="fa fa-search"></i> Buscar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- / End Filter Form -->
              <!-- Table Results -->
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-bordered table-striped table-align-middle">
                    <thead>
                      <tr>
                        <th>Opciones</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                        <td>
                          <!-- Row Options -->
                          <div class="row-options flex-center">
                            <button
                              type="button"
                              @click="agregarDetalleModal(articulo)"
                              class="btn btn-link"
                            >
                              <i class="icon-check"></i>
                            </button>
                          </div>
                          <!-- / End Row Options -->
                        </td>
                        <td v-text="articulo.codigo"></td>
                        <td v-text="articulo.nombre"></td>
                        <td v-text="articulo.nombre_categoria"></td>
                        <td v-text="articulo.precio_venta"></td>
                        <td v-text="articulo.stock"></td>
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
                </div>
              </div>
              <!-- / End Table Results -->
            </div>
          </div>
          <!-- / End Modal Body -->
          <!-- Modal Footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-link" @click="cerrarModal()">Cerrar</button>
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
    <!-- / End Select Products Modal-->
  </main>
</template>

<script>
import vSelect from "vue-select";
export default {
  data() {
    return {
      fecha_guardada: null,
      ingreso_id: 0,
      idproveedor: 0,
      proveedor: "",
      nombre: "",
      tipo_comprobante: "",
      serie_comprobante: "",
      num_comprobante: "",
      impuesto: 0,
      total: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      arrayIngreso: [],
      arrayProveedor: [],
      arrayDetalle: [],
      listado: 1,
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorIngreso: 0,
      errorMostrarMsjIngreso: [],
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
      offset: 3,
      criterio: "num_comprobante",
      buscar: "",
      criterioA: "nombre",
      buscarA: "",
      arrayArticulo: [],
      idarticulo: 0,
      codigo: "",
      articulo: "",
      precio: 0,
      cantidad: 0
    };
  },
  components: {
    vSelect
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
    },
    calcularTotal: function() {
      var resultado = 0.0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        resultado =
          resultado +
          this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad;
      }
      return resultado.toFixed(2);
    }
  },
  methods: {
    validarImpuesto() {
      let me = this;
      me.impuesto =
        me.tipo_comprobante === "BOLETA" || me.tipo_comprobante === "TICKET"
          ? 0
          : 0.18;
    },
    listarIngreso(page, buscar, criterio) {
      let me = this;
      var url =
        "/ingreso?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayIngreso = respuesta.ingresos.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    selectProveedor(search, loading) {
      let me = this;
      loading(true);

      var url = "/proveedor/selectProveedor?filtro=" + search;
      axios
        .get(url)
        .then(function(response) {
          //console.log(response);
          var respuesta = response.data;
          //q: search;

          respuesta.proveedores.map(function(obj, ind) {
            obj.fullname =
              obj.num_documento +
              " - " +
              obj.nombre +
              " <" +
              obj.contacto +
              ">";
          });

          me.arrayProveedor = respuesta.proveedores;
          loading(false);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    getDatosProveedor(val1) {
      let me = this;
      me.loading = true;
      me.idproveedor = val1.id;
    },
    buscarArticulo() {
      let me = this;
      var url = "/articulo/buscarArticulo?filtro=" + me.codigo;

      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayArticulo = respuesta.articulos;

          if (me.arrayArticulo.length > 0) {
            me.articulo = me.arrayArticulo[0]["nombre"];
            me.idarticulo = me.arrayArticulo[0]["id"];
          } else {
            me.articulo = "Ningún producto seleccionado";
            me.idarticulo = 0;
          }
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
      me.listarIngreso(page, buscar, criterio);
    },
    encuentra(id) {
      var sw = 0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        if (this.arrayDetalle[i].idarticulo == id) {
          sw = true;
        }
      }
      return sw;
    },
    eliminarDetalle(index) {
      let me = this;
      me.arrayDetalle.splice(index, 1);
    },
    agregarDetalle() {
      let me = this;
      //console.log(me);
      if (me.idarticulo == 0 || me.cantidad == 0 || me.precio == 0) {
      } else {
        if (me.encuentra(me.idarticulo)) {
          swal({
            type: "error",
            title: "Error...",
            text: "Ese producto ya se encuentra agregado!"
          });
        } else {
          me.arrayDetalle.push({
            idarticulo: me.idarticulo,
            articulo: me.articulo,
            cantidad: me.cantidad,
            precio: me.precio
          });
          me.codigo = "";
          me.idarticulo = 0;
          me.articulo = "";
          me.cantidad = 0;
          me.precio = 0;
        }
      }
    },
    agregarDetalleModal(data = []) {
      let me = this;
      /*
      if (me.encuentra(data["id"])) {
        swal({
          type: "error",
          title: "Error...",
          text: "Ese producto ya se encuentra agregado!"
        });
      } else {
        */
      me.arrayDetalle.push({
        idarticulo: data["id"],
        articulo: data["nombre"],
        cantidad: 1,
        precio: 1
      });
      /*}*/
    },
    listarArticulo(buscar, criterio) {
      let me = this;
      var url =
        "/articulo/listarArticulo?buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayArticulo = respuesta.articulos.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    registrarIngreso() {
      if (this.validarIngreso()) {
        return;
      }

      let me = this;

      axios
        .post("/ingreso/registrar", {
          idproveedor: this.idproveedor,
          tipo_comprobante: this.tipo_comprobante,
          serie_comprobante: this.serie_comprobante,
          num_comprobante: this.num_comprobante,
          impuesto:
            this.tipo_comprobante === "BOLETA" ||
            this.tipo_comprobante === "TICKET"
              ? 0
              : this.impuesto,
          total: this.total,
          data: this.arrayDetalle
        })
        .then(function(response) {
          me.listado = 1;
          me.listarIngreso(1, "", "num_comprobante");
          me.idproveedor = 0;
          me.tipo_comprobante = "BOLETA";
          me.serie_comprobante = "";
          me.num_comprobante = "";
          me.impuesto = 0.18;
          me.total = 0.0;
          me.idarticulo = 0;
          me.articulo = "";
          me.cantidad = 0;
          me.precio = 0;
          me.arrayDetalle = [];
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    validarIngreso() {
      this.errorIngreso = 0;
      this.errorMostrarMsjIngreso = [];

      if (this.idproveedor == 0)
        this.errorMostrarMsjIngreso.push("Seleccione un Proveedor");
      if (this.tipo_comprobante == 0)
        this.errorMostrarMsjIngreso.push("Seleccione el comprobante");
      if (!this.num_comprobante)
        this.errorMostrarMsjIngreso.push("Ingrese el número de comprobante");
      /*if (!this.impuesto)
        this.errorMostrarMsjIngreso.push("Ingrese el impuesto de compra");*/
      if (this.arrayDetalle.length <= 0)
        this.errorMostrarMsjIngreso.push("Ingrese detalles");

      if (this.errorMostrarMsjIngreso.length) this.errorIngreso = 1;

      return this.errorIngreso;
    },
    prepararNuevoIngreso() {
      let me = this;
      me.listado = 0;

      me.idproveedor = 0;
      me.tipo_comprobante = "";
      me.serie_comprobante = "";
      me.num_comprobante = "";
      me.impuesto = 0;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = "";
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];

      me.errorIngreso = 0;
      me.errorMostrarMsjIngreso = [];
    },
    cancelarTodo() {
      let me = this;

      me.listado = 1;

      me.idproveedor = 0;
      me.tipo_comprobante = "";
      me.serie_comprobante = "";
      me.num_comprobante = "";
      me.impuesto = 0;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = "";
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];

      me.proveedor = "";
      me.fecha_guardada = null;
    },
    verDetalleIngreso(id) {
      let me = this;
      me.listado = 2;

      //Obtener los datos del ingreso
      var arrayIngresoT = [];
      var url = "/ingreso/obtenerCabecera?id=" + id;

      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          arrayIngresoT = respuesta.ingreso;
          me.fecha_guardada = arrayIngresoT[0]["fecha_hora"];
          me.proveedor = arrayIngresoT[0]["nombre"];
          me.tipo_comprobante = arrayIngresoT[0]["tipo_comprobante"];
          me.serie_comprobante = arrayIngresoT[0]["serie_comprobante"];
          me.num_comprobante = arrayIngresoT[0]["num_comprobante"];
          me.impuesto = arrayIngresoT[0]["impuesto"];
          me.total = arrayIngresoT[0]["total"];
        })
        .catch(function(error) {
          console.log(error);
        });

      //Obtener los datos de los detalles
      var urld = "/ingreso/obtenerDetalles?id=" + id;

      axios
        .get(urld)
        .then(function(response) {
          console.log(response);
          var respuesta = response.data;
          me.arrayDetalle = respuesta.detalles;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    cerrarModal() {
      this.modal = 0;
      this.tituloModal = "";
    },
    abrirModal(modelo, accion, data = []) {
      this.arrayArticulo = [];
      this.modal = 1;
      this.tituloModal =
        '<i class="icon-present"></i> Seleccione uno o varios articulos';
    },
    desactivarIngreso(id) {
      swal.queue([
        {
          title: "",
          text: "¿Está seguro de anular este ingreso?",
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
              .put("/ingreso/desactivar", {
                id: id
              })
              .then(function(response) {
                me.listarIngreso(1, "", "num_comprobante");
                swal.insertQueueStep({
                  title: "Anulado!",
                  text: "El ingreso ha sido anulado con éxito.",
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
    }
  },

  mounted() {
    this.listarIngreso(1, this.buscar, this.criterio);
    this.validarImpuesto();
  }
};
</script>
<style>
</style>
