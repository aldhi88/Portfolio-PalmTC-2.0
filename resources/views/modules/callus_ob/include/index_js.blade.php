<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/libs/moment.js/moment.min.js')}}"></script>
<script>
    // datatables
    const routes = {
        callusObsShow: (id) => `{{ url('callus-obs') }}/${id}`,
        callusObsCreate: (id) => `{{ url('callus-obs/create') }}/${id}`,
        callusObsComment: (id) => `{{ url('callus-obs/comment') }}/${id}`,
    };
    var dtTable = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [ [0,'desc'] ],
        columnDefs: [
            { className: 'text-center text-nowrap', targets: [0] },
            { className: 'text-center', targets: ['_all'] },
        ],
        ajax: '{{ route("callus-obs.dt") }}',
        // columns: [
        //     // { data: 'reminder', name: 'reminder', orderable:false, searchable:false},
        //     { data: 'sample_action', name: 'tc_samples.sample_number', orderable:true, searchable:true},
        //     { data: 'init_date', name: 'created_at', orderable:false, searchable:false},
        //     { data: 'tc_callus_obs_count', name: 'tc_callus_obs_count', orderable:false, searchable:true},
        //     { data: 'bottle_callus', name: 'bottle_callus', orderable:false, searchable:true},
        //     { data: 'explant_callus', name: 'explant_callus', orderable:false, searchable:true},
        //     { data: 'persen_bottle_callus', name: 'persen_bottle_callus', orderable:false, searchable:true},
        //     { data: 'persen_explant_callus', name: 'persen_explant_callus', orderable:false, searchable:true},
        //     { data: 'bottle_oxi', name: 'bottle_oxi', orderable:false, searchable:true},
        //     { data: 'explant_oxi', name: 'explant_oxi', orderable:false, searchable:true},
        //     { data: 'bottle_contam', name: 'bottle_contam', orderable:false, searchable:true},
        //     { data: 'explant_contam', name: 'explant_contam', orderable:false, searchable:true},
        // ],

        columns: [
            { data: null, name: 'tc_samples.sample_number',
                render: function(data) {
                    let latestObs = data.tc_callus_obs.length ? data.tc_callus_obs[data.tc_callus_obs.length - 1] : null;
                    let html = `<p class="mb-0"><strong>${data.tc_samples.sample_number_display}</strong></p>`;
                    html += `<p class='mb-0'>
                                <a class='text-primary' href='${routes.callusObsShow(data.id)}'>View</a>`;

                    if (latestObs) {
                        html += ` - <a class='text-link' href='${routes.callusObsCreate(data.id)}'>Observation</a>`;
                    }

                    html += ` - <a class='text-primary' href='${routes.callusObsComment(data.id)}'>Comment</a></p>`;
                    return html;
                }
            },
            { data: 'created_at', name: 'created_at',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            { data: null, name: 'tc_callus_obs_count',
                render: function(data) {
                    return data.tc_callus_obs_count || 0;
                }
            },
            { data: 'bottle_callus',
                render: d => d ?? 0
            },
            { data: 'explant_callus',
                render: d => d ?? 0
            },
            {data: null,
                render: function(data) {
                    const bottleCallus = data.bottle_callus ?? 0;
                    const totalBottle = data.total_bottle ?? 1; // hindari pembagi nol
                    const percentage = (bottleCallus / totalBottle) * 100;
                    return percentage.toFixed(2).replace('.', ',');
                }
            },

            { data: null,
                render: function(data) {
                    const totalBottle = data.total_bottle ?? 1;
                    const totalExplant = totalBottle * data.number_of_plant;
                    const percentage = data.explant_callus ? (data.explant_callus / totalExplant) * 100 : 0;
                    return percentage.toFixed(2).replace('.', ',');
                }
            },
            { data: 'bottle_oxi',
                render: d => d ?? 0
            },
            { data: null,
                render: function(data) {
                    const bottleOxi = parseFloat(data.bottle_oxi ?? 0);
                    const plant = parseFloat(data.number_of_plant ?? 0);
                    const result = bottleOxi * plant;
                    return result;
                }
            },
            { data: 'bottle_contam',
                render: d => d ?? 0
            },
            { data: 'explant_contam',
                render: d => d ?? 0
            }
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
            }
    });

    $("body").on("click", "button#btnPrint", function(){
        var initId = 'blank';
        var page = $('input[name="page"]').val();
        var div = document.getElementById("exportPrint");
        var route = '{{ route("callus-obs.printObsForm") }}?initId='+initId+'&page='+page;
        div.innerHTML = '<iframe id="exportPrint" src="'+route+'" onload="print(this);"></iframe>';
    })

    function print(a){
        a.contentWindow.print();
        loader(false);
    }

    $('#formImportModal').submit(function(e) {
        loader(true);
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('import.callusImport') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (a) => {
                $('#formImportModal').trigger('reset')
                if (a.status == 'success') {
                    dtTable.ajax.reload();
                }
                $('#importModal').modal('toggle');
                showAlert(a.data.type, a.data.icon, a.data.el, a.data.msg);
                loader(false);
            },
            error: (a) => {
                showAlert('danger', 'times', 'alert-area', a.status);
                loader(false);
            }
        });
    });
</script>
