<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class GenerateResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateResources {arg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate all resources. model include relationship, migration, controller, route';

    /**
     * The list used function of convertToPlural.
     *
     * @var array
     */
    protected $listForConvertToPlural = [
        'child'       => 'children',
        'foot'        => 'feet',
        'louse'       => 'lice',
        'man'         => 'men',
        'woman'       => 'women',
        'medium'      => 'media',
        'mouse'       => 'mice',
        'person'      => 'people',
        'seaman'      => 'seamen',
        'snowman'     => 'snowmen',
        'money'       => 'money',
        'water'       => 'water',
        'advice'      => 'advice',
        'information' => 'information',
        'music'       => 'music',
        'business'    => 'advice',
        'work'        => 'work',
        'homework'    => 'homework',
        'love'        => 'love',
        'art'         => 'art',
        'nature'      => 'nature',
        'food'        => 'food',
        'weather'     => 'weather',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resourceName = $this->argument('arg');
        $this->checkUpperCaseInitialStr($resourceName);

        // add model and migration
        $this->call('make:model', [
            'name' => $resourceName,
            '--migration' => true,
        ]);

        echo "\n" . "Success: Added model and migration file." . "\n\n";

        // add relationship in model file
        echo $resourceName . ' has any relationships? (y/n)' . "\n> ";
        $hasRelationShip = trim(fgets(STDIN));

        if ($hasRelationShip === 'y') :
            echo "\n" . "Please input name of model for relationships." . "\n> ";
            $relatedModelName = trim(fgets(STDIN));

            $this->checkUpperCaseInitialStr($relatedModelName);
            $this->checkExistModel($relatedModelName);

            // relationship for resource model
            $selectedRelateSide = $this->selectRelationShip($resourceName);
            $this->addRelationShipToModelFile($resourceName, $relatedModelName, $selectedRelateSide);

            // relationship for related model
            $selectedRelatedSide = $this->selectRelationShip($relatedModelName);
            $this->addRelationShipToModelFile($relatedModelName, $resourceName, $selectedRelatedSide);

            echo "\n" . "Success: Added relationship with " . $resourceName . " and " . $relatedModelName . ".\n\n";
        endif;

        // add controller
        $this->call('make:controller', [
            'name' => "{$resourceName}Controller",
            '--resource' => true,
        ]);

        // add route
        $routeFile = './routes/web.php';
        $lowerCaseResourceName = strtolower($resourceName);
        $lowerAndPluralResourceName = $this->convertToPlural($lowerCaseResourceName);

        $newRoute = "    Route::resource('/$lowerAndPluralResourceName', {$resourceName}Controller::class);";
        $rowArray = file($routeFile);

        $rowLength = count($rowArray);
        $targetIndexes = [];
        foreach ($rowArray as $i => $row) {
            if (strpos($row, "App\Http\Controllers")) {
                echo  "\n" . $i . ": " . $row . "\n";
                $targetIndexes[] = $i;
            }
            if ($i > $rowLength / 2)
                break;
        }

        $targetIndex = end($targetIndexes);
        $rowArray[$targetIndex] = $rowArray[$targetIndex] . "use App\\Http\\Controllers\\" . $resourceName . "Controller;" . "\n";

        $rowArray[$rowLength - 3] = $newRoute . "\n" . '});' . "\n";
        file_put_contents($routeFile, $rowArray);

        return Command::SUCCESS;
    }

    /**
     * Check the initial letter of argument is uppercase.
     *
     * @param string $value
     */
    private function checkUpperCaseInitialStr($value)
    {
        $initialStr = substr($value, 0, 1);
        if (!ctype_upper($initialStr)) {
            throw new Exception('please start arg with an uppercase');
        }
    }

    /**
     * Check the existence of the model.
     *
     * @param string $modelName
     */
    private function checkExistModel($modelName)
    {
        if ($modelName === "User" && class_exists("Illuminate\Foundation\Auth\User")) {
            return;
        }

        if (class_exists("App\\Models\\{$modelName}")) {
        } else {
            throw new Exception("Not exist model of " . $modelName);
        }
    }

    /**
     * Add relationship to model file.
     *
     * @param string $modelName
     * @return string $selectedRelationShip
     */
    private function selectRelationShip($modelName)
    {
        $relationShipArr = ['hasMany', 'hasOne', 'belongsTo'];
        $selectedRelationShip = null;

        while ($selectedRelationShip === null) {
            echo "\n" . "Please select relationship number for " .  $modelName . " side." . "\n";

            foreach ($relationShipArr as $i => $relationShip) {
                echo $i . ": " . $relationShip . "\n";
            }

            echo "> ";
            $relationShipNumber = trim(fgets(STDIN));

            if (in_array($relationShipNumber, ["0", "1", "2"], true))
                $selectedRelationShip = $relationShipArr[intval($relationShipNumber)];
        }

        return $selectedRelationShip;
    }

    /**
     * Add relationship to model file.
     *
     * @param string $modelName
     * @param string $relatedModelName
     * @param string $relationName
     */
    private function addRelationShipToModelFile($modelName, $relatedModelName, $relationName)
    {
        $lowerCaseRelatedModelName = strtolower($relatedModelName);

        $functionName = $relationName !== "belongsTo" ?
            $this->convertToPlural($lowerCaseRelatedModelName) : $lowerCaseRelatedModelName;

        $addText =
            "\n" .
            "    public function " . $functionName . "()" . "\n" .
            "    {" . "\n" .
            '        return $this->' . $relationName . "(" . $relatedModelName . "::class" . ");" . "\n" .
            "    }";

        $modelFile = "./app/Models/" . $modelName . ".php";
        $row_array = file($modelFile);
        $rowLength = count($row_array);
        $row_array[$rowLength - 1] = $addText . "\n" . '}' . "\n";

        file_put_contents($modelFile, $row_array);
    }

    /**
     * Convert the singular to plural.
     *
     * @param string $value
     * @return string $converted
     */
    private function convertToPlural($value)
    {
        if (array_key_exists($value, $this->listForConvertToPlural)) {
            return $this->listForConvertToPlural[$value];
        } else {
            $converted = preg_replace("/(s|sh|ch|o|x)$/", "$1es", $value);
            $converted = preg_replace("/(f|fe)$/", "ves", $converted);
            $converted = preg_replace("/(a|i|u|e|o)y$/", "$1ys", $converted);
            $converted = preg_replace("/y$/", "ies", $converted);

            if (!preg_match("/s$/", $converted)) {
                $converted = $converted . "s";
            }
            return $converted;
        }
    }
}
