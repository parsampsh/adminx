<?php
$username = $core->get_userinfo()['username'];
$userimage = $core->get_userinfo()['image'];
$logout_url = url($core->get_logout());
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ $core->get_title() }} - @yield('adminx_title')</title>
  <link href="{{ url('/adminx-public/default/font-awesome.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ url('/adminx-public/default/styles.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ url('/adminx-public/default/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  @if($core->is_rtl())
  <link href="{{ url('/adminx-public/default/bootstrap-rtl.css') }}" rel="stylesheet">
  <style>
    *{
      direction: rtl !important;
      text-align: right !important;
    }

    #userDropdown{
      float: left !important;
    }

    #accordionSidebar{
      padding-right: 5px;
      padding-left: 20px;
    }

    .nav-link span{
      margin: 2px;
    }

    #sidebarToggle{
      direction: ltr !important;
      text-align: center !important;
    }
  </style>
  @endif
  @if($core->get_font() !== '')
    <style>
    @font-face {
    font-family: customfont;
    src: url('{{ $core->get_font() }}');
    }

    *{
    font-family: customfont;
    }
    </style>
  @endif
</head>
<body id="page-top">
  <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ $core->url('/') }}">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ $core->get_title() }}</div>
      </a>

      <hr class="sidebar-divider my-0">

      <li class="nav-item">
        <a class="nav-link" href="{{ $core->url('/') }}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>{{ $core->get_word('menu.dashboard', 'Dashboard') }}</span></a>
      </li>

      <hr class="sidebar-divider">

      @foreach($core->get_menu() as $item)
        @if($item['type'] === 'link')
          <li class="nav-item">
            <a class="nav-link" href="{{ $item['link'] }}" target="{{ $item['target'] }}">
            <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
          </li>
        @else
          @if($item['type'] === 'page' && $item['slug'] !== '.')
            <li class="nav-item">
              <a class="nav-link" href="{{ $core->url('page/' . $item['slug']) }}" target="{{ $item['target'] }}">
              <i class="{{ $item['icon'] }}"></i><span>{{ $item['title'] }}</span></a>
            </li>
          @else
            @if($item['type'] === 'model')
              <li class="nav-item">
                <a class="nav-link" href="{{ $core->url('model/' . $item['config']['slug']) }}" target="{{ $item['config']['target'] }}">
                <i class="{{ $item['config']['icon'] }}"></i><span>{{ $item['config']['title'] }}</span></a>
              </li>
            @endif
          @endif
        @endif
      @endforeach

      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <ul class="navbar-nav ml-auto">

            <div class="topbar-divider d-none d-sm-block"></div>

            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ $username }}</span>
                <img class="img-profile rounded-circle" src="{{ $userimage }}">
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Item 1
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Item 2
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Item 3
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>{{ $core->get_word('logout.btn', 'Logout') }}</a>
              </div>
            </li>

          </ul>

        </nav>

        <div class="container">
        @yield('adminx_content')
        </div>

      </div>

      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>{{ $core->get_copyright() }}</span>
            <br />
            <span>Theme by <a target="_blank" href="https://startbootstrap.com/theme/sb-admin-2">startbootstrap</a></span>
            <br />
            <span>Powered by <a target="_blank" href="https://github.com/parsampsh/adminx">adminx</a></span>
          </div>
        </div>
      </footer>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ $core->get_word('logout.title', 'Ready to Leave?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">{{ $core->get_word('logout.message', 'Select "Logout" below if you are ready to end your current session.') }}</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ $core->get_word('logout.cancel', 'Cancel') }}</button>
          <a class="btn btn-primary" href="{{ $logout_url }}">{{ $core->get_word('logout.btn', 'Logout') }}</a>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ url('/adminx-public/default/jquery.min.js') }}"></script>
  <script src="{{ url('/adminx-public/default/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ url('/adminx-public/default/jquery.easing.min.js') }}"></script>
  <script src="{{ url('/adminx-public/default/sb-admin-2.min.js') }}"></script>
  <script src="{{ url('/adminx-public/default/select2/select2.full.min.js') }}"></script>
  <script>
    $(document).ready(function(){
      $('.select2-box').select2();
    });
  </script>
</body>
</html>