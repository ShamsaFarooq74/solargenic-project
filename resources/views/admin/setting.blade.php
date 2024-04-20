@extends('layouts.admin.master')
@section('title', 'All Settings')
@section('content')
<div class="container-fluid px-xl-5">
    <section class="py-2">
        <div class="row">
            <div class="col-12 mb-1">
                <div class="report-head-vt">
                    <h4>All Setting</h4>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    @include('alert')
                    <form class="parsley-examples" method="post" action="{{ url('admin/update-setting') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Email</label>
                                    <input type="email" class="form-control" name="smtp_email" placeholder="SMTP Email" value="{{ $setting['smtp_email'] ? $setting['smtp_email'] : '' }}" required/>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Password</label>
                                    <input type="password" class="form-control" name="smtp_password" placeholder="SMTP Password" value="{{ $setting['smtp_password'] ? $setting['smtp_password'] : '' }}" required/>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
                            <input type="hidden" name="id" value="{{ $setting['id'] ? $setting['id'] : '' }}">
                            
                        </div>
                        <div class="form-group mb-0">
                            <div>
                                <button type="submit" class="btn-create-vt">
                                    Update Setting
                                </button>
                                <button type="reset" class="btn-close-vt">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection