<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
    <img src="/images/icons/58.png" alt="Live waves logo" class="brand-image img-circle elevation-3"
   style="opacity: .8">
<span class="brand-text font-weight-light">LiveWaves</span>
</a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="/img/profile.png" class="img-circle elevation-2" alt="User Image">l
            </div>
            <div class="info">
                <a href="#" class="d-block"> {{auth()->user()->name!=null ? auth()->user()->name : "Administrator"}} </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				
				<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
						<i class="nav-icon fa fa-users"></i>
						<p>
							Users
							<i class="right fa fa-angle-left"></i>
						</p>
					</a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/users" class="nav-link">
								<i class="fa fa-users nav-icon"></i>
								<p>List Users</p>
							</a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/user/add" class="nav-link">
								<i class="fa fa-plus nav-icon"></i>
								<p>Add user</p>
						  </a>
                        </li>
                    </ul>
                </li>
				
				
				<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
						<i class="nav-icon fa fa-list "></i>
						<p>
							Categories
							<i class="right fa fa-angle-left"></i>
						</p>
					</a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/categories" class="nav-link">
								<i class="fa fa-list  nav-icon"></i>
								<p>Categories</p>
							</a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/subcategories" class="nav-link">
								<i class="fa fa-list-alt nav-icon"></i>
								<p>Sub Categories</p>
						  </a>
                        </li>
                    </ul>
                </li>
				
				
				
				<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
						<i class="nav-icon 	fa fa-credit-card"></i>
						<p>
							Payments
							<i class="right fa fa-angle-left"></i>
						</p>
					</a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/payments" class="nav-link">
								<i class="fa fa-credit-card nav-icon"></i>
								<p>List All Payments</p>
							</a>
                        </li>
						
						<li class="nav-item">
                            <a href="/dashboard/payments/donations" class="nav-link">
								<i class="fa fa-credit-card nav-icon"></i>
								<p>List Donations</p>
							</a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/dashboard/payments/ppv" class="nav-link">
								<i class="fa fa-credit-card nav-icon"></i>
								<p>List PPV payments</p>
							</a>
                        </li>
                        
                        
						<li class="nav-item">
                            <a href="/dashboard/payments/event" class="nav-link">
								<i class="fa fa-credit-card nav-icon"></i>
								<p>List event payments</p>
							</a>
                        </li>
                        
                    </ul>
                </li>
                
                
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
						<i class="nav-icon fa fa-money"></i>
						<p>
							Withdrawls
							<i class="right fa fa-angle-left"></i>
						</p>
					</a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/withdrawls" class="nav-link">
								<i class="fa fa-money nav-icon"></i>
								<p>List Withdrawls</p>
							</a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/withdrawls/requests" class="nav-link">
								<i class="fa fa-money nav-icon"></i>
								<p>List Withdrawl Requests</p>
						  </a>
                        </li>
                    </ul>
                </li>
				
				
				<li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
						<i class="nav-icon fa fa-cog"></i>
						<p>
							Settings
							<i class="right fa fa-angle-left"></i>
						</p>
					</a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/dashboard/setting/contact_us" class="nav-link">
								<i class="fa fa-phone nav-icon"></i>
								<p>Contact Us</p>
							</a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/setting/terms_privacy" class="nav-link">
								<i class="fa fa-info nav-icon"></i>
								<p>Terms & Privacy</p>
						  </a>
                        </li>
                        <li class="nav-item">
                            <a href="/dashboard/setting/help" class="nav-link">
								<i class="fa fa-question-circle nav-icon"></i>
								<p>Help</p>
						  </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                <a href="/dashboard/setting/password" class="nav-link">
						<i class="nav-icon fa fa-key"></i>
						<p>
							Change Password
						</p>
					</a>
                </li>
				
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>