<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

<script>
var columns = [
    { data: 'tree_date_action', name: 'tree_date_format', orderable:false, searchable:true},
    { data: 'tc_inits.tc_samples.program', name: 'tc_inits.tc_samples.program', orderable:false, searchable:true},
    { data: 'tc_inits.tc_samples.sample_number_display', name: 'tc_inits.tc_samples.sample_number', orderable:false, searchable:true},
    { data: 'tc_workers.code', name: 'tc_workers.code', orderable:false, searchable:true},
    { data: 'total_obs', name: 'total_obs', orderable:false, searchable:false},
    { data: 'total_data', name: 'tree_date', orderable:true, searchable:false},
    { data: 'total_death', name: 'total_death', orderable:false, searchable:false},
    { data: 'normal', name: 'normal', orderable:false, searchable:false},
    { data: 'abnormal', name: 'abnormal', orderable:false, searchable:false},
    { data: 'panen', name: 'panen', orderable:false, searchable:false},
];
var death = jQuery.parseJSON( '{!! json_encode($data["death"]) !!}' );
$.each(death, function(index, value) {
    columns.splice(6+index,0,{ data: 'total_death_'+value.id, name: 'total_death_'+value.id, orderable:false, searchable:false});
});

var dtTable = $('#myTable').DataTable({
    processing: true,serverSide: true,scrollX: true,pageLength: 25,
    order: [[5, 'desc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: '{{ route("field-obs.dtShow") }}',
        data: function(d){ // note !
            d.initId = '{{ $data["initId"] }}';
            d.filter = $('select[name="filterActive"] option:selected').val();
        }
    },
    columns: columns,
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


var columns2 = [
    { data: 'tree_date_format', name: 'ob_date', orderable:true, searchable:false},
    { data: 'ob_date_action', name: 'ob_date_format', orderable:false, searchable:true},
    { data: 'total_death', name: 'total_death', orderable:false, searchable:false},
    { data: 'normal', name: 'normal', orderable:false, searchable:false},
    { data: 'abnormal', name: 'abnormal', orderable:false, searchable:false},
    { data: 'panen', name: 'panen', orderable:false, searchable:false},
];
var death2 = jQuery.parseJSON( '{!! json_encode($data["death"]) !!}' );
$.each(death2, function(index, value) {
    columns2.splice(2+index,0,{ data: 'total_death_'+value.id, name: 'total_death_'+value.id, orderable:false, searchable:false});
});
var dtTable2 = $('#myTable2').DataTable({
    processing: true,serverSide: true,scrollX: true,pageLength: 100,lengthChange:false,
    order: [[0,'desc']],
    columnDefs: [
        { className: 'text-center', targets: ['_all'] },
    ],
    ajax: {
        url: '{{ route("field-obs.dtShow2") }}',
        data: function(d){ // note !
            d.initId = '{{ $data["initId"] }}';
            d.filter = $('input[name="tab2Filter"]').val();
        }
    },
    columns: columns2,
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
        initDelEvent();
    },
    
});

function initDelEvent(){
        $('#delModal').on('show.bs.modal', function(e) {
            $(this).find('#attr').text($(e.relatedTarget).data('attr'));
            $(this).find('input[name="id"]').val($(e.relatedTarget).data('id'));
        });
    }
    $('#formDel').submit(function (e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST', cache: false, contentType: false, processData: false,
            url: '{{ url("field-obs") }}/'+formData.get('id'),
            data: formData,
            success: (a) => {
                dtTable2.ajax.reload();
                $('#delModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                if(a.status == 422){
                    clearValidationCreate();
                    $.each(a.responseJSON.errors, function(key, value){
                        cekValidationCreate(key, value);
                    })
                }else{
                    showAlert('danger', 'times', 'alert-area', a.status);
                    $('#deleteInitModal').modal('toggle');
                }
                loader(false);
            }
        });
    });


</script>


