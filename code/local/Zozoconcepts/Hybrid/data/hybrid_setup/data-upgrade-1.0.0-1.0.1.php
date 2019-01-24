<?php
try{
    /* @var $installer Mage_Catalog_Model_Resource_Setup */
    $installer = $this;

    $installer->startSetup();

    // ------------------------------------------------------
    // --- Create and enable Sample Home Page ---------------
    // ------------------------------------------------------

    $content=<<<EOF
<!-- Carousel ================================================== -->
<div class="carousel slide" id="myCarousel">
    <!-- Indicators -->

    <ol class="carousel-indicators">
        <li class="active" data-slide-to="0" data-target="#myCarousel"></li>

        <li data-slide-to="1" data-target="#myCarousel"></li>

        <li data-slide-to="2" data-target="#myCarousel"></li>
    </ol>

    <div class="carousel-inner">
        <div class="item active">
            <img alt="First slide" src="https://placeholdit.imgix.net/~text?txtsize=33&txt=1343%C3%97400&w=1343&h=400">

            <div class="container">
                <div class="carousel-caption">
                    <h1>Headline</h1>

                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget
                    quam. Donec id elit non mi porta gravida at eget metus.
                    Nullam id dolor id nibh ultricies vehicula ut id elit.</p>

                    <p><a class="btn btn-large btn-primary" href="#">Learn
                    more</a></p>
                </div>
            </div>
        </div>

        <div class="item">
            <img alt="Second slide" src="https://placeholdit.imgix.net/~text?txtsize=33&txt=1343%C3%97400&w=1343&h=400">

            <div class="container">
                <div class="carousel-caption">
                    <h1>Another example headline.</h1>

                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget
                    quam. Donec id elit non mi porta gravida at eget metus.
                    Nullam id dolor id nibh ultricies vehicula ut id elit.</p>

                    <p><a class="btn btn-large btn-primary" href="#">Learn
                    more</a></p>
                </div>
            </div>
        </div>

        <div class="item">
            <img alt="Third slide" src="https://placeholdit.imgix.net/~text?txtsize=33&txt=1343%C3%97400&w=1343&h=400">

            <div class="container">
                <div class="carousel-caption">
                    <h1>One more for good measure.</h1>

                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget
                    quam. Donec id elit non mi porta gravida at eget metus.
                    Nullam id dolor id nibh ultricies vehicula ut id elit.</p>

                    <p><a class="btn btn-large btn-primary" href="#">Browse
                    gallery</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.carousel -->
<p>&nbsp;</p>
<!-- Marketing messaging and featurettes ================================================== -->
<div class="container marketing">
    <p>{{widget type="productslider/widget" title="New Arrivals" max_items="10" order="random" attributes="name" slider="jcarousel"}}</p>

    <p>&nbsp;</p>
    <!-- START THE FEATURETTES -->
    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading">First featurette heading.
            <span class="text-muted">It'll blow your mind.</span></h2>

            <p class="lead">Donec ullamcorper nulla non metus auctor fringilla.
            Vestibulum id ligula porta felis euismod semper. Praesent commodo
            cursus magna, vel scelerisque nisl consectetur. Fusce dapibus,
            tellus ac cursus commodo.</p>
        </div>

        <div class="col-md-5"><img alt="Generic placeholder image" class=
        "featurette-image img-responsive" data-src="holder.js/500x500/auto"
        src="data:image/png;base64,"></div>
    </div>
    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-5"><img alt="Generic placeholder image" class=
        "featurette-image img-responsive" data-src="holder.js/500x500/auto"
        src="data:image/png;base64,"></div>

        <div class="col-md-7">
            <h2 class="featurette-heading">Oh yeah, it's that good.
            <span class="text-muted">See for yourself.</span></h2>

            <p class="lead">Donec ullamcorper nulla non metus auctor fringilla.
            Vestibulum id ligula porta felis euismod semper. Praesent commodo
            cursus magna, vel scelerisque nisl consectetur. Fusce dapibus,
            tellus ac cursus commodo.</p>
        </div>
    </div>
    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading">And lastly, this one. <span class=
            "text-muted">Checkmate.</span></h2>

            <p class="lead">Donec ullamcorper nulla non metus auctor fringilla.
            Vestibulum id ligula porta felis euismod semper. Praesent commodo
            cursus magna, vel scelerisque nisl consectetur. Fusce dapibus,
            tellus ac cursus commodo.</p>
        </div>

        <div class="col-md-5"><img alt="Generic placeholder image" class=
        "featurette-image img-responsive" data-src="holder.js/500x500/auto"
        src="data:image/png;base64,"></div>
    </div>
    <hr class="featurette-divider">
</div>
<!-- /END THE FEATURETTES -->
EOF;

    $cmsPageData = array(
        'title'             => 'Home Page',
        'root_template'     => 'one_column',
        'identifier'        => 'home-page',
        'stores'            => array(0), //available for all store views
        'is_active'         => 1,
        'content'           => $content,
    );

    $cmsPage = Mage::getModel('cms/page')->setData($cmsPageData)->save();

    $config = new Mage_Core_Model_Config();
    $config->saveConfig('web/default/cms_home_page', 'home-page');

    $installer->endSetup();
}catch(Exception $e){
    Mage::logException($e);
}
