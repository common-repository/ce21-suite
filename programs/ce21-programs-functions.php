<?php
function ce21_get_service_wp_masters_list()
{

        $api_settings = get_ce21_ss_api_settings_data();
        $api_Access_token = (!empty($api_settings)) ?  $api_settings->AccessToken : '';
        $base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
        $url = $base_url."/wp/masters";

        return getApiCall($url,'',$api_Access_token);
}

/*
 * Function used to save API Settings
 * */
add_action('wp_ajax_ce21_save_programs_list_settings','ce21_save_programs_list_settings');
add_action('wp_ajax_nopriv_ce21_save_programs_list_settings','ce21_save_programs_list_settings');
function ce21_save_programs_list_settings()
{
    $topicArea    =   $_POST['topicArea'];
    $tags         =   $_POST['tags'];
    $productTypes =   $_POST['productTypes'];
    $categories   =   $_POST['categories'];
    $page_size    =   $_POST['page_size'];
    $sort_order   =   $_POST['sort_order'];

    $topicAreaShortcode='';
    if( !empty($topicArea) ){
        $topicArea = implode(",",$topicArea);
        $topicAreaShortcode = ' topic-area="'.$topicArea.'"';
    }
    $tagsShortcode='';
    if( !empty($tags) ){
        $tags = implode(",",$tags);
        $tagsShortcode = ' tags="'.$tags.'"';
    }
    $productTypesShortcode='';
    if( !empty($productTypes) ){
        $productTypes = implode(",",$productTypes);
        $productTypesShortcode = ' product-types="'.$productTypes.'"';
    }
    $categoriesShortcode='';
    if( !empty($categories) ){
        $categories = implode(",",$categories);
        $categoriesShortcode = ' categories="'.$categories.'"';
    }

    $sort_orderShortcode='';
    if( !empty($sort_order) ){
        $sort_orderShortcode = ' sort_order="'.$sort_order.'"';
    }

    if( empty($page_size) ){
        $page_size = 25;
    }
    $page_sizeShortcode = ' pagesize="'.$page_size.'"';

    $shortcode = '[ce21_programs_list'.$topicAreaShortcode.''.$tagsShortcode.''.$productTypesShortcode.''.$categoriesShortcode.''.$page_sizeShortcode.''.$sort_orderShortcode.']';

    $response = array(
        'success' => true,
        'shortcode' => $shortcode,
        'message' => 'Shortcode Generated Success fully.'
    );


    if (!empty($response)) {
        echo json_encode($response);
        exit;
    }
}

// Add Shortcode
function ce21_programs_list_shortcode( $atts ) {

    // Attributes
    $atts = shortcode_atts(
        array(
            'topic-area' => '',
            'tags' => '',
            'product-types' => '',
            'categories' => '',
            'pagesize' => '',
            'sort_order' => '',
        ),
        $atts
    );
    ob_start();
    ?>
        <div class="product-list-holder">
            <?php getProgramsList($atts); ?>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'ce21_programs_list', 'ce21_programs_list_shortcode' );

/*
 * Get Programs list
 */
function getProgramsList($dataArray)
{
    $access_token_response = get_access_token();

    if ($access_token_response['success'])
    {
        $api_settings = get_ce21_ss_api_settings_data();
        $api_Access_token = (!empty($api_settings)) ?  $api_settings->AccessToken : '';
        $base_url = (!empty($api_settings)) ? $api_settings->BaseURL : '';
        $url = $base_url."/wp/programlist";
        $newDataArray = array(
            'categories' => $dataArray['categories'],
            'tags' => $dataArray['tags'],
            'productTypes'  => $dataArray['product-types'],
            'topicAreas' => $dataArray['topic-area'],
            'pageSize'  => $dataArray['pagesize'],
            'sortOrder'  => $dataArray['sort_order'],
        );
        $response = getApiCall($url,$newDataArray,$api_Access_token);

        if( is_array($response) ){
            $message = $response['message'];
            $success = $response['success'];
            if(!$success) {
                $messageClassName = 'error';
                echo '<div class="'.esc_attr($messageClassName).'">'.esc_html($message).'</div>';
            }
        }else{
            generateProgramListHtml($response);
        }
    }
}
/*
 * Programs list HTML
 */
