<?php
function load_log($file_path,$key_r){
	if(file_exists($file_path)){
		$fp=@fopen($file_path,"r");
		if(!$fp){$err_str='open err '.$file_path;}else{
			while(!feof($fp)){
				$temp = fgets($fp);
				if($temp!="")$buff[]=$temp;
			}fclose($fp);
		}
	}$i=0;
	if(isset($buff) && count($buff)>0){
		foreach($buff as $value){
			$log_tmp=explode("<>",rtrim($value));
			for($j=0;$j<=count($key_r);$j++){
				if($log_tmp[$j])$log_put[$log_tmp[0]][$key_r[$j]]=$log_tmp[$j];
			}$i++;
		}
	}return $log_put;
}
function load_log1($file_path,$type=''){
	if(file_exists($file_path)){
		$fp=@fopen($file_path,"r");
		if(!$fp){$err_str='open err '.$file_path;}else{
			while(!feof($fp)){
				$temp = fgets($fp);
				if($temp!="")$buff[]=$temp;
			}fclose($fp);
		}
	}$i=0;
	if(isset($buff) && count($buff)>0){
		foreach($buff as $value){
			if($type=='html'){
				$value= str_replace("&lt;",'<',$value);
				$value= str_replace("&gt;",'>',$value);
				$value= str_replace("&quot;",'"',$value);
				$value= str_replace("&amp;",'&',$value);
			}$log_put[$i]=$value;$i++;
		}
	}return $log_put;
}
function check($arr,$names='') {
	foreach($names as $name_key => $name_val){
		if(!$arr[$name_key] && $name_val['hissu']=='on')$arr[$name_val['name']]='';
	}
	foreach($arr as $key => $val) {
		if(is_array($val)){
			$key = htmlspecialchars($key);
			foreach($val as $key2 => $val2) {
				if($key=='email' && $val2<>$val[0]){
					$error_name[$key]='on';
				}else if($key=='email' && !ereg("^[a-zA-Z0-9!$&*.=^`|~#%'+\/?_{}-]+@([a-zA-Z0-9_-]+\.)+[a-zA-Z]{2,4}$", $val2) ){
					$error_name[$key]='on';
				}else if($key==$names[$key]['name'] && $names[$key]['hissu']=='on' && !$val2)$error_name[$key]='on';
				$val2 = htmlspecialchars($val2);
				$ret[$key][$key2] = strip_tags($val2);
			}
		}else{
			if($names[$key]['hissu']=='on' && $val==''){
				$error_name[$key]='on';
			}else if($key=='email' && !ereg("^[a-zA-Z0-9!$&*.=^`|~#%'+\/?_{}-]+@([a-zA-Z0-9_-]+\.)+[a-zA-Z]{2,4}$", $val) ){
				$error_name[$key]='on';
			}
			$val = htmlspecialchars($val);
			$ret[$key] = strip_tags($val);
		}
	}	$ret['error']=$error_name;
	return $ret;
}
if(ereg("^DoCoMo",$_SERVER['HTTP_USER_AGENT'])){$tanmatsu='i';
}else if(ereg("^J-PHONE|^Vodafone|^SoftBank|^iPhone",$_SERVER['HTTP_USER_AGENT'])){$tanmatsu='s';
}else if(ereg("^UP.Browser|^KDDI",$_SERVER['HTTP_USER_AGENT'])){$tanmatsu='au';
}else{$tanmatsu='pc';}
switch($tanmatsu){
	case 'i':	case 's':
	case 'au':	if(file_exists('./mb.html'))$html=load_log1('./mb.html','html');
				if(file_exists('./data/res_mb.dat'))$res_tmp=load_log1('./data/res_mb.dat','html');
				$table_s="<dl>\n";	$table_e="</dl>\n";	$tr_s='';$tr_e="\n";	$th_s='<dt';	$th_s2='>';	$th_e='</dt>';
				$td_s='<dd';$td_s2='><font color="#336699">';	$td_e='</font></dd>';
				$td_col2_s='<dd>';$td_col2_e='</dd>';
				break;

	default:	if(file_exists('./pc.html'))$html=load_log1('./pc.html','html');
				if(file_exists('./data/res_pc.dat'))$res_tmp=load_log1('./data/res_pc.dat','html');
				$table_s='<table border="0" cellspacing="0" cellpadding="0" class="preview">'."\n";$table_e="</table>\n";
				$tr_s='<tr>';	$tr_e="</tr>\n";
				$th_s='<th';	$th_s2='>';	$th_e='</th>';	$td_s='<td';	$td_s2='>';	$td_e='</td>';
				$td_col2_s='<td colspan="2">';$td_col2_e='</td>';
				break;
}
if(file_exists('./data/res_kanri.dat'))$res_tmp2=load_log1('./data/res_kanri.dat','html');
if(file_exists('./data/conf.dat'))$conf=load_log('./data/conf.dat',array('key','val') );
foreach($conf as $mes_tmp){
	$mes[$mes_tmp['key']]=$mes_tmp['val'];
}
if(file_exists('./data/user_conf.dat'))$names=load_log('./data/user_conf.dat',array('name','hissu','title','option','') );
$error_div='';
$post=check($_POST,$names);
if($post['reset']){	$post=array('');
}else if($post['error'] && $post['status']){
	unset($post['status']);
	$error_div='<div class="error_message"><font color="#dd3300">'.$mes['error_message'].'</font></div>';
	$error_flg='on';
}else if($post['edit']){unset($post['status']);}
$form_f='<div align="center"><p><a href="http://magical-form.com/" target="blank" style="font-size:10px;text-decoration:none;"><font color="#aaaaaa">Magical Form0.96</font></a></p></div>';
switch($post['status']){
	case 'preview':
		$form='<form action="./" method="POST" id="m-form">'."\n";
		$form.='<div class="pan"><font color="#777777">'.$mes['title_view'].' &gt; <strong><font color="#336699">'.$mes['title_preview'].'</font></strong> &gt; '.$mes['title_send'].'</font></div>';
		$form.=$table_s;
		$count=0;
		foreach($names as $val){
			$option=explode(",",$val['option']);
			if($count==0){$tr_class=' class="top"';}else{$tr_class='';}
			if(is_array($post[$val['name']])){
				$form.=$tr_s.$th_s.$tr_class.$th_s2.$val['title'].$th_e.$td_s.$tr_class.$td_s2;
				foreach($post[$val['name']] as $key2=>$val2){
					if($val['name']=='zip' && $key2=='0'){$zip_c=$mes['zip_mark'];}else{$zip_c='';}
					if($val['name']=='email' && $key2<>'0'){
						$form.=$zip_c.'<input type="hidden" name="'.$val['name'].'['.$key2.']" value="'.$val2.'">';
					}else{
						$form.=$zip_c.'<input type="hidden" name="'.$val['name'].'['.$key2.']" value="'.$val2.'">'.$val2.$option[$key2].' ';
					}
				}$form.=$td_e.$tr_e;
			}else{
				$form.=$tr_s.$th_s.$tr_class.$th_s2.$val['title'].$th_e.$td_s.$tr_class.$td_s2.
				'<input type="hidden" name="'.$val['name'].'" value="'.$post[$val['name']].'">'.$post[$val['name']].$td_e.$tr_e;
			}$count++;
		}
		$form.=''.$td_col2_s.'<div class="message">'.$mes['preview_mess'].'</div>
		<div class="send">
		<input type="hidden" name="status" value="send">'."\n".'<input type="submit" value="'.$mes['button_send'].
		'"> <input type="submit" name="edit" value="'.$mes['button_edit'].'"></div>'.$td_col2_e."\n".$table_e.'</form>';
		foreach($html as $html_tmp){
			if(ereg('<form',$html_tmp) && ereg('id="m-form"',$html_tmp)){
				$flag='on';$html_v.=$form;
			}else if(ereg('</form>',$html_tmp) && $flag=='on'){$flag='';
			}else if($flag=='on'){
			}else{$html_v.=$html_tmp;}
		}
		break;

	case 'send':

	$form='<div id="m-form">'."\n";
	$form.='<div class="pan"><font color="#777777">'.$mes['title_view'].' &gt; '.$mes['title_preview'].' &gt; <strong><font color="#336699">'.$mes['title_send'].'</font></strong></font></div>'."\n";
	$form.=$table_s;
	$form.=$tr_s.$th_s.$th_s2.$mes['title_send'].$th_e.$tr_e.$tr_s.$td_s.$td_s2.$mes['send_text'].$td_e.$tr_e;
	$count=0;
	foreach($names as $val){
		$option=explode(",",$val['option']);
		if($count==0){$tr_class=' class="top"';}else{$tr_class='';}
		$kakunin.='k'.$val['title'].'l'."\n@";
		if(is_array($post[$val['name']])){
			foreach($post[$val['name']] as $key2=>$val2){
				if($val['name']=='name' && $key2==0){$kyaku_name=$val2;}
				if($val['name']=='zip' && $key2=='0'){$zip_c=$mes['zip_mark'];}else{$zip_c='';}
				if($val['name']=='email' && $key2=='0'){
					$kakunin.=$val2;$kyaku_mail=$val2;
				}else if($val['name']<>'email'){
					$kakunin.=$zip_c.''.$val2.$option[$key2].' ';
				}
			}
		}else{
			if($val['name']=='name'){$kyaku_name=$val;}
			if($val['name']=='email'){$kyaku_mail=$val;}
			$kakunin.=$post[$val['name']];
		}$kakunin.="\n\n";$count++;
	}
	$form.=$tr_s.$td_col2_s.'<div class="message">'.$mes['send_mess'].'</div>'.$td_col2_e.$tr_e.$table_e."</div>\n";
	foreach($html as $html_tmp){
		if(ereg('<form',$html_tmp) && ereg('id="m-form"',$html_tmp)){
			$flag='on';$html_v.=$form;
		}else if(ereg('</form>',$html_tmp) && $flag=='on'){$flag='';
		}else if($flag=='on'){
		}else{$html_v.=$html_tmp;}
	}
	$date_now=date( "Y, m/d  (D) H:i:s", time() );
	foreach($res_tmp as $res_val){
		$res_val= str_replace('{name}',$kyaku_name,$res_val);
		$res_val= str_replace('{subject}',$mes['subject'],$res_val);
		$res_val= str_replace('{kaisya_name}',$mes['kaisya_name'],$res_val);
		$res_val= str_replace('{kaisya_mail}',$mes['kaisya_mail'],$res_val);
		$res_val= str_replace('{kaisya_url}',$mes['kaisya_url'],$res_val);
		$res_val= str_replace('{kakunin}',$kakunin,$res_val);
		$res.=$res_val;
	}
	foreach($res_tmp2 as $res_val){
		$res_val= str_replace('{name}',$kyaku_name,$res_val);
		$res_val= str_replace('{subject}',$mes['subject'],$res_val);
		$res_val= str_replace('{kaisya_name}',$mes['kaisya_name'],$res_val);
		$res_val= str_replace('{kaisya_mail}',$mes['kaisya_mail'],$res_val);
		$res_val= str_replace('{kaisya_url}',$mes['kaisya_url'],$res_val);
		$res_val= str_replace('{kakunin}',$kakunin,$res_val);
		$res_val= str_replace('{DATE_NOW}',$date_now,$res_val);
		$res_val= str_replace('{SERVER_NAME}',$_SERVER['SERVER_NAME'],$res_val);
		$res_val= str_replace('{SCRIPT_NAME}','http://'.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"],$res_val);
		$res_val= str_replace('{USER_AGENT}',$_SERVER['HTTP_USER_AGENT'],$res_val);
		$res_val= str_replace('{HOST}',gethostbyaddr($_SERVER["REMOTE_ADDR"]),$res_val);
		$res_val= str_replace('{REMOTE_ADDR}',$_SERVER['REMOTE_ADDR'],$res_val);
		$res_kanri.=$res_val;
	}
	switch($mes['char_set']){
		case 'Shift_JIS':
		case 'SJIS':		$char_set='SJIS';			break;
		default:			$char_set=$mes['char_set'];	break;
	}
	$res_kanri=mb_convert_kana($res_kanri,"KV",$char_set);
	$res=mb_convert_kana($res,"KV",$char_set);
	mb_language("ja");
	mb_internal_encoding($char_set);
	$mail_header="From: ".mb_encode_mimeheader($mes['kaisya_name'])."<".$mes['kaisya_mail'].">";
	$kanri_header="From: <".$kyaku_mail.">";

	mb_send_mail($kyaku_mail,$mes['subject'],$res,$mail_header);
	mb_send_mail($mes['kaisya_mail'],$mes['subject'],$res_kanri,$kanri_header);
	break;
	default:

	foreach($html as $html_tmp){
		$val=$key=$hissu_mess=$error_css='';
		foreach($post as $key=>$val){
			if(is_array($val)){
				$key2=$val2='';
				foreach($val as $key2 => $val2){
					if(preg_match("/$key\[$key2\]/",$html_tmp) ){
						if($post['error'][$key]=='on' && !$post['edit'] && $error_flg=='on'){
							$hissu_mess=$mess['hissu'];
							$error_css=' style="background:#f9e5e5;" ';
						}else{	$hissu_mess=$error_css='';}

						if(ereg('type="text"',$html_tmp) ){
							$html_tmp= str_replace('name="'.$key.'['.$key2.']"','name="'.
							$key.'['.$key2.']" value="'.$val2.'"'.$error_css,$html_tmp);
						}else if(ereg('type="checkbox"',$html_tmp) ){
							$html_tmp= str_replace('value="'.$val2.'"','value="'.$val2.'" checked',$html_tmp);
						}else if(ereg('<textarea ',$html_tmp) ){
							$html_tmp= str_replace('></textarea>','>'.$val2.'</textarea>',$html_tmp);
						}else if(ereg('<select ',$html_tmp) ){
							$select_flg=$key.$key2;
						}else if(ereg('</select>',$html_tmp) ){	$select_flg='';}
					}
					if(ereg('<option value=',$html_tmp) && $select_flg==$key.$key2){
						$html_tmp= str_replace('"'.$val2.'">'.$val2.'</option>','"'.$val2.'" selected>'.$val2.'</option>',$html_tmp);
					}else if($select_flg==$key.$key2){
						$html_tmp= str_replace('>'.$val2.'</option>',' value="'.$val2.'" selected>'.$val2.'</option>',$html_tmp);
					}
				}
			}else{
				if(ereg('name="'.$key.'"',$html_tmp) ){
					if($post['error'][$key]=='on' && !$post['edit'] && $error_flg=='on'){
						$hissu_mess=$mess['hissu'];
						$error_css=' style="background:#f9e5e5;" ';
					}else{	$hissu_mess=$error_css='';}

					if(ereg('type="text"',$html_tmp) ){
						$html_tmp= str_replace('name="'.$key.'"','name="'.$key.'" value="'.$val.'"'.$error_css,$html_tmp);
					}else if(ereg('type="radio"',$html_tmp) ){
						$html_tmp= str_replace('value="'.$val.'"','value="'.$val.'" checked',$html_tmp);
					}else if(ereg('<textarea ',$html_tmp) ){
						$html_tmp= str_replace('></textarea>',$error_css.'>'.$val.'</textarea>',$html_tmp);
					}else if(ereg('<select ',$html_tmp) ){
						$select_flg='on';
					}else if(ereg('</select>',$html_tmp) ){
						$html_tmp= str_replace('</select>','</select>'.$error_mess,$html_tmp);
						$select_flg='';
					}
				}
				if($select_flg=='on'){
					if(ereg('<option value=',$html_tmp)){
						$html_tmp= str_replace('"'.$val.'">'.$val.'</option>','"'.$val.'" selected>'.$val.'</option>',$html_tmp);
					}else{
						$html_tmp= str_replace('>'.$val.'</option>',' value="'.$val.'" selected>'.$val.'</option>',$html_tmp);
					}
				}
			}
		}
		$html_tmp= str_replace("</form>",'<input type="hidden" name="status" value="preview"></form>'.$form_f,$html_tmp);
		$html_v.=$html_tmp;
		if(ereg('<form',$html_tmp) && ereg('id="m-form"',$html_tmp)){
			$html_v.='<div class="pan"><font color="#777777"><strong><font color="#336699">'.$mes['title_view'].'</font></strong> &gt; '.$mes['title_preview'].
			' &gt; '.$mes['title_send'].'</font></div>'.$error_div."\n";
		}
	}
	break;
}
echo $html_v;
?>