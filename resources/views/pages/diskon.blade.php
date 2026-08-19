<style>
    .services {
        padding: 10px;
        border: 1px solid black;
    }
    .orderNum{
        margin-left:7px;
    }
    .detail-control{
        width:20px;
        height:20px;
        cursor: pointer;
    }
    .hidden-child{
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
    }
    .shown-child{
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3">
                <h1>Total Diskon : <div class="font-weight-bold" style='display:inline'>{{App\Http\Controllers\Controller::rupiah($totaldiskon)}}</div></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <ul class="nav nav-pills" id="navChooseCust" style="width: fit-content;border: 1px solid #ffc107;border-radius:5px">
                    <li class="nav-item">
                        <a class="nav-link active" id="I" data-toggle="tab" href="#nav-individual" role="tab">Individual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="C" data-toggle="tab" href="#nav-corporate" role="tab">Corporate</a>
                    </li>
                </ul>
            </div>
            <div class="col-7" style="height:100%;vertical-align:middle">
                <div class="row justify-content-end">
                        <div class="col-3" style="padding-left:2px;padding-right:2px">
                            <select name="customerId" id="customerId" class="form-control" style="width:100%">
                                <option value="">ALL CUSTOMER</option>
                                @foreach($cust as $c)
                                    @if($c->totalDiskon>0)
                                        <option value="{{$c->id}}">{{$c->first_name}} {{$c->middle_name}} {{$c->last_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3" style="padding-left:2px;padding-right:2px">
                            <input type="text" class="form-control" name="filterTanggal" id="filterTanggal" placeholder="Filter Tanggal" autocomplete="off" readonly>
                        </div>
                        <div class="col fit-btn" style="padding-left:2px;padding-right:2px">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle btn-view"
                                        type="button" id="dropdownMenu1" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a id="view" class="dropdown-item" href="#!"><i class="fas fa-search"></i> Cari</a>
                                    <a id="reset" class="dropdown-item" href="#!"><i class="fas fa-sync-alt"> Reset</i></a>
                                    <!-- <a class="dropdown-item" href="#!">Action</a>
                                    <a class="dropdown-item" href="#!">Another action</a> -->
                                </div>
                            </div>
                            <!-- <button type="button" id="viewTwo" class="btn btn-view" style="width:100%">View</button> -->
                        </div>
                </div>
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
                            <div class="tab-pane fade show active" id="nav-individual" role="tabpanel">
                                <table class="table table-striped" id="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>No. Invoice</th>
                                            <!-- <th>No. Order (Doku)</th> -->
                                            <th id="thResi">No. Resi</th>
                                            <th>User</th>
                                            <th>Customer</th>
                                            <th>Kontak & Alamat</th>
                                            <th>Jumlah</th>
                                            <th>Rincian Biaya</th>
                                            <th>Action</th>
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
<script>
    auth = "{{Auth::user()->shiplist_printout_resi}}";
</script>
<script src="{{url('assets/customs/js/filtertanggal.js?v='.env('APP_VERSION'))}}"></script>
<script src="{{url('assets/customs/js/diskonlist.js?v='.env('APP_VERSION'))}}"></script>