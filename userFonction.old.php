else {
		$pc="";
		$spy="<a href=\"javascript:void(0)\" onclick=\"affiche_cache('spy_".$rubrique."_".$artid."');\">SPY</a>
		<table id=\"spy_".$rubrique."_".$artid."\" style=\"display:none; position:absolute; z-index:1002; background-color: white; width:300px; border: solid thin black; border-collapse: collapse;\">";
		$spy.="<tr class=\"td_alt1\">
		<td width=\"25%\" align=\"center\"><b>Site</b></td>
		<td width=\"25%\" align=\"center\"><b>PV TTC</b></td>
		<td align=\"center\" width=\"25%\"><b>Diff %</b></td>
		<td align=\"center\" width=\"25%\" nowrap><b>Marge alignée</b></td>
		</tr>
		<tr  class=\"td_alt2\">
		<td>MCAV</td>
		<td align=\"right\">".number_format($article->prix,2,',',' ')." &euro;</td>
		<td align=\"center\">-</td>
		<td align=\"center\">-</td>
		</tr>";
		$ret['spy_site']="-";
		$ret['spy_prix']="-";
		$prix_spy_ht_trspt=0;
		$i=0;
		$cinqmois = strtotime('-5 months',time());
		$requete=$DB_site2->query("	SELECT h.* FROM history AS h 
			WHERE h.artcode='".$article->artcode."' 
			AND h.timestamp>".$cinqmois." 
			GROUP BY site
			ORDER BY price ASC");
		while($data=$DB_site2->fetch_array($requete)){
			if($i==0){
				$ret['spy_site']=$data['site'];
				$ret['spy_prix']=floatval($data['price']);
			}
			if(floatval($article->prix)!=0){
				$pc=round(-(100-100*$data['price']/$article->prix),2)."%";
			}
			$prix_spy_ht_trspt = ($data['price'] / $tvanormal) + $frais_port->prixht;
			$marge_aligne_ht = $prix_spy_ht_trspt - $article_paht_total;
			$marge_aligne_pct = $marge_aligne_ht * 100 / $prix_spy_ht_trspt;
			$rowalt=($i%2)+1;
			$spy.="<tr class=\"td_alt".$rowalt."\">
			<td>".$data['site']."</td>
			<td align=\"right\">".number_format($data['price'],2,'.',' ')." &euro;</td>
			<td align=\"right\" style=\"color:". ($data['price'] < $article->prix ? "red" : "green") .";\">$pc</td>
			<td align=\"right\">".couleur_marge($marge_aligne_pct)."</td>
			</tr>";
			$i++;
		}
		$ret['spy_nb']=$i;
		if($ret['spy_nb']>0){
			if($ret['spy_prix']<$prixtotal){
				$ret['spy_marge']=0; // marge à calculer ....
				$prix_spy_ht_trspt = ($ret['spy_prix'] / $tvanormal) + $frais_port->prixht;
				$marge_aligne_ht = $prix_spy_ht_trspt - $article_paht_total;
				$marge_aligne_pct = $marge_aligne_ht * 100 / $prix_spy_ht_trspt;
				$ret['spy_marge']=number_format($marge_aligne_ht, 0, ',', ' ')." &euro;<br>".couleur_marge($marge_aligne_pct);
				$ret['spy_prix']=number_format($ret['spy_prix'],2,'.',' ');
				if($admin_salon==false) {
					$ret['rows']['Concurrents']   		= $ret['spy_nb'] . " <a href=\"export/export_marge_article.php?artids=".$artid."&active[]=0&active[]=1&active[]=2&active[]=3\" target=\"_blank\">Voir</a>";
				}
				$ret['rows']['Site + bas']   		= $ret['spy_site'];
				$ret['rows']['Prix + bas']   		= $ret['spy_prix'];
				$ret['rows']['Marge alignée']   	= $ret['spy_marge'];
			} else {
				$ret['spy_marge']="-";
				$ret['spy_prix']="-";
				$ret['spy_site']="-";
				if($admin_salon==false) {
					$ret['rows']['Concurrents']   		= $ret['spy_nb'] . " <a href=\"export/export_marge_article.php?artids=".$artid."&active[]=0&active[]=1&active[]=2&active[]=3\" target=\"_blank\">Voir</a>";
				}
			}
		} else {
			$ret['spy_marge']="-";
			if($admin_salon==false) {
				$ret['rows']['Concurrents']   			= $ret['spy_nb'] . " <a href=\"export/export_marge_article.php?artids=".$artid."&active[]=0&active[]=1&active[]=2&active[]=3\" target=\"_blank\">Voir</a>";
			}
		}
		$spy.="</table>";
		if($DB_site2->num_rows($requete)==0){
			$spy="-";
		}
		$ret['spy']=$spy;
	}
	
	$ret['lockit']="";
	if($article->arguments()){
		$ret['lockit']="<img src=\"images/lockit.jpg\" class=\"picto\" width=\"30\" title=\"".$article->argument."\">";
	}
	$ret['files']="";
	$nbfile=$DB_site2->query_first("SELECT COUNT(*) FROM article_files WHERE artid=".intval($article->artid));
	if($nbfile[0]>0){
		$ret['files'].="<img src=\"images/icon/pdf.png\" width=\"30\"  class=\"picto\" onclick=\"fb.show('article_files.php?artid=".$article->artid."&parent=0')\">";
	}
	return $ret;
}

?>
utilisateur_fonction.php
Page 1 sur 2 