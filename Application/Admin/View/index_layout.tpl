<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>北辰区考试数据分析平台-后台管理</title>
    <foreach name="adminCss" item="css" >
        <link rel="stylesheet" href="<%$css%>">
    </foreach>
</head>
<body class="bg-admin">
<div id="doc-view" class="bg-top-banner">
    <div id="doc-bd">
        {__CONTENT__}
    </div>
</div>
</body>
<foreach name="adminJs" item="js" >
    <script src="<%$js%>"></script>
</foreach>
</html>