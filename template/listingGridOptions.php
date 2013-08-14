<div id="<?php echo $instance_id; ?>" class="wolfnet_listingGridOptions">

    <input id="<?php echo $criteria_wpid; ?>" name="<?php echo $criteria_wpname; ?>" value="<?php echo $criteria; ?>" type="hidden" />

    <table class="form-table">

        <tr>
            <td><label>Title:</label></td>
            <td><input id="<?php echo $title_wpid; ?>" name="<?php echo $title_wpname; ?>" value="<?php echo $title; ?>" type="text" /></td>
        </tr>

        <tr class="modeField">
            <td><label>Mode:</label></td>
            <td>
                <input id="<?php echo $mode_wpid; ?>" name="<?php echo $mode_wpname; ?>" value="basic" type="radio" <?php echo $mode_basic_wpc; ?> /> Basic <br/>
                <input id="<?php echo $mode_wpid; ?>" name="<?php echo $mode_wpname; ?>" value="advanced" type="radio" <?php echo $mode_advanced_wpc; ?> /> Advanced
            </td>
        </tr>

        <tr class="advanced-option savedSearchField">
            <td><label>Saved Search:</label></td>
            <td>
                <select id="<?php echo $savedsearch_wpid; ?>" name="<?php echo $savedsearch_wpname; ?>" style="width:200px;">
                    <?php $foundOne = false; ?>
                    <option value="">-- Saved Search --</option>
                    <?php foreach ($savedsearches as $ss) { ?>
                        <?php $foundOne = ($savedsearch == $ss->ID) ? true : $foundOne; ?>
                        <option value="<?php echo $ss->ID; ?>" <?php selected($savedsearch, $ss->ID) ?>>
                            <?php echo $ss->post_title; ?>
                        </option>
                    <?php } ?>
                    <?php if ( !$foundOne && ( $criteria != '' && $criteria != '[]' ) ) { ?>
                        <option value="deleted" selected="selected">** DELETED **</option>
                    <?php } ?>
                </select>
                <span class="wolfnet_moreInfo">
                    Select a saved search to define the properties to be displayed. Saved searches
                    are created via the Search Manager page within the WolfNet plugin admin section.
                </span>
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>Price:</label></td>
            <td>
                <select id="<?php echo $minprice_wpid; ?>" name="<?php echo $minprice_wpname; ?>">
                    <option value="">Min. Price</option>
                    <?php foreach ($prices as $price) { ?>
                        <option value="<?php echo $price['value']; ?>" <?php selected($minprice, $price['value']); ?>>
                            <?php echo $price['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span>to</span>
                <select id="<?php echo $maxprice_wpid; ?>" name="<?php echo $maxprice_wpname; ?>">
                    <option value="">Max. Price</option>
                    <?php foreach ( $prices as $price ) { ?>
                        <option value="<?php echo $price['value']; ?>" <?php selected($maxprice, $price['value']); ?>>
                            <?php echo $price['label']; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>City:</label></td>
            <td>
                <input id="<?php echo $city_wpid; ?>" name="<?php echo $city_wpname; ?>"
                    type="text" value="<?php echo $city; ?>" />
            </td>
        </tr>

        <tr class="basic-option">
            <td><label>Zipcode:</label></td>
            <td>
                <input id="<?php echo $zipcode_wpid; ?>" name="<?php echo $zipcode_wpname; ?>"
                    type="text" value="<?php echo $zipcode; ?>" />
            </td>
        </tr>

        <tr>
            <td><label>Agent/Broker:</label></td>
            <td>
                <select id="<?php echo $ownertype_wpid; ?>" name="<?php echo $ownertype_wpname; ?>">
                    <option value="all">All</option>
                    <?php foreach ($ownertypes as $ot) { ?>
                        <option value="<?php echo $ot['value']; ?>" <?php selected($ownertype, $ot['value']); ?>>
                            <?php echo $ot['label']; ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="wolfnet_moreInfo">
                    Restrict search results by brokerage and/or agent. When All (the default) is
                    selected, all matching properties display, regardless of listing brokerage and
                    agent. When any of the other options is selected, search results are restricted
                    to the site owning agent or brokerage, as indicated by the name of the option
                    (ie, Agent Then Broker, Agent Only, Broker Only).
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Pagination Enabled/Disabled:</label></td>
            <td>
                <select id="<?php echo $paginated_wpid; ?>" name="<?php echo $paginated_wpname; ?>" >
                    <option value="false" <?php echo $paginated_false_wps; ?>>Disabled</option>
                    <option value="true"  <?php echo $paginated_true_wps; ?> >Enabled</option>
                </select>
                <span class="wolfnet_moreInfo">
                    Enable to add pagination capabilities for the user to the result set.
                    Results per page can be defined below in the Max Results Per Page field.
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Sort Options:</label></td>
            <td>
                <select id="<?php echo $sortoptions_wpid; ?>" name="<?php echo $sortoptions_wpname; ?>" >
                    <option value="false" <?php echo $sortoptions_false_wps; ?>>Disabled</option>
                    <option value="true"  <?php echo $sortoptions_true_wps; ?> >Enabled</option>
                </select>
                <span class="wolfnet_moreInfo">
                    Enable to add a drop-down menu which will allow users to sort listings by a
                    predefined set of data fields.
                </span>
            </td>
        </tr>

        <tr>
            <td><label>Max Results Per Page:</label></td>
            <td>
                <input id="<?php echo $maxresults_wpid; ?>" name="<?php echo $maxresults_wpname; ?>"
                    type="text" maxlength="2" size="2" value="<?php echo $maxresults; ?>" />
                <span class="wolfnet_moreInfo">
                    Define the number of properties to display per search results page.
                    The maximum number of properties that can be displayed per page is 50.
                </span>
            </td>
        </tr>

    </table>

</div>

<script type="text/javascript">

    jQuery(function($){
        $('#<?php echo $instance_id; ?>').wolfnetListingGridControls();
        wolfnet.initMoreInfo( $( '#<?php echo $instance_id; ?> .wolfnet_moreInfo' ) );
    });

</script>
