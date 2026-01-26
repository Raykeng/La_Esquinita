<?php
// modulos/dashboard.php
// Dashboard principal traducido
?>

<div class="dashboard-main-body">

    <!-- Header removed as requested -->

    <div class="row gy-4">
        <div class="col-12">
            <div class="card radius-12">
                <div class="card-body p-16">
                    <div class="row gy-4">
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Ventas Brutas</span>
                                        <h6 class="fw-semibold mb-1">Q 40,000.00</h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                        <i class="ri-shopping-cart-fill"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> 12%</span> vs mes pasado </p>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Compras Totales</span>
                                        <h6 class="fw-semibold mb-1">Q 35,000.00</h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-lilac-200 text-lilac-600">
                                        <i class="ri-handbag-fill"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> 5%</span> vs mes pasado </p>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Ingresos Totales</span>
                                        <h6 class="fw-semibold mb-1">Q 30,000.00</h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-200 text-success-600">
                                        <i class="ri-money-dollar-circle-fill"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm"><i class="ri-arrow-right-down-line"></i> 30%</span> vs mes pasado </p>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Gastos Totales</span>
                                        <h6 class="fw-semibold mb-1">Q 7,000.00</h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                        <i class="ri-wallet-3-fill"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> 60%</span> vs mes pasado </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-8">
            <div class="card h-100">
                <div class="card-body p-24 mb-8">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Ingresos vs Gastos</h6>
                        <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <option>Anual</option>
                            <option>Mensual</option>
                            <option>Semanal</option>
                            <option>Hoy</option>
                        </select>
                    </div>
                    <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                        <li class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Ingresos</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <h6 class="mb-0">Q 26,201</h6>
                                <span class="text-success-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                    10%
                                    <i class="ri-arrow-up-s-fill d-flex"></i>
                                </span>
                            </div>
                        </li>
                        <li class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">Gastos</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <h6 class="mb-0">Q 18,120</h6>
                                <span class="text-danger-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                    10%
                                    <i class="ri-arrow-down-s-fill d-flex"></i>
                                </span>
                            </div>
                        </li>
                    </ul>
                    <div id="incomeExpense" class="apexcharts-tooltip-style-1"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Usuarios</h6>
                        <a href="javascript:void(0)" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            Ver Todos
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <!-- Static User List - Can be dynamic later -->
                <div class="card-body p-20">
                    <div class="d-flex flex-column gap-24">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/user-grid/user-grid-img1.png" alt="" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                <div class="flex-grow-1">
                                    <h6 class="text-md mb-0">Administrador</h6>
                                    <span class="text-sm text-secondary-light fw-normal">Super Admin</span>
                                </div>
                            </div>
                            <span class="text-success-main fw-medium text-md">Activo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Proveedores Top</h6>
                        <a href="javascript:void(0)" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            Ver Todos
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="text-secondary-light">1</span></td>
                                    <td><span class="text-secondary-light">Coca Cola</span></td>
                                    <td><span class="text-secondary-light">Q 3,000.00</span></td>
                                </tr>
                                <tr>
                                    <td><span class="text-secondary-light">2</span></td>
                                    <td><span class="text-secondary-light">Pepsi</span></td>
                                    <td><span class="text-secondary-light">Q 4,000.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Clientes Top</h6>
                         <a href="javascript:void(0)" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            Ver Todos
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="text-secondary-light">1</span></td>
                                    <td><span class="text-secondary-light">Cliente Frecuente</span></td>
                                    <td><span class="text-secondary-light">Q 500.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg">Reporte General</h6>
                         <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <option>Esta Semana</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-24">
                     <div class="mt-32">
                        <div id="userOverviewDonutChart" class="mx-auto apexcharts-tooltip-z-none"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-20 justify-content-center mt-48">
                        <div class="d-flex align-items-center gap-8">
                            <span class="w-16-px h-16-px radius-2 bg-primary-600"></span>
                            <span class="text-secondary-light">Compras</span>
                        </div>
                        <div class="d-flex align-items-center gap-8">
                            <span class="w-16-px h-16-px radius-2 bg-lilac-600"></span>
                            <span class="text-secondary-light">Ventas</span>
                        </div>
                        <div class="d-flex align-items-center gap-8">
                            <span class="w-16-px h-16-px radius-2 bg-warning-600"></span>
                            <span class="text-secondary-light">Gastos</span>
                        </div>
                         <div class="d-flex align-items-center gap-8">
                            <span class="w-16-px h-16-px radius-2 bg-success-600"></span>
                            <span class="text-secondary-light">Ganancia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                 <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Compras & Ventas</h6>
                        <select class="form-select form-select-sm w-auto bg-base text-secondary-light">
                            <option>Este Mes</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-24">
                     <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-3">
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-8-px rounded-pill bg-warning-600"></span>
                            <span class="text-secondary-light text-sm fw-semibold">Compras: Q<span class="text-primary-light fw-bold">500</span>
                            </span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-8-px rounded-pill bg-success-600"></span>
                            <span class="text-secondary-light text-sm fw-semibold">Ventas: Q<span class="text-primary-light fw-bold">800</span>
                            </span>
                        </li>
                    </ul>
                    <div id="purchaseSaleChart" class="margin-16-minus y-value-left"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
             <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Transacciones Recientes</h6>
                        <a href="javascript:void(0)" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            Ver Todos
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                </div>
                 <div class="card-body p-24">
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Método</th>
                                    <th scope="col">Monto Pagado</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="text-secondary-light">1</span></td>
                                    <td><span class="text-secondary-light">21 Jun 2024</span></td>
                                    <td><span class="text-secondary-light">Efectivo</span></td>
                                    <td><span class="text-secondary-light">Q 150.00</span></td>
                                    <td><span class="text-secondary-light">Q 150.00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// ===================== Income VS Expense Start =============================== 
