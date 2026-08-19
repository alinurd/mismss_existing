<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <h1>Void Invoice</h1>
            </div>
            <div class="col">
                <ul class="nav nav-pills float-right" id="navChooseCust" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link left-nav active pointlink" id="IND" data-toggle="tab" role="tab">Individual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav pointlink" id="COR" data-toggle="tab" role="tab">Corporate</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col m-100">
                <h5 id="totalAllVoid">Total Void : <b>Rp.0</b></h5>
            </div>
            <div class="col d-flex flex-wrap gap-1 w-filter" style="padding-left:2px;padding-right:2px">
                <select name="filterPayment" id="filterPayment" style="flex:1;" class="form-control">
                    <option value="">All Payment</option>
                    <option value="DOKU">DOKU</option>
                    <option value="BANK">BANK</option>
                </select>
                <input type="text" class="form-control w-auto" style="flex:1;" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal Void" autocomplete="off" readonly>
                <a id="view" class="filter btn-not-end btn btn-success"><i class="fas fa-filter"></i> Filter</a>
                <a id="reset" class="resetFilter btn btn-danger"><i class="fas fa-sync-alt"></i> Reset</a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" role="tabpanel">
                                <table id="table-void" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Resi Tracking</th>
                                            <th>No. Invoice</th>
                                            <th>User Create</th>
                                            <th>User Void</th>
                                            <th>Detail Customer</th>
                                            <th>Payment</th>
                                            <th>Catatan</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/void.js?v='.env('APP_VERSION'))}}"></script>