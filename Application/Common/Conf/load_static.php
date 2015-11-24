<?php
/**
 * 静态文件配置
 */
return array(
    'STATIC_CONFIG' => array(
        'global' => array(
            'css' => array(
                './Public/static/css/lib/bootstrap/bootstrap.min.css',
                './Public/static/css/common/common.css'
            ),
            'js_head' => array(),
            'js_foot' => array(
                './Public/static/js/lib/jquery.min.js',
                './Public/static/js/lib/bootstrap/bootstrap.min.js'
            )
        ),
        'home' => array(
            'css' => array(
                './Public/static/css/app/home.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        ),
        'admin' => array(
            'css' => array(
                './Public/static/css/app/admin.css'
            ),
            'js_head' => array(),
            'js_foot' => array()
        )

    )
);
