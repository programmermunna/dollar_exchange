<?php
if(!defined('CryptExchanger_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}

function admin_pagination($query,$ver,$per_page = 10,$page = 1, $url = '?') { 
    	global $db;
		$query = $db->query("SELECT * FROM $query");
    	$total = $query->num_rows;
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination'>";
                
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    				else
    					$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled page-item'>...</li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lpm1' class='page-link'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>$lastpage</a></li>";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li class='page-item'><a href='$ver&page=1' class='page-link'>1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=2' class='page-link'>2</a></li>";
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>...</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>..</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lpm1' class='page-link'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>$lastpage</a></li>";		
    			}
    			else
    			{
    				$pagination.= "<li class='page-item'><a href='$ver&page=1' class='page-link'>1</a></li>";
    				$pagination.= "<li class='page-item'><a href='$ver&page=2' class='page-link'>2</a></li>";
    				$pagination.= "<li class='disabled page-item'><a class='page-link'>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item'><a class='active page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a href='$ver&page=$counter' class='page-link'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li class='page-item'><a href='$ver&page=$next' class='page-link'>Next</a></li>";
                $pagination.= "<li class='page-item'><a href='$ver&page=$lastpage' class='page-link'>Last</a></li>";
    		}else{
    			$pagination.= "<li class='page-item'><a class='disabled page-link'>Next</a></li>";
                $pagination.= "<li class='page-item'><a class='disabled page-link'>Last</a></li>";
            }
    		$pagination.= "</ul>\n";		
    	}
    
    
        return $pagination;
} 

function web_pagination($query,$ver,$per_page = 10,$page = 1, $url = '?') { 
    	global $db;
		$query = $db->query("SELECT * FROM $query");
    	$total = $query->num_rows;
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<nav aria-label='pagination'><ul class='pagination'>";
                
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
    				else
    					$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$counter'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='page-item disabled'>...</li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$lastpage'>$lastpage</a></li>";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/1'>1</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/2'>2</a></li>";
    				$pagination.= "<li class='page-item disabled'><a class='page-link'>...</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='page-item disabled'><a class='page-link'>..</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$lpm1'>$lpm1</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$lastpage'>$lastpage</a></li>";		
    			}
    			else
    			{
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/1'>1</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link' href='$ver/2'>2</a></li>";
    				$pagination.= "<li class='page-item'><a class='page-link'>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
    					else
    						$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$counter'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li class='page-item'><a class='page-link' href='$ver/$next'>Next</a></li>";
                $pagination.= "<li class='page-item'><a class='page-link' href='$ver/$lastpage'>Last</a></li>";
    		}else{
    			$pagination.= "<li class='page-item disabled'><a class='page-link'>Next</a></li>";
                $pagination.= "<li class='page-item disabled'><a class='page-link'>Last</a></li>";
            }
    		$pagination.= "</ul></nav>\n";		
    	}
    
    
        return $pagination;
} 
?>