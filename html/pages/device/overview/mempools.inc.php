<?php

$graph_type = "mempool_usage";

$mempools = dbFetchRows("SELECT * FROM `mempools` WHERE device_id = ?", array($device['device_id']));

if (count($mempools))
{
  echo("<div style='background-color: #eeeeee; margin: 5px; padding: 5px;'>");
  echo("<p style='padding: 0px 5px 5px;' class=sectionhead>");
  echo('<a class="sectionhead" href="device/device='.$device['device_id'].'/tab=health/metric=mempool/">');
  echo("<img align='absmiddle' src='images/icons/memory.png'> Memory Pools</a></p>");
  echo("<table width=100% cellspacing=0 cellpadding=5>");

  foreach ($mempools as $mempool)
  {

    if ($config['memcached']['enable'])
    {
      $state = $memcache->get('mempool-'.$mempool['mempool_id'].'-state');
      if($debug) { print_r($state); }
      if(is_array($state)) { $mempool = array_merge($mempool, $state); }
      unset($state);
    }

    $percent= round($mempool['mempool_perc'],0);
    $text_descr = rewrite_entity_descr($mempool['mempool_descr']);
    $total = formatStorage($mempool['mempool_total']);
    $used = formatStorage($mempool['mempool_used']);
    $free = formatStorage($mempool['mempool_free']);
    $background = get_percentage_colours($percent);

    $graph_array           = array();
    $graph_array['height'] = "100";
    $graph_array['width']  = "210";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $mempool['mempool_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['from']   = $config['time']['day'];
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link = generate_url($link_array);

    $overlib_content = generate_overlib_content($graph_array, $device['hostname'] . " - " . $text_descr);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.

    $minigraph =  generate_graph_tag($graph_array);

    echo("<tr class=device-overview>
           <td class=tablehead>".overlib_link($link, $text_descr, $overlib_content)."</td>
           <td width=90>".overlib_link($link, $minigraph, $overlib_content)."</td>
           <td width=200>".overlib_link($link, print_percentage_bar (200, 20, $percent, NULL, "ffffff", $background['left'], $percent . "%", "ffffff", $background['right']), $overlib_content)."
           </a></td>
         </tr>");
  }

  echo("</table>");
  echo("</div>");
}

?>
