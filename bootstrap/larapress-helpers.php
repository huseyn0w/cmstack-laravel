<?php
/**
 * LaraPress CMS
 * File: larapress-helpers.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 22.07.2019
 */

use App\Http\Models\UserRoles;
use App\Http\Models\UserPermissions;
use App\Http\Models\User;
use App\Http\Models\Category;
use App\Http\Models\CategoryTranslation;
use App\Http\Models\PostTranslation;
use App\Http\Models\PageTranslation;
use App\Http\Models\Post;
use App\Http\Models\Page;
use App\Http\Models\Comments;
use App\Http\Models\Menu;
use App\Http\Models\CPanel\CPanelSiteOptions;
use App\Http\Models\CPanel\CPanelGeneralSettings;
use App\Http\Models\CPanel\CPanelSeoSettings;
use App\Http\Models\CPanel\CPanelGeoSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Doctrine\DBAL\Driver\PDOException;

function lang_exist($code)
{
    $languages = get_languages();

    if(array_key_exists($code,$languages)) return true;

    return false;
}

function get_languages()
{
    return Config::get('app.languages_list');
}


function get_front_templates_array():array
{
    $folders_array = [];

    $dir = public_path().'/front';
    $array= scandir($dir);

    if($array)
    {
        unset($array[0]);
        unset($array[1]);
        $folders_array = $array;
    }

    return $folders_array;
}

function is_logged_in():bool
{
    if(\Auth::check()) return true;

    return false;
}


function get_user_roles():object
{
    $roles = UserRoles::select('id', 'name')->get();

    return $roles;

}

function get_post_categories_list($fields = []):object
{
    $locale = get_current_lang();

    if(empty($fields)) $fields = ['category_id', 'title'];

    $categories = CategoryTranslation::where('locale' ,$locale)->orderBy('id', 'ASC')->get($fields);


    return $categories;

}

function get_post_list($fields = []):object
{
    $locale = get_current_lang();

    if(empty($fields)) $fields = ['posts.id', 'post_translations.title', 'post_translations.slug'];
    $posts = Post::join('post_translations', 'posts.id', '=','post_translations.post_id')
        ->select($fields)->orderBy('posts.id', 'ASC')->where('locale', $locale)->get();

    return $posts;

}

function get_pages_list($fields = []):object
{
    $locale = get_current_lang();

    if(empty($fields)) $fields = ['pages.id', 'post_translations.title', 'post_translations.slug'];
    $pages = Page::join('page_translations', 'pages.id', '=','page_translations.page_id')
        ->select($fields)->orderBy('pages.id', 'ASC')->where('locale', $locale)->get();

    return $pages;

}

function get_user_role_permissions()
{
    $role_permissions = UserPermissions::all();

    return $role_permissions;

}
function get_authors_list()
{
    $list = User::select('id', 'username')->get();

    return $list;
}

function registration_status():bool
{
    return false;
}

