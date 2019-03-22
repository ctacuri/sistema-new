<div class="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav">
                    <li class="nav-item" v-bind:class="{'nav-item-active': isActive([0])}">
                        <a class="nav-link" @click.prevent="setActive(0)"><i class="icon-speedometer"></i> Dashboard</a>
                    </li>
                    {{-- 
                    <li class="nav-title">
                        Mantenimiento
                    </li>
                    --}}
                    @if (Auth::user()->puede('VER_PRODUCTOS') || Auth::user()->puede('VER_CATEGORIAS'))
                    <li class="nav-item nav-dropdown" v-bind:class="{'nav-item-active': isActive([1,2]), 'open': isOpen('almacen')}">
                        <a class="nav-link nav-dropdown-toggle" @click.prevent="toogleOpenClass('almacen')"><i class="icon-layers"></i> Almacén</a>
                        <ul class="nav-dropdown-items">
                            @if (Auth::user()->puede('VER_CATEGORIAS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([1])}">
                                <a class="nav-link" @click.prevent="setActive(1)"><i class="icon-folder"></i> Categorías</a>
                            </li>
                            @endif
                            @if (Auth::user()->puede('VER_PRODUCTOS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([2])}">
                                <a class="nav-link" @click.prevent="setActive(2)"><i class="icon-tag"></i> Productos</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->puede('VER_INGRESOS') || Auth::user()->puede('VER_PROVEEDORES'))
                    <li class="nav-item nav-dropdown" v-bind:class="{'nav-item-active': isActive([3,4]), 'open': isOpen('compras')}">
                        <a class="nav-link nav-dropdown-toggle" @click.prevent="toogleOpenClass('compras')"><i class="icon-handbag"></i> Compras</a>
                        <ul class="nav-dropdown-items">
                            @if (Auth::user()->puede('VER_INGRESOS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([3])}">
                                <a class="nav-link" @click.prevent="setActive(3)"><i class="icon-present"></i> Ingresos</a>
                            </li>
                            @endif
                            @if (Auth::user()->puede('VER_PROVEEDORES'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([4])}">
                                <a class="nav-link" @click.prevent="setActive(4)"><i class="icon-briefcase"></i> Proveedores</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->puede('VER_VENTAS') || Auth::user()->puede('VER_CLIENTES'))
                    <li class="nav-item nav-dropdown" v-bind:class="{'nav-item-active': isActive([5,6]), 'open': isOpen('ventas')}">
                        <a class="nav-link nav-dropdown-toggle" @click.prevent="toogleOpenClass('ventas')"><i class="icon-basket"></i> Ventas</a>
                        <ul class="nav-dropdown-items">
                            @if (Auth::user()->puede('VER_VENTAS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([5])}">
                                <a class="nav-link" @click.prevent="setActive(5)"><i class="icon-basket-loaded"></i> Ventas</a>
                            </li>
                            @endif
                            @if (Auth::user()->puede('VER_CLIENTES'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([6])}">
                                <a class="nav-link" @click.prevent="setActive(6)"><i class="icon-wallet"></i> Clientes</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->puede('VER_USUARIOS') || Auth::user()->puede('VER_ROLES'))
                    <li class="nav-item nav-dropdown" v-bind:class="{'nav-item-active': isActive([7,8]), 'open': isOpen('acceso')}">
                        <a class="nav-link nav-dropdown-toggle" @click.prevent="toogleOpenClass('acceso')"><i class="icon-people"></i> Acceso</a>
                        <ul class="nav-dropdown-items">
                            @if (Auth::user()->puede('VER_USUARIOS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([7])}">
                                <a class="nav-link" @click.prevent="setActive(7)"><i class="icon-user"></i> Usuarios</a>
                            </li>
                            @endif
                            @if (Auth::user()->puede('VER_ROLES'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([8])}">
                                <a class="nav-link" @click.prevent="setActive(8)"><i class="icon-user-following"></i> Roles</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->puede('VER_INGRESOS') || Auth::user()->puede('VER_VENTAS'))
                    <li class="nav-item nav-dropdown" v-bind:class="{'nav-item-active': isActive([9,10]), 'open': isOpen('reportes')}">
                        <a class="nav-link nav-dropdown-toggle"  @click.prevent="toogleOpenClass('reportes')"><i class="icon-graph"></i> Reportes</a>
                        <ul class="nav-dropdown-items">
                            @if (Auth::user()->puede('VER_INGRESOS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([9])}">
                                <a class="nav-link" @click.prevent="setActive(9)"><i class="icon-chart"></i> Reporte Ingresos</a>
                            </li>
                            @endif
                            @if (Auth::user()->puede('VER_VENTAS'))
                            <li class="nav-item" v-bind:class="{'nav-item-active': isActive([10])}">
                                <a class="nav-link" @click.prevent="setActive(10)"><i class="icon-pie-chart"></i> Reporte Ventas</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->puede('VER_EMPRESAS'))
                    <li class="nav-item" v-bind:class="{'nav-item-active': isActive([13])}">
                        <a class="nav-link" @click.prevent="setActive(13)"><i class="icon-home"></i> Empresa</a>
                    </li>
                    @endif
                </ul>
            </nav>
            <button class="sidebar-minimizer brand-minimizer" type="button"></button>
        </div>