<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>北辰区考试数据分析平台-后台管理</title>
    <foreach name="adminCss" item="css" >
        <link rel="stylesheet" href="<%$css%>">
    </foreach>
</head>
<body class="page-header-fixed">

    <include file="Public/header" />

    <div class="page-container">

        <include file="Public/sider" />

        <div class="page-content clearfix">
            
            {__CONTENT__}

        </div>

    </div>

    <include file="Public/footer" />

</div>
</body>
<foreach name="adminJs" item="js" >
    <script src="<%$js%>"></script>
</foreach>
<script type="text/javascript">

    jQuery(document).ready(function() {    

       App.init(); // initlayout and core plugins

    });

</script>
</html>