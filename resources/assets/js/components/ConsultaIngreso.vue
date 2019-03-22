<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">Reportes</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Ingresos</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-chart"></i> Reporte de ingresos
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options"></div>
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
                      <option value="tipo_comprobante" selected>Por tipo comprobante</option>
                      <option value="num_comprobante">Por nro. comprobante</option>
                      <option value="fecha_hora">Por fecha - hora</option>
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
                        @click="verIngreso(ingreso.id)"
                        class="btn btn-link"
                      >
                        <i class="icon-eye"></i>
                      </button>
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
      impuesto: 0.18,
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
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarIngreso(page, buscar, criterio);
    },
    mostrarDetalle() {
      let me = this;
      me.listado = 0;

      me.idproveedor = 0;
      me.tipo_comprobante = "";
      me.serie_comprobante = "";
      me.num_comprobante = "";
      me.impuesto = 0.18;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = "";
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];
    },
    cancelarTodo() {
      let me = this;
      me.listado = 1;

      me.idproveedor = 0;
      me.tipo_comprobante = "";
      me.serie_comprobante = "";
      me.num_comprobante = "";
      me.impuesto = 0.18;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = "";
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];

      me.proveedor = "";
      me.fecha_guardada = null;
    },
    verIngreso(id) {
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
    }
  },
  mounted() {
    this.listarIngreso(1, this.buscar, this.criterio);
  }
};
</script>
<style>
</style>