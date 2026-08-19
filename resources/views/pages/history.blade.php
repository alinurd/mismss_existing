<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-4 mb-3">
                <h1 id="headerText"></h1>
            </div>
            <div class="col">
                <ul class="nav nav-pills float-right" id="navTab">
                    <li class="nav-item">
                        <a class="nav-link left-nav" id="main" data-toggle="tab" href="#main-tab" role="tab">Main Record</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link center-nav" id="jne" data-toggle="tab" href="#webhook-tab" role="tab">JNE Log</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link right-nav" id="sentral" data-toggle="tab" href="#webhook-tab" role="tab">SENTRAL Log</a>
                    </li>
                </ul>
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
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade" id="main-tab" role="tabpanel">
                                <table id="table-history" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th style="width:100px">User</th>
                                            <th>Description</th>
                                            <th>Whatsapp Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="webhook-tab" role="tabpanel">
                                <table id="table-webhook" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th style="width:100px">Created At</th>
                                            <th>Data</th>
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

<script src="{{url('assets/customs/js/historylist.js?v='.env('APP_VERSION'))}}"></script>