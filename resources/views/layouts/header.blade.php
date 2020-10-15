 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>

    </ul>
	
	<ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/users">Users</i></a>
        </li>

    </ul>
	
	<ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/payments">Payments</i></a>
        </li>

    </ul>
	
	<ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard/categories">Categories</i></a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

	
	<li class="nav-item dropdown">
	 <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();
               document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
     </a>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
     </form>
	 </li>
		
    </ul>
	
</nav>
<!-- /.navbar -->