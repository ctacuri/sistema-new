<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="#">Escritorio</a>
      </li>
      <li class="breadcrumb-item">
        <a href="#">Ventas</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">Clientes</li>
    </ol>
    <!-- / End Breadcrumb -->
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <!-- Title -->
          <h3>
            <i class="icon-wallet"></i> Clientes
          </h3>
          <!-- / End Title -->
          <!-- Header Options -->
          <div class="card-header-options">
            <template v-if="$parent.granted['AGREGAR_CLIENTES']">
              <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-link">
                <i class="icon-plus"></i>&nbsp;Agregar cliente
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
                <th>Numero</th>
                <th>Direccion</th>
                <th>Telefono</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="persona in arrayPersona" :key="persona.id">
                <td>
                  <!-- Row Options -->
                  <div class="row-options">
                    <template v-if="$parent.granted['ACTUALIZAR_CLIENTES']">
                      <button
                        type="button"
                        @click="abrirModal('persona','actualizar',persona)"
                        class="btn btn-link"
                      >
                        <i class="icon-pencil"></i>
                      </button>
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
    <!-- Create/Update Modal -->
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
    <!-- / End Create/Update Modal -->
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
      arrayPersona: [],
      modal: 0,
      tituloModal: "",
      tipoAccion: 0,
      errorPersona: 0,
      errorMostrarMsjPersona: [],
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
        "/cliente?page=" + page + "&buscar=" + buscar + "&criterio=" + criterio;
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
    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarCategoria(page, buscar, criterio);
    },
    registrarPersona() {
      if (this.validarPersona()) {
        return;
      }

      let me = this;

      axios
        .post("/cliente/registrar", {
          nombre: this.nombre,
          tipo_documento: this.tipo_documento,
          num_documento: this.num_documento,
          direccion: this.direccion,
          telefono: this.telefono,
          email: this.email
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
        .put("/cliente/actualizar", {
          nombre: this.nombre,
          tipo_documento: this.tipo_documento,
          num_documento: this.num_documento,
          direccion: this.direccion,
          telefono: this.telefono,
          email: this.email,
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

      if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

      return this.errorPersona;
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
      this.errorPersona = 0;
    },
    abrirModal(modelo, accion, data = []) {
      switch (modelo) {
        case "persona": {
          switch (accion) {
            case "registrar": {
              this.modal = 1;
              this.tituloModal = '<i class="icon-plus"></i> Registrar Cliente';
              this.nombre = "";
              this.tipo_documento = "";
              this.num_documento = "";
              this.direccion = "";
              this.telefono = "";
              this.email = "";
              this.tipoAccion = 1;
              break;
            }
            case "actualizar": {
              //console.log(data);
              this.modal = 1;
              this.tituloModal = '<i class="icon-pencil"></i> Actualizar Cliente';
              this.tipoAccion = 2;
              this.persona_id = data["id"];
              this.nombre = data["nombre"];
              this.tipo_documento = data["tipo_documento"];
              this.num_documento = data["num_documento"];
              this.direccion = data["direccion"];
              this.telefono = data["telefono"];
              this.email = data["email"];
              break;
            }
          }
        }
      }
    }
  },
  mounted() {
    this.listarPersona(1, this.buscar, this.criterio);
  }
};
</script>
<style>
</style>