function get_countries_array():array
{
    $countries = array(
        array('code' => 'US', 'name' => 'United States'),
        array('code' => 'CA', 'name' => 'Canada'),
        array('code' => 'AF', 'name' => 'Afghanistan'),
        array('code' => 'AL', 'name' => 'Albania'),
        array('code' => 'DZ', 'name' => 'Algeria'),
        array('code' => 'AS', 'name' => 'American Samoa'),
        array('code' => 'AD', 'name' => 'Andorra'),
        array('code' => 'AO', 'name' => 'Angola'),
        array('code' => 'AI', 'name' => 'Anguilla'),
        array('code' => 'AQ', 'name' => 'Antarctica'),
        array('code' => 'AG', 'name' => 'Antigua and/or Barbuda'),
        array('code' => 'AR', 'name' => 'Argentina'),
        array('code' => 'AM', 'name' => 'Armenia'),
        array('code' => 'AW', 'name' => 'Aruba'),
        array('code' => 'AU', 'name' => 'Australia'),
        array('code' => 'AT', 'name' => 'Austria'),
        array('code' => 'AZ', 'name' => 'Azerbaijan'),
        array('code' => 'BS', 'name' => 'Bahamas'),
        array('code' => 'BH', 'name' => 'Bahrain'),
        array('code' => 'BD', 'name' => 'Bangladesh'),
        array('code' => 'BB', 'name' => 'Barbados'),
        array('code' => 'BY', 'name' => 'Belarus'),
        array('code' => 'BE', 'name' => 'Belgium'),
        array('code' => 'BZ', 'name' => 'Belize'),
        array('code' => 'BJ', 'name' => 'Benin'),
        array('code' => 'BM', 'name' => 'Bermuda'),
        array('code' => 'BT', 'name' => 'Bhutan'),
        array('code' => 'BO', 'name' => 'Bolivia'),
        array('code' => 'BA', 'name' => 'Bosnia and Herzegovina'),
        array('code' => 'BW', 'name' => 'Botswana'),
        array('code' => 'BV', 'name' => 'Bouvet Island'),
        array('code' => 'BR', 'name' => 'Brazil'),
        array('code' => 'IO', 'name' => 'British lndian Ocean Territory'),
        array('code' => 'BN', 'name' => 'Brunei Darussalam'),
        array('code' => 'BG', 'name' => 'Bulgaria'),
        array('code' => 'BF', 'name' => 'Burkina Faso'),
        array('code' => 'BI', 'name' => 'Burundi'),
        array('code' => 'KH', 'name' => 'Cambodia'),
        array('code' => 'CM', 'name' => 'Cameroon'),
        array('code' => 'CV', 'name' => 'Cape Verde'),
        array('code' => 'KY', 'name' => 'Cayman Islands'),
        array('code' => 'CF', 'name' => 'Central African Republic'),
        array('code' => 'TD', 'name' => 'Chad'),
        array('code' => 'CL', 'name' => 'Chile'),
        array('code' => 'CN', 'name' => 'China'),
        array('code' => 'CX', 'name' => 'Christmas Island'),
        array('code' => 'CC', 'name' => 'Cocos (Keeling) Islands'),
        array('code' => 'CO', 'name' => 'Colombia'),
        array('code' => 'KM', 'name' => 'Comoros'),
        array('code' => 'CG', 'name' => 'Congo'),
        array('code' => 'CK', 'name' => 'Cook Islands'),
        array('code' => 'CR', 'name' => 'Costa Rica'),
        array('code' => 'HR', 'name' => 'Croatia (Hrvatska)'),
        array('code' => 'CU', 'name' => 'Cuba'),
        array('code' => 'CY', 'name' => 'Cyprus'),
        array('code' => 'CZ', 'name' => 'Czech Republic'),
        array('code' => 'CD', 'name' => 'Democratic Republic of Congo'),
        array('code' => 'DK', 'name' => 'Denmark'),
        array('code' => 'DJ', 'name' => 'Djibouti'),
        array('code' => 'DM', 'name' => 'Dominica'),
        array('code' => 'DO', 'name' => 'Dominican Republic'),
        array('code' => 'TP', 'name' => 'East Timor'),
        array('code' => 'EC', 'name' => 'Ecudaor'),
        array('code' => 'EG', 'name' => 'Egypt'),
        array('code' => 'SV', 'name' => 'El Salvador'),
        array('code' => 'GQ', 'name' => 'Equatorial Guinea'),
        array('code' => 'ER', 'name' => 'Eritrea'),
        array('code' => 'EE', 'name' => 'Estonia'),
        array('code' => 'ET', 'name' => 'Ethiopia'),
        array('code' => 'FK', 'name' => 'Falkland Islands (Malvinas)'),
        array('code' => 'FO', 'name' => 'Faroe Islands'),
        array('code' => 'FJ', 'name' => 'Fiji'),
        array('code' => 'FI', 'name' => 'Finland'),
        array('code' => 'FR', 'name' => 'France'),
        array('code' => 'FX', 'name' => 'France, Metropolitan'),
        array('code' => 'GF', 'name' => 'French Guiana'),
        array('code' => 'PF', 'name' => 'French Polynesia'),
        array('code' => 'TF', 'name' => 'French Southern Territories'),
        array('code' => 'GA', 'name' => 'Gabon'),
        array('code' => 'GM', 'name' => 'Gambia'),
        array('code' => 'GE', 'name' => 'Georgia'),
        array('code' => 'DE', 'name' => 'Germany'),
        array('code' => 'GH', 'name' => 'Ghana'),
        array('code' => 'GI', 'name' => 'Gibraltar'),
        array('code' => 'GR', 'name' => 'Greece'),
        array('code' => 'GL', 'name' => 'Greenland'),
        array('code' => 'GD', 'name' => 'Grenada'),
        array('code' => 'GP', 'name' => 'Guadeloupe'),
        array('code' => 'GU', 'name' => 'Guam'),
        array('code' => 'GT', 'name' => 'Guatemala'),
        array('code' => 'GN', 'name' => 'Guinea'),
        array('code' => 'GW', 'name' => 'Guinea-Bissau'),
        array('code' => 'GY', 'name' => 'Guyana'),
        array('code' => 'HT', 'name' => 'Haiti'),
        array('code' => 'HM', 'name' => 'Heard and Mc Donald Islands'),
        array('code' => 'HN', 'name' => 'Honduras'),
        array('code' => 'HK', 'name' => 'Hong Kong'),
        array('code' => 'HU', 'name' => 'Hungary'),
        array('code' => 'IS', 'name' => 'Iceland'),
        array('code' => 'IN', 'name' => 'India'),
        array('code' => 'ID', 'name' => 'Indonesia'),
        array('code' => 'IR', 'name' => 'Iran (Islamic Republic of)'),
        array('code' => 'IQ', 'name' => 'Iraq'),
        array('code' => 'IE', 'name' => 'Ireland'),
        array('code' => 'IL', 'name' => 'Israel'),
        array('code' => 'IT', 'name' => 'Italy'),
        array('code' => 'CI', 'name' => 'Ivory Coast'),
        array('code' => 'JM', 'name' => 'Jamaica'),
        array('code' => 'JP', 'name' => 'Japan'),
        array('code' => 'JO', 'name' => 'Jordan'),
        array('code' => 'KZ', 'name' => 'Kazakhstan'),
        array('code' => 'KE', 'name' => 'Kenya'),
        array('code' => 'KI', 'name' => 'Kiribati'),
        array('code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of'),
        array('code' => 'KR', 'name' => 'Korea, Republic of'),
        array('code' => 'KW', 'name' => 'Kuwait'),
        array('code' => 'KG', 'name' => 'Kyrgyzstan'),
        array('code' => 'LA', 'name' => 'Lao People\'s Democratic Republic'),
        array('code' => 'LV', 'name' => 'Latvia'),
        array('code' => 'LB', 'name' => 'Lebanon'),
        array('code' => 'LS', 'name' => 'Lesotho'),
        array('code' => 'LR', 'name' => 'Liberia'),
        array('code' => 'LY', 'name' => 'Libyan Arab Jamahiriya'),
        array('code' => 'LI', 'name' => 'Liechtenstein'),
        array('code' => 'LT', 'name' => 'Lithuania'),
        array('code' => 'LU', 'name' => 'Luxembourg'),
        array('code' => 'MO', 'name' => 'Macau'),
        array('code' => 'MK', 'name' => 'Macedonia'),
        array('code' => 'MG', 'name' => 'Madagascar'),
        array('code' => 'MW', 'name' => 'Malawi'),
        array('code' => 'MY', 'name' => 'Malaysia'),
        array('code' => 'MV', 'name' => 'Maldives'),
        array('code' => 'ML', 'name' => 'Mali'),
        array('code' => 'MT', 'name' => 'Malta'),
        array('code' => 'MH', 'name' => 'Marshall Islands'),
        array('code' => 'MQ', 'name' => 'Martinique'),
        array('code' => 'MR', 'name' => 'Mauritania'),
        array('code' => 'MU', 'name' => 'Mauritius'),
        array('code' => 'TY', 'name' => 'Mayotte'),
        array('code' => 'MX', 'name' => 'Mexico'),
        array('code' => 'FM', 'name' => 'Micronesia, Federated States of'),
        array('code' => 'MD', 'name' => 'Moldova, Republic of'),
        array('code' => 'MC', 'name' => 'Monaco'),
        array('code' => 'MN', 'name' => 'Mongolia'),
        array('code' => 'MS', 'name' => 'Montserrat'),
        array('code' => 'MA', 'name' => 'Morocco'),
        array('code' => 'MZ', 'name' => 'Mozambique'),
        array('code' => 'MM', 'name' => 'Myanmar'),
        array('code' => 'NA', 'name' => 'Namibia'),
        array('code' => 'NR', 'name' => 'Nauru'),
        array('code' => 'NP', 'name' => 'Nepal'),
        array('code' => 'NL', 'name' => 'Netherlands'),
        array('code' => 'AN', 'name' => 'Netherlands Antilles'),
        array('code' => 'NC', 'name' => 'New Caledonia'),
        array('code' => 'NZ', 'name' => 'New Zealand'),
        array('code' => 'NI', 'name' => 'Nicaragua'),
        array('code' => 'NE', 'name' => 'Niger'),
        array('code' => 'NG', 'name' => 'Nigeria'),
        array('code' => 'NU', 'name' => 'Niue'),
        array('code' => 'NF', 'name' => 'Norfork Island'),
        array('code' => 'MP', 'name' => 'Northern Mariana Islands'),
        array('code' => 'NO', 'name' => 'Norway'),
        array('code' => 'OM', 'name' => 'Oman'),
        array('code' => 'PK', 'name' => 'Pakistan'),
        array('code' => 'PW', 'name' => 'Palau'),
        array('code' => 'PA', 'name' => 'Panama'),
        array('code' => 'PG', 'name' => 'Papua New Guinea'),
        array('code' => 'PY', 'name' => 'Paraguay'),
        array('code' => 'PE', 'name' => 'Peru'),
        array('code' => 'PH', 'name' => 'Philippines'),
        array('code' => 'PN', 'name' => 'Pitcairn'),
        array('code' => 'PL', 'name' => 'Poland'),
        array('code' => 'PT', 'name' => 'Portugal'),
        array('code' => 'PR', 'name' => 'Puerto Rico'),
        array('code' => 'QA', 'name' => 'Qatar'),
        array('code' => 'SS', 'name' => 'Republic of South Sudan'),
        array('code' => 'RE', 'name' => 'Reunion'),
        array('code' => 'RO', 'name' => 'Romania'),
        array('code' => 'RU', 'name' => 'Russian Federation'),
        array('code' => 'RW', 'name' => 'Rwanda'),
        array('code' => 'KN', 'name' => 'Saint Kitts and Nevis'),
        array('code' => 'LC', 'name' => 'Saint Lucia'),
        array('code' => 'VC', 'name' => 'Saint Vincent and the Grenadines'),
        array('code' => 'WS', 'name' => 'Samoa'),
        array('code' => 'SM', 'name' => 'San Marino'),
        array('code' => 'ST', 'name' => 'Sao Tome and Principe'),
        array('code' => 'SA', 'name' => 'Saudi Arabia'),
        array('code' => 'SN', 'name' => 'Senegal'),
        array('code' => 'RS', 'name' => 'Serbia'),
        array('code' => 'SC', 'name' => 'Seychelles'),
        array('code' => 'SL', 'name' => 'Sierra Leone'),
        array('code' => 'SG', 'name' => 'Singapore'),
        array('code' => 'SK', 'name' => 'Slovakia'),
        array('code' => 'SI', 'name' => 'Slovenia'),
        array('code' => 'SB', 'name' => 'Solomon Islands'),
        array('code' => 'SO', 'name' => 'Somalia'),
        array('code' => 'ZA', 'name' => 'South Africa'),
        array('code' => 'GS', 'name' => 'South Georgia South Sandwich Islands'),
        array('code' => 'ES', 'name' => 'Spain'),
        array('code' => 'LK', 'name' => 'Sri Lanka'),
        array('code' => 'SH', 'name' => 'St. Helena'),
        array('code' => 'PM', 'name' => 'St. Pierre and Miquelon'),
        array('code' => 'SD', 'name' => 'Sudan'),
        array('code' => 'SR', 'name' => 'Suriname'),
        array('code' => 'SJ', 'name' => 'Svalbarn and Jan Mayen Islands'),
        array('code' => 'SZ', 'name' => 'Swaziland'),
        array('code' => 'SE', 'name' => 'Sweden'),
        array('code' => 'CH', 'name' => 'Switzerland'),
        array('code' => 'SY', 'name' => 'Syrian Arab Republic'),
        array('code' => 'TW', 'name' => 'Taiwan'),
        array('code' => 'TJ', 'name' => 'Tajikistan'),
        array('code' => 'TZ', 'name' => 'Tanzania, United Republic of'),
        array('code' => 'TH', 'name' => 'Thailand'),
        array('code' => 'TG', 'name' => 'Togo'),
        array('code' => 'TK', 'name' => 'Tokelau'),
        array('code' => 'TO', 'name' => 'Tonga'),
        array('code' => 'TT', 'name' => 'Trinidad and Tobago'),
        array('code' => 'TN', 'name' => 'Tunisia'),
        array('code' => 'TR', 'name' => 'Turkey'),
        array('code' => 'TM', 'name' => 'Turkmenistan'),
        array('code' => 'TC', 'name' => 'Turks and Caicos Islands'),
        array('code' => 'TV', 'name' => 'Tuvalu'),
        array('code' => 'UG', 'name' => 'Uganda'),
        array('code' => 'UA', 'name' => 'Ukraine'),
        array('code' => 'AE', 'name' => 'United Arab Emirates'),
        array('code' => 'GB', 'name' => 'United Kingdom'),
        array('code' => 'UM', 'name' => 'United States minor outlying islands'),
        array('code' => 'UY', 'name' => 'Uruguay'),
        array('code' => 'UZ', 'name' => 'Uzbekistan'),
        array('code' => 'VU', 'name' => 'Vanuatu'),
        array('code' => 'VA', 'name' => 'Vatican City State'),
        array('code' => 'VE', 'name' => 'Venezuela'),
        array('code' => 'VN', 'name' => 'Vietnam'),
        array('code' => 'VG', 'name' => 'Virgin Islands (British)'),
        array('code' => 'VI', 'name' => 'Virgin Islands (U.S.)'),
        array('code' => 'WF', 'name' => 'Wallis and Futuna Islands'),
        array('code' => 'EH', 'name' => 'Western Sahara'),
        array('code' => 'YE', 'name' => 'Yemen'),
        array('code' => 'YU', 'name' => 'Yugoslavia'),
        array('code' => 'ZR', 'name' => 'Zaire'),
        array('code' => 'ZM', 'name' => 'Zambia'),
        array('code' => 'ZW', 'name' => 'Zimbabwe'),
    );

    return $countries;
}



