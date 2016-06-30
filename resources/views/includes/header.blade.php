<nav class="navbar navbar-default navbar-static-top" style="background: #227fc3; border: none;">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <strong>TP</strong>CRM
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->


            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <div class="form-group navbar-form navbar-right" role="search">
                    <input type="text" id="searchkey" class="form-control" placeholder="{{ trans('messages.headerTabSearchBoxPlaceholder') }}">
                    <img src="/assets/img/loading.gif" class="media-middle srch-loader invisible" width="24px" alt="loading" >
                    <div style="margin-top: -1px;position: absolute; z-index: 100; background: #f8f8f8; padding: 30px; display: none;" id="navSrchBox" class="table-bordered col-md-3"></div>
                </div>
                <!-- Authentication Links -->
                <li><a href="{{ url('/customer/create') }}">{{ trans('messages.headerTabAddCustomers') }}</a></li>
                <li><a href="{{ url('/services') }}">{{ trans('messages.headerTabServices') }}</a></li>
                <li><a href="{{ url('/events') }}">{{ trans('messages.headerTabEvents') }}</a></li>
            </ul>
        </div>
    </div>
</nav>