<?php
/**
 * 静态文件配置
 */
return array(
    'STATIC_CONFIG' => array(
        'global' => array(
            'css' => array(
                __ROOT__ . '/Public/static/css/lib/bootstrap/bootstrap.min.css',
                __ROOT__ . '/Public/static/css/common/layout.css'
            ),
            'js_head' => array(),
            'js_foot' => array(
                __ROOT__ . '/Public/static/js/lib/jquery.min.js',
                __ROOT__ . '/Public/static/js/lib/bootstrap/bootstrap.min.js'
            )
        ),
        'home' => array(
            'css' => array(
                __ROOT__ . '/Public/static/css/app/home.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'list' => array(
            'css' => array(
                __ROOT__ . '/Public/static/css/common/common.css',
                __ROOT__ . '/Public/static/css/app/list.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'edu' => array(
            'css' => array(
                __ROOT__ . '/Public/static/css/common/common.css',
                __ROOT__ . '/Public/static/css/app/detail.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'admin' => array(
            'css' => array(
            	__ROOT__ . '/Public/static/css/admin/common.css',
                __ROOT__ . '/Public/static/css/admin/admin.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        )

    )
);
