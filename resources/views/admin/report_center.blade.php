@extends('layouts.admin.master')
@section('title', 'Report Center')
@section('content')
<div class="container-fluid px-xl-3">
    <section class="py-2">
        <div class="row">
            <div class="col-12">
                <div class="report-head-vt">
                    <h4>Report</h4>
                    <button type="button" class="btn-report-vt" data-toggle="modal" data-target="#exampleModal">
                        Create Report
                    </button>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="inline-editable">
                                <thead>
                                    <tr>
                                        <th>Report Name</th>
                                        <th>Report Type</th>
                                        <th>Country/Region</th>
                                        <th>Province</th>
                                        <th>City</th>
                                        <th>Report Generation Time</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td>Analysis</td>
                                        <td>Consumption</td>
                                        <td>Pakistan</td>
                                        <td>Punjab</td>
                                        <td>Lahore</td>
                                        <td>12:35 PM, 14-5-2020</td>
                                        <td><button><i class="fas fa-download"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- end .table-responsive-->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </section>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Report</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Report Name*</label>
                        <input type="email" placeholder="Report Name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Report Type*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Report Type</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Plant Region*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Plant Region</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Province*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Province</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">City*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>City</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Plant*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Plant*</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Display info*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>Display info*</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">End Time*</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option>End Time</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-create-vt">Create</button>
                    <button type="button" class="btn-close-vt" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection