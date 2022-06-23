<?php

namespace Ashr\Keonn\Services\Api;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait Media
{
    /**
     * Upload file to keonn sftp
     *
     * @param mixed $file
     * @param string $fileName
     * @return string
     * @throws Exception
     */
    public function uploadFile(mixed $file, string $fileName): string
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

        $path = $this->getStoragePath();

        return Storage::disk($this->getStorageDisk())
            ->put($path . $fileName, $file) ? $fileName : '';
    }

    /**
     * Delete file from keonn resources
     *
     * @param string $fileName
     * @return bool
     */
    public function deleteFile(string $fileName): bool
    {
        $path = $this->getStoragePath();

        return Storage::disk($this->getStorageDisk())
            ->delete($path . $fileName);
    }

    /**
     * Get file path
     *
     * @param string $fileName
     * @return string
     */
    public function getFilePath(string $fileName): string
    {
        $path = $this->getStoragePath() . $fileName;

        if (Storage::disk($this->getStorageDisk())->exists($path)) {
            return Storage::disk($this->getStorageDisk())->url($path);
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function getStoragePath(): string
    {
        return $this->config['keonn_storage_driver'] === 'sftp' ? $this->config['app_mode'] . '/' : '';
    }

    public function getStorageDisk(): string
    {
        return 'keonn_' . $this->config['keonn_storage_driver'];
    }
}