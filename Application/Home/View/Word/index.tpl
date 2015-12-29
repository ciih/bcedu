<layout name="index_layout" />
<style>
.excel-data tr:nth-child(odd){background:#fff;}
.excel-data tr:nth-child(even){background:#e5e5e5;}
</style>
<div id="index-login" class="bg-index">
  <table cellpadding="0" cellspacing="1">
    <thead style="background:#ccc;">
        <th>知识范畴</th>
        <th>题号</th>
        <th>满分值</th>  
        <th>G5</th>
        <th>G4</th> 
        <th>G3</th>
        <th>G2</th>
    </thead>
    <tbody class="excel-data">
        <tr>
          <td>语言知识</td>
          <td>1,2,3,4,5,22,23,24_1,24_2</td>
          <td>27.00</td>
          <td><%$data1["语言知识"]["G5"]%></td>
          <td><%$data1["语言知识"]["G4"]%></td>
          <td><%$data1["语言知识"]["G3"]%></td>
          <td><%$data1["语言知识"]["G2"]%></td>
        </tr>
        <tr>
          <td>文学常识和名句名篇</td>
          <td>15_1, 15_2, 15_3, 15_4, 15_5,</td>
          <td>5.00</td>
          <td><%$data1["文学常识和名句名篇"]["G5"]%></td>
          <td><%$data1["文学常识和名句名篇"]["G4"]%></td>
          <td><%$data1["文学常识和名句名篇"]["G3"]%></td>
          <td><%$data1["文学常识和名句名篇"]["G2"]%></td>
        </tr>
        <tr>
          <td>古代诗文阅读</td>
          <td>9,10,11,12,13,14_1,14_2,14_3</td>
          <td>28.00</td>
          <td><%$data1["古代诗文阅读"]["G5"]%></td>
          <td><%$data1["古代诗文阅读"]["G4"]%></td>
          <td><%$data1["古代诗文阅读"]["G3"]%></td>
          <td><%$data1["古代诗文阅读"]["G2"]%></td>
        </tr>
        <tr>
          <td>现代文阅读</td>
          <td>6,7,8,16,17,18,19,20,21</td>
          <td>30.00</td>
          <td><%$data1["现代文阅读"]["G5"]%></td>
          <td><%$data1["现代文阅读"]["G4"]%></td>
          <td><%$data1["现代文阅读"]["G3"]%></td>
          <td><%$data1["现代文阅读"]["G2"]%></td>
        </tr>
        <tr>
          <td>写作</td>
          <td>25_1,25_2,25_3,25_4</td>
          <td>60.00</td>
          <td><%$data1["写作"]["G5"]%></td>
          <td><%$data1["写作"]["G4"]%></td>
          <td><%$data1["写作"]["G3"]%></td>
          <td><%$data1["写作"]["G2"]%></td>
        </tr>        
    </tbody>
  </table>
</div>

<img src="data:image/png;base64,<%$image%>"/>
 
</script>
