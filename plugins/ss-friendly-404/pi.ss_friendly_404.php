<?php
#
# This file must be placed in the
# /system/plugins/ folder in your ExpressionEngine installation.
#
# NAME
#		SS Friendly 404 
#
# SYNOPSIS
#		Returns suggestions of weblog entries on a 404 page. 	
#
# DESCRIPTION:	
#		The plugin matches entries to the last segment of the 404 URL. 
#		Add the following tag to your 404 template
#
#		{exp:ss_friendly_404}
#
#		The following options are available:
#
#		limit		limits the number of entries returned
#					e.g {exp:ss_friendly_404 limit="10"}
#					default: 5
#
#		weblog		limits entries to weblogs defined by their short name
#					e.g {exp:ss_friendly_404 weblog="news"}	
#					default: show all weblogs
#
#		title		outputs a title before the list of suggested articles
#					e.g {exp:ss_friendly_404 title="<h3>Perhaps you were looking for</h3>"}	
#					default: none
#
# EXAMPLES
#		{exp:ss_friendly_404 limit="10"}
#		10 results will be returned	
#
#		{exp:ss_friendly_404 weblog="news|services"}
#		Only results from the news and services weblogs will be returned
#
# COMPATIBILITY
#		ExpressionEngine Version 1.6.x 
#	
# SEE ALSO
#		http://code.google.com/p/shapeshed-ee-addons/wiki/Friendly404Plugin
#
# BUGS
#		http://code.google.com/p/shapeshed-ee-addons/issues/list
#

$plugin_info = array(
						'pi_name'			=> 'SS Friendly 404',
						'pi_version'		=> '1.0.0',
						'pi_author'			=> 'George Ornbo, Shape Shed',
						'pi_author_url'		=> 'http://shapeshed.com/',
						'pi_description'	=> 'Returns suggestions of weblog entries for a 404 page',
						'pi_usage'			=> Ss_friendly_404::usage()
					);

class Ss_friendly_404{

	var $return_data;

	function Ss_friendly_404() 
	    {

			global $TMPL, $DB, $IN;	

			 /** ----------------------------------
			 /**  Get the last segment of the 404 URL 
			 /**  Get any parameters and set defaults if none		
			 /** ----------------------------------*/

			$search_segment = end($IN->SEGS);
			$limit = ( ! $TMPL->fetch_param('limit')) ? '5' : $TMPL->fetch_param('limit');
			$weblog = ( ! $TMPL->fetch_param('weblog')) ? '' : $TMPL->fetch_param('weblog');
			$title = ( ! $TMPL->fetch_param('title')) ? '' : $TMPL->fetch_param('title');
			
			/** ----------------------------------
			/**  Build weblog query based on parameters
			/** ----------------------------------*/

			$weblog_str = "";
			if ($weblog != "") 
				{
				$count = 1; 
				$weblogs = explode("|", $weblog);
				foreach ($weblogs as $weblog_name) 
					{
					if ($count == 1) 
						{
							$weblog_str .= " AND ( w.blog_name = '".$DB->escape_str($weblog_name)."'";
							$count++;
						}
					else 
						{
							$weblog_str .= " OR w.blog_name = '".$DB->escape_str($weblog_name)."'";
						}
					} 
				$weblog_str .= " )";
				} 	
				
			/** ----------------------------------
			/**  Query the database
			/** ----------------------------------*/
			
		    $query = $DB->query("SELECT t.entry_id, t.title, t.url_title, t.weblog_id, w.search_results_url FROM exp_weblog_titles AS t
						LEFT JOIN exp_weblogs AS w ON t.weblog_id = w.weblog_id 
						WHERE t.entry_date < UNIX_TIMESTAMP()
						$weblog_str
						AND (t.expiration_date = 0 || t.expiration_date > UNIX_TIMESTAMP())
						AND (t.title LIKE '%".$DB->escape_str($search_segment)."%')
						AND t.status = 'Open' AND t.status != 'closed'
						ORDER BY t.sticky desc, t.entry_date desc, t.entry_id desc
						LIMIT 0, ".$DB->escape_str($limit)."");
								
	        /** ----------------------------------
	        /**  Loop through results and make the results available 
	        /** ----------------------------------*/
				
			$total_results = sizeof($query->result);			

			foreach($query->result as $count => $row)
			{
			    $tagdata = $TMPL->tagdata;
			
	            $row['count']			= $count+1;
	            $row['total_results']	= $total_results;

			    foreach ($TMPL->var_single as $key => $val)
			    {
			        if (isset($row[$val]))
			        {
			            $tagdata = $TMPL->swap_var_single($val, $row[$val], $tagdata);
			        }
			    }

			    $this->return_data .= $tagdata; 
			}	
			

		}

	/** ----------------------------------
	/**  Plugin usage
	/** ----------------------------------*/
	
	function usage()
	{
	return '
	NAME
	=======================
			SS Friendly 404 

	SYNOPSIS
	=======================
			Returns suggestions of weblog entries on a 404 page. 	

	DESCRIPTION
	=======================		
			The plugin matches entries to the last segment of the 404 URL. 

			Add the following tag to your 404 template

			{exp:ss_friendly_404}

			The following options are available:

			limit -		limits the number of entries returned

						e.g {exp:ss_friendly_404 limit="10"}
						default: 5

			weblog -	limits entries to weblogs defined by their short name

 						e.g {exp:ss_friendly_404 weblog="news"}	
						default: show all weblogs
						
			title -		outputs a title before the list of articles

 						e.g {exp:ss_friendly_404 title="<h3>Perhaps you were looking for</h3>"}	
						Outputs an HTML title and then the list of articles

	EXAMPLES
	=======================
			{exp:ss_friendly_404 limit=\"10\"}
			10 results will be returned	

			{exp:ss_friendly_404 weblog=\"news|services\"}
			Only results from the news and services weblogs will be returned

	COMPATIBILITY
	=======================
			ExpressionEngine Version 1.6.x 
	
	SEE ALSO
	=======================
			http://code.google.com/p/shapeshed-ee-addons/wiki/Friendly404Plugin

	BUGS
	=======================
			http://code.google.com/p/shapeshed-ee-addons/issues/list';

	}
	
}

?>