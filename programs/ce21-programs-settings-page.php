<?php
    $messagesArr = '';
    $access_token_response = get_access_token();

    if ($access_token_response['success'])
    {
        $response = ce21_get_service_wp_masters_list();
        if( is_array($response)){
            $message = $response['message'];
            $success = $response['success'];
            if(!$success){
                $messageClassName = 'error';
            }
            $messagesArr = array( 'message' => $message,  'messageClassName' => $messageClassName );
        }
        if( is_object($response)){
            $topicAreas = $response->topicAreas;
            $tags = $response->tags;
            $productTypes = $response->productTypes;
            $categories = $response->categories;
        }
    }
    else{
        $message = $access_token_response['message'];
        $messageClassName = 'error';
        $messagesArr = array( 'message' => $message,  'messageClassName' => $messageClassName );
    }

    if(!empty($messagesArr['message']) && !empty($messagesArr['messageClassName']))
    {
        echo '<div id="setting-error-settings_updated" class="'.esc_attr($messagesArr['messageClassName']).' settings-error notice is-dismissible"> 
                                <p><strong> '.esc_html($messagesArr['message']).'</strong></p>
                                <button type="button" class="notice-dismiss">
                                    <span class="screen-reader-text">Dismiss this notice.</span>
                                </button>
                            </div>';
    }

if ($access_token_response['success'])
{
?>
    <div class="program-list">
        <h1>Program List Settings</h1>
        <p style="margin:0;"><i>Use the below settings to generate a shortcode for the product list of your choice.</i></p>
        <p style="margin:0;"><i>You can select multiple Topic Areas, Tags, Product Types, Categories, and set page size or default sort order of the product list.</i></p>
        <form id="ce21_programs_list_settings_form" name="ce21_programs_list_settings_form">
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="topicArea">Topic Areas</label>
                </div>
                <div class="col-md-10">
                    <?php if(!empty($topicAreas)){ ?>
                        <select name="topicArea[]" id="topicArea" multiple="multiple" class="3col activeTopicAreas">
                            <?php
                            foreach ($topicAreas as $topicArea)
                            {
                                echo '<option value="'.esc_attr($topicArea->topicAreaId).'">'.esc_html($topicArea->topicAreaName).'</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="tags">Tags</label>
                </div>
                <div class="col-md-10">
                    <?php if(!empty($tags)){ ?>
                        <select name="tags[]" id="tags" multiple="multiple" class="3col activeTags">
                            <?php
                            foreach ($tags as $tag)
                            {
                                echo '<option value="'.esc_attr($tag->tagId).'">'.esc_html($tag->tagName).'</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="productTypes">Product Types</label>
                </div>
                <div class="col-md-10">
                    <?php if(!empty($productTypes)){ ?>
                        <select name="productTypes[]" id="productTypes" multiple="multiple" class="3col activeProductTypes">
                            <?php
                            foreach ($productTypes as $productType)
                            {
                                echo '<option value="'.esc_attr($productType->productTypeId).'">'.esc_html($productType->productTypeName).'</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="categories">Categories</label>
                </div>
                <div class="col-md-10">
                    <?php if(!empty($categories)){ ?>
                        <select name="categories[]" id="categories" multiple="multiple" class="3col activeCategories">
                            <?php
                            foreach ($categories as $category)
                            {
                                echo '<option value="'.esc_attr($category->categoryId).'">'.esc_html($category->categoryName).'</option>';
                            }
                            ?>
                        </select>
                    <?php } ?>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="page_size">Page size</label>
                </div>
                <div class="col-md-10">
                    <input type="text" name="page_size" id="page_size" value="25" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="sort_order">Default sort order</label>
                </div>
                <div class="col-md-10">
                    <select name="sort_order" id="sort_order">
                        <option value="11">Publish Date</option>
                        <option value="31">Program Date</option>
                        <option value="41">Title</option>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2"></div>
                <div class="col-md-10">
                    <input type="button" id="btn_save_ce21_program_settings" class="btn btn-success" value="Generate Shortcode"/>
                </div>
            </div>

        </form>
        <div id="ce21_ss_api_settings_notification_div" class="ce21-ss-alert alert alert-success" style="display: none">
        </div>

        <div class="row mt-2" id="ce21_div_generated_shortcode" style="display: none">
            <div class="col-md-2">
                <label for="ce21_generated_shortcode">Shortcode</label>
            </div>
            <div class="col-md-8">
                <input type="text" id="ce21_generated_shortcode" value="" />
            </div>
            <div class="col-md-2">
                <button id="copy_short_code" class="btn btn-success" onclick="copyShortcode()">
                    Copy Shortcode
                </button>
            </div>
        </div>

    </div>
    <div id="ce21_ss_admin_loader_div" class="ce21-ss-admin-loader" style="display: none">
        <img src="<?php echo esc_url(SINGLE_SIGN_ON_CE21__PLUGIN_URL . "admin/images/loader.gif"); ?>">
    </div>

    <style type="text/css">
        .ce21-ss-admin-loader{
            z-index: 9;
        }
    </style>
    <script type="text/javascript">
        jQuery('select[multiple].activeTopicAreas.3col').multiselect({
            columns: 3,
            placeholder: 'Select Topic Areas',
            search: true,
            searchOptions: {
                'default': 'Search Topic Areas'
            },
            //selectAll: true
        });
        jQuery('select[multiple].activeTags.3col').multiselect({
            columns: 3,
            placeholder: 'Select Tags',
            search: true,
            searchOptions: {
                'default': 'Search Tags'
            },
            //selectAll: true
        });
        jQuery('select[multiple].activeProductTypes.3col').multiselect({
            columns: 3,
            placeholder: 'Select Product Types',
            search: true,
            searchOptions: {
                'default': 'Search Product Types'
            },
            //selectAll: true
        });
        jQuery('select[multiple].activeCategories.3col').multiselect({
            columns: 3,
            placeholder: 'Select Categories',
            search: true,
            searchOptions: {
                'default': 'Search Categories'
            },
            //selectAll: true
        });
        function copyShortcode() {
            var copyText = document.getElementById("ce21_generated_shortcode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            var tooltip = document.getElementById("copy_short_code");
            tooltip.innerHTML = "Shortcode Copied";
        }
    </script>
<?php } ?>