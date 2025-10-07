<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$speakLanguagesArr = explode(",", $spoken_language_names);
$totalSpeakLanguages = count($speakLanguagesArr) - 1;
$speakLanguagesProficiencyArr = explode(",", $spoken_languages_proficiency);
?>
<?php foreach ($speakLanguagesArr as $index => $spokenLangName) { ?>
    <?php if (isset($proficiencyArr[$speakLanguagesProficiencyArr[$index] ?? ''])) { ?>
        <span class="txt-inline__tag"><?php echo $spokenLangName; ?><strong> (<?php echo $proficiencyArr[$speakLanguagesProficiencyArr[$index]]; ?>)</strong></span><?php echo ($index < $totalSpeakLanguages) ? ',' : ''; ?>
    <?php } ?>
<?php } ?>