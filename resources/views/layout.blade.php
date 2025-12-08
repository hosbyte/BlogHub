<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'BlogHub') | سیستم مدیریت وبلاگ چندکاربره</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&family=Samim&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <!-- اسکرول به بالا -->
    <button id="scroll-to-top" class="scroll-top-btn">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <!-- لوگو -->
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-blog"></i>
                <span>BlogHub</span>
            </a>

            <!-- منوی اصلی -->
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> صفحه اصلی
                    </a></li>

                <li class="nav-dropdown">
                    <a href="#" class="nav-link">
                        <i class="fas fa-th-large"></i> دسته‌بندی‌ها
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- دسته‌بندی‌ها با JavaScript لود می‌شوند -->
                        <li><a href="#" class="loading-categories">در حال بارگذاری...</a></li>
                    </ul>
                </li>

                <li><a href="{{ route('posts.popular') }}" class="nav-link">
                        <i class="fas fa-fire"></i> پربازدیدها
                    </a></li>

                <li><a href="{{ route('about') }}" class="nav-link">
                        <i class="fas fa-info-circle"></i> درباره ما
                    </a></li>
            </ul>

            <!-- بخش جستجو -->
            <div class="search-container">
                <form action="{{ route('search') }}" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="جستجو مقالات..." class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- بخش کاربر -->
            <div class="user-section">
                @auth
                    <div class="user-dropdown">
                        <button class="user-btn">
                            @if (Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="پروفایل"
                                    class="user-avatar">
                            @else
                                <div class="avatar-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <div class="user-dropdown-menu">
                            @if (Auth::user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                    <i class="fas fa-tachometer-alt"></i> پنل مدیریت
                                </a>
                                <hr class="dropdown-divider">
                            @elseif(Auth::user()->hasRole('author'))
                                <a href="{{ route('author.dashboard') }}" class="dropdown-item">
                                    <i class="fas fa-edit"></i> پنل نویسنده
                                </a>
                                <hr class="dropdown-divider">
                            @endif

                            <a href="{{ route('user.dashboard') }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i> پنل کاربری
                            </a>
                            <a href="{{ route('user.posts') }}" class="dropdown-item">
                                <i class="fas fa-newspaper"></i> مقالات من
                            </a>
                            <a href="{{ route('user.profile') }}" class="dropdown-item">
                                <i class="fas fa-cog"></i> تنظیمات پروفایل
                            </a>
                            <hr class="dropdown-divider">
                            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <i class="fas fa-sign-out-alt"></i> خروج
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="btn btn-outline">ورود</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">ثبت‌نام</a>
                    </div>
                @endauth
            </div>

            <!-- منوی موبایل -->
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <button class="close-mobile-menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mobile-nav">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> صفحه اصلی</a></li>
            <li><a href="{{ route('categories.index') }}"><i class="fas fa-th-large"></i> دسته‌بندی‌ها</a></li>
            <li><a href="{{ route('posts.popular') }}"><i class="fas fa-fire"></i> پربازدیدها</a></li>
            <li><a href="{{ route('about') }}"><i class="fas fa-info-circle"></i> درباره ما</a></li>

            @auth
                <li class="mobile-user-info">
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="پروفایل">
                    @else
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <span>{{ Auth::user()->name }}</span>
                </li>

                @if (Auth::user()->hasRole('admin'))
                    <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> پنل مدیریت</a>
                    </li>
                @elseif(Auth::user()->hasRole('author'))
                    <li><a href="{{ route('author.dashboard') }}"><i class="fas fa-edit"></i> پنل نویسنده</a></li>
                @endif

                <li><a href="{{ route('user.dashboard') }}"><i class="fas fa-user-circle"></i> پنل کاربری</a></li>
                <li><a href="{{ route('user.posts') }}"><i class="fas fa-newspaper"></i> مقالات من</a></li>
                <li><a href="{{ route('user.profile') }}"><i class="fas fa-cog"></i> تنظیمات پروفایل</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="mobile-logout">
                            <i class="fas fa-sign-out-alt"></i> خروج
                        </button>
                    </form>
                </li>
            @else
                <li class="mobile-auth-buttons">
                    <a href="{{ route('login') }}" class="btn btn-outline">ورود</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">ثبت‌نام</a>
                </li>
            @endauth
        </ul>
    </div>

    <!-- محتوای اصلی -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- درباره وبلاگ -->
                <div class="footer-section">
                    <h3 class="footer-title">
                        <i class="fas fa-blog"></i> BlogHub
                    </h3>
                    <p class="footer-description">
                        سیستم مدیریت وبلاگ چندکاربره با قابلیت‌های پیشرفته برای نویسندگان و مدیران محتوا
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                    </div>
                </div>

                <!-- لینک‌های سریع -->
                <div class="footer-section">
                    <h3 class="footer-title">لینک‌های سریع</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}"><i class="fas fa-chevron-left"></i> صفحه اصلی</a></li>
                        <li><a href="{{ route('posts.popular') }}"><i class="fas fa-chevron-left"></i> پربازدیدها</a>
                        </li>
                        <li><a href="{{ route('posts.index') }}"><i class="fas fa-chevron-left"></i> همه مقالات</a>
                        </li>
                        <li><a href="{{ route('authors.index') }}"><i class="fas fa-chevron-left"></i> نویسندگان</a>
                        </li>
                    </ul>
                </div>

                <!-- دسته‌بندی‌ها -->
                <div class="footer-section">
                    <h3 class="footer-title">دسته‌بندی‌ها</h3>
                    <ul class="footer-links category-links">
                        <!-- با JavaScript لود می‌شوند -->
                        <li><a href="#" class="loading">در حال بارگذاری...</a></li>
                    </ul>
                </div>

                <!-- تماس -->
                <div class="footer-section">
                    <h3 class="footer-title">ارتباط با ما</h3>
                    <ul class="footer-contact">
                        <li><i class="fas fa-envelope"></i> support@bloghub.com</li>
                        <li><i class="fas fa-phone"></i> ۰۲۱-۱۲۳۴۵۶۷۸</li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="copyright">
                    <i class="far fa-copyright"></i>
                    کلیه حقوق برای BlogHub محفوظ است. {{ date('Y') }}
                </p>
                <p class="footer-note">
                    ساخته شده با <i class="fas fa-heart"></i> و لاراول
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- Script برای لود دسته‌بندی‌ها -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // لود دسته‌بندی‌ها برای منو و فوتر
            fetchCategories();

            // فعال کردن منوی موبایل
            initMobileMenu();

            // فعال کردن اسکرول به بالا
            initScrollToTop();

            // فعال کردن Dropdown کاربر
            initUserDropdown();
        });

        function fetchCategories() {
            // این تابع بعداً با Ajax کامل می‌شود
            console.log('بارگذاری دسته‌بندی‌ها...');
        }

        function initMobileMenu() {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const closeBtn = document.querySelector('.close-mobile-menu');
            const overlay = document.querySelector('.mobile-menu-overlay');
            const mobileMenu = document.querySelector('.mobile-menu');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.add('active');
                    overlay.classList.add('active');
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }
        }

        function initScrollToTop() {
            const scrollBtn = document.getElementById('scroll-to-top');

            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollBtn.classList.add('visible');
                } else {
                    scrollBtn.classList.remove('visible');
                }
            });

            scrollBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

        function initUserDropdown() {
            const userBtn = document.querySelector('.user-btn');
            const dropdownMenu = document.querySelector('.user-dropdown-menu');

            if (userBtn) {
                userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // بستن Dropdown با کلیک خارج از آن
                document.addEventListener('click', () => {
                    dropdownMenu.classList.remove('show');
                });

                dropdownMenu.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }
        }
    </script>

    @yield('scripts')
</body>

</html>
