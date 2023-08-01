<aside class="main-sidebar sidebar-light-primary elevation-4 bg-white ">
    <div class="w-100 text-center my-3">
        <!-- Brand Logo -->
        <a href="index3.html" class="my-3">
            <img src="/img/logo.svg" alt="Logo" class="brand-image" width="40%">
        </a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item mb-2">
                    <a href="/" class="nav-link {{ url('/') ? 'active' : '' }}">
                        <img src="/img/explore.svg" alt="explore" class="nav-icon">
                        <p>
                            Explore Now
                        </p>
                    </a>
                </li>
                @canany(['user.list', 'role_permission.list'])
                    <li class="nav-item menu-open my-2">
                        <a href="#" class="nav-link">
                            <img src="/img/users.svg" alt="users" class="nav-icon">
                            <p>
                                User Management
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('user.list')
                                <li class="nav-item">
                                    <a href="/users" class="nav-link">
                                        <p>Users</p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_permission.list')
                                <li class="nav-item">
                                    <a href="/roles" class="nav-link">
                                        <p>Roles</p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('role_permission.list')
                                <li class="nav-item">
                                    <a href="/permissions" class="nav-link">
                                        <p>Permissions</p>
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcanany

                @canany(['master_category.list', 'master_article.list'])
                    <li class="nav-item my-2">
                        <a href="#" class="nav-link">
                            <img src="/img/master.svg" alt="master" class="nav-icon">
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('master_category.list')
                                <li class="nav-item">
                                    <a href="/master-categories" class="nav-link">
                                        <p>Master Category</p>
                                    </a>
                                </li>
                            @endcan
                            @can('master_article.list')
                                <li class="nav-item">
                                    <a href="/master-articles" class="nav-link">
                                        <p>Master Article</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
