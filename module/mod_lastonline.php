<?php defined('_JEXEC') or die;
function lastOnline() {
    $db = JFactory::getDbo();
    $db->getQuery(true);
    $query = "";
    for ($i = 1; $i < 13; $i++) {
        if ($i != 5 && $i != 11) {
            $query .= "
            (SELECT `d`.`name`, `r`.`status` FROM `#__rasp_online` as `r` LEFT JOIN `#__directions` as `d` ON (`r`.`dir`=`d`.`id`) WHERE `dir` = $i AND `dat`=CURRENT_DATE() AND `r`.`status`=(SELECT AVG(`status`) FROM `#__rasp_online` WHERE `dir` = $i AND `dat`=CURRENT_DATE() AND `num`=`r`.`num`) GROUP BY `dir` ORDER BY `d`.`name` DESC LIMIT 5)
            ";
        }
        if (($i != 12) && ($i != 5 && $i != 11)) $query .= "
            UNION
            ";
    }
    $db->setQuery($query);
    return $db->loadObjectList();
}
$opozdaniya = lastOnline();
?>
    <table style="width: 100%;">
        <thead>
        <tr>
            <th><?=JText::_('MOD_LASTONLINE_DIRECTION')?></th>
            <th><?=JText::_('MOD_LASTONLINE_LATENESS')?></th>
        </tr>
        </thead>
        <tbody>
<?php
foreach ($opozdaniya as $dir) {
    $status = $dir->status == '0' ? JText::_('MOD_LASTONLINE_STATUS_OK') : $dir->status.' '.JText::_('MOD_LASTONLINE_ARR_MINUTE');
    ?>
    <tr>
        <td><?=$dir->name?></td>
        <td><?=$status?></td>
    </tr>
    <?php
}
?>
        </tbody>
    </table>
