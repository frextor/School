<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Génère un squelette de modèle Eloquent pour chaque table de la base
 * legacy (préfixe `amos_`) qui n'a pas encore de modèle écrit à la main.
 *
 * Usage : php artisan legacy:generate-models [--force]
 */
class GenerateLegacyModels extends Command
{
    protected $signature = 'legacy:generate-models {--force : Écrase les modèles déjà générés automatiquement}';

    protected $description = 'Génère les modèles Eloquent manquants à partir du schéma de la base legacy';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');

        $existingTables = $this->tablesAlreadyMapped();

        $tables = DB::select(
            "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE' ORDER BY TABLE_NAME",
            [$database]
        );

        $generated = 0;
        $skipped = 0;

        foreach ($tables as $row) {
            $table = $row->TABLE_NAME;

            if (isset($existingTables[$table])) {
                $skipped++;

                continue;
            }

            $this->generateModel($table);
            $generated++;
        }

        $this->info("Modèles générés : {$generated}. Tables déjà mappées ignorées : {$skipped}.");

        return self::SUCCESS;
    }

    /** Table => true pour toute table déjà référencée par un modèle existant (protected $table = '...'). */
    private function tablesAlreadyMapped(): array
    {
        $map = [];
        $dir = app_path('Models');

        foreach (glob($dir.'/*.php') as $file) {
            $content = file_get_contents($file);

            if (preg_match("/protected \\\$table\s*=\s*'([a-zA-Z0-9_]+)'/", $content, $m)) {
                $map[$m[1]] = true;
            }
        }

        return $map;
    }

    private function generateModel(string $table): void
    {
        $database = config('database.connections.mysql.database');

        $columns = DB::select(
            'SELECT COLUMN_NAME, COLUMN_KEY, EXTRA, DATA_TYPE FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ORDINAL_POSITION',
            [$database, $table]
        );

        $primaryKey = null;
        $fillable = [];
        $casts = [];
        $hasCreatedAt = false;
        $hasUpdatedAt = false;

        foreach ($columns as $col) {
            if ($col->COLUMN_KEY === 'PRI' && $primaryKey === null) {
                $primaryKey = $col->COLUMN_NAME;
            }

            if ($col->COLUMN_NAME === 'created_at') {
                $hasCreatedAt = true;
            }
            if ($col->COLUMN_NAME === 'updated_at') {
                $hasUpdatedAt = true;
            }

            if (in_array($col->DATA_TYPE, ['tinyint'], true) && str_starts_with((string) $col->COLUMN_NAME, 'is_')) {
                $casts[$col->COLUMN_NAME] = 'boolean';
            }

            if (! str_contains($col->EXTRA, 'auto_increment')
                && ! in_array($col->COLUMN_NAME, ['created_at', 'updated_at'], true)) {
                $fillable[] = $col->COLUMN_NAME;
            }
        }

        $primaryKey ??= 'id';
        $className = $this->classNameFor($table);
        $timestamps = $hasCreatedAt && $hasUpdatedAt ? 'true' : 'false';

        $fillableExport = "'".implode("',\n        '", $fillable)."'";
        $castsLines = collect($casts)
            ->map(fn ($type, $col) => "        '{$col}' => '{$type}',")
            ->implode("\n");

        $castsBlock = $castsLines !== '' ? "\n\n    protected \$casts = [\n{$castsLines}\n    ];" : '';

        $stub = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle généré automatiquement depuis le schéma legacy (table `{$table}`).
 * À compléter manuellement (relations, accessors, casts) au fil de la
 * migration du module correspondant.
 */
class {$className} extends Model
{
    protected \$table = '{$table}';
    protected \$primaryKey = '{$primaryKey}';
    public \$timestamps = {$timestamps};

    protected \$fillable = [
        {$fillableExport},
    ];{$castsBlock}
}

PHP;

        file_put_contents(app_path("Models/{$className}.php"), $stub);
    }

    private function classNameFor(string $table): string
    {
        $withoutPrefix = Str::startsWith($table, 'amos_') ? substr($table, 5) : $table;

        return Str::studly($withoutPrefix);
    }
}
