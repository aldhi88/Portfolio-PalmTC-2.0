<style>
    .dataTables_filter { 
        display: none; 
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="sampleModalLabel"><i class="feather icon-check-square"></i> Pick Medium Stocks</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <span id="alert-area-addStock"></span>
        <div class="row">
            <div class="col">
                <table id="myTableMediumStock" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Medium</th>
                            <th>Bottle</th>
                            <th>Agar</th>
                            <th class="text-center">Stock</th>
                            <th width="100"></th>
                        </tr>
                    </thead>
                    <thead id="myTableMediumStockFilter">
                        <tr>
                            <th disable="true"></th>
                            <th>....</th>
                            <th>....</th>
                            <th>....</th>
                            <th disable="true"></th>
                            <th disable="true" width="100"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col" style="padding-top: 14px">
                <h6>Used Stock List</h6>
                <table id="mediumStockPicked" class="table table-xs table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Medium</th>
                            <th>Bottle</th>
                            <th>Agar</th>
                            <th class="text-center">Used</th>
                            <th width="100"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-primary" data-dismiss="modal">Save & Finish</button>
    </div>
</div>

<script>
    
</script>