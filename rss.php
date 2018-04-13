<?php
/**
 *
 * @category        modules
 * @package         oneforall
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       Website Baker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version
 * @filesource
 * @lastmodified    $Date: 2016-08-18 20:22:28 +0200 (Do, 08. Aug 2016) $
 *
 */

// Shorturl yes/no
$shorturl = true;
$group_id = '';

// Check that GET values have been supplied
if(isset($_GET['page_id']) && is_numeric($_GET['page_id'])) {
    $page_id = intval($_GET['page_id']);
} else {
    // something is gone wrong, send error header
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
    if (preg_match('/fcgi/i', php_sapi_name())) {
        header("Status: 204 No Content"); // RFC7231, Section 6.3.5
    } else {
        header("HTTP/1.0 204  No Content");
    }
    flush();
    exit;
}

if(isset($_GET['group_id']) && is_numeric($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    define('GROUP_ID', $group_id);
}

// Include WB files
if ( !defined( 'WB_PATH' ) ){
    require( dirname(dirname((__DIR__))).'/config.php' );
    // Include path
    $inc_path = str_replace(DIRECTORY_SEPARATOR,'/',__DIR__);
    // Get module name
    require($inc_path.'/info.php');
}
if ( !class_exists('frontend')) { require(WB_PATH.'/framework/class.frontend.php');  }
// Create new frontend object
if (!isset($wb) || !($wb instanceof frontend)) { $wb = new frontend(); }
$wb->page_id = $page_id;
$wb->get_page_details();
$wb->get_website_settings();

//checkout if a charset is defined otherwise use UTF-8
if(defined('DEFAULT_CHARSET')) {
    $charset=DEFAULT_CHARSET;
} else {
    $charset='utf-8';
}

// Sending XML header
header("Content-type: text/xml; charset=$charset" );

// Header info
// Required by CSS 2.0
echo '<?xml version="1.0" encoding="'.$charset.'"?>';
?>
<rss version="2.0">
    <channel>
        <title><![CDATA[<?php echo PAGE_TITLE.' - '.WEBSITE_TITLE; ?>]]></title>
        <link>http://<?php echo $_SERVER['SERVER_NAME']; ?></link>
        <description><![CDATA[<?php echo PAGE_DESCRIPTION; ?>]]></description>
<?php
// Optional header info
?>
        <language><?php echo strtolower(DEFAULT_LANGUAGE); ?></language>
        <copyright><?php $thedate = date('Y'); $websitetitle = WEBSITE_TITLE; echo "Copyright {$thedate}, {$websitetitle}"; ?></copyright>
        <managingEditor><?php echo SERVER_EMAIL; ?></managingEditor>
        <webMaster><?php echo SERVER_EMAIL; ?></webMaster>
        <category><?php echo WEBSITE_TITLE; ?></category>
        <generator>WebsiteBaker CMS</generator>
<?php


// Get news items from database
$time = time();

/*
 * field ID of wanted description (value) from mod_name_item_fields
 * in head of your template index.php. Example:
 * <link rel="alternate" type="application/rss+xml" title="Your Title RSS-Feed" href="<?php echo WB_URL ?>/modules/your_module_name/rss.php?page_id=7&desc_id=4" />
*/

    // define some fixed select parts for the Querys
    // define a description id or leave it empty
    $sDescID = (@$_GET['desc_id']? : '');
    $sDescOrder = ($sDescID ? 'AND `f`.`field_id` = '.(int)$sDescID.' ' : '' );
    // use a defined group id or leave it empty
    $sGroupOrder= ($group_id ? 'AND `i`.`group_id` = '.(int)$group_id.' ' : '' );

//Querys
    // we need a link to this page here
    $sql = 'SELECT `link` FROM `'.TABLE_PREFIX.'pages` '
         . 'WHERE `page_id` = '.$page_id.' ';
    $pages_link = $database->get_one($sql);

    // simple select without descriptions
    if(!$sDescID){
    $sql='SELECT * FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` `i` '
        .'WHERE `i`.`page_id` = '.(int)$page_id.' '
        .''.$sGroupOrder.' '
        .'AND `i`.`active` = 1 '
        .'ORDER BY `i`.`modified_when` DESC' ;

    } else {

     $sql='SELECT *, `i`.`title` '  // i.title must be set, because there is also a title in table images
        .' FROM `'.TABLE_PREFIX.'mod_'.$mod_name.'_items` i, '
        .'      `'.TABLE_PREFIX.'mod_'.$mod_name.'_item_fields` f, '
        .'      `'.TABLE_PREFIX.'mod_'.$mod_name.'_images` img '
        .'WHERE `i`.`page_id` = '.(int)$page_id.' '
        .'AND   `i`.`item_id` = `f`.`item_id` '
        .'AND   `i`.`item_id` = `img`.`item_id` '
        .'AND   `img`.`position` = 1 '
        .''.$sDescOrder.' '
        .''.$sGroupOrder.' '
        .'AND `i`.`active` = 1 '
        .'ORDER BY `i`.`modified_when` DESC' ;
     }

     $result = $database->query($sql);

    if ($shorturl) {
        $page_extension = '/';
        $pages_dir = '';
    } else {
        $page_extension = PAGE_EXTENSION;
        $pages_dir = PAGES_DIRECTORY;
    }

     //Generating the news items
     while ($item = $result->fetchRow( MYSQLI_ASSOC )) {
        // switch between group description and field description
        $description = (!$sDescID ? stripslashes($item["description"]) : stripslashes($item["value"]));

        // if desc_id show image and add it before desc
        if($sDescID){
            // Image with position 1
            $img = '<img src="'.WB_URL.MEDIA_DIRECTORY.'/'.$mod_name.'/thumbs/item'.$item['item_id'].'/'.$item['filename'].'" ></img>';
            // Image + Desc
            $description = $img.$description;
        }

        $sPagesLink = ((isset($item["link"]) && ($item["link"] !=''))  ? stripslashes($pages_link).stripslashes($item["link"].$page_extension) : stripslashes($pages_link).$page_extension);
        // let the filter do his job (replace wblink && Media Url)
        $description = OutputFilterApi('WbLink|SysvarMedia', $description);

?>
    <item>
        <title><![CDATA[<?php echo stripslashes($item['title']); ?>]]></title>
        <description><![CDATA[<?php echo $description; ?>]]></description>
        <link><?php echo WB_URL.$pages_dir.$sPagesLink; ?></link>
        <pubDate><?PHP echo date('r', $item["modified_when"]); ?></pubDate>
        <guid><?php echo WB_URL.$pages_dir.$sPagesLink; ?></guid>
    </item>
<?php } ?>
    </channel>
</rss>