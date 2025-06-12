<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>

var dtTable = $('#myTable').DataTable({
    processing: true,serverSide: true,scrollX: true,pageLength: 25,order: [[5, 'desc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: '{{ route("aclim-lists.dtShow") }}',
        data: function(d){ // note !
            d.initId = '{{ $data["initId"] }}';
            d.filter = $('select[name="filterActive"] option:selected').val();
        }
    },
    columns: [
        { data: 'import', name: 'import', orderable:false, searchable:true},
        { data: 'tree_date_action', name: 'tree_date_format', orderable:false, searchable:true},
        { data: 'tc_inits.tc_samples.program', name: 'tc_inits.tc_samples.program', orderable:false, searchable:true},
        { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:false, searchable:true},
        { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:true},
        { data: 'total_data', name: 'tree_date', orderable:true, searchable:false},
        { data: 'total_death', name: 'total_death', orderable:false, searchable:false},
        { data: 'total_transfer', name: 'total_transfer', orderable:false, searchable:false},
        { data: 'total_active', name: 'total_active', orderable:false, searchable:false},
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
            dtTable
                .column( $(this).parent().index() )
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
        onClickDetail();
    },

});

function onClickDetail(){
    $('body').on('click','a.detail',function(){
        $('#select-data').text($(this).attr('data-date'));
        $('input[name="tab2Filter"]').val($(this).attr('data-id'));
        dtTable2.ajax.reload();
    })
}

var dtTable2 = $('#myTable2').DataTable({
    processing: true,serverSide: true,scrollX: true,pageLength: 25,
    order: [[1,'asc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: '{{ route("aclim-lists.dtShow2") }}',
        data: function(d){ // note !
            d.initId = '{{ $data["initId"] }}';
            d.filter = $('input[name="tab2Filter"]').val();
        }
    },
    columns: [
        { data: 'tree_date_format', name: 'tree_date_format', orderable:true, searchable:true},
        { data: 'index_number', name: 'index_number', orderable:false, searchable:true},
        { data: 'status_format', name: 'status_format', orderable:false, searchable:true},
        { data: 'skor_akar', name: 'skor_akar', orderable:false, searchable:false},
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
            dtTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
        initSwitch();
        changeSkorAkar();
    },

});

function initSwitch(){
    $("#myTable2").on("click","i.change",function(){
        loader(true);
        var status = parseInt($(this).attr('data-status'))-1;
        status = status*status;
        var id = $(this).attr('data-id');
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST', cache: false, processData: true,
            url: '{{ route("aclim-lists.changeStatus") }}',
            data: {
                status:status,
                id:id,
            },
            success: function(a) {
                dtTable2.ajax.reload();
                loader(false);
            },
            error: (a) => {
                alert("Error #003, InitiationController-getStep2 function is invalid.");
            }
        });
    })
}

function changeSkorAkar(){
    $('body').on('change','input.skor-akar',function(){
        var formData = {};
        formData.id = $(this).attr('data-id');
        formData.skor_akar = $(this).val();
        $.ajax({
            type: 'POST', cache: false, processData: true,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route("aclim-lists.changeSkorAkar") }}',
            data: formData,
            success: function(a) {
                $('#myTable2').DataTable().ajax.reload(function(){
                },false);
            },
            error: (a) => {
                alert("Error #003, CallusObservationController - store function is invalid.");
            }
        });

    });
}

</script>