function render_menu($menu_data, $params)
{
    if(!$menu_data) return false;

    $route_name = Route::currentRouteName();

    $menu_type                    = $params['menu_type'] ?? "list";
    $menu_class                   = $params['menu_class'] ?? "menu";
    $menu_id                      = $params['menu_id'] ?? "menu";
    $item_class                   = $params['item_class'] ?? "menu-item";
    $item_class_with_submenu      = $params['item_class_with_submenu'] ?? "";
    $link_class                   = $params['link_class'] ?? "menu-item-link";
    $item_link_class_with_submenu = $params['item_link_class_with_submenu'] ?? "";
    $submenu_type                 = $params['submenu_type'] ?? "list";
    $submenu_class                = $params['submenu_class'] ?? "sub-menu";
    $subitem_class                = $params['subitem_class'] ?? "sub-menu-item";
    $sub_link_class               = $params['sublink_class'] ?? "sub-menu-item-link";

    $html = "";


    $html .= $menu_type === "list" ? "<ul class='$menu_class' id='$menu_id'>" : "<div class=".$menu_class." id=".$menu_id.">";


    $locale = get_current_lang();

    if($locale === config('app.locale'))
    {
        $locale = null;
    }
    else
    {
        $locale.='/';
    }

    foreach($menu_data as $menu_item){

        switch ($menu_item->type){
            case "posts": $type = $locale."/posts/";
                break;
            case "categories": $type = $locale."/category/";
                break;
            default: $type = $locale .'';
                break;
        }

        $slug  = $menu_item->slug === "/" ? " " : $menu_item->slug;

        $link_part = strpos($slug, "https") !== false ? $type.$slug : config('app.url').'/'.$type.$slug;

        $link = $route_name === "cpanel_edit_menu" ? "javascript:void()": $link_part;

        // Menu titles/slugs/types are user supplied; escape everything that is
        // interpolated into HTML or HTML attributes to prevent stored XSS.
        $safe_title = e($menu_item->title);
        $safe_type  = e($menu_item->type);
        $safe_slug  = e($menu_item->slug);
        $safe_link  = e($link);

        $label = $route_name === "cpanel_edit_menu" ? "<span>{$safe_title}</span>" : $safe_title;

        if($route_name === "cpanel_edit_menu")
        {
            $html .= $menu_type === "list" ? "<li class='$item_class' data-type='$safe_type' data-title='$safe_title' data-link='$safe_slug'>" : null;
            $html.= "<a href='$safe_link' class='$link_class'>".$label;
        }
        else
        {
            if(isset($menu_item->children) && is_array($menu_item->children) && !empty($menu_item->children))
            {
                $html .= $menu_type === "list" ? "<li class='$item_class $item_class_with_submenu'>" : null;
                $html.= "<a href='$safe_link' class='$link_class $item_link_class_with_submenu'>".$label;
            }
            else
            {
                $html .= $menu_type === "list" ? "<li class='$item_class'>" : null;
                $html.= "<a href='$safe_link' class='$link_class'>".$label;
            }
        }


        if($route_name === "cpanel_edit_menu"){
            $html .= "<button class='remove_menu_item' type='button'>X</button>";
        }

        $html.= "</a>";

        if(isset($menu_item->children) && is_array($menu_item->children) && !empty($menu_item->children))
        {
            $submenu_params = [
                'menu_type' => $submenu_type,
                'menu_class' => $submenu_class,
                'item_class' => $subitem_class,
                'link_class' => $sub_link_class
            ];
            $html.= render_menu($menu_item->children, $submenu_params);
        }

        $html .= $menu_type === "list" ? "</li>" : null;

    }

    $html .= $menu_type === "list" ? "</ul>" : "</div>";
    return $html;
}

