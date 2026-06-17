<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('cpanel-logout');

/*
|--------------------------------------------------------------------------
| Phase 7 (SEO/GEO): public SEO endpoints
|--------------------------------------------------------------------------
| Registered before the front-end catch-all ({locale?}/{slug?}) so they are
| not swallowed by the page router.
*/
Route::get('/sitemap.xml', 'SeoController@sitemap')->name('sitemap');
Route::get('/robots.txt', 'SeoController@robots')->name('robots');
Route::get('/llms.txt', 'SeoController@llms')->name('llms');


/*
|--------------------------------------------------------------------------
| Control Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('larapress-admin')->middleware(['auth', 'see_admin_panel'])->namespace('CPanel')->group(function () {

    Route::get('/locale/{locale}', 'CPanelLanguageController@index')->name('lang_route');

    Route::get('/', 'CPanelHomeController@index')->name('cpanel_home');

    Route::prefix('general-settings')->middleware('manage_general_settings')->group(function(){
        Route::get('/', 'CPanelGeneralSettingController@index')->name('cpanel_general_settings');
        Route::post('/', 'CPanelGeneralSettingController@store')->name('cpanel_update_general_settings');
    });

    Route::prefix('site-options')->middleware('manage_general_settings')->group(function(){
        Route::get('/', 'CPanelSiteOptionsController@index')->name('cpanel_site_options');
        Route::post('/', 'CPanelSiteOptionsController@store')->name('cpanel_update_site_options');
    });

    Route::prefix('seo-settings')->middleware('manage_general_settings')->group(function(){
        Route::get('/', 'CPanelSeoSettingsController@index')->name('cpanel_seo_settings');
        Route::post('/', 'CPanelSeoSettingsController@store')->name('cpanel_update_seo_settings');
    });

    Route::prefix('geo-settings')->middleware('manage_general_settings')->group(function(){
        Route::get('/', 'CPanelGeoSettingsController@index')->name('cpanel_geo_settings');
        Route::post('/', 'CPanelGeoSettingsController@store')->name('cpanel_update_geo_settings');
    });

    // The profile controller resolves the user from Auth when no id is
    // supplied (see CPanelUserController::editUser), so this route can only
    // ever surface the authenticated user's own profile.
    Route::prefix('myprofile')->group(function(){
        Route::get('/', 'CPanelUserController@editUser')->name('cpanel_myprofile');
    });

    Route::prefix('users')->middleware('manage_users')->group(function(){
        Route::get('/', 'CPanelUserController@index')->name('cpanel_all_users_list');
        Route::get('/{id}', 'CPanelUserController@editUser')->name('cpanel_edit_user_profile')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelUserController@updateUser')->name('cpanel_update_user_profile')->where('id', '[0-9]+');
        Route::delete('/{id}/delete', 'CPanelUserController@deleteAjax')->name('cpanel_delete_user')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelUserController@multipleDelete')->name('cpanel_users_bulk_delete');
        Route::get('/new', 'CPanelUserController@addUser')->name('cpanel_add_new_user');
        Route::post('/new', 'CPanelUserController@createUser')->name('cpanel_save_new_user');
    });


    Route::prefix('roles')->middleware('manage_roles')->group(function(){
        Route::get('/', 'CPanelRoleController@index')->name('cpanel_user_roles');
        Route::get('/{id}', 'CPanelRoleController@editRole')->name('cpanel_edit_user_role')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelRoleController@updateRole')->name('cpanel_update_user_role')->where('id', '[0-9]+');
        Route::delete('/{id}/delete', 'CPanelRoleController@deleteAjax')->name('cpanel_delete_user_role')->where('id', '[0-9]+');
        Route::get('/new', 'CPanelRoleController@addRole')->name('cpanel_add_user_role');
        Route::post('/new', 'CPanelRoleController@createRole')->name('cpanel_save_user_role');
    });

    Route::prefix('pages')->middleware('manage_pages')->group(function(){
        Route::get('/', 'CPanelPageController@index')->name('cpanel_pages_list');
        Route::get('/{id}/{lang}', 'CPanelPageController@editPage')->name('cpanel_edit_page')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelPageController@updatePage')->name('cpanel_update_page')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelPageController@multipleDelete')->name('cpanel_pages_bulk_delete');
        Route::delete('/{id}/delete', 'CPanelPageController@deleteAjax')->name('cpanel_ajax_soft_delete_page')->where('id', '[0-9]+');
        Route::get('/new', 'CPanelPageController@addPage')->name('cpanel_add_new_page');
        Route::post('/new/{id?}', 'CPanelPageController@createPage')->name('cpanel_save_new_page')->where('id', '[0-9]+');
    });

    Route::prefix('categories')->middleware('manage_categories')->group(function(){
        Route::get('/', 'CPanelCategoryController@index')->name('cpanel_category_list');
        Route::get('/{id}/{lang}', 'CPanelCategoryController@edit')->name('cpanel_edit_category')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelCategoryController@updateCategory')->name('cpanel_update_category')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelCategoryController@multipleDelete')->name('cpanel_category_bulk_delete');
        Route::delete('/{id}/delete', 'CPanelCategoryController@deleteAjax')->name('cpanel_ajax_soft_delete_category')->where('id', '[0-9]+');
        Route::get('/new', 'CPanelCategoryController@addCategory')->name('cpanel_add_new_category');
        Route::post('/new/{id?}', 'CPanelCategoryController@createCategory')->name('cpanel_save_new_category')->where('id', '[0-9]+');
    });

    Route::prefix('posts')->middleware('manage_posts')->group(function(){
        Route::get('/', 'CPanelPostController@index')->name('cpanel_posts_list');
        Route::get('/trashed', 'CPanelPostController@trashedPosts')->name('cpanel_trashed_posts_list');
        Route::get('/{id}/{lang}', 'CPanelPostController@editPost')->name('cpanel_edit_post')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelPostController@updatePost')->name('cpanel_update_post')->where('id', '[0-9]+');
        Route::get('/{id}/restore', 'CPanelPostController@restore')->name('cpanel_restore_post')->where('id', '[0-9]+');
        Route::delete('/{id}/destroy', 'CPanelPostController@destroyAjax')->name('cpanel_destroy_post')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelPostController@multipleDelete')->name('cpanel_posts_bulk_delete');
        Route::post('/multiple', 'CPanelPostController@multipleActions')->name('cpanel_posts_bulk_action');
        Route::delete('/{id}/delete', 'CPanelPostController@deleteAjax')->name('cpanel_ajax_soft_delete_post')->where('id', '[0-9]+');
        Route::get('/new', 'CPanelPostController@addPost')->name('cpanel_add_new_post');
        Route::post('/new/{id?}', 'CPanelPostController@createPost')->name('cpanel_save_new_post')->where('id', '[0-9]+');
    });

    Route::prefix('menus')->middleware('manage_menus')->group(function(){
        Route::get('/', 'CPanelMenuController@index')->name('cpanel_menu_list');
        Route::get('/{id}/{lang}', 'CPanelMenuController@editMenu')->name('cpanel_edit_menu')->where('id', '[0-9]+');
        Route::put('/{id}/update', 'CPanelMenuController@updateMenu')->name('cpanel_update_menu')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelMenuController@multipleDelete')->name('cpanel_menus_bulk_delete');
        Route::delete('/{id}/delete', 'CPanelMenuController@deleteAjax')->name('cpanel_ajax_soft_delete_menu')->where('id', '[0-9]+');
        Route::get('/new', 'CPanelMenuController@addMenu')->name('cpanel_add_new_menu');
        Route::post('/new/{id?}', 'CPanelMenuController@createMenu')->name('cpanel_save_new_menu')->where('id', '[0-9]+');
    });

    Route::prefix('comments')->middleware('manage_comments')->group(function(){
        Route::get('/', 'CPanelCommentController@index')->name('cpanel_comments_list');
        Route::put('/{id}/approve', 'CPanelCommentController@approve')->name('cpanel_approve_comment')->where('id', '[0-9]+');
        Route::put('/{id}/unapprove', 'CPanelCommentController@unApprove')->name('cpanel_unapprove_comment')->where('id', '[0-9]+');
        Route::delete('/{id}/delete', 'CPanelCommentController@deleteAjax')->name('cpanel_delete_comment')->where('id', '[0-9]+');
        Route::delete('/multipleDelete', 'CPanelCommentController@multipleDelete')->name('cpanel_comments_bulk_delete');
    });

    // Media management is gated behind general-settings permission. There is
    // no dedicated manage_media permission yet; settings is the closest
    // admin-only capability. TODO(phase-4): introduce a manage_media permission
    // (seeder + roles) and switch this guard to it.
    Route::prefix('media')->middleware('manage_general_settings')->group(function(){
        Route::get('/', 'CPanelMediaController@index')->name('cpanel_all_media');
    });

});



/*
|--------------------------------------------------------------------------
| Front End Routes
|--------------------------------------------------------------------------
*/

