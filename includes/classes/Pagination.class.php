<?php
class Pagination
{
    private $page;
    private $total_pages;
    private $num_links;
    private $css_class;
    private $pagevar;
    private $uri;

    public function __construct($current_page, $total_pages, $num_links=10, $css_class=NULL, $pagevar='p', $uri=NULL)
    {
        $this->page = $current_page;
        $this->total_pages = $total_pages;
        $this->num_links = $num_links;
        $this->css_class = $css_class;
        $this->pagevar = $pagevar;
        $this->uri = isset($uri) ? $uri : $_SERVER['REQUEST_URI'];
    }

    private function getCleanUri()
    {
        $uri = preg_replace('/[\?&]' . $this->pagevar . '=\d*/', '', $this->uri);
        return (strpos($uri, '?') === false) ? $uri . '?' . $this->pagevar . '=' : $uri . '&' . $this->pagevar . '=';
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getPages($show_previous_next=true, $show_first_last=true, $show_arrow_text=true)
    {
    	
        if ($this->total_pages == 1)
        {
            return '';
        }

        $paging_uri = $this->getCleanUri();
        $paging_end = $this->page + ceil($this->num_links / 2) - 1;

        if ($paging_end > $this->total_pages)
        {
            $paging_end = $this->total_pages;
        }

        if ($paging_end - $this->num_links > 0)
        {
            $paging_start = $paging_end - $this->num_links + 1;
        }
        else
        {
            $paging_start = 1;
            $paging_end  += $this->num_links - $paging_end;

            if ($paging_end > $this->total_pages)
            {
                $paging_end = $this->total_pages;
            }
        }

        $output = '<ul' . (isset($this->css_class) ? ' class="' . $this->css_class . '"' : '') . '>';

        if ($this->page > 1)
        {
            if ($show_first_last && $this->total_pages > 2)
            {
                $output .= '<li><a href="' . $paging_uri . '1" title="Go to the first page">&laquo;' . ($show_arrow_text ? ' First' : '') . '</a></li>';
            }
            if ($show_previous_next)
            {
                $output .= '<li><a href="' . $paging_uri . ($this->page - 1) . '" title="Go to the previous page">&lsaquo;' . ($show_arrow_text ? ' Prev' : '') . '</a></li>';
            }
        }

        for ($i=$paging_start; $i<=$paging_end; $i++)
        {
        	$separator = ($i == $paging_end) ? "" : "|";
        	
            if ($this->page == $i)
            {
                $output .= '<li><strong>' . $i . ' </strong> | </li>';
            }
            else
            {
                $output .= '<li><a href="' . $paging_uri . $i . '" title="Go to page ' . $i . '"> ' . $i . '</a> ' . $separator . '  </li>';
            }
        }

        if ($this->page < $this->total_pages)
        {
            if ($show_previous_next)
            {
                $output .= '<li><a href="' . $paging_uri . ($this->page + 1) . '" title="Go to the next page">' . ($show_arrow_text ? 'Next ' : '') . '&rsaquo;</a></li>';
            }
            if ($show_first_last && $this->total_pages > 2)
            {
                $output .= '<li><a href="' . $paging_uri . $this->total_pages . '" title="Go to the last page">' . ($show_arrow_text ? 'Last ' : '') . '&raquo;</a></li>';
            }
        }

        $output .= '</ul>';

        return $output;
    }
}
?>