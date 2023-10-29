<?php partial('head', $data); ?>
    <body>
    <div class="page page-<?= $data->name ?>">
        <?php partial('header', $data); ?>
        <?php // partial('subheader', $data); ?>
        <main class="main">
            <div class="main-stage">
                <div class="article-wrapper">
                    <article class="article-stage">
                        <?php partial('breadcrumb', $data); ?>
                        <?php partial('jsonld', $data); ?>
                        <div class="article-content entry-content">
                            <?php view('pages/' . $data->name, $data); ?>
                        </div>
                    </article>
                </div>
            </div>
        </main>
        <?php partial('footer', $data); ?>
    </div>
    </body>
<?php partial('foot', $data); ?>