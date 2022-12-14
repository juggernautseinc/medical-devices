<?php

/*
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";

use OpenEMR\Core\Header;

?>
<!DOCTYPE html>

<head>
    <?php Header::setupHeader(['report-helper', 'common']); ?>
    <meta charset="utf-8" />
    <title><?php echo xlt('Patient Monitoring'); ?></title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js">
    </script>
    <style>
    /* Finder Processing style */
    div.dataTables_wrapper div.dataTables_processing {
        width: auto;
        margin: 0;
        color: var(--danger);
        transform: translateX(-50%);
    }

    .card {
        border: 0;
        border-radius: 0;
    }

    @media screen and (max-width: 640px) {

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            float: inherit;
            text-align: justify;
        }
    }

    /* Color Overrides for jQuery-DT */
    table.dataTable thead th,
    table.dataTable thead td {
        border-bottom: 1px solid var(--gray900) !important;
    }

    table.dataTable tfoot th,
    table.dataTable tfoot td {
        border-top: 1px solid var(--gray900) !important;
    }

    table.dataTable tbody tr {
        background-color: var(--white) !important;
        cursor: pointer;
    }

    table.dataTable.row-border tbody th,
    table.dataTable.row-border tbody td,
    table.dataTable.display tbody th,
    table.dataTable.display tbody td {
        border-top: 1px solid var(--gray300) !important;
    }

    table.dataTable.cell-border tbody th,
    table.dataTable.cell-border tbody td {
        border-top: 1px solid var(--gray300) !important;
        border-right: 1px solid var(--gray300) !important;
    }

    table.dataTable.cell-border tbody tr th:first-child,
    table.dataTable.cell-border tbody tr td:first-child {
        border-left: 1px solid var(--gray300) !important;
    }

    table.dataTable.stripe tbody tr.odd,
    table.dataTable.display tbody tr.odd {
        background-color: var(--light) !important;
    }

    table.dataTable.hover tbody tr:hover,
    table.dataTable.display tbody tr:hover {
        background-color: var(--light) !important;
    }

    table.dataTable.order-column tbody tr>.sorting_1,
    table.dataTable.order-column tbody tr>.sorting_2,
    table.dataTable.order-column tbody tr>.sorting_3,
    table.dataTable.display tbody tr>.sorting_1,
    table.dataTable.display tbody tr>.sorting_2,
    table.dataTable.display tbody tr>.sorting_3 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.odd>.sorting_1,
    table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.odd>.sorting_2,
    table.dataTable.order-column.stripe tbody tr.odd>.sorting_2 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.odd>.sorting_3,
    table.dataTable.order-column.stripe tbody tr.odd>.sorting_3 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.even>.sorting_1,
    table.dataTable.order-column.stripe tbody tr.even>.sorting_1 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.even>.sorting_2,
    table.dataTable.order-column.stripe tbody tr.even>.sorting_2 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr.even>.sorting_3,
    table.dataTable.order-column.stripe tbody tr.even>.sorting_3 {
        background-color: var(--light) !important;
    }

    table.dataTable.display tbody tr:hover>.sorting_1,
    table.dataTable.order-column.hover tbody tr:hover>.sorting_1 {
        background-color: var(--gray200) !important;
    }

    table.dataTable.display tbody tr:hover>.sorting_2,
    table.dataTable.order-column.hover tbody tr:hover>.sorting_2 {
        background-color: var(--gray200) !important;
    }

    table.dataTable.display tbody tr:hover>.sorting_3,
    table.dataTable.order-column.hover tbody tr:hover>.sorting_3 {
        background-color: var(--gray200) !important;
    }

    table.dataTable.display tbody .odd:hover,
    table.dataTable.display tbody .even:hover {
        background-color: var(--gray200) !important;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid var(--gray900) !important;
    }

    .dataTables_wrapper .dataTables_processing {
        background-color: var(--white) !important;
        background: -webkit-gradient(linear, left top, right top, color-stop(0%, transparent), color-stop(25%, rgba(var(--black), 0.9)), color-stop(75%, rgba(var(--black), 0.9)), color-stop(100%, transparent)) !important;
        background: -webkit-linear-gradient(left, transparent 0%, rgba(var(--black), 0.9) 25%, rgba(var(--black), 0.9) 75%, transparent 100%) !important;
        background: -moz-linear-gradient(left, transparent 0%, rgba(var(--black), 0.9) 25%, rgba(var(--black), 0.9) 75%, transparent 100%) !important;
        background: -ms-linear-gradient(left, transparent 0%, rgba(var(--black), 0.9) 25%, rgba(var(--black), 0.9) 75%, transparent 100%) !important;
        background: -o-linear-gradient(left, transparent 0%, rgba(var(--black), 0.9) 25%, rgba(var(--black), 0.9) 75%, transparent 100%) !important;
        background: linear-gradient(to right, transparent 0%, rgba(var(--black), 0.9) 25%, rgba(var(--black), 0.9) 75%, transparent 100%) !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: var(--dark) !important;
    }

    .dataTables_wrapper.no-footer .dataTables_scrollBody {
        border-bottom: 1px solid var(--gray900) !important;
    }

    /* Pagination button Overrides for jQuery-DT */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
    }

    /* Sort indicator Overrides for jQuery-DT */
    table thead .sorting::before,
    table thead .sorting_asc::before,
    table thead .sorting_asc::after,
    table thead .sorting_desc::before,
    table thead .sorting_desc::after,
    table thead .sorting::after {
        display: none !important;
    }
    </style>

    <script>
    function add_patient() {
        var url = 'form/add_patient.php';
        dlgopen(url, '_blank', 620, 360, '', 'Add Patient', {
            onClosed: 'reload'
        });

    }
    </script>

</head>

<body class="body_top">
    <div>
        <a href="#" class="btn btn-secondary" onclick="goBack()" style="margin-top:10px;"><i
                class="fa fa-arrow-left"><?php echo xlt('  Go back'); ?></i></a>
    </div>
    <h3 class="mt-4 pb-4">List Devices</h3>
    <div id="container_div" class="mt-3">
        <div class="w-100">
            <div class="jumbotron mt-3 p-4">
                <div id="dynamic">
                    <div class="table-responsive">
                        <input type="hidden" name="pid" id="pid" value="<?php echo $_GET['pid'];?>">
                        <table id="example" cellpadding="0" cellspacing="0" class="border-0 display">
                            <thead>
                                <tr class="table-primary">
                                    <th>#</th>
                                    <th>SubEhrEmrId</th>
                                    <th>Device Id</th>
                                    <th>Device Model</th>
                                    <th>Device Maker</th>
                                    <th>Device OS</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
$(document).ready(function() {
    var pid = $('#pid').val();
    $('#example').DataTable({
        "ajax": "list_device_ajax.php?pid=" + pid
    });
});

function goBack() {
    window.history.back();
}

$(document).on('click', '#edit', function() {
    var id = $(this).attr('data-id');

    var url = 'edit_devices.php?id=' + id;
    dlgopen(url, '_blank', 620, 360, '', 'Edit Devices', {
        onClosed: 'reload'
    });

});
$(document).on('click', '#delete', function() {
    var pid = $(this).attr('data-id');

    $.post("delete_devices.php", {
            pid: pid,
        },
        function(data, status) {
            alert("Status: " + data);
            location.reload();
        });
});
</script>




</html>
