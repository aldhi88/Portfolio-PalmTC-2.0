<!DOCTYPE html>
<html lang="en">
<head>
    <title>404 - Page Not Found</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="{{ env('APP_AUTHOR') }}" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ env('PORTAL_URL').'/images/logo.png' }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ env('PORTAL_URL').'/assets/css/style.css' }}">
    
    

</head>
<!-- [ offline-ui ] start -->
<div class="auth-wrapper maintance">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center">
                    <img src="{{ env('PORTAL_URL').'/assets/images/maintance/404.png' }}" alt="" class="img-fluid">
                    <h5 class="text-muted my-4">Sorry! The page you are looking for was not found!</h5>
                    <form action="index.html">
                        <a href="{{ url()->previous() }}" class="btn waves-effect waves-light btn-primary mb-4 text-light"><i class="feather icon-corner-up-left mr-2"></i>Back </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ offline-ui ] end -->
<!-- Required Js -->
<script src="{{ env('PORTAL_URL').'/assets/js/vendor-all.min.js' }}"></script>
<script src="{{ env('PORTAL_URL').'/assets/js/plugins/bootstrap.min.js' }}"></script>


</body>
</html>
