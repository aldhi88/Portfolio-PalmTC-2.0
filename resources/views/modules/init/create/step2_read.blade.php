<div class="row align-items-center">
    <div class="col">
        <h5>
            <span class="badge badge-primary rounded-0">Step 2 (Worker)</span>
            <a href="javascript:void(0)" class="text-light" style="text-decoration: underline" id="edit-worker-btn">
                <span class="badge badge-danger rounded-0"><i class="feather icon-edit-2 mr-1"></i>Modify</span>
            </a>
        </h5>
    </div>
    <div class="col py-0 text-right"><h4><i class="fas fa-check-circle text-success"></i></h4></div>
    
</div>
<div class="row">
    <div class="col">
        <table class="table table-xs table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Worker</th>
                    <th>Laminar</th>
                    <th class="text-right">Block</th>
                    <th class="text-right">Bottle</th>
                    <th class="text-right">Explant</th>
                    {{-- <th>Edit</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $modulus = $data['session']['read']['modulus'];
                    $totalBlock = 0;
                    $totalBottle = 0;
                    $totalExplant = 0;
                @endphp
                @foreach ($data['session']['data'] as $item)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $item['worker_code'] }}</td>
                        <td>{{ $item['laminar_code'] }}</td>
                        <td class="text-right">
                            @php
                                $blockLoad = $data['session']['read']['blockPerWorker'];
                                if($modulus !=0){
                                    $blockLoad += 1;
                                    $modulus--;
                                }
                                $totalBlock = $totalBlock + $blockLoad;
                                echo $blockLoad;
                            @endphp
                        </td>
                        <td class="text-right">
                            @php
                                $bottleLoad = $blockLoad * $data['session']['read']['numOfBottle'];
                                $totalBottle = $totalBottle + $bottleLoad;
                                echo $bottleLoad;
                            @endphp
                        </td>
                        <td class="text-right">
                            @php
                                $explantLoad = $bottleLoad * $data['session']['read']['numOfExplant'];
                                $totalExplant = $totalExplant + $explantLoad;
                                echo $explantLoad;
                            @endphp
                        </td>
                        {{-- <td>
                            <button data-worker="{{ $item->tc_worker_id }}" class="badge badge-primary btn text-light rounded-0" data-toggle="modal" data-target="#changeBottleModal" >Change</button>
                        </td> --}}
                    </tr>
                @endforeach
                <tr class="bg-light">
                    <th colspan="3" class="text-right font-weight-bold py-1">Total:</th>
                    <th class="text-right py-1">{{ $totalBlock }}</th>
                    <th class="text-right py-1">{{ $totalBottle }}</th>
                    <th class="text-right py-1">{{ $totalExplant }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('modules.init.include.step2_read_js')

{{-- <script>
    $('body').on('click', '#edit-worker-btn', function(){
        genStep2('step2_edit');
    })

    $("#changeBottleModal").on("show.bs.modal", function(e) {
        var workerId = $(e.relatedTarget).data('worker');
        var initId = '{{ session("sess_init_id") }}';
        genModalContent(initId,workerId);
    });
    function genModalContent(initId,workerId){
        $.ajax({
            type: 'GET', cache: false, contentType: false, processData: true,
            url: '{{ url("worker-of-initiations/bottle-detail") }}',
            data: { 
                initId:initId,
                workerId:workerId 
            },
            success: function(a) {
                $('#changeBottleModal .modal-content').html(a);
            },
            error: (a) => {
                alert("Error #003, generate sample data in initiation form.");
            }
        });
    }
</script> --}}