<?php partial('head', $data); ?>
    <body>
    <?php partial('facebook', $data); ?>
    <div class="post post-<?= $data->id ?>">
        <?php partial('header', $data); ?>
        <?php // partial('subheader', $data); ?>
        <?php //partial('ads/lv-display-hp'); ?>
        <main class="main">
            <div class="main-stage">


                <div class="article-wrapper">
                    <?php partial('jsonld/article', $data); ?>
                    <article class="article story" data-id="<?= $data->id ?>">
                        <div class="article-stage">

                            <ins class="adsbygoogle"
                                 style="display:block; text-align:center;"
                                 data-ad-layout="in-article"
                                 data-ad-format="fluid"
                                 data-ad-client="ca-pub-5632762269034234"
                                 data-ad-slot="8132054894"></ins>
                            <br/>

                            <?php partial('breadcrumb', $data); ?>
                            <?php if (isset($data->subtitle) && !empty($data->subtitle)): ?>
                                <p class="article-subtitle"><?= $data->subtitle ?></p>
                            <?php endif ?>
                            <h1 class="article-title"><?= html_entity_decode($data->title) ?></h1>

                            <?php if (!isset($data->show_image) || true === $data->show_image): ?>
                                <header class="article-header">
                                    <?php if (isset($data->image) && !empty($data->image)): ?>
                                        <figure class="article-image-figure">
                                            <div class="article-image-wrapper">
                                                <img
                                                        class="article-image lazy"
                                                        src="<?= $data->image ?>"
                                                        data-src="<?= $data->image ?>"
                                                        data-srcset="<?= $data->image ?> 100vw"
                                                        width="880"
                                                        height="495"
                                                        alt="<?= encode($data->title) ?>"/>

                                                <?php /* if (isset($data->image_title)): ?>
                                                    <div class="image-title large">
                                                        <div class="image-title-txt">
                                                            <?= $data->image_title ?>
                                                        </div>
                                                    </div>
                                                <?php endif */ ?>

                                            </div>
                                            <?php if (isset($data->image_caption) && !empty($data->image_caption)): ?>
                                                <figcaption class="article-image-figcaption">
                                                    <?= $data->image_caption ?>
                                                </figcaption>
                                            <?php endif; ?>
                                        </figure>
                                    <?php endif ?>
                                </header>
                            <?php endif ?>

                            <div class="article-meta-bar">
                                <div class="article-meta">
                                    <span class="article-meta__column article-meta__reading-time">
                                        <span class="article-meta__label"><i class="fas fa-calendar"></i></span>
                                        <span class="article-meta__text">Veröffentlicht: <time
                                                    datetime="<?= $data->created ?>"><?= $data->created_format ?></time></span>
                                    </span>
                                    <span class="article-meta__column article-meta__reading-time">
                                        <span class="article-meta__label"><i class="fas fa-calendar"></i></span>
                                        <span class="article-meta__text">Aktualisiert: <time
                                                    datetime="<?= $data->modified ?>"><?= $data->modified_format ?></time></span>
                                    </span>
                                    <span class="article-meta__column article-meta__words" title="Anzahl Wörter">
                                        <span class="article-meta__label"><i class="fas fa-font"></i></span>
                                        <span class="article-meta__text">Wörter: <?= $data->words ?></span>
                                    </span>
                                    <span class="article-meta__column article-meta__reading-time" title="Lesezeit">
                                        <span class="article-meta__label"><i class="fas fa-clock"></i></span>
                                        <span class="article-meta__text">Lesezeit: ca. <?= $data->readingTime ?></span>
                                    </span>

                                    <?php if (isset($data->tags) && !empty($data->tags)): ?>
                                        <div class="tags">
                                            <ul class="list list--tags">
                                                <?php foreach ($data->tags as $tag): ?>
                                                    <?php $tag = (object)$tag; ?>
                                                    <li class="list-item"><a
                                                                href="<?= $tag->link ?>">#<?= $tag->title ?></a></li>
                                                <?php endforeach ?>
                                            </ul>
                                        </div>
                                    <?php endif ?>
                                </div>

                                <!--
                                <div class="article-votes-wrapper">
                                    <div class="article-votes" data-component="PostVote" data-param='<?= json_encode($data->votes) ?>' data-post-id="<?= $data->id ?>" data-url="/ajax/vote/<?= $data->id ?>">
                                        <div class="article-votes__body">
                                            <button class="button-vote-up" data-vote-up title="Mag ich">
                                                <i class="fa fa-heart"></i> <span data-votes-up><?= $data->votes->up ?></span>
                                            </button>
                                        </div>
                                        <div class="article-votes__footer">
                                            <div class="article-votes-bar" data-votes-bar></div>
                                        </div>
                                    </div>
                                </div>
                                -->


                                <?php // partial('list-socials-compact', $data) ?>
                            </div>

                            <?php // partial('/article-socials', $data) ?>

                            <div class="article-body" data-component="Article">
                                <div class="article-content entry-content">


                                    <?php // partial('ads/in-article'); ?>
                                    <?php // partial('paywall') ?>

                                    <?php post($data, $data); ?>

                                    <br/><br/>

                                    <div class="werb-lazy"></div>

                                    <script async src="https://cdn.jsdelivr.net/npm/lazyhtml@1.2.3/dist/lazyhtml.min.js"
                                            crossorigin="anonymous"></script>

                                    <div class="lazyhtml" data-lazyhtml onvisible>
                                        <script type="text/lazyhtml">
                                            <!--
                                                <ins class="adsbygoogle"
                                                 style="display:block"
                                                 data-ad-format="autorelaxed"
                                                 data-ad-client="ca-pub-5632762269034234"
                                                 data-ad-slot="8012684678"
                                                 data-full-width-responsive="true"></ins>
                                                <script>
                                                     (adsbygoogle = window.adsbygoogle || []).push({});



                                        </script>
                                        -->
                                        </script>
                                    </div>

                                    <div class="article-votes-wrapper">
                                        <div class="article-votes" data-component="PostVote"
                                             data-param='<?= json_encode($data->votes) ?>'
                                             data-post-id="<?= $data->id ?>" data-url="/ajax/vote/<?= $data->id ?>">
                                            <div class="article-votes-header">
                                                <h3>Dieser Artikel ist witzig</h3>
                                            </div>
                                            <div class="article-votes-body">
                                                <button class="button-vote-up" data-vote-up title="Das ist lustig">
                                                    <i class="fa fa-thumbs-up"></i> <span
                                                            data-votes-up><?= $data->votes->up ?></span>
                                                </button>
                                                <button class="button-vote-down" data-vote-down
                                                        title="Das ist nicht lustig">
                                                    <i class="fa fa-thumbs-down"></i> <span
                                                            data-votes-down><?= $data->votes->down ?></span>
                                                </button>
                                            </div>
                                            <div class="article-votes-footer">
                                                <div class="article-votes-bar" data-votes-bar></div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php partial('/article-socials', $data) ?>
                                </div>

                                <aside class="article-sidebar">
                                    <?php // partial('article-sidebar', $data) ?>
                                </aside>
                            </div>


                            <?php partial('related-posts', $data) ?>

                            <?php if (isset($data->authors) && !empty($data->authors)): ?>
                                <h3 class="center">Über den Autor</h3>
                                <?php foreach ($data->authors as $author): ?>
                                    <?php $author = (object)$author ?>
                                    <?php partial('article-author', $author) ?>
                                <?php endforeach; ?>
                            <?php endif ?>

                            <footer class="article-footer">
                                <?php partial('recommended-posts', $data) ?>
                            </footer>

                        </div>

                    </article>
                </div>


                <?php // partial('sections/gender'); ?>
                <?php // partial('sections/absolut-sinnlos', []); ?>
                <?php // partial('sections/ratgeber', $data) ?>

            </div>
        </main>
        <?php partial('footer', $data); ?>
    </div>
    </body>
<?php partial('foot', $data); ?>