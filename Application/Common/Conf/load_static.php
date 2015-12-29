<?php
/**
 * 静态文件配置
 */
return array(
    'STATIC_CONFIG' => array(
        'global' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/lib/bootstrap/bootstrap.min.css',
                __ROOT__.'/Public/static/css/common/layout.css'
            ),
            'js_head' => array(),
            'js_foot' => array(
                __ROOT__.'/Public/static/js/lib/jquery-1.10.1.min.js',
                __ROOT__.'/Public/static/js/lib/bootstrap/bootstrap.min.js'
            )
        ),
        'home' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/app/home.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'list' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/common/common.css',
                __ROOT__.'/Public/static/css/app/list.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'edu' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/common/common.css',
                __ROOT__.'/Public/static/css/app/detail.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'admin_index' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/admin/common.css',
                __ROOT__.'/Public/static/css/admin/index.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'admin_other' => array(
            'css' => array(
                __ROOT__.'/Public/static/css/admin/common.css',
                __ROOT__.'/Public/static/css/admin/lib/font_awesome.css',
                __ROOT__.'/Public/static/css/admin/lib/style_metro.css',
                __ROOT__.'/Public/static/css/admin/lib/style.css',
                __ROOT__.'/Public/static/css/admin/lib/style_responsive.css',
                __ROOT__.'/Public/static/css/admin/lib/default.css',
                __ROOT__.'/Public/static/css/admin/lib/select2_metro.css',
                __ROOT__.'/Public/static/css/admin/lib/DT_bootstrap.css'
            ),
            'js_head' => array(),
            'js_foot' => array(
                __ROOT__.'/Public/static/js/admin/app.js',
                __ROOT__.'/Public/static/js/admin/common.js'
                
            )
        )
    )
);
