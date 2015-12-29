var page = require('webpage').create();
var url = '{baseurl}/home/word/datapic?data={data}&case={case}&sign={sign}';
var pic = '{workdir}/{pic}';

page.open(url, function (status){
    page.render(pic);
    phantom.exit();
});