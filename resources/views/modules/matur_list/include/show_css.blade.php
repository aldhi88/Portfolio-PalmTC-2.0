<link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<style>
    .table td, .table th{
        white-space: normal !important;
    }

    .table th{
        vertical-align: middle !important;
    }

    table, table thead th, .column-search{
        /* font-size: 13px !important; */
        line-height: 130% !important;
    }
    table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
        padding-right: 10px !important;
    }
    .dataTables_filter { 
        display: none; 
    }

    table.dataTable tbody tr:hover {
        background-color: #ffa;
    }

    table.dataTable tbody tr:hover > .sorting_1 {
        background-color: #ffa;
    }

    
</style>