<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Menu\Menu;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Native\Laravel\Facades\Notification;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */


    public function boot(): void
    {

        Menu::new()
            ->appMenu()
            ->editMenu()
            ->viewMenu()
            ->submenu(
                'About',
                Menu::new()
                    ->link('https://github.com/zakarialabib/mystockmaster', 'Github')
                    ->separator()
                    ->link('https://github.com/zakarialabib/mystockmaster/docs', 'Docs')
            )
            ->register();

        Window::open()
            ->title(config('app.name'))
            ->fullscreen()
            ->resizable()
            ->width(1080)
            ->minWidth(1080)
            ->maxWidth(1080)
            ->height(900)
            ->minHeight(900)
            ->showDevTools(false)
            ->maximizable(false);

        // Menu::new()
        //     ->submenu(
        //         'Settings',
        //         Menu::new()
        //             ->link(route('settings.index'), 'Settings')
        //             ->link(route('logs.index'), 'Logs')
        //             ->link(route('currencies.index'), 'Currencies')
        //             ->link(route('languages.index'), 'Languages')
        //             ->link(route('backup.index'), 'Backup')
        //     )
        //     ->register();

        $sqliteFilePath = database_path('nativephp.sqlite');


        if (file_exists($sqliteFilePath)) {
            // Set the database connection to 'nativephp'
            config(['database.default' => 'nativephp']);
        } else {
            // Show a confirmation dialog to the user
            $dialogResult = Window::confirm('SQLite database not found. Would you like to open an existing SQLite file or create a new one?');

            if ($dialogResult === 'Open') {
                // Open a file dialog and allow the user to select the SQLite file
                $selectedFile = Window::openFileDialog();

                if ($selectedFile) {
                    // Set the database connection to 'sqlite' and use the selected file as the SQLite database
                    //config(['database.connections.sqlite.database' => $selectedFile]);
                    config(['database.default' => 'nativephp']);
                }
            } elseif ($dialogResult === 'Create') {
                // Open a file dialog and allow the user to specify the location and name of the new SQLite file
                $newFile = Window::saveFileDialog();

                if ($newFile) {
                    // Create the SQLite database file at the specified path
                    touch($newFile);

                    // Set the database connection to 'sqlite' and use the new file as the SQLite database
                    config(['database.connections.sqlite.database' => $newFile]);
                    config(['database.default' => 'nativephp']);
                }
            }
        }

        if ($this->getInternetStatus()) {
            // Show a system-wide notification to connect to the last saved SQL connection
            Notification::title('✅ Your are Connected')
                ->message("now your are connected to server.")
                ->show();

            // check unsaved data from sqlite

            // Show a menu bar with the option to get data
            MenuBar::create()
                ->label('Connected to Internet')
                ->showDockIcon();

            Menu::new()
                ->submenu(
                    'Check Unsaved data',
                    Menu::new()
                        ->event(App\Events\MyEvent::class, 'Trigger my event')
                )
                ->register();
        } else {
            // Show a system-wide notification to connect to the last saved SQL connection
            Notification::title('🛑 Not Connected')
                ->message('No Internet Connection.')
                ->show();

            // Show a menu bar with the option to get data
            MenuBar::create()
                ->label('Connection Lost')
                ->showDockIcon();
        }
    }

    //     NativePHP uses the `local` disk by default. If you would like to use a different disk, you may configure this in your
    // `config/filesystems.php` file.

    // Remember, you can set the filesystem disk your application uses by default in your `config/filesystems.php` file or by
    // adding a `FILESYSTEM_DISK` variable to your `.env` file.

    private function getInternetStatus(): bool
    {
        /*if (connection_status() === CONNECTION_NORMAL) {
            return true;
        } elseif (in_array(connection_status(), [CONNECTION_ABORTED, CONNECTION_TIMEOUT])) {
            return false;
        }*/

        try {
            $response = Http::timeout(1)->get("https://www.google.com");

            return $response->successful();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            return false;
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '36000',
            'max_input_time' => '0',
            'post_max_size' => '20M',
            'upload_max_filesize' => '20M',
            'max_file_uploads' => '20',
            'default_charset' => 'UTF-8',
            'date.timezone' => 'America/New_York'
        ];
    }
}