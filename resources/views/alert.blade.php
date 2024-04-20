@if(Session::has('message'))
    <div class="alert {{ Session::get('alert-class', 'alert-info') }}" role="alert">
        <i class="mdi mdi-alert-circle-outline mr-2"></i>{{ Session::get('message') }}
        <script>
            setTimeout(function () {
                $('div.alert').toggle(1000);
            }, 20000);
        </script>
    </div>
@endif 
