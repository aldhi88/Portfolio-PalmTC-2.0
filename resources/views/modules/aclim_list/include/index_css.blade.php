<link href="{{asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
<style>
    .table td, .table th{
        white-space: normal !important;
    }

    .table th{
        vertical-align: middle !important;
    }

    #ingredient{
        line-height: 120% !important;
        font-size: 11px !important;
    }

    table, table thead th, .column-search{
        line-height: 130% !important;
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