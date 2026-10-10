@php
    $branch = \App\Models\Master\Branch::find(Session::get('branch_id'));
    $branchSidebarIds = !empty($branch->branch_sidebar_id) ? explode(',', $branch->branch_sidebar_id) : [];
    $Permisn = Helper::getPermisn();

    if ((int) Session::get('role_id') === 1) {
        // Admin: Show all modules and submenus for comprehensive review
        $sidebar = DB::table('sidebars')->orderBy('order_by','ASC')->get();
        $subSidebar = DB::table('sidebar_sub')->whereNull('deleted_at')->orderBy('orderBy','ASC')->get();
    } else {
        $sidebar = DB::table('sidebars')->whereNull('deleted_at')->whereIn('id', $Permisn)->orderBy('order_by','ASC')->get();
        $subSidebar = DB::table('sidebar_sub')->where('sub_sidebar','yes')->whereIn('sidebar_id', $branchSidebarIds)->whereNull('deleted_at')->orderBy('orderBy','ASC')->get();
    }

    if ((int) Session::get('role_id') === 1) {
        $sidebar = $sidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
        $subSidebar = $subSidebar->reject(function ($item) {
            return trim((string) ($item->url ?? ''), '/') === 'attendance/self';
        })->values();
    }

    $getSetting = Helper::getSetting();
@endphp

<aside class="main-sidebar" id="sidebar">
    <div class="top_brand_section">
        <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 8px; flex: 1; overflow: hidden; text-decoration: none;">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting->left_logo ?? '' }}" 
                 alt="" class="brand_img" 
                 onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'">
            <span class="brand_title">{{ $getSetting->name ?? '' }}</span>
        </a>
        <button type="button" class="mobile-close-sidebar" aria-label="Close Sidebar">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                @foreach($sidebar as $data)
                    @php
                        if ((int) Session::get('role_id') === 1) {
                            $submenus = $subSidebar->where('sidebar_id', $data->id)->pluck('id')->map(fn($id) => (string) $id)->toArray();
                        } else {
                            $submenus = array_map('strval', Helper::getSubPermisn($data->id));
                        }

                        if ((int) $data->id === 17 || trim((string)($data->url ?? ''), '/') === 'settings_dashboard' || trim((string)($data->url ?? ''), '/') === 'viewSetting') {
                            $submenus = [];
                            $data->url = 'editSetting/1';
                        }

                        $activeSub = false;
                        foreach($subSidebar as $sub){
                            if(in_array((string) $sub->id, array_map('strval', $submenus), true) && (url($sub->url) == URL::current() || (!empty($sub->url) && request()->is(trim($sub->url, '/').'*')))){
                                $activeSub = true;
                                break;
                            }
                        }
                        $isParentActive = (url($data->url) == URL::current()) || 
                                          (!empty($data->url) && request()->is(trim($data->url, '/').'*')) ||
                                          ((int) $data->id === 17 && (request()->is('editSetting*') || request()->is('settings*') || request()->is('viewSetting*')));
                    @endphp

                    <li class="nav-item {{ !empty($submenus) ? 'has-treeview' : '' }} {{ $activeSub ? 'menu-open has-active-child' : '' }} {{ ($isParentActive && empty($submenus)) ? 'active' : '' }}">
                        <a href="{{ !empty($submenus) ? '#' : url($data->url) }}" 
                           class="nav-link {{ $isParentActive ? 'active' : ($activeSub ? 'active-parent' : '') }}"
                           title="{{ (Session::get('locale') == 'hi' && !empty($data->hindi_name)) ? $data->hindi_name : ($data->name ?? '') }}">
                            <i class="nav-icon fa {{ $data->ican ?? '' }}"></i>
                            <p>
                                @if(Session::get('locale') == 'hi') 
                                    {{ $data->hindi_name ?? '' }} 
                                @else 
                                    {{ $data->name ?? '' }} 
                                @endif
                                @if(!empty($submenus))
                                    <i class="right fa fa-angle-left"></i>
                                @endif
                            </p>
                        </a>

                        @if(!empty($submenus))
                            <ul class="nav nav-treeview" style="{{ $activeSub ? 'display: block;' : 'display: none;' }}">
                                @foreach($subSidebar as $sub)
                                    @if(in_array((string) $sub->id, array_map('strval', $submenus), true))
                                        @php
                                            $isChildActive = (url($sub->url) == URL::current() || (!empty($sub->url) && request()->is(trim($sub->url, '/').'*')));
                                        @endphp
                                        <li class="nav-item">
                                            <a href="{{ url($sub->url) }}" 
                                               class="nav-link {{ $isChildActive ? 'active' : '' }}"
                                               title="{{ (Session::get('locale') == 'hi' && !empty($sub->hindi_name)) ? $sub->hindi_name : ($sub->name ?? '') }}">
                                                <i class="fa fa-circle nav-icon"></i>
                                                <p>
                                                    @if(Session::get('locale') == 'hi') 
                                                        {{ $sub->hindi_name ?? '' }} 
                                                    @else 
                                                        {{ $sub->name ?? '' }} 
                                                    @endif
                                                </p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
                @if((int) Session::get('role_id') !== 3)
                <li class="nav-item">
                    <a href="{{ url('notice-management') }}" class="nav-link {{ request()->is('notice-management*') ? 'active' : '' }}" title="Notice Management">
                        <i class="nav-icon fa fa-bullhorn"></i>
                        <p>Notice Management</p>
                    </a>
                </li>
                @endif
                @if((int) Session::get('role_id') === 1)
                <li class="nav-item">
                    <a href="{{ url('complaints-management') }}" class="nav-link {{ request()->is('complaints-management*') ? 'active' : '' }}" title="Complaints Management">
                        <i class="nav-icon fa fa-comments-o"></i><p>Complaints Management</p>
                    </a>
                </li>
                @endif
                {{-- Logout --}}
                 <li class="nav-item"> 
                     <a href="#" class="nav-link" onclick="confirmLogout(event)" title="Log Out"> 
                         <i class="nav-icon fa fa-sign-out"></i>
                         <p>Log Out</p>
                     </a>
                 </li>
              
            </ul>
        </nav>
    </div>
</aside>
