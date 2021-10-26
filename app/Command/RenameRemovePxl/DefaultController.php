<?php

namespace App\Command\RenameRemovePxl;

use Minicli\App;
use Minicli\Command\CommandController;
use Minicli\Input;

class DefaultController extends CommandController
{
    private Input $userInput;
    private array $fileWithErrors;

    public function boot(App $app)
    {
        parent::boot($app);
        $this->userInput = new Input('ImageNameFormat$> ');
        $this->fileWithErrors = [];
    }

    public function handle()
    {
        $folder = $this->folder();
        $files = scandir($folder);
        $counter = 0;

        if (false === $files) {
            $this->getPrinter()->error('I can\'t get the files in the path');
            exit;
        }
        $files = array_diff($files, ['.', '..']);

        foreach ($files as $file) {
            if (is_dir($folder.$file)) {
                continue;
            }
            $newFileName = str_replace('PXL_', '', $file);
            if ($file !== $newFileName) {
                if (false !== rename($folder.$file, $folder.$newFileName)) {
                    $counter++;
                } else {
                    $this->fileWithErrors[] = $folder.$file;
                }
            }
        }
        $this->getPrinter()->success('Renamed '.$counter.' files!');
        $this->displayErrors();
    }

    private function folder(): string
    {
        if ($this->hasParam('folder')) {
            $folder = $this->getParam('folder');
        } else {
            $this->getPrinter()->info('Provide the folder full path');
            $folder = $this->userInput->read();
        }

        return rtrim($folder, '\\').'\\';
    }

    private function displayErrors(): void
    {
        if (count($this->fileWithErrors) === 0) {
            return;
        }
        $this->getPrinter()->error('I cannot rename these files:', true);
        $this->getPrinter()->newline();
        foreach ($this->fileWithErrors as $path) {
            $this->getPrinter()->error($path);
        }
    }
}
