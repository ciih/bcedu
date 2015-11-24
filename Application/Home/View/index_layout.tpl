<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>北辰区考试数据分析平台</title>
    <foreach name="homeCss" item="css" >
        <link rel="stylesheet" href="<%$css%>">
    </foreach>
</head>
<body>
<div class="wrapper">
    <div id="doc-view">
        <div id="doc-bd">
            {__CONTENT__}
        </div>
    </div>
</div>
</body>
<foreach name="homeJs" item="js" >
    <script src="<%$js%>"></script>
</foreach>
</html>