function createChartTwo(chartId, color1, color2) {
    var options = {
        series: [{
            name: "Ingresos",
            data: [48, 35, 50, 32, 48, 40, 55, 50, 60]
        }, {
            name: "Egresos",
            data: [12, 20, 15, 26, 22, 30, 25, 35, 25]
        }],
        legend: {
            show: false
        },
        chart: {
            type: "area",
            width: "100%",
            height: 270,
            toolbar: {
                show: false
            },
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: "smooth",
            width: 3,
            colors: [color1, color2], // Use two colors for the lines
            lineCap: "round"
        },
        grid: {
            show: true,
            borderColor: "#D1D5DB",
            strokeDashArray: 1,
            position: "back",
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            row: {
                colors: undefined,
                opacity: 0.5
            },
            column: {
                colors: undefined,
                opacity: 0.5
            },
            padding: {
                top: -20,
                right: 0,
                bottom: -10,
                left: 0
            },
        },
        fill: {
            type: "gradient",
            colors: [color1, color2], // Use two colors for the gradient
            gradient: {
                shade: "light",
                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: [undefined, `${color2}00`], // Apply transparency to both colors
                inverseColors: false,
                opacityFrom: [0.4, 0.6], // Starting opacity for both colors
                opacityTo: [0.3, 0.3], // Ending opacity for both colors
                stops: [0, 100],
            },
        },
        markers: {
            colors: [color1, color2], // Use two colors for the markers
            strokeWidth: 3,
            size: 0,
            hover: {
                size: 10
            }
        },
        xaxis: {
            labels: {
                show: false
            },
            categories: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            tooltip: {
                enabled: false
            },
            labels: {
                formatter: function(value) {
                    return value;
                },
                style: {
                    fontSize: "14px"
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return "Q" + value + "k";
                },
                style: {
                    fontSize: "14px"
                }
            },
        },
        tooltip: {
            x: {
                format: "dd/MM/yy HH:mm"
            }
        }
    };

    var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
    chart.render();
}

createChartTwo("incomeExpense", "#487FFF", "#FF9F29");
// ===================== Income VS Expense End =============================== 

// ================================ Users Overview Donut chart Start ================================ 
var options = {
    series: [30, 30, 20, 20],
    colors: ["#FF9F29", "#487FFF", "#45B369", "#9935FE"],
    labels: ["Compras", "Ventas", "Gastos", "Ganancias"],
    legend: {
        show: false
    },
    chart: {
        type: "donut",
        height: 270,
        sparkline: {
            enabled: true // Remove whitespace
        },
        margin: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        },
        padding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        }
    },
    stroke: {
        width: 0,
    },
    dataLabels: {
        enabled: true
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: "bottom"
            }
        }
    }],
};

var chart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), options);
chart.render();
// ================================ Users Overview Donut chart End ================================ 

// ================================ Purchase & sale chart End ================================ 
var options = {
    series: [{
        name: "Ganancia Neta",
        data: [44, 100, 40, 56, 30, 58, 50]
    }, {
        name: "Efectivo Libre",
        data: [60, 120, 60, 90, 50, 95, 90]
    }],
    colors: ["#45B369", "#FF9F29"],
    labels: ["Activo", "Nuevo", "Total"],

    legend: {
        show: false
    },
    chart: {
        type: "bar",
        height: 260,
        toolbar: {
            show: false
        },
    },
    grid: {
        show: true,
        borderColor: "#D1D5DB",
        strokeDashArray: 4, // Use a number for dashed style
        position: "back",
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: 8,
        },
    },
    dataLabels: {
        enabled: false
    },
    states: {
        hover: {
            filter: {
                type: "none"
            }
        }
    },
    stroke: {
        show: true,
        width: 0,
        colors: ["transparent"]
    },
    xaxis: {
        categories: ["Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"],
    },
    fill: {
        opacity: 1,
        width: 18,
    },
};

var chart = new ApexCharts(document.querySelector("#purchaseSaleChart"), options);
chart.render();
// ================================ Purchase & sale chart End ================================ 
</script>