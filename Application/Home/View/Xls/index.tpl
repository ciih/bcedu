<layout name="index_layout" />
<style>
.excel-data tr:nth-child(odd){background:#fff;}
.excel-data tr:nth-child(even){background:#e5e5e5;}
</style>
<div id="index-login" class="bg-index">
	<table cellpadding="0" cellspacing="1">
		<thead style="background:#ccc;">
			<foreach name="keys" item="vo" >
			    <th style="min-width:100px;text-align:center;"><%$vo%></th>
			</foreach>
		</thead>
		<tbody class="excel-data">
			<foreach name="rets" item="vo" >
				<tr>
					<foreach name="vo" item="vos">
				    	<td style="min-width:100px;text-align:center;"><%$vos%></td>
				    </foreach>
			    </tr>
			</foreach>			
		</tbody>
	</table>
</div>