function generateProgramListHtml($response)
{
    if( !empty($response->products)){
        foreach ($response->products as $res){
        ?>
            <div class="single-product">
                <div class="top-content-product">
                    <div class="product-image-holder">
                        <a href="<?php echo esc_attr($res->url); ?>">
                            <img src="<?php echo esc_attr($res->image->src); ?>" alt="<?php echo esc_attr($res->image->alt); ?>"/>
                        </a>
                        <?php if( !empty($res->mediaType) && !$res->mediaType->moveOtherFormatToListArea ){ ?>
                            <div class="mediaType-head">
                                <?php
                                if( !empty($res->mediaType->label) ){
                                    echo '<div class="info-title mb-10p"><strong>'.esc_html($res->mediaType->label).'</strong></div>';
                                }else{
                                    echo '<div class="info-title">Media Type</div>';
                                }
                                ?>
                                <div class="mdtype mb-10p text-center ce21-custom-button Availablebutton text-uppercase <?php echo esc_attr($res->mediaType->class); ?>"><?php echo esc_html($res->mediaType->mediaType); ?></div>
                                <?php if( !empty($res->mediaType->alsoAvailableAs) ){ ?>
                                    <div class="info-title mb-10p"><strong>Also Available</strong></div>
                                    <?php foreach( $res->mediaType->alsoAvailableAs as $alsoAvailableAs ) { ?>
                                        <a href="<?php echo esc_attr($alsoAvailableAs->url); ?>" class="mdtype text-center ce21-custom-button Availablebutton text-uppercase <?php echo esc_attr($alsoAvailableAs->class); ?>">
                                            <?php echo esc_html($alsoAvailableAs->label); ?>
                                            <?php
                                            if($alsoAvailableAs->count > 1){
                                                echo '<span class="badge">'.esc_html($alsoAvailableAs->count).'</span> ';
                                            }
                                            ?>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                     </div>
                    <div class="product-detail-info">
                        <h4><a href="<?php echo esc_url($res->url); ?>"><?php echo esc_html($res->title); ?></a></h4>
                        <?php
                            if( !empty($res->credits->url) ){
                                echo '<a href="'.esc_url($res->credits->url).'">Credit available - Click Here for more information</a>';
                            }else{
                                if( !empty($res->credits->credits) ) {
                                ?>
                                    <p>
                                        <span class="semi-bold">Total Credits:</span>
                                        <?php if( $res->credits->totalCredits > 0 ){ ?>
                                            <span class="badge"><?php echo esc_html($res->credits->totalCredits); ?></span>
                                            <span>including</span>
                                        <?php } ?>
                                        <?php foreach($res->credits->credits as $credit) {?>
                                            <span class="credit-list"><span class="badge"><?php echo esc_html($credit->amount); ?></span> <?php echo esc_html($credit->lupValue); ?></span>
                                        <?php } ?>
                                    </p>
                                <?php
                                }
                            }
                        ?>
                        <ul class="list-info">
                            <?php if( !empty($res->averageRatings) ){ ?>
                                <li class="list-head">
                                    <?php
                                    if( !empty($res->averageRatings->label) ){
                                        echo '<div class="info-title">'.esc_html($res->averageRatings->label).':</div>';
                                    }else{
                                        echo '<div class="info-title">Average Rating:</div>';
                                    }
                                    ?>
                                    <div class="info-text">
                                        <?php
                                        $rating = $res->averageRatings->ratings * 20;
                                        ?>
                                        <span class="stars-container stars-<?php echo esc_attr($rating); ?>">★★★★★</span> <?php echo esc_html( number_format((float)$res->averageRatings->ratings, 1, '.', '')); ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->topicAreas->topicAreas) ){ ?>
                                <li class="list-head">
                                    <?php
                                    if( !empty($res->topicAreas->label) ){
                                        echo '<div class="info-title">'.esc_html($res->topicAreas->label).':</div>';
                                    }else{
                                        echo '<div class="info-title">Topic Areas:</div>';
                                    }
                                    ?>
                                    <div class="info-text">
                                        <?php foreach($res->topicAreas->topicAreas as $topicArea) {?>
                                            <a href="<?php echo esc_url($topicArea->url); ?>"><?php echo esc_html($topicArea->name); ?></a>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->categories) ){ ?>
                                <li class="list-head">
                                    <?php
                                    if( !empty($res->categories->label) ){
                                        echo '<div class="info-title">'.esc_html($res->categories->label).':</div>';
                                    }else{
                                        echo '<div class="info-title">Categories:</div>';
                                    }
                                    ?>
                                    <div class="info-text">
                                        <?php foreach($res->categories->categories as $category) {?>
                                            <a href="<?php echo esc_url($category->url); ?>"><?php echo esc_html($category->name); ?></a>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->faculties) ){ ?>
                                <li class="list-head">
                                    <?php
                                        if( !empty($res->faculties->label) ){
                                            echo '<div class="info-title">'.esc_html($res->faculties->label).':</div>';
                                        }else{
                                            echo '<div class="info-title">Faculty:</div>';
                                        }
                                    ?>
                                    <div class="info-text">
                                        <?php foreach($res->faculties->faculties as $faculty) {?>
                                            <a href="<?php echo esc_url($faculty->url); ?>"><?php echo esc_html($faculty->name); ?></a>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->duration) ){ ?>
                                <li class="list-head">
                                    <div class="info-title">Duration:</div>
                                    <div class="info-text"><?php echo esc_html($res->duration); ?></div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->format) ){ ?>
                                <li class="list-head">
                                    <div class="info-title">Format:</div>
                                    <div class="info-text"><?php echo esc_html($res->format); ?></div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->programDate) ){ ?>
                                <li class="list-head">
                                    <?php
                                    if( !empty($res->programDate->label) ){
                                        echo '<div class="info-title">'.esc_html($res->programDate->label).':</div>';
                                    }else{
                                        echo '<div class="info-title">Program Date:</div>';
                                    }
                                    ?>
                                    <div class="info-text"><?php echo esc_html($res->programDate->value); ?></div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->sku) ){ ?>
                                <li class="list-head">
                                    <?php
                                    if( !empty($res->sku->label) ){
                                        echo '<div class="info-title">'.esc_html($res->sku->label).':</div>';
                                    }else{
                                        echo '<div class="info-title">Product Code:</div>';
                                    }
                                    ?>
                                    <div class="info-text"><?php echo esc_html($res->sku->sku); ?></div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->mediaType) && $res->mediaType->moveOtherFormatToListArea){ ?>
                                <li class="list-head">
                                    <?php
                                        if( !empty($res->mediaType->label) ){
                                            echo '<div class="info-title">'.esc_html($res->mediaType->label).':</div>';
                                        }else{
                                            echo '<div class="info-title">Media Type:</div>';
                                        }
                                    ?>
                                    <div class="info-text">
                                        <span><?php echo esc_html($res->mediaType->mediaType); ?></span>
                                        <?php if( !empty($res->mediaType->alsoAvailableAs) ){ ?>
                                            <strong>- Also Available</strong>
                                            <?php foreach( $res->mediaType->alsoAvailableAs as $alsoAvailableAs ) { ?>
                                                <a href="<?php echo esc_url($alsoAvailableAs->url); ?>">
                                                    <?php echo esc_html($alsoAvailableAs->label); ?>
                                                    <?php
                                                        if($alsoAvailableAs->count > 1){
                                                            echo '<span class="badge">'.esc_html($alsoAvailableAs->count).'</span> ';
                                                        }
                                                    ?>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->shortDescription) ){ ?>
                                <li class="list-head">
                                    <div class="info-title">Short Description:</div>
                                    <div class="info-text"><?php echo esc_html($res->shortDescription); ?></div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->price) ){ ?>
                                <li class="list-head">
                                    <?php
                                        if( !empty($res->price->label) ){
                                            echo '<div class="info-title">'.esc_html($res->price->label).':</div>';
                                        }else{
                                            echo '<div class="info-title">Media Type:</div>';
                                        }
                                    ?>
                                    <div class="info-text">
                                        <strong><?php echo esc_html($res->price->price); ?></strong>
                                        <?php if( !empty($res->price->discountPrice) && !$res->price->discountPrice->hideStrikeOnBasePrice ){ ?>
                                            <del><?php echo esc_html($res->price->discountPrice->basePrice); ?></del>
                                        <?php } ?>
                                        <?php if( !empty($res->price->discountPrice->endDate) ){ ?>
                                            <span style="color:red;display:block;"><strong><i><?php echo esc_html($res->price->discountPrice->endDate); ?></i></strong></span>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>

                            <?php if( !empty($res->eventDates) ){ ?>
                                <li class="list-head">
                                    <?php
                                        if( !empty($res->eventDates->label) ){
                                            echo '<div class="info-title">'.esc_html($res->eventDates->label).':</div>';
                                        }else{
                                            echo '<div class="info-title">Date:</div>';
                                        }
                                    ?>
                                    <div class="info-text" style="display:flex;align-items:center;">
                                        <svg id="Capa_1" class="icon-clock" enable-background="new 0 0 443.294 443.294" height="512" viewBox="0 0 443.294 443.294" width="512" xmlns="http://www.w3.org/2000/svg">
                                            <path d="m221.647 0c-122.214 0-221.647 99.433-221.647 221.647s99.433 221.647 221.647 221.647 221.647-99.433 221.647-221.647-99.433-221.647-221.647-221.647zm0 415.588c-106.941 0-193.941-87-193.941-193.941s87-193.941 193.941-193.941 193.941 87 193.941 193.941-87 193.941-193.941 193.941z"/>
                                            <path d="m235.5 83.118h-27.706v144.265l87.176 87.176 19.589-19.589-79.059-79.059z"/>
                                        </svg>
                                        <?php echo esc_html($res->eventDates->eventDate); ?>
                                        <?php if( !empty($res->eventDates->additionalDates) ){ ?>
                                            <div class="ce21-dropdown">
                                                <span class="ce21-dropbtn" href="#"> - more dates <i class="ce21-arrow-down"></i></span>
                                                <div class="ce21-dropdown-content">
                                                    <h6 class="spnAdditionalDate">Additional Dates</h6>
                                                    <span class="divider"></span>
                                                    <?php foreach($res->eventDates->additionalDates as $additionalDate ){ ?>
                                                        <a class="afterNone" href="<?php echo esc_url($additionalDate->url); ?>">
                                                            <svg id="Capa_1" class="icon-clock" enable-background="new 0 0 443.294 443.294" height="512" viewBox="0 0 443.294 443.294" width="512" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="m221.647 0c-122.214 0-221.647 99.433-221.647 221.647s99.433 221.647 221.647 221.647 221.647-99.433 221.647-221.647-99.433-221.647-221.647-221.647zm0 415.588c-106.941 0-193.941-87-193.941-193.941s87-193.941 193.941-193.941 193.941 87 193.941 193.941-87 193.941-193.941 193.941z"/>
                                                                <path d="m235.5 83.118h-27.706v144.265l87.176 87.176 19.589-19.589-79.059-79.059z"/>
                                                            </svg>
                                                            <?php echo esc_html($additionalDate->eventDate); ?>
                                                        </a>
                                                    <?php } ?>
                                                    <span class="divider"></span>
                                                    <p style="text-align:center;">You will be able to choose a specific date when you add this seminar to the cart.</p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if( !empty($res->scheduleAtGlance) ) {?>
                                            <a class="ce21-tt-wrapper" href="#">
                                                Schedule at glance
                                                <div class="ce21-tt-tooltip">
                                                    <div class="arrow"></div>
                                                    <h3 class="ce21-tt-popover-title">Schedule at glance</h3>
                                                    <div class="subdiv"><?php echo esc_html($res->scheduleAtGlance); ?></div>
                                                </div>
                                            </a>

                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="footer-product">
                    <a target="_blank" href="<?php echo esc_url($res->url); ?>" class="product-add-card-btn">
                        <svg height="512pt" viewBox="0 -31 512.00033 512" width="512pt" xmlns="http://www.w3.org/2000/svg">
                            <path d="m166 300.003906h271.003906c6.710938 0 12.597656-4.4375 14.414063-10.882812l60.003906-210.003906c1.289063-4.527344.40625-9.390626-2.433594-13.152344-2.84375-3.75-7.265625-5.964844-11.984375-5.964844h-365.632812l-10.722656-48.25c-1.523438-6.871094-7.617188-11.75-14.648438-11.75h-91c-8.289062 0-15 6.710938-15 15 0 8.292969 6.710938 15 15 15h78.960938l54.167968 243.75c-15.9375 6.929688-27.128906 22.792969-27.128906 41.253906 0 24.8125 20.1875 45 45 45h271.003906c8.292969 0 15-6.707031 15-15 0-8.289062-6.707031-15-15-15h-271.003906c-8.261719 0-15-6.722656-15-15s6.738281-15 15-15zm0 0"/>
                            <path d="m151 405.003906c0 24.816406 20.1875 45 45.003906 45 24.8125 0 45-20.183594 45-45 0-24.8125-20.1875-45-45-45-24.816406 0-45.003906 20.1875-45.003906 45zm0 0"/>
                            <path d="m362.003906 405.003906c0 24.816406 20.1875 45 45 45 24.816406 0 45-20.183594 45-45 0-24.8125-20.183594-45-45-45-24.8125 0-45 20.1875-45 45zm0 0"/>
                        </svg>
                        Add to Cart »
                    </a>
                </div>

                <?php if( !empty($res->tag) ){ ?>
                    <p class="tags-head">
                        <?php
                            if( !empty($res->tag->label) ){
                                echo '<span style="display:inline-block;">'.esc_html($res->tag->label).':</span>';
                            }
                            foreach($res->tag->tags as $tag) {
                                echo '<span class="badge text-white"><a href="'.esc_url($tag->url).'">'.esc_html($tag->name).'</a></span>';
                            }
                         ?>
                    </p>
                <?php } ?>

            </div>
        <?php
    }
        ?>
        <div class="product-pagging">
            <a target="_blank" href="<?php echo esc_url($response->pagging->viewAllUrl); ?>"><?php echo esc_html($response->pagging->label); ?></a>
        </div>
        <?php
    }else{
        ?>
        <div class="product-pagging">
            <p>No Products were found.</p>
        </div>
        <?php
    }

}