Route::group([], function () {


    Route::prefix('search')->group(function(){
        Route::get('/', 'PageController@search')->name('get_search_page');
        Route::post('/', 'PageController@searchResult')->name('get_search_result');
        Route::get('/query/{searchstring}/filter/{searchtype}/page/{page}', 'PageController@paginatedResult')->name('search_result_paginated');
    });

    Route::get('/{locale?}/posts/{slug}', 'PostController@languageIndex')->name('posts_localized');

    Route::prefix('/posts')->group(function(){

        Route::get('/{slug}', 'PostController@index')->name('posts');
        Route::post('/handlelike/{id}', 'PostController@handleLike')->middleware('auth')->name('handle_post_likes')->where('id', '[1-9]+[0-9]*');
        Route::put('/handlecomment/', 'PostCommentController@update')->middleware('auth')->name('update_post_comment');
        Route::post('/handlecomment/{id}', 'PostCommentController@store')->middleware('auth')->name('store_post_comments')->where('id', '[1-9]+[0-9]*');
        Route::delete('/deletecomment/{id}', 'PostCommentController@delete')->middleware('auth')->name('delete_post_comments')->where('id', '[1-9]+[0-9]*');

    });


    Route::post('/contact/sendform', 'PageController@sendMail')->name('sendform');

    Route::get('/{locale?}/category/{slug}', 'CategoryController@languageIndex')->name('categories_localized');

    Route::prefix('/category')->group(function(){
        Route::get('/{slug}', 'CategoryController@index')->name('categories_first_page');
        Route::get('/{slug}/page/{page?}', 'CategoryController@index')->name('categories_display_pages')->where('page', '[1-9]+[0-9]*');
    });


    Route::prefix('profile')->middleware('auth')->group(function(){
        Route::get('/edit', 'UserController@yourProfile')->name('get_user_info');
        Route::put('/update', 'UserController@update')->name('update_user_info');
        Route::get('/change_password', 'UserController@password')->name('get_change_password_interface');
        Route::put('/change_password', 'UserController@changePassword')->name('change_password_action');
    });



    Route::get('/users/{username}', 'UserController@show')->name('show_user');

    Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider')->where('provider','twitter|facebook|linkedin|google|github');;
    Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback')->where('provider','twitter|facebook|linkedin|google|github');;

    Route::get('/{locale?}/{slug?}', 'PageController@languageIndex')->name('front_pages');


});