function get_current_lang()
{
    $lang = \Session::get('locale');

    if(is_null($lang) || empty($lang)) $lang = app()->getLocale();
    return $lang;
}

function set_current_lang(string $string)
{
    return app()->setLocale($string);
}

function get_menu_data($menu_slug, $data)
{

    $locale = get_current_lang();

    try{
        $menu = Menu::join('menu_translations', 'menus.id', '=','menu_translations.menu_id')
            ->select('menus.id', 'menu_translations.content')
            ->where('menu_translations.locale', $locale)
            ->where('menus.slug', $menu_slug)->first();

    } catch (QueryException $e) {
        return false;
    } catch (PDOException $e) {
        return false;
    } catch (\Error $e) {
        return false;
    }


    $html = render_menu(json_decode($menu->content), $data);

    return $html;
}


function get_taxonomy_name()
{
    $request = app('request')->route()->getAction();
    $controller_name = $request['as'];

    return $controller_name;
}

function get_field($field_key, $custom_fields_array)
{
    if(!is_array($custom_fields_array) || empty($custom_fields_array) || !isset($custom_fields_array[$field_key]['value'])) return false;

    return $custom_fields_array[$field_key]['value'];

}

function is_search_page()
{
    $request_route_name = app('request')->route()->getName();
    return $request_route_name === "get_search_page";
}


