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
      <li class="breadcrumb-item active" aria-current="page">Ventas</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-pie-chart"></i> Reporte de ventas
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options"></div>
          <!-- / End Header Options -->
        </div>

        <!-- Listado-->
        <template v-if="listado==1">

          <div class="card-body">
            <!-- Filter Form -->
            <div class="form-group row">
              <div class="col-md-6">
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
                    @keyup.enter="listarVenta(1,buscar,criterio)"
                    v-model="buscar"
                  >
                  <div class="input-group-append">
                    <button
                      class="btn btn-primary"
                      type="submit"
                      @click="listarVenta(1,buscar,criterio)"
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
                  <th>Cliente</th>
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
                <tr v-for="venta in arrayVenta" :key="venta.id">
                  <td>
                    <!-- Row Options -->
                    <div class="row-options">
                      <button
                        type="button"
                        @click="verVenta(venta.id)"
                        class="btn btn-link"
                      >
                        <i class="icon-eye"></i>
                      </button> &nbsp;
                      <button
                        type="button"
                        @click="pdfVenta(venta.id)"
                        class="btn btn-link"
                      >
                        <i class="icon-doc"></i>
                      </button>
                    </div>
                    <!-- / End Row Options -->
                  </td>
                  <td v-text="venta.usuario"></td>
                  <td v-text="venta.nombre"></td>
                  <td v-text="venta.tipo_comprobante"></td>
                  <td v-text="venta.serie_comprobante"></td>
                  <td v-text="venta.num_comprobante"></td>
                  <td v-text="venta.fecha_hora"></td>
                  <td v-text="venta.total"></td>
                  <td v-text="venta.impuesto"></td>
                  <td v-text="venta.estado"></td>
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
                      <label>Cliente</label>
                      <input readonly="readonly" type="text" v-bind:value="cliente">
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
                            <th>Descuento</th>
                            <th>Subtotal</th>
                          </tr>
                        </thead>
                        <tbody v-if="arrayDetalle.length">
                          <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                            <td v-text="detalle.articulo"></td>
                            <td v-text="detalle.precio" class="text-right"></td>
                            <td v-text="detalle.cantidad" class="text-right"></td>
                            <td v-text="detalle.descuento" class="text-right"></td>
                            <td class="text-right">{{ ((detalle.precio * detalle.cantidad) - detalle.descuento).toFixed(2) | commaformat }}</td>
                          </tr>
                          <tr>
                            <th colspan="4" class="text-right">Total Parcial:</th>
                            <td
                              class="text-right"
                              style="background-color: rgba(80, 168, 216, 0.2);"
                            >
                              <!--$-->
                              {{ totalParcial = parseFloat(total - totalImpuesto).toFixed(2) | commaformat }}
                            </td>
                          </tr>
                          <tr>
                            <th colspan="4" class="text-right">Total Impuesto:</th>
                            <td
                              class="text-right"
                              style="background-color: rgba(80, 168, 216, 0.4);"
                            >
                              <!--$-->
                              {{ totalImpuesto = parseFloat(total * impuesto).toFixed(2) | commaformat }}
                            </td>
                          </tr>
                          <tr>
                            <th colspan="4" class="text-right">Total Neto:</th>
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
                            <td colspan="5">No hay productos agregados</td>
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
      venta_id: 0,
      idcliente: 0,
      cliente: "",
      tipo_comprobante: "",
      serie_comprobante: "",
      num_comprobante: "",
      impuesto: 0.18,
      total: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      arrayVenta: [],
      arrayCliente: [],
      arrayDetalle: [],
      listado: 1,
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorVenta: 0,
      errorMostrarMsjVenta: [],
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
      cantidad: 0,
      descuento: 0,
      stock: 0
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
          (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad -
            this.arrayDetalle[i].descuento);
      }
      return resultado;
    }
  },
  methods: {
    listarVenta(page, buscar, criterio) {
      let me = this;
      var url =
        "/venta?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          me.arrayVenta = respuesta.ventas.data;
          me.pagination = respuesta.pagination;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    pdfVenta(id) {
      window.open("/venta/pdf/" + id + "," + "_blank");
    },
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarVenta(page, buscar, criterio);
    },
    mostrarDetalle() {
      let me = this;
      me.listado = 0;

      me.idcliente = 0;
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

      me.idcliente = 0;
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

      me.cliente = "";
      me.fecha_guardada = null;
    },
    verVenta(id) {
      let me = this;
      me.listado = 2;

      //Obtener los datos del ingreso
      var arrayVentaT = [];
      var url = "/venta/obtenerCabecera?id=" + id;

      axios
        .get(url)
        .then(function(response) {
          var respuesta = response.data;
          arrayVentaT = respuesta.venta;

          me.fecha_guardada = arrayVentaT[0]["fecha_hora"];
          me.cliente = arrayVentaT[0]["nombre"];
          me.tipo_comprobante = arrayVentaT[0]["tipo_comprobante"];
          me.serie_comprobante = arrayVentaT[0]["serie_comprobante"];
          me.num_comprobante = arrayVentaT[0]["num_comprobante"];
          me.impuesto = arrayVentaT[0]["impuesto"];
          me.total = arrayVentaT[0]["total"];
        })
        .catch(function(error) {
          console.log(error);
        });

      //Obtener los datos de los detalles
      var urld = "/venta/obtenerDetalles?id=" + id;

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
    this.listarVenta(1, this.buscar, this.criterio);
  }
};
</script>
<style>
</style>
