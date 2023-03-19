<?php

namespace Manoz\Venus\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'venus:install {--all : Indicates that all optional packages should be installed}
                                          {--frontend : Indicates if the front-end stack should be installed. It will install Tailwind CSS, Alpine JS, autoprefixer and PostCSS}
                                          {--duster : Indicates if Duster should be installed}
                                          {--eslint : Indicates if ESLint should be installed}
                                          {--prettier : Indicates if Prettier should be installed}
                                          {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Venus scaffolding';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $this->option('all') && ! $this->option('duster') && ! $this->option('eslint') && ! $this->option('prettier') && ! $this->option('frontend')) {
            $this->warn('Please specify which packages you want to install.');
            $this->newLine();
            $this->line('You can use the --all option to install all packages at once.');
            $this->line('You can also use the --frontend, --duster, --eslint and --prettier options to install individual packages.');
            $this->newLine();
            $this->info('For more information, run "php artisan help venus:install".');

            return;
        }

        if ($this->option('duster')) {
            $this->installDuster();
        }

        if ($this->option('eslint')) {
            $this->installEslint();
        }

        if ($this->option('prettier')) {
            $this->installPrettier();
        }

        if ($this->option('frontend')) {
            $this->installFrontEnd();
        }

        if ($this->option('all')) {
            $this->installDuster();
            $this->installEslint();
            $this->installPrettier();
            $this->installFrontEnd();

            $this->info('All packages installed successfully.');
            $this->info('Don\'t forget to run "npm i" to install the new dependencies.');
            $this->warn('If you installed Tailwind CSS, don\'t forget to import it in your CSS file using:');
            $this->warn('@import "./tailwind.css";');
        }
    }

    /**
     * Install Duster.
     */
    protected function installDuster(): void
    {
        $this->removeComposerDevPackages(['laravel/pint', 'friendsofphp/php-cs-fixer']);

        if (! $this->requireComposerDevPackages(['tightenco/duster:^1.1'])) {
            $this->error('Unable to install Duster.');
        }

        $this->addComposerScripts();

        copy(__DIR__ . '/../../stubs/Duster/.php-cs-fixer.dist.php', base_path('.php-cs-fixer.dist.php'));
        copy(__DIR__ . '/../../stubs/Duster/pint.json', base_path('pint.json'));

        if (! $this->option('all')) {
            $this->successMessage('Duster');
        }
    }

    /**
     * Install ESLint.
     */
    protected function installEslint(): void
    {
        $this->updateNodePackages(function ($packages) {
            return [
                'eslint' => '^8.36.0',
                'eslint-plugin-import' => '^2.27.5',
            ] + $packages;
        });

        $this->updateNodeScripts(function ($scripts) {
            return [
                'lint' => 'eslint resources/js/**/*.js',
            ] + $scripts;
        });

        $this->copyEditorConfig();

        copy(__DIR__ . '/../../stubs/Eslint/.eslintignore.stub', base_path('.eslintignore'));
        copy(__DIR__ . '/../../stubs/Eslint/.eslintrc.js.stub', base_path('.eslintrc.js'));

        if (! $this->option('all')) {
            $this->successMessage('ESLint', true);
        }
    }

    /**
     * Install Prettier.
     */
    protected function installPrettier(): void
    {
        $this->updateNodePackages(function ($packages) {
            return [
                'prettier' => '^2.5.1',
            ] + $packages;
        });

        $this->updateNodeScripts(function ($scripts) {
            return [
                'format' => 'prettier --write resources/js/**/*.js',
            ] + $scripts;
        });

        $this->copyEditorConfig();

        copy(__DIR__ . '/../../stubs/Prettier/.prettierignore.stub', base_path('.prettierignore'));
        copy(__DIR__ . '/../../stubs/Prettier/.prettierrc.json.stub', base_path('.prettierrc.json'));

        if (! $this->option('all')) {
            $this->successMessage('Prettier', true);
        }
    }

    /**
     * Install Front-end stack.
     */
    protected function installFrontEnd(): void
    {
        $this->updateNodePackages(function ($packages) {
            return [
                '@tailwindcss/forms' => '^0.5.3',
                '@tailwindcss/typography' => '^0.5.9',
                'autoprefixer' => '^10.4.14',
                'cssnano' => '^5.1.15',
                'postcss' => '^8.4.21',
                'tailwindcss' => '^3.2.7',
            ] + $packages;
        });

        $this->updateNodePackages(function ($packages) {
            return [
                'alpinejs' => '^3.12.0',
            ] + $packages;
        }, false);

        // Copy configuration files
        copy(__DIR__ . '/../../stubs/Frontend/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__ . '/../../stubs/Frontend/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/Frontend/tailwind.css', base_path('resources/css/tailwind.css'));

        // Ask if we should backup existing app.js file
        if ($this->confirm('Do you want to backup your existing app.js file?')) {
            copy(base_path('resources/js/app.js'), base_path('resources/js/app.js.bak'));
        }

        // Copy new app.js file
        copy(__DIR__ . '/../../stubs/Frontend/app.js', base_path('resources/js/app.js'));

        if (! $this->option('all')) {
            $this->successMessage('Front-end stack', true);

            $this->warn('Also, don\'t forget to import Tailwind\'s CSS file in app.css file:');
            $this->line('@import "./tailwind.css";');
        }
    }

    /**
     * Copy the .editorconfig file to the project root.
     */
    protected function copyEditorConfig(): void
    {
        copy(__DIR__ . '/../../stubs/Editorconfig/.editorconfig.stub', base_path('.editorconfig'));
    }

    /**
     * Add Composer script to run Duster's lint and fix commands.
     */
    protected function addComposerScripts(): void
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['scripts'] = array_merge(
            $composer['scripts'] ?? [],
            [
                'fmt' => [
                    'duster fix',
                ],
                'lint' => [
                    'duster lint',
                ],
            ],
        );

        file_put_contents(base_path('composer.json'), json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Install the given Composer Packages as "dev" dependencies.
     */
    protected function requireComposerDevPackages(mixed $packages): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'require', '--dev'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require', '--dev'],
            is_array($packages) ? $packages : func_get_args()
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Removes the given Composer Packages as "dev" dependencies.
     */
    protected function removeComposerDevPackages(mixed $packages): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'remove', '--dev'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove', '--dev'],
            is_array($packages) ? $packages : func_get_args()
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Add the given npm script to the package.json file.
     */
    protected static function updateNodeScripts(callable $callback): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $scripts = json_decode(file_get_contents(base_path('package.json')), true);

        $scripts['scripts'] = $callback(
            array_key_exists('scripts', $scripts) ? $scripts['scripts'] : [],
            'scripts'
        );

        ksort($scripts['scripts']);

        file_put_contents(
            base_path('package.json'),
            json_encode($scripts, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Add the given npm package to the package.json file.
     */
    protected static function updateNodePackages(callable $callback, bool $dev = true): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $dependenciesType = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$dependenciesType] = $callback(
            array_key_exists($dependenciesType, $packages) ? $packages[$dependenciesType] : [],
            $dependenciesType
        );

        ksort($packages[$dependenciesType]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Get the path to the appropriate PHP binary.
     */
    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    protected function successMessage(string $stack, bool $npm = false): void
    {
        $this->info("{$stack} installed successfully and configuration files have been updated.");

        if ($npm) {
            $this->newLine();
            $this->comment('Don\'t forget to run "npm install" to install the new dependencies.');
        }
    }
}
