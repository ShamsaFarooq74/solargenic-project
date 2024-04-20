@extends('layouts.admin.master')

@section('title', 'Inverter Details')

@section('content')

<?php

$ac_output_total_power = 0;

?>
<div class="bred_area_vt">
	<div class="row">
		<div class="col-12">
			<div class="page-title-box">
				<div class="page-title">
					<ol class="breadcrumb m-0 p-0">
						<li class="breadcrumb-item"><a href="{{ url('admin/Plants') }}">Plants</a></li>
						<li class="breadcrumb-item"><a href="{{ url('admin/user-plant-detail/'.$plant->id) }}">{{$plant->plant_name}}<i class="check_pla_vt fas fa-check-circle"></i></a></li>
						<li class="breadcrumb-item active">Inverters</li>
					</ol>
				</div>
			</div>
			<div class="btn-companies-vt">
				<a href="}">
					<button name="refresh" type="button" class="btn-clear-ref-vt">
						<img src="{{ asset('assets/images/refresh.png')}}" alt="refresh">
					</button>
				</a>
				<p>Updated at {{date('H:i A, d-m-Y',strtotime($plant->updated_at))}}</p>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid px-xl-5">
	<section class="py-2">

		<!-- <div class="row">

			<div class="col-12">

				<div class="report-head-vt">

					<h4>Inverter Detail</h4>

					<div class="btn-companies-vt">

						<a href="{{ url('admin/plant-detail/'.$plant->id.'?page=refresh')}}">

							<button name="refresh" type="button" class="btn-clear-ref-vt">

								Refresh

							</button>

						</a>

					</div>

				</div>

			</div>

		</div> -->
		<div class="row">
			<div class="col-lg-12 tabel_inv_vt">
				<div class="card-box">
					<h4 class="header_title_vt">All Inverters</h4>
					<div class="table-responsive">
						<table class="table table-borderless mb-0">
							<thead class="thead-light">
								<tr>
									<th>AC Output Total Power</th>
									<th>Daily Generation (Active)</th>
									<th>Monthly Generation</th>
									<th>Annual Generation</th>
									<th>Total Generation</th>
								</tr>
							</thead>
							<tbody>
								@if(count($plant) > 0)
								@foreach($plant->inverters as $key => $inverter)

								@php $ac_output_total_power = (double)$plant->inverters[$key]->ac_output_power + $ac_output_total_power; @endphp

								@endforeach
								@endif
								<tr>
									<th scope="row">{{$ac_output_total_power}}W</th>
									<td>{{round($total_daily_generation, 2)}}kWh</td>
									<td>{{round($total_monthly_generation, 2)}}kWh</td>
									<td>{{round($total_yearly_generation, 2)}}kWh</td>
									<td>{{round($total_generation_sum, 2)}}kWh</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="table-responsive">
						<table class="table table-borderless mb-0">
							<thead class="thead-light">
								<tr>
									<th>Serial Number </th>
									<th>Total DC Input Power</th>
									<th>Generation Yesterday</th>
									<th>Generation of Last Month</th>
									<th>Generation of Last Year</th>
								</tr>
							</thead>
							<tbody>
								@if(count($inverter_previous_data) > 0)
								@foreach($inverter_previous_data as $key => $inverter)

								<tr>
									<th scope="row">{{$inverter->dv_inverter_serial_no}}</th>
									<td>{{$inverter->dc_power}}W</td>
									<td>{{$inverter->daily_generation}}kWh</td>
									<td>{{$inverter->monthly_generation}}kWh</td>
									<td class="on_off_vt">{{$inverter->yearly_generation}}kWh <div class="btn_on_vt"><i class="fas fa-power-off"></i></div>
									</td>
									{{-- <div class="btn_off_vt"><i class="fas fa-power-off"></i></div><div class="btn_fault_vt"><i class="fas fa-power-off"></i></div> --}}
								</tr>

								@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div> <!-- end card-box -->
			</div> <!-- end col -->
		</div>

	</section>

	@if(count($plant->inverters) > 0)

	<!-- <section>

		<div class="row">

			<div class="col-lg-4 mb-4">

				<div class="plant-profile-detail-vt">

					<div class="row">

						<div class="col-md-12">

							<div class="row">

								<div class="col-md-6 alerts-head-text-vt">

									<p>Last Updated</p>

								</div>

								<div class="col-md-6 alerts-detail-text-vt">

									<p>{{ date('d-m-Y H:i',strtotime($plant->inverters[0]->lastUpdated)) }}</p>

								</div>

								<hr>

								<div class="col-md-6 alerts-head-text-vt">

									<p>Plant</p>

								</div>



								<div class="col-md-6 alerts-detail-text-vt">

									<p>{{ $plant->plant_name }}</p>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

			<div class="col-lg-8 mb-4">

				<div class="power-detail-area-vt">

					<table class="table card-text">

						<thead>

							<tr class="ac-power-detail-vt">

								<th>Ac Output Total Power</th>

								<th>Daily Generation (Active)</th>

								<th>Monthly Generation</th>

								<th>Annual Generation</th>

								<th>Total Generation</th>

							</tr>

						</thead>

						<tbody>

							<tr class="text-power-detail-vt">

								<td>{{ $plant->inverters ? $plant->inverters[0]->ac_output_power : 0 }} kW</td>

								<td>

									<?php $daily = $plant->daily_inverter_detail->where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 00:01:00'), date('Y-m-d 23:59:00')]); ?>

									{{ isset($daily) && !empty($daily) && count($daily) > 0 ? $daily->first()->daily_generation : 0 }} kWh

								</td>

								<td>

									<?php $monthly = $plant->monthly_inverter_detail->where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-01 00:01:00'), date('Y-m-d 23:59:00')]); ?>

									{{ isset($monthly) && !empty($monthly) && count($monthly) > 0 ? $monthly->first()->monthly_generation : 0 }} kWh

								</td>

								<td>

									<?php

									$yearly = $plant->monthly_inverter_detail->where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-01-01 00:00:00'), date('Y-m-d 23:59:00')])->sum('monthly_generation'); ?>

									{{ isset($yearly) && !empty($yearly) > 0 ? $yearly : 0 }} kWh

								</td>

								<td>{{ $plant->inverters ? $plant->inverters[0]->total_generation : 0 }} kWh</td>

							</tr>

						</tbody>

					</table>

				</div>

			</div>

		</div>

	</section> -->

	<?php

	// $inverters_array = $plant->inverters->where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')]);

	// dd($inverters_array);

	$inverters_array = $plant->inverters;

	?>

	<section>

		@if(count($inverters_array) > 0)

		<div class="row">

			<div class="col-lg-12">

				<div class="ibox-content power-daciec-area-vt">

					<div id="carouselExampleControls" class="carousel slide" data-interval="false">

						<ol class="carousel-indicators">

							@if(count($inverters_array) > 0)

							@foreach($inverters_array as $key => $inverter)

							<li data-target="#carouselExampleControls" data-slide-to="{{ $inverter->serial_no }}" class="{{ $key == 0 ? 'active' : '' }}"></li>

							@endforeach

							@endif

						</ol>
						<a class="left carousel-control" href="#carouselExampleControls" data-slide="prev">
							<img src="{{ asset('assets/images/left.svg') }}" alt="">
							<span class="sr-only">Previous</span>
						</a>
						<a class="right carousel-control" href="#carouselExampleControls" data-slide="next">
							<img src="{{ asset('assets/images/right.svg') }}" alt="">
							<span class="sr-only">Next</span>
						</a>
						<div class="carousel-inner">

							@if(count($inverters_array) > 0)

							@foreach($inverters_array as $key => $inverter)

							<div class="carousel-item {{ $key == 0 ? 'active' : '' }}">


								<div class="row border_one_vt">
									<div class="col-lg-12">
										<h4 class="header_title_vt"> Inverter Details ({{$inverter->serial_no}})</h4>
									</div>
									<div class="col-lg-4 for_table_vt">

										<table id="demo-foo-row-toggler" class="table table-bordered toggle-circle mb-0">
											<thead class="thead-light">
												<tr>
													<th data-toggle="true"> DC </th>
													<th> Voltage </th>
													<th> Current </th>
													<th> Power </th>
													<th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String1 </th>
													<th data-hide="all" style="width: 80px; text-align: left; padding-left: 16px;"> String2 </th>
												</tr>
											</thead>
											<tbody>
												<tr class="add_mar_vt">
													<td>PV1</td>
													<td>610.5V</td>
													<td>10.6A</td>
													<td>6471.3W</td>
													<td><span>value </span> <span>value </span> <span>value </span></td>
													<td> <span>value </span> <span>value </span> <span>value </span> </td>
												</tr>
												<tr class="add_mar_vt">
													<td>PV2</td>
													<td>610.5V</td>
													<td>10.6A</td>
													<td>6471.3W</td>
													<td><span>value </span> <span>value </span> <span>value </span></td>
													<td> <span>value </span> <span>value </span> <span>value </span> </td>
												</tr>
												<tr class="add_mar_vt">
													<td>PV3</td>
													<td>610.5V</td>
													<td>10.6A</td>
													<td>6471.3W</td>
													<td><span>value </span> <span>value </span> <span>value </span></td>
													<td> <span>value </span> <span>value </span> <span>value </span> </td>
												</tr>
											</tbody>
										</table>

										<!-- <table class="table card-text">

											<thead class="thead-light">

												<tr>

													<th>DC</th>

													<th>Voltage</th>

													<th>Current</th>

													<th>Power</th>

												</tr>

											</thead>

											<tbody>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV1
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>{{ $inverter->l_voltage1 }}V</td>

													<td>{{ $inverter->l_current1 }}A</td>

													<td>{{ $inverter->l_voltage1 * $inverter->l_current1 }}W</td>

												</tr>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV2
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>{{ $inverter->l_voltage2 }}V</td>

													<td>{{ $inverter->l_current2 }}A</td>

													<td>{{ $inverter->l_voltage2 * $inverter->l_current2 }}W</td>

												</tr>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV3
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>{{ $inverter->l_voltage3 }}V</td>

													<td>{{ $inverter->l_current3 }}A</td>

													<td>{{ $inverter->l_voltage3 * $inverter->l_current3 }}W</td>

												</tr>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV4
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>---</td>

													<td>---</td>

													<td>---</td>

												</tr>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV5
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>---</td>

													<td>---</td>

													<td>---</td>

												</tr>

												<tr>

													<td>
														<div class="dropdown">
															<button class="btn_string_vt dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<img src="{{ asset('assets/images/svg_1.svg') }}" alt=""> PV6
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<a class="dropdown-item" href="#">String 1</a>
																<a class="dropdown-item" href="#">String 2</a>
															</div>
														</div>
													</td>

													<td>---</td>

													<td>---</td>

													<td>---</td>

												</tr>

											</tbody>

										</table> -->

									</div>

									<div class="col-lg-4  text-center">

										<table class="table card-text">
											<div class="img_center_vt"></div>

											<img src="{{ asset('assets/images/davice.jpg')}}" alt="" class="pt-3">

										</table>

									</div>

									<div class="col-lg-4">

										<table class="table card-text">

											<thead class="thead-light">

												<tr>

													<th>Phase</th>

													<th>Voltage</th>

													<th>Current</th>

													<th>Frequency</th>

												</tr>

											</thead>

											<tbody>

												<tr class="vt_this">

													<td>R</td>

													<td>{{ $inverter->r_voltage1 }} V</td>

													<td>{{ $inverter->r_current1 }} A</td>

													<td>{{ $inverter->frequency }} Hz</td>

												</tr>

												<tr class="">

													<td>S</td>

													<td>{{ $inverter->r_voltage2 }} V</td>

													<td>{{ $inverter->r_current2 }} A</td>

													<td>{{ $inverter->frequency }} Hz</td>

												</tr>

												<tr class="">

													<td>T</td>

													<td>{{ $inverter->r_voltage3 }} V</td>

													<td>{{ $inverter->r_current3 }} A</td>

													<td>{{ $inverter->frequency }} Hz</td>

												</tr>

											</tbody>

										</table>

									</div>

								</div>

								<div class="row">
									<div class="col-lg-1"></div>
									<div class="col-lg-10 table-details-vt mt-3">
										<div class="card">
											<div class="card-header">
												<h2 class="All-graph-heading-vt">History</h2>
											</div>
											<div class="day_month_year_vt" id="day_month_year_vt">
												<button><i class="fa fa-caret-left"></i></button>
												<input type="text" id="humanfd-datepicker" class="form-control" placeholder="October 9, 2018">
												<button><i class="fa fa-caret-right"></i></button>
											</div>
											<div class="day_my_btn_vt">
												<button class="day_bt_vt">Day</button>
												<button class="month_bt_vt" id="month_bt_vt">Month</button>
												<button class="month_bt_vt" id="year_bt_vt">Year</button>
											</div>
											<div class="card-box">
												<div class="energy_gener_vt">
													<div class="ch_one_vt" id="graphDiv">
														{{-- <div class="kWh_eng_vt">kWh</div> --}}
														<div class="ch_tr_vt"><span>00:00</span></div>
													</div>
												</div>
											</div>
											<div class="generation-overview-vt" id="generationID">
											</div>
										</div>
									</div>

								</div>
								<div class="col-lg-1"></div>

							</div>

							@endforeach

							@else

							<div>Generation not found.</div>

							@endif

						</div>
					</div>

				</div>

			</div>

		</div>

		@endif

	</section>

	@else

	<section>

		<div class="row">

			<div class="col-lg-12 mb-4">

				<p>Inverter Record not found at this moment.</p>

			</div>

		</div>

	</section>

	@endif

