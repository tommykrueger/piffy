<script type="application/ld+json">
    {
        "@context":"https://schema.org",
        "@graph":[
            {
                "@type":"WebSite",
                "@id":"<?= DOMAIN ?>/#website",
            "url":"<?= DOMAIN ?>/",
            "name":"lachvegas.de",
            "description": "Die Webseite für derben Humor, lustige Quatschgeschichten und absoluten Unfug.",
            "inLanguage":"de-DE",
            "potentialAction": [
                {
                    "@type":"SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "<?= DOMAIN ?>/suche/?s={search_term_string}"
                    },
                    "query-input":"required name=search_term_string"
                }
            ]
        }
    <?php if (isArticle($data)): ?>
        ,{
            "@type":"WebPage",
            "@id":"<?= DOMAIN ?>/#webpage",
            "url":"<?= DOMAIN ?>/",
            "name":"lachvegas.de | Die Webseite f\u00fcr derben Humor, lustige Quatschgeschichten und absolut sinnlosen Unfug",
            "description":"Die Webseite f\u00fcr derben Humor, lustige Quatschgeschichten und absolut sinnlosen Unfug",
            "isPartOf":{
                "@id":"<?= DOMAIN ?>/#website"
            },
            "inLanguage":"de-DE",
            "primaryImageOfPage":{
                "@id":"<?= DOMAIN ?>/#primaryimage"
            },
            "datePublished":"<?= date('c', strtotime($data->modified ?? $data->created)) ?>",
            "dateModified":"<?= date('c', strtotime($data->modified ?? $data->created)) ?>",
            "breadcrumb": {
                "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#breadcrumb"
            },
            "potentialAction": {
                "@type": "ReadAction",
                "target": [
                    "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>"
                ]
            },
            "author": {
                "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#/schema/person/733db0acb47dab4c61638a77f9feeace"
            }
        }
<?php endif ?>
    ,{
        "@type": "Person",
        "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#/schema/person/733db0acb47dab4c61638a77f9feeace",
            "name": "tommytestus",
            "image": {
                "@type": "ImageObject",
                "inLanguage": "de-DE",
                "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#/schema/person/image/",
                "url": "https://secure.gravatar.com/avatar/e6a10083f08e13410dd3a34208f3e0fe?s=96&d=mm&r=g",
                "contentUrl": "https://secure.gravatar.com/avatar/e6a10083f08e13410dd3a34208f3e0fe?s=96&d=mm&r=g",
                "caption": "tommytestus"
            },
            "url": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>author/tommytestus/"
        },
        {
            "@type":"Organization",
            "name":"lachvegas.de",
            "sameAs":[
                "https://www.facebook.com/lachvegas.de/",
                "https://twitter.com/lachvegas/",
                "https://www.pinterest.com/lachvegas/"
            ]
        }

    <?php if (isset($data->breadcrumb)): ?>
        ,{
            "@context": "https://schema.org",
            "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#breadcrumb",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "<?= DOMAIN ?>/"
            }
            <?php $i = 2 ?>
        <?php foreach ($data->breadcrumb as $item): ?>
            <?php if (isset($item->url)): ?>
            ,{
                "@type": "ListItem",
                "position": <?= $i ?>,
                "name": "<?= htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8', false); ?>",
                "item": "<?= $item->url ?>"
            }<?php endif ?><?php if ($i <= count($data->breadcrumb)): ?><?php endif ?>
            <?php $i++ ?>
        <?php endforeach ?>
           ]
        }
<?php endif ?>


<?php if (isCategory($data)): ?>
        ,{
            "@type":"WebPage",
            "headline":"<?= encode($data->name) ?>",
            "mainEntityOfPage":"<?= getCategoryUrl($data->id, true) ?>",
            "name":"<?= encode($data->name) ?>",
            "url":"<?= getCategoryUrl($data->id, true) ?>"
        }
<?php endif ?>
    ]
}
</script>


<?php if (isArticle($data)): ?>

    <?php

    $domainImg = DOMAIN . '/app/public/img/posts/';
    $keywords = false;
    if (isset($data->tags) && !empty($data->tags)) {
        foreach ($data->tags as $tag) {
            $keywords .= $tag->title . ' ';
        }
    }

    $data->title = htmlspecialchars($data->title, ENT_QUOTES, 'UTF-8', false);

    ob_start();
    post($data->id);
    $data->body = ob_get_clean();
    $data->body = htmlspecialchars($data->body, ENT_QUOTES, 'UTF-8', false);

    ?>


    <script type="application/ld+json">
        {
            "@context":"http://schema.org",
            "@type":"Article",
            "url":"<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>",
    "mainEntityOfPage": {
        "@type":"WebPage",
        "@id":"https://google.com/article",
        "url" : "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>"
    },
    "headline":"<?= htmlentities($data->seo_title ?? $data->title) ?>",
    "dateCreated":"<?= date('c', strtotime($data->created)) ?>",
    "datePublished":"<?= date('c', strtotime($data->modified)) ?>",
    "dateModified":"<?= date('c', strtotime($data->modified)) ?>",
    "publisher": {
        "@type":"Organization",
        "name":"lachvegas.de",
        "logo":{
            "@type":"ImageObject",
            "url":"<?= DOMAIN ?>/app/public/img/lachvegas-logo-square.png",
            "width":"512",
            "height":"512"
        }
    },
        <?php if (!empty($data->image)): ?>
    "image": {
        "@type":"ImageObject",
        "@id": "<?= DOMAIN . (is_array($data->slug) ? $data->slug[0] : $data->slug) ?>#primaryimage",
        "url":"<?= $data->image ?>",
        "width":"640",
        "height":"640",
        "inLanguage": "de-DE",
        "caption": "<?= $data->image ?? $data->excerpt ?>"
    },
    <?php endif ?>
        "author": {
            "@type":"Person",
            "name":"Tommy Krüger"
        },
        <?php if (isset($data->words)): ?>
    "wordCount": "<?= $data->words ?>",
    <?php endif ?>
    <?php if ($keywords): ?>
    "keywords":"<?= htmlentities(trim($keywords)) ?>",
    <?php endif ?>
    <?php if (isset($data->excerpt)): ?>
    "description":"<?= htmlentities(htmlspecialchars($data->excerpt)) ?>",
    <?php endif ?>
    <?php if (isset($data->body) && !empty($data->body)): ?>
    "articleBody":"<?= $data->body ?>"
    <?php endif ?>
        }

    </script>

<?php endif ?>