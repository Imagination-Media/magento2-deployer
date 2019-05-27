<?php

/**
 * Magento 2 Deployer
 *
 * Deployment tool for Magento 2 by Imagination Media.
 *
 * @package ImaginationMedia\Deployer
 * @author Igor Ludgero Miura <igor@imaginationmedia.com>
 * @copyright Copyright (c) 2019 Imagination Media (https://www.imaginationmedia.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

if (isset($argv[1]) && is_string($argv[1]) && (int)$argv[1] === 1 &&
    isset($argv[2]) && is_string($argv[2]) && $argv[2] !== "") {
    $projectPath = __DIR__;
    $projectPath = str_replace("vendor/imaginationmedia/deployer-magento2/Helper/Scripts", "", $projectPath);
    $projectPath = str_replace("deployment/Helper/Scripts", "", $projectPath);

    $envFile = include $projectPath . '/app/etc/env.php';
    $databaseInfo = $envFile["db"];
    $defaultConnection = $databaseInfo["connection"]["default"];

    $tablePrefix = $databaseInfo["table_prefix"];

    $conn = new mysqli($defaultConnection["host"], $defaultConnection["username"], $defaultConnection["password"], $defaultConnection["dbname"]);

    $sql = "SELECT module, schema_version FROM ".$databaseInfo["table_prefix"]."setup_module";
    $result = $conn->query($sql);

    $moduleVersions = [];

    if ($result !== null && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $moduleVersions[$row["module"]] = $row["schema_version"];
        }
    }

    ksort($moduleVersions);

    $fp = fopen($argv[2] . '/var/db_schema.json', 'w');
    fwrite($fp, json_encode($moduleVersions));
    fclose($fp);

    if (!$result) {
        throw new \Exception('Not possible to generate the database schema file.');
    }
}