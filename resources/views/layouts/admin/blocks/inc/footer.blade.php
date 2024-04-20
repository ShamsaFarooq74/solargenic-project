</div>
              <!-- Footer Start -->
              <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                               <p> &copy; 2021 SolarGenic. All Rights Reserved. </p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right footer-links d-none d-sm-block">
                                  <p>info@solargenic.net</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>

                <!-- ============================================================== -->

                <!-- End Page content -->

                <!-- ============================================================== -->

                </div>

                <!-- END wrapper -->



                <!-- Vendor js -->

                <script src="{{ asset('assets/js/vendor.min.js')}}"></script>

                <script src="{{ asset('assets/js/select2/select2.full.min.js')}}"></script>
                <script>
                    $(document).ready(function () {
                        $(".select2_demo_2").select2({
                            theme: 'bootstrap4',
                            placeholder: 'Select',
                            color: '#d9d6d6 !important',
                        });
                    });
                </script>

                <!-- ======================================= -->

                <!--  Users Script  -->

                <!-- ======================================= -->

                <script type="text/javascript">
                var auth_id = {!! Auth::user()->roles !!};
                  $(document).ready(function() {

                    $('#datatable_1').dataTable({
                      "pageLength": 5,
                      "bLengthChange": false,
                      "ordering": false,
                      "info": false,
                      "bFilter": false
                    });

                    $('#datatable_2').DataTable({
                      "pageLength": 15,
                      "bLengthChange": true,
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0, 1, 2, 3, 7, 10]
                        },
                      ],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;
                        this.api().columns([2]).every(function() {
                          var title = this.header();

                          title = $(title).html().replace(/[\W]/g, '-');
                          var column = this;
                          var select = $('<select id="' + title + '" class="select2" ></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {

                              var data = $.map($(this).select2('data'), function(value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                              });

                              if (data.length === 0) {
                                data = [""];
                              }

                              var val = data.join('|');

                              column.search(val ? val : '', true, false).draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });

                          $('#' + title).select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: title,
                          });

                          $('.select2').val(null).trigger('change');
                        });

                        this.api().columns([10]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_2 th").eq([d]).text();
                          var select = $('<select><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });

                    if(auth_id == 5 || auth_id == 6) {

                        $('#datatable_plant').DataTable({
                      "orderCellsTop": true,
                      "pageLength": 10,
                      "bFilter":false,
                      "bLengthChange": true,
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0, 1, 3, 4, 5]
                        },
                        { "orderSequence": [ "asc" ], "targets": [ 2 ] },
                      ],
                      order: [[2, 'asc']],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;
                        this.api().columns([2]).every(function() {
                          var title = this.header();

                          title = $(title).html().replace(/[\W]/g, '-');
                          var column = this;
                          var select = $('<select id="' + title + '" class="select2" ></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {

                              var data = $.map($(this).select2('data'), function(value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                              });

                              if (data.length === 0) {
                                data = [""];
                              }

                              var val = data.join('|');

                              column.search(val ? val : '', true, false).draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });

                          $('#' + title).select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: title,
                          });

                          $('.select2').val(null).trigger('change');
                        });

                        this.api().columns([3, 4]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_plant th").eq([d]).text();
                          var select = $('<select><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });
                    }

                    else {

                        $('#datatable_plant').DataTable({
                      "pageLength": 15,
                      "bFilter":false,
                      "bLengthChange": true,
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0, 1, 3, 4]
                        },
                      ],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;
                        this.api().columns([2]).every(function() {
                          var title = this.header();

                          title = $(title).html().replace(/[\W]/g, '-');
                          var column = this;
                          var select = $('<select id="' + title + '" class="select2" ></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {

                              var data = $.map($(this).select2('data'), function(value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                              });

                              if (data.length === 0) {
                                data = [""];
                              }

                              var val = data.join('|');

                              column.search(val ? val : '', true, false).draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });

                          $('#' + title).select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: title,
                          });

                          $('.select2').val(null).trigger('change');
                        });

                        this.api().columns([3, 4]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_plant th").eq([d]).text();
                          var select = $('<select><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });
                    }

                    $('#datatable_3').dataTable({
                      "pageLength": 10,
                      "ordering": false,
                      "info": false,
                      "bFilter": false
                    });

                    $('#datatable_4').DataTable({
                      "pageLength": 10,
                      "bLengthChange": true,
                      "order": [[ 9, "desc" ]],
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0,1,2,3,4,5,6,7,8]
                        }
                      ],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;
                        this.api().columns([2,3,4,5,6,8]).every(function() {
                          var title = this.header();

                          title = $(title).html().replace(/[\W]/g, '-');
                          var column = this;
                          var select = $('<select id="' + title + '" class="select2" ></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {

                              var data = $.map($(this).select2('data'), function(value, key) {
                                return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                              });

                              if (data.length === 0) {
                                data = [""];
                              }

                              var val = data.join('|');

                              column.search(val ? val : '', true, false).draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });

                          $('#' + title).select2({
                            multiple: true,
                            closeOnSelect: false,
                            placeholder: title,
                          });

                          $('.select2').val(null).trigger('change');
                        });

                        this.api().columns([0,7]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_4 th").eq([d]).text();
                          var select = $('<select class="select_dro_vt"><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });

                    $('#datatable_5').DataTable({
                      "pageLength": 10,
                      "bLengthChange": false,
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0,1,2]
                        }
                      ],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;

                        this.api().columns([2]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_5 th").eq([d]).text();
                          var select = $('<select class="select_dro_vt"><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });

                    $('#datatable_6').DataTable({
                      "pageLength": 10,
                      "bLengthChange": false,
                      columnDefs: [
                        {
                          orderable: false,
                          targets: [0,1,2]
                        }
                      ],
                      "info": false,
                      "scrollX": true,
                      initComplete: function() {
                        count = 0;

                        this.api().columns([2]).every(function(d) {
                          var column = this;
                          var theadname = $("#datatable_6 th").eq([d]).text();
                          var select = $('<select class="select_dro_vt"><option value="">' + theadname + "</option></select>")
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                              var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                              );

                              column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                            });

                          column.data().unique().sort().each(function(d, j) {
                            var val = $('<div/>').html(d).text();
                            select.append('<option value="' + val + '">' + val + '</option>');
                          });
                        });
                      }
                    });

                    $('#datatable_alert').dataTable({
                      "deferRender": true
                    });

                    $('#datatable_7').dataTable({
                        "bLengthChange": false,
                        "pageLength":5,
                        "ordering": false,
                        "bFilter": false,
                        "bInfo": false,
                    });

                    setTimeout($('#datatable_1').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_1').DataTable().page.len(5).draw(), 2000);
                    setTimeout($('#datatable_2').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_2').DataTable().page.len(15).draw(), 2000);
                    setTimeout($('#datatable_plant').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_plant').DataTable().page.len(15).draw(), 2000);
                    setTimeout($('#datatable_3').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_3').DataTable().page.len(10).draw(), 2000);
                    setTimeout($('#datatable_4').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_4').DataTable().page.len(10).draw(), 2000);
                    setTimeout($('#datatable_5').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_5').DataTable().page.len(10).draw(), 2000);
                    setTimeout($('#datatable_6').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_6').DataTable().page.len(10).draw(), 2000);
                    setTimeout($('#datatable_7').DataTable().page.len(25).draw(), 2000);
                    setTimeout($('#datatable_7').DataTable().page.len(10).draw(), 2000);

                    $('.deleteuser').click(function() {

                      var user_id = $(this).attr('data-id');

                      $('.deletuser_modal #user_id').val(user_id);

                    });

                    $('.report_user').change(function() {

                      if ($(this).val() == 1 || $(this).val() == 2) {

                        $('.all_company').hide();

                        $('.all_plants').hide();

                      } else if ($(this).val() == 3 || $(this).val() == 4) {

                        $('.all_plants').hide();

                        $('.all_company').show();

                      } else if ($(this).val() == 5) {

                        $('.all_company').show();

                        $('.all_plants').show();

                      } else if ($(this).val() == 6) {

                        $('.all_company').show();

                        $('.all_plants').show();

                      }

                    });

                    $('.viewuser').click(function() {

                      var user_detail = $(this).attr('data-user_detail');

                      var values = JSON.parse(user_detail);

                      var company_name = $(this).attr('data-company_name');

                      var plant_name = $(this).attr('data-plant_name');

                      // console.log(values);

                      $('#name_val').html(values.name);

                      $('#email_val').html(values.email);

                      $('#username_val').html(values.username);

                      if (values.roles == 1) {

                        $('#report_type_val').html('Super Admin');

                      } else if (values.roles == 2) {

                        $('#report_type_val').html('Super NOC');

                      } else if (values.roles == 3) {

                        $('#report_type_val').html('Company Admin');

                      } else if (values.roles == 4) {

                        $('#report_type_val').html('Company NOC');

                      } else if (values.roles == 5) {

                        $('#report_type_val').html('Company User');

                      } else if (values.roles == 6) {

                        $('#report_type_val').html('Individual User');

                      }

                      if (company_name) {

                        $('#company_val').html(company_name);

                      } else if (values.roles == 6) {

                        $('#company_val').html('No Company');

                      } else {

                        $('#company_val').html('- - -');

                      }

                      if (plant_name) {

                        $('#plant_val').html(plant_name);

                      } else {

                        $('#plant_val').html('- - -');

                      }

                    });





                    $('.edituser').click(function() {

                      var user_detail = $(this).attr('data-user_detail');

                      var values = JSON.parse(user_detail);

                      var plant_id = $(this).attr('data-plant_id').split(',');

                      // console.log(plant_id);

                      $('.edit_user_detail #user_id').val(values.id);

                      $('.edit_user_detail #name').val(values.name);

                      $('.edit_user_detail #email').val(values.email);

                      $('.edit_user_detail #username').val(values.username);

                      $('.edit_user_detail #user_type').val(values.roles);

                      if (values.company_id) {

                        $('.edit_user_detail #company_id').val(values.company_id);

                      } else {

                        if (values.roles == 1 || values.roles == 2) {

                          $('.edit_user_detail .all_company').hide();

                        } else {

                          $('.edit_user_detail .all_company').show();



                        }

                      }

                      if (plant_id) {

                        $('.edit_user_detail #plant_id').val(plant_id);

                        if (values.roles > 2) {

                          $('.edit_user_detail .all_plants').show();

                        } else {

                          $('.edit_user_detail .all_plants').hide();

                        }

                      } else {

                        $('.edit_user_detail .all_plants').hide();

                      }

                    });

                  });
                </script>



                <!-- ======================================= -->

                <!-- Companies Script  -->

                <!-- ======================================= -->

                <script type="text/javascript">
                  $(document).ready(function() {

                    $('.deletecompany').click(function() {

                      var company_id = $(this).attr('data-id');

                      $('.deleteuser_modal #company_id').val(company_id);

                    });



                    $('.viewcompany').click(function() {

                      var company_detail = $(this).attr('data-company_detail');

                      var values = JSON.parse(company_detail);

                      $('#company_name_val').html(values.company_name);

                      $('#phone_val').html(values.contact_number);

                      $('#email_val').html(values.email);

                    });



                    /*$('.editcompany').click(function() {

                      var company_detail = $(this).attr('data-company_detail');

                      var values = JSON.parse(company_detail);
                      console.log(values);

                      $('.edit_company_detail #company_id').val(values.id);

                      $('.edit_company_detail #company_name').val(values.company_name);

                      $('.edit_company_detail #contact_number').val(values.contact_number);

                      $('.edit_company_detail #email').val(values.email);

                    });*/





                    $('.alerts').click(function() {

                      var type = $(this).attr('data-alert_type');

                      var importance = $(this).attr('data-importance');

                      var plant_name = $(this).attr('data-plant_name');

                      var description = $(this).attr('data-description');

                      var alarm_code = $(this).attr('data-alarm_code');

                      var correction_action = $(this).attr('data-correction_action');

                      var created_at = $(this).attr('data-created_at');

                      var updated_at = $(this).attr('data-updated_at');

                      // updated_at = updated_at ? updated_at : 'Current';



                      $('.type').html(type);

                      $('.importance').html(importance);

                      $('.plant_name').html(plant_name);

                      $('.description').html(description);

                      $('.alarm_code').html(alarm_code);

                      $('.correction_action').html(correction_action);

                      $('.from').html(created_at);

                      $('.to').html(updated_at);

                    });

                  });
                </script>



                <!-- ======================================= -->

                <!-- Choose File or Image  -->

                <!-- ======================================= -->

                <script>
                  $('#chooseFile').bind('change', function() {

                    var filename = $("#chooseFile").val();

                    if (/^\s*$/.test(filename)) {

                      $(".file-upload").removeClass('active');

                      $("#noFile").text("No file chosen...");

                    } else {

                      $(".file-upload").addClass('active');

                      $("#noFile").text(filename.replace("C:\\fakepath\\", ""));

                    }

                  });

                  $('#chooseFile1').bind('change', function() {

                    var filename = $("#chooseFile1").val();

                    if (/^\s*$/.test(filename)) {

                      $(".file-upload").removeClass('active');

                      $("#noFile1").text("No file chosen...");

                    } else {

                      $(".file-upload").addClass('active');

                      $("#noFile1").text(filename.replace("C:\\fakepath\\", ""));

                    }

                  });



                  // $('.edit_user_detail #chooseFile').bind('change', function() {

                  //   var filename = $(".edit_user_detail #chooseFile").val();

                  //   if (/^\s*$/.test(filename)) {

                  //     $(".edit_user_detail .file-upload").removeClass('active');

                  //     $(".edit_user_detail #noFile").text("No file chosen...");

                  //   } else {

                  //     $(".edit_user_detail .file-upload").addClass('active');

                  //     $(".edit_user_detail #noFile").text(filename.replace("C:\\fakepath\\", ""));

                  //   }

                  // });



                  // $('.edit_company_detail #chooseFile').bind('change', function() {

                  //   var filename = $(".edit_company_detail #chooseFile").val();

                  //   if (/^\s*$/.test(filename)) {

                  //     $(".edit_company_detail .file-upload").removeClass('active');

                  //     $(".edit_company_detail #noFile").text("No file chosen...");

                  //   } else {

                  //     $(".edit_company_detail .file-upload").addClass('active');

                  //     $(".edit_company_detail #noFile").text(filename.replace("C:\\fakepath\\", ""));

                  //   }

                  // });
                </script>



                <script type="text/javascript">
                  $('#pac-input').keydown(function(event) {

                    if (event.keyCode == 13) {

                      event.preventDefault();

                      return false;

                    }

                  });

                  function format_output(num) {
                    return parseInt(Math.log(num) / Math.log(10));
                  }

                  function site_Id_data() {

                    var site_id = $('.site_Id_data').val();

                    var lat = $('.' + site_id).attr('data-lat');

                    var long = $('.' + site_id).attr('data-long');

                    var isOnline = $('.' + site_id).attr('data-isOnline');

                    var alarmLevel = $('.' + site_id).attr('data-alarmLevel');



                    if (lat) {

                      $('#loc_lat').val(lat);

                      $('#loc_lat').attr('readonly', true);

                    }

                    if (long) {

                      $('#loc_long').val(long);

                      $('#loc_long').attr('readonly', true);

                    }

                    $('#isOnline').val(isOnline);

                    $('#alarmLevel').val(alarmLevel);



                    // console.log(lat,long);

                    if (lat && long) {

                      get_data_agaist_lat_log(lat, long);

                    }

                  }



                  $('#loc_lat').keyup(function() {

                    var lat = $('#loc_lat').val();

                    var long = $('#loc_long').val();

                    if (long != '' && lat.length > 5) {

                      console.log(lat);

                      console.log(long);

                      get_data_agaist_lat_log(lat, long)

                    }

                  });

                  $('#loc_long').keyup(function() {

                    var lat = $('#loc_lat').val();

                    var long = $('#loc_long').val();

                    if (lat != '' && long.length > 5) {

                      console.log(lat);

                      console.log(long);

                      get_data_agaist_lat_log(lat, long)

                    }

                  });





                  function get_data_agaist_lat_log(lat, long) {

                    if (lat && long) {

                      $.ajax({

                        url: "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + long + "&key=AIzaSyB12hWno8_DIMqw7xCV1QeqYn6I8FiIxVw",

                        success: function(res) {

                          $('#location').val(res.results[1].formatted_address);
                          $('#pac-input').val(res.results[1].formatted_address);

                          var address = res.results[0].address_components;
                          console.log(res);
                          console.log(address);

                          for (var i = 0; i <= address.length; i++) {

                            if (address[i].types[0] === "administrative_area_level_2") {

                              var city = address[i].long_name;

                              $('#city').val(city);

                            } else if (address[i].types[0] === "administrative_area_level_3") {

                              var city = address[i].long_name;

                              $('#city').val(city);

                            } else if (address[i].types[0] === "locality") {

                              var city = address[i].long_name;

                              $('#city').val(city);

                            }

                            if (address[i].types[0] === "administrative_area_level_1") {

                              var province = address[i].long_name;

                              $('#province').val(province);

                            }

                          }

                        }

                      });

                    }

                  }
                </script>

                <!-- Plugins js-->



                <script src="{{ asset('assets/libs/selectize/js/standalone/selectize.min.js')}}"></script>

                <script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js')}}"></script>

                <script src="{{ asset('assets/libs/dropify/js/dropify.min.js')}}"></script>



                <!-- Third Party js-->



                <!--Chartist Chart-->

                <!-- Init js -->

                <!-- Custom DatePicker JS -->

                <script src="{{ asset('assets/js/date-picker-custom.js')}}"></script>

                <!--C3 Chart-->






                <!--C3 Chart-->





                <!-- Sparkline charts -->




                <!-- Chart JS -->




                <script src="{{ asset('assets/libs/summernote/summernote-bs4.min.js')}}"></script>

                <!-- Init js -->
                <script src="{{ asset('assets/js/pages/form-summernote.init.js')}}"></script>

                <!-- Peity chart-->





                <script src="{{ asset('assets/libs/multiselect/js/jquery.multi-select.js') }}"></script>

                <script src="{{ asset('assets/libs/select2/js/select2.min.js')}}"></script>

                <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>


                <!-- Footable js -->


                <!-- Init js-->

                <script src="{{ asset('assets/js/datepicker.all.js')}}"></script>
                <script src="{{ asset('assets/js/datepicker.en.js')}}"></script>


                <script src="{{ asset('assets/js/pages/form-pickers.init.js')}}"></script>


                <script src="{{ asset('assets/js/pages/form-fileuploads.init.js')}}"></script>

                <script src="{{ asset('assets/js/app.min.js')}}"></script>

                <!-- Add -->
                <!-- <script src="{{ asset('assets/libs/ladda/spin.min.js')}}"></script> -->
                <script src="{{ asset('assets/js/loader.js.js')}}"></script>
                <script src="{{ asset('assets/js/jquery.min.js')}}"></script>

                <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" defer></script>

                <script>
                  $(".dial").knob({

                    'change': function(v) {

                      console.log(v);



                    }

                  });
                </script>

                <script>
                  $('#province').change(function() {

                    province = $('#province').val();

                    $.ajax({

                      type: 'GET',

                      url: 'city/' + province,

                      success: function(res) {

                        $('#city').html(res);

                      }

                    });

                  });
                </script>

                <script type="text/javascript">
                  $(function() {
                    $('.J-datepicker-time').datePicker({
                      format: 'HH:mm:ss',
                      min: '04:23:11',
                      language: 'en'
                    });
                    $('.J-datepicker-time-range').datePicker({
                      format: 'HH:mm:ss',
                      isRange: true,
                      min: '04:23:11',
                      max: '20:59:59',
                      language: 'en'
                    });

                    var DATAPICKERAPI = {
                      activeMonthRange: function() {
                        return {
                          begin: moment().set({
                            'date': 1,
                            'hour': 0,
                            'minute': 0,
                            'second': 0
                          }).format('YYYY-MM-DD HH:mm:ss'),
                          end: moment().set({
                            'hour': 23,
                            'minute': 59,
                            'second': 59
                          }).format('YYYY-MM-DD HH:mm:ss')
                        }
                      },
                      shortcutMonth: function() {
                        var nowDay = moment().get('date');
                        var prevMonthFirstDay = moment().subtract(1, 'months').set({
                          'date': 1
                        });
                        var prevMonthDay = moment().diff(prevMonthFirstDay, 'days');
                        return {
                          now: '-' + nowDay + ',0',
                          prev: '-' + prevMonthDay + ',-' + nowDay
                        }
                      },
                      shortcutPrevHours: function(hour) {
                        var nowDay = moment().get('date');
                        var prevHours = moment().subtract(hour, 'hours');
                        var prevDate = prevHours.get('date') - nowDay;
                        var nowTime = moment().format('HH:mm:ss');
                        var prevTime = prevHours.format('HH:mm:ss');
                        return {
                          day: prevDate + ',0',
                          time: prevTime + ',' + nowTime,
                          name: 'Nearly ' + hour + ' Hours'
                        }
                      },
                      rangeMonthShortcutOption1: function() {
                        var result = DATAPICKERAPI.shortcutMonth();
                        var resultTime = DATAPICKERAPI.shortcutPrevHours(18);
                        return [{
                          name: 'Yesterday',
                          day: '-1,-1',
                          time: '00:00:00,23:59:59'
                        }, {
                          name: 'This Month',
                          day: result.now,
                          time: '00:00:00,'
                        }, {
                          name: 'Lasy Month',
                          day: result.prev,
                          time: '00:00:00,23:59:59'
                        }, {
                          name: resultTime.name,
                          day: resultTime.day,
                          time: resultTime.time
                        }];
                      },
                      rangeShortcutOption1: [{
                        name: 'Last week',
                        day: '-7,0'
                      }, {
                        name: 'Last Month',
                        day: '-30,0'
                      }, {
                        name: 'Last Three Months',
                        day: '-90, 0'
                      }],
                      singleShortcutOptions1: [{
                        name: 'Today',
                        day: '0',
                        time: '00:00:00'
                      }, {
                        name: 'Yesterday',
                        day: '-1',
                        time: '00:00:00'
                      }, {
                        name: 'One Week Ago',
                        day: '-7'
                      }]
                    };
                    $('.J-datepicker').datePicker({
                      hasShortcut: true,
                      language: 'en',
                      min: '2018-01-01 04:00:00',
                      max: '2029-10-29 20:59:59',
                      shortcutOptions: [{
                        name: 'Today',
                        day: '0'
                      }, {
                        name: 'Yesterday',
                        day: '-1',
                        time: '00:00:00'
                      }, {
                        name: 'One Week Ago',
                        day: '-7'
                      }],
                      hide: function() {
                        console.info(this)
                      }
                    });


                    $('.J-datepicker-day').datePicker({
                      hasShortcut: true,
                      language: 'en',
                      shortcutOptions: [{
                        name: 'Today',
                        day: '0'
                      }, {
                        name: 'Yesterday',
                        day: '-1'
                      }, {
                        name: 'One week ago',
                        day: '-7'
                      }]
                    });


                    $('.J-datepicker-range-day').datePicker({
                      hasShortcut: true,
                      language: 'en',
                      format: 'YYYY-MM-DD',
                      isRange: true,
                      shortcutOptions: DATAPICKERAPI.rangeShortcutOption1
                    });


                    $('.J-datepickerTime-single').datePicker({
                      format: 'YYYY-MM-DD HH:mm',
                      language: 'en',
                    });


                    $('.J-datepickerTime-range').datePicker({
                      format: 'YYYY-MM-DD HH:mm',
                      isRange: true,
                      language: 'en'
                    });


                    $('.J-datepicker-range').datePicker({
                      hasShortcut: true,
                      language: 'en',
                      min: '2018-01-01 06:00:00',
                      max: '2029-04-29 20:59:59',
                      isRange: true,
                      shortcutOptions: [{
                        name: 'Yesterday',
                        day: '-1,-1',
                        time: '00:00:00,23:59:59'
                      }, {
                        name: 'Last Week',
                        day: '-7,0',
                        time: '00:00:00,'
                      }, {
                        name: 'Last Month',
                        day: '-30,0',
                        time: '00:00:00,'
                      }, {
                        name: 'Last Three Months',
                        day: '-90, 0',
                        time: '00:00:00,'
                      }],
                      hide: function(type) {
                        console.info(this.$input.eq(0).val(), this.$input.eq(1).val());
                        console.info('Type:', type)
                      }
                    });
                    $('.J-datepicker-range-betweenMonth').datePicker({
                      isRange: true,
                      between: 'month',
                      language: 'en',
                      hasShortcut: true,
                      shortcutOptions: DATAPICKERAPI.rangeMonthShortcutOption1()
                    });


                    $('.J-datepicker-range-between30').datePicker({
                      isRange: true,
                      language: 'en',
                      between: 30
                    });

                    /*$('.J-yearMonthDayPicker-single').datePicker({
                      format: 'YYYY-MM-DD',
                      language: 'en',
                      hide: function(type) {
                        console.info(this.$input.eq(0).val());
                      }
                    });*/

                    /*$('.J-yearMonthPicker-single').datePicker({
                      format: 'MM-YYYY',
                      language: 'en',
                      hide: function(type) {
                        console.info(this.$input.eq(0).val());
                      }
                    });

                    $('.J-yearPicker-single').datePicker({
                      format: 'YYYY',
                      language: 'en',
                    });*/


                  });

                  // Open Model
                  var mpptID = 0;

                  $('#changeMPPT').change(function() {

                    $('.modal-body').empty();

                    mpptID = $('#changeMPPT').val();

                    console.log(mpptID);
                    if (mpptID != '') {

                      $('.modal-body').append('<div class="row"></div>');

                      for (var i = 1; i <= mpptID * 2; i++) {

                        $('.modal-body .row .col-md-6 #string' + i + 'mppt').remove();

                        $('.modal-body .row').append('<div class="col-md-6 mb-3">' +
                          '<label>String ' + i + ' :</label>' +
                          '<select class="select_drop_vt" name="string' + i + 'mppt" id="string' + i + 'mppt">' +
                          '<option>Select MPPT</option>' +
                          '</select>' +
                          '</div>');

                        for (var j = 1; j <= mpptID; j++) {
                          $('.modal-body .row .col-md-6 #string' + i + 'mppt').append('<option value="MPPT' + j + '">MPPT ' + j + '</option>');
                        }

                      }

                      var title = $(this).val();
                      $('.modal-title').html(title);
                      $('.modal').modal('show');
                    }


                  });
                </script>
                <script src="{{ asset('assets/js/moment.min.js')}}"></script>
                <script src="{{ asset('assets/js/echarts.min.js')}}"></script>
                <script src="{{ asset('assets/js/jquery.dataTables.min.js')}}"></script>
                <script src="{{ asset('assets/js/datepicker.all.js')}}"></script>
                <script src="{{ asset('assets/js/datepicker.en.js')}}"></script>

                </body>

                </html>
