<!DOCTYPE html>
<html lang="en" class="h-100">
@include('layouts.partials.head')

<body class="h-100">
    <script src="{{ asset('dist') }}/assets/static/js/initTheme.js"></script>
    <div id="app" class="h-100">
        @if(auth()->user()->isAdmin())
            @include('layouts.partials.menu-admin')
        @elseif(auth()->user()->isAset())
            @include('layouts.partials.menu-aset')
        @elseif(auth()->user()->isGA())
            @include('layouts.partials.menu-ga')
        @elseif(auth()->user()->isKabag())
            @include('layouts.partials.menu-kabag')
        @elseif(auth()->user()->isPjDivisi())
            @include('layouts.partials.menu-pjdivisi')
        @endif

        <div id="main">
            <header class="mb-2">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading mb-2">
                @yield('header')
            </div>
            <div class="page-content">
                @yield('content')
            </div>
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('dist') }}/assets/static/js/components/dark.js"></script>
    <script src="{{ asset('dist') }}/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('dist') }}/assets/compiled/js/app.js"></script>
    <script src="{{ asset('dist') }}/assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="{{ asset('dist') }}/assets/static/js/pages/dashboard.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".has-treeview > a").forEach(function(el) {
                el.addEventListener("click", function(e) {
                    e.preventDefault();
                    let parent = this.parentElement;
                    parent.classList.toggle("menu-open"); // Toggle class agar bisa buka/tutup
                    let subMenu = parent.querySelector(".nav-treeview");
                    if (subMenu) {
                        subMenu.style.display = subMenu.style.display === "block" ? "none" : "block";
                    }
                });
            });
        });
    </script>
    
    @stack('js')
</body>
</html>