<?php partial('head', $data); ?>
    <body>
    <?php partial('facebook', $data); ?>
    <?php //partial('ads/google-ad', $data); ?>
    <div class="search search-<?= $data->id ?>">
        <?php partial('header', $data); ?>
        <?php // partial('subheader', $data); ?>
        <main class="main">
            <div class="main-stage">

                <div class="article-wrapper">
                    <section class="category-header">
                        <?php partial('breadcrumb', $data); ?>
                        <?php if (!empty($data->search->query)): ?>
                            <h1><?= count($data->posts ?? []) ?> Ergebnisse für "<?= $data->search->query ?>"</h1>
                        <?php else: ?>
                            <p>Keine Ergebnisse gefunden</p>
                        <?php endif ?>
                    </section>

                    <section class="category-body">
                        <div class="category-posts">
                            <?php if (isset($data->posts)): ?>
                                <?php
                                partial('post-list', [
                                    'posts' => $data->posts,
                                    'start' => 0,
                                    'limit' => 50,
                                    'class_name' => 'post-list-category',
                                    'template' => $data->template ?? null
                                ]);
                                ?>
                            <?php endif ?>
                        </div>
                    </section>

                    <?php partial('sections/gender'); ?>
                    <?php partial('sections/ratgeber', $data) ?>
                </div>
            </div>
        </main>
        <?php partial('footer', $data); ?>
    </div>
    </body>

<?php partial('foot', $data); ?>