function get_page_templates_list()
{
    $files  = Storage::disk('views')->files('default/pages');
    if(empty($files)) return false;

    $final_array = [];

    foreach ($files as $key => $file)
    {
        $filename = str_replace("default/pages/", "", $file);
        $filename = str_replace(".blade.php", "", $filename);

        $file_content  = Storage::disk('views')->get($file);
        $tokens = token_get_all( $file_content );

        foreach($tokens as $token) {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
                $comments[$filename] = $token[1];
            }
        }

    }

    foreach($comments as $filename => $comment){

        $start_position = strpos($comment, "Template Name:") + 15;
        $end_position = strpos($comment, ";");

        $string = mb_substr($comment, $start_position, $end_position);

        $string = str_replace('"', "", $string);
        $string = trim(preg_replace('/\s+/', ' ', $string));
        $string = str_replace('; */', "", $string);

        $final_array[$filename] = $string;
    }


    return $final_array;

}


function get_site_options($key = null)
{

    $data = null;
    if(is_null($key))
    {
        $data = CPanelSiteOptions::first();
    }
    else{
        $collection = CPanelSiteOptions::all($key);
        $data = $collection[0]->$key;
    }


    return $data;
}

function get_general_settings($key = null)
{


    $data = null;
    if(is_null($key))
    {
        $data = CPanelGeneralSettings::first();
    }
    else{
        $collection = CPanelGeneralSettings::select($key)->first();

        $data = $collection->$key;
    }


    return $data;
}

