<!-- dialog-confirm-logout is called from pageTimeout.js -->
<div id="dialog-confirm-logout" style="display:none;">Due to inactivity, you will be logged out of rConfig in 1 minute!<br /> Click OK to continue working</div>
<div id="breadcrumb">
    <h2>
        <?php
        // set pageType
        $pageType = 'breadcrumb';
        // get data from functions.inc.php, which is a query to the menupages table in the DB
        echo pageTitles($config_page, $pageType);
        ?>
    </h2>
</div>
