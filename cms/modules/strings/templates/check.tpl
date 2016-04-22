<h1><?=at('strings')?></h1>

<a class="back" href="<?=_cfg('cmssite')?>/#strings">Back</a>

<table class="table strings check">
	<tr>
        <td width="100%"><b><?=at('check_strings_in_files')?></b></td>
    </tr>
    <?
        if ($module->checkData->inFilesNotDb) {
            foreach($module->checkData->inFilesNotDb as $value) {
                ?>
                <tr>
                    <td><?=$value?></td>
                </tr>
                <?
            }
        }
    ?>
</table>

<table class="table strings">
	<tr>
        <td width="100%" colspan="3"><b><?=at('check_strings_in_db')?></b></td>
    </tr>
    <tr>
        <td width="20%"><b><?=at('string_name')?></b></td>
        <td width="70%"><b><?=at('string_output')?></b></td>
        <td width="10%" class="centered b"><?=at('actions')?></td>
    </tr>
    <?
        if ($module->checkData->inDbNotInFiles) {
            foreach($module->checkData->inDbNotInFiles as $value) {
                ?>
                <tr>
                    <td><a href="<?=_cfg('cmssite').'/#strings/edit/'.$value->key?>"><?=$value->key?></a></td>
                    <td><a href="<?=_cfg('cmssite').'/#strings/edit/'.$value->key?>"><?=substr(strip_tags($value->value),0,100)?></a></td>
                    <td class="centered">
                        <a href="<?=_cfg('cmssite').'/#strings/edit/'.$value->key?>" class="hint" name="Edit"><img src="<?=_cfg('cmsimg')?>/edit.png" /></a>
                        <a href="javascript:void(0);" class="hint" name="Delete" onclick="TM.deletion('<?=_cfg('cmssite').'/#strings/delete/'.$v->key?>'); return false;">
                            <img src="<?=_cfg('cmsimg')?>/cancel.png" />
                        </a>
                    </td>
                </tr>
                <?
            }
        }
    ?>
</table>