<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>

var dtTable = $('#myTable').DataTable({
    processing: true,serverSide: true,scrollX: true,pageLength: 25,
    // destroy: true,
    order: [ [6, 'desc'],[7, 'desc'] ],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: '{{ route("embryo-lists.dtShow") }}',
        data: function(d){ // note !
            d.initId = '{{ $data["initId"] }}';
            d.filter = $('select[name="filterActive"] option:selected').val();
        }
    },
    columns: [
        { data: 'import', name: 'import', orderable:true, searchable:true},
        { data: 'bottle_date_format', name: 'bottle_date_format', orderable:true, searchable:true},
        { data: 'tc_inits.tc_samples.program', name: 'tc_inits.tc_samples.program', orderable:true, searchable:true},
        { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:true, searchable:true},
        { data: 'sub', name: 'sub', orderable:true, searchable:true},
        { data: 'tc_workers.code', name: 'tc_workers.code', orderable:true, searchable:true},
        { data: 'number_of_bottle', name: 'bottle_date', orderable:true, searchable:false},
        { data: 'last_total', name: 'created_at', orderable:true, searchable:false},
    ],
    initComplete: function () {
        $('#header-filter th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search px-1 form-control-sm text-center"/>');
            }
        });
        $('#header-filter').on('keyup', ".column-search",function () {
            dtTable.column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
        $('#myTable_filter').css('display','inline');
        $('#myTable_filter').html(
            '<div class="row"><div class="col"></div><div class="col">'+
                '<select name="filterActive" class="form-control form-control-sm">'+
                    '<option value="1">Active</option>'+
                    '<option value="0">All Data</option>'+
                '</select>'+
            '</div></div>'
        );
        $('select[name="filterActive"]').change(function(){
            dtTable.ajax.reload();
        });
    },
    footerCallback: function ( row, data, start, end, display ) {
        var api = this.api(), data;
        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };
        // Total over this page
        firstTotal = api.column( 6, { page: 'current'} ).data().reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
        lastTotal = api.column( 7, { page: 'current'} ).data().reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Update footer
        $('#firstTotal').html(firstTotal);
        $('#lastTotal').html(lastTotal);
    }
});

var dtTable2 = $('#myTable2').DataTable({
    processing: true,serverSide: true,
    scrollX: true,
    pageLength: 50,
    order: [ [5,'desc'],[9,'desc'],[6,'desc'] ],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url : '{{ route("embryo-lists.dtShow2") }}',
        data : {
            initId:'{{ $data["initId"] }}'
        },
    },
    columns: [
        { data: 'bottle_date_format', name: 'bottle_date_format', orderable:false, searchable:true},
        { data: 'tc_inits.tc_samples.program', name: 'tc_inits.tc_samples.program', orderable:true, searchable:true},
        { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:true, searchable:true},
        { data: 'tc_embryo_bottles.sub', name: 'tc_embryo_bottles.sub', orderable:true, searchable:true},
        { data: 'tc_embryo_bottles.tc_workers.code', name: 'tc_embryo_bottles.tc_workers.code', orderable:true, searchable:true},
        { data: 'first_total', name: 'tc_embryo_bottles.bottle_date', orderable:true, searchable:true},
        { data: 'obs_date', name: 'created_at', orderable:true, searchable:true},
        { data: 'transfer_date', name: 'transfer_date', orderable:true, searchable:true},
        { data: 'tc_workers.code', name: 'tc_workers.code', orderable:true, searchable:true},
        { data: 'last_total', name: 'tc_embryo_bottles.created_at', orderable:true, searchable:true},
    ],
    initComplete: function () {
        $('#header-filter2 th').each(function() {
            var title = $(this).text();
            var disable = $(this).attr("disable");
            if(disable!="true"){
                $(this).html('<input placeholder="'+title+'" type="text" class="form-control column-search2 px-1 form-control-sm text-center"/>');
            }
        });
        $('#header-filter2').on('keyup', ".column-search2",function () {
            dtTable2.column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    }
});


</script>