</div>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script type="text/javascript">
	window.onload = function() {

		var serial_no = $('.carousel-indicators li').data('slide-to');

		var totalItems = $('.carousel-item').length;
		var currentIndex = $('div.active').index();
		console.log(serial_no);

		$.ajax({
			type: 'get',
			url: "{{ URL('admin/plant-inverter-graphs') }}" + "/" + serial_no,
			success: function(res) {
				$('div#graphDiv').empty();
				$('div#generationID').empty();
				$('div#generationID').append('<p><samp></samp> Daily Generation: <br><span>' + res["today_energy_generation"] + ' kWh</span></p>');
				$('div#graphDiv').append('<div class="ch_tr_vt"><span>00:00</span></div><div id="' + serial_no + '" style="height: 200px; width: 100%;" data-today_log=' + res["today_generation"] + ' data-today_log_time=' + res["today_time"] + '></div>');
				graph(serial_no);
			},
			error: function(res) {
				console.log('Failed');
				console.log(res);
			}
		});

		$('.carousel-indicators li').click(function(e) {
			var serial_no = $(this).data('slide-to');

			var totalItems = $('.carousel-item').length;
			var currentIndex = $('div.active').index();
			console.log(serial_no);

			$.ajax({
				type: 'get',
				url: "{{ URL('admin/plant-inverter-graphs') }}" + "/" + serial_no,
				success: function(res) {
					$('div#graphDiv').empty();
					$('div#generationID').empty();
					$('div#generationID').append('<p><samp></samp> Daily Generation: <br><span>' + res["today_energy_generation"] + ' kWh</span></p>');
					$('div#graphDiv').append('<div class="ch_tr_vt"><span>00:00</span></div><div id="' + serial_no + '" style="height: 200px; width: 100%;" data-today_log=' + res["today_generation"] + ' data-today_log_time=' + res["today_time"] + '></div>');
					$('canvas').remove();
					$('#' + serial_no).append('<canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; user-select: none;"></canvas><canvas class="canvasjs-chart-canvas" width="850" height="200" style="position: absolute; -webkit-tap-highlight-color: transparent; user-select: none; cursor: default;"></canvas>');
					graph(serial_no);
				},
				error: function(res) {
					console.log('Failed');
					console.log(res);
				}
			});
		});

	}

	function graph(serial_no) {
		console.log("Hello" + serial_no);
		var today_log = $('#' + serial_no).attr('data-today_log').split(',');
		var today_time = $('#' + serial_no).attr('data-today_log_time').split(',');
		var today = [];

		for (var i = 0; i < today_log.length; i++) {
			today[i] = {
				label: today_time[i],
				y: parseFloat(today_log[i])
			};
		}

		var date = '<?php echo date('d-m-Y') ?>';

		var options = {
			exportEnabled: true,
			animationEnabled: true,
			axisX: {
				interval: 8,
			},
			axisY: {
				gridThickness: 1,
				gridColor: "#DCDCDC"
			},
			data: [{
				toolTipContent: date + " {label}<br/> Today Generation: {y} kWh",
				markerType: "none",
				type: "line",
				dataPoints: today,
			}]
		};

		$('#' + serial_no).CanvasJSChart(options);

	}
</script>

<script>
	$(document).ready(function() {
		$('.add_mar_vt').on('click',function() {
			$('.vt_this').toggleClass("highlight");
			// console.log('hello');
		});
	});
</script>
<script src="{{ asset('assets/js/jquery-3.5.1.min.js')}}" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
@endsection