<?php defined('_JEXEC') or die;
function lastOnline() {
    $db = JFactory::getDbo();
    $db->getQuery(true);
    $query = "";
    for ($i = 1; $i < 13; $i++) {
        if ($i != 5 && $i != 11) {
            $query .= "
            SELECT `temp`.`did`, `temp`.`name`, CEIL(AVG(`temp`.`status`)) as `status`, MAX(`temp`.`status`) as `max`, `number`, `route` FROM (
            (SELECT `d`.`id` as `did`, `d`.`name` as `name`, `r`.`status` as `status`, `r`.`num` as `number`, `r`.`route` as `route`
            FROM `#__rasp_online` as `r`
            LEFT JOIN `#__directions` as `d` ON (`r`.`dir`=`d`.`id`)
            WHERE `dir` = $i AND `dat`=CURRENT_DATE() AND `r`.`arr`=(SELECT MAX(`arr`) FROM `#__rasp_online` WHERE `dir` = $i AND `dat`=CURRENT_DATE() AND `num`=`r`.`num`)
            ORDER BY `arr`
            DESC LIMIT 5) as `temp`)
            ";
        }
        if (($i != 12) && ($i != 5 && $i != 11)) $query .= "
            UNION
            ";
    }
    //exit(var_dump($query));
    $db->setQuery($query);
    return $db->loadObjectList();
}
$opozdaniya = lastOnline();
?>
    <table style="width: 100%;">
        <thead>
        <tr>
            <th><?=JText::_('MOD_LASTONLINE_DIRECTION')?></th>
            <th><?=JText::_('MOD_LASTONLINE_LATENESS_AVG')?></th>
            <th><?=JText::_('MOD_LASTONLINE_LATENESS_MAX')?></th>
        </tr>
        </thead>
        <tbody>
<?php
foreach ($opozdaniya as $dir) {
    if ($dir) {
        $status = $dir->status == '0' ? JText::_('MOD_LASTONLINE_STATUS_OK') : $dir->status.' '.JText::_('MOD_LASTONLINE_ARR_MINUTE');
        $status_max = $dir->max == '0' ? JText::_('MOD_LASTONLINE_STATUS_OK') : $dir->max.' '.JText::_('MOD_LASTONLINE_ARR_MINUTE');
        $url_dir = '/rasp/online?dir='.$dir->did.'&date='.date("Y-m-d");
        ?>
        <tr>
            <td><a href="<?=$url_dir?>"><?=$dir->name?></a></td>
            <td><?=$status?></td>
            <td>
                <?=$status_max?>
            </td>
        </tr>
        <?php
    }
}
?>
        </tbody>
    </table>
<?php if (JFactory::getApplication()->input->getString('view') != 'last_online') { ?>
<a href="/rasp/online"><?=JText::_('MOD_LASTONLINE_SPOILER')?></a>
<?php }?>
