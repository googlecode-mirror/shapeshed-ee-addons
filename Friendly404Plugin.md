# Introduction #

> This file must be placed in the
> /system/plugins/ folder in your ExpressionEngine installation.

## NAME ##
> SS Friendly 404

## SYNOPSIS ##
> Returns suggestions of weblog entries on a 404 page.

## DESCRIPTION ##
> The plugin matches entries to the last segment of the 404 URL helping users to find pages that match what they were looking for.

> Add the following to your 404 template

```
	{exp:ss_friendly_404}
		{if count == 1}<ul>{/if}
			<li><a href="{auto_path}">{title}</a></li>
		{if count == total_results}</ul>{/if}
	{/exp:ss_friendly_404} 
```

> If no match is found nothing will be shown

### PARAMETERS ###
> The following parameters are available:

> limit - limits the number of entries returned

> e.g `{exp:ss_friendly_404 limit="10"} `
> default: 5

> weblog - limits entries to weblogs defined by their short name

> e.g `{exp:ss_friendly_404 weblog="news|jobs"} `
> default: show all weblogs

### SINGLE VARIABLES ###
> `{title} `

> `{auto_path} `

> `{url_title} `

> `{count} `

> `{total_results} `

> `{weblog_id} `

> `{search_results_url} `


## EXAMPLES ##
> `{exp:ss_friendly_404 limit="10"} `

> Only 10 results will be returned

> `{exp:ss_friendly_404 weblog="news|services"`}

> Only results from the news and services weblogs will be returned

## COMPATIBILITY ##
> ExpressionEngine Version 1.6.x

## SEE ALSO ##
> http://expressionengine.com/forums/viewthread/92908/

## BUGS ##
> http://code.google.com/p/shapeshed-ee-addons/issues/list