/**
 * Phase 7 (SEO/GEO): read the global SEO settings singleton (row id = 1).
 *
 * Returns the whole model when $key is null, otherwise the single attribute.
 * Tolerant of a missing table/row (e.g. before the migration runs in some
 * edge cases) so the public theme never fatals on it.
 *
 * @param  string|null  $key
 * @return mixed
 */
function get_seo_settings($key = null)
{
    // Reads go through genealabs/model-caching (Cachable on the model): cached
    // in production, fresh in tests where the cache is disabled. No local
    // static cache — that would leak stale state across requests in-process.
    try {
        $settings = CPanelSeoSettings::first();
    } catch (\Throwable $e) {
        $settings = null;
    }

    if (is_null($settings)) {
        return null;
    }

    if (is_null($key)) {
        return $settings;
    }

    return $settings->$key ?? null;
}

/**
 * GEO settings singleton accessor. Returns the model, a single field, or null.
 * Mirrors get_seo_settings(); reads go through model-caching.
 *
 * @param  string|null  $key
 * @return \App\Http\Models\CPanel\CPanelGeoSettings|mixed|null
 */
function get_geo_settings($key = null)
{
    try {
        $settings = CPanelGeoSettings::first();
    } catch (\Throwable $e) {
        $settings = null;
    }

    if (is_null($settings)) {
        return null;
    }

    if (is_null($key)) {
        return $settings;
    }

    return $settings->$key ?? null;
}

/**
 * Phase 7 (SEO/GEO): render a schema.org JSON-LD block.
 *
 * Encodes with flags that keep slashes/unicode readable and escapes the
 * closing </script> sequence so structured data can never break out of the
 * <script type="application/ld+json"> element.
 *
 * @param  array  $data
 * @return string
 */
