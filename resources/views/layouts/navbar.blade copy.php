
<nav class="pcoded-navbar menu-light ">
    <div class="navbar-wrapper">
        <div class="navbar-content scroll-div" >
            
            <div class="mb-3">
                <div class="main-menu-header">
                    <img class="img-radius" src="{{ env('PORTAL_URL').'/images/icon/kuljs.png' }}" alt="User-Profile-Image">
                    <div class="user-details">
                        <div id="more-details">{{ Str::title(Auth::user()->nama) }} <i class="fa fa-caret-down"></i></div>
                    </div>
                </div>
                <div class="collapse" id="nav-user-link">
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="user-profile.html" data-toggle="tooltip" title="View Profile"><i class="feather icon-user"></i></a></li>
                        <li class="list-inline-item"><a href="{{ env('PORTAL_URL') }}"><i class="feather icon-package" data-toggle="tooltip" title="Portal List"></i></a></li>
                        <li class="list-inline-item"><a href="{{ route('auth.logout') }}" data-toggle="tooltip" title="Logout" class="text-danger"><i class="feather icon-power"></i></a></li>
                    </ul>
                </div>
            </div>
            <hr>
            
            <ul class="nav pcoded-inner-navbar">
                
                {{-- new section --}}
                <li class="nav-item pcoded-menu-caption"><label>Dashboard</label></li>
                <li class="nav-item"><a href="{{ route('dashboard.index') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Index</span></a></li>
                <li class="nav-item"><a href="sample-page.html" class="nav-link "><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">About App</span></a></li>

                {{-- new section --}}
                <li class="nav-item pcoded-menu-caption"><label>Master Data</label></li>
                <li class="nav-item"><a href="{{ route('workers.index') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-users"></i></span><span class="pcoded-mtext">Worker</span></a></li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-flask"></i></span><span class="pcoded-mtext">Media</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('bottles.index') }}">Bottle</a></li>
                        <li><a href="{{ route('agars.index') }}">Agar Rose</a></li>
                        <li><a href="{{ route('mediums.index') }}">Medium</a></li>
                        <li><a href="{{ route('medium-stocks.index') }}">Medium Stock</a></li>
                        <li><a href="{{ route('medium-validate.index') }}">Stock Validation</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="{{ route('laminars.index') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-map"></i></span><span class="pcoded-mtext">Laminar</span></a></li>
                <li class="nav-item"><a href="{{ route('rooms.index') }}" class="nav-link "><span class="pcoded-micon"><i class="feather icon-map-pin"></i></span><span class="pcoded-mtext">Room</span></a></li>
                <li class="nav-item"><a href="{{ route('contaminations.index') }}" class="nav-link "><span class="pcoded-micon"><i class="fas fa-bug"></i></span><span class="pcoded-mtext">Contamination</span></a></li>
                <li class="nav-item"><a href="{{ route('treefiles.index') }}" class="nav-link "><span class="pcoded-micon"><i class="fas fa-tree"></i></span><span class="pcoded-mtext">Tree File</span></a></li>
                
                
                {{-- new section --}}
                <li class="nav-item pcoded-menu-caption"><label>Invitro</label></li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-leaf"></i></span><span class="pcoded-mtext">Sample</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('samples.index') }}">All Data</a></li>
                        <li><a href="{{ route('samples.create') }}">Add New</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-vials"></i></span><span class="pcoded-mtext">Initiation</span></a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('inits.index') }}">All Data</a></li>
                        <li><a href="{{ route('inits.create') }}">Add New</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-snowflake"></i></span>
                        <span class="pcoded-mtext">Callus</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('callus-obs.index') }}">
                            Observation 
                        </a></li>
                        <li><a href="{{ route('callus-transfers.index') }}">Transfer</a></li>
                        <li><a href="{{ route('callus-lists.index') }}">Report List</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-stroopwafel"></i></span>
                        <span class="pcoded-mtext">Embryogenesis</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('embryo-lists.index') }}">Bottle List</a></li>
                        <li><a href="{{ route('embryo-obs.index') }}">Observation</a></li>
                        <li><a href="{{ route('embryo-transfers.index') }}">Transfer</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-holly-berry"></i></span>
                        <span class="pcoded-mtext">Liquid</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('liquid-lists.index') }}">Bottle List</a></li>
                        <li><a href="{{ route('liquid-obs.index') }}">Observation</a></li>
                        <li><a href="{{ route('liquid-transfers.index') }}">Transfer</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link "><span class="pcoded-micon"><i class="fas fa-braille"></i></span>
                        <span class="pcoded-mtext">Maturation</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('matur-lists.index') }}">Bottle List</a></li>
                        <li><a href="{{ route('matur-obs.index') }}">Observation</a></li>
                        <li><a href="{{ route('matur-transfers.index') }}">Transfer</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-hasmenu">
                    <a href="#" class="nav-link"><span class="pcoded-micon"><i class="fas fa-cannabis"></i></span>
                        <span class="pcoded-mtext">Germination</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('germin-lists.index') }}">Bottle List</a></li>
                        <li><a href="{{ route('germin-obs.index') }}">Observation</a></li>
                        <li><a href="{{ route('germin-transfers.index') }}">Transfer</a></li>
                    </ul>
                </li>
            
            </ul>
            
            
        </div>
    </div>
</nav>
