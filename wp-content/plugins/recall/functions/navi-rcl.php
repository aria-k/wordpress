<?php
class RCL_navi{

	public $inpage;
	public $navi;
	public $cnt_data;
	public $num_page;
	public $get;
	public $page;
	public $offset;
	
	function __construct($inpage,$cnt_data,$get=false,$page=false){
		if($_GET['navi']) $this->navi = $_GET['navi'];
		else $this->navi=1;
		if($page) $this->navi = $page;
		$this->inpage = $inpage;
		$this->cnt_data = $cnt_data;
		$this->get = $get;
		$this->offset = ($this->navi-1)*$this->inpage;
		$this->limit();
	}
	
	function limit(){				
		$limit_us = $this->offset.','.$this->inpage;
        $this->num_page = ceil($this->cnt_data/$this->inpage);
		return $limit_us;
	}
	
	function navi(){		
		global $post,$group_id;
		
		if($group_id){
			$prm = get_term_link((int)$group_id,'groups' );
			if($_GET['group-page']) $prm = get_redirect_url_rcl($prm).'group-page='.$_GET['group-page'];
		}else{ $prm = get_permalink($post->ID);}
		
		$prm = get_permalink($post->ID);

		if($this->inpage&&$this->cnt_data>$this->inpage){
			
			$redirect_url = get_redirect_url_rcl($prm);

            $page_navi = '<div class="user-navi">';
            $next = $this->navi + 3;
            $prev = $this->navi - 4;
            if($prev==1) $page_navi .= '<a href="'.$redirect_url.'navi=1'.$this->get.'">1</a>';
            for($a=1;$a<=$this->num_page;$a++){
                if($a==1&&$a<=$prev&&$prev!=1) $page_navi .= '<a href="'.$redirect_url.'navi=1'.$this->get.'">1</a> ... ';                   
                if($prev<$a&&$a<=$next){
                    if($this->navi==$a) $page_navi .= '<span>'.$a.'</span>';
                    else $page_navi .= '<a href="'.$redirect_url.'navi='.$a.''.$this->get.'">'.$a.'</a>';
                }                              
            }
            if($next<$this->num_page&&$this->num_page!=$next+1) $page_navi .= ' ... <a href="'.$redirect_url.'navi='.$this->num_page.''.$this->get.'">'.$this->num_page.'</a>';
            if($this->num_page==$next+1) $page_navi .= '<a href="'.$redirect_url.'navi='.$this->num_page.''.$this->get.'">'.$this->num_page.'</a>';
            $page_navi .= '</div>';
        }
                   
        return $page_navi;
	}
}

function navi_rcl($inpage,$cnt_data,$num_page,$page=false,$get=false){
		
		global $post,$group_id;
		if($_GET['navi']) $navi = $_GET['navi'];
        else $navi=1;
		if($page) $navi = $page;
		
		if($group_id){
			$prm = get_term_link((int)$group_id,'groups' );
			if($_GET['group-page']) $prm = get_redirect_url_rcl($prm).'group-page='.$_GET['group-page'];
		}else{ $prm = get_permalink($post->ID);}

		if($inpage&&$cnt_data>$inpage){
			
			$redirect_url = get_redirect_url_rcl($prm);

            $page_navi = '<div class="user-navi">';
            $next = $navi + 3;
            $prev = $navi - 4;
            if($prev==1) $page_navi .= '<a href="'.$redirect_url.'navi=1'.$get.'">1</a>';
            for($a=1;$a<=$num_page;$a++){
                if($a==1&&$a<=$prev&&$prev!=1) $page_navi .= '<a href="'.$redirect_url.'navi=1'.$get.'">1</a> ... ';                   
                if($prev<$a&&$a<=$next){
                    if($navi==$a) $page_navi .= '<span>'.$a.'</span>';
                    else $page_navi .= '<a href="'.$redirect_url.'navi='.$a.''.$get.'">'.$a.'</a>';
                }                              
            }
            if($next<$num_page&&$num_page!=$next+1) $page_navi .= ' ... <a href="'.$redirect_url.'navi='.$num_page.''.$get.'">'.$num_page.'</a>';
            if($num_page==$next+1) $page_navi .= '<a href="'.$redirect_url.'navi='.$num_page.''.$get.'">'.$num_page.'</a>';
            $page_navi .= '</div>';
        }
                   
        return $page_navi;
}

function get_pagenavi_ajax_rcl($userid,$post_type){
	global $wpdb;

	$count = $wpdb->get_var("SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_author='$userid' AND post_type='$post_type' AND post_status NOT IN ('draft','auto-draft')");
	if(!$count) return false;
	$in_page = 20;
	$pages = ceil($count/$in_page);

	$navi .= '<div class="pagenavi-rcl">';
	for($a=0;$a<$pages;$a++){
		$navi .= '<a type="'.$post_type.'" data="'.$a*$in_page.'" class="sec_block_button';
		if($a==0)$navi .= ' active';
		$navi .= '" href="#">'.++$page.'</a>';
	}
	$navi .= '</div>';
	return $navi;
}

function admin_navi_rcl($inpage,$cnt_data,$page,$page_id,$get_data){

	if($_GET['paged']) $page = $_GET['paged'];
	else $page=1;

	$num_page = ceil($cnt_data/$inpage);

	$prev = $page-1;
	$next = $page+1;
	$pagination .= '<div class="tablenav">
		<div class="tablenav-pages">
			<span class="pagination-links">';
				
			if($page!=1)$pagination .= '<a class="first-page" href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.$page_id.''.$get_data.'" title="Перейти на первую страницу">«</a>
			<a class="prev-page" href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.$page_id.''.$get_data.'&paged='.$prev.'" title="Перейти на предыдущую страницу">‹</a>';
			$pagination .= '<span class="paging-input">
				'.$page.' из <span class="total-pages">'.$num_page.'</span>
			</span>';
			if($page!=$num_page)$pagination .= '<a class="next-page" href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.$page_id.''.$get_data.'&paged='.$next.'" title="Перейти на следующую страницу">›</a>
			<a class="last-page" href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page='.$page_id.''.$get_data.'&paged='.$num_page.'" title="Перейти на последнюю страницу">»</a>
			
			</span>
		</div>
	</div>
	<input type="button" value="Назад" onClick="history.back()">';
	
	return $pagination;
}
?>