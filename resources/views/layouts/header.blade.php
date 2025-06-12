<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        <a href="{{ env('APP_URL') }}" class="b-brand">
            <!-- ========   change your logo hear   ============ -->
            <img src="{{ asset('assets/images/logo.png') }}" alt="" class="logo">
            
        </a>
        <a href="#!" class="mob-toggler">
            <i class="feather icon-more-vertical"></i>
        </a>
    </div>
    
    <div class="collapse navbar-collapse">
        
        <ul class="navbar-nav ml-auto">
            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="feather icon-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            <img src="{{ asset('assets/images/kuljs.png') }}" class="img-radius" alt="User-Profile-Image">
                            <span>{{ Str::title(Auth::user()->nama) }}</span>
                        </div>
                        <ul class="pro-body">
                            <li><a href="user-profile.html" class="dropdown-item"><i class="feather icon-user"></i> My Profile</a></li>
                            <li><a href="email_inbox.html" class="dropdown-item"><i class="feather icon-package"></i> Portal List</a></li>
                            <li><a href="{{ route('auth.logout') }}" class="dropdown-item"><i class="feather icon-lock"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>


</header>