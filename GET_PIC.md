# 这是什么 

解释如何安装配置以到达动态生成图片


＃ 安装phatomjs

- 安装nodejs 在 [nodejs主页](https://nodejs.org/en/) 下载4.2.X版本的nodejs
- 使用命令 `npm install -g nrm --registry=https://registry.npm.taobao.org --verbose` 安装nrm，用以应付GFW对npm源的屏蔽
- 使用命令 `nrm use taobao` 切换node源到淘宝
- 使用命令 `npm install -g phantomjs --verbose` 用以安装phantomjs

# 配置项目

- 打开Tmp/Template.docx 此文件是默认的模版。
- 修改${value}, 此变量将被php的同名变量替换。
- 对于图片右击，设置alt_text, word好像叫“额外文字”，如设置为${placeholder}，则此图片将会被替换。
- 打开WordController.php 修改49行为你的域名，index.php可以不加。
- 83～105行为对应的变量替换。要与word中的变量对应
- 109行为图片的替换，既是上面设置的“额外文字”。对应即可。


# 测试

访问 /home/word/ 即可

