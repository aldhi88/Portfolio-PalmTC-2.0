<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        @include('modules.medium_stock.delete')
    </div>
</div>
<div id="historyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <span id="historyData"></span>
    </div>
</div>

<div id="stockValidateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        @include('modules.medium_opname.create')
    </div>
</div>