function json_ld(array $data): string
{
    $json = json_encode(
        $data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    if ($json === false) {
        return '';
    }

    // Prevent any user-supplied value from terminating the script element.
    $json = str_replace('<', '<', $json);

    return '<script type="application/ld+json">' . $json . '</script>';
}

function get_translated_data_params($entity)
{
    $data = [];
    switch ($entity){
        case 'page':
            $data['model'] = new Page;
            $data['main_table'] = "pages";
            $data['translated_table'] = "page_translations";
            $data['join_column'] = "page_id";
            break;
        case 'post':
            $data['model'] = new Post;
            $data['main_table'] = "posts";
            $data['translated_table'] = "post_translations";
            $data['join_column'] = "post_id";
            break;
        case 'category':
            $data['model'] = new Category;
            $data['main_table'] = "categories";
            $data['translated_table'] = "category_translations";
            $data['join_column'] = "category_id";
            break;
        default:
            break;
    }

    return $data;
}

function get_data(int $id, string $entity, $fields = [])
{
    $params = get_translated_data_params($entity);
    $locale = get_current_lang();

    try{
        $data = $params['model']::join($params['translated_table'], $params['main_table'].'.id', '=', $params['translated_table'].'.'.$params['join_column'])
            ->select($fields)
            ->where($params['translated_table'].'.locale', $locale)
            ->where($params['main_table'].'.id', $id)
            ->with('author')->first();

    } catch (QueryException $e) {
//            dd($e->getMessage());
        throwAbort();
    } catch (PDOException $e) {
//            dd($e->getMessage());
        throwAbort();
    } catch (\Error $e) {
//            dd($e->getMessage());
        throwAbort();
    }



    return $data;

}

function throwNotFound($message = null)
{
    if(is_null($message)) $message = trans('cpanel/controller.page_not_found');
    return abort(404, $message);
}


function throwAbort($message = null)
{
    if(is_null($message)) $message = trans('cpanel/controller.problem_occurred');
    return abort(403, $message);
}


function get_category_posts(array $args, $page = 1)
{
    if(!is_array($args)) return false;




    if(!empty($args['category_id'])){
        $id = $args['category_id'];

        $locale = get_current_lang();

        if(!empty($args['fields'])){
            $fields = $args['fields'];
        }


        if(isset($args['count'])){
            $count = (int) $args['count'];
        }
        else{
            $count = get_general_settings('posts_per_page');
        }


        $data = Post::join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->select($fields)
            ->with('categories')
            ->where('post_translations.locale', $locale)
            ->whereHas('categories', function($query) use($id) {
                $query->select('category_id');
                $query->where('category_id',$id);
            })->paginate($count);


        return $data;
    }

    return false;

}

function get_category_posts_count(int $category_id)
{
    if(!$category_id) return false;

    $category = Category::withCount('posts')->find($category_id);

    if(is_null($category)) return 0;

    return $category->posts_count;
}

function pretty_url($links)
{
    $patterns = '#\?page=#';

    $replacements = '/page/';
    $one = preg_replace($patterns, $replacements, $links);



    $pattern2 = '#page/([1-9]+[0-9]*)/page/([1-9]+[0-9]*)#';
    $replacements2 = 'page/$2';
    $paginate_links = preg_replace($pattern2, $replacements2, $one);

    return $paginate_links;
}

function pretty_search_url($links, string $filter_type, string $string)
{
    $patterns = '#\?page=#';

    $replacements = '/query/'.$string.'/filter/'.$filter_type.'/page/';
    $one = preg_replace($patterns, $replacements, $links);



    $pattern2 = '#/query/'.$string.'/filter/'.$filter_type.'/page/([1-9]+[0-9]*)/query/'.$string.'/filter/'.$filter_type.'/page/([1-9]+[0-9]*)#';
    $replacements2 = '/query/'.$string.'/filter/'.$filter_type.'/page/$2';
    $paginate_links = preg_replace($pattern2, $replacements2, $one);

    return $paginate_links;
}

function check_if_post_liked_by_current_user($post_id):bool
{
    if(!is_logged_in()) return false;

    $result = Auth::user()->likes()->where('post_id', $post_id)->first();

    if(!empty($result)) return true;

    return false;
}

function get_post_comments_count($post_id):int
{
    $result = Comments::where('post_id', $post_id)->count();

    return $result;
}

function get_contact_email():string
{
    return get_general_settings('contact_email');
}

function get_comments_count_per_page():int
{
    $count = get_general_settings('comments_per_page');

    return $count;
}

function get_logged_user_id()
{
    if(is_logged_in()) return Auth()->user()->id;

    return false;
}

function get_logged_user_username()
{
    if(is_logged_in()) return Auth()->user()->username;

    return false;
}



function get_entity_translation_links($type, $id):array
{
    $result = [];

    $locale = get_current_lang();

    $languages_list = config('app.languages_list');

    foreach ($languages_list as $prefix => $data)
    {
        if($prefix === $locale) continue;
        $result[$data['title']] = 'larapress-admin/'.$type.'/'.$id.'/'.$prefix;
    }


    return $result;
}



function get_lang_prefixes()
{
    $languages_list = config('app.languages_list');
    return array_keys($languages_list);
}


function get_translation_links()
{

    $languages = get_languages();
    $language_prefixes = get_lang_prefixes();

    $result = [];
    $route_name = request()->route()->getName();
    $slug = request()->route('slug');
    $default_locale = app()->getLocale();
    $page_locale = request()->route('locale');

    if(!in_array($page_locale, $language_prefixes) && (is_null($slug) || $slug === "/"))
    {
        $slug = $page_locale;
    }


    if(is_null($slug)) $slug = '/';


    switch ($route_name)
    {
        case 'front_pages':
            $model = new PageTranslation;
            $field_name = 'page_id';
            $type = '';
            break;
        case 'posts':
        case 'posts_localized':
            $model = new PostTranslation;
            $field_name = 'post_id';
            $type = 'posts/';
            break;
        case 'categories_first_page':
        case 'categories_display_pages':
        case 'categories_localized':
            $model = new CategoryTranslation;
            $field_name = 'category_id';
            $type = 'category/';
            break;
        default:
            $model = new PageTranslation;
            $field_name = 'page_id';
            $type = '';
            break;
    }


    foreach ($languages as $key => $value)
    {

        $result[$key]['title'] = $value['title'];
        $result[$key]['icon'] = $value['icon'];

        if($key === get_current_lang())
        {
            $result[$key]['url'] = null;
            continue;
        }


        $entity_id = $model->select($field_name)->where('slug', $slug)->first();

        $new_slug = $model->select('slug')->where('locale', $key)->where($field_name, $entity_id->$field_name)->first();

        if(is_null($new_slug))
        {
            $result[$key]['url'] = "/".$key;
            continue;
        }

        $translated_slug = $new_slug->slug;

        $updated_key = null;

        if($key !== $default_locale)
        {
            $updated_key = $key;
            if($slug != "/") $updated_key .='/';
        }

        $result[$key]['url'] = config('app.url').'/'.$updated_key.$type.$translated_slug;


    }


    return $result;
}

function get_current_lang_prefix()
{
    $current_lang = get_current_lang();

    if($current_lang === config('app.locale'))
    {
        $current_lang = null;
    }
    else
    {
        $current_lang .= '/';
    }

    return $current_lang;
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}


function uploadImage($file)
{
    $logged_user_id = get_logged_user_id();

    $imageName = time().'.'.$file->getClientOriginalName();

    $dir = public_path('uploads/avatars/'.$logged_user_id);


    if(deleteDirectory($dir))
    {
        mkdir($dir);

        $path = public_path('uploads/avatars/'.$logged_user_id.'/'.$imageName);


        Image::make($file)
            ->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);

        return asset('uploads/avatars/'.$logged_user_id.'/'.$imageName);
    }

    return false;

}

