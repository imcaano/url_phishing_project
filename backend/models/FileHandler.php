<?php

class FileHandler {
    private $allowedMimeTypes = [
        'text/plain',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    
    public function readFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File not found");
        }

        $mimeType = mime_content_type($filePath);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            throw new Exception("File type not allowed");
        }

        try {
            $content = file_get_contents($filePath);
            return $content;
        } catch (Exception $e) {
            throw new Exception("Error reading file: " . $e->getMessage());
        }
    }

    public function writeFile($filePath, $content) {
        try {
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            if (file_put_contents($filePath, $content) === false) {
                throw new Exception("Failed to write file");
            }
            return true;
        } catch (Exception $e) {
            throw new Exception("Error writing file: " . $e->getMessage());
        }
    }

    public function deleteFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File not found");
        }

        try {
            if (!unlink($filePath)) {
                throw new Exception("Failed to delete file");
            }
            return true;
        } catch (Exception $e) {
            throw new Exception("Error deleting file: " . $e->getMessage());
        }
    }
} 