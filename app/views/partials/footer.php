<footer class="footer">
    <div class="footer-stage">
        <div class="footer-infobox">
            <nav class="footermenu">
                <div class="menu-footermenu-container">
                    <ul id="menu-footermenu" class="menu">
                        <li class="menu-item">
                            <a href="/partner/">Partner</a>
                        </li>
                        <li class="menu-item">
                            <a href="/kontakt/">Kontakt</a>
                        </li>
                        <li class="menu-item">
                            <a href="/impressum/">Impressum</a>
                        </li>
                        <li class="menu-item">
                            <a href="/datenschutz/">Datenschutz</a>
                        </li>
                        <li class="menu-item">
                            <a href="/ueber-uns/">Über uns</a>
                        </li>
                        <li class="menu-item">
                            <a href="//grunoaph.net/4/5825064" target="_blank">Unterstützen</a>
                        </li>

                    </ul>
                </div>
            </nav>
        </div>
        <div class="footer-text">
            <p>&copy; <?php echo date('Y') ?> - lachlesegeschichten.de</p>
        </div>
    </div>

</footer>

<div class="menu-mobile">
    <?php partial('mainmenu', $data); ?>
</div>

<?php partial('cookie-box', $data); ?>

<script async defer src="/app/public/js/fawesome.js?ver=<?= getVersion() ?>"></script>
<script type="text/javascript" src="/app/public/js/vendor.js?ver=<?= getVersion() ?>"></script>
<script type="text/javascript" src="/app/public/js/app.js?ver=<?= getVersion() ?>"></script>
<script>require('src/js/app')</script>