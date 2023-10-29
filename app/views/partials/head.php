<!doctype html>
<html lang="de">
<head>
    <title><?= getTitle($data) ?> | lachlesegeschichten.de</title>
    <?php /* ?>
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
		window.googletag = window.googletag || {cmd: []};
		googletag.cmd.push(function() {
			googletag.defineSlot('/22074192382/lachvegas_300x250_sidebar', [[300, 31], [300, 50], [300, 100], [300, 75], [300, 600], [300, 250]], 'div-gpt-ad-1596467522327-0').addService(googletag.pubads());
			
			googletag.defineSlot('/22074192382/test', [250, 250], 'div-gpt-ad-1596544590894-0').addService(googletag.pubads());
			
			googletag.defineSlot('/22074192382/lachvegas_top', [728, 90], 'div-gpt-ad-1596546237124-0').addService(googletag.pubads());
			
			googletag.defineSlot('/22074192382/lachvegas_bottom', [728, 90], 'div-gpt-ad-1596546289442-0').addService(googletag.pubads());
			
			googletag.pubads().enableSingleRequest();
			//googletag.pubads().collapseEmptyDivs();
			googletag.enableServices();
		});
    </script>
    <?php */ ?>
    <?php //if (!DEBUG_MODE): ?>
    <?php /* ?>
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
		window.googletag = window.googletag || {cmd: []};
		googletag.cmd.push(function() {
			googletag.defineSlot('/22074192382/lachvegas_300x250_sidebar', [[300, 31], [300, 50], [300, 100], [300, 75], [300, 600], [300, 250]], 'div-gpt-ad-1596467522327-0').addService(googletag.pubads());
			googletag.pubads().enableSingleRequest();
			//googletag.pubads().collapseEmptyDivs();
			googletag.enableServices();
		});
    </script>
    <?php //endif ?>
 <?php */ ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <meta name="robots" content="<?= $data->robots ?? 'index, follow' ?>"/>
    <meta name="locale" content="de_DE"/>
    <meta name="description" content="<?= getDescription($data) ?>"/>
    <meta name="keywords" content="<?= getKeywords($data) ?>"/>
    <?php /* pinterest domain verification*/ ?>
    <meta name="p:domain_verify" content="571f66866ffeafd18f545a47451fff9d"/>
    <?php /* ?><meta name="propeller" content="c2b860295249a506dff76955ae90f448" /><?php */ ?>
    <link rel="canonical" href="<?= getUrl($data) ?>">
    <meta property="og:locale" content="de_DE"/>
    <meta property="og:type" content="<?= isHome() ? 'website' : 'article' ?>"/>
    <meta property="og:title" content="<?= getTitle($data) ?>"/>
    <meta property="og:description" content="<?= getDescription($data) ?>"/>
    <meta property="og:url" content="<?= getUrl($data) ?>"/>
    <meta property="og:site_name" content="lachlesegeschichten.de"/>
    <meta property="fb:app_id" content="430709494322673"/>

    <meta name="twitter:card" content="summary"/>
    <meta name="twitter:site" content="@lachlesegeschichten"/>
    <meta name="twitter:title" content="<?= getTitle($data) ?>"/>
    <meta name="twitter:description" content="<?= getDescription($data) ?>"/>

    <?php if (isHome() || isCategory($data)): ?>
        <meta name="twitter:image" content="<?= DOMAIN ?>/app/public/img/lachvegas-logo-16-9.png"/>
        <meta property="og:image" content="<?= DOMAIN ?>/app/public/img/lachvegas-logo-16-9.png"/>
        <meta property="og:image:type" content="image/png"/>
        <meta property="og:image:width" content="1000"/>
        <meta property="og:image:height" content="563"/>
    <?php endif ?>

    <?php if ($url = getImage($data)): ?>
        <meta name="twitter:image" content="<?= $url ?>"/>
        <meta property="og:image" content="<?= $url ?>"/>
        <meta property="og:image:type" content="image/png"/>
        <meta property="og:image:width" content="1920"/>
        <meta property="og:image:height" content="1080"/>
    <?php endif ?>


    <?php partial('jsonld', $data); ?>
    <?php //partial('jsonld/home', $data); ?>
    <?php //partial('jsonld/category', $data); ?>

    <?php /* ?>
    <script type="application/ld+json">
    [
        {
            "@context":"http://schema.org",
            "@type":"WebPage",
            "headline":"Politik - DER SPIEGEL",
            "mainEntityOfPage":"https://www.spiegel.de/politik/",
            "name":"Politik - DER SPIEGEL",
            "publisher":{
                "@type":"Organization",
                "logo":"https://www.spiegel.de/public/spon/images/logos/der-spiegel-h60.png",
                "name":"DER SPIEGEL",
                "sameAs":[
                    "https://www.facebook.com/derspiegel",
                    "https://www.instagram.com/spiegelmagazin/",
                    "https://twitter.com/derspiegel"
                ]
            },
            "url":"https://www.spiegel.de/politik/"
        }
    ]
    </script>
 <?php */ ?>

    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="stylesheet" type="text/css" media="all"
          href="/app/public/css/app.css?ver=<?= getVersion() ?>"/>
    <link rel="shortcut icon" type="image/png" href="/app/public/img/eyes.png"/>
    <?php /* ?> <script src="https://gloumsee.net/pfe/current/tag.min.js?z=3492936" data-cfasync="false" async></script> <?php */ ?>

    <?php /* ?>
    <style>@import url('https://fonts.googleapis.com/css?family=Montserrat:100,300,400,500,600,700,800,900|Indie+Flower');</style>
    <?php */ ?>
    <?php partial('ads/google-ad', $data); ?>

</head>