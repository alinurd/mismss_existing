<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 mb-3 m-100">
                <h1>TnC Blast</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="bold-text" style="font-weight:700">Versi TnC Aktif</div>
                        <div style="display:flex;gap:5px;margin-top:10px">
                            <input type="text" class="form-control" id="tncVersionInput" value="{{$currentVersion}}" {{Auth::user()->tncblast_send ? '' : 'readonly'}}>
                            @if(Auth::user()->tncblast_send)
                            <button type="button" class="btn btn-primary" id="saveVersionBtn">Simpan</button>
                            @endif
                        </div>
                        <small class="text-muted">Ubah versi ini setiap kali dokumen T&amp;C diperbarui. Customer yang belum menerima versi ini akan muncul di daftar pending.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="bold-text" style="font-weight:700">Belum Menerima Versi Terbaru</div>
                        <div style="font-size:28px;font-weight:700;color:red" id="pendingCountLabel">{{$pendingCount}}</div>
                        <small class="text-muted">dari total {{$totalCustomer}} customer</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="bold-text" style="font-weight:700">Kirim Blast</div>
                        @if(Auth::user()->tncblast_send)
                        <div style="display:flex;gap:5px;margin-top:10px;flex-wrap:wrap">
                            <button type="button" class="btn bg-gradient-success" id="sendSelectedBtn"><i class="fas fa-paper-plane"></i> Kirim ke Terpilih</button>
                            <button type="button" class="btn bg-gradient-warning" id="sendAllBtn"><i class="fas fa-broadcast-tower"></i> Kirim ke Semua Pending</button>
                        </div>
                        <div class="progress mt-2" id="sendProgressWrap" style="display:none;height:20px">
                            <div class="progress-bar bg-success" id="sendProgressBar" role="progressbar" style="width:0%">0%</div>
                        </div>
                        <div id="sendProgressLabel" class="text-muted mt-1" style="display:none"></div>
                        @else
                        <div class="text-muted">Anda tidak memiliki akses untuk mengirim blast.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped" id="tableTncBlast">
                            <thead>
                                <tr>
                                    <th style="width:10px"><input type="checkbox" id="tncCheckAll"></th>
                                    <th style="width:10px">#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status Kirim Terakhir</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="{{url('assets/customs/js/tncblast.js?v='.env('APP_VERSION'))}}"></script>
