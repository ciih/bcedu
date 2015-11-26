<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>北辰区考试数据分析平台</title>
    <foreach name="loadCss" item="css" >
        <link rel="stylesheet" href="<%$css%>">
    </foreach>
</head>
<body>
<div class="wrapper">
    <div id="doc-view">
        <include file="Public/topbar" />
        <div id="doc-hd">
        	<include file="Public/header" />
        </div>
        <div id="doc-bd">
            <div class="container">
                {__CONTENT__}
            </div>
        </div>
        <div id="doc-ft">
        	<include file="Public/footer" />
        </div>
    </div>
</div>
</body>
<foreach name="loadJs" item="js" >
    <script src="<%$js%>"></script>
</foreach>
</html>