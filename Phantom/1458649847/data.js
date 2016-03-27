var page = require('webpage').create();
var url = 'http://chenhong.bcedu.com/index.php/home/word/datapic?data=eyJcdThiZWRcdThhMDBcdTc3ZTVcdThiYzYiOnsiRzUiOjAuNjMsIkc0IjowLjgsIkczIjowLjY1LCJHMiI6MC41MX0sIlx1NjU4N1x1NWI2Nlx1NWUzOFx1OGJjNlx1NTQ4Y1x1NTQwZFx1NTNlNVx1NTQwZFx1N2JjNyI6eyJHNSI6MC43MywiRzQiOjAuOTIsIkczIjowLjc4LCJHMiI6MC41NH0sIlx1NTNlNFx1NGVlM1x1OGJkN1x1NjU4N1x1OTYwNVx1OGJmYiI6eyJHNSI6MC41OCwiRzQiOjAuNzYsIkczIjowLjYsIkcyIjowLjQ2fSwiXHU3M2IwXHU0ZWUzXHU2NTg3XHU5NjA1XHU4YmZiIjp7Ikc1IjowLjc0LCJHNCI6MC44NiwiRzMiOjAuNzYsIkcyIjowLjY2fSwiXHU1MTk5XHU0ZjVjIjp7Ikc1IjowLjczLCJHNCI6MC43NywiRzMiOjAuNzMsIkcyIjowLjcxfX0%3D&case=line1&sign=d71ed697';
var pic = "D:/webstudy/bcedu/Phantom/1458649847/data.pic.png";

page.open(url, function (status){
    page.render(pic);
    phantom.exit();
});