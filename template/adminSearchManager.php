<div id="wolfnet-search-manager" class="wrap">

    <div id="icon-options-wolfnet" class="icon32"><br></div>

    <h2>WolfNet - Search Manager</h2>

    <noscript>
        <div class="error">
            This page will not work without JavaScript enabled.
        </div>
    </noscript>

    <div style="width:875px">

        <p>The <strong>Search Manager</strong> allows you to create and save custom searches for use
            in shortcodes and widgets. The WordPress Search Manager works much the same way as the
            URL Search Builder within the MLSFinder Admin.</p>

        <p>Custom searches can target any of the search criteria that is available on your property
            search. Keep in mind that some search criteria is more restrictive than others, which
            means less results will be produced. Use the <strong>Results</strong> feature to
            determine how restrictive a search may be. NOTE: the search criteria available on your
            property search is based on the data available in the feed from your MLS. This data is
            subject to change, which may affect custom search strings you generate. WolfNet
            recommends that you periodically review your custom searches to verify that they still
            produce the expected results. If not, you may need to revisit the search manager and
            create a new custom search.</p>

    </div>

    <?php echo $searchForm; ?>

    <div id="save_search" class="style_box">
        <div class="style_box_header">Save</div>
        <div class="style_box_content">
            <input type="text" title="Description" style="width: 85%;" placeholder="Description">
            <button class="button-primary" style="margin-left: 15px;">Save Search</button>
        </div>
    </div>

    <table id="savedsearches" class="wp-list-table widefat" style="width:100%;">
        <thead>
            <tr>
                <th style="text-align:left;">Description</th>
                <th style="wwidth:200px;">Date Created</th>
                <th style="width:110px;"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<script type="text/javascript" src="<?php echo $this->url; ?>/js/jquery.wolfnetSearchManager.min.js"></script>
<script type="text/javascript">

    if ( typeof jQuery != 'undefined' ) {

        ( function ( $ ) {

            $( '#savedsearches' ).wolfnetSearchManager( {
                saveForm  : $( '#save_search' )
            } );

        } )( jQuery );

    }

</script>
