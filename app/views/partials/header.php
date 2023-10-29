<header class="header">
    <div class="header-stage">
        <div class="header-logo">
            <a href="/">
                <span class="page-name">
                    <img src="/app/public/img/lachvegas-logo-square.svg" style="width: 36px;" width="36"
                         alt="Lachvegas Logo"/>
                    <span class="page-name-num">Lach</span><span class="page-name-txt">LeseGeschichten</span>
                </span>
            </a>
        </div>

        <div class="header-menu">
            <nav class="mainmenu" data-component="Menu">
                <span class="menu-icon-wrapper" data-toggle>
                    <span class="menu-icon"></span>
                </span>
                <ul class="menu-list" data-menu>
                    <li class="menu-item <?= isPage('') ? 'active' : '' ?>">
                        <a href="/">Home</a>
                    </li>
                </ul>
            </nav>
        </div>

    </div>
</header>
