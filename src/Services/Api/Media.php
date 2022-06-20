<?php

namespace Ashr\Keonn\Services\Api;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

trait Media
{
    /**
     * Upload file to keonn sftp
     *
     * @param mixed $file
     * @param string $fileName
     * @return bool
     * @throws Exception
     */
    public function uploadFile(mixed $file, string $fileName): bool
    {
        if (is_null($file) || trim($file) === '') {
            throw new Exception('Invalid file!');
        }

        if (trim($fileName) === '') {
            throw new Exception('Given filename is invalid!');
        }

        $allowedExtensions = ['jpg', 'JPEG', 'png', 'PNG', 'MP4', 'mp4'];

        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension();
        } else {
            $extension = (new SplFileInfo($file))->getExtension();

            foreach ($allowedExtensions as $allowedExtension) {
                if (str_contains($extension, $allowedExtension)) {
                    $extension = $allowedExtension;
                }
            }
        }

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Invalid file extension');
        }

        $fileName = sprintf('%s.%s', $fileName, $extension);

        $file = @file_get_contents($file);
        if ($file === false) {
            throw new Exception('Cannot get file, please provide valid path!');
        }

        return Storage::disk('keonn_sftp')
            ->put($this->config['app_mode'] . '/' . $fileName, $file);
    }

    /**
     * Delete file from keonn resources
     *
     * @param string $fileName
     * @return bool
     */
    public function deleteFile(string $fileName): bool
    {
        return Storage::disk('keonn_sftp')
            ->delete($this->config['app_mode'] . '/' . $fileName);
    }
}