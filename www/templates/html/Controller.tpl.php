<?php
UNL_Templates::setCachingService(new UNL_Templates_CachingService_Null());
UNL_Templates::$options['version']        = 3.1;
UNL_Templates::$options['sharedcodepath'] = dirname(__FILE__).'/sharedcode';

$url     = UNL_UndergraduateBulletin_Controller::getURL();
$baseURL = UNL_UndergraduateBulletin_Controller::getBaseURL();
$protocolAgnosticBaseURL = str_replace('http://', '//', $baseURL);

$page    = UNL_Templates::factory('Local');

$page->doctitle     = '<title>Undergraduate Bulletin | University of Nebraska-Lincoln</title>';
$page->titlegraphic = 'Undergraduate Bulletin '.UNL_UndergraduateBulletin_Controller::getEdition()->getRange();
$page->pagetitle     = '<h1>Your Undergraduate Bulletin</h1>';
$page->breadcrumbs  = '
<ul>
    <li><a href="http://www.unl.edu/">UNL</a></li>
    <li>Undergraduate Bulletin</li>
</ul>
';

$page->navlinks = $savvy->render(null, 'Navigation.tpl.php');

$page->loadSharedCodeFiles();
$page->addStylesheet('/wdn/templates_3.1/css/content/notice.css');
$page->addStylesheet('/wdn/templates_3.1/css/content/grid-v3.css');
$page->addStylesheet($protocolAgnosticBaseURL. 'templates/html/css/jquery.qtip.css');
if (UNL_UndergraduateBulletin_OutputController::getCacheInterface() instanceof UNL_UndergraduateBulletin_CacheInterface_Mock) {
    $page->addStylesheet($protocolAgnosticBaseURL. 'templates/html/css/debug.css');
} else {
    $page->addStylesheet($protocolAgnosticBaseURL. 'templates/html/css/all.css');
}
$page->addStyleSheet($protocolAgnosticBaseURL . 'templates/html/css/print.css', 'print');

$page->head .= '
<script type="text/javascript">
    var UNL_UGB_URL = "'.$url.'";
    var UNL_UGB_BASEURL = "'.$baseURL.'";
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script src="'.$protocolAgnosticBaseURL.'templates/html/scripts/jQuery.toc.min.js"></script>
<script src="'.$protocolAgnosticBaseURL.'templates/html/scripts/jquery.qtip.min.js"></script>
<script src="/wdn/templates_3.1/scripts/plugins/ui/jQuery.ui.js"></script>
<script src="'.$protocolAgnosticBaseURL.'templates/html/scripts/bulletin.functions.js"></script>
<!-- '.md5($context->getRawObject()->getCacheKey()).' -->
';

// Check if the year of this edition indicates it has not been published
if (mktime(0, 0, 0, 6, 1, $context->getEdition()->year) > time() ) {
    $page->head .= <<<UNPUBLISHED
    <meta name="robots" content="noindex" />
    <script type="text/javascript">
    //<![CDATA[
    WDN.loadJQuery(function() {
        WDN.jQuery(document).ready( function() {
            WDN.jQuery('#wdn_wrapper').before('<div id="testIndicator"></div>');
        });
    });
    //]]>
    </script>
UNPUBLISHED;
}

$page->maincontentarea = '';
$page->maincontentarea .= $savvy->render($context->output);

$page->maincontentarea .= $savvy->render($context, 'EditionNotice.tpl.php');

echo $page;
