<?php
#
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
#		
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

/**
*
* @version 1.0.0
* @author George Ornbo <http://shapeshed.com/>
* @license {@link http://www.opensource.org/licenses/mit-license.php} 
*
*/

/**
* Plugin information used by ExpressionEngine
* @global array $plugin_info
*/
$plugin_info = array(
						'pi_name'			=> 'SS Friendly 404',
						'pi_version'		=> '1.0.0',
						'pi_author'			=> 'George Ornbo, Shape Shed',
						'pi_author_url'		=> 'http://shapeshed.com/',
						'pi_description'	=> 'Returns suggestions of weblog entries for a 404 page',
						'pi_usage'			=> Ss_friendly_404::usage()
					);

class Ss_friendly_404{

	/**
	* Returned string
	* @var array
	*/
    var $return_data;

	/**
	* Function to find relevant posts in relation to the last segment of the 404 URL
	* @access	public
	*/
	function Ss_friendly_404() 
	    {

	        global $TMPL, $DB, $IN;	

			// Get the last segment of the 404 URL into a variable
			$search_segment = end($IN->SEGS);

			// Fetch parameters and set default if we need to
			$limit = ( ! $TMPL->fetch_param('limit')) ? '5' : $TMPL->fetch_param('limit');
			$weblog = ( ! $TMPL->fetch_param('weblog')) ? '' : $TMPL->fetch_param('weblog');

			// Build weblog query
			$weblog_str = '';
			if ($weblog != "") 
				{
				$count = 1; 
				$weblogs = explode('|', $weblog);
				foreach ($weblogs as $weblog_name) 
					{
					if ($count == 1) 
						{
							$weblog_str .= " AND ( w.blog_name = '$weblog_name'";
							$count++;
						}
					else 
						{
							$weblog_str .= " OR w.blog_name = '$weblog_name'";
						}
					} 
				$weblog_str .= " )";
				} 	

			// Now for the SQL
		    $query = $DB->query("SELECT t.entry_id, t.title, t.url_title, t.weblog_id, w.search_results_url FROM exp_weblog_titles AS t
						LEFT JOIN exp_weblogs AS w ON t.weblog_id = w.weblog_id 
						WHERE t.entry_date < UNIX_TIMESTAMP()
						$weblog_str
						AND (t.expiration_date = 0 || t.expiration_date > UNIX_TIMESTAMP())
						AND (t.title LIKE '%".$DB->escape_str($search_segment)."%')
						AND t.status = 'open' AND t.status != 'closed'
						ORDER BY t.sticky desc, t.entry_date desc, t.entry_id desc
						LIMIT 0, ".$DB->escape_str($limit)."");
								
			// If we have a result open the ul
			if ($query->num_rows > 0)
				{
					$this->return_data .= '<ul>';	
				}

			// For each result spit out an li
		    foreach($query->result as $row)
		    	{
		      		$this->return_data .="\n\t\t".'<li><a href="'.$row['search_results_url'].''.$row['url_title'].'">'.$row['title'].'</a></li>';
		    	}

			// If we have a result close the ul		
			if ($query->num_rows > 0)
				{
					$this->return_data .= "\n\t".'</ul>';	
				}

		}

	/**
	* Plugin usage documentation
	*
	* @return	string Plugin usage instructions
	*/
	function usage()
	{
	return "
	NAME
			SS Friendly 404 

	SYNOPSIS
			Returns suggestions of weblog entries on a 404 page. 	

	DESCRIPTION:		
			The plugin matches entries to the last segment of the 404 URL. 
			Add the following tag to your 404 template

			{exp:ss_friendly_404}

			The following options are available:

			limit		limits the number of entries returned
						e.g {exp:ss_friendly_404 limit=\"10\"}
						default: 5

			weblog		limits entries to weblogs defined by their short name
						e.g {exp:ss_friendly_404 weblog=\"news\"}	
						default: show all weblogs

	EXAMPLES
			{exp:ss_friendly_404 limit=\"10\"}
			10 results will be returned	

			{exp:ss_friendly_404 weblog=\"news|services\"}
			Only results from the news and services weblogs will be returned

	COMPATIBILITY
			ExpressionEngine Version 1.6.x 
	
	SEE ALSO
			http://code.google.com/p/shapeshed-ee-addons/wiki/Friendly404Plugin

	BUGS
			http://code.google.com/p/shapeshed-ee-addons/issues/list
";

	}
	
}

?>