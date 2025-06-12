<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
    // datatables
var columns = [
    { data: 'sample_number_format', name: 'tc_samples.sample_number', orderable:true, searchable:true},
    { data: 'tc_samples.program', name: 'tc_samples.program', orderable:false, searchable:true},
    { data: 'nur_count', name: 'nur_count', orderable:false, searchable:true},
    { data: 'obs_count', name: 'obs_count', orderable:false, searchable:true},
    { data: 'total_data', name: 'total_data', orderable:false, searchable:false},
    { data: 'total_death', name: 'total_death', orderable:false, searchable:false},
    { data: 'total_transfer', name: 'total_transfer', orderable:false, searchable:false},
    { data: 'nur_active_nursery', name: 'nur_active_nursery', orderable:false, searchable:true},
    { data: 'nur_active_estate', name: 'nur_active_estate', orderable:false, searchable:true},
];
var death = jQuery.parseJSON( '{!! json_encode($data["death"]) !!}' );
$.each(death, function(index, value) {
    columns.splice(5+index,0,{ data: 'total_death_'+value.id, name: 'total_death_'+value.id, orderable:false, searchable:false});
});
var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [ [0,'desc'] ],
        columnDefs: [
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("nur-obs.dt") }}',
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
            }
    });

    $("body").on("click", "button#btnPrint", function(){
        var div = document.getElementById("exportPrint");
        var route = '{{ route("matur-obs.printObsForm") }}';
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }
</script>