if (! function_exists('placeholder_image')) {
    /**
     * Public URL of the generic "no image" placeholder (16:9).
     */
    function placeholder_image(): string
    {
        return asset('images/placeholder.svg');
    }
}

if (! function_exists('placeholder_avatar')) {
    /**
     * Public URL of the avatar placeholder (square / round-friendly).
     */
    function placeholder_avatar(): string
    {
        return asset('images/avatar-placeholder.svg');
    }
}

if (! function_exists('image_src')) {
    /**
     * Resolve an image URL for display, falling back to a placeholder when the
     * value is empty/null. This covers the "no value" case; broken or 404ing
     * URLs are caught at render time by the image_fallback() onerror handler.
     *
     * @param  string|null  $url     The stored image URL (may be empty).
     * @param  bool         $avatar  Use the avatar placeholder instead of the generic one.
     */
    function image_src(?string $url, bool $avatar = false): string
    {
        $url = is_string($url) ? trim($url) : '';

        if ($url !== '') {
            return $url;
        }

        return $avatar ? placeholder_avatar() : placeholder_image();
    }
}

if (! function_exists('image_fallback')) {
    /**
     * Ready-to-print `onerror` attribute that swaps a broken image for the
     * placeholder (and clears the handler so it can't loop). Print it raw:
     *   <img src="{{ image_src($url) }}" {!! image_fallback() !!}>
     *
     * @param  bool  $avatar  Use the avatar placeholder instead of the generic one.
     */
    function image_fallback(bool $avatar = false): string
    {
        $placeholder = $avatar ? placeholder_avatar() : placeholder_image();

        return 'onerror="this.onerror=null;this.src=\''.e($placeholder).'\'"';
    }
}