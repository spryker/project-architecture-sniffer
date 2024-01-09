<?php

const PRIORITY_MAP = [
    1 => 'CRITICAL',
    2 => 'MAJOR',
    3 => 'MEDIUM',
    4 => 'MINOR',
    5 => 'CORE',
];

$file = file_get_contents('results.json');
$results = json_decode($file, true);

$convertedResults = [];

foreach ($results['files'] as $fileResults) {
    foreach ($fileResults['violations'] as $violation) {
        $priorityKey = $violation['priority'];
        $ruleSetKey = $violation['ruleSet'];

        $group = str_contains($ruleSetKey, 'Spryker') ? 'SPRYKER ARCHITECTURE RULES' : 'GENERAL RULES';

        $ruleKey = $violation['rule'];

        $convertedResults[$group][$priorityKey][$ruleSetKey][$ruleKey][] = [
            'file' => $fileResults['file'],
            'violation' => $violation,
        ];
    }
}

$fileOutput = '';
foreach ($convertedResults as $group => $convertedResult) {
    $fileOutput .= '=================================== ' . $group . ' ===================================' . PHP_EOL . PHP_EOL;

    ksort($convertedResult);

    $totalInGroup = 0;

    foreach ($convertedResult as $priorityIndex => $rulesetGroup) {
        $priorityValue = PRIORITY_MAP[$priorityIndex];

        $totalInPriority = 0;

        $fileOutput .= '========== ' . $priorityValue . ':' . PHP_EOL . PHP_EOL;

        foreach ($rulesetGroup as $ruleSetName => $ruleGroup) {
            $totalInRuleSet = 0;

            $fileOutput .= '===== ' . $ruleSetName . ':' . PHP_EOL . PHP_EOL;

            foreach ($ruleGroup as $ruleName => $violations) {
                $totalInRuleName = 0;

                $fileOutput .= '= ' . $ruleName . ':' . PHP_EOL;

                foreach ($violations as $violation) {
                    $fileOutput .= '- ' . $violation['file'] . ':' . $violation['violation']['beginLine'] . ' - ' . $violation['violation']['description'] . PHP_EOL;
                    $totalInGroup++;
                    $totalInPriority++;
                    $totalInRuleSet++;
                    $totalInRuleName++;
                }
                $fileOutput .= 'TOTAL violations for ' . $ruleName . ' ' . $totalInRuleName . PHP_EOL . PHP_EOL;
            }

            $fileOutput .= 'TOTAL violations for ' . $ruleSetName . ' ' . $totalInRuleSet . PHP_EOL . PHP_EOL;
        }

        $fileOutput .= 'TOTAL violations for ' . $priorityValue . ' ' . $totalInPriority . PHP_EOL . PHP_EOL;
    }

    $fileOutput .= 'TOTAL violations for ' . $group . ' ' . $totalInGroup . PHP_EOL . PHP_EOL;
}

echo $fileOutput;

file_put_contents('formatted-results.txt', $fileOutput);
