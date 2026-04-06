<style>
    /* * ==========================================
     * SIDEBAR SPECIFIC STYLING
     * ==========================================
     */
    .sidebar-wrapper {
        background: linear-gradient(180deg, #0A2342 0%, #051324 100%);
        height: 100%;
        min-height: 100vh;
        color: #ecf0f1;
        box-shadow: 2px 0 15px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        font-family: 'Inter', sans-serif;
        width: 250px; /* Fixed width for desktop */
        transition: all 0.3s ease-in-out;
        z-index: 999;
    }

    /* Branding Section */
    .sidebar-brand {
        padding: 25px 15px;
        text-align: center;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(200, 169, 81, 0.1);
        position: relative;
    }
    .brand-text {
        color: #fff;
        font-weight: 800;
        font-size: 24px;
        letter-spacing: 1px;
        margin-top: 10px;
        display: block;
    }
    .brand-sub {
        color: #C8A951;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    /* User Profile Section */
    .sidebar-user {
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255,255,255,0.02);
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        background: #C8A951;
        color: #0A2342;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        box-shadow: 0 0 10px rgba(200, 169, 81, 0.3);
        flex-shrink: 0;
    }
    .user-info span {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }
    .user-info small {
        color: #94a3b8;
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Navigation Links */
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        flex-grow: 1;
        overflow-y: auto; /* Allow scroll if menu is long */
    }
    .sidebar-menu li {
        margin-bottom: 5px;
    }
    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: #bdc3c7;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-size: 14px;
    }
    .sidebar-link i {
        width: 25px;
        font-size: 16px;
        margin-right: 10px;
        text-align: center;
        transition: transform 0.2s;
    }

    /* Hover & Active States */
    .sidebar-link:hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
        text-decoration: none;
    }
    .sidebar-link:hover i {
        transform: translateX(3px);
        color: #C8A951;
    }
    .sidebar-menu li.active .sidebar-link {
        background: linear-gradient(90deg, rgba(200, 169, 81, 0.15) 0%, transparent 100%);
        border-left-color: #C8A951;
        color: #fff;
        font-weight: 600;
    }
    .sidebar-menu li.active .sidebar-link i {
        color: #C8A951;
    }

    /* Logout Button Area */
    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.05);
    }
    .btn-logout {
        background: rgba(231, 76, 60, 0.1);
        color: #ff6b6b;
        border: 1px solid rgba(231, 76, 60, 0.3);
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        transition: all 0.3s;
        text-align: center;
        display: block;
        font-weight: 600;
    }
    .btn-logout:hover {
        background: #e74c3c;
        color: white;
        border-color: #e74c3c;
        text-decoration: none;
    }

    /* ==========================================
     * MOBILE RESPONSIVENESS
     * ==========================================
     */

    /* Toggle Button (Hidden on Desktop) */
    .mobile-toggle {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1001;
        background: #0A2342;
        color: #C8A951;
        border: 1px solid #C8A951;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* Close Button inside Sidebar (Hidden on Desktop) */
    .sidebar-close {
        display: none;
        position: absolute;
        top: 10px;
        right: 15px;
        background: transparent;
        border: none;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
    }

    /* Overlay (Dark background when menu is open) */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        .mobile-toggle {
            display: block; /* Show hamburger button */
        }

        .sidebar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            transform: translateX(-100%); /* Hide sidebar by default */
            width: 260px;
            box-shadow: 5px 0 15px rgba(0,0,0,0.3);
        }

        /* Class to slide sidebar in */
        .sidebar-wrapper.active {
            transform: translateX(0);
        }

        .sidebar-close {
            display: block; /* Show close button inside sidebar */
        }

        /* Show overlay when sidebar is active */
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Reduce brand size slightly on mobile */
        .sidebar-brand { padding: 15px; }
        .brand-text { font-size: 20px; }
    }
</style>

{{-- 1. MOBILE TOGGLE BUTTON (Appears on small screens) --}}
<button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="fa fa-bars"></i>
</button>

{{-- 2. OVERLAY (Click to close sidebar) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- 3. SIDEBAR WRAPPER --}}
<div class="main-sidebar sidebar-wrapper" id="mainSidebar">

    {{-- Close Button for Mobile --}}
    <button class="sidebar-close" onclick="toggleSidebar()">
        <i class="fa fa-times"></i>
    </button>

    <div class="sidebar-brand">
        <img src="{{ asset('storage/images/logo12.png') }}" alt="CMS Logo" style="height: 60px; width: auto; object-fit: contain;">
        <span class="brand-text">CMS</span>
        <span class="brand-sub">Giga Mall Systems</span>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            {{-- UPDATED: Added ?-> to prevent crashes for guests --}}
            {{ substr(Auth::user()?->fullname ?? 'U', 0, 1) }}
        </div>
        <div class="user-info">
            {{-- UPDATED: Added ?-> to prevent crashes for guests --}}
            <span>{{ Auth::user()?->fullname ?? 'Guest User' }}</span>
            <small>
                {{-- UPDATED: Cleaned up role checks --}}
                @if(auth()->user()?->user_role == 1)
                    <i class="fa fa-shield" title="Admin"></i> Administrator
                @elseif(auth()->user()?->user_role == 2)
                    <i class="fa fa-user-tag" title="Employee"></i> Employee
                @else
                    <i class="fa fa-user" title="Guest"></i> Guest
                @endif
            </small>
        </div>
    </div>

    <ul class="sidebar-menu">
        {{-- TASKS --}}
        <li class="{{ Request::is('tasks*') || Request::is('/') ? 'active' : '' }}">
            <a href="{{ route('tasks.index') }}" class="sidebar-link">
                <i class="fa fa-tasks"></i>
                <span>Tasks Board</span>
            </a>
        </li>

        {{-- ADMIN LINKS --}}
        @if(auth()->user()?->user_role == 1)
            <li class="{{ Request::is('admin/manage-admin*') || Request::is('admin/manage-users*') ? 'active' : '' }}">
                <a href="{{ route('admin.manage') }}" class="sidebar-link">
                    <i class="fa fa-cogs"></i>
                    <span>Manage Admin</span>
                </a>
            </li>
        @endif

        {{-- REPORTS --}}
        @if(auth()->user()?->user_role == 1 || auth()->user()?->user_role == 2)
            <li class="{{ Request::is('admin/reports*') ? 'active' : '' }}">
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link">
                    <i class="fa fa-bar-chart"></i>
                    <span>System Reports</span>
                </a>
            </li>
        @endif

        {{-- EMPLOYEE PROFILE --}}
        @if(auth()->user()?->user_role == 2)
            <li class="{{ Request::routeIs('profile.show') ? 'active' : '' }}">
                <a href="{{ route('profile.show') }}" class="sidebar-link">
                    <i class="fa fa-id-card"></i>
                    <span>My Profile</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        {{-- LOGOUT FIX: Form explicitly defined with POST and @csrf --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-logout">
                <i class="fa fa-sign-out"></i> Logout
            </button>
        </form>
    </div>

</div>

{{-- 4. JAVASCRIPT FOR TOGGLING --}}
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
</script>
