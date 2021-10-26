<?php

namespace App\Command\RenameFromOriginal;

use Minicli\App;
use Minicli\Command\CommandController;
use Minicli\Input;

class DefaultController extends CommandController
{
    private Input $userInput;
    protected array $fileWithErrors;
    const EXTENSIONS = [
        'jpeg',
        'jpg',
        'JPG',
        'JPEG',
        'png',
        'PNG',
    ];

    public function boot(App $app)
    {
        parent::boot($app);
        $this->userInput = new Input('ImageNameFormat$> ');
        $this->fileWithErrors = [];
    }

    public function handle()
    {
        $originalFolder = $this->originalFolder();
        $destinationFolder = $this->destinationFolder();
        $counter = 0;
        $originalFiles = array_diff(scandir($originalFolder), ['.', '..']);

        if (false === $originalFiles) {
            $this->getPrinter()->error('I can\'t get the files in the original folder path');
            exit;
        }

        foreach ($originalFiles as $file) {
            $oldNameWithoutExt = preg_filter('/\.(heic|HEIC)/', '', $file);
            $originalPath = $originalFolder.$file;
            $output = [];
            exec('identify -format "%[EXIF:DateTimeOriginal]" '.$originalPath, $output, $exitCode);
            if ($exitCode !== 0 || count($output) !== 1) {
                $this->fileWithErrors[] = $originalPath;
                continue;
            }

            $date = $output[0];
            $newName = date('Ymd-His', strtotime($date));

            foreach (self::EXTENSIONS as $extension) {
                $oldPath = $destinationFolder.$oldNameWithoutExt.'.'.$extension;
                if (file_exists($oldPath)) {
                    $newPath = $destinationFolder.$newName.'.'.$extension;
                    if (false === rename($oldPath, $newPath)) {
                        $this->fileWithErrors[] = $newPath;
                    } else {
                        $counter++;
                    }
                }
            }
        }
        $this->getPrinter()->success('Renamed '.$counter.' files!');
        $this->displayErrors();
    }

    private function originalFolder(): string
    {
        if ($this->hasParam('original')) {
            $original = $this->getParam('original');
        } else {
            $this->getPrinter()->info('Provide the original folder full path');
            $original = $this->userInput->read();
        }

        return rtrim($original, '/').'/';
    }

    private function destinationFolder(): string
    {
        if ($this->hasParam('destination')) {
            $destination = $this->getParam('destination');
        } else {
            $this->getPrinter()->info('Provide the destination folder full path');
            $destination = $this->userInput->read();
        }

        return rtrim($destination, '/').'/';
    }

    private function displayErrors(): void
    {
        if (count($this->fileWithErrors) === 0) {
            return;
        }
        $this->getPrinter()->error('Some files generated errors, check these paths:', true);

        foreach ($this->fileWithErrors as $path) {
            $this->getPrinter()->error($path);
        }
    }
}
