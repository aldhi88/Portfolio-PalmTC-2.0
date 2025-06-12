<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME').$data['title'] }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="{{ env('APP_AUTHOR') }}" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('assets/images/kuljs.png') }}" type="image/x-icon">
    <!-- vendor css -->
	@yield('css')

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/mine/mystyle.css') }}">
	{{-- @livewireStyles --}}
</head>
<body class="background-grd-purple">
	<!-- [ Pre-loader ] start -->
	<div class="loading"><div class="loader"></div></div>

	@include('layouts.navbar')

	@include('layouts.header')

<!-- [ Main Content ] start -->
	<div class="pcoded-main-container">
		<div class="pcoded-content">
			<!-- [ breadcrumb ] start -->
			<div class="page-header">
				<div class="page-block">
					<div class="row align-items-center">
						<div class="col-md-12">
							<div class="page-header-title">
								<h4 class="m-b-10 text-white">{{ $data['title'] }}</h4>
							</div>
							<ul class="breadcrumb">
								{{-- <li class="breadcrumb-item"><a href="#"><i class="feather icon-chevrons-left"></i> Back</a></li> --}}
								<li class="breadcrumb-item"><a href="javascript: void(0)" style="cursor: default;"><i class="feather icon-info"></i> {{ $data['desc'] }}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- [ breadcrumb ] end -->
			<!-- [ Main Content ] start -->
			@yield('content')
			<!-- [ Main Content ] end -->
		</div>
	</div>

<!-- Required Js -->
<script src="{{ asset('assets/js/vendor-all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/ripple.js') }}"></script>
<script src="{{ asset('assets/js/pcoded.min.js') }}"></script>
<script src="{{ asset('assets/mine/myscript.js') }}"></script>
@yield('js')
@stack('pushJs')
{{-- @livewireScripts --}}
</body>

</html>
