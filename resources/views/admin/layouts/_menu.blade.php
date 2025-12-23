<!--左侧导航-->
<aside class="lyear-layout-sidebar">
    <!-- logo -->
    <div id="logo" class="sidebar-header">
        <a href="{{ route('admin.main') }}"><img src="{{ asset('/images/logo-sidebar.png') }}" title="Logo" alt="Logo" /></a>
    </div>
    <div class="lyear-layout-sidebar-scroll">
        <nav class="sidebar-main">
            <ul class="nav nav-drawer">
            @foreach (app(App\Services\MenuService::class)->getPermissionsByGuard() as $permissions)
                <li class="nav-item{{ $loop->first ? ' active' : ($permissions->children ? ' nav-item-has-subnav' : '') }}">
                    @if($permissions->route_name)
                    <a class="multitabs" href="{{ route($permissions->route_name) }}" title="{{ $permissions->getLangName() }}">
                    @else
                    <a href="javascript:void(0)" title="{{ $permissions->getLangName() }}">
                    @endif
                    <i class="{{ $permissions->icon }}"></i><span>{{ $permissions->getLangName() }}</span>
                    </a>
                    @if($permissions->children)
                    <ul class="nav nav-subnav">
                    @foreach ($permissions->children as $permission)
                        @if($permission->is_show && $permission->isItemShow())
                            <li><a class="multitabs" href="{{ $permission->route_name ? route($permission->route_name) : '#' }}" title="{{ $permission->getLangName() }}"><i class="{{ $permission->icon }}"></i>{{ $permission->getLangName() }}</a></li>
                        @endif
                    @endforeach
                    </ul>
                    @endif
                </li>
            @endforeach
            </ul>
        </nav>
    </div>
</aside>
<!--End 左侧导航-->
