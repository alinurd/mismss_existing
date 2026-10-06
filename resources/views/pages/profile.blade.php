<section class="content mt-5">
    <div class="container-fluid">
        <div class="row row-middle font-18">
            <div class="col-12 col-sm-7 col-md-5 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">
                    <div class="card-header text-muted border-bottom-0">
                        {{$role_name}}
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>{{$username}}</b></h2>
                                <ul class="mt-3 ml-4 mb-0 fa-ul text-muted">
                                    <li class="small"><span class="fa-li"><i class="fas fa-user"></i></span> {{$fullname}}</li>
                                    <li class="small mt-1"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> {{$phone}}</li>
                                    <li class="small mt-1"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> {{$address}}</li>
                                    <li class="small mt-1"><span class="fa-li"><i class="fas fa-envelope"></i></span> {{$email}}</li>
                                    @if($referral!="")
                                        <input type="hidden" id="referral" value="https://old.mismasslogistic.com/form-order?ref={{$referral}}"/>
                                        <li class="small mt-1"><span class="fa-li"><i class="fas fa-link"></i></span> <a class='pointlink copyLinkRef' data-toggle="tooltip" title="Link Tercopy!">Copy Link Referral</a></li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img src="{{url('/assets/dist/img/default.jpg')}}" alt="user-avatar" class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col text-right">
                                <button class=" btn btn-danger" data-toggle="modal" data-target="#modalGantiPass"><i class="fas fa-unlock-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" data-backdrop="static" id="modalGantiPass" tabindex="-1" role="dialog" aria-labelledby="modalGantiPassTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="formGantiPass">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Ganti Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passlama">Password Lama</label>
                                        <input type="text" class="form-control" name="passlama" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passbaru">Password Baru</label>
                                        <input type="password" class="form-control" name="passbaru" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="passlama">Password Baru Lagi</label>
                                        <input type="password" class="form-control" name="passbarulagi" placeholder="Password Lama">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="btnToTop"><i class='fas fa-arrow-up'></i></div>

<script src="{{url('assets/customs/js/profile.js?v='.env('APP_VERSION'))}}"></script>
