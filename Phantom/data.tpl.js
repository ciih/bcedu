var page = require('webpage').create();
var url = '{baseurl}/home/word/data';
var pic = '{workdir}/{pic}';

page.open(url, function (status){
    page.render(pic);
    phantom